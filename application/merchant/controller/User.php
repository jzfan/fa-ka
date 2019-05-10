<?php

namespace app\merchant\controller;

use app\common\util\Sms;
use app\common\model\UserCollect as UserCollectModel;
use app\common\model\UserLoginLog;
use app\common\model\UserChannel as UserChannelModel;
use app\common\model\User as UserModel;
use service\MerchantLogService;

class User extends Base {
    // 商家设置
    public function settings() {
        if (!$this->request->isPost()) {
            $token = md5(time() . md5(time()) . time()) . time();
            session('register_sms_code', $token);
            session('register_sms_code_time', time());
            $this->assign('sms_token', $token);
            $this->setTitle('商家设置');
            return $this->fetch();
        }

        $mobile = $this->user->mobile;
        if (!empty($this->user->mobile)) {
            //原有手机号码，需要验证旧手机号码
            $smsCode = input('mobileCode/s');
            if ($smsCode) {
                //校验短信验证码
                $verify = new Sms();
                //通过，修改验证码，不通过
                if ($verify && !$verify->verifyCode($this->user->mobile, $smsCode, 'reset_phone')) {
                    $mobile = $this->user->mobile;
                } else {
                    $mobile = input('mobile/s', '');
                }
            }
        }

        $qq                      = input('qq/s', '');
        $subdomain               = strtolower(input('subdomain/s', ''));
        $shop_name               = input('shop_name/s', '');
        $shop_notice             = input('shop_notice/s', '');
        $shop_notice_auto_pop    = input('shop_notice_auto_pop/s', '');
        $user_notice_auto_pop    = input('user_notice_auto_pop/s', '');
        $shop_gouka_protocol_pop = input('shop_gouka_protocol_pop/s', '');
        $cash_type               = input('cash_type/s', '');
        $website                 = input('website/s', '');
        $pay_theme               = input('pay_theme/s', 'default');
        $stock_display           = input('stock_display/d', 2);
        $fee_payer               = input('fee_payer/d', 0);
        $login_auth              = input('login_auth/d', 0);
        $login_auth_type         = input('login_auth_type/d', 1);
        if (!is_mobile_number($mobile)) {
            $this->error('手机格式不正确！');
        }
        // 检查子域名
        if ($subdomain !== '') {
            // 域名过滤
            $domains = explode('|', sysconf('disabled_domains'));
            if (in_array($subdomain, $domains)) {
                $this->error('该子域名禁止使用！');
            }
            $res = UserModel::where(['id' => ['<>', $this->user->id], 'subdomain' => $subdomain])->count();
            if ($res) {
                $this->error('该子域名已被使用！');
            }
        }
        // 字词检查
        $res = check_wordfilter($shop_name);
        if ($res !== true) {
            $this->error('店铺名称包含敏感词汇“' . $res . '”！');
        }
        // 字词检查
        $res = check_wordfilter($shop_notice);
        if ($res !== true) {
            $this->error('店铺公告包含敏感词汇“' . $res . '”！');
        }
        $this->user->mobile                  = $mobile;
        $this->user->qq                      = $qq;
        $this->user->subdomain               = $subdomain;
        $this->user->shop_name               = $shop_name;
        $this->user->shop_notice             = $shop_notice;
        $this->user->shop_notice_auto_pop    = $shop_notice_auto_pop;
        $this->user->user_notice_auto_pop    = $user_notice_auto_pop;
        $this->user->shop_gouka_protocol_pop = $shop_gouka_protocol_pop;
        $this->user->pay_theme               = $pay_theme;
        $this->user->stock_display           = $stock_display;
        $this->user->website                 = $website;
        $this->user->login_auth              = $login_auth;
        $this->user->login_auth_type         = $login_auth_type;
        $this->user->cash_type               = $cash_type;
        $this->user->fee_payer               = $fee_payer;
        $res                                 = $this->user->save();
        MerchantLogService::write('商家设置', '商家设置保存成功');
        $this->success('保存成功');
    }

    /**
     * 发送短信验证码
     */
    public function sendSmsCode() {
        $code = input('chkcode/s', '');
        if (!verify_code($code, 'order.query')) {
            return J(0, '验证码错误');
        }

        //验证唯一码
        $token      = input('token/s', '');
        $smsToken   = session('register_sms_code');
        $token_time = session('register_sms_code_time');
        if (empty($smsToken) || $smsToken != $token) {
            return J(0, '唯一码错误');
        }

        $mobile = input('phone/s', '');
        if (!is_mobile_number($mobile)) {
            return J(-1, '不是有效的号码！');
        }

        if (sysconf('site_register_smscode_status') != 1 || sysconf('site_register_code_type') != 'sms') {
            return J(0, '短信已关闭');
        }

        $sms = new Sms();
        $res = $sms->sendCode($mobile, 'reset_phone');
        if ($res === false) {
            return J(-1, $sms->getError());
        }
        $token = md5(time() . md5(time()) . time()) . time();
        session('register_sms_code', $token);
        session('register_sms_code_time', time());
        return J(1, '已发送验证码到你的手机，请注意查收！！', ['token' => $token]);

    }

