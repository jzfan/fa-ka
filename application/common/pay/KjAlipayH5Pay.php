<?php
/**
 * 快接支付宝扫码支付
 * @author lhj
 */
namespace app\common\pay;
use think\Request;
use app\common\Pay;
use service\HttpService;

class KjAlipayH5Pay extends Pay
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
        $config = [
            'merchant_no'       => $this->account->params->merchant_no,
            'merchant_order_no' => $outTradeNo,
            'notify_url'        => Request::instance()->domain().'/pay/notify/KjAlipayScanPay',
            'return_url'        => Request::instance()->domain().'/orderquery/orderid/'.$outTradeNo,
            'start_time'        => date('YmdHis'),
            'trade_amount'      => $totalAmount,
            'goods_name'        => $subject,
            'goods_desc'        => $subject,
            'sign_type'         => 1,
            'appkey'            => $this->account->params->appkey
        ];
        $params = $this->buildParams($config);
        //请求获取支付地址
        $response = HttpService::post(trim($this->account->params->api_url, '/') . '/alipay/wap_pay', $params, 30, [], $this->account->params->refer);
        $response = \json_decode($response, true);
        if($response['status'] != 1){
            $this->code = 1;
            $this->error = $response['info'];
            return false;
        }

        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$response['data']['pay_url'];
        $obj->content_type = 2;
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
        if($params['status'] == 'Success'){
            $sign_data = array_merge($params, ['appkey'=>$this->account->params->appkey]);
            $sign = $this->sign($sign_data);
            if ($sign != $params["sign"]) {
                record_file_log('KjAlipayH5Pay_notify_error','验签错误！'."\r\n".$order->trade_no);
                die('验签错误！');
            }
            // 金额异常检测
            $money=$params['amount'];
            if($order->total_price>$money){
                record_file_log('KjAlipayH5Pay_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$money}");
                die('金额异常！');
            }
            // TODO 这里去完成你的订单状态修改操作
            // 流水号
            $order->transaction_id =$params['trade_no'];
            $this->completeOrder($order);
            record_file_log('KjAlipayH5Pay_notify_success',$order->trade_no);
            echo 'success';
            return true;
        }else if($params['status'] == 'Fail'){
            record_file_log('KjAlipayH5Pay_notify_error','交易失败！'."\r\n".$order->trade_no);
            die('交易失败！');
        }
    }

    /**
     * 过滤参数
     *
     * @param Array $para
     * @return Array
     */
    protected function paraFilters($para)
    {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $val == "" || $key == 'appkey')continue;
            else	$para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    /**
     * 对数组进行排序
     *
     * @param Array $para
     * @return Array
     */
    protected function argSorts($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 签名
     *
     * @param Array $datas
     * @param String $key
     * @return String
     */
    protected function sign($datas = [], $key = "")
    {
        $str = urldecode(http_build_query($this->argSorts($this->paraFilters($datas))));
        $sign = md5($str."&key=".$datas['appkey']);
        return $sign;
    }

    /**
     * 构建参数
     *
     * @param Array $config
     * @return Array
     */
    public function buildParams($config)
    {
        $sign = $this->sign($config);
        $config['sign'] = $sign;

        unset($config['appkey']);

        return $config;
    }
}
?>