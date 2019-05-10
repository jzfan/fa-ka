<?php

namespace app\api\controller;

use app\api\service\CardService;
use app\api\service\GoodsService;
use app\api\service\LinkService;
use service\MerchantLogService;
use think\Db;
use think\Loader;

/**
 * Class Goods
 *
 * @package app\api\controller
 */
class Goods extends AuthBase {

    /***************************************************/
    //商品的操作
    /***************************************************/
    /**
     * 获取商品列表
     */
    public function lists() {
        $this->limitRequestMethod('GET');

        $where = [
            'user_id' => ['=', $this->userid],
        ];

        //是否查找回收站
        $trash = input('trash/d', 0);
        if ($trash) {
            $where['delete_at'] = ['>', 0];
        } else {
            $where['delete_at'] = null;
        }

        //名称查找
        $name = input('name/s', '');
        if ($name) {
            $where['name'] = ['like', "%$name%"];
        }

        //分类查找
        $category = input('category/d', '');
        if ($category) {
            $where['cate_id'] = ['=', $category];
        }

        $config = [
            'order'  => input('order/s', 'id desc'),
            'fields' => 'id as goods_id, name, price, status, create_at, user_id, cate_id',
            'page'   => input('page/d', 1),
            'limit'  => input('limit/d', 0),
        ];

        GoodsService::getLists($where, $config);
    }

