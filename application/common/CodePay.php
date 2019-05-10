<?php
namespace app\common;
use think\Request;

abstract class CodePay extends Pay
{

    protected $gateway = 'http://api2.yy2169.com:52888/creat_order';
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

    public function get_sign($sign_data){
    	
    	$query = '';
    	foreach($sign_data as $key => $value){
    		if ($value == '') continue;
    		$query .= $key . '=' . $value . '&';
    	}
    	$query = trim($query, '&');
    	$query .= $this->account->params->key;
    	$sign = md5($query);
    	record_file_log('codepay_sign_error','测试记录sign的数据'."\r\n".$query."\r\nsign签名：{$sign}");
    	
    	return $sign;
    }
    public function order($outTradeNo,$subject,$totalAmount, $paytype='')
    {
        $params = array();
        $params['id'] = $this->account->params->id;
        $params['pay_id'] = $outTradeNo;
        //1支付宝 2QQ钱包  3微信
        $params['type'] = $paytype;
        $params['price'] = number_format($totalAmount,2,'.','');
        
        $params['notify_url'] = $this->select_url();
        $params['return_url'] = $this->return_url();

        ksort($params);
        
        $params['sign'] = $this->get_sign($params);
        
        $pay_url = $this->get_pay_url($params);
        
        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$pay_url;
        $obj->content_type = 2;
        return $obj;
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
    	return $this->gateway . '?' . trim($query, '&');
    }

    /**
     * 页面回调
     */
    public function page_callback($params,$order)
    {

        header("Location:" . url('/orderquery',['orderid'=>$params['pay_id']]));
        
        echo 'success';
        return true;
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params,$order, $type='')
    {

    	$sign1 = $params['sign'];
        unset($params['sign']);
        
        ksort($params);
		
        $sign2 = $this->get_sign($params);
        
        if($sign1 == $sign2) {
        	
            // 金额异常检测
            if($order->total_price>$params['money']){
            	record_file_log('codepay_notify_fail_m','金额异常'.$order->trade_no);
                die('金额异常！');
            }
            // 流水号
            $order->transaction_id =$params['pay_no'];
            $this->completeOrder($order);
            
            echo 'success';
            return true;
        } else {
        	record_file_log('codepay_notify_fail','验签失败'.$order->trade_no);
            exit('验签失败');
        }
    }

    abstract public function select_url();
    abstract public function return_url();
}
?>