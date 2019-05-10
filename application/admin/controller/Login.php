<?php

namespace app\admin\controller;

use controller\BasicAdmin;
use service\LogService;
use service\NodeService;
use think\Db;
use think\captcha\Captcha;

/**
 * 系统登录控制器
 * class Login
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/10 13:59
 */
class Login extends BasicAdmin
{

    /**
     * 控制器基础方法
     */
    public function _initialize()
    {
        if (session('user') && $this->request->action() !== 'out' && $this->request->action() != 'verifycode') {
            $this->redirect('@admin');
        }
    }

    /**
     * 用户登录
     * @return string
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->fetch('', ['title' => '用户登录']);
        }
        // 输入数据效验
        $username = $this->request->post('username', '', 'trim');
        $password = $this->request->post('password', '', 'trim');
        $code = $this->request->post('code', '', 'trim');
        strlen($username) < 4 && $this->error('登录账号长度不能少于4位有效字符!');
        strlen($password) < 4 && $this->error('登录密码长度不能少于4位有效字符!');
        if($code == '') {
            $this->error('请输入验证码!');
        }
        $captcha = new Captcha();
        if(!$captcha->check($code)) {
            $this->error('验证码错误!');
        }
        // 用户信息验证
        $user = Db::name('SystemUser')->where(['username' => $username, 'is_deleted' => 0])->find();
        empty($user) && $this->error('登录账号不存在，请重新输入!');
        //删除今天零时以前的错误密码登录记录
        $start_time = strtotime(date('Y-m-d'));
        $end_time = $start_time + 60*60*24 -1;
        Db::name('user_login_error_log')->where('login_time<'.$start_time)->delete();
        $error_count = Db::name('user_login_error_log')->where(['login_name'=>$username, 'user_type'=>1,'login_time'=>['BETWEEN',[$start_time, $end_time]]])->count();
        if($error_count>=sysconf('wrong_password_times')) {
            $last_time =  Db::name('user_login_error_log')->where(['login_name' => $username, 'user_type'=>1])->order('id DESC')->limit(1)->value('login_time');
            if($last_time>0) {
                $time = $last_time + 24*60*60 - time();
                $time_str = sec2Time($time);
                $this->error('输入错误密码超限，账户已被锁定，将于'.$time_str.'后自动解锁!');
            }
        }
        if($user['password'] !== md5($password)) {
            $plog['login_name'] = $username;
            $plog['password'] = $password;
            $plog['user_type'] = 1;
            $plog['login_from'] = 1;
            $plog['login_time'] = time();
            Db::name('user_login_error_log')->insert($plog);
            $error_count++;
            if($error_count>=sysconf('wrong_password_times')) {
                $this->error('登录密码与账号不匹配，您的账号已被锁定，将于24小时后自动解锁!');
            } else {
                $this->error('登录密码与账号不匹配，请重新输入，您还有'.(sysconf('wrong_password_times')-$error_count).'次机会!');
            }
        }
        empty($user['status']) && $this->error('账号已经被禁用，请联系管理!');
        // 更新登录信息
        $data = ['login_at' => ['exp', 'now()'], 'login_num' => ['exp', 'login_num+1']];
        Db::name('SystemUser')->where(['id' => $user['id']])->update($data);
        session('user', $user);
        //记住登录7天
        session('user_expire_time', time() + 86400*7);
        !empty($user['authorize']) && NodeService::applyAuthNode();
        LogService::write('系统管理', '用户登录系统成功');
        if(!session('google_auth') && sysconf('is_google_auth')) {
            if(!($this->request->controller() == 'Auth' && $this->request->action() == 'google')
                &&!($this->request->controller() == 'Login' && $this->request->action() == 'out')
                &&!($this->request->controller() == 'Login' && $this->request->action() == 'verifycode')
            ) {
                $this->success('登录成功，进行谷歌令牌二次验证...', 'admin/auth/google');
            }
        } else {
            $this->success('登录成功，正在进入系统...', '@admin');
        }
    }

    /**
     * 退出登录
     */
    public function out()
    {
        if (session('user')) {
            LogService::write('系统管理', '用户退出系统成功');
        }
        session('user', null);
        session('user_expire_time', null);
        session_destroy();
        $this->success('退出登录成功！', '@index');
    }

    /**
     * 验证码
     */
    public function verifycode()
    {
        $config =    [
            // 验证码位数
            'length'      =>    4,
            // 验证码过期时间
            'expire'      =>    300,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
}
