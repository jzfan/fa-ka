<?php

namespace app\api\service;

use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * Class OrderService
 * @package app\api\service
 */
class OrderService extends BaseService
{
    public static function getOrders($where, $config)
    {
        $res = self::lists('order', $where, $config);

        if ($res['status']) {

            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    switch ($item['status']) {
                        case '0':
                            $item['status_name'] = '未支付';
                            break;
                        case '1':
                            $item['status_name'] = '已支付';
                            break;
                        case '2':
                            $item['status_name'] = '已关闭';
                            break;
                        default:
                            $item['status_name'] = '';
                    }
                    return $item;
                });

                //转化数据
                $res['data'] = $res['data']->toArray();
            } else {
                foreach ($res['data']['data'] as &$item) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    switch ($item['status']) {
                        case '0':
                            $item['status_name'] = '未支付';
                            break;
                        case '1':
                            $item['status_name'] = '已支付';
                            break;
                        case '2':
                            $item['status_name'] = '已关闭';
                            break;
                        default:
                            $item['status_name'] = '';
                    }
                }
            }

            return $res;
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 获取订单列表
     * @param string|array $where
     * @param array $config
     */
    public static function getLists($where, $config)
    {
        $res = self::getOrders($where, $config);
        if ($res['status']) {
            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 获取订单统计信息
     * @param string|array $where
     * @param array $config
     */
    public static function getStatistic($where, $config)
    {
        $res = self::getOrders($where, $config);
        if ($res['status']) {
            //获取统计信息
            $statistic = [];
            try {
                $statistic = Db::name('order')->field([
                    'count(id) as total', 'sum(total_price) as amount', 'sum(quantity) as cards_num',
                    'sum(total_price-total_cost_price-sms_price-agent_fee-fee) as profits',
                ])->where($where)->find();
                foreach ($statistic as &$v) {
                    is_numeric($v) ? $v += 0 : $v = 0;
                }
            } catch (DbException $e) {
                error(500, '统计失败');
            }
            $res['data'] = array_merge($res['data'], $statistic);

            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 获取订单详情
     * @param $where
     * @param $config
     */
    public static function getOrderDetail($where, $config)
    {
        $res = self::find('order', $where, $config);
        if ($res['status']) {
            $res['data']['create_at'] = date('Y-m-d H:i:s', $res['data']['create_at']);
            $res['data']['channel_name'] = Db::name('channel')->where('id', '=', $res['data']['channel_id'])->value('title');

            switch ($res['data']['status']) {
                case '0':
                    $res['data']['status_name'] = '未支付';
                    break;
                case '1':
                    $res['data']['status_name'] = '已支付';
                    break;
                case '2':
                    $res['data']['status_name'] = '已关闭';
                    break;
                default:
                    $res['data']['status_name'] = '';
            }

            if ($res['data']['sendout']) {
                $res['data']['sendout_name'] = $res['data']['sendout'] >= $res['data']['quantity'] ? '已取' : '已取部分';
            } else {
                $res['data']['sendout_name'] = '未取';
            }

            //补充卡密信息
            $cards = Db::name('order_card')->field('number,secret')
                ->where('order_id', $res['data']['order_id'])->select();
            $res['data']['cards'] = $cards;

            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }
}