    // 收款信息
    public function collect() {
        if (!$this->request->isPost()) {
            return;
        }
        $data = [
            'user_id'      => $this->user->id,
            'type'         => input('type/d', 1),
            'create_at'    => $_SERVER['REQUEST_TIME'],
            'allow_update' => 0
        ];
        switch ($data['type']) {
            case 1:
                $upload = upload('ali_collect_img');
                if ($upload['code'] == 'SUCCESS') {
                    $data['collect_img'] = $upload['site_url'];
                } else {
                    $this->error($upload['msg']);
                }
                $data['info'] = input('alipay/a', []);
                break;
            case 2:
                $upload = upload('collect_img');
                if ($upload['code'] == 'SUCCESS') {
                    $data['collect_img'] = $upload['site_url'];
                } else {
                    $this->error($upload['msg']);
                }
                $data['info'] = input('wxpay/a', []);
                break;
            case 3:
                $data['info'] = input('bank/a', []);
                break;
        }

        if (1 == sysconf('idcard_auth_type')) {
            //二要素校验
            if (!idcardAuth($data['info']['idcard_number'], $data['info']['realname'])) {
                $this->error('身份证，姓名校验失败，请重试！');
            }
        }

        $CollectModel = new UserCollectModel();
        $collect      = $CollectModel->where(['user_id' => $this->user->id])->find();
        if ($collect) {
            // 更新
            $res = $collect->update($data, ['user_id' => $this->user->id]);
        } else {
            // 新增
            $res = $CollectModel::create($data);
        }
        if ($res !== false) {
            $this->success('保存成功！');
        } else {
            $this->error('保存失败，请重试！');
        }
    }

    // 修改密码
    public function password() {
        if (!$this->request->isPost()) {
            $this->setTitle('修改密码');
            return $this->fetch();
        }
        $password      = input('password/s', '');
        $new_password  = input('new_password/s', '');
        $new_password2 = input('new_password2/s', '');
        if ($this->user->password != md5($password)) {
            $this->error('旧密码不正确！');
        }
        if (!$new_password) {
            $this->error('密码不能为空！');
        }
        if ($new_password != $new_password2) {
            $this->error('两次新密码输入不一致！');
        }
        $this->user->password = md5($new_password);
        // 更新登录凭证
        $this->user->login_key = rand(1000000, 9999999);
        $res                   = $this->user->save();
        if ($res !== false) {
            session('merchant.login_key', $this->user->login_key);
            MerchantLogService::write('修改密码', '商家设置保存成功');
            $this->success('保存成功！');
        } else {
            $this->error('保存失败，请重试！');
        }
    }

    // 店铺链接
    public function link() {
        $this->setTitle('店铺链接');
        $this->assign('shop_link', $this->user->shortLink);
        return $this->fetch();
    }

    // 登录日志
    public function loginlog() {
        $this->setTitle('登录日志');
        $monthTime = strtotime('-30 day');
        $logs      = UserLoginLog::where(['user_id' => $this->user->id])->where(['create_at' => ['>=', $monthTime]])->order('id desc')->paginate(30);
        $this->assign('logs', $logs);
        $this->assign('page', $logs->render());
        return $this->fetch();
    }

    // 付款方式
    public function channel() {
        $this->setTitle('支付方式管理');
        $userChannels = get_user_channels($this->user->id);
        $this->assign('userChannels', $userChannels);
        return $this->fetch();
    }

    // 改变状态
    public function changeChannelStatus() {
        if (!$this->request->isPost()) {
            return;
        }
        $id     = input('id/d', 0);
        $status = input('status/d', 0);
        $status = $status ? 1 : 0;
        $data   = UserChannelModel::get(['user_id' => $this->user->id, 'channel_id' => $id]);
        if (!$data) {
            $res = UserChannelModel::create([
                'user_id'    => $this->user->id,
                'channel_id' => $id,
                'status'     => $status
            ]);
        } else {
            $data->status = $status;
            $res          = $data->save();
        }
        if ($res !== false) {
            return J(0, 'success');
        } else {
            return J(1, 'error');
        }
    }

    /**
     * 重置链接
     */
    public function relink() {
        $type = input('type/s', '');
        switch ($type) {
            case 'links':
                $this->user->link()->delete();
                $this->user->link;
                break;
            case 'liebiao':
                $cate_id = input('cate_id/d', 0);
                $cate    = $this->user->categorys()->where('id', $cate_id)->find();
                if ($cate) {
                    $cate->link()->delete();
                    $cate->link;
                }
                break;
            case 'details':
                $goods_id = input('goods_id/d', 0);
                $goods    = $this->user->goodsList()->where('id', $goods_id)->find();
                if ($goods) {
                    $goods->link()->delete();
                    $goods->link;
                }
                break;
        }
        $this->success('购买链接重置成功！');
    }

    /**
     * 关闭链接
     */
    public function closelink() {
        $type   = input('type/s', '');
        $status = input('status/d', 1);
        switch ($type) {
            case 'links':
                $this->user->link()->update(['status' => $status]);
                break;
            case 'liebiao':
                $cate_id = input('cate_id/d', 0);
                $cate    = $this->user->categorys()->where('id', $cate_id)->find();
                if ($cate) {
                    $cate->link()->update(['status' => $status]);
                }
                break;
            case 'details':
                $goods_id = input('goods_id/d', 0);
                $goods    = $this->user->goodsList()->where('id', $goods_id)->find();
                if ($goods) {
                    $goods->link()->update(['status' => $status]);
                }
                break;
        }
        $str = $status == 1 ? '开启' : '关闭';
        $this->success('该链接已' . $str . '成功！');
    }

    /**
     * 关闭店铺
     */
    public function closeShop() {
        $status = input('status/d', 1);
        $this->user->update(['is_close' => $status], ['id' => $this->user->id]);
        $str = $status == 1 ? '关闭' : '开启';
        $this->success('操作成功，该店铺已' . $str . '！');
    }
}
