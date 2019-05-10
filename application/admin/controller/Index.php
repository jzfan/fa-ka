<?php

namespace app\admin\controller;

use controller\BasicAdmin;
use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Db;
use think\View;

/**
 * 后台入口
 * Class Index
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 10:41
 */
class Index extends BasicAdmin
{

    /**
     * 后台框架布局
     * @return View
     */
    public function index()
    {
        if (!session('?user')) {
            if(sysconf('admin_login_path') == 'admin' || sysconf('admin_login_path') == '') {
                $this->redirect('@admin/login');
            } else {
                header("HTTP/1.1 404 Not Found");
                return $this->fetch(APP_PATH.'404.html');
            }
        } else {
            //验证登录是否已经过期
            if(session('user_expire_time') < time()){
                session('user',null);
                session('user_expire_time',null);
                if(sysconf('admin_login_path') == 'admin' || sysconf('admin_login_path') == '') {
                    $this->redirect('@admin/login');
                } else {
                    header("HTTP/1.1 404 Not Found");
                    return $this->fetch(APP_PATH.'404.html');
                }
            }
            if(!session('google_auth') && sysconf('is_google_auth')) {
                if(!($this->request->controller() == 'Auth' && $this->request->action() == 'google')
                    &&!($this->request->controller() == 'Login' && $this->request->action() == 'out')
                    &&!($this->request->controller() == 'Login' && $this->request->action() == 'verifycode')
                    &&!($this->request->controller() == 'Auth' && $this->request->action() == 'resetGoogle')
                ) {
                    $this->redirect('admin/auth/google');
                }
            }
        }
        NodeService::applyAuthNode();
        $list = (array) Db::name('SystemMenu')->where(['status' => '1'])->order('sort asc,id asc')->select();
        $menus = $this->_filterMenuData(ToolsService::arr2tree($list), NodeService::get(), !!session('user'));
        return view('', ['title' => '系统管理', 'menus' => $menus]);
    }

    /**
     * 后台主菜单权限过滤
     * @param array $menus 当前菜单列表
     * @param array $nodes 系统权限节点数据
     * @param bool $isLogin 是否已经登录
     * @return array
     */
    private function _filterMenuData($menus, $nodes, $isLogin)
    {
        foreach ($menus as $key => &$menu) {
            !empty($menu['sub']) && $menu['sub'] = $this->_filterMenuData($menu['sub'], $nodes, $isLogin);
            if (!empty($menu['sub'])) {
                $menu['url'] = '#';
            } elseif (preg_match('/^https?\:/i', $menu['url'])) {
                continue;
            } elseif ($menu['url'] !== '#') {
                $node = join('/', array_slice(explode('/', preg_replace('/[\W]/', '/', $menu['url'])), 0, 3));
                $menu['url'] = url($menu['url']);
                if (isset($nodes[$node]) && $nodes[$node]['is_login'] && empty($isLogin)) {
                    unset($menus[$key]);
                } elseif (isset($nodes[$node]) && $nodes[$node]['is_auth'] && $isLogin && !auth($node)) {
                    unset($menus[$key]);
                }
            } else {
                unset($menus[$key]);
            }
        }
        return $menus;
    }

    /**
     * 主机信息显示
     * @return View
     */
    public function main()
    {
        $_version = Db::query('select version() as ver');
        return view('', ['mysql_ver' => array_pop($_version)['ver'], 'title' => '后台首页']);
    }

    /**
     * 修改密码
     */
    public function pass()
    {
        if (intval($this->request->request('id')) !== intval(session('user.id'))) {
            $this->error('只能修改当前用户的密码！');
        }
        if ($this->request->isGet()) {
            $this->assign('verify', true);
            return $this->_form('SystemUser', 'user/pass');
        }
        $data = $this->request->post();
        if ($data['password'] !== $data['repassword']) {
            $this->error('两次输入的密码不一致，请重新输入！');
        }
        $user = Db::name('SystemUser')->where('id', session('user.id'))->find();
        if (md5($data['oldpassword']) !== $user['password']) {
            $this->error('旧密码验证失败，请重新输入！');
        }
        if (DataService::save('SystemUser', ['id' => session('user.id'), 'password' => md5($data['password'])])) {
            $this->success('密码修改成功，下次请使用新密码登录！', '');
        }
        $this->error('密码修改失败，请稍候再试！');
    }

    /**
     * 修改资料
     */
    public function info()
    {
        if (intval($this->request->request('id')) === intval(session('user.id'))) {
            return $this->_form('SystemUser', 'user/form');
        }
        $this->error('只能修改当前用户的资料！');
    }

    /**
     * 代码版本更新列表
     */
    public function version()
    {
        $result = json_decode(file_get_contents(get_version_list_url()), true);
        $versions = $result['data'];
        array_pop($versions); //当前版本不需要
        return $this->fetch('version/form', ['versions' => $versions]);
    }

    /**
     * 代码版本更新
     */
    public function version_update()
    {
        $versionHash = $this->request->request('version_hash');
        if (empty($versionHash)) {
            $this->error('参数错误');
        }
        $lockFilePath = RUNTIME_PATH . 'version_update.lock';
        if (is_file($lockFilePath)) {
            $this->error('代码更新中');
        }
        //try {
            $lockFile = fopen($lockFilePath, 'w+');
            fwrite($lockFile, $versionHash);
            fclose($lockFile);
            $update_url = get_version_update_url($versionHash);
            $result = json_decode(file_get_contents($update_url), true);
            if (is_array($result) && $result['success'] == true) {
                $this->success('操作成功', null, $result);
            } else {
                $this->error('操作失败(-1)', null, $result);
            }
        //} catch (\Exception $e) {
        //    if (is_file($lockFilePath)) {
        //        unlink($lockFilePath);
        //    }
        //    $this->error('操作失败(-2)', null, $e->getMessage());
        //}
    }

    /**
     * @desc 一键更新商户后端
     */
    public function autoupdate(){
        //获取资源路径
        $fileName = config('AUTO_UPDATE_INSTALL_FILE');
        //解压缩
        $zip = new \ZipArchive();
        $res = $zip->open($fileName);
        if($res === true){
            //覆盖当前代码路径
            @chmod(ROOT_PATH,0777);
            $zip->extractTo(ROOT_PATH);
            $zip->close();
            $this->success('一键更新成功');
            //返回结果
        }else{
            //解压失败
            $this->error('一键更新失败！');
        }
    }

}
