<?php
class Reqbase
{
    protected $values = array();

    /**
	* 设置签名，详见签名生成算法
	* @param string $value 
	**/
    public function SetSign($appsecret)
    {
        $sign=$this->MakeSign($appsecret);
        $this->values['Sign']=$sign;
        return $sign;
    }

    /**
	* 获取签名，详见签名生成算法的值
	* @return 值
	**/
    public function GetSign()
	{
		return $this->values['Sign'];
    }
    
    /**
	* 判断签名，详见签名生成算法是否存在
	* @return true 或 false
	**/
    public function IsSignSet()
	{
		return array_key_exists('Sign', $this->values);
	}

	public function FromJson($json)
	{
		$this->values = json_decode($json,true);		
		return $this->values;
	}

    /**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams()
	{
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($k != "Sign" && $v."" != "" && !is_array($v)){
				if(is_float($v))
				{
					$buff .= $k . "=" . sprintf('%.2f', $v) . "&";
				}
				else
				{
					$buff .= $k . "=" . $v . "&";
				}
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
    }
    
    /**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
    public function MakeSign($appsecret)
	{
		if(trim($appsecret)=='')
			throw new DzPayException("商户密钥未设置");
		//签名步骤一：按字典序排序参数
		ksort($this->values);
		$string = $this->ToUrlParams();
		//签名步骤二：在string后加入KEY
		$string = $string . "&Key=".$appsecret;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
    }
    
    /**
	 * 获取设置的值
	 */
    public function GetValues()
	{
		return $this->values;
	}
}

class Respbase extends Reqbase
{
    /**
	 * 
	 * 检测签名
	 */
	public function CheckSign($appsecret)
	{
		//fix异常
		if(!$this->IsSignSet()){
			throw new DzPayException("签名错误！签名为空");
		}
		
		$sign = $this->MakeSign($appsecret);
		if($this->GetSign() == $sign){
			return true;
		}
		throw new DzPayException("签名错误！"."签名串:".$this->ToUrlParams()."签名结果：".$sign."返回签名:".$this->GetSign());
    }
    
    /**
	 * 
	 * 使用数组初始化
	 * @param array $array
	 */
	public function FromArray($array)
	{
		$this->values = $array;
	}
	
	/**
	 * 
	 * 使用数组初始化对象
	 * @param array $array
	 */
	public static function InitFromArray($array)
	{
		$obj = new self();
		$obj->FromArray($array);
        return $obj;
    }
    
    /**
	 * 
	 * 设置参数
	 * @param string $key
	 * @param string $value
	 */
	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}

	public function GetData($key)
	{
		return $this->values[$key];
	}
	
    /**
     * 将json转为array
     * @param string $json
     */
	public static function Init($json)
	{	
		#echo $json;
		$obj = new self();
		$obj->FromJson($json);
        return $obj;
	}

	public function GetSuccess($appsecret, $checkCode=true)
	{
		$success = false;
		try
		{
			$this->CheckSign($appsecret);
			if($checkCode)
			{
				if($this->values['RespType'] == '0' && $this->values['RespCode'] =='00'){
					$success=true;
				}
			}
			else
			{
				$success=true;
			}
		}
		catch(Exception $ex)
		{

		}
		return $success;
	}
}

class Payment extends Reqbase
{
	public function SetAppid($value)
    {
        $this->values['AppId'] = $value;
    }

    public function GetAppid()
    {
        return $this->values['AppId'];
    }

    public function IsAppidSet()
    {
        return array_key_exists('AppId',$this->values);
	}
	
	public function SetMerchantOrderNo($value)
	{
		$this->values['MerchantOrderNo'] = $value;
	}

    public function GetMerchantOrderNo()
    {
        return $this->values['MerchantOrderNo'];
    }

    public function IsMerchantOrderNoSet()
    {
        return array_key_exists('MerchantOrderNo',$this->values);
	}
	
	public function SetProductName($value)
	{
		$this->values['ProductName'] = $value;
	}

	public function GetProductName()
	{
		return $this->values['ProductName'];
	}

	public function IsProductNameSet(){
		return array_key_exists('ProductName',$this->values);
	}

