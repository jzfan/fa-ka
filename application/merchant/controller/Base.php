<?php

namespace app\merchant\controller;

use think\Controller;
use think\Request;
use think\Config;
use think\Db;
use controller\BasicWechat;
use app\common\model\User as UserModel;

class Base extends BasicWechat {

    public function __construct() {
        //保证时区
        date_default_timezone_set("Asia/Shanghai");

        if (sysconf('login_auth') == 1 && session('merchant.login_auth')) {
            //处于安全登录检验状态，跳转到检验页面去
            $userId = session('merchant.user');
            $user   = UserModel::get($userId);

            switch ($user->login_auth_type) {
                case '1':
                    // 短信验证
                    $authUrl = url('index/user/smsAuth');
                    break;
                case '2':
                    //邮件验证
                    $authUrl = url('index/user/emailAuth');
                    break;
                case '3':
                    //google code 验证
                    $authUrl = url('index/user/googleAuth');
                    break;
                default:
                    throw new \Exception('未知安全登录验证方式');
                    break;
            }

            $this->success('请进行二次验证...', $authUrl);
        }

        if (sysconf('wx_auto_login') && isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {// 微信浏览器打开
            $user_id = session("merchant.user");
            $openid  = session('openid');
            if (!$openid) {
                $this->oAuth();
            }
            if ($openid && !$user_id) {
                if (!$user_id) {
                    $wechat_fans = Db::name('wechat_fans')->where(["openid" => $openid])->find();
                    if (empty($wechat_fans)) {
                        $this->oAuth();
                    }
                    $member = UserModel::get(["openid" => $openid]);
                    if (!empty($member) && sysconf('wx_auto_login') == 1) {//自动登录
                        session('merchant.user', $member['id']);
                        session('merchant.login_key', $member['login_key']);
                        session('merchant.username', $member['username']);
                        session('merchant.login_expire', time() + 7 * 86400);
                    }
                }
            }
        }
        $request    = request::instance();
        $modulePath = $request->module();
        //**************************模板主题**************************
        $viewPath = Config::get('template.view_path');
        empty($viewPath) && $viewPath = \think\App::$modulePath . 'view' . DS;
        //影响 view_path 的意义，作废 
//        $themeDir = basename(Config::get('theme.dir'));
//        empty($themeDir) or $viewPath = $viewPath . $themeDir . DS;

        if (!$request->isMobile()) {
            Config::set('template.view_platform', 'pc');
            Config::set('template.view_theme', 'default');
            Config::set('dispatch_error_tmpl', 'tpl/jump');
            Config::set('dispatch_success_tmpl', 'tpl/jump');
        } else {
            Config::set('template.view_platform', 'mobile');
            Config::set('template.view_theme', 'default');
            Config::set('dispatch_error_tmpl', 'tpl/jump');
            Config::set('dispatch_success_tmpl', 'tpl/jump');
        }
        Config::set('template.view_path', $viewPath);

        parent::__construct();

        $user_id    = session('merchant.user');
        $this->user = UserModel::get($user_id);

        if ($this->user['is_freeze']) {
            session(null);
            $this->error('账号已被冻结，请联系管理员', '/login');
        }

        if (!$this->user) {
            $this->redirect('/login');
        }

        if ($this->user->login_key !== session('merchant.login_key')) {
            //修改过密码，登录凭证被刷新了，重新登录
            session('merchant', null);
            $this->redirect('/login');
        }

        //校验登录是否已经过期了
        if (session('merchant.login_expire') < time()) {
            //已经过期，退出登录
            session('merchant.login_expire', null);
            session('merchant.user', null);
            session('merchant.username', null);
            session('merchant', null);
            $this->redirect('/login');
        } else {
            //登录超时倒计时重置为半小时
            session('merchant.login_expire', time() + 1800);
        }

        if ($this->user['shop_notice_auto_pop'] == 1) {
            $common_announce = [];
            //自动弹出系统公告
            if (sysconf('announce_push') == 1) {
                $common_announce = Db::name('article')->where(['status' => 1, 'cate_id' => 1])->order('id DESC')->find();
                if (!empty($common_announce)) {
                    $announce_log = Db::name('announce_log')->where(['user_id' => $user_id, 'article_id' => $common_announce['id']])->find();
                    if (empty($announce_log)) {
                        Db::name('announce_log')->insert(['user_id' => $user_id, 'article_id' => $common_announce['id'], 'create_at' => time()]);
                    } else {
                        $common_announce = [];
                    }
                }
            }
            $this->assign('common_announce', $common_announce);
        }
        $this->assign('_user', $this->user);
        $this->assign('_controller', $this->request->controller());
        $this->assign('_action', $this->request->action());
    }

    protected function setTitle($title) {
        $this->assign('_title', $title);
    }
}
