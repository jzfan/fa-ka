<?php
/**
 * 支付宝扫码
 */
namespace app\common\pay;

use app\common\TaomiBase;
use think\Request;

/**
 * 淘米支付宝扫码
 * Class TaomiAlipayScan
 * @package app\common\pay
 */
class TaomiAlipayScan extends TaomiBase
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
        $this->notify_url = Request::instance()->domain() . '/pay/notify/TaomiAlipayScan';
        $this->body = $subject;

        $obj = $this->request();
        if ($obj) {
            $obj->content_type = 4;
            return $obj;
        } else {
            return $obj;
        }
    }
}
