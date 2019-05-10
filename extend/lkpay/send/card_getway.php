<?php 
require("../config.php");
header("Content-type: "."text/json");
// ???????????????????????????
// ???????????????+query/
// ???????P_UserId={}&P_OrderId={}&P_ChannelId={}&P_CardId={}&P_FaceValue={}&P_PostKey={?????}
// ???????????????md5???????????P_PostKey???
// ????????????????????????/query/?P_UserId={}&P_OrderId={}&P_ChannelId={}&P_CardId={}&P_FaceValue={}&P_PostKey={}
//****************************************
$Method=checkstr($_REQUEST["Method"]);
if ($Method=="GetRechargeResult")
{

  $C_OrderId=checkstr($_REQUEST["C_OrderId"]);
  $C_ChannelId=checkstr($_REQUEST["C_ChannelId"]);
  $C_CardId=checkstr($_REQUEST["C_CardId"]);
  $C_FaceValue=checkstr($_REQUEST["C_FaceValue"]);
  $C_Format="json"; //??????????????xml??json,?????????????
  $postkey=$Sparter.$C_OrderId.$C_CardId.$C_FaceValue.$C_ChannelId.$C_Format.$Suserkey;
  $postkey=md5($postkey);
  $params="P_UserId=".$Sparter;
  $params=$params."&P_OrderId=".$C_OrderId;
  $params=$params."&P_CardId=".$C_CardId;
  $params=$params."&P_FaceValue=".$C_FaceValue;
  $params=$params."&P_ChannelId=".$C_ChannelId;
  $params=$params."&P_Format=".$C_Format;
  $strurl=$params."&P_PostKey=".$postkey;
  $strurl=$gateWary."query/?".$strurl;
  $resultmsg=curl_get_contents($strurl); //????API????????????????????
  $getresult_json=json_decode($resultmsg);
  $J_Code=$getresult_json->P_ErrCode;
  $J_ErrMsg=$getresult_json->P_ErrMsg;
  $J_PayMoney=$getresult_json->P_PayMoney;
// ???????????????????????????????????????????¡Â???xml??json?????????????????
  if ($J_Code=="0")//????????????§Ø?????????)
  {
	$API_Code="fail";
	$API_Msg=mb_convert_encoding("???????§µ??????...", "UTF-8", "gb2312"); 
  }
    elseif ($J_Code=="1000")//????==1000$??????????????)
  {
    $API_Code="success";
    $API_Msg=mb_convert_encoding("???,?????????".$J_PayMoney."", "UTF-8", "gb2312");
  }
    else
  {
    $API_Code="error";
    $API_Msg=mb_convert_encoding(rawurldecode($J_ErrMsg), "UTF-8", "gb2312"); 
  } 


?>{"Status":{"Code":"<?=$API_Code?>","Msg":"<?=$API_Msg?>","Orderid":"<?=$C_OrderId?>"}}
<?php
exit();
}
?>
<? 
$P_CardId=checkstr($_REQUEST["cardId"]);
$P_CardPass=checkstr($_REQUEST["cardPass"]);
$P_FaceValue=checkstr($_REQUEST["FaceValue"]);
$P_ChannelId=checkstr($_REQUEST["ChannelId"]);
$P_Subject="gamepay"; //checkstr("subject")
$P_Price=checkstr($_REQUEST["FaceValue"]);

if ($P_CardId=="")
{
  $API_Msg="????????";
  $API_Code="error";
}
elseif ($P_CardPass=="")
{
  $API_Msg="??????????";
  $API_Code="error";
}
elseif ($P_FaceValue=="")
{
  $API_Msg="?????????";
  $API_Code="error";
}
elseif ($P_ChannelId=="")
{
  $API_Msg="???????";
  $API_Code="error";
}
  else
{

  $txtPayAccounts=checkstr($_REQUEST["txtPayAccounts"]); //????????
  $TransID=getOrderId();
  $P_Quantity="1";
  $P_Format="json"; //??????????????xml??json,?????????????
  $P_BandId="";
  $P_Notic=rawurlencode($txtPayAccounts);//????????
  $P_Result_url=$result_url;
  $P_Notify_url=$notify_url;
  $P_Subject="";
  $P_Description="";
//?????????????????????????§Ø??????????????????






//===========================================
  if ($P_Price=="")
  {
	$P_Price=$P_FaceValue;
  } 

  $preEncodeStr="".$Sparter."".$TransID."".$P_CardId."".$P_CardPass."".$P_FaceValue."".$P_ChannelId."".$P_BandId."".$P_Subject."".$P_Description."".$P_Quantity."".$P_Price."".$P_Format."".$P_Notic."".$P_Result_url."".$P_Notify_url."".$Suserkey.""; //?????????????MD5????
  $P_PostKey=md5($preEncodeStr);
  $params="P_UserId=".$Sparter;
  $params=$params."&P_OrderId=".$TransID;
  $params=$params."&P_CardId=".$P_CardId;
  $params=$params."&P_CardPass=".$P_CardPass;
  $params=$params."&P_FaceValue=".$P_FaceValue;
  $params=$params."&P_ChannelId=".$P_ChannelId;
  $params=$params."&P_Subject=".$P_Subject;
  $params=$params."&P_Price=".$P_Price;
  $params=$params."&P_Quantity=".$P_Quantity;
  $params=$params."&P_Description=".$P_Description;
  $params=$params."&P_Notic=".$P_Notic;
  $params=$params."&P_Format=".$P_Format;
  $params=$params."&P_Result_url=".$P_Result_url;
  $params=$params."&P_Notify_url=".$P_Notify_url;
  $params=$params."&P_PostKey=".$P_PostKey;
//?????????????API
  $csnum=0;
  $opts = array(   
           'http'=>array(   
           'method'=>"GET",   
           'timeout'=>10, //???¨®??  
        )   
  );   
  //echo $params;
  $context = stream_context_create($opts);  
  //while($csnum < 3 && ($resultmsg=@file_get_contents($gateWary."?".$params,false,$context))===FALSE) $csnum++;
  //$response_header=$http_response_header;
  $resultmsg = curl_get_contents($gateWary."?".$params);
  //echo $resultmsg;
  if (is_null($resultmsg)){
	$API_Msg=mb_convert_encoding("???¼^?,???????????", "UTF-8", "gb2312");
  	$API_Code="error";
  }else{
	$getresult_json=json_decode($resultmsg);
  	$C_Code=$getresult_json->P_ErrCode;
  	$C_ErrMsg=$getresult_json->P_ErrMsg;
	//???????????????C_Code??§Ø???????????????
    if ($C_Code=="0")
    {
    $API_Msg=mb_convert_encoding("???????§µ??????...", "UTF-8", "gb2312");
    $API_Code="fail";
    }
    else
   {
    $API_Msg=$C_ErrMsg; 
    $API_Code="error";
    } 
  }
}

function curl_get_contents($url,$timeout=5) { 
$curlHandle = curl_init(); 
curl_setopt( $curlHandle , CURLOPT_URL, $url ); 
curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 ); 
curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout ); 
$result = curl_exec( $curlHandle ); 
curl_close( $curlHandle ); 
return $result; 
} 
//????????§Ø?;
?>{"Status":{"Code":"<?=$API_Code?>","Msg":"<?=$API_Msg?>","Orderid":"<?=$TransID?>"}}