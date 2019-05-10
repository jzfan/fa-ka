<?php

namespace app\common\pay;

use app\common\Wsyh;

class WsyhWxScan extends Wsyh
{
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];

        $jsonArray = $this->realOrder('WX', $outTradeNo, $subject, $totalAmount);

        if ($jsonArray['response']['body']['RespInfo']['ResultStatus'] == 'S') {
            if ((strpos($ua, 'MicroMessenger') == false)) {
                //不是微信扫码，让用户微信打开连接
                $this->code = 0;
                $obj = new \stdClass();
                $obj->pay_url = url('index/pay/wx_jspay_page') . '?trade_no=' . $outTradeNo . '&url=' . base64_encode($jsonArray['response']['body']['PayInfo']);
                $obj->content_type = 2;
                return $obj;
            }

            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = $jsonArray['response']['body']['PayInfo'];
            $obj->content_type = 1;
            return $obj;
        } else {
            $msg = isset($jsonArray['response']['body']['RespInfo']['ResultMsg']) ? $jsonArray['response']['body']['RespInfo']['ResultMsg'] : '下单失败，请检查支付配置';
            $this->code = 500;
            $this->error = $msg;
            return false;

            exit;
        }
    }
}
