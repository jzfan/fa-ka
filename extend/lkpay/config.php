<?php 
header( "content-type:text/html; charset=utf-8" );
date_default_timezone_set('PRC');
error_reporting(E_ALL & ~E_NOTICE); 

$Sparter="2079"; //商户ID
$Suserkey="2ZTnPiPziWfCYnbHrWpjRGMEIH"; //商户密钥
; //这个是网银充值比例
$bili=1;
//这个是点卡充值比例
$bili2=0.9;

$ip=""; //数据库IP
$uid=""; //数据库帐号
$upass=""; //数据库密码

//接口密钥，需要更换成你自己的密钥，要跟后台设置的一致
//登录API平台，商户管理-->接入方式-->API接入，这里查看自己的密钥和ID

//网关地址
$gateWary="http://open.la-ka.com/";

//充值结果后台通知地址，注意文件路径
//$result_url="http://".$_SERVER['SERVER_NAME']."/result_url.php";

//充值操作后用户返回到网站上的地址，注意文件路径
$result_url="";
$notify_url="http://".$_SERVER['SERVER_NAME']."/notify_Url.php";


//***************************************************



//生成订单号
function getOrderId() {
        list($usec, $sec) = explode(" ", microtime());
        $usec = substr(str_replace('0.', '', $usec), 0 ,4);
        $str  = rand(10,99);
        return date("YmdHis").$usec.$str;
}



function checkstr($str){

    $html_string = array("&amp;", "&nbsp;", "'", '"', "<", ">", "\t", "\r");

    $html_clear = array("&", " ", "&#39;", "&quot;", "&lt;", "&gt;", "&nbsp; &nbsp; ", "");

    $js_string = array("/<script(.*)<\/script>/isU");

    $js_clear = array("");

    

    $frame_string = array("/<frame(.*)>/isU", "/<\/fram(.*)>/isU", "/<iframe(.*)>/isU", "/<\/ifram(.*)>/isU",);

    $frame_clear = array("", "", "", "");

    

    $style_string = array("/<style(.*)<\/style>/isU", "/<link(.*)>/isU", "/<\/link>/isU");

    $style_clear = array("", "", "");

    

    $str = trim($str);

    //过滤字符串

    $str = str_replace($html_string, $html_clear, $str);

    //过滤JS

    $str = preg_replace($js_string, $js_clear, $str);

    //过滤ifram

    $str = preg_replace($frame_string, $frame_clear, $str);

    //过滤style

    $str = preg_replace($style_string, $style_clear, $str);

	
    return $str;

}
?>
