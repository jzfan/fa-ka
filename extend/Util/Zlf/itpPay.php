<?php
namespace Util\Zlf;
/* *
 * 功能：明天云平台支付类
 * 说明：构造支付请求函数及生成签名，支付接口表单HTML文本
 * 版本：1.0
 * 日期：2017-05-15
 */
class itpPay{
	
	var $itppay_config;
	
	//支付请求地址
	var $itppay_gateway = "https://www.itppay.com/newsdk/api/v1.0/cli/order_pc/0";

	function __construct($itppay_config){
		$this->itppay_config = $itppay_config;
	}
	
	/**
	 * 生成签名
	 * $parameter 已排序要签名的数组
	 * $moveNull 是否清除为空的参数
	 * return 签名结果字符串
	 */
	function setSignature($parameter, $moveNull=true) {
		$signature="";
		if(is_array($parameter)){
			ksort($parameter);
			foreach($parameter as $k=>$v){
				if($moveNull){
					if($v!=="" && !is_null($v)){
						$signature .= $k."=".$v."&";
					}
				}else{
					$signature .= $k."=".$v."&";
				}
			}
			if($signature){
				$signature .= "key=".$this->itppay_config["key"];
				$signature = md5($signature);
			}
		}
		
		
		return $signature;
	}
	
	/**
	 * 生成POST传递值
	 * $parameter 已排序要签名的数组
	 * return 生成的字符串
	 */
	function setPostValue($parameter) {
		$orderInfo="";
		if(is_array($parameter)){
			$parameter["signature"] = $this->setSignature($parameter);
			foreach($parameter as $k=>$v){
				if($v!=="" && !is_null($v)){
					$orderInfo[$k] = $v;
				}
			}
		}
		$orderInfo = json_encode($orderInfo,JSON_UNESCAPED_UNICODE);
		return $orderInfo;
	}
	
	/**
	 * 获取加密后的参数数据
	 * $parameter 已排序要签名的数组
	 * return 加密后的字符串
	 */
	function getOrderInfo($parameter) {
		$crypto="";
		$orderInfo = $this->setPostValue($parameter);
		//绝对路径
		//$itppay_cert = file_get_contents("D:/php/Apache24/htdocs/itppay_cert.pem");
		//相对路径 请根据项目自行更正
		$itppay_cert = file_get_contents("./cert/Zlf/itppay_cert.pem");
		$publickey = openssl_pkey_get_public($itppay_cert);
		foreach(str_split($orderInfo, 117) as $chunk){
			openssl_public_encrypt($chunk, $encryptData, $publickey);
			$crypto .= $encryptData;
		}
		$crypto = base64_encode($crypto);
		
		return $crypto;
	}
	
	/**
	 * 建立以表单形式构造
	 * $orderInfo POST传递参数值
	 * $button 提交按钮显示内容
	 * return HTML表单
	 */
	function RequestForm($orderInfo, $button){
		$html = "<form id=\"itppay_form\" name=\"itppay_form\" action=\"".$this->itppay_gateway."\" method=\"post\">";
		$html .= "<input type=\"hidden\" name=\"orderInfo\" value=\"".$orderInfo."\">";
		$html .= "<input type=\"submit\" value=\"".$button."\">";
		$html .= "</form>";
		//去除等待2秒后执行
		//$html .= "<script>setTimeout(function(){document.forms['itppay_form'].submit();}, 2000);</script>";
		$html .= "<script>document.forms['itppay_form'].submit();</script>";
		return $html;
	}
	
	/**
	 * POST提交
	 * $url 提交地址
	 * $data POST提交的数组
	 * return 字符串
	 */
	function curl($url, $data){
		
		//创建curl资源
		$curl=curl_init();
		
		//设置URL和相应的选项
		curl_setopt($curl, CURLOPT_URL, $url);
		if(!empty($data)){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		//执行curl
		$output=curl_exec($curl);
		
		//关闭并释放curl资源
		curl_close($curl);
		
		return $output;
		
	}

}

?>
