<?php 
require("../config.php");
//��������������������������������������
$txtPayAccounts=checkstr($_REQUEST["txtPayAccounts"]); //��Ϸ�ʺ���
$chargeMoney=checkstr($_REQUEST["chargeMoney"]); //���
$TransID=checkstr($_REQUEST["TransID"]); //������
$bankid=checkstr($_REQUEST["bankid"]); //�����������Ϊ����ID������Ϊͨ��ID
$getsign=checkstr($_REQUEST["getsign"]); //md5��Կ
;
$presignurl="txtPayAccounts=".$txtPayAccounts."&ChargeMoney=".$chargeMoney."&TransID=".$TransID."&bankid=".$bankid;//��ΪҪ��תҳ�棬��������һ��MD5��Կ���������ݵ�ҳ����֤������Դ�İ�ȫ��;
$presign=md5($presignurl.$Suserkey);

if ($presign==$getsign)
{
$P_ChannelId="1";
$P_BankId=$bankid;
$P_FaceValue=$chargeMoney; //��ֵ
$P_Result_url=$result_url;
$P_Notify_url=$notify_url;
//��������������������������������������
//�������������������ѡ��ɴ��ݿ�ֵ������������
$P_Subject=rawurlencode(checkstr($_REQUEST["P_Subject"])); //��Ʒ����
$P_Price=checkstr($_REQUEST["P_Price"]); //��Ʒ�۸�
$P_Quantity=checkstr($_REQUEST["P_Quantity"]); //��Ʒ����
$P_Description=rawurlencode(checkstr($_REQUEST["P_Description"])); //��Ʒ����
$P_Notic=$txtPayAccounts;//�Զ�����Ϣ;
$P_Format=""; //������ͣ���ѡֵ��xml��json,���������ַ���
if ($P_FaceValue==""){
	echo "<script>alert('�������ֵ���');history.back(1);</script>";
}elseif ($P_FaceValue>"50000"){
	echo "<script>alert('���ʳ�ֵ���ܳ���50900Ԫ');history.back(1);</script>";
}elseif ($txtPayAccounts==""){
	echo "<script>alert('��������ȷ���û���Ϣ');history.back(1);</script>";
}else{
	$P_OrderId=$TransID;
	$preEncodeStr=$Sparter.$P_OrderId.$P_CardId.$P_CardPass.$P_FaceValue.$P_ChannelId.$P_BankId.$P_Subject.$P_Description.$P_Quantity.$P_Price.$P_Format.$P_Notic.$P_Result_url.$P_Notify_url.$P_TimesTamp.$Suserkey;//ƴ���ַ����ٽ���MD5����
	$P_PostKey=strtolower(md5($preEncodeStr));//���ܺ��ֵ����ΪСд
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
	$params=$params."&P_PostKey=".$P_PostKey;
	//����������ύ��API
	header("Location: ".$gateWary."?".$params); 
}
}
  else
{

  print "�����ύ�Ƿ�";
} 

?>
