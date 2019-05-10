<?php

namespace app\api\controller;

use app\api\service\GoodsService;
use app\api\service\OrderService;

/**
 * Class Order
 * @package app\api\controller
 */
class Order extends AuthBase
{
    /**
     * 生成搜索条件
     * @return array
     */
    protected function generateWhere()
    {
        //组装搜索条件
        $where = [
            'user_id' => ['=', $this->userid],
        ];

        //时间处理
        $create_at = input('create_at/s', '');
        if ($create_at) {
            $create_at = explode('-', $create_at);
            if (count($create_at) == 2) {
                foreach ($create_at as &$time) {
                    $time = strtotime($time);
                }
                $where['create_at'] = ['between', "{$create_at[0]}, {$create_at[1]}"];
            }
        }

        //状态处理
        $status = input('status/s', '');
        if ($status != '') {
            $where['status'] = ['=', $status];
        }

        //分类处理
        $category = input('category_id/s', '');
        if ($category) {
            $goods = GoodsService::getLists([
                'user_id' => $this->userid,
                'cate_id' => $category,
            ], [
                'limit' => 0,
                'fields' => 'id',
            ]);

            if ($goods['status']) {
                $ids = array_column($goods['data']['data'], 'id');

                if (!empty($ids)) {
                    $where['goods_id'] = ['in', $ids];
                }
            }
            $where['goods_id'] = ['=', 0];
        }

        //指定类型的搜索
        $type = input('type/s', '');
        $key = input('key/s', '');
        if ($key && $type) {
            $where[$type] = ['like', "%$key%"];
        }

        //支付方式
        $payType = input('pay_type/s', '');
        if ($payType) {
            $where['paytype'] = ['=', $payType];
        }

        return $where;
    }

    /**
     * 获取订单列表
     */
    public function lists()
    {
        $this->limitRequestMethod('GET');

        $where = $this->generateWhere();

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'trade_no, status, create_at, total_price',
            'page' => input('page/d', 1),
            'limit' => input('limit/d', 0),
        ];

        OrderService::getLists($where, $config);
    }

    /**
     * 获取订单统计信息
     */
    public function getStatistic()
    {
        $this->limitRequestMethod('GET');

        $where = $this->generateWhere();

        //统计信息只拿已支付的订单
        $where['status'] = ['=', 1];

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'trade_no, status, create_at, total_price',
            'page' => input('page/d', 1),
            'limit' => input('limit/d', 0),
        ];

        OrderService::getStatistic($where, $config);
    }

    /**
     * 获取订单详情
     */
    public function getInfo()
    {
        $this->limitRequestMethod('GET');

        $tradeNo = input('trade_no/s', '');
        if (empty($tradeNo)) {
            error(414, '请提供订单号');
        }

        $where = [
            'user_id' => $this->userid,
            'trade_no' => $tradeNo,
        ];

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'id as order_id, quantity, sendout, contact, trade_no, goods_name, status, create_at, (goods_price * quantity) as total_goods_price, total_price, channel_id',
        ];

        OrderService::getOrderDetail($where, $config);
    }
}
