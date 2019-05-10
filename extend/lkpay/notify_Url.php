<?php
require("config.php"); 
$UserId=checkstr($_REQUEST["P_UserId"]);
$SysOrderId=checkstr($_REQUEST["P_SysOrderId"]);
$OrderId=checkstr($_REQUEST["P_OrderId"]);
$CardId=checkstr($_REQUEST["P_CardId"]);
$CardPass=checkstr($_REQUEST["P_CardPass"]);
$FaceValue=checkstr($_REQUEST["P_FaceValue"]);
$PayMoney=checkstr($_REQUEST["P_PayMoney"]);
$SubMoney=checkstr($_REQUEST["P_SubMoney"]);
$ChannelId=checkstr($_REQUEST["P_ChannelId"]);
$BankId=checkstr($_REQUEST["P_BankId"]);
$Subject=checkstr($_REQUEST["P_Subject"]);
$Description=checkstr($_REQUEST["P_Description"]);
$ChannelId=checkstr($_REQUEST["P_ChannelId"]);
$Quantity=checkstr($_REQUEST["P_Quantity"]);
$Price=checkstr($_REQUEST["P_Price"]);
$Notic=encodereplace(iconv("GB2312","UTF-8//IGNORE", rawurldecode(checkstr($_REQUEST["P_Notic"]))));
$B_Result_Url=checkstr($_REQUEST["P_Result_Url"]);
$B_Notify_Url=checkstr($_REQUEST["P_Notify_Url"]);
$ErrCode=checkstr($_REQUEST["P_ErrCode"]);
$ErrMsg=checkstr($_REQUEST["P_ErrMsg"]);
$TimesTamp=checkstr($_REQUEST["P_TimesTamp"]);
$PostKey=checkstr($_REQUEST["P_PostKey"]);

$preEncodeStr=$Sparter.$SysOrderId.$OrderId.$CardId.$CardPass.$FaceValue.$PayMoney.$SubMoney.$ChannelId.$BankId.$Subject.$description.$Quantity.$Price.rawurlencode(iconv("UTF-8","GB2312//IGNORE",$Notic)).$B_Result_Url.$B_Notify_Url.$ErrCode.$TimesTamp.$Suserkey;

echo $preEncodeStr;

$encodeStr=md5($preEncodeStr);

if ($PostKey==$encodeStr)
{
	if ($ErrCode=="1000"){//ErrCode为1000订单成功 
		print "支付成功";
	}else{//支付失败
		print "支付失败";
	} 
}else{
	print "数据被篡改";

}
?>