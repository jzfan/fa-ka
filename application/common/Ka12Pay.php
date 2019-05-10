<?php
namespace app\common;
use think\Request;

abstract class Ka12Pay extends Pay
{

    protected $gateway = 'http://pay.ecmayi.cn/apisubmit';
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
    		$query .= $key . '=' . $value . '&';
    	}
    	
    	$query .= $this->account->params->apikey;
    	$sign = md5($query);
    	//record_file_log('ka12pcpay_sign_error','测试记录sign的数据'."\r\n".$query."\r\nsign签名：{$sign}");
    	
    	return $sign;
    }
    public function order($outTradeNo,$subject,$totalAmount, $paytype='')
    {
        $params = array();
        $params['version'] = '1.0';
        $params['customerid'] = $this->account->params->customerid;
        $params['sdorderno'] = $outTradeNo;
        $params['paytype'] = $paytype;
        $params['total_fee'] = number_format($totalAmount,2,'.','');
        
        $params['notifyurl'] = $this->select_url();
        $params['returnurl'] = $this->return_url();
        $params['remark'] = '';
        $params['bankcode'] = 'ICBC';
        $params['get_code'] = 0;
        
        $signdata = array();
        $signdata['version'] = $params['version'];
        $signdata['customerid'] = $params['customerid'];
        $signdata['total_fee'] = $params['total_fee'];
        $signdata['sdorderno'] = $params['sdorderno'];
        $signdata['notifyurl'] = $params['notifyurl'];
        $signdata['returnurl'] = $params['returnurl'];
        $params['sign'] = $this->get_sign($signdata);
        
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
        // 验签
        $sign1 = $params['sign'];
        unset($params['sign']);
        $signdata = array();
        $signdata['customerid'] = $params['customerid'];
        $signdata['status'] = $params['status'];
        $signdata['sdpayno'] = $params['sdpayno'];
        $signdata['sdorderno'] = $params['sdorderno'];
        $signdata['total_fee'] = number_format($params['total_fee'],2,'.','');;
        $signdata['paytype'] = $params['paytype'];

        $sign2 = $this->get_sign($signdata);
        
        if($sign1 != $sign2) {
        	die('验签错误');
        }
        // 金额异常检测
        if($order->total_price>$params['total_fee']){
            die('金额异常！');
        }
        // TODO 这里去完成你的订单状态修改操作
        // 流水号
        /* $order->transaction_id =$params['sdpayno'];
        $this->completeOrder($order); */
        header("Location:" . url('/orderquery',['orderid'=>$params['sdpayno']]));
        

        //header('location:'.url('/orderquery').'?orderid='.$params['sdpayno']);
        die('success');
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params,$order, $type='')
    {
    	$sign1 = $params['sign'];
        unset($params['sign']);
        $signdata = array();
        $signdata['customerid'] = $params['customerid'];
        $signdata['status'] = $params['status'];
        $signdata['sdpayno'] = $params['sdpayno'];
        $signdata['sdorderno'] = $params['sdorderno'];
        $signdata['total_fee'] = number_format($params['total_fee'],2,'.','');
        $signdata['paytype'] = $params['paytype'];

        $sign2 = $this->get_sign($signdata);
        if($sign1 == $sign2) {
            // 金额异常检测
            if($order->total_price>$params['total_fee']){
            	record_file_log('ka12_notify_fail_m','金额异常'.$order->trade_no);
                die('金额异常！');
            }
            // 流水号
            $order->transaction_id =$params['sdpayno'];
            $this->completeOrder($order);
            //record_file_log('ka12_notify_fail',$order->trade_no);
            echo 'success';
            return true;
        } else {
        	//record_file_log('ka12_notify_fail_m',$sign1);
        	record_file_log('ka12_notify_fail','验签失败'.$order->trade_no);
            exit('验签失败');
        }
    }

    abstract public function select_url();
    abstract public function return_url();
}
?>