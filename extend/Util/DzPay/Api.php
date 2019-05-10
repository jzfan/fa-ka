<?php
class Pay
{
    private $APIHOST='http://api.0592pay.com';
    public $error;

	public function Payment($inputObj,$timeOut = 10, $appsecret, $refer='')
	{
		try
		{
			//检测必填参数
			if(!$inputObj->IsAppidSet() || $inputObj->GetAppid()=='')
			{
				$this->error = "缺少必填参数AppId";
                return false;
			}
			else if(!$inputObj->IsProductNameSet() || $inputObj->GetProductName()=='')
			{
                $this->error = "缺少必填参数ProductName";
                return false;
			}
			else if(!$inputObj->IsProductDescriptionSet() || $inputObj->GetProductDescription()=='')
			{
                $this->error = "缺少必填参数ProductDescription";
                return false;
			}
			else if(!$inputObj->IsMerchantOrderNoSet() || $inputObj->GetMerchantOrderNo()=='')
			{
                $this->error = "缺少必填参数MerchantOrderNo";
                return false;
			}
			else if(!$inputObj->IsAmountSet() || $inputObj->GetAmount()=='')
			{
                $this->error = "缺少必填参数Amount";
                return false;
			}
			else if(!$inputObj->IsNotifyUrlSet() || $inputObj->GetNotifyUrl()=='')
			{
                $this->error = "缺少必填参数NotifyUrl";
                return false;
			}
			
			if(!$inputObj->IsPayChannelSet() || $inputObj->GetPayChannel()=='')
			{
                $this->error = "缺少必填参数PayChannel";
                return false;
			}
			switch($inputObj->GetPayChannel())
			{
				case "1102":
				case "1202":
				case "1301":
					break;
				case "1103":
				case "1205":
				case "1302":
					if(!$inputObj->IsAuthCodeSet() || $inputObj->GetAuthCode()=='')
					{
                        $this->error = "缺少必填参数AuthCode";
                        return false;
					}
					break;
				case "1203":
					if(!$inputObj->IsWechatAppIdSet() || $inputObj->GetWechatAppId()=='')
					{
                        //$this->error = "缺少必填参数WechatAppId";
                        //return false;
					}
					if(!$inputObj->IsBuyerIdSet() || $inputObj->GetBuyerId()=='')
					{
                        //$this->error = "缺少必填参数BuyerId";
                        //return false;
					}
					break;
				case "1206":
					if(!$inputObj->IsWechatAppIdSet() || $inputObj->GetWechatAppId()=='')
					{
                        $this->error = "缺少必填参数WechatAppId";
                        return false;
					}
					if(!$inputObj->IsBuyerIdSet() || $inputObj->GetBuyerId()=='')
					{
                        $this->error = "缺少必填参数BuyerId";
                        return false;
					}
					break;
				case "1201":
					if(!$inputObj->IsSceneInfoSet() || $inputObj->GetSceneInfo()=='')
					{
                        $this->error = "缺少必填参数SceneInfo";
                        return false;
					}
					break;
				case "1104":
					break;
                case "1106":
                    break;
				case "1107":
					break;
                case "1401":
                    break;
				default:
                    $this->error = "PayChannel不正确";
                    return false;
			}

			$inputObj->SetReqDate(date("YmdHis"));
			$inputObj->SetSign($appsecret);//签名
			$reqParams = $inputObj->ToUrlParams().'&Sign='.$inputObj->GetSign();
			$url = $this->APIHOST.'/Order/ToPay';
			$response = self::postCurl($reqParams, $url, $timeOut, $refer);
			$result = Respbase::Init($response);
			return $result;
		}
		catch(Exception $ex)
		{
			$result = new Respbase();
			$result->SetData("RespType","-1");
			$result->SetData("RespMessage",$ex->getMessage());

			return $result;
		}
	}

