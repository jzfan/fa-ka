<?php

namespace app\common\pay;

use app\common\Wsyh;

class WsyhAliscan extends Wsyh
{
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $ua = $_SERVER['HTTP_USER_AGENT'];

        $jsonArray = $this->realOrder('ALI', $outTradeNo, $subject, $totalAmount);

        if ($jsonArray['response']['body']['RespInfo']['ResultStatus'] == 'S') {
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
