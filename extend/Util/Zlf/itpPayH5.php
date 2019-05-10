<?php
namespace Util\Zlf;
/* *
 * 功能：掌灵支付类
 * 说明：构造支付请求函数及生成签名，支付接口表单HTML文本
 * 版本：1.0
 * 日期：2017-05-15
 */
class itpPayH5{
	
	var $itppay_config;
	
	//支付请求地址
	var $itppay_gateway = "https://trans.palmf.cn/sdk/api/v1.0/cli/order_h5/0";

	function __construct($itppay_config){
		$this->itppay_config = $itppay_config;
	}
	
	/**
	 * 生成签名
	 * $parameter 已排序要签名的数组
	 * $moveNull 是否清除为空的参数
	 * return 签名结果字符串
	 * php语言切记值为0的时候也要参与拼接
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
		//$orderInfo="";
		$orderInfo=array();
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

        $html .= "<script>setTimeout(function(){document.forms['itppay_form'].submit();}, 2000);</script>";

        return $html;
    }

    /**
	 * 建立微信支付表单
     * @param $orderInfo
     * @param string $trade_no
     * @return string
     */
	function RequestWxForm($orderInfo, $trade_no = ''){
		$html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
			.'<meta http-equiv="X-UA-Compatible" content="ie=edge"><title>Document</title></head><body>'
			.'<style>*{margin:0;padding:0;font-size:16px;word-wrap: break-word;}.title{background-color: #d9edf6;padding:.7rem;text-align: center;}'
			.'.blue{color:#5f8394;}.mt10{margin-top:10px;}.mb10{margin-bottom: 10px;}.pl10{padding: 10px}.pr10{padding: 10px}</style>'
			.'<div style="margin: 0 .7rem; border: 2px solid #51b4ea;"><div class="title blue mb10">请保存二维码图片打开微信，从相册选择并支付</div>'
			.'<div style="text-align: center;"><img src="'. generate_qrcode_link(url('index/pay/payment',['trade_no' => $trade_no]))
			.'" width="260px" height="260px" style="margin:1rem auto;"></div>'
			.'<div class="title blue mt10 mb10">或 <button style="color:#dcb653;background: none;border: none;" data-clipboard-action="copy" data-clipboard-target="#url" id="copy_btn">点击复制</button> 以下链接到微信打开</div><div class="mb10 mt10 pl10 pr10"> '
			.'<p class="blue" id="url">' . url('index/pay/payment',['trade_no' => $trade_no]) . '</p></div>'
			.'<div style="border-top:1px solid #bbb;border-bottom:1px solid #bbb;padding:.7rem .7rem;margin:.7rem 0;">'
			.'提示：你可以将以上链接发到自己微信的聊天框（在微信内顶部搜索框可以搜到自己的微信），即可进入支付</div><div class="mb10" style="text-align: center;">'
			.'支付成功自动跳转查看结果</div></div><form style="display:none;" id="itppay_form" name="itppay_form" action="'.$this->itppay_gateway.'" method="post">'
			.'<input type="hidden" name="orderInfo" value="'. $orderInfo .'"><input type="submit" value="前往支付"></form>'
			.'<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>'
            .'<script src="https://cdn.bootcss.com/clipboard.js/1.6.1/clipboard.min.js"></script>'
            .'<script>$(function(){var clipboard = new Clipboard("#copy_btn");clipboard.on("error", function(e) { alert("当前浏览器不支持此功能，请手动复制。")});clipboard.on("success", function(e) {alert("复制成功",1500);e.clearSelection();});var ua = window.navigator.userAgent.toLowerCase();if(ua.match(/MicroMessenger/i) == "micromessenger"){document.forms["itppay_form"].submit();}})</script>'
			.'</body></html>';
		
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
