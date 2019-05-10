<?php

namespace app\api\service;

use think\Db;

/**
 * Class GoodsService
 * @package app\api\service
 */
class GoodsService extends BaseService
{
    /**
     * 获取商品列表
     * @param array $where
     * @param array $config
     */
    static function getLists($where, $config)
    {
        $res = self::lists('goods', $where, $config);
        if ($res['status']) {

            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);

                    //分类名称
                    $item['cate_name'] = Db::name('goods_category')->where(['id' => $item['cate_id']])->value('name');

                    $item['stock'] = Db::name('goods_card')->where(['goods_id' => $item['goods_id'], 'delete_at' => null, 'status' => 1])->count();
                    $item['sold'] = Db::name('goods_card')->where(['goods_id' => $item['goods_id'], 'status' => 2])->count();
                    
                    //补充商品链接
                    $link = LinkService::getGoodsLink($item['user_id'], $item['goods_id'])['data'];
                    $item['link'] = $link['link'];
                    $item['short_link'] = $link['short_link'];
                    return $item;
                });
            } else {
                foreach ($res['data']['data'] as &$item) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);

                    //分类名称
                    $item['cate_name'] = Db::name('goods_category')->where(['id' => $item['cate_id']])->value('name');

                    $item['stock'] = Db::name('goods_card')->where(['goods_id' => $item['goods_id'], 'delete_at' => null, 'status' => 1])->count();
                    $item['sold'] = Db::name('goods_card')->where(['goods_id' => $item['goods_id'], 'status' => 2])->count();

                    //补充商品链接
                    $link = LinkService::getGoodsLink($item['user_id'], $item['goods_id'])['data'];
                    $item['link'] = $link['link'];
                    $item['short_link'] = $link['short_link'];
                }
            }

            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 变更商品上下架状态
     * @param $userId
     * @param $goodsId
     * @param string $status
     */
    static function toggleStatus($userId, $goodsId, $status = '')
    {
        if (empty($goodsId)) {
            error(414, '请指定商品');
        }

        $where = [
            'user_id' => $userId,
            'id' => $goodsId,
        ];

        $res = self::toggleField('goods', $where, $status);
        if ($res['status']) {
            success($res['data'], $res['msg']);
        } else {
            error($res['code'], $res['msg']);
        }
    }
}
