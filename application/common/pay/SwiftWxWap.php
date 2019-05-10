<?php

namespace app\common\pay;

use app\common\Swift;
use think\Request;

/**
 * Class SwiftWxWap
 * @package app\common\pay
 */
class SwiftWxWap extends Swift
{

    /**
     * 下单
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return bool|\stdClass|string
     * @throws \Exception
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $paramter = [
            'out_trade_no' => $outTradeNo,
            'body' => $subject,
            'total_fee' => $totalAmount * 100,
            'mch_create_ip' => $this->getAddress(),
            'device_info' => 'AND_WAP',
            'mch_app_name' => '发卡系统',
            'mch_app_id' => Request::instance()->domain(),
        ];

        $notify = Request::instance()->domain() . '/pay/notify/SwiftWxWap';
        $result = parent::request('pay.weixin.wappay', $paramter, $notify);

        if ($result['status'] == 0 && $result['result_code'] == 0) {
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = $result['pay_info'];
            $obj->content_type = 2;
            return $obj;
        } else {
            $this->code = 500;
            if ($result['status'] != 0) {
                $error = isset($result['message']) ? $result['message'] : '';
            } else {
                $error = isset($result['err_msg']) ? $result['err_msg'] : '';
            }
            $this->error = '获取支付信息失败!' . $error;
            return false;
        }

    }
}
