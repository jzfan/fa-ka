<?php
/**
 * 微信公众号支付
 */
namespace app\common\pay;

use app\common\TaomiBase;
use think\Request;

class TaomiWxGzh extends TaomiBase
{

    public function __construct()
    {
        parent::__construct();
        $this->pay_type = '303';
        $this->request_url = 'http://gateway.taomipay.com/gateway/jspay';
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        $openid = session('openid');
        if(empty($openid))
        {
            $this->code  = 500;
            $this->error = '请在微信中打开，并完成授权';
            return false;
        }
        $this->total_amount = ($totalAmount * 100) . '';
        $this->out_trade_no = $outTradeNo;
        $this->device_info = date('YmdHis') . rand(1000, 9999);
        $this->notify_url = Request::instance()->domain() . '/pay/notify/TaomiWxGzh';
        $this->body = $subject;

        return $this->request();
    }
}
