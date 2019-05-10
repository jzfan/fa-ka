<?php
/**
 * 深度支付宝公众号
 * @author mapeijian
 */
namespace app\common\pay;
use think\Request;
use app\common\Pay;
class ShenduAliJspay extends Pay
{

    protected $gateway = 'http://gateway.shendupay.com/gateway';
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
        $params = [
            'mch_id'       => $this->account->params->mch_id,
            'sign_type'    => 'MD5',
            'charset'      => 'utf-8',
            'version'      => '1.0',
            'timestamp'    => date('Y-m-d H:i:s'),
            'notify_url'   => Request::instance()->domain().'/pay/notify/ShenduAliJspay',
            'payment_code' => '8006',
            'out_trade_no' => $outTradeNo,
            'total_fee'    => $totalAmount,
            'body'         => $subject,
            'return_url'   => Request::instance()->domain().'/pay/page/ShenduAliJspay',
        ];
        $string = $this->create_link_string($params);
        $params['sign'] = md5($string.'&key='.$this->account->params->md5_key);
        $header = array(
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
            'X-Requested-With:XMLHttpRequest',
        );
        $response = $this->curl_http(rtrim($this->gateway, '/'), $params, 'post', $header);
        if(!$response) {
            $this->code    =201;
            $this->error = '调用接口失败';
            return false;
        }
        $response = json_decode($response, true);
        if($response['state'] == '1') {
            $this->code    =0;
            $obj           =new \stdClass();
            if(Request::instance()->isMobile()) {
                $obj->pay_url  =$response['data']['jump_url'];
                $obj->content_type = 2;
            }else{
                $url = $response['data']['image_url'];
                $url = parse_url($url);
                $url = substr(urldecode($url['query']),4);
                $obj->pay_url  =$url;
                $obj->content_type = 1;
            }
            return $obj;
        } else {
            $this->code    =201;
            $this->error = isset($response['msg']) ? $response['msg'] : '支付失败';
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
        //商户号(商户id)
        $mch_id           = isset($params['mch_id']) ? $params['mch_id'] : '';
        //签名算法类型
        $sign_type        = isset($params['sign_type']) ? $params['sign_type'] : '';
        //编码格式
        $charset          = 'utf-8';
        //接口版本
        $version          = isset($params['version']) ? $params['version'] : '';
        //商户订单号
        $out_trade_no     = isset($params['out_trade_no']) ? $params['out_trade_no'] : '';
        //发送请求的时间
        $timestamp        = isset($params['timestamp']) ? $params['timestamp'] : '';
        //支付渠道编码
        $payment_code     = isset($params['payment_code']) ? $params['payment_code'] : '';
        //订单描述
        $body             = isset($params['body']) ? $params['body'] : '';
        //业务扩展参数
        $attach           = isset($params['attach']) ? $params['attach'] : '';
        //付款金额
        $total_fee        = isset($params['total_fee']) ? $params['total_fee'] : '';
        //网关订单号
        $trade_no         = isset($params['trade_no']) ? $params['trade_no'] : '';
        //银行订单号,仅在订单为支付成功或部分付款状态时,才会有反馈出来
        $channel_trade_no = isset($params['channel_trade_no']) ? $params['channel_trade_no'] : '';
        //订单付款状态(付款成功:TRADE_FINISHED; 部分付款:TRADE_PART_PAY; 有退款:TRADE_REFUND; 未付款:TRADE_WAIT_PAY; 订单关闭:TRADE_CLOSED)
        $trade_status     = isset($params['trade_status']) ? $params['trade_status'] : '';
        //订单付款时间,仅在订单为支付成功或部分付款状态时,才会有反馈出来
        $payment_time     = isset($params['payment_time']) ? $params['payment_time'] : '';
        //接口签名
        $sign             = isset($params['sign']) ? $params['sign'] : '';

        //构造验签数组
        $params = [
            'mch_id'           => $mch_id,				//商户号(商户id)
            'sign_type'        => $sign_type,			//签名算法类型
            'charset'          => $charset,				//编码格式
            'version'          => $version,				//接口版本
            'out_trade_no'     => $out_trade_no,		//商户订单号
            'timestamp'        => $timestamp,			//发送请求的时间
            'payment_code'     => $payment_code,		//支付渠道编码
            'body'             => $body,				//订单描述
            'attach'           => $attach,				//业务扩展参数
            'total_fee'        => $total_fee,			//付款金额
            'trade_no'         => $trade_no,			//网关订单号
            'channel_trade_no' => $channel_trade_no,	//银行订单号,仅在订单为支付成功或部分付款状态时,才会有反馈出来
            'trade_status'     => $trade_status,		//订单付款状态
            'payment_time'     => $payment_time,		//订单付款时间
            'sign'             => $sign,				//接口签名
        ];

        if ($params['sign'] == '' || $params['out_trade_no'] == '') exit('fail');

        $is_sign = $this->check_sign($params, $this->account->params->md5_key);
        if ($is_sign) {
            if ($params['trade_status'] == 'TRADE_FINISHED') {
                $order->transaction_id = $params['trade_no'];
                $this->completeOrder($order);
                exit('success');
            }
        } else {
            exit('fail');
        }
    }

