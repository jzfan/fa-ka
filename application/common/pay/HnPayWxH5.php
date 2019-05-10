<?php

namespace app\common\pay;

use app\common\HnPay;
use think\Request;

/**
 * 微信H5
 * Class HnPayWxScan
 * @package app\common\pay
 */
class HnPayWxH5 extends HnPay
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
            'pay_method' => 'weixin_wap',
            'order_name' => $subject,
            'total_amount' => $totalAmount,
            'spbill_create_ip' => $_SERVER['SERVER_ADDR'],
            'notify_url' => Request::instance()->domain() . '/pay/notify/HnPayWxH5',
            'start_time' => date("Y-m-d H:i:s"),
            'goods_tag'=>'卡密',
            'sceneInfo'=>json_encode([
                'h5_info' => [
                    'type' => 'wap',
                    'wap_url' => Request::instance()->domain(),
                    'wap_name' => sysconf('site_name'),
                ]
            ]),
            'redirect_url' => Request::instance()->domain() . '/pay/page/HnPayWxH5',
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
            $obj->pay_url = $result['result']['pay_url'];
            $obj->content_type = 2;
            return $obj;
        }else{
            $this->code  = 500;
            $this->error = $result['errMsg'];
            return false;
        }
    }
    
}