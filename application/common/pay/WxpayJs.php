<?php
/**
 * 微信公众号支付
 * @author mapeijian
 */
namespace app\common\pay;

use think\Db;
use think\Request;
use app\common\Pay;

class WxpayJs extends Pay{
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

    /**
     * 支付
     * @param string $outTradeNo 外部单号
     * @param string $subject 标题
     * @param float $totalAmount 支付金额
     */
    public function order($outTradeNo,$subject,$totalAmount) {

        $params = [
            'body' => $subject,
            'out_trade_no' => $outTradeNo,
            'total_fee' => $totalAmount*100,
        ];

        $result = \wxpay\JsapiPay::getPayUrl($params);
        halt($result);

        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$result;
        $obj->content_type = 2;
        return $obj;
    }

    /**
     * 支付同步通知处理
     */
    public function page_callback($params,$order) {
        header("Location:" . url('/orderquery',['orderid'=>$order->trade_no]));
    }

    /**
     * 支付异步通知处理
     */
    public function notify_callback($params,$order) {
        if($params['out_trade_no']) {
            $buff = "";
            foreach ($params as $k => $v) {
                if ($k != "sign" && $v != "" && !is_array($v)) {
                    $buff .= $k . "=" . $v . "&";
                }
            }
            $buff = trim($buff, "&");
            $string = $buff . "&key=" . $this->account->params->signkey;
            $string = md5($string);
            $sign = strtoupper($string);
            if ($sign != $params["sign"]) {
                record_file_log('wxpay_notify_error','验签错误！'."\r\n".$order->trade_no);
                die('验签错误！');
            }
            // 金额异常检测
            $money=$params['total_fee']/100;
            if($order->total_price>$money){
                record_file_log('alipay_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$money}");
                die('金额异常！');
            }
            // TODO 这里去完成你的订单状态修改操作
            // 流水号
            $order->transaction_id =$params['transaction_id'];
            $this->completeOrder($order);
            record_file_log('wxpay_notify_success',$order->trade_no);
            echo 'success';
            return true;
        }
    }

    /**
     * 支付宝当面付异步回调数据验签
     * @param  array $params                待验证数据
     * @param  string $alipay_public_key    支付宝应用公钥
     * @param  string $sign_type            秘钥类型
     * @return boolean                      验签状态
     */
    private function verify_sign($params,$alipay_public_key,$sign_type='RSA2')
    {
        $ori_sign=$params['sign'];
        unset($params['sign']);
        unset($params['sign_type']);
        ksort($params);
        $data='';
        foreach($params as $k => $v){
            $data.=$k.'='.$v.'&';
        }
        $data=substr($data,0,-1);
    	$public_content="-----BEGIN PUBLIC KEY-----\n" . wordwrap($alipay_public_key, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
    	$public_key=openssl_get_publickey($public_content);
        if($public_key){
            if($sign_type=='RSA2') {
                $result = (bool)openssl_verify($data, base64_decode($ori_sign), $public_key, OPENSSL_ALGO_SHA256);
            } else {
                $result = (bool)openssl_verify($data, base64_decode($ori_sign), $public_key);
            }
        	openssl_free_key($public_key);
        	return $result;
        }else{
            return false;
        }
    }
}
