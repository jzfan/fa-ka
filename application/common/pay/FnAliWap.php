<?php
/**
 * 蜂鸟支付宝WAP
 * @author mapeijian
 */
namespace app\common\pay;
use think\Request;
use app\common\Pay;
use Util\FnPay\ApiClient;
class FnAliWap extends Pay
{

    protected $gateway = '';
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
        $client = new ApiClient();
        $client->appId = $this->account->params->appid;
        $client->secret = $this->account->params->secret;
        $response = $client->call('alipay.qr_code_pay', [
            'merchant_no' => $this->account->params->merchant_no,
            'out_trade_no' => $outTradeNo,
            'order_name' => $subject,
            'total_amount' => $totalAmount,
            'notify_url' => Request::instance()->domain().'/pay/notify/FnAliWap',
            //'success_url' => Request::instance()->domain().'/orderquery/orderid/'.$outTradeNo
        ], $this->account->params->refer);
        if($response['error_code'] == 0) {
            $this->code    =0;
            $obj           =new \stdClass();
            $obj->pay_url  =$response['qr_code'];
            $obj->content_type = 2;
            return $obj;
        } else {
            $this->code    =201;
            $this->error = $response['error_msg'];
            return false;
        }
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
        $client = new ApiClient();
        $client->appId = $this->account->params->appid;
        $client->secret = $this->account->params->secret;
        if ($client->requestSignVerify($params)) {
            // 金额异常检测
            if($order->total_price!=$params['total_amount']){
                record_file_log('FnAliWap_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$params['total_amount']}");
                die('金额异常！');
            }
            if($params['trade_status'] == 'SUCCESS') {
                $order->transaction_id =$params['trade_no'];
                $this->completeOrder($order);
                record_file_log('FnAliWap_notify_success',$order->trade_no);
                echo 'SUCCESS';
                return true;
            } else {
                exit('FAIL');
            }
        } else {
            exit('FAIL');
        }
    }
}
?>