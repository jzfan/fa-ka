<?php

namespace app\common;

use think\Request;

require_once ROOT_PATH . 'extend/Alipay/AopSdk.php';
require_once ROOT_PATH . 'extend/Alipay/aop/XmlseclibsAdapter.php';

class Wsyh extends Pay
{

    /**
     * 真实下单接口
     */
    public function realOrder($ChannelType, $outTradeNo, $subject, $totalAmount)
    {
        if (bccomp(0, $totalAmount) === 1) {
            exit('请输入正确的金额');
        }
        $Appid = $this->account->params->Appid;
        $function = 'ant.mybank.bkmerchanttrade.prePay';
        $ReqTime = date('YmdHis', time());
        $ReqMsgId = $this->getuuid();
        $OutTradeNo = $outTradeNo;
        $Body = $subject;
        $TotalAmount = $totalAmount * 100;
        $Currency = 'CNY';
        $MerchantId = $this->account->params->merchantid;
        $IsvOrgId = $this->account->params->IsvOrgId;
        $DeviceCreateIp = $this->get_client_ip();
        $SettleType = $this->account->params->settleType;
        $ProviderType = $this->account->params->ProviderType;
        if ($ChannelType == 'WX') {
            $NotifyUrl = Request::instance()->domain() . '/pay/notify/WsyhWxScan'; //异步回调地址
            $SuccessUrl = Request::instance()->domain() . '/pay/page/WsyhWxScan'; //同步回调地址
            $signA = "<document><request id='request'><head><Version>1.0.0</Version><Appid>$Appid</Appid><Function>$function</Function><ReqTime>$ReqTime</ReqTime><ReqTimeZone>UTC+8</ReqTimeZone><ReqMsgId>$ReqMsgId</ReqMsgId><InputCharset>UTF-8</InputCharset><ProviderType>$ProviderType</ProviderType></head><body><IsvOrgId>$IsvOrgId</IsvOrgId><OutTradeNo>$OutTradeNo</OutTradeNo><Body>$Body</Body><TotalAmount>$TotalAmount</TotalAmount><Currency>$Currency</Currency><MerchantId>$MerchantId</MerchantId><ChannelType>$ChannelType</ChannelType><NotifyUrl>$NotifyUrl</NotifyUrl><SuccessUrl>$SuccessUrl</SuccessUrl><DeviceCreateIp>$DeviceCreateIp</DeviceCreateIp><SettleType>$SettleType</SettleType></body></request></document>";
        } elseif ($ChannelType == 'ALI') {
            $NotifyUrl = Request::instance()->domain() . '/pay/notify/WsyhAliScan'; //异步回调地址
            $SuccessUrl = Request::instance()->domain() . '/pay/page/WsyhAliScan'; //同步回调地址
            $signA = "<document><request id='request'><head><Version>1.0.0</Version><Appid>$Appid</Appid><Function>$function</Function><ReqTime>$ReqTime</ReqTime><ReqTimeZone>UTC+8</ReqTimeZone><ReqMsgId>$ReqMsgId</ReqMsgId><InputCharset>UTF-8</InputCharset><ProviderType>$ProviderType</ProviderType></head><body><IsvOrgId>$IsvOrgId</IsvOrgId><OutTradeNo>$OutTradeNo</OutTradeNo><Body>$Body</Body><TotalAmount>$TotalAmount</TotalAmount><Currency>$Currency</Currency><MerchantId>$MerchantId</MerchantId><ChannelType>$ChannelType</ChannelType><NotifyUrl>$NotifyUrl</NotifyUrl><SuccessUrl>$SuccessUrl</SuccessUrl><DeviceCreateIp>$DeviceCreateIp</DeviceCreateIp><SettleType>$SettleType</SettleType></body></request></document>";
        }
        $url = 'http://openapi.huilianpay.com/pay/mybank'; //正式
        //$url = 'http://huilian.51vip.biz/pay/mybank'; //浙商本地
        //$url = 'http://test.huilianpay.com/pay/mybank';
        $dataxml = $this->postXmlws($signA, $url); //后台 POST 传参地址
        libxml_disable_entity_loader(true);
        $objectxml = (array) simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = json_encode($objectxml);
        $jsonArray = json_decode($jsonStr, true);
        return $jsonArray;
    }

