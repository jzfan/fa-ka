<?php

namespace app\admin\controller;

use controller\BasicAdmin;
use service\DataService;
use service\NodeService;
use service\ToolsService;
use think\Db;
use service\LogService;

/**
 * 系统后台管理管理
 * Class Menu
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15
 */
class Menu extends BasicAdmin
{

    /**
     * 绑定操作模型
     * @var string
     */
    public $table = 'SystemMenu';

    /**
     * 菜单列表
     */
    public function index()
    {
        $this->title = '系统菜单管理';
        $db = Db::name($this->table)->order('sort asc,id asc');
        return parent::_list($db, false);
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_data_filter(&$data)
    {
        foreach ($data as &$vo) {
            ($vo['url'] !== '#') && ($vo['url'] = url($vo['url']));
            $vo['ids'] = join(',', ToolsService::getArrSubIds($data, $vo['id']));
        }
        $data = ToolsService::arr2table($data);
    }

    /**
     * 添加菜单
     */
    public function add()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 编辑菜单
     */
    public function edit()
    {
        return $this->_form($this->table, 'form');
    }

    /**
     * 表单数据前缀方法
     * @param array $vo
     */
    protected function _form_filter(&$vo)
    {
        if ($this->request->isGet()) {
            // 上级菜单处理
            $_menus = Db::name($this->table)->where(['status' => '1'])->order('sort asc,id asc')->select();
            $_menus[] = ['title' => '顶级菜单', 'id' => '0', 'pid' => '-1'];
            $menus = ToolsService::arr2table($_menus);
            foreach ($menus as $key => &$menu) {
                if (substr_count($menu['path'], '-') > 3) {
                    unset($menus[$key]);
                    continue;
                }
                if (isset($vo['pid'])) {
                    $current_path = "-{$vo['pid']}-{$vo['id']}";
                    if ($vo['pid'] !== '' && (stripos("{$menu['path']}-", "{$current_path}-") !== false || $menu['path'] === $current_path)) {
                        unset($menus[$key]);
                        continue;
                    }
                }
            }
            // 读取系统功能节点
            $nodes = NodeService::get();
            foreach ($nodes as $key => $node) {
                if (empty($node['is_menu'])) {
                    unset($nodes[$key]);
                }
            }
            $this->assign('nodes', array_column($nodes, 'node'));
            $this->assign('menus', $menus);
        }
    }

    /**
     * 删除菜单
     */
    public function del()
    {
        if (DataService::update($this->table)) {
            LogService::write('系统配置', '删除前台菜单成功');
            $this->success("菜单删除成功!", '');
        }
        $this->error("菜单删除失败, 请稍候再试!");
    }

    /**
     * 菜单禁用
     */
    public function forbid()
    {
        if (DataService::update($this->table)) {
            LogService::write('系统配置', '禁用菜单成功，ID:'.$this->request->post('id'));
            $this->success("菜单禁用成功!", '');
        }
        $this->error("菜单禁用失败, 请稍候再试!");
    }

    /**
     * 菜单禁用
     */
    public function resume()
    {
        if (DataService::update($this->table)) {
            LogService::write('系统配置', '启用菜单成功，ID:'.$this->request->post('id'));
            $this->success("菜单启用成功!", '');
        }
        $this->error("菜单启用失败, 请稍候再试!");
    }

}
