<?php

namespace app\api\controller;

use app\api\service\CategoryService;
use app\api\service\LinkService;
use service\MerchantLogService;
use think\Loader;

/**
 * Class Category
 *
 * @package app\api\controller
 */
class Category extends AuthBase {
    /**
     * 获取分类列表
     */
    public function lists() {
        $this->limitRequestMethod('GET');

        $where = [
            'user_id' => ['=', $this->userid],
        ];

        $config = [
            'order'  => input('order/s', 'sort desc, id desc'),
            'fields' => 'id as category_id, user_id, name, sort, status, create_at, theme',
            'page'   => input('page/d', 1),
            'limit'  => input('limit/d', 0),
        ];

        CategoryService::getLists($where, $config);
    }

    /**
     * 获取商品分类信息
     */
    public function getInfo() {
        $this->limitRequestMethod('GET');

        $id = input('category_id/s', 0);
        if (empty($id)) {
            error(414, '请指定商品分类');
        }

        $where = [
            'user_id' => $this->userid,
            'id'      => $id,
        ];

        $config = [
            'fields' => 'id as category_id,sort,name',
        ];

        $res = CategoryService::find('goods_category', $where, $config);
        if ($res['status']) {
            //补充下单链接信息
            $link                      = LinkService::getCategoryLink($this->userid, $id);
            $res['data']['link']       = $link['data']['link'];
            $res['data']['short_link'] = $link['data']['short_link'];
            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 增加商品分类
     */
    public function add() {
        $this->limitRequestMethod('POST');
        $data = [
            'name'      => input('name/s', ''),
            'sort'      => input('sort/s', ''),
            'user_id'   => $this->userid,
            'status'    => 1,
            'create_at' => time(),
        ];

        $validate = Loader::validate('app\common\validate\Category');

        if (!$validate->check($data)) {
            error(414, $validate->getError());
        } else {
            $res = CategoryService::add('goods_category', $data);
            if ($res['status']) {
                MerchantLogService::write('添加商品分类成功', '添加商品分类成功，ID:' . $res['data'] . '，名称:' . $data['name']);
                success($res['data'], $res['msg']);
            } else {
                error(500, $res['msg']);
            }
        }
    }

    /**
     * 编辑商品分类
     */
    public function edit() {
        $this->limitRequestMethod('POST');

        $id = input('category_id/d', '');
        if (empty($id)) {
            error(414, '请指定商品分类');
        }

        $where = [
            'id'      => ['=', $id],
            'user_id' => ['=', $this->userid],
        ];

        $data = [
            'name' => input('name/s', ''),
            'sort' => input('sort/s', ''),
        ];

        $validate = Loader::validate('app\common\validate\Category');

        if (!$validate->check($data)) {
            error(414, $validate->getError());
        } else {
            $res = CategoryService::edit('goods_category', $where, $data);
            if ($res['status']) {
                MerchantLogService::write('编辑商品分类成功', '编辑商品分类成功，ID:' . $id);
                success($res['data'], $res['msg']);
            } else {
                error(500, $res['msg']);
            }
        }
    }

    /**
     * 删除分类
     */
    public function del() {
        $this->limitRequestMethod('POST');

        $id = input('category_id', '');
        if (empty($id)) {
            error(414, '请指定分类');
        }

        CategoryService::delete($this->userid, $id);
    }

    /**
     * 商品重置短链接
     */
    public function refreshLink() {
        $this->limitRequestMethod('POST');

        $id = input('category_id/d', '');
        if (empty($id)) {
            error(414, '请指定商品分类');
        }

        $where = [
            'id'      => ['=', $id],
            'user_id' => ['=', $this->userid],
        ];

        $res = CategoryService::find('goods_category', $where);
        if ($res['status']) {
            $res = LinkService::refresh($this->userid, 'goods_category', $id);
            if ($res['status']) {
                success($res['data'], '重置成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(500, $res['msg']);
        }
    }
}
