<?php
/**
 * 蜂鸟微信公众号支付
 * @author mapeijian
 */
namespace app\common\pay;
use think\Request;
use app\common\Pay;
use Util\FnPay\ApiClient;
class FnWxJspay extends Pay
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
        $redirect_uri = urlencode(Request::instance()->domain().'/index/pay/wx_js_api_call');
        $pay_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->account->params->wx_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$outTradeNo."#wechat_redirect";
        if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $this->code    =0;
            $obj           =new \stdClass();
            $obj->pay_url  =$pay_url;
            $obj->content_type = 2;
            return $obj;
        } else {
            $this->code    =0;
            $obj           =new \stdClass();
            $obj->pay_url  =url('index/pay/wx_jspay_page').'?trade_no=' . $outTradeNo . '&url='.base64_encode($pay_url);
            $obj->content_type = 2;
            return $obj;
        }
    }

    public function js_api_call($code, $order)
    {
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->account->params->wx_appid."&secret=".$this->account->params->wx_secret."&code=".$code."&grant_type=authorization_code");
        $array = json_decode($weixin, true);
        $openid = isset($array['openid']) ? $array['openid'] : '';
        if(!$openid) {
            echo '获取微信openid失败';
            exit();
        }
        $client = new ApiClient();
        $client->appId = $this->account->params->appid;
        $client->secret = $this->account->params->secret;
        $response = $client->call('weixin.mppay', [
            'merchant_no' => $this->account->params->merchant_no,
            'out_trade_no' => $order['trade_no'],
            'order_name' => $order['trade_no'],
            'total_amount' => $order['total_price'],
            'sub_appid' => $this->account->params->wx_appid,
            'sub_openid' => $openid,
            'spbill_create_ip' => $this->getAddress(),
            'notify_url' => Request::instance()->domain().'/pay/notify/FnWxJspay',
            'success_url' => Request::instance()->domain().'/orderquery/orderid/'.$order['trade_no']
        ], $this->account->params->refer);

        if($response['error_code'] == 0) {
            header("Location: ".$response['pay_url']);
        } else {
            exit($response['error_msg']);
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
                record_file_log('FnWxJspay_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$params['total_amount']}");
                die('金额异常！');
            }
            $order->transaction_id =$params['trade_no'];
            $this->completeOrder($order);
            record_file_log('FnWxJspay_notify_success',$order->trade_no);
            echo 'SUCCESS';
            return true;
        } else {
            exit('FAIL');
        }
    }

    private function getAddress()
    {
        foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'REMOTE_ADDR') as $header) {
            if (!isset($_SERVER[$header]) || ($spoof = $_SERVER[$header]) === null) {
                continue;
            }
            sscanf($spoof, '%[^,]', $spoof);
            if (!filter_var($spoof, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $spoof = null;
            } else {
                return $spoof;
            }
        }
        return '0.0.0.0';
    }
}
?>