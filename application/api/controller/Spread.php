<?php

namespace app\api\controller;

use app\api\service\SpreadService;

/**
 * 推广
 * Class Spread
 * @package app\api\controller
 */
class Spread extends AuthBase
{

    /**
     * 推广列表
     */
    public function lists()
    {
        $this->limitRequestMethod('GET');

        $where = [
            'parent_id' => $this->userid
        ];

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'username, id as user_id, create_at',
            'page' => input('page/d', 1),
            'limit' => input('limit/d', 0),
        ];

        SpreadService::getLists($where, $config);
    }

    /**
     * 返佣列表
     */
    public function rebate()
    {
        $this->limitRequestMethod('GET');
        $where = [
            'user_id' => $this->userid,
            'business_type' => 'sub_sold_rebate',
        ];

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'user_id, money, create_at',
            'page' => input('page/d', 1),
            'limit' => input('limit/d', 0),
        ];
        SpreadService::getRebateLists($where, $config);
    }
}