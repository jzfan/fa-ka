<?php

namespace app\index\controller;

use app\common\model\Complaint as ComplaintModel;
use app\common\model\ComplaintMessage;
use app\common\model\Goods;
use app\common\model\Order as OrderModel;
use app\common\util\Sms;
use service\FileService;
use think\captcha\Captcha;
use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use think\Session;

class Order extends Base {
    private $seKey = 'zhiyu';
    private $expire = 60;

    public function __construct() {
        parent::__construct();

        if ($this->request->isPost()) {
            //post 请求进来，校验 token
            $token = input('token/s', '');
            if (empty($token) || $token != session('token')) {
                $this->error('非法请求');
            }
        }

        // 检查是否有 token ，如果没有，设置请求 token
        $token = session('token');
        if (!$token) {
            $token = md5(time() . md5(time()) . time()) . time();
            session('token', $token);
        }
        $this->assign('token', $token);
    }

    /**
     * 查询订单
     */
    public function query() {
        $code = input('chkcode/s', '');
        $this->assign('chkcode', $code);

        $queryType = input('querytype/d', '2');
        $this->assign('querytype', $queryType);

        $trade_no = input('orderid/s', '');
        $this->assign('trade_no', $trade_no);

        $is_verify = false;

        if ($trade_no || $queryType == 1) {
            // 验证码不能为空
            if (sysconf('order_query_chkcode') == 1) {
                $key    = $this->authcode($this->seKey) . 'orderquery';
                $secode = Session::get($key, '');

                if (!empty($code) && !empty($secode)) {

                    // session 过期
                    if (time() - $secode['verify_time'] > $this->expire) {
                        Session::delete($key, '');
                    } else {
                        if ($secode['verify_code'] == $code) {
                            $is_verify = true;

                            Session::delete($key, '');
                        }
                    }
                }
            } else {
                $is_verify = true;
            }

            switch ($queryType) {
                case '1':
                    //获取已登录用户最近一次购买的卡密
                    if (session('last_order_trade_no')) {
                        $trade_no = session('last_order_trade_no');
                        $order    = OrderModel::where(['trade_no' => $trade_no])->order('id DESC')->find();
                    } else {
                        $order = false;
                    }
                    break;
                case '2':
                    //按订单号方式获取
                    $order = OrderModel::where(['trade_no' => $trade_no])->order('id DESC')->find();
                    break;
                case '3':
                    //按联系方式获取
                    $count = OrderModel::where(['contact' => $trade_no, 'status' => 1])->count();
                    if ($count > 1) {
                        $order = OrderModel::where(['contact' => $trade_no, 'status' => 1])->order('id DESC')->paginate(30);
                        // 分页
                        $page = $order->render();
                        $this->assign('page', $page);
                        $this->assign('sekey', $this->seKey);
                        $this->assign('order', $order);
                        return $this->fetch('querybycontact');
                    } else {
                        $order = OrderModel::where(['contact' => $trade_no, 'status' => 1])->order('id DESC')->find();
                    }

                    break;
            }

            // 如果存在密码
            if ($order && $order->take_card_type != 0) {
                if (!empty($order->take_card_password)) {
                    $take_card_password = input('pwd/s', '');
                    if ($take_card_password) {
                        if ($take_card_password != $order->take_card_password) {
                            $this->error('查询密码错误！');
                        } else {
                            $is_verify = true;
                        }
                    } else {
                        $this->assign('trade_no', $order->trade_no);
                        return $this->fetch('query_pass');
                    }
                }
            }

            if (!empty($order) && $order['first_query'] == 0) {
                $is_verify = true;

                $order->save(['first_query' => 1]);
            }
        }

        $l = input('l/s', '');
        if ($l && $l == md5($trade_no . $this->seKey)) {
            $is_verify = true;
        }

        $this->assign('is_verify', $is_verify);
        if ($is_verify) {
            $this->assign('order', $order);
            if (isset($order->channel)) {
                $this->assign('channel', $order->channel);
            }

            if ($order['status'] == 1) {
                //查询订单资金是否还在冻结中，如果是，允许投诉，否则不允许用户投诉
                $unfreeze = Db::table('auto_unfreeze')->where(['trade_no' => $order['trade_no']])->find();
                if ($unfreeze) {
                    // 因为商户订单一旦结算了，钱就可能会被提走，极端情况下，商户余额里面可能一分钱都没有（跑路了）
                    // 那么平台就没办法追回这部分的损失，所以这里采用了支付后订单冻结24小时，而投诉只允许在冻结的 24小时内申请
                    // 支付超出 24 小时的订单，因为一开始提到的原因不再提供投诉入口，如果有问题，平台自行与商家，买家进行协商
                    $this->assign('canComplaint', true);
                }
            }
        }
        return $this->fetch();
    }