	public function SetProductDescription($value)
	{
		$this->values['ProductDescription'] = $value;
	}

	public function GetProductDescription()
	{
		return $this->values['ProductDescription'];
	}

	public function IsProductDescriptionSet(){
		return array_key_exists('ProductDescription',$this->values);
	}

	public function SetAmount($value)
    {
		$this->values['Amount'] = $value;
    }
    
    public function GetAmount()
	{
		return $this->values['Amount'];
    }
    
    public function IsAmountSet()
	{
		return array_key_exists('Amount', $this->values);
    }
	
	public function SetNotifyUrl($value)
	{
		$this->values['NotifyUrl'] = $value;
	}

	public function GetNotifyUrl()
	{
		return $this->values['NotifyUrl'];
	}

	public function IsNotifyUrlSet()
	{
		return array_key_exists('NotifyUrl',$this->values);
	}

	public function SetPayChannel($value)
	{
		$this->values['PayChannel'] = $value;
	}

	public function GetPayChannel()
	{
		return $this->values['PayChannel'];
	}

	public function IsPayChannelSet()
	{
		return array_key_exists('PayChannel',$this->values);
	}

	public function SetReqDate($value)
    {
        $this->values['ReqDate'] = $value;
    }

    public function GetReqDate()
	{
		return $this->values['ReqDate'];
    }
    
    public function IsReqDateSet()
	{
		return array_key_exists('ReqDate', $this->values);
    }

    public function SetExtMsg($value)
    {
        $this->values['ExtMsg']= $value;
    }

    public function GetExtMsg()
    {
        return $this->values['ExtMsg'];
    }

    public function IsExtMsgSet()
    {
        return array_key_exists('ExtMsg',$this->values);
	}
	
	public function SetVersion($value)
	{
		$this->values['Version']=$value;
	}

	public function GetVersion()
	{
		return $this->value['Version'];
	}

	public function IsVersionSet()
	{
		return array_key_exists('Version',$this->values);
	}

	function __construct() {
		$this->SetVersion("1.2");
	}
}

class WXScancode extends Payment
{
	public function SetEqrc($value)
	{
		$this->values['Eqrc']=$value;
	}

	public function GetEqrc()
	{
		return $this->value['Eqrc'];
	}

	public function IsEqrcSet()
	{
		return array_key_exists('Eqrc',$this->values);
	}

	function __construct($appid, $notify_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1202");
		$this->SetEqrc("False");
	}
}

class AliScancode extends Payment
{
	public function SetEqrc($value)
	{
		$this->values['Eqrc']=$value;
	}

	public function GetEqrc()
	{
		return $this->value['Eqrc'];
	}

	public function IsEqrcSet()
	{
		return array_key_exists('Eqrc',$this->values);
	}

	function __construct($appid, $notify_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1102");
		$this->SetEqrc("False");
	}
}

class QQScancode extends Payment
{
	function __construct($appid, $notify_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1301");
	}
}

class WXBarcode extends Payment
{
    public function SetAuthCode($value)
    {
        $this->values['AuthCode'] = $value;
    }

    public function GetAuthCode()
	{
		return $this->values['AuthCode'];
    }
    
    public function IsAuthCodeSet()
	{
		return array_key_exists('AuthCode', $this->values);
    }
	
	function __construct($appid, $notify_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1205");
	}
}

class AliBarcode extends Payment
{
    public function SetAuthCode($value)
    {
        $this->values['AuthCode'] = $value;
    }

    public function GetAuthCode()
	{
		return $this->values['AuthCode'];
    }
    
    public function IsAuthCodeSet()
	{
		return array_key_exists('AuthCode', $this->values);
    }
	
	function __construct($appid, $notify_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1103");
	}
}

class WXPublic extends Payment
{
	public function SetWechatAppId($value)
    {
        $this->values['WechatAppId'] = $value;
    }

    public function GetWechatAppId()
	{
		return $this->values['WechatAppId'];
    }
    
    public function IsWechatAppIdSet()
	{
		return array_key_exists('WechatAppId', $this->values);
	}
	
	public function SetBuyerId($value)
    {
        $this->values['BuyerId'] = $value;
    }

    public function GetBuyerId()
	{
		return $this->values['BuyerId'];
    }
    
