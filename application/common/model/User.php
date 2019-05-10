<?php

namespace app\common\model;

use app\common\util\Email;
use app\common\util\Sms;
use service\MerchantLogService;
use think\Loader;
use think\Model;
use think\Db;
use think\Request;
use think\Validate;

class User extends Model
{
    public function collect()
    {
        return $this->hasOne('UserCollect', 'user_id');
    }

    public function goodsList()
    {
        return $this->hasMany('Goods', 'user_id');
    }

    public function categorys()
    {
        return $this->hasMany('GoodsCategory', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany('Order', 'user_id');
    }

    public function messagesByFrom()
    {
        return $this->hasMany('Message', 'from_id');
    }

    public function messagesByTo()
    {
        return $this->hasMany('Message', 'to_id');
    }

    public function messages()
    {
        return $this->hasMany('Message', 'user_id');
    }

    public function getParentAttr($value, $data)
    {
        return $this->where(['id' => $data['parent_id']])->find();
    }

    public function getSubUserCountAttr($value, $data)
    {
        return $this->where(['parent_id' => $data['id']])->count();
    }

    public function rate()
    {
        return $this->hasMany('UserRate', 'user_id');
    }

    public function cashs()
    {
        return $this->hasMany('UserCash', 'user_id');
    }

    public function rebates()
    {
        return $this->hasMany('UserRebate', 'user_id');
    }

    public function complaints()
    {
        return $this->hasMany('Complaint', 'user_id');
    }

    /**
     * 获取投诉次数
     */
    public function getComplaintCountAttr($value, $data)
    {
        return $this->complaints()->count();
    }

    public function loginLogs()
    {
        return $this->hasMany('UserLoginLog', 'user_id');
    }

    public function channelStatus()
    {
        return $this->hasMany('UserChannel', 'user_id');
    }

    /**
     * 链接
     */
    public function link()
    {
        return $this->morphMany('Link', 'relation', 'user')->order('id desc');
    }

    /**
     * 获取店铺链接
     */
    public function getLinkAttr($value, $data)
    {
        $links = $this->link()->find();
        $domain = sysconf('site_shop_domain') . '/links/';
        if (!$links) {
            while (1) {
                $token = strtoupper(get_uniqid(8));

                //检测token是否存在
                $count = Db::name('link')->where('token', $token)->count();

                if ($count == 0) {
                    break;
                }
            }

            $short_url = get_short_domain($domain . $token);
            $this->link()->insert([
                'user_id' => $data['id'],
                'relation_type' => 'user',
                'relation_id' => $data['id'],
                'token' => $token,
                'short_url' => $short_url,
                'status' => 1,
                'create_at' => $_SERVER['REQUEST_TIME'],
            ]);
        }
        return $domain . $this->link()->value('token');
    }

    /**
     * 获取店铺短链接
     * @param $value
     * @param $data
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getShortLinkAttr($value, $data)
    {
        $links = $this->link()->find();
        if (!$links) {
            $domain = sysconf('site_shop_domain') . '/links/';
            while (1) {
                $token = strtoupper(get_uniqid(8));

                //检测token是否存在
                $count = Db::name('link')->where('token', $token)->count();

                if ($count == 0) {
                    break;
                }
            }

            $short_url = get_short_domain($domain . $token);
            $this->link()->insert([
                'user_id' => $data['id'],
                'relation_type' => 'user',
                'relation_id' => $data['id'],
                'token' => $token,
                'short_url' => $short_url,
                'status' => 1,
                'create_at' => $_SERVER['REQUEST_TIME'],
            ]);
        } else {
            $short_url = $links['short_url'];
        }
        return $short_url;
    }

    /**
     * 获取链接状态
     */
    public function getLinkStatusAttr($value, $data)
    {
        return $this->link()->value('status');
    }

    /**
     * 登录
     * @param $username
     * @param $password
     * @param $platform
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    static function login($username, $password, $platform = 'web')
    {

        $user = Db::name('user')->field(['id', 'username', 'status', 'is_freeze', 'password'])
            ->where('username', '=', $username)->find();

        if (empty($user)) {
            $user = Db::name('user')->field(['id', 'username', 'status', 'is_freeze', 'password'])
                ->where('mobile', '=', $username)->find();
        }

        if (!$user) {
            return [
                'status' => false,
                'code' => 414,
                'msg' => '不存在该账号！',
            ];
        }

        if ($user['status'] == 0) {
            return [
                'status' => false,
                'code' => 500,
                'msg' => '该账号已被禁用！',
            ];
        }
        //检查是否冻结
        if ($user['is_freeze'] == 1) {
            return [
                'status' => false,
                'code' => 500,
                'msg' => '该账号已被冻结！',
            ];
        }
        //删除今天零时以前的错误密码登录记录
        $start_time = strtotime(date('Y-m-d'));
        $end_time = $start_time + 60 * 60 * 24 - 1;
        Db::name('user_login_error_log')->where('login_time<' . $start_time)->delete();
        $error_count = Db::name('user_login_error_log')->where(['login_name' => $username, 'user_type' => 0, 'login_time' => ['BETWEEN', [$start_time, $end_time]]])->count();
        if ($error_count >= sysconf('wrong_password_times')) {
            $last_time = Db::name('user_login_error_log')->where(['login_name' => $username, 'user_type' => 0])->order('id DESC')->limit(1)->value('login_time');
            $time = $last_time + 24 * 60 * 60 - time();
            $time_str = sec2Time($time);
            return [
                'status' => false,
                'code' => 500,
                'msg' => '输入错误密码超限，账户已被锁定，将于' . $time_str . '后自动解锁!',
            ];
        }
        if ($user['password'] != md5($password)) {
            $plog['login_name'] = $username;
            $plog['password'] = $password;
            $plog['user_type'] = 0;
            $plog['login_from'] = 0;
            $plog['login_time'] = time();
            Db::name('user_login_error_log')->insert($plog);
            $error_count++;
            if ($error_count >= sysconf('wrong_password_times')) {
                return [
                    'status' => false,
                    'code' => 500,
                    'msg' => '密码错误，您的账号已被锁定，将于24小时后自动解锁！',
                ];
            } else {
                return [
                    'status' => false,
                    'code' => 414,
                    'msg' => '密码错误，请重新输入，您还有' . (sysconf('wrong_password_times') - $error_count) . '次机会!',
                ];
            }
        }
        // 记录登录日志
        UserLoginLog::create([
            'user_id' => $user['id'],
            'ip' => Request::instance()->ip(),
            'platform' => $platform,
            'create_at' => $_SERVER['REQUEST_TIME'],
        ]);
        session('merchant.user', $user['id']);
        session('merchant.username', $user['username']);
        MerchantLogService::write('登录成功', '登录成功');

        return [
            'status' => true,
            'data' => $user,
            'msg' => '登录成功',
        ];
    }

    /**
     * 注册
     * @param $data array 注册数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    static function register($data)
    {
        // 检测站点注册状态
        if (sysconf('site_register_status') == 0) {
            return [
                'status' => false,
                'code' => 500,
                'msg' => '暂未开启注册!',
            ];
        }

        $ip = Request::instance()->ip();
        // 检查当前IP当日注册次数
        if (sysconf('ip_register_limit') > 0) {
            if (FALSE === registerIpCheck($ip)) {
                return [
                    'status' => false,
                    'code' => 500,
                    'msg' => 'IP：' . $ip . '，今日注册次数超限！',
                ];
            }
        }

        $validate = Loader::validate('app\common\validate\Register');

        if (!$validate->check($data)) {
            return [
                'status' => false,
                'code' => 414,
                'msg' => $validate->getError()
            ];
        }

        //检查邀请码
        if (sysconf('spread_invite_code') == 1 && sysconf('is_need_invite_code') == 1 && $data['invite_code'] == '') {
            if ($data['invite_code'] === '') {
                return [
                    'status' => false,
                    'code' => 414,
                    'msg' => '邀请码不能为空!',
                ];
            }

            $code = Db::name('invite_code')
                ->where(['code' => $data['invite_code'], 'status' => 0])
                ->find();

            if (!$code) {
                return [
                    'status' => false,
                    'code' => 414,
                    'msg' => '邀请码不正确!',
                ];
            }
            if ($code->expire_at < time()) {
                return [
                    'status' => false,
                    'code' => 414,
                    'msg' => '邀请码已过期!',
                ];
            }
            $parent_id = $code->user_id;
            $invite_type = 1;
        }

        // 检测注册是否需要短信验证码
        if (sysconf('site_register_smscode_status') == 1) {
            $verify = null;
            if (sysconf('site_register_code_type') == 'sms') {
                $verify = new Sms();
                $verifyData = $data['mobile'];
            } elseif (sysconf('site_register_code_type') == 'email') {
                $verify = new Email();
                $verifyData = $data['email'];
            }

            if ($verify && !$verify->verifyCode($verifyData, $data['chkcode'], 'register')) {
                return [
                    'status' => false,
                    'code' => 414,
                    'msg' => $verify->getError(),
                ];
            }
        }

        if (!isset($parent_id)) {
            // 检测推广注册
            $parent_id = input('spread_userid/d', 0);
            $invite_type = 2;
        }
        if ($parent_id > 0) {
            $user = Db::table('user')->lock(true)->where('id', $parent_id)->find();
            if ($user) {
                $data['parent_id'] = $user['id'];
            }
        }
        $data['password'] = md5($data['password']);
        $data['money'] = 0;
        // 检测注册是否自动审核
        if (sysconf('site_register_verify') == 1) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $data['create_at'] = $_SERVER['REQUEST_TIME'];
        $data['ip'] = $ip;
        $UserModel = new self($data);
        $res = $UserModel->allowField(true)->save();
        if ($res) {
            //获取成功，获取 userid
            $res = Db::name('user')->where('username', $data['username'])->value('id');
            if (isset($code) && isset($user) && !empty($user)) {
                $code->invite_uid = $res;
                $code->invite_at = $_SERVER['REQUEST_TIME'];
                $code->status = 1;
                $code->save();
            }
            $spread_reward_money = sysconf('spread_reward_money');
            if (sysconf('spread_reward') == 1 && $spread_reward_money > 0 && isset($user) && !empty($user)) {
                self::update([
                    'money' => ['exp', 'money+' . $spread_reward_money]
                ], ['id', '=', $user['id']]);
                if ($invite_type == 1) {
                    $remark = '通过邀请码';
                } else {
                    $remark = '通过推广链接';
                }
                record_user_money_log('sub_register', $user['id'], $spread_reward_money, $user['money'] + $spread_reward_money, $remark . '成功推荐用户【' . $data['username'] . '】');
            }
            if (sysconf('site_register_verify') == 1) {
                return [
                    'status' => true,
                    'data' => $res,
                    'msg' => '注册成功!',
                ];
            } else {
                return [
                    'status' => true,
                    'data' => [],
                    'msg' => '注册成功，请联系客服QQ（' . sysconf('site_info_qq') . '）审核开通！',
                ];
            }
        } else {
            return [
                'status' => false,
                'code' => 500,
                'msg' => '注册失败，请重试！',
            ];
        }
    }
}
