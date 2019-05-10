<?php
/**
 * 微信扫码支付
 * @author Veris
 */
namespace app\common\pay;

use think\Db;
use think\Request;
use app\common\Pay;

class WxpayScan extends Pay{
    protected $code='';
    protected $error='';

    public function getCode()
    {
        return $this->code;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * 扫码支付
     * @param string $outTradeNo 外部单号
     * @param string $subject 标题
     * @param float $totalAmount 支付金额
     */
    public function order($outTradeNo,$subject,$totalAmount) {
        $params = [
            "trade_type"       => "NATIVE",
            'appid'            => $this->account->params->appid,
            "mch_id"           => $this->account->params->mch_id,
            "out_trade_no"     => $outTradeNo,
            "body"             => "普通消费",
            "total_fee"        => $totalAmount*100,
            "spbill_create_ip" => Request::instance()->ip(),
            "notify_url"       => Request::instance()->domain().'/pay/notify/WxpayScan',
            "nonce_str"        => random_str(),
        ];
        ksort($params);
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        $string = $buff . "&key=" .  $this->account->params->signkey;
        $string = md5($string);
        $sign = strtoupper($string);
        $params["sign"] = $sign;
        $xml = "<xml>";
        foreach ($params as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        $ch = curl_init();
        if($this->account->params->refer){
            curl_setopt($ch, CURLOPT_REFERER, $this->account->params->refer); //防封域名
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, 'https://api.mch.weixin.qq.com/pay/unifiedorder');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        curl_close($ch);
        libxml_disable_entity_loader(true);
        $dataxml = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        if ($dataxml) {
            if ($dataxml['return_code'] == 'SUCCESS') {
                $pay_url = urldecode($dataxml['code_url']);

                $this->code    =0;
                $obj           =new \stdClass();
                $obj->pay_url  =$pay_url;
                $obj->content_type = 1;
                return $obj;
            }
        }
        $this->code=1;
        $this->error=$dataxml['return_msg'];
        return false;
    }

    /**
     * 页面回调
     */
    public function page_callback($params,$order)
    {
        header("Location:" . url('/orderquery',['orderid'=>$order->trade_no]));
    }

    /**
     * 支付通知处理
     */
    public function notify_callback($params,$order) {
        if($params['out_trade_no']) {
            $buff = "";
            foreach ($params as $k => $v) {
                if ($k != "sign" && $v != "" && !is_array($v)) {
                    $buff .= $k . "=" . $v . "&";
                }
            }
            $buff = trim($buff, "&");
            $string = $buff . "&key=" . $this->account->params->signkey;
            $string = md5($string);
            $sign = strtoupper($string);
            if ($sign != $params["sign"]) {
                record_file_log('wxpay_notify_error','验签错误！'."\r\n".$order->trade_no);
                die('验签错误！');
            }
            // 金额异常检测
            $money=$params['total_fee']/100;
            if($order->total_price>$money){
                record_file_log('alipay_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$money}");
                die('金额异常！');
            }
            // TODO 这里去完成你的订单状态修改操作
            // 流水号
            $order->transaction_id =$params['transaction_id'];
            $this->completeOrder($order);
            record_file_log('wxpay_notify_success',$order->trade_no);
            echo 'success';
            return true;
        }
    }
}