    public function IsBuyerIdSet()
	{
		return array_key_exists('BuyerId', $this->values);
	}
	
	public function SetPromptView($value)
    {
        $this->values['PromptView'] = $value;
    }

    public function GetPromptView()
	{
		return $this->values['PromptView'];
    }
    
    public function IsPromptViewSet()
	{
		return array_key_exists('PromptView', $this->values);
	}

	public function SetReturnUrl($value)
    {
        $this->values['ReturnUrl'] = $value;
    }

    public function GetReturnUrl()
	{
		return $this->values['ReturnUrl'];
    }
    
    public function IsReturnUrlSet()
	{
		return array_key_exists('ReturnUrl', $this->values);
	}
	
	function __construct($appid, $notify_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1203");
	}
}

class WXMinpay extends Payment
{
	public function SetWechatAppId($value)
    {
        $this->values['WechatAppId'] = $value;
    }

    public function GetWechatAppId()
	{
		return $this->values['WechatAppId'];
    }
    
    public function IsWechatAppIdSet()
	{
		return array_key_exists('WechatAppId', $this->values);
	}
	
	public function SetBuyerId($value)
    {
        $this->values['BuyerId'] = $value;
    }

    public function GetBuyerId()
	{
		return $this->values['BuyerId'];
    }
    
    public function IsBuyerIdSet()
	{
		return array_key_exists('BuyerId', $this->values);
	}
	
	function __construct($appid, $notify_url)
	{
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1206");
	}
}

class WXH5 extends Payment
{
	public function SetReturnUrl($value)
    {
        $this->values['ReturnUrl'] = $value;
    }

    public function GetReturnUrl()
	{
		return $this->values['ReturnUrl'];
    }
    
    public function IsReturnUrlSet()
	{
		return array_key_exists('ReturnUrl', $this->values);
	}

	public function SetSceneInfo($value)
    {
        $this->values['SceneInfo'] = $value;
    }

    public function GetSceneInfo()
	{
		return $this->values['SceneInfo'];
    }
    
    public function IsSceneInfoSet()
	{
		return array_key_exists('SceneInfo', $this->values);
	}

	function __construct($appid, $notify_url, $return_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1201");
		$this->SetReturnUrl($return_url);
	}
}

class AliH5 extends Payment
{
	public function SetReturnUrl($value)
    {
        $this->values['ReturnUrl'] = $value;
    }

    public function GetReturnUrl()
	{
		return $this->values['ReturnUrl'];
    }
    
    public function IsReturnUrlSet()
	{
		return array_key_exists('ReturnUrl', $this->values);
	}
	
	function __construct($appid, $notify_url, $return_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1104");
		$this->SetReturnUrl($return_url);
	}
}

class AliWap extends Payment
{
	function __construct($appid, $notify_url) {
		parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
		$this->SetPayChannel("1107");
	}
}

class Query extends Reqbase
{
	public function SetAppid($value)
    {
        $this->values['AppId'] = $value;
    }

    public function GetAppid()
    {
        return $this->values['AppId'];
    }

    public function IsAppidSet()
    {
        return array_key_exists('AppId',$this->values);
	}
	
	public function SetMerchantOrderNo($value)
	{
		$this->values['MerchantOrderNo'] = $value;
	}

    public function GetMerchantOrderNo()
    {
        return $this->values['MerchantOrderNo'];
    }

    public function IsMerchantOrderNoSet()
    {
        return array_key_exists('MerchantOrderNo',$this->values);
	}

	public function SetChannelOrderNo($value)
	{
		$this->values['ChannelOrderNo'] = $value;
	}

    public function GetChannelOrderNo()
    {
        return $this->values['ChannelOrderNo'];
    }

    public function IsChannelOrderNoSet()
    {
        return array_key_exists('ChannelOrderNo',$this->values);
	}

	public function SetReqDate($value)
    {
        $this->values['ReqDate'] = $value;
    }

    public function GetReqDate()
	{
		return $this->values['ReqDate'];
    }
    
    public function IsReqDateSet()
	{
		return array_key_exists('ReqDate', $this->values);
    }

    public function SetExtMsg($value)
    {
        $this->values['ExtMsg']= $value;
    }

