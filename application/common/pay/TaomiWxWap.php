<?php
/**
 * 微信 wap 支付
 */
namespace app\common\pay;

use app\common\TaomiBase;
use think\Request;

class TaomiWxWap extends TaomiBase
{

    public function __construct()
    {
        parent::__construct();
        $this->pay_type = '206';
        $this->request_url = 'http://gateway.taomipay.com/gateway/wapay';
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->total_amount = ($totalAmount * 100) . '';
        $this->out_trade_no = $outTradeNo;
        $this->device_info = date('YmdHis') . rand(1000, 9999);
        $this->notify_url = Request::instance()->domain() . '/pay/notify/TaomiWxWap';
        $this->body = $subject;

        return $this->request();
    }
}
