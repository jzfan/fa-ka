<?php
namespace app\common\pay;
use think\Request;
use think\Log;
use app\common\Pay;

class PayapiAli extends Pay
{

    protected $gateway = 'https://pay.bbbapi.com';
    protected $code = '';
    protected $error = '';

    public function getCode()
    {
        return $this->code;
    }

    public function getError()
    {
        return $this->error;
    }

    public function order($outTradeNo,$subject,$totalAmount)
    {
        $params['uid'] =  $this->account->params->uid;
        $params['price'] = $totalAmount;
        $params['istype'] = '1';
        $params['notify_url'] = Request::instance()->domain().'/pay/notify/PayapiAli';
        $params['return_url'] = Request::instance()->domain().'/pay/page/PayapiAli';
        $params['orderid'] = $outTradeNo;
        $params['orderuid'] = input('userid/d');
        $params['goodsname'] = $outTradeNo;
        $params['key'] = md5($params['goodsname']. $params['istype'] . $params['notify_url'] . $params['orderid'] . $params['orderuid'] . $params['price'] . $params['return_url'] . $this->account->params->key . $params['uid']);
        Log::record("PayapiAli支付参数：" . json_encode($params) . " 网关：".$this->gateway, Log::INFO);
        $this->code    = 0;
        $obj           = new \stdClass();
        $obj->pay_url  = $this->createForm($this->gateway, $params);
        $obj->content_type = 3;
        return $obj;
    }

    /**
     * 页面回调
     */
    public function page_callback($params,$order)
    {
        header("Location:" . url('/orderquery',['orderid'=>$order->trade_no]));
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params,$order)
    {
        $paysapi_id = $params['paysapi_id'];
        $orderid = $params['orderid'];
        $price = $params['price'];
        $realprice = $params['realprice'];
        $orderuid = $params['orderuid'];
        $key = $params['key'];
        $token = $this->account->params->key;
        $temps = md5($orderid . $orderuid . $paysapi_id . $price . $realprice . $token);
        $diff    = $realprice * 100 - $price * 100;//实际付款金额不得相差5分钱

        if($diff > 5) {
            record_file_log('PayapiAli_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$realprice}");
            die('金额异常！');
        }
        if ($temps == $key){
            // 流水号
            $order->transaction_id = $paysapi_id;
            $this->completeOrder($order);
            exit('OK');
        } else {
            exit('验签失败');
        }
    }

    protected function createForm($url, $data)
    {
        $str = '<!doctype html>
            <html>
                <head>
                    <meta charset="utf8">
                    <title>正在跳转付款页</title>
                </head>
                <body onLoad="document.pay.submit()">
                <form method="post" action="' . $url . '" name="pay">';

        foreach ($data as $k => $vo) {
            $str .= '<input type="hidden" name="' . $k . '" value="' . $vo . '">';
        }

        $str .= '</form>
                <body>
            </html>';
        return $str;
    }
}
?>