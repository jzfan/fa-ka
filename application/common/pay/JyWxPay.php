<?php
/**
 * 点缀支付微信公众号
 *
 * @author mapeijian
 */

namespace app\common\pay;

use think\Request;
use app\common\Pay;

class JyWxPay extends Pay {

    public function order($outTradeNo, $subject, $totalAmount) {
        $apiurl      = "http://gateway.jyipay.com/online/gateway";/*接口提交地址*/
        $version     = "3.0";/*接口版本号,目前固定值为3.0*/
        $method      = "JiYi.online.interface";/*接口名称: JiYi.online.interface*/
        $partner     = $this->account->params->key;//"16964";//商户id,由API分配
        $banktype    = "WEIXIN";//银行类型 default为跳转到接口进行选择支付
        $paymoney    = $totalAmount;//单位元（人民币）,两位小数点
        $ordernumber = $outTradeNo;//商户系统订单号，该订单号将作为接口的返回数据。该值需在商户系统内唯一
        $callbackurl = Request::instance()->domain() . '/pay/notify/JyWxPay';// $_POST[txtcallbackurl];//下行异步通知的地址，需要以http://开头且没有任何参数
        $hrefbackurl = Request::instance()->domain() . "/orderquery?orderid=" . $outTradeNo;//下行同步通知过程的返回地址(在支付完成后接口将会跳转到的商户系统连接地址)。注：若提交值无该参数，或者该参数值为空，则在支付完成后，接口将不会跳转到商户系统，用户将停留在接口系统提示支付成功的页面。
        $goodsname   = $outTradeNo;//商品名称。若该值包含中文，请注意编码
        $attach      = "";//备注信息，下行中会原样返回。若该值包含中文，请注意编码
        $isshow      = "0";//该参数为支付宝扫码、微信、QQ钱包专用，默认为1，跳转到网关页面进行扫码，如设为0，则网关只返回二维码图片地址供用户自行调用
        $key         = $this->account->params->refer;//"369b7658ecf9351ac215f4d071229014";//商户Key,由API分配
        $channelcode = $this->account->params->channelcode;

        $signSource = sprintf("version=%s&method=%s&partner=%s&banktype=%s&paymoney=%s&ordernumber=%s&callbackurl=%s%s", $version, $method, $partner, $banktype, $paymoney, $ordernumber, $callbackurl, $key);
        $sign       = md5($signSource);//32位小写MD5签名值，UTF-8编码
        $data       = [
            'version'     => $version,
            'method'      => $method,
            'partner'     => $partner,
            'banktype'    => $banktype,
            'paymoney'    => $paymoney,
            'ordernumber' => $ordernumber,
            'callbackurl' => $callbackurl,
            'hrefbackurl' => $hrefbackurl,
            'goodsname'   => $goodsname,
            'attach'      => $attach,
            'isshow'      => $isshow,
            'sign'        => $sign,
            'body'        => $outTradeNo,
            'channelcode' => $channelcode,
        ];
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
//        print_r($result);
        if ($result['status'] == 1) {
            $obj               = new \stdClass();
            $obj->pay_url      = $result['qrurl'];
            $obj->content_type = 1;
            return $obj;
        } else {
            return false;
        }


    }

    /**
     * 页面回调
     */
    public function page_callback($params, $order) {
        header("Location:" . url('/orderquery', ['orderid' => $order->trade_no]));
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params, $order) {
        // $partner = "16964";//商户ID
        //  $Key = "369b7658ecf9351ac215f4d071229014";//商户KEY
        $partner     = $this->account->params->key;//"16964";//商户ID
        $Key         = $this->account->params->refer; //"369b7658ecf9351ac215f4d071229014";//商户KEY
        $orderstatus = $params["orderstatus"];
        $ordernumber = $params["ordernumber"];
        $paymoney    = $params["paymoney"];
        $sign        = $params["sign"];
        $attach      = $params["attach"];
        $signSource  = sprintf("partner=%s&ordernumber=%s&orderstatus=%s&paymoney=%s%s", $partner, $ordernumber, $orderstatus, $paymoney, $Key);
        if ($sign == md5($signSource))//签名正确
        {
            // 金额异常检测
            if ($order->total_price != $paymoney) {
                record_file_log('DzWxGzh_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$amount}");
                die('金额异常！');
            }
            // 流水号
            $order->transaction_id = $ordernumber;
            $this->completeOrder($order);
            record_file_log('JyWxWap_notify_success', $order->trade_no);
            //此处作逻辑处理
        }
        echo('ok');
        exit;
    }
}

?>