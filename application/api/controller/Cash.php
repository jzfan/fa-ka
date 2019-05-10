<?php

namespace app\api\controller;

use app\api\service\CashService;

/**
 * Class Cash
 * @package app\api\controller
 */
class Cash extends AuthBase
{

    /**
     * 获取提现信息
     */
    public function getInfo()
    {
        $this->limitRequestMethod('GET');

        CashService::getInfo($this->userid);
    }

    /**
     * 申请提现
     */
    public function apply()
    {
        $this->limitRequestMethod('POST');

        $money = input('money/s',0);
        if($money <= 0){
            error(414, '请输入正确的提现金额');
        }

        CashService::apply($this->userid,$money);
    }

    /**
     * 获取提现申请列表
     */
    public function lists()
    {
        $this->limitRequestMethod('GET');

        $where = [
            'user_id' => ['=', $this->userid]
        ];

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'user_id, money, fee, actual_money, status, create_at, complete_at',
            'page' => input('page/d', 1),
            'limit' => input('limit/d', 0),
        ];

        CashService::getLists($where, $config);
    }
}