    /**
     * 检查商品并出货
     */
    public function checkGoods() {
        $token = input('token/s', '');
        if (empty($token) || $token != session('token')) {
            return json(['msg' => '非法请求']);
        }

        $trade_no = input('orderid/s', '');
        if ($trade_no) {
            return Goods::sendOut($trade_no);
        } else {
            return json([
                'msg'    => '请提供订单号',
                'status' => 0,
            ]);
        }
    }

    /**
     * 投诉
     */
    public function complaint() {
        if (!$this->request->isPost()) {
            return $this->fetch();
        }

        $trade_no = input('trade_no/s', '');
        $type     = input('type/s', '');
        $qq       = input('qq/s', '');
        $mobile   = input('mobile/s', '');
        $desc     = input('desc/s', '');

        if (!$qq) {
            $this->error('请输入联系QQ！');
        }
        if (!is_mobile_number($mobile)) {
            $this->error('这不是一个有效的手机号格式！');
        }
        if (!$desc) {
            $this->error('请输入投诉说明！');
        }

        $order = OrderModel::get(['trade_no' => $trade_no]);
        if (!$order) {
            $this->error('不存在该订单！');
        }
        if ($order->status === 0) {
            $this->error('该订单未完成，暂不能受理投诉！');
        }

        // 获取该手机号投诉次数
        $count = ComplaintModel::where(['trade_no' => $trade_no, 'mobile' => $mobile])->count();

        //2018-06-22 限制只能投诉一次

//        $limitNum = (int)sysconf('complaint_limit_num');
        //        if ($count >= $limitNum) {
        //            $this->error('您已投诉过该订单！');
        //        }
        if ($count > 0) {
            $token = md5(md5(time()).rand(1000,5000));
            session('token',$token);
            $this->error('您已投诉过该订单！', url('Index/order/complaintpass', ['trade_no' => $trade_no, 'token' => $token]));
        }

        try {
            Db::startTrans();

            //投诉查看密码，需要发送到投诉人联系手机中
            $code = rand(100000, 999999);

            $res = ComplaintModel::create([
                'user_id'   => $order->user_id,
                'trade_no'  => $trade_no,
                'type'      => $type,
                'qq'        => $qq,
                'mobile'    => $mobile,
                'desc'      => $desc,
                'status'    => 0,
                'create_at' => $_SERVER['REQUEST_TIME'],
                'create_ip' => $this->request->ip(),
                'pwd'       => $code,
                'expire_at' => time() + 86400,
            ]);
            if ($res !== false) {
                Db::table('complaint_message')->insert([
                    'trade_no'  => $trade_no,
                    'content'   => $desc,
                    'create_at' => time(),
                ]);

                //投诉申请成功，指定的订单作废，不允许该订单的资金解冻。
                $res = Db::table('auto_unfreeze')->where(['trade_no' => $order->trade_no])->update(['status' => -1]);

                if ($res) {

                    //冻结订单
                    $res = Db::table('order')->where(['trade_no' => $order->trade_no])->update(['is_freeze' => 1]);
                    if ($res) {

                        //判断是否 T0 结算的订单，如果是，需要扣除商家余额
                        if (0 == $order->settlement_type) {

                            $user    = Db::table('user')->where('id', $order->user->id)->lock(true)->find();
                            $balance = round($user['money'] - $order->total_price, 3);
                            Db::table('user')->where('id', $user['id'])->update(['money' => ['exp', 'money-' . $order->total_price], 'freeze_money' => ['exp', 'freeze_money+' . $order->total_price]]);
                            // 记录用户金额变动日志
                            record_user_money_log('freeze', $user['id'], $order->total_price, $balance, "T0订单被投诉，冻结金额：{$order->total_price}元");
                        }

                        Db::commit();
                        $sms = new Sms;
                        // 向买家发送投诉短信
                        $sms->sendComplaintPwd($mobile, $trade_no, $code);
                        // 向卖家发送投诉成功短信
                        $sms->sendComplaintNotify($order->user->mobile, $trade_no);
                        $token = md5(md5(time()).rand(1000,5000));
                        session('token',$token);
                        $this->success('投诉成功！', url('Index/order/complaintpass', ['trade_no' => $trade_no, 'token' => $token]));
                    }
                }
            }

            Db::rollback();
            $this->error('操作失败，请重试！');
        } catch (Exception $e) {
            Db::rollback();
            $this->error('操作失败，请重试！' . $e->getMessage());
        }
    }

