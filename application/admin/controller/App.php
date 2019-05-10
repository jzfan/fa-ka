<?php

namespace app\admin\controller;

use controller\BasicAdmin;
use think\Db;

class App extends BasicAdmin
{

    public function index()
    {
        if (!$this->request->isPost()) {
            return $this->fetch();
        }

        $version = input('version/s');
        if(empty($version)){
            $this->error('请输入版本号');
        }
        $data = [
            'platform' => input('platform/s'),
            'version' => $version,
        ];

        //判断是否已经存在相同的版本号
        $res = Db::name('app_version')->where($data)->find();
        if($res){
            $this->error('已存在相同版本');
        }

        $platform = input('platform/s');
        $url = '';
        if ($platform == 'android') {
            $package = getUploadFile('package', true, ['apk']);

            if (!$package['status']) {
                $this->error($package['msg']);
            } else {
                $url = $package['data']['file'];
            }
        } else {
            $url = input('appstore_url/s');
        }

        $data = array_merge($data,[
            'package' => $url,
            'create_at' => time(),
            'create_ip' => $_SERVER['REMOTE_ADDR'],
            'remark' => input('remark/s')
        ]);

        $res = Db::name('app_version')->insert($data);
        if ($res) {
            $this->success('新增版本成功');
        } else {
            $this->error('新增版本失败');
        }
    }
}
