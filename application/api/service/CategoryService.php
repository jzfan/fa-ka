<?php

namespace app\api\service;

use service\MerchantLogService;

/**
 * Class CategoryService
 * @package app\api\service
 */
class CategoryService extends BaseService
{
    /**
     * 获取商品分类列表
     * @param $where
     * @param $config
     */
    public static function getLists($where, $config)
    {
        $res = self::lists('goods_category', $where, $config);
        if ($res['status']) {
            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    $item['link'] = LinkService::getCategoryLink($item['user_id'], $item['category_id'])['data']['link'];
                    $item['short_link'] = LinkService::getCategoryLink($item['user_id'], $item['category_id'])['data']['short_link'];
                    return $item;
                });
            } else {
                foreach ($res['data']['data'] as &$item) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    $item['link'] = LinkService::getCategoryLink($item['user_id'], $item['category_id'])['data']['link'];
                    $item['short_link'] = LinkService::getCategoryLink($item['user_id'], $item['category_id'])['data']['short_link'];
                }
            }

            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 删除优惠券
     * @param $userId
     * @param $id
     */
    public static function delete($userId, $id)
    {
        $where = [
            'id' => $id,
            'user_id' => $userId,
        ];

        //检查是否有商品
        $goods = BaseService::find('goods', ['user_id' => $userId, 'cate_id' => $id, 'delete_at' => null], ['fields' => 'id']);
        if ($goods['status']) {
            error(414, '该分类下有商品，不允许删除！');
        }

        //检查是否有优惠券
        $goodsCoupon = BaseService::find('goods_coupon', ['user_id' => $userId, 'cate_id' => $id, 'delete_at' => null], ['fields' => 'id']);
        if ($goodsCoupon['status']) {
            error(414, '该分类下有商品优惠券，不允许删除！');
        }

        $goodsCategory = CategoryService::find('goods_category', $where, ['fields' => 'id']);
        if ($goodsCategory['status']) {
            $res = CategoryService::del('goods_category', $where);
            if ($res['status']) {
                //删除分类下所有商品和优惠券
                BaseService::del('goods', ['cate_id' => $id, 'user_id' => $userId]);
                BaseService::del('goods_coupon', ['cate_id' => $id, 'user_id' => $userId]);
                MerchantLogService::write('删除商品分类成功', '删除商品分类成功，ID:' . $id);

                success([], '删除成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '分类不存在');
        }
    }
}
