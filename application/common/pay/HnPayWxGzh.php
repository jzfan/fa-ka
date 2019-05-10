<?php

namespace app\common\pay;

use app\common\HnPay;
use think\Request;

/**
 * 微信公众号
 * Class HnPayWxScan
 * @package app\common\pay
 */
class HnPayWxGzh extends HnPay
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
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $requestUrl = $this->gateway . '/gateway/create_order';

            $data = [
                'out_trade_no' => $outTradeNo,
                'order_name' => $subject,
                'total_amount' => $totalAmount,
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
                'notify_url' => Request::instance()->domain() . '/pay/notify/HnPayWxGzh',
                'start_time' => date("Y-m-d H:i:s"),
                //demo演示为线下微信公众号支付 如果线上公众号支付 pay_method为"weixin_mp_online"
                'pay_method' => 'weixin_mp_offline',
            ];

            $data['rurl'] = Request::instance()->domain() . '/orderquery?orderid=' . $outTradeNo;

            if(isset($this->account->params->publicappid) && !empty($this->account->params->publicappid)) {
                $data['sub_openid'] = session('openid');
            }

            $content = ['data' => json_encode($data)];

            $sysParams["app_id"] = $this->account->params->appid;
            $sysParams["merchant_no"] = $this->account->params->merchant;
            $sysParams["version"] = "1.0";
            $sysParams = array_merge($content, $sysParams);
            $sign = self::sign($sysParams, $this->account->params->key);
            $sysParams["sign"] = $sign;
            $sysParams["sign_type"] = "MD5";

            $result = $this->request($requestUrl, $sysParams, $this->account->params->refer);

            if (isset($result['errNo']) && $result['errNo'] == 0) {
                $this->code = 0;
                $obj = new \stdClass();
                if(isset($this->account->params->publicappid) && !empty($this->account->params->publicappid)) {
                    $obj->pay_url = $result['result']['pay_info'];
                    $obj->content_type = 5;
                } else {
                    $obj->pay_url = json_decode($result['result']['pay_info'], 1)['pay_url'];
                    $obj->content_type = 2;
                }
                return $obj;
            } else {
                $this->code = 500;
                $this->error = $result['errMsg'];
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