<?php
/**
 * 支付宝 wap
 */
namespace app\common\pay;

use think\Request;

class QgjfAlipayWap extends QgjfBase
{

    public function __construct()
    {
        parent::__construct();
        $this->pay_type = '204';
        $this->request_url = 'https://pay.jinfupass.com/gateway/wapay';
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->total_amount = ($totalAmount * 100) . '';
        $this->out_trade_no = $outTradeNo;
        $this->device_info = date('YmdHis') . rand(1000, 9999);
        $this->notify_url = Request::instance()->domain() . '/pay/notify/QgjfAlipayWap';
        $this->body = $subject;

        return $this->request();
    }
}
