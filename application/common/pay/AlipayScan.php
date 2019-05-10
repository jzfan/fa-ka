<?php
/**
 * 支付宝扫码支付
 * @author mapeijian
 */
namespace app\common\pay;

use think\Db;
use think\Request;
use app\common\Pay;
use Alipay\wappay\service\AlipayTradeService;
use Alipay\wappay\buildermodel\AlipayTradePagePayContentBuilder;

require_once(ROOT_PATH.'extend/Alipay/aop/request/AlipayTradePagePayRequest.php');

require_once(ROOT_PATH.'extend/Alipay/aop/AopClient.php');

require_once(ROOT_PATH.'extend/Alipay/wappay/service/AlipayTradeService.php');

require_once(ROOT_PATH.'extend/Alipay/wappay/buildermodel/AlipayTradePagePayContentBuilder.php');


class AlipayScan extends Pay{
    protected $code='';
    protected $error='';
    protected $gatewayUrl = 'https://openapi.alipay.com/gateway.do';

    public function getCode()
    {
        return $this->code;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * 扫码支付
     * @param string $outTradeNo 外部单号
     * @param string $subject 标题
     * @param float $totalAmount 支付金额
     */
    public function order($outTradeNo,$subject,$totalAmount) {

        $payRequestBuilder = new AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($subject);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($outTradeNo);
        $payRequestBuilder->setTotalAmount($totalAmount);

        $config=[
            'sign_type'            =>'RSA2',
            'alipay_public_key'    =>$this->account->params->alipay_public_key,
            'merchant_private_key' =>$this->account->params->merchant_private_key,
            'charset'              =>'UTF-8',
            'gatewayUrl'           =>$this->gatewayUrl,
            'app_id'               =>$this->account->params->app_id,
            'notify_url'           =>Request::instance()->domain().'/pay/notify/AlipayScan',
            'return_url'           =>Request::instance()->domain().'/pay/page/AlipayScan',
        ];
        $aop = new AlipayTradeService($config);
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$response;
        $obj->content_type = 3;
        return $obj;
    }

    /**
     * 页面回调
     */
    public function page_callback($params,$order)
    {
         // 验签
        if(!$this->verify_sign($params,$this->account->params->alipay_public_key,'RSA2')){
            record_file_log('alipay_page_error','验签错误！'."\r\n".$order->trade_no."\r\n".$this->account->params->alipay_public_key."\r\n".'RSA2');
            die('验签错误！');
        }
        // 金额异常检测
        if($order->total_price>$params['total_amount']){
            record_file_log('alipay_page_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$params['total_amount']}");
            die('金额异常！');
        }
        // TODO 这里去完成你的订单状态修改操作

        // 流水号
        $order->transaction_id =$params['trade_no'];
        $this->completeOrder($order);
        record_file_log('alipay_page_success',$order->trade_no);

        header("Location:" . url('Index/Order/query',['orderid'=>$order->trade_no]));
        die('success');
    }

    /**
     * 支付通知处理
     */
    public function notify_callback($params,$order) {
        if($params && isset($params['trade_status']) && $params['trade_status']==='TRADE_SUCCESS'){
            // 验签
            if(!$this->verify_sign($params,$this->account->params->alipay_public_key,'RSA2')){
                record_file_log('alipay_notify_error','验签错误！'."\r\n".$order->trade_no."\r\n".$this->account->params->alipay_public_key."\r\n".'RSA2');
                die('验签错误！');
            }
            // 金额异常检测
            if($order->total_price>$params['total_amount']){
                record_file_log('alipay_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$params['total_amount']}");
                die('金额异常！');
            }
            // TODO 这里去完成你的订单状态修改操作
            // 流水号
            $order->transaction_id =$params['trade_no'];
            $this->completeOrder($order);
            record_file_log('alipay_notify_success',$order->trade_no);
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