    /**
     * 投诉查询页
     */
    public function complaintquery() {
        return $this->fetch();
    }

    /**
     * 投诉撤销
     */
    public function complaintCancel() {
        if ($this->request->isPost()) {
            $tradeNo   = input('trade_no/s', '');
            $pwd       = input('pwd/s', '');
            $complaint = ComplaintModel::where(['trade_no' => $tradeNo, 'pwd' => $pwd])->find();
            if ($complaint) {
                DB::startTrans();
                try {
                    $complaint->status = -1;
                    $res               = $complaint->save();
                    if ($res) {
                        //买家撤诉，该笔订单可以解冻
                        $res = Db::table('auto_unfreeze')->where(['trade_no' => $complaint->trade_no])->update(['status' => 1]);
                        if ($res) {
                            //资金状态修改成功，解冻订单
                            $res = Db::table('order')->where(['trade_no' => $complaint->trade_no])->update(['is_freeze' => 0]);

                            $order = OrderModel::get(['trade_no' => $tradeNo]);
                            //判断是否 T0 结算的订单，如果是，需要返还商家余额
                            if (0 == $order->settlement_type) {
                                $user    = Db::table('user')->where('id', $order->user->id)->lock(true)->find();
                                $balance = round($user['money'] + $order->total_price, 3);
                                Db::table('user')->where('id', $user['id'])->update(['money' => ['exp', 'money+' . $order->total_price], 'freeze_money' => ['exp', 'freeze_money-' . $order->total_price]]);
                                // 记录用户金额变动日志
                                record_user_money_log('freeze', $user['id'], $order->total_price, $balance, "T0订单投诉撤诉，解冻金额：{$order->total_price}元");
                            }

                            DB::commit();
                            return J(200, '撤销成功！');
                        }
                    }
                } catch (Exception $e) {
                    DB::rollback();
                    return J(500, '撤销失败，如有问题请联系客服处理');
                }
            }

            DB::rollback();
            return J(500, '密码不正确，如有问题请联系客服处理');
        }
    }

