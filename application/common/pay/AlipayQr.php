<?php
/**
 * 支付宝当面付
 * @author Veris
 */
namespace app\common\pay;

use think\Db;
use think\Request;
use app\common\Pay;

require_once ROOT_PATH . 'extend/Alipay/f2fpay/model/builder/AlipayTradePrecreateContentBuilder.php';
require_once ROOT_PATH . 'extend/Alipay/f2fpay/service/AlipayTradeService.php';

class AlipayQr extends Pay{
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
    	$undiscountableAmount = $totalAmount;
    	$body = $subject;
    	$providerId = ""; //系统商pid,作为系统商返佣数据提取的依据
    	$extendParams = new \ExtendParams();
    	$extendParams->setSysServiceProviderId($providerId);
    	$extendParamsArr = $extendParams->getExtendParams();

    	// 支付超时，线下扫码交易定义为5分钟
    	$timeExpress = "5m";

    	// 商品明细列表，需填写购买商品详细信息，
    	$goodsDetailList = array();

    	// 创建一个商品信息，参数含义分别为商品id（使用国标）、名称、单价（单位为分）、数量，如果需要添加商品类别，详见GoodsDetail
    	// $goods1 = new \GoodsDetail();
    	// $goods1->setGoodsId("apple-01");
    	// $goods1->setGoodsName("iphone");
    	// $goods1->setPrice(3000);
    	// $goods1->setQuantity(1);
    	// //得到商品1明细数组
    	// $goods1Arr = $goods1->getGoodsDetail();
    	// $goodsDetailList = array($goods1Arr);

    	//第三方应用授权令牌,商户授权系统商开发模式下使用
    	$appAuthToken = "";//根据真实值填写

    	// 创建请求builder，设置请求参数
    	$qrPayRequestBuilder = new \AlipayTradePrecreateContentBuilder();
    	$qrPayRequestBuilder->setOutTradeNo($outTradeNo);
    	$qrPayRequestBuilder->setTotalAmount($totalAmount);
    	$qrPayRequestBuilder->setTimeExpress($timeExpress);
    	$qrPayRequestBuilder->setSubject($subject);
    	$qrPayRequestBuilder->setBody($body);
    	$qrPayRequestBuilder->setUndiscountableAmount($undiscountableAmount);
    	$qrPayRequestBuilder->setExtendParams($extendParamsArr);
    	$qrPayRequestBuilder->setGoodsDetailList($goodsDetailList);

    	$qrPayRequestBuilder->setAppAuthToken($appAuthToken);

        $config=[
            'sign_type'            => 'RSA2',
            'alipay_public_key'    => $this->account->params->alipay_public_key,
            'merchant_private_key' => $this->account->params->merchant_private_key,
            'charset'              => 'UTF-8',
            'gatewayUrl'           => $this->gatewayUrl,
            'app_id'               => $this->account->params->app_id,
            'notify_url'           => Request::instance()->domain().'/pay/notify/AlipayQr',
            'MaxQueryRetry'        => 10,
            'QueryDuration'        => 3,
        ];
    	// 调用qrPay方法获取当面付应答
    	$qrPay = new \AlipayTradeService($config);
    	$qrPayResult = $qrPay->qrPay($qrPayRequestBuilder);
    	//	根据状态值进行业务处理
    	switch ($qrPayResult->getTradeStatus()){
    		case "SUCCESS":
    			$response = $qrPayResult->getResponse();
    			// $qrcode = $qrPay->create_erweima($response->qr_code);
    			// echo $qrcode;
                $this->code    =0;
                $obj           =new \stdClass();
                $obj->pay_url  =$response->qr_code;
                $obj->content_type = 1;
                return $obj;
    			break;
    		case "FAILED":
                $this->code=201;
                $this->error='支付宝创建订单二维码失败！'.$qrPayResult->getResponse();
                return false;
    			break;
    		case "UNKNOWN":
                $this->code=202;
                $this->error='系统异常，状态未知！'.$qrPayResult->getResponse();
                return false;
    			break;
    		default:
                $this->code=203;
                $this->error='不支持的返回状态，创建订单二维码返回异常！';
                return false;
    			break;
    	}
    	return ;
    }

    /**
     * 页面回调
     */
    public function page_callback($params,$order)
    {
        header("Location:" . url('/orderquery',['orderid'=>$order->trade_no]));
    }

    /**
     * 支付通知处理
     */
    public function notify_callback($params,$order) {
        if($params && isset($params['trade_status']) && $params['trade_status']==='TRADE_SUCCESS'){
            // 验签
            if(!$this->verify_sign($params,$this->account->params->alipay_public_key,$this->account->params->sign_type)){
                record_file_log('alipay_notify_error','验签错误！'."\r\n".$order->trade_no."\r\n".$this->account->params->alipay_public_key."\r\n".$this->account->params->sign_type);
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
