<?php

namespace app\common\pay;

use app\common\Tpay;
use think\Request;

/**
 * 微信支付
 * Class TpayAlipay
 * @package app\common\pay
 */
class TpayWxpay extends Tpay
{

    /**
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $params = [
            'uid' => $this->account->params->uid,
            'qr_amount' => $totalAmount,
            'notify_url' => Request::instance()->domain() . '/pay/notify/TpayWxpay',
            'return_url' => Request::instance()->domain() . '/pay/page/TpayWxpay',
            'order_number' => $outTradeNo,
            'order_uid' => $outTradeNo,
            'type' => 2,
        ];

        ksort($params);

        $params['key'] = $this->sign($params);

        return $this->request($params,$this->account->params->refer);
    }
}
