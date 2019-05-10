<?php
/**
 * 漯河15173微信支付
 * @author lhj
 */
namespace app\common;
use think\Request;

abstract class Lh15173Pay extends Pay
{

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
            'bargainor_id' => $this->account->params->bargainor_id,
            'sp_billno' => $outTradeNo,
            'total_fee' => $totalAmount,
            'pay_type' => 'a',//用微信支付直接用a，这个是15173工作人员提供的
            'return_url' => $this->return_url(),
            'select_url' => $this->select_url(),
            'attach' => '1',
            'zidy_code' => '1',
            'czip' => Request::instance()->ip(),
            'key' => $this->account->params->key
        ];
        $params = $this->buildParams($config);
        $pay_url = $this->get_pay_url($params);

        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$pay_url;
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
    public function notify_callback($params,$order, $type = '')
    {
        //返回参数要含有订单号才进入校验
        if(isset($params['sp_billno'])){
            //验签
            $sign_data = [
                'pay_result' => $params['pay_result'],
                'bargainor_id' => $params['bargainor_id'],
                'sp_billno' => $params['sp_billno'],
                'total_fee' => $params['total_fee'],
                'attach' => $params['attach'],
                'key' => $this->account->params->key
            ];
            $sign = $this->get_sign($sign_data);

            if ($sign != $params["sign"]) {
                record_file_log('lh15173' . $type . 'pay_notify_error','验签错误！'."\r\n".$order->trade_no);
                die('验签错误！');
            }

            //验证金额
            $money=$params['total_fee'];
            if($order->total_price != $money){
                record_file_log('lh15173' . $type . 'pay_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$money}");
                die('金额异常！');
            }

            // TODO 这里去完成你的订单状态修改操作
            // 流水号
            $order->transaction_id =$params['transaction_id'];
            $this->completeOrder($order);
            record_file_log('lh15173' . $type . 'pay_notify_success',$order->trade_no);

            echo 'OK';//不能修改，必须为OK才是成功对账
            return true;
        }
    }

    /**
     * 构建请求参数
     *
     * @param Array $config
     * @return Array
     */
    public function buildParams($config)
    {
        $sign_data = [
            'bargainor_id'=>$config['bargainor_id'],
            'sp_billno'=>$config['sp_billno'],
            'pay_type'=>'a',
            'return_url'=>$config['return_url'],
            'attach'=>1,
            'key'=>$config['key']
        ];
        $sign = $this->get_sign($sign_data);

        unset($config['key']);

        $params = array_merge($config, ['sign' => $sign]);

        return $params;
    }

    /**
     * 获取签名
     *
     * @param Array $sign_data
     * @return String
     */
    public function get_sign($sign_data)
    {
        
        $query = '';
        foreach($sign_data as $key => $value){
            $query .= $key . '=' . $value . '&';
        }

        $query = trim($query, '&');
        $sign = strtoupper(md5($query));
        //record_file_log('lh15173pcpay_sign_error','测试记录sign的数据'."\r\n".$query."\r\nsign签名：{$sign}");

        return $sign;
    }

    /**
     * 组合支付链接
     *
     * @param Array $params 链接的参数
     * @return String
     */
    public function get_pay_url($params)
    {
        $query = '';
        foreach($params as $key => $value){
            $query .= $key . '=' . $value . '&';
        }
        return $this->pay_api_url() . '?' . trim($query, '&');
    }

    abstract public function pay_api_url();
    abstract public function return_url();
    abstract public function select_url();
}
?>