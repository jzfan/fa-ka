<?php

namespace app\api\controller;

use think\Controller;
use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * 配置
 * Class Config
 * @package app\api\controller
 */
class Config extends Controller
{

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    function menu()
    {

        if ($this->request->isPost()) {
            $id = input('id/d', 0);
            if ($id) {
                $this->updateMenu();
            } else {
                $this->insertMenu();
            }
        }
        return $this->fetch();
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getMenus()
    {
        $menus = Db::name('app_menu')->select();
        if (!$menus) {
            $menus = [];
        } else {
            foreach ($menus as &$item) {
                $item['menu'] = json_decode($item['menu'], 1);
                $item['menu']['id'] = $item['id'];
            }
        }
        success($menus);
    }

    /**
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    function insertMenu()
    {
        $img = getUploadFile('img', true)['data']['file'];

        //获取最大自增 id
        $function_id = input('function_id/s', '');
        if (empty($function_id)) {
            $function_id = Db::name('app_menu')->order('id desc')->value('id') + 1;
        }

        $menu = [
            'title' => input('title/s', ''),
            'img_url' => $img,
            'function_id' => $function_id,
            'sort' => input('sort/d', 0),
            'is_show' => input('is_show/b', true),
            'function_links' => input('function_links/s', ''),
            'iOS_ViewType' => input('iOS_ViewType/s', ''),
            'iOS_sotryBoard' => input('iOS_sotryBoard/s', ''),
            'iOS_ViewController' => input('iOS_ViewController/s', ''),
            'android_Ver' => input('android_Ver', '1.0'),
            'iOS_Ver' => input('iOS_Ver', '1.0'),
        ];

        $insertData = [
            'menu' => json_encode($menu),
            'function_id' => $function_id,
        ];
        $res = Db::name('app_menu')->insert($insertData);
        if ($res) {
            $this->success();
        } else {
            $this->error();
        }
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function updateMenu()
    {
        $img = getUploadFile('img', true)['data']['file'];
        if (!$img) {
            $img = input('img_url/s', '');
        }
        $menu = [
            'title' => input('title/s', ''),
            'img_url' => $img,
            'function_id' => input('function_id/s', ''),
            'sort' => input('sort/d', 0),
            'is_show' => input('is_show/b', true),
            'function_links' => input('function_links/s', ''),
            'iOS_ViewType' => input('iOS_ViewType/s', ''),
            'iOS_sotryBoard' => input('iOS_sotryBoard/s', ''),
            'iOS_ViewController' => input('iOS_ViewController/s', ''),
            'android_Ver' => input('android_Ver', '1.0'),
            'iOS_Ver' => input('iOS_Ver', '1.0'),
        ];

        $updateData = ['menu' => json_encode($menu)];
        $res = Db::name('app_menu')->where('id', input('id/d', 0))->update($updateData);
        if ($res) {
            $this->success();
        } else {
            $this->error();
        }
    }

    /**
     * 删除菜单
     */
    public function delMenu()
    {
        $id = input('id/d', '');
        if (!$id) {
            error(414, '请指定数据');
        }

        try {
            Db::name('app_menu')->where('id', $id)->delete();
            success();
        } catch (DbException $e) {
            error(500, '删除失败，原因：' . $e->getMessage());
        }
    }
}