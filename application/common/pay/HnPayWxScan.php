<?php

namespace app\common\pay;

use app\common\HnPay;
use think\Request;

/**
 * 微信扫码
 * Class HnPayWxScan
 * @package app\common\pay
 */
class HnPayWxScan extends HnPay
{

    /**
     * 下单
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return bool|\stdClass
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $requestUrl = $this->gateway .'/gateway/create_order';
        $content = ['data' => json_encode([
            'out_trade_no' => $outTradeNo,
            'order_name' => $subject,
            'total_amount' => $totalAmount,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url' => Request::instance()->domain() . '/pay/notify/HnPayWxScan',
            'start_time' => date("Y-m-d H:i:s"),
            'pay_method' => 'weixin_scan_offline',
        ])];
        $sysParams["app_id"] = $this->account->params->appid;
        $sysParams["merchant_no"] = $this->account->params->merchant;
        $sysParams["version"] = "1.0";
        $sysParams = array_merge($content, $sysParams);
        $sign = self::sign($sysParams, $this->account->params->key);
        $sysParams["sign"] = $sign;
        $sysParams["sign_type"] = "MD5";

        $result = $this->request($requestUrl,$sysParams, $this->account->params->refer);

        if (isset($result['errNo']) && $result['errNo'] == 0) {
            $this->code    = 0;
            $obj          = new \stdClass();
            $obj->pay_url = $result['result']['qr_code'];
            $obj->content_type = 1;
            return $obj;
        }else{
            $this->code  = 500;
            $this->error = $result['errMsg'];
            return false;
        }
    }

}