<?php

namespace app\common\pay;

use app\common\YcshPay;
use think\Request;

/**
 * 优畅上海微信公众号
 * Class YcshWxGzh
 * @package app\common\pay
 */
class YcshWxGzh extends YcshPay
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
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {

            $paramter = [
                'mch_id' => $this->account->params->mch_id,
                'sub_appid' => $this->account->params->appid,
                'device_info' => 'WEB',
                'nonce_str' => $this->nonce_str,
                'body' => $subject,
                'out_trade_no' => $outTradeNo,
                'total_fee' => $totalAmount * 100,
                'spbill_create_ip' => $this->getAddress(),
                'notify_url' => Request::instance()->domain() . '/pay/notify/YcshWxGzh',
                'trade_type' => 'JSAPI',
                'sub_openid' => session('openid'),
                'payment_code' => 'WX_OFFLINE_JSAPI'
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
                        $obj->content_type = 5;
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
        } else {
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = url('index/pay/wx_jspay_page') . '?trade_no=' . $outTradeNo . '&url=' . base64_encode(Request::instance()->domain() . '/index/pay/payment?trade_no=' . $outTradeNo);
            $obj->content_type = 2;
            return $obj;
        }
    }
}