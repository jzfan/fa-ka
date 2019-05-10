<?php
namespace app\common\pay;
use think\Request;
use app\common\Pay;
use Util\Qpay\QpayMchUtil;

class QqNative extends Pay
{

    protected $gateway = 'https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi';
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
        $params = array();
        $params["mch_id"] = $this->account->params->mch_id;
        $params["nonce_str"] = QpayMchUtil::createNoncestr();
        $params["body"] = $subject;
        $params["out_trade_no"] = $outTradeNo;
        $params["fee_type"] = "CNY";
        $params["total_fee"] = $totalAmount*100;
        $params["spbill_create_ip"] = Request::instance()->ip();
        $params["trade_type"] = "NATIVE";
        $params["notify_url"] = Request::instance()->domain().'/pay/notify/QqNative';
        $params["sign"] = QpayMchUtil::getSign($params,$this->account->params->key);
        $xml = QpayMchUtil::arrayToXml($params);
        $ret =  QpayMchUtil::reqByCurlSSLPost($xml, $this->gateway, 10, $this->account->params->refer);
        $result = QpayMchUtil::xmlToArray($ret);
        if($result['return_code'] == 'SUCCESS') {
            if($result['result_code'] == 'SUCCESS') {
                $this->code    =0;
                $obj           =new \stdClass();
                $obj->pay_url  =$result['code_url'];
                $obj->content_type = 1;
                return $obj;
            } else {
                $this->code=202;
                $this->error = $result['err_code_des'];
                return false;
            }
        } else {
            $this->code=201;
            $this->error = '通信失败';
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
        $sign1 = $params['sign'];
        unset($params['sign']);
        $sign2 = QpayMchUtil::getSign($params,$this->account->params->key);
        if($sign1 == $sign2) {
            // 金额异常检测
            if($order->total_price>($params['total_fee']/100)){
                record_file_log('QqNative_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$params['realprice']}");
                die('金额异常！');
            }
            // 流水号
            $order->transaction_id =$params['transaction_id'];
            $this->completeOrder($order);
            record_file_log('QqNative_notify_success',$order->trade_no);
            echo '<xml><return_code>SUCCESS</return_code></xml>';
            return true;
        } else {
            exit('验签失败');
        }
    }

}
?>