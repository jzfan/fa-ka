<?php
/**
 * 微信公众号支付
 */

namespace app\common\pay;

use think\Request;

class QgjfWxGzh extends QgjfBase
{

    public function __construct()
    {
        parent::__construct();
        $this->pay_type = '303';
        $this->request_url = 'https://pay.jinfupass.com/gateway/jspay';
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            //微信中打开
            $openid = session('openid');
            if (empty($openid)) {
                $this->code = 500;
                $this->error = '请在微信中打开，并完成授权';
                return false;
            }
            $this->total_amount = ($totalAmount * 100) . '';
            $this->out_trade_no = $outTradeNo;
            $this->device_info = date('YmdHis') . rand(1000, 9999);
            $this->notify_url = Request::instance()->domain() . '/pay/notify/QgjfWxGzh';
            $this->body = $subject;

            $result = $this->post($this->request_url,$this->getPostData());

            if ($result['result_code'] == 1) {
                $this->code    = 0;
                $obj          = new \stdClass();
                $obj->pay_url = json_encode($result['pay_info']);
                $obj->content_type = 5;
                return $obj;
            }else{
                $this->code  = 500;
                $this->error = $result['return_msg'];
                return false;
            }
        } else {

            //不是微信里面打开
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = url('index/pay/wx_jspay_page') . '?trade_no=' . $outTradeNo . '&url=' . base64_encode(Request::instance()->domain() . '/index/pay/payment?trade_no=' . $outTradeNo);
            $obj->content_type = 2;
            return $obj;
        }

    }
}