    /**
     * 投诉查询密码页
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function complaintPass() {
        if ($this->request->isPost()) {
            $tradeNo   = input('trade_no/s', '');
            $pwd       = input('pwd/s', '');
            $complaint = ComplaintModel::where(['trade_no' => $tradeNo, 'pwd' => $pwd])->find();
            if ($complaint) {
                //设置 cookie 半小时有效
                cookie('complaint_order', $tradeNo, ['expire' => '1800']);
                cookie('complaint_pwd', $pwd, ['expire' => '1800']);
                $token = md5(time() . md5(time()) . time()) . time();
                session('token', $token);
                return J(200, '密码正确！', '', url('Index/Order/complaintDetail') . '?token=' . $token);
            } else {
                return J(500, '密码不正确，如有问题请联系客服处理');
            }
        }

        $token = input('token/s', '');
        if (empty($token) || $token != session('token')) {
            return json(['msg' => '非法请求']);
        }

        $tradeNo = input('trade_no/s', '');
        if ($tradeNo) {
            $complaint = ComplaintModel::where(['trade_no' => $tradeNo])->find();
            if ($complaint) {
                $this->assign('complaint', $complaint);
            }
        }
        return $this->fetch('complaint_pass');
    }

    /**
     * 投诉详情
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function complaintDetail() {
        $token = input('token/s', '');
        if (empty($token) || $token != session('token')) {
            return json(['msg' => '非法请求']);
        }

        //获取投诉内容
        $tradeNo   = cookie('complaint_order');
        $pwd       = cookie('complaint_pwd');
        $complaint = ComplaintModel::where(['trade_no' => $tradeNo, 'pwd' => $pwd])->find();

        if ($complaint) {
            $this->assign('complaint', $complaint);

            //延长 cookie 的有效期
            cookie('complaint_order', $tradeNo, ['expire' => '1800']);
            cookie('complaint_pwd', $pwd, ['expire' => '1800']);

            //获取投诉对话内容
            $messages = DB::name('complaint_message')->where(['trade_no' => $tradeNo])->select();
            $this->assign('messages', $messages);

            return $this->fetch('complaint_detail');
        } else {
            //清除 cookie
            cookie('complaint_order', null);
            cookie('complaint_pwd', null);
            $this->error('登录已过期，请重新登录');
        }
    }

    /**
     * 发送沟通内容
     *
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function complaintSend() {
        if ($this->request->isPost()) {
            $content = input('content/s', '');
            if (empty($content)) {
                return J(500, '请输入沟通内容');
            }

            $tradeNo   = cookie('complaint_order');
            $pwd       = cookie('complaint_pwd');
            $complaint = ComplaintModel::where(['trade_no' => $tradeNo, 'pwd' => $pwd])->find();

            if ($complaint) {
                $data = [
                    'trade_no'  => $tradeNo,
                    'content'   => $content,
                    'create_at' => time(),
                ];
                ComplaintMessage::create($data);
                return J(200, '发送成功');
            } else {
                return J(500, '登录超时，请重新登录');
            }
        }
    }

    /**
     * 发送投诉图片
     *
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function complaintImg() {
        if ($this->request->isPost()) {
            //获取上传文件
            $file = $this->request->file('image');

            if ($file) {
                //检查文件的扩展名
                $ext = strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'gif', 'png'])) {
                    //检查投诉是否存在
                    $tradeNo   = cookie('complaint_order');
                    $pwd       = cookie('complaint_pwd');
                    $complaint = ComplaintModel::where(['trade_no' => $tradeNo, 'pwd' => $pwd])->find();
                    if ($complaint) {
                        //保存图片
                        $md5      = [uniqid(), uniqid()];
                        $filename = join('/', $md5) . ".{$ext}";

                        $info = $file->move('static' . DS . 'upload' . DS . $md5[0], $md5[1], true);

                        if ($info) {
                            $file_url = FileService::getFileUrl($filename, 'local');
                            $data     = [
                                'trade_no'     => $tradeNo,
                                'content'      => $file_url,
                                'content_type' => '1',
                                'create_at'    => time(),
                            ];
                            ComplaintMessage::create($data);
                            return J(200, '发送成功');
                        } else {
                            return J(500, '发送失败，请稍候再试');
                        }
                    } else {
                        return J(500, '登录超时，请重新登录');
                    }
                } else {
                    return J(500, '发送失败，不支持的图片文件格式');
                }
            } else {
                return J(500, '请上传举证图片');
            }
        }
    }

    /**
     * 验证码
     */
    public function chkcode() {
        $captcha           = new Captcha();
        $captcha->fontSize = 30;
        $captcha->length   = 4;
        $captcha->useNoise = true;
        return $captcha->entry('order.query');
    }

    /**
     * 验证验证码
     */
    public function verifyCode() {
        $code = input('chkcode/s', '');
        if (verify_code($code, 'order.query')) {
            //验证成功之后保存验证码到session中，查询的时候判断是否超时
            $key                   = $this->authcode($this->seKey) . 'orderquery';
            $secode                = [];
            $secode['verify_code'] = $code; // 把校验码保存到session
            $secode['verify_time'] = time(); // 验证码创建时间
            Session::set($key, $secode, '');

            return 'ok';
        } else {
            return 'faile';
        }
    }

    /* 加密验证码 */
    private function authcode($str) {
        $key = substr(md5($this->seKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }
}