    /**
     * 获取指定商品信息
     */
    public function getInfo() {
        $this->limitRequestMethod('GET');

        $id = input('goods_id/s', 0);
        if (empty($id)) {
            error(414, '请指定商品');
        }

        $where = [
            'user_id' => $this->userid,
            'id'      => $id,
        ];

        $config = [
            'fields' => 'id as goods_id,cate_id,theme,sort,name,price,cost_price,wholesale_discount,wholesale_discount_list,' .
                        'limit_quantity,inventory_notify,inventory_notify_type,coupon_type,sold_notify,take_card_type,visit_type,' .
                        'visit_password,content,remark,status,create_at,is_freeze,sms_payer,contact_limit ',
        ];

        $res = GoodsService::find('goods', $where, $config);
        if ($res['status']) {
            $res['data']['cate_name']               = Db::name('goods_category')->where(['id' => $res['data']['cate_id']])->value('name');
            $res['data']['wholesale_discount_list'] = json_decode($res['data']['wholesale_discount_list'], 1);
            switch ($res['data']['contact_limit']) {
                case 'default':
                    $res['data']['contact_limit_name'] = '默认';
                    break;
                case 'email':
                    $res['data']['contact_limit_name'] = '邮箱';
                    break;
                case 'mobile':
                    $res['data']['contact_limit_name'] = '手机';
                    break;
                case 'qq':
                    $res['data']['contact_limit_name'] = 'qq';
                    break;
                case 'any':
                    $res['data']['contact_limit_name'] = '任意字符';
                    break;
            }
            $link                      = LinkService::getGoodsLink($this->userid, $id)['data'];
            $res['data']['link']       = $link['link'];
            $res['data']['short_link'] = $link['short_link'];
            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 变更商品上下架状态
     */
    public function toggleStatus() {
        $this->limitRequestMethod('POST');

        $goodsId = input('goods_id/d', 0);
        if (empty($goodsId)) {
            error(414, '请指定商品');
        }

        $status    = input('status/s', '');
        $statusStr = $status == 1 ? '上架' : '下架';

        MerchantLogService::write('修改商品状态', '将ID为' . $goodsId . '的商品' . $statusStr);

        GoodsService::toggleStatus($this->userid, $goodsId, $status);
    }

    /**
     * 新增商品
     */
    public function add() {
        $this->limitRequestMethod('POST');

        $wholesaleDiscountList = htmlspecialchars_decode(input('wholesale_discount_list/s', ''));
//        if(!empty($wholesaleDiscountList)){
        //            $wholesaleDiscountList = json_encode($wholesaleDiscountList);
        //        }

        $data = [
            'user_id'                 => $this->userid,
            'cate_id'                 => input('cate_id/s', ''),
            'name'                    => input('name/s', ''),
            'sort'                    => input('sort/s', ''),
            'price'                   => input('price/s', ''),
            'cost_price'              => input('cost_price/s', ''),
            'wholesale_discount'      => input('wholesale_discount/s', ''),
            'sms_payer'               => input('sms_payer/s', ''),
            'limit_quantity'          => input('limit_quantity/s', ''),
            'inventory_notify'        => input('inventory_notify/s', ''),
            'coupon_type'             => input('coupon_type/s', ''),
            'sold_notify'             => input('sold_notify/s', ''),
            'take_card_type'          => input('take_card_type/s', ''),
            'visit_type'              => input('visit_type/s', ''),
            'content'                 => input('content/s', ''),
            'remark'                  => input('remark/s', ''),
            'theme'                   => input('theme/s', 'default'),
            'wholesale_discount_list' => $wholesaleDiscountList,
            'inventory_notify_type'   => input('inventory_notify/s', '1'),
            'visit_password'          => input('visit_password/s', ''),
            'status'                  => input('status/s', '1'),
            'create_at'               => time(),
            'contact_limit'           => input('contact_limit/s', 'default'),
        ];

        $validate = Loader::validate('app\common\validate\Goods');

        if (!$validate->check($data)) {
            error(414, $validate->getError());
        } else {

            if ($data['price'] < sysconf('goods_min_price') || $data['price'] > sysconf('goods_max_price')) {
                error(414, '商品价格必须在' . sysconf('goods_min_price') . '~' . sysconf('goods_max_price') . '内');
            }

            $res = GoodsService::add('goods', $data);
            if ($res['status']) {
                MerchantLogService::write('添加商品成功', '添加商品成功，商品ID:' . $res['data'] . ',名称:' . $data['name'] . ',价格:' . $data['price'] . ',成本价:' . $data['cost_price']);
                success($res['data'], $res['msg']);
            } else {
                error(500, $res['msg']);
            }
        }
    }

    /**
     * 商品信息编辑
     */
    public function edit() {
        $this->limitRequestMethod('POST');

        $id = input('goods_id/s', '');

        if (empty($id)) {
            error(414, '请指定商品');
        }

        $wholesaleDiscountList = htmlspecialchars_decode(input('wholesale_discount_list/s', ''));

        $data = [
            'cate_id'                 => input('cate_id/s', ''),
            'name'                    => input('name/s', ''),
            'sort'                    => input('sort/s', ''),
            'price'                   => input('price/s', ''),
            'cost_price'              => input('cost_price/s', ''),
            'wholesale_discount'      => input('wholesale_discount/s', ''),
            'sms_payer'               => input('sms_payer/s', ''),
            'limit_quantity'          => input('limit_quantity/s', ''),
            'inventory_notify'        => input('inventory_notify/s', ''),
            'coupon_type'             => input('coupon_type/s', ''),
            'sold_notify'             => input('sold_notify/s', ''),
            'take_card_type'          => input('take_card_type/s', ''),
            'visit_type'              => input('visit_type/s', ''),
            'content'                 => input('content/s', ''),
            'remark'                  => input('remark/s', ''),
            'theme'                   => input('theme/s', 'default'),
            'wholesale_discount_list' => $wholesaleDiscountList,
            'inventory_notify_type'   => input('inventory_notify/s', ''),
            'visit_password'          => input('visit_password/s', ''),
            'contact_limit'           => input('contact_limit/s', 'default'),
        ];

        $validate = Loader::validate('app\common\validate\Goods');

        if (!$validate->check($data)) {
            error(414, $validate->getError());
        } else {
            $res = GoodsService::edit('goods', ['id' => $id], $data);
            if ($res['status']) {
                MerchantLogService::write('编辑商品成功', '编辑商品成功，商品ID:' . $id);

                success($res['data'], $res['msg']);
            } else {
                error(500, $res['msg']);
            }
        }
    }

    /**
     * 把商品删除到回收站
     */
    public function del() {
        $this->limitRequestMethod('POST');

        $goodsId = input('goods_id', '');
        if (empty($goodsId)) {
            error(414, '请指定商品');
        }

        $where = [
            'id'        => $goodsId,
            'user_id'   => $this->userid,
            'delete_at' => null,
        ];

        $goods = GoodsService::find('goods', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = GoodsService::del('goods', $where, true);
            if ($res['status']) {
                MerchantLogService::write('删除商品', '删除ID为' . $goodsId . '的商品');
                success([], '删除成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '商品不存在');
        }
    }

    /**
     * 恢复商品
     */
    public function restore() {
        $this->limitRequestMethod('POST');

        $goodsId = input('goods_id', '');
        if (empty($goodsId)) {
            error(414, '请指定商品');
        }

        $where = [
            'id'        => ['=', $goodsId],
            'user_id'   => ['=', $this->userid],
            'delete_at' => ['>', 0],
        ];

        $goods = GoodsService::find('goods', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = GoodsService::restore('goods', $where);
            if ($res['status']) {
                MerchantLogService::write('恢复商品', '恢复ID为' . $goodsId . '的商品');
                success([], '恢复成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '商品不存在');
        }
    }

    /**
     * 从回收站删除商品
     */
    public function hardDel() {
        $this->limitRequestMethod('POST');

        $goodsId = input('goods_id', '');
        if (empty($goodsId)) {
            error(414, '请指定商品');
        }

        $where = [
            'id'        => ['=', $goodsId],
            'user_id'   => ['=', $this->userid],
            'delete_at' => ['>', 0],
        ];

        $goods = GoodsService::find('goods', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = GoodsService::del('goods', $where);
            if ($res['status']) {
                MerchantLogService::write('删除商品', '删除ID为' . $goodsId . '的商品');
                success([], '删除成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '商品不存在');
        }
    }

    /**
     * 清空卡密
     */
    public function emptyCards() {
        $this->limitRequestMethod('POST');

        $goodsId = input('goods_id', '');
        if (empty($goodsId)) {
            error(414, '请指定商品');
        }

        $where = [
            'id'      => ['=', $goodsId],
            'user_id' => ['=', $this->userid],
        ];

        $goods = GoodsService::find('goods', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = CardService::del('goods_card', [
                'goods_id' => $goodsId,
                'user_id'  => $this->userid,
                'status'   => 1, // 只清空未售出的库存卡密
            ], true);
            if ($res['status']) {
                MerchantLogService::write('成功清空商品卡密库存', '成功清空商品卡密库存，商品ID:' . $goodsId);
                success([], '清空成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '商品不存在');
        }
    }

    /**
     * 商品分类重置短链接
     */
    public function refreshLink() {
        $this->limitRequestMethod('POST');

        $goodsId = input('goods_id', '');
        if (empty($goodsId)) {
            error(414, '请指定商品');
        }

        $where = [
            'id'      => ['=', $goodsId],
            'user_id' => ['=', $this->userid],
        ];

        $res = GoodsService::find('goods', $where, ['fields' => 'id']);
        if ($res['status']) {
            $res = LinkService::refresh($this->userid, 'goods', $goodsId);
            if ($res['status']) {
                success($res['data'], '重置成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, $res['msg']);
        }
    }
}
