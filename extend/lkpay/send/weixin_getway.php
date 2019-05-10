<?php
require("../config.php");
//↓↓↓↓↓↓请求参数必填↓↓↓↓↓↓↓↓
$txtPayAccounts=checkstr($_REQUEST["txtPayAccounts"]); //游戏帐号名
$chargeMoney=checkstr($_REQUEST["chargeMoney"]); //金额
$TransID=checkstr($_REQUEST["TransID"]); //订单号
$bankid=checkstr($_REQUEST["bankid"]); //如果是网银则为银行ID，否则为通道ID
$getsign=checkstr($_REQUEST["getsign"]); //md5密钥
;
$presignurl="txtPayAccounts=".$txtPayAccounts."&ChargeMoney=".$chargeMoney."&TransID=".$TransID."&bankid=".$bankid;//因为要跳转页面，所以生成一个MD5密钥给接收数据的页面验证数据来源的安全性;
$presign=md5($presignurl.$Suserkey);

if ($presign==$getsign)
{
$P_ChannelId=$bankid;
$P_FaceValue=$chargeMoney; //面值
$P_Result_url=$result_url;
$P_Notify_url=$notify_url;
//↑↑↑↑↑↑请求参数必填↑↑↑↑↑↑↑↑
//↓↓↓↓↓↓请求参数选填，可传递空值↓↓↓↓↓↓
$P_Subject=rawurlencode(iconv("UTF-8","GB2312//IGNORE","")); //产品名称
$P_Price=checkstr($_REQUEST["P_Price"]); //产品价格
$P_Quantity=checkstr($_REQUEST["P_Quantity"]); //产品数量
$P_Description=rawurlencode(iconv("UTF-8","GB2312//IGNORE","")); //产品描述
$P_Notic=$txtPayAccounts;//自定义信息;
$P_Format=""; //输出类型，可选值：xml、json,留空则输出字符串
if ($P_FaceValue==""){
	echo "<script>alert('请输入充值金额');history.back(1);</script>";
}elseif ($P_FaceValue>"5000"){
	echo "<script>alert('单笔充值不能超过5000元');history.back(1);</script>";
}elseif ($txtPayAccounts==""){
	echo "<script>alert('请输入正确的用户信息');history.back(1);</script>";
}else{
    $P_OrderId=$TransID;
	$preEncodeStr=$Sparter.$P_OrderId.$P_CardId.$P_CardPass.$P_FaceValue.$P_ChannelId.$P_BankId.$P_Subject.$P_Description.$P_Quantity.$P_Price.$P_Format.$P_Notic.$P_Result_url.$P_Notify_url.$P_TimesTamp.$Suserkey;//拼接字符串再进行MD5加密
	$P_PostKey=strtolower(md5($preEncodeStr));//加密后的值必须为小写
	$params="P_UserId=".$Sparter;
	$params=$params."&P_OrderId=".$P_OrderId;
	$params=$params."&P_CardId=".$P_CardId;
	$params=$params."&P_CardPass=".$P_CardPass;
	$params=$params."&P_FaceValue=".$P_FaceValue;
	$params=$params."&P_ChannelId=".$P_ChannelId;
	$params=$params."&P_BankId=".$P_BankId;
	$params=$params."&P_Subject=".$P_Subject;
	$params=$params."&P_Price=".$P_Price;
	$params=$params."&P_Quantity=".$P_Quantity;
	$params=$params."&P_Description=".$P_Description;
	$params=$params."&P_Notic=".$P_Notic;
	$params=$params."&P_Format=".$P_Format;
	$params=$params."&P_Result_url=".$P_Result_url;
	$params=$params."&P_Notify_url=".$P_Notify_url;
	$params=$params."&P_WeiXinType=img";
	$params=$params."&P_PostKey=".$P_PostKey;
	//下面这句是提交到API
	//header("Location: ".$gateWary."?".$params); 
	//exit();
	$csnum=0;
  $opts = array(   
           'http'=>array(   
           'method'=>"GET",   
           'timeout'=>10, //设置超时  
        )   
  );   
  $context = stream_context_create($opts);   
  while($csnum < 3 && ($resultmsg=@file_get_contents($gateWary."?".$params,false,$context))===FALSE) $csnum++;
  $response_header=$http_response_header;
  if (is_null($response_header)){
	$API_ImgUrl="/images/error.gif"; 
	$API_Msg=mb_convert_encoding("网络超时,请稍后重新提交", "UTF-8", "gb2312");
	$API_Ico="fail";
  }else{
	$getresult_json=json_decode($resultmsg);
  	$C_Status=$getresult_json->Status;
  	$C_ImgUrl=$getresult_json->ImgUrl;
    if ($C_Status=="success")
    {
    $API_ImgUrl=$C_ImgUrl;
	$API_Msg="二维码生成成功";
	$API_Ico="success";
    }
    else
   {
    $API_ImgUrl="/images/error.gif"; 
	$API_Msg="二维码生成失败";
	$API_Ico="fail";
    } 
  }
}
}
  else
{

  print "参数提交非法";
  exit();
} 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title id="AgentSiteName">微信充值</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <link href="../images/Recharge.css" rel="stylesheet" type="text/css" />
</head>
<body>
    
<div>
</div>

<div>


</div>
    <div class="header">
        <div class="alignMid">
            <div class="pageName left" style="margin-left:0">
                微信充值</div>
            <div class="clear">
            </div>
        </div>
    </div>
    <div class="main">
        <div class="alignMid">
            <div class="content">
                <div class="conMid">
                <style>
				.img {display: table-cell;vertical-align:middle;text-align:center;*display: block;*font-size: 262px;/*约为高度的0.873，200*0.873 约为175*/*font-family:Arial;/*防止非utf-8引起的hack失效问题，如gbk编码*/width:300px;height:300px;border: 3px solid #cccccc; background:#ffffff}
				.img img {vertical-align:middle;}
				.title{margin-bottom:10px; text-indent:50px;height:50px;line-height:50px}
				.success{ background:url("/images/success.png") no-repeat;}
				.fail{ background:url("/images/fail.png") no-repeat;}
				</style>
                    <div style="margin:0 auto;width:300px">
                    	
                        <div class="order">订单号：<?=$P_OrderId?><p>订单金额：<?=$P_FaceValue?>元</p><p>充值帐号：<?=$P_Notic?></p></div>
                        <div class="<?=$API_Ico?> title"><?=$API_Msg?></div>
                    	<div class="img"><img src="<?=$API_ImgUrl?>" alt="请打开微信扫描二维码付款"/></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        版权所有 &copy;<label id="Copyright">Copyright 2011 - 2016</label>
    </div>
</body>
</html>
