<?php

namespace app\admin\controller;

use controller\BasicAdmin;
use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Request;
use think\Session;
use think\Db;
use think\captcha\Captcha;
use app\common\util\Sms;
use service\LogService;

/**
 * 系统权限管理控制器
 * Class Auth
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:13
 */
class Auth extends BasicAdmin
{

    /**
     * 默认数据模型
     * @var string
     */
    public $table = 'SystemAuth';

    /**
     * 权限列表
     */
    public function index()
    {
        $this->title = '系统权限管理';
        return parent::_list($this->table);
    }

    /**
     * 权限授权
     * @return string
     */
    public function apply()
    {
        $auth_id = $this->request->get('id', '0');
        $method = '_apply_' . strtolower($this->request->get('action', '0'));
        if (method_exists($this, $method)) {
            return $this->$method($auth_id);
        }
        $this->assign('title', '节点授权');
        return $this->_form($this->table, 'apply');
    }

    /**
     * 读取授权节点
     * @param $auth_id
     */
    protected function _apply_getnode($auth_id)
    {
        $nodes = NodeService::get();
        $checked = Db::name('SystemAuthNode')->where(['auth' => $auth_id])->column('node');
        foreach ($nodes as &$node) {
            $node['checked'] = in_array($node['node'], $checked);
        }
        $all = $this->_apply_filter(ToolsService::arr2tree($nodes, 'node', 'pnode', '_sub_'));
        $this->success('获取节点成功！', '', $all);
    }

    /**
     * 保存授权节点
     * @param $auth_id
     */
    protected function _apply_save($auth_id)
    {
        list($data, $post) = [[], $this->request->post()];
        foreach (isset($post['nodes']) ? $post['nodes'] : [] as $node) {
            $data[] = ['auth' => $auth_id, 'node' => $node];
        }
        Db::name('SystemAuthNode')->where(['auth' => $auth_id])->delete();
        Db::name('SystemAuthNode')->insertAll($data);
        LogService::write('系统权限', '节点授权成功');
        $this->success('节点授权更新成功！', '');
    }

    /**
     * 节点数据拼装
     * @param array $nodes
     * @param int $level
     * @return array
     */
    protected function _apply_filter($nodes, $level = 1)
    {
        foreach ($nodes as $key => &$node) {
            if (!empty($node['_sub_']) && is_array($node['_sub_'])) {
                $node['_sub_'] = $this->_apply_filter($node['_sub_'], $level + 1);
            }
        }
        return $nodes;
    }

    /**
     * 权限添加
     */
    public function add()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 权限编辑
     */
    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 权限禁用
     */
    public function forbid()
    {
        if (DataService::update($this->table)) {
            LogService::write('系统权限', '权限禁用成功');
            $this->success("权限禁用成功！", '');
        }
        $this->error("权限禁用失败，请稍候再试！");
    }

