<?php
/**
 * 易云微信WAP
 * @author mapeijian
 */
namespace app\common\pay;
use think\Request;
use app\common\Pay;
class YiyunWxWap extends Pay
{

    protected $gateway = 'http://api.47idc.cn/api/submit';
    protected $code='';
    protected $error='';

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
        $params['version'] = '1.0';
        $params['customerid'] = $this->account->params->userid;
        $params['sdorderno'] = $outTradeNo;
        $params['total_fee'] = number_format($totalAmount,2,'.','');
        $params['paytype'] = 'wxwap';
        $params['notifyurl'] = Request::instance()->domain().'/pay/notify/YiyunWxWap';
        $params['returnurl'] = Request::instance()->domain().'/pay/page/YiyunWxWap';
        $params['remark'] = $subject;
        $params['sign'] = md5('version='.$params['version'].'&customerid='.$params['customerid'].'&total_fee='.$params['total_fee'].'&sdorderno='.$params['sdorderno'].'&notifyurl='.$params['notifyurl'].'&returnurl='.$params['returnurl'].'&'.$this->account->params->userkey);
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
        $status = isset($params['status']) ? $params['status'] : '';
        $customerid = isset($params['customerid']) ? $params['customerid'] : '';
        $sdorderno = isset($params['sdorderno']) ? $params['sdorderno'] : '';
        $total_fee = isset($params['total_fee']) ? $params['total_fee'] : '';
        $paytype = isset($params['paytype']) ? $params['paytype'] : '';
        $sdpayno = isset($params['sdpayno']) ? $params['sdpayno'] : '';
        $sign = isset($params['sign']) ? $params['sign'] : '';
        $userkey = $this->account->params->userkey;
        $mysign = md5('customerid='.$customerid.'&status='.$status.'&sdpayno='.$sdpayno.'&sdorderno='.$sdorderno.'&total_fee='.$total_fee.'&paytype='.$paytype.'&'.$userkey);
        if($sign == $mysign) {
            if($status == '1') {
                $order->transaction_id = $sdpayno;
                $this->completeOrder($order);
                record_file_log('YiyunWxWap_notify_success',$order->trade_no);
                echo 'success';
            } else {
                echo 'fail';
            }
        } else {
            echo 'signerr';
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