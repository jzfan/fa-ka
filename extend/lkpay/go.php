<?php
require("config.php");
$bankid = checkstr($_REQUEST["bankid"]);//如果是网银则为银行ID，否则为通道ID

switch ($bankid)
{
case "2":
	$Channelname="支付宝充值";
	$filename="alipay";
break;
case "4":
	$Channelname="支付宝WAP充值";
	$filename="alipay_wap";
break;
case "3":
	$Channelname="微信支付";
	$filename="weixin";
break;
case "30":
	$Channelname="微信WAP支付";
	$filename="weixin_wap";
break;
default:
	$Channelname="网银充值";
	$filename="wangyin";
}

$txtPayAccounts=checkstr($_REQUEST["txtPayAccounts"]);//游戏帐号名
$ChargeMoney=checkstr($_REQUEST["chargeMoney"]);//充值金额
$TransID=getOrderId();//生成订单号

$getsignurl="txtPayAccounts=".$txtPayAccounts."&ChargeMoney=".$ChargeMoney."&TransID=".$TransID."&bankid=".$bankid;//因为要跳转页面，所以生成一个MD5密钥给接收数据的页面验证数据来源的安全性
$getsign=md5($getsignurl.$Suserkey);
//订单入库操作，以及对帐号名的判断等业务代码都可以在此进行


//===========================================
?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
<title>提交充值</title>
 
  </head>

<body onLoad="document.gotopay.submit()">
<form name="gotopay" id="gotopay"  action="send/<?=$filename?>_getway.php" method="post"   target="_top">
	<input type="hidden" id="txtPayAccounts" name="txtPayAccounts" value='<?=$txtPayAccounts?>' size="50" readonly/>
	<input type="hidden" id="chargeMoney" name="chargeMoney" value='<?=$ChargeMoney?>' size="50" readonly/> 
	<input type="hidden" id="TransID" name="TransID" value='<?=$TransID?>' size="50" readonly/>
	<input type="hidden" id="bankid" name="bankid" value='<?=$bankid?>' size="50" readonly/>
	<input type="hidden" id="getsign" name="getsign" value='<?=$getsign?>' size="50" readonly/>
</form>
</body>
</html>








 