    /*
    生成UUID
     */

    private function getuuid()
    {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4) . $hyphen
        . substr($charid, 20, 12);
        return $uuid;
    }

    /**
     * 页面回调
     */
    public function page_callback($params, $order)
    {
        header("Location:" . url('/orderquery', ['orderid' => $order->trade_no]));
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params, $order)
    {
        if ($params['request']['body']['RespInfo']['ResultStatus'] == 'S' && $params['request']['body']['RespInfo']['ResultCode'] == '0000') {
            //充值金额校验
            if (bccomp($params['request']['body']['TotalAmount'], ($order->total_price * 100)) === 0) {
                $this->completeOrder($order);
                //给网商回调
                $Appid = $this->account->params->Appid;
                $function = 'ant.mybank.bkmerchanttrade.prePayNotice';
                $RespTime = date('YmdHis', time());
                $ReqMsgId = $this->getuuid();
                $RespTimeZone = 'UTC+8';
                //$InputCharset = 'UTF-8';
                $ResultStatus = 'S';
                $ResultCode = 0000;
                $ResultMsg = '成功';
                $signA = "<document><response id=\"response\"><head><Version>1.0.0</Version><Appid>$Appid</Appid><Function>$function</Function><RespTime>$RespTime</RespTime><RespTimeZone>$RespTimeZone</RespTimeZone><ReqMsgId>$ReqMsgId</ReqMsgId><InputCharset>UTF-8</InputCharset><SignType>RSA</SignType></head><body><RespInfo><ResultStatus>$ResultStatus</ResultStatus><ResultCode>$ResultCode</ResultCode><ResultMsg>$ResultMsg</ResultMsg></RespInfo></body></response></document>";
                $pass_key = $this->account->params->private_key;
                $pass_key = chunk_split($pass_key, 64, "\n");
                $private_key = "-----BEGIN RSA PRIVATE KEY-----\n$pass_key-----END RSA PRIVATE KEY-----\n";
                $xmlTool = new \XmlseclibsAdapter();
                $xmlTool->setPrivateKey($private_key);
                $xmlTool->addTransform(\XmlseclibsAdapter::ENVELOPED);
                $xmlDocument = new DOMDocument();
                $xmlDocument->loadXML(trim($signA)); //把请求主体载入
                $xmlTool->sign($xmlDocument);
                $post_data = $xmlDocument->saveXML();
                echo $post_data;

                return true;
            } else {
                record_file_log('wsyh_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['request']['body']['TotalAmount']}");
                exit('<xml><RespInfo>FAIL</RespInfo></xml>');
            }
        }else{
            record_file_log('wsyh_notify_error', '支付失败！' . "\r\n" . $order->trade_no);
            exit('<xml><RespInfo>FAIL</RespInfo></xml>');
        }
    }

    public function postXmlws($xml, $url, $second = 30)
    {
        $pass_key = $this->account->params->private_key;
        $pass_key = chunk_split($pass_key, 64, "\n");
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n$pass_key-----END RSA PRIVATE KEY-----\n";
        $xmlTool = new \XmlseclibsAdapter();
        $xmlTool->setPrivateKey($private_key);
        $xmlTool->addTransform(\XmlseclibsAdapter::ENVELOPED);
        $xmlDocument = new \DOMDocument();
        $xmlDocument->loadXML(trim($xml)); //把请求主体载入
        $xmlTool->sign($xmlDocument);
        $post_data = $xmlDocument->saveXML();
        $header = array(
            "Content-Type: application/xml;charset=UTF-8", //设置请求头  请求的内容格式，编码
        );
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //设置 header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //post 提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置header
        //运行 curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_error($ch);
            curl_close($ch);
            echo "curl 出错，错误码:$error" . "<br>";
        }
    }
    public function get_client_ip()
    {
        //$ip = 'unknown';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }
}
