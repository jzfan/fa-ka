<?php

namespace app\common\pay;

use app\common\YcshPay;
use think\Request;

/**
 * 优畅上海微信 H5
 * Class YcshWxH5
 * @package app\common\pay
 */
class YcshWxH5 extends YcshPay
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
            'device_info' => 'WEB',
            'nonce_str' => $this->nonce_str,
            'body' => $subject,
            'out_trade_no' => $outTradeNo,
            'total_fee' => $totalAmount * 100,
            'spbill_create_ip' => $this->getAddress(),
            'notify_url' => Request::instance()->domain() . '/pay/notify/YcshWxH5',
            'trade_type' => 'MWEB',
            'scene_info' => json_encode(['h5_info' => [
                'type' => 'Wap',
                'wap_url' => Request::instance()->domain(),
                'wap_name' => '自动发卡'
            ]]),
            'payment_code' => 'WX_ONLINE_MWEB'
        ];

        $url = $url = $this->getGateway() . '/wechat/orders';
        $result = $this->curlPost($url, $paramter);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            if ($this->checkSign($result)) {
                $url = $this->getPayUrl($result);
                if ($url) {
                    $this->code = 0;
                    $obj = new \stdClass();
                    $obj->pay_url = $url;
                    $obj->content_type = 2;
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