    public function GetExtMsg()
    {
        return $this->values['ExtMsg'];
    }

    public function IsExtMsgSet()
    {
        return array_key_exists('ExtMsg',$this->values);
	}

	function __construct($appid) {
        $this->SetAppid($appid);
	}
}

class Close extends Reqbase
{
	public function SetAppid($value)
    {
        $this->values['AppId'] = $value;
    }

    public function GetAppid()
    {
        return $this->values['AppId'];
    }

    public function IsAppidSet()
    {
        return array_key_exists('AppId',$this->values);
	}
	
	public function SetMerchantOrderNo($value)
	{
		$this->values['MerchantOrderNo'] = $value;
	}

    public function GetMerchantOrderNo()
    {
        return $this->values['MerchantOrderNo'];
    }

    public function IsMerchantOrderNoSet()
    {
        return array_key_exists('MerchantOrderNo',$this->values);
	}

	public function SetReqDate($value)
    {
        $this->values['ReqDate'] = $value;
    }

    public function GetReqDate()
	{
		return $this->values['ReqDate'];
    }
    
    public function IsReqDateSet()
	{
		return array_key_exists('ReqDate', $this->values);
    }

    public function SetExtMsg($value)
    {
        $this->values['ExtMsg']= $value;
    }

    public function GetExtMsg()
    {
        return $this->values['ExtMsg'];
    }

    public function IsExtMsgSet()
    {
        return array_key_exists('ExtMsg',$this->values);
	}

	function __construct($appid) {
        $this->SetAppid($appid);
	}
}

class Refund extends Reqbase
{
	public function SetAppid($value)
    {
        $this->values['AppId'] = $value;
    }

    public function GetAppid()
    {
        return $this->values['AppId'];
    }

    public function IsAppidSet()
    {
        return array_key_exists('AppId',$this->values);
	}
	
	public function SetMerchantOrderNo($value)
	{
		$this->values['MerchantOrderNo'] = $value;
	}

    public function GetMerchantOrderNo()
    {
        return $this->values['MerchantOrderNo'];
    }

    public function IsMerchantOrderNoSet()
    {
        return array_key_exists('MerchantOrderNo',$this->values);
	}

	public function SetRefundMoney($value)
	{
		$this->values['RefundMoney'] = $value;
	}

    public function GetRefundMoney()
    {
        return $this->values['RefundMoney'];
    }

    public function IsRefundMoneySet()
    {
        return array_key_exists('RefundMoney',$this->values);
	}

	public function SetReqDate($value)
    {
        $this->values['ReqDate'] = $value;
    }

    public function GetReqDate()
	{
		return $this->values['ReqDate'];
    }
    
    public function IsReqDateSet()
	{
		return array_key_exists('ReqDate', $this->values);
    }

    public function SetExtMsg($value)
    {
        $this->values['ExtMsg']= $value;
    }

    public function GetExtMsg()
    {
        return $this->values['ExtMsg'];
    }

    public function IsExtMsgSet()
    {
        return array_key_exists('ExtMsg',$this->values);
	}

	function __construct($appid) {
		$this->SetAppid($appid);
	}
}

class Notify extends Reqbase
{

}

class DzPayException extends Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
}

class AliToPay extends Payment
{
    public function SetEqrc($value)
    {
        $this->values['Eqrc']=$value;
    }

    public function GetEqrc()
    {
        return $this->value['Eqrc'];
    }

    public function IsEqrcSet()
    {
        return array_key_exists('Eqrc',$this->values);
    }

    function __construct($appid, $notify_url) {
        parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
        $this->SetPayChannel("1106");
        $this->SetEqrc("False");
    }
}

class JdScan extends Payment
{
    public function SetEqrc($value)
    {
        $this->values['Eqrc']=$value;
    }

    public function GetEqrc()
    {
        return $this->value['Eqrc'];
    }

    public function IsEqrcSet()
    {
        return array_key_exists('Eqrc',$this->values);
    }

    function __construct($appid, $notify_url) {
        parent::__construct();
        $this->SetAppid($appid);
        $this->SetNotifyUrl($notify_url);
        $this->SetPayChannel("1401");
        $this->SetEqrc("False");
    }
}