    /**
     * 生成待签名的字符串
     */
    protected function create_link_string($para) {
        //除去数组中的空值和签名参数
        $tmp = [];
        foreach ($para as $k => $v) {
            if ($k == 'sign' || $k == 'sign_type' || strval($v) === '') continue;
            $tmp[$k] = $v;
        }

        //对数组排序
        ksort($tmp);
        reset($tmp);

        //把数组所有元素，按照'参数=参数值'的模式用'&'字符拼接成字符串
        $arg  = '';
        foreach ($tmp as $k => $v) {
            $arg .= $k. '=' . strval($v) . '&';
        }
        $arg = trim($arg, '&');

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) $arg = stripslashes($arg);

        return $arg;
    }

    /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @param  array  $header 需要发送的请求header
     * @return
     */
    protected function curl_http($url, $params='', $method='GET', $header=array()) {
        $opts = array(
            CURLOPT_TIMEOUT         =>  5,
            CURLOPT_RETURNTRANSFER  =>  1,// 显示输出结果
            CURLOPT_HEADER          =>  0,// 过滤HTTP头
            CURLOPT_FOLLOWLOCATION  =>  1,// 跳转选项,如果出现错误码:3, 优先检查这里
            CURLOPT_SSL_VERIFYPEER  =>  0,// SSL证书认证
            CURLOPT_SSL_VERIFYHOST  =>  0,// 证书认证
            CURLOPT_HTTP_VERSION    =>  CURL_HTTP_VERSION_1_0,// 强制协议为1.0
            CURLOPT_IPRESOLVE       =>  CURL_IPRESOLVE_V4,// 强制使用IPV4
        );

        //添加header
        if ($header) $opts[CURLOPT_HTTPHEADER] = $header;

        if (is_array($params)) $params = http_build_query($params);
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL]           = $params ? $url.'?'.$params : $url;
                $opts[CURLOPT_CUSTOMREQUEST] = 'GET';
                break;

            case 'POST':
                $opts[CURLOPT_URL]        = $url;
                $opts[CURLOPT_POST]       = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;

            default:
                //
                break;
        }

        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $result  = curl_exec($ch);

        /* 判断请求响应 */
        if ($result) {
            curl_close($ch);
            return $result;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            exit('请求发起失败,错误码:' . $error);
        }
    }

    /**
     * 判断订单签名
     * $params 待签名的数组
     * $sign_type 签名方式
     */
    protected function check_sign($params, $md5_key) {

        if (empty($params)) return false;
        if (!is_array($params)) return false;
        if (!isset($params['sign'])) return false;
        //拼接待签名的参数
        $string = $this->create_link_string($params);
        $sign = md5($string . '&key=' . $md5_key);
        return boolval($sign == $params['sign']);
    }
}
?>