	public function Query($inputObj,$timeOut = 10,$appsecret,$refer='')
	{
		try
		{
			$url = $this->APIHOST.'/Order/Query';
			//检测必填参数
			if(!$inputObj->IsAppidSet() || $inputObj->GetAppid()=='')
			{
				throw new DzPayException("缺少必填参数AppId");
			}
			else if(!$inputObj->IsChannelOrderNoSet() && !$inputObj->IsMerchantOrderNoSet()) {
				throw new DzPayException("订单查询接口中，ChannelOrderNo、MerchantOrderNo至少填一个！");
			}
			
			$inputObj->SetAppid(DzPayConfig::APPID);
			$inputObj->SetReqDate(date("YmdHis"));
			$inputObj->SetSign($appsecret);//签名
			$reqParams = $inputObj->ToUrlParams().'&Sign='.$inputObj->GetSign();
			
			$response = self::postCurl($reqParams, $url, $timeOut, $refer);
			$result = Respbase::Init($response);

			return $result;
		 }
		 catch(Exception $ex)
		 {
			$result = new Respbase();
			$result->SetData("RespType","-1");
			$result->SetData("RespMessage",$ex->getMessage());

			return $result;
		 }
	}

	public function Close($inputObj,$timeOut = 10,$appsecret,$refer='')
	{
		try
		{
			$url = $this->APIHOST.'/Order/Close';
				//检测必填参数
			if(!$inputObj->IsAppidSet() || $inputObj->GetAppid()=='')
			{
				throw new DzPayException("缺少必填参数AppId");
			}
			else if(!$inputObj->IsMerchantOrderNoSet())
			{
				throw new DzPayException("缺少必填参数MerchantOrderNo");
			}
			
			$inputObj->SetAppid(DzPayConfig::APPID);
			$inputObj->SetReqDate(date("YmdHis"));
			$inputObj->SetSign($appsecret);//签名
			$reqParams = $inputObj->ToUrlParams().'&Sign='.$inputObj->GetSign();
			
			$response = self::postCurl($reqParams, $url, $timeOut,$refer);
			$result = Respbase::Init($response);

			return $result;
		 }
		 catch(Exception $ex)
		 {
			$result = new Respbase();
			$result->SetData("RespType","-1");
			$result->SetData("RespMessage",$ex->getMessage());

			return $result;
		 }
	}

	public function Refund($inputObj,$timeOut = 10,$appsecret,$refer='')
	{
		try
		{
			$url = $this->APIHOST.'/Order/Refund';
			//检测必填参数
			if(!$inputObj->IsAppidSet() || $inputObj->GetAppid()=='')
			{
				throw new DzPayException("缺少必填参数AppId");
			}
			else if(!$inputObj->IsMerchantOrderNoSet())
			{
				throw new DzPayException("缺少必填参数MerchantOrderNo");
			}
			else if(!$inputObj->IsRefundMoneySet())
			{
				throw new DzPayException("缺少必填参数RefundMoney");
			}
			 
			$inputObj->SetAppid(DzPayConfig::APPID);
			$inputObj->SetReqDate(date("YmdHis"));
			$inputObj->SetSign($appsecret);//签名
			$reqParams = $inputObj->ToUrlParams().'&Sign='.$inputObj->GetSign();
			
			$response = self::postCurl($reqParams, $url, $timeOut,$refer);
			$result = Respbase::Init($response);

			return $result;
		 }
		 catch(Exception $ex)
		 {
			$result = new Respbase();
			$result->SetData("RespType","-1");
			$result->SetData("RespMessage",$ex->getMessage());

			return $result;
		 }
	}

	public static function postCurl($param, $url, $second = 30, $refer)
	{		
		$ch = curl_init();
        //设置 Refer
        if($refer) {
            curl_setopt($ch, CURLOPT_REFERER, $refer); //防封域名
        }

		//设置超时
		curl_setopt($ch,CURLOPT_TIMEOUT, $second);
		curl_setopt($ch,CURLOPT_URL, $url);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else { 
			$error = curl_errno($ch);
			curl_close($ch);
			throw new Exception("curl出错，错误码:$error");
		}
	}

	public function getError(){
	    return $this->error;
    }
}