<?php

namespace app\api\service;

use service\MerchantLogService;
use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * Class CardService
 * @package app\api\service
 */
class CardService extends BaseService
{
    /**
     * 获取商品分类列表
     * @param $where
     * @param $config
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLists($where, $config)
    {
        $res = self::lists('goods_card', $where, $config);
        if ($res['status']) {
            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    $goods = Db::name('goods')->field('name,cate_id,price')->where('id', '=', $item['goods_id'])->find();
                    $item['goods_name'] = $goods['name'];
                    $item['price'] = $goods['price'];
                    $item['category_name'] = Db::name('goods_category')->where('id', '=', $goods['cate_id'])->value('name');
                    return $item;
                });
            } else {
                foreach ($res['data']['data'] as &$item) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    $goods = Db::name('goods')->field('name,cate_id,price')->where('id', '=', $item['goods_id'])->find();
                    $item['goods_name'] = $goods['name'];
                    $item['price'] = $goods['price'];
                    $item['category_name'] = Db::name('goods_category')->where('id', '=', $goods['cate_id'])->value('name');
                }
            }

            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 新增卡密
     * @param array $cards
     * @return array|void
     */
    public static function addCards($cards)
    {
        if (empty($cards)) {
            error(414, '虚拟卡内容格式不正确');
        }

        try {
            $count = count($cards);
            $success = 0;

            foreach ($cards as $card) {
                $res = Db::name('goods_card')->insert($card);
                if ($res) {
                    $success++;
                }
            }

            MerchantLogService::write('成功添加卡密', '成功添加 ' . $success . ' 张卡密');
            success([], "共 {$count} 张卡密，成功添加 {$success} 张卡密！");
        } catch (DbException $e) {
            error(500, '添加失败，原因：' . $e->getMessage());
        }
    }
}
