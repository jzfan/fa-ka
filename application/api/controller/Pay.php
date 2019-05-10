<?php

namespace app\api\controller;

use app\api\service\PayService;

/**
 * Class Pay
 * @package app\api\controller
 */
class Pay extends AuthBase
{

    /**
     * 获取支付方式列表
     */
    public function lists()
    {
        $this->limitRequestMethod('GET');

        PayService::getLists($this->userid);
    }

    /**
     * 开关支付方式
     */
    public function toggleStatus()
    {
        $this->limitRequestMethod('POST');

        $id = input('channel_id', '');
        if (empty($id)) {
            error(414, '请指定支付方式');
        }

        $status = input('status/s', '');

        PayService::toggleStatus($this->userid, $id, $status);
    }

    /**
     * 获取支付渠道分析
     */
    public function getStatistic()
    {
        $this->limitRequestMethod('GET');

        $where = [
            'user_id' => ['=', $this->userid],
        ];
        //时间处理
        $create_at = input('create_at/s', '');
        if ($create_at) {
            $create_at = explode('-', $create_at);
            if (count($create_at) != 2) {
                error(414, '请传入正确的时间范围，且以 - 分割');
            }
            foreach ($create_at as &$time) {
                $time = strtotime($time);
            }
            $where['create_at'] = ['between', "{$create_at[0]}, {$create_at[1]}"];
        } else {
            //只获取今天的统计
            $today = strtotime(date('Y-m-d'));
            $where['create_at'] = ['>=', $today];
        }

        PayService::getStatistic($this->userid, $where);
    }
}
