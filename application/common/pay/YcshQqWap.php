<?php

namespace app\common\pay;

use app\common\YcshPay;
use think\Request;

/**
 * 优畅上海QQweb
 * Class YcshQqWap
 * @package app\common\pay
 */
class YcshQqWap extends YcshPay
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
            'mch_id' => $this->account->params->mch_id,
            'nonce_str' => $this->nonce_str,
            'total_fee' => $totalAmount * 100,
            'out_trade_no' => $outTradeNo,
            'body' => $subject,
            'spbill_create_ip' => $this->getAddress(),
            'trade_type' => 'NATIVE',
            'notify_url' => Request::instance()->domain() . '/pay/notify/YcshQqWap',
            'payment_code' => 'QQ_OFFLINE_NATIVE',
        ];

        $url = $url = $this->getGateway() . '/qqpay/orders';
        $result = $this->curlPost($url, $paramter);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            if ($this->checkSign($result)) {
                if ($result['code_url']) {
                    $this->code = 0;
                    $obj = new \stdClass();
                    $obj->pay_url = $result['code_url'];
                    $obj->content_type = 6;
                    return $obj;
                } else {
                    $this->code = 500;
                    $this->error = '获取支付信息失败';
                    return $url;
                }
            } else {
                $this->code = 500;
                $this->error = '签名验证失败';
                return false;
            }
        } else {
            $this->code = 500;
            $this->error = $result['return_msg'];
            return false;
        }
    }
}
