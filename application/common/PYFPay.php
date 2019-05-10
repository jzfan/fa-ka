<?php
/**
 * 拉卡支付
 * 扫码支付
 * @author lhj
 */
namespace app\common;
use think\Request;
use app\common\Pay;
use service\HttpService;

abstract class PYFPay extends Pay
{
    protected $code='';
    protected $error='';
    protected $api_url = "https://pay.pinyewang.com/submit.php";
	protected $skey = "YZuku9fUm9q9ufQ7mZb8Bu8ZkFSFZ7R9";
	protected $signtype = "MD5";

    public function getCode()
    {
        return $this->code;
    }

    public function getError()
    {
        return $this->error;
    }
	public function order_put($outTradeNo,$subject,$totalAmount)
    {
		$pay_url='https://pay.pinyewang.com/';
        $parameter = array(
            "pid" => '815972',
            "type" => $this->get_bankid(),
            "notify_url"	=> $this->get_notify_url(),
            "return_url"	=> $this->get_result_url(),
            "out_trade_no"	=> $outTradeNo,
            "name"	=> $subject,
            "money"	=> $totalAmount,
            "sitename"	=> $subject
        );

		//echo "<pre>";
		//print_r($parameter);
		$html_text = $this->buildRequestForm($parameter);
		echo $html_text;
    }
    public function order($outTradeNo,$subject,$totalAmount)
    {
        $parameter = array(
            "pid" => '815972',
            "type" => $this->get_bankid(),
            "notify_url"	=> $this->get_notify_url(),
            "return_url"	=> $this->get_result_url(),
            "out_trade_no"	=> $outTradeNo,
            "name"	=> $subject,
            "money"	=> $totalAmount,
            "sitename"	=> $subject
        );
        $parameter = $this->buildRequestPara($parameter);
        $parameter = $this->createLinkstring($parameter);


        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$this->api_url . '?' . $parameter;
        $obj->content_type = $this->get_content_type();
        return $obj;
    }
    /**
     * 页面回调
     */
    public function page_callback($params,$order,$type='')
    {
        //echo '<pre>';
        //print_r($params);
        //exit;
        $isSign = $this->getSignVeryfy($params, $params["sign"]);
        $responseTxt = 'true';
        if (preg_match("/true$/i",$responseTxt) && $isSign) {
            //die("SIGN OK");
        } else {
            die("SIGN ERROR");
        }

        record_file_log('PYFPay_notify',$order->trade_no);

        $money= $params["money"];
        $trade_status= $params["trade_status"];
        $trade_no= $params["trade_no"];
        $out_trade_no= $params["out_trade_no"];
        //订单号为必须接收的参数，若没有该参数，则返回错误
        if(empty($out_trade_no)){
            die("out_trade_no NOT FOUND");
        }
        if($isSign){
            if ($trade_status == "TRADE_SUCCESS") {
                if($money != $order->total_price){
                    $errstr='金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$money}";
                    record_file_log('PYFPay NOTIFY' . $type . 'pay_notify_error',$errstr);
                    die($errstr);
                }
                $order->transaction_id =$trade_no;
                $this->completeOrder($order);
                record_file_log('PYFPay NOTIFY' . $type . 'pay_notify_success',$order->trade_no);
                header("Location:" . Request::instance()->domain().'/orderquery?orderid='.$order->trade_no);
            }else{
                exit("pay err");
            }
        }else{
            die("SIGN ERROR");		//签名不正确
        }
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params,$order, $type = '')
    {
		//echo '<pre>';
		//print_r($params);
		//exit;		
		$isSign = $this->getSignVeryfy($params, $params["sign"]);
		$responseTxt = 'true';
		if (preg_match("/true$/i",$responseTxt) && $isSign) {
			//die("SIGN OK");
		} else {
			die("SIGN ERROR");
		}

        record_file_log('PYFPay_notify',$order->trade_no);
			
		$money= $params["money"];
		$trade_status= $params["trade_status"];
		$trade_no= $params["trade_no"];
		$out_trade_no= $params["out_trade_no"];
		//订单号为必须接收的参数，若没有该参数，则返回错误
		if(empty($out_trade_no)){
			die("out_trade_no NOT FOUND");
		}
		if($isSign){
			if ($trade_status == "TRADE_SUCCESS") {
                   if($money != $order->total_price){
                    $errstr='金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$money}";
                    record_file_log('PYFPay NOTIFY' . $type . 'pay_notify_error',$errstr);
                    die($errstr);
                }
                $order->transaction_id =$trade_no;
                $this->completeOrder($order);
                record_file_log('PYFPay NOTIFY' . $type . 'pay_notify_success',$order->trade_no);
                echo 'SUCCESS';
                return true;
            }else{
                exit("pay err");
            }
		}else{
			die("SIGN ERROR");		//签名不正确
		}
    }
	/**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
	function buildRequestForm($para_temp, $method='POST', $button_name='正在跳转') {
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		$sHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$sHtml .= '<html>';
		$sHtml .= '<head>';
		$sHtml .= '	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		$sHtml .= '	<title>'.$button_name.'</title>';
		$sHtml .= '</head>';
		$sHtml .= "<form name='alipaysubmit' action='".$this->api_url."submit.php?_input_charset=".trim(strtolower('MD5'))."' method='".$method."'>";
		while (list ($key, $val) = each ($para)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
		//submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";
		$sHtml = $sHtml."<script type='text/javascript'>document.alipaysubmit.submit();</script>";
		$sHtml = $sHtml."</body>";
		$sHtml = $sHtml."</html>";
		return $sHtml;
	}
	/**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
	function buildRequestPara($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = $this->paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = $this->argSort($para_filter);

		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = strtoupper(trim($this->signtype));
		
		return $para_sort;
	}
	function buildRequestMysign($para_sort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->createLinkstring($para_sort);
		
		$mysign = $this->md5Sign($prestr, $this->skey);

		return $mysign;
	}
	/**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = $this->paraFilter($para_temp);
		
		//对待签名参数数组排序
		$para_sort = $this->argSort($para_filter);
		
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->createLinkstring($para_sort);
		
		$isSgin = false;
		$isSgin = $this->md5Verify($prestr, $sign, $this->skey);
		return $isSgin;
	}
	function createLinkstring($para) {
		$arg  = "";
		while (list ($key, $val) = each ($para)) {
			$arg.=$key."=".$val."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
		
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
		
		return $arg;
	}
	function paraFilter($para) {
		$para_filter = array();
		while (list ($key, $val) = each ($para)) {
			if($key == "sign" || $key == "sign_type" || $val == "")continue;
			else	$para_filter[$key] = $para[$key];
		}
		return $para_filter;
	}
	/**
	 * 对数组排序
	 * @param $para 排序前的数组
	 * return 排序后的数组
	 */
	function argSort($para) {
		ksort($para);
		reset($para);
		return $para;
	}
	/**
	 * 签名字符串
	 * @param $prestr 需要签名的字符串
	 * @param $key 私钥
	 * return 签名结果
	 */
	function md5Sign($prestr, $key) {
		$prestr = $prestr . $key;
		return md5($prestr);
	}

	/**
	 * 验证签名
	 * @param $prestr 需要签名的字符串
	 * @param $sign 签名结果
	 * @param $key 私钥
	 * return 签名结果
	 */
	function md5Verify($prestr, $sign, $key) {
		$prestr = $prestr . $key;
		$mysgin = md5($prestr);
		if($mysgin == $sign) {
			return true;
		}
		else {
			return false;
		}
	}
	
    abstract public function get_bankid();
    abstract public function get_content_type();
    abstract public function get_result_url();
    abstract public function get_notify_url();
}