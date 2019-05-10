<?php
/**
 * 支付宝 wap
 */
namespace app\common\pay;

use app\common\TaomiBase;
use think\Request;

class TaomiAlipayWap extends TaomiBase
{

    public function __construct()
    {
        parent::__construct();
        $this->pay_type = '207';
        $this->request_url = 'http://gateway.taomipay.com/gateway/h5pay';
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->total_amount = ($totalAmount * 100) . '';
        $this->out_trade_no = $outTradeNo;
        $this->device_info = date('YmdHis') . rand(1000, 9999);
        $this->notify_url = Request::instance()->domain() . '/pay/notify/TaomiAlipayWap';
        $this->body = $subject;

        $form = $this->buildRequestForm();

        $this->code    = 0;
        $obj          = new \stdClass();
        $obj->pay_url = $form;
        $obj->content_type = 3;
        return $obj;
    }
}
