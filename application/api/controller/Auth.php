<?php

namespace app\api\controller;

use app\common\model\UserLoginLog;
use app\common\util\Sms;
use service\MerchantLogService;
use think\Db;
use think\exception\PDOException;
use think\Request;

/**
 * 鉴权类
 * Class Auth
 * @package app\api\controller
 */
class Auth extends ApiBase
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * 登录
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function login()
    {
        $this->limitRequestMethod('POST');

        $username = input('username/s');
        $password = input('password/s');
        $platform = input('platform/s');

        if (empty($username) || empty($password)) {
            error(414, '请提供账号密码');
        }

        //获取真正的密码
        $password = $this->getRealPassword($platform, $password);

        $res = \app\common\model\User::login($username, $password);
        if ($res['status']) {
            //登录成功
            $user = $res['data'];
            //绑定微信小程序
            if ($platform == 'wxapp') {
                $this->bindWxapp($user['id'], input('openid/s'), input('unionid/s'));
            }

            $res = $this->generateLoginToken($user['id'], $platform);
            if ($res['status']) {
                success($res['data'], $res['msg']);
            }
        }

        error($res['code'], $res['msg']);
    }

    /**
     * 绑定微信小程序
     * @param $userid
     * @param $openid
     * @param $unionid
     */
    protected function bindWxapp($userid, $openid, $unionid)
    {
        $update = [];

        if ($openid) {
            $update['wx_openid'] = $openid;
        }

        if ($unionid) {
            $update['unionid'] = $unionid;
        }

        if (!empty($update)) {
            \app\common\model\User::update($update, ['id' => $userid]);
        }
    }

    /**
     * 微信/微信小程序登录
     * @throws PDOException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function loginByopenid()
    {
        $this->limitRequestMethod('POST');

        $platform = input('platform');
        $openid = input('openid');

        $where = [];
        switch ($platform) {
            case 'wxapp':
                $where = ['wxapp_openid' => ['=', $openid]];
                break;
            case '':
                $where = ['openid' => ['=', $openid]];
                break;
            default:
                error(411, '暂不支持的平台');
        }

        //检查用户是否已经绑定过
        $record = Db::name('user')->field('id')->where($where)->find();

        if ($record) {
            //已经绑定过，直接返回登录 token
            $res = $this->generateLoginToken($record['id'], $platform);
            if ($res['status']) {
                success($res['data'], $record['msg']);
            } else {
                error($res['code'], $res['msg']);
            }
        } else {
            error(413, '登录失败，账号未绑定');
        }
    }

    /**
     * 生成登录 token
     * @param $userid
     * @param $platform
     * @param string $refreshToken
     * @return array
     * @throws PDOException
     * @throws \think\Exception
     */
    protected function generateLoginToken($userid, $platform, $refreshToken = '')
    {
        //生成登录凭证(用户 ID，用户平台，生成时间，随机数)
        $tokenStr = $userid . '$' . $platform . '$' . time() . '$' . rand(100, 999);
        $token = $this->encrypt($platform, $tokenStr);

        if ($refreshToken) {
            //刷新 token
            $res = Db::name('user_token')->where([
                'platform' => ['=', $platform],
                'user_id' => ['=', $userid],
                'refresh_token' => ['=', $refreshToken],
            ])->update([
                'token' => $token,
                'expire_at' => time() + 86400,
            ]);

            if ($res) {
                return [
                    'status' => true,
                    'data' => [
                        'token' => $token,
                        'refresh_token' => $refreshToken,
                    ],
                    'msg' => '登录凭证刷新成功'
                ];
            }
        } else {
            // 生成新的登录 token
            $refreshToken = md5($tokenStr);

            Db::name('user_token')->where([
                'platform' => ['=', $platform],
                'user_id' => $userid
            ])->delete();

            $res = Db::name('user_token')->insert([
                'platform' => $platform,
                'user_id' => $userid,
                'created_at' => time(),
                'expire_at' => time() + 86400,
                'token' => $token,
                'refresh_token' => $refreshToken,
            ]);

            if ($res) {
                return [
                    'status' => true,
                    'data' => [
                        'token' => $token,
                        'refresh_token' => $refreshToken,
                    ],
                    'msg' => '登录成功'
                ];
            }
        }

        return [
            'status' => false,
            'code' => 500,
            'msg' => '登录凭证生成失败'
        ];
    }

    /**
     * 刷新登录凭证
     * @throws PDOException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function refreshLoginToken()
    {
        $this->limitRequestMethod('POST');

        $platform = input('platform/s');
        $refresh_token = input('refresh_token/s');

        //30天内的刷新凭证
        $record = Db::name('user_token')->where([
            'platform' => $platform,
            'refresh_token' => $refresh_token,
        ])->whereTime('created_at', '-30 days')->find();

        if ($record) {
            //刷新凭证有效，生产新的登陆凭证
            $res = $this->generateLoginToken($record['user_id'], $platform, $refresh_token);

            if ($res['status']) {
                success($res['data'], $res['msg']);
            } else {
                error($res['code'], $res['msg']);
            }

        }

        error(414, '刷新凭据无效或已过期');
    }

    /**
     * 注册
     * @throws PDOException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register()
    {
        $this->limitRequestMethod('POST');

        //检查密码
        $password = input('password/s', '');
        if (empty($password)) {
            error(414, '请填写密码');
        }

        $platform = input('platform/s', '');

        //密码解密
        $password = $this->decrypt($platform, $password);

        //获取注册信息
        $data = [
            'username' => input('username/s', ''),
            'mobile' => input('mobile/s', ''),
            'email' => input('email/s', ''),
            'qq' => input('qq/s', ''),
            'password' => $password,
            'chkcode' => input('chkcode/s', ''),
            'invite_code' => input('invite_code/s', '')
        ];

        //账号注册
        $res = \app\common\model\User::register($data);
        if ($res['status']) {
            session('merchant.user', $res['data']);
            session('merchant.username', $data['username']);
            MerchantLogService::write('注册成功', '注册成功');

            if (sysconf('site_register_verify') == 1) {
                //如果开了自动审核，返回 token

                //记录登录日志
                UserLoginLog::create([
                    'user_id' => $res['data'],
                    'ip' => Request::instance()->ip(),
                    'platform' => $platform,
                    'create_at' => $_SERVER['REQUEST_TIME'],
                ]);
                
                $res = $this->generateLoginToken($res['data'], $platform);
                if ($res['status']) {
                    success($res['data'], $res['msg']);
                }
            }
            success($res['data'], $res['msg']);
        } else {
            error($res['code'], $res['msg']);
        }
    }

    /**
     * 忘记密码
     * @throws PDOException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function forgot()
    {
        $this->limitRequestMethod('POST');

        $mobile = input('mobile/s');
        $chkcode = input('chkcode/s');
        $platform = input('platform/s');
        $password = input('password/s');
        $passwordConfirm = input('password_confirm/s');

        //校验手机号
        if (empty($mobile)) {
            error(414, '请填写手机号码');
        }

        //校验密码
        if (empty($password)) {
            error(414, '请填写密码');
        }

        if ($password != $passwordConfirm) {
            error(414, '两次密码不一致');
        }

        //校验验证码
        $chkcodeValidate = new Sms();
        if (empty($chkcode) || !$chkcodeValidate->verifyCode($mobile, $chkcode, 'forgot')) {
            error(414, '验证码不正确');
        }

        //重置密码
        $password = $this->getRealPassword($platform, $password);

        $res = Db::name('user')->where(['mobile' => $mobile])->update(['password' => md5($password)]);
        if ($res) {

            //如果此时用户已经登录，退出用户的登录
            $loginToken = $this->getHeader('HTTP_ZUY_TOKEN');
            if (!empty($loginToken)) {
                Db::name('user_token')->where(['token' => $loginToken])->delete();
            }

            success([], '密码重置成功');
        } else {
            error(500, '密码重置失败');
        }
    }
}