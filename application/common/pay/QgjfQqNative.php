<?php
/**
 * qq 钱包
 */

namespace app\common\pay;

use think\Request;

class QgjfQqNative extends QgjfBase
{

    public function __construct()
    {
        parent::__construct();
        $this->pay_type = '103';
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->total_amount = ($totalAmount * 100) . '';
        $this->out_trade_no = $outTradeNo;
        $this->device_info = date('YmdHis') . rand(1000, 9999);
        $this->notify_url = Request::instance()->domain() . '/pay/notify/QgjfQqNative';
        $this->body = $subject;

        return $this->request();
    }
}
