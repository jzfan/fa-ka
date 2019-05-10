<?php

namespace app\api\controller;

use think\Db;

/**
 * 登录鉴权基类
 * 继承此基类的请求都需要校验登录状态
 * Class AuthBase
 * @package app\api\controller
 */
class AuthBase extends ApiBase
{

    protected $loginToken;
    protected $userid;

    /**s
     * AuthBase constructor.
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function __construct()
    {
        parent::__construct();
        $res = $this->checkToken();
        if (!$res['status']) {
            error($res['code'], $res['msg']);
        }
    }

    /**
     * 检查登录凭证是否有效
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function checkToken()
    {
        $loginToken = $this->getHeader('HTTP_ZUY_TOKEN');

        //检验登录 token
        if (!empty($loginToken)) {

            //校验登录凭证的有效性
            $platform = input('platform');
            $tokenRecord = Db::name('user_token')
                ->field('user_id,token,created_at,expire_at,platform')
                ->where([
                    'token' => ['=', $loginToken],
                    'platform' => ['=', $platform],
                ])->whereTime('created_at', '-30 days')->find();

            if ($tokenRecord) {
                //登录凭证有效，检查凭证是否过期
                if ($tokenRecord['expire_at'] < time()) {
                    //登录凭证已过期
                    return [
                        'status' => false,
                        'code' => 413,
                        'msg' => '登录凭证过期，请刷新凭证'
                    ];
                }

                $this->loginToken = $loginToken;
                $this->userid = $tokenRecord['user_id'];
                session('merchant.user',$this->userid);
                session('merchant.username',Db::name('user')->where('id','=',$this->userid)->value('username'));
                return [
                    'status' => true,
                ];
            }
        }

        //没有传递登录，驳回请求
        return [
            'status' => false,
            'code' => 413,
            'msg' => '未登录'
        ];
    }
}