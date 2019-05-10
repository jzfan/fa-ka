<?php
/**
 * 支付宝扫码
 */
namespace app\common\pay;

use think\Request;

class QgjfAlipayScan extends QgjfBase
{

    public function __construct()
    {
        parent::__construct();
        $this->pay_type = '101';
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->total_amount = ($totalAmount * 100) . '';
        $this->out_trade_no = $outTradeNo;
        $this->device_info = date('YmdHis') . rand(1000, 9999);
        $this->notify_url = Request::instance()->domain() . '/pay/notify/QgjfAlipayScan';
        $this->body = $subject;

        $obj = $this->request();
        $obj->content_type = 4;
        return $obj;
    }
}
