<?php

namespace app\common\pay;

use app\common\Swift;
use think\Request;

/**
 * Class SwiftAliScan
 * @package app\common\pay
 */
class SwiftAliScan extends Swift
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
            'total_fee' => $totalAmount * 100,
            'out_trade_no' => $outTradeNo,
            'body' => $subject,
            'mch_create_ip' => $this->getAddress(),
            'attach' => '',
        ];

        $notify = Request::instance()->domain() . '/pay/notify/SwiftAliScan';
        $result = parent::request('pay.alipay.native', $paramter, $notify);

        if ($result['status'] == 0 && $result['result_code'] == 0) {
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = $result['code_url'];
            $obj->content_type = 1;
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