    /**
     * 权限恢复
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            LogService::write('系统权限', '权限启用成功');
            $this->success("权限启用成功！", '');
        }
        $this->error("权限启用失败，请稍候再试！");
    }

    /**
     * 权限删除
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            $id = $this->request->post('id');
            Db::name('SystemAuthNode')->where(['auth' => $id])->delete();
            LogService::write('系统权限', '权限删除成功');
            $this->success("权限删除成功！", '');
        }
        $this->error("权限删除失败，请稍候再试！");
    }

    /**
     * 谷歌令牌验证
     */
    public function google()
    {
        if (!session('user')) {
            $this->error('未登录');
        }
        if(sysconf('is_google_auth') == 0) {
            $this->error('系统未开启谷歌身份验证', '@admin');
        }
        $google_auth = session('google_auth');
        if($google_auth) {
            $this->redirect('@admin');
        }
        $ga = new \Util\Verify\PHPGangsta_GoogleAuthenticator();
        $google_token = Db::name('system_user')->where('id',session('user')['id'])->value('google_secret_key');
        if (!$this->request->isPost()) {
            if($google_token == '') {
                $secret = $ga->createSecret();
                $qrCodeUrl = $ga->getQRCodeGoogleUrl(Request::instance()->domain(), $secret);
                session('google_secret_key', $secret);
                $this->assign('secret', $secret);
                $this->assign('qrCodeUrl', $qrCodeUrl);

            }
            $this->assign('action_type', $google_token == '' ? 0 : 1);
            $this->assign('google_token', $google_token);
            return view();
        } else {
            $action_type = input('action_type/d', 0);
            $code = $this->request->post('code', '', 'trim');
            if($code == '') {
                $this->error("请输入验证码");
            }
            if($action_type == 0) {//首次绑定
                $google_secret_key = session('google_secret_key');
                if(!$google_secret_key) {
                    $this->error("绑定失败，请刷新页面重试");
                }
                $oneCode = $ga->getCode($google_secret_key);
                if($code !== $oneCode) {
                    $this->error("验证码错误");
                } else {
                    $re = Db::name('system_user')->where(['id'=>session('user')['id']])->update(['google_secret_key'=>$google_secret_key]);
                    if(FALSE !== $re) {
                        session('google_auth', $oneCode);
                        session('google_secret_key', null);
                        $this->success("绑定成功", '@admin');
                    } else {
                        $this->error("绑定失败，请售后重试");
                    }
                }
            } else {
                $google_secret_key = Db::name('system_user')->where(['id'=>session('user')['id']])->value('google_secret_key');
                if($google_secret_key == '') {
                    $this->error("您未绑定谷歌身份验证器");
                }
                $captcha = $this->request->post('captcha_code', '', 'trim');
                $captchaClass = new Captcha();
                if(!$captchaClass->check($captcha)) {
                    $this->error('图形验证码错误!');
                }
                $oneCode = $ga->getCode($google_secret_key);
                if($code != $oneCode) {
                    $this->error("身份验证码错误");
                } else {
                    session('google_auth', $oneCode);
                    LogService::write('系统权限', '绑定谷歌身份验证器成功');
                    $this->success("验证通过，正在进入系统...", '@admin');
                }
            }

        }
    }
    public function resetGoogle()
    {
        if (!session('user')) {
            $this->error('未登录');
        }
        if(sysconf('is_google_auth') == 0) {
            $this->error('系统未开启谷歌身份验证');
        }
        $mobile = session('user')['phone'];
        $google_auth = session('google_auth');
        if($google_auth) {
            $this->redirect('@admin');
        }
        if (!$this->request->isPost()) {
            if($mobile) {
                $mobile = substr($mobile, 0, 3).'****'.substr($mobile, 7);
            }
            $this->assign('mobile', $mobile);
            return view();
        } else {
            $code = input('sms_code');
            if($code == '') {
                $this->error('请输入验证码');
            }
            $sms=new Sms();
            if(!$sms->verifyCode($mobile, $code,'google_auth')){
                $this->error($sms->getError());
            }
            $re = Db::name('system_user')->where(['id'=>session('user')['id']])->update(['google_secret_key'=>'']);
            if(FALSE !== $re) {
                LogService::write('系统权限', '谷歌身份验证器验证通过');
                $this->success('验证通过，进入下一步...','admin/auth/google');
            } else {
                $this->error("重置失败");
            }
        }
    }

    //生成绑定谷歌身份验证器二维码（测试，部署生产环境时删除！）
    public function bindGoogle()
    {
        $id = input('id/d');
        if(!$id) {
            return false;
        }
        $ga = new \Util\Verify\PHPGangsta_GoogleAuthenticator();
        $google_secret_key = Db::name('system_user')->where(['id'=>$id])->value('google_secret_key');
        if(!$google_secret_key) {
            return false;
        }
        $qrCodeUrl = $ga->getQRCodeGoogleUrl(Request::instance()->domain(), $google_secret_key);
        echo '<img src="'.$qrCodeUrl.'"">';die;
    }

    /**
     * 发送短信验证码
     */
    public function sendSmsCode()
    {
        if (!session('user')) {
            $this->error('未登录');
        }
        $mobile=session('user')['phone'];
        if(!is_mobile_number($mobile)){
            $this->error('不是有效的号码！');
        }
        $sms=new Sms();
        $res=$sms->sendCode($mobile, 'google_auth');
        if($res===false){
            $this->error($sms->getError());
        }
        $this->success('已发送验证码，请注意查收！！');
    }
}
