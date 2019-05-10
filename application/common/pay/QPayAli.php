<?php
namespace app\common\pay;
use think\Request;
use think\Log;
use app\common\Pay;

class QPayAli extends Pay
{

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

    public function order($outTradeNo,$subject,$totalAmount)
    {
        $params['uid'] =  $this->account->params->uid;
        $params['price'] = $totalAmount;
        $params['istype'] = 1;
        $params['notify_url'] = Request::instance()->domain().'/pay/notify/QPayAli';
        $params['return_url'] = Request::instance()->domain().'/pay/page/QPayAli';
        $params['orderid'] = $outTradeNo;
        $params['goodsname'] = $subject;
        $params['key'] = $this->generate_sign($params,$this->account->params->token);
        Log::record("QPayAli支付参数：" . json_encode($params) . " 网关：".$this->account->params->gateway, Log::INFO);
        $jsonResult = $this->curlPost($this->account->params->gateway, $params);
        $result = json_decode($jsonResult, true);
        if($result && $result['code'] == 1) {
            if(isset($result['data']['qrcode']) && $result['data']['qrcode'] != '') {
                $this->code    =0;
                $obj           =new \stdClass();
                $obj->pay_url  =$result['data']['qrcode'];
                $obj->content_type = 1;
                return $obj;
            } else {
                $this->code=201;
                $this->error = $result['msg'];
                Log::record("QPayAli支付失败：" . json_encode($result), Log::ERROR);
                return false;
            }
        } else {
            $this->code=202;
            $this->error = '支付失败';
            Log::record("QPayAli支付失败：" . json_encode($result), Log::ERROR);
            return false;
        }
    }

    /**
     * 页面回调
     */
    public function page_callback($params,$order)
    {
        header("Location:" . url('/orderquery',['orderid'=>$order->trade_no]));
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params,$order)
    {
        if(!isset($params['platform_trade_no']) || !isset($params['orderid']) || !isset($params['price']) || !isset($params['realprice'])  || !isset($params['key'])) {
            exit();
        }
        $sign = strtolower(md5($params['orderid'].$params['orderuid'].$params['platform_trade_no'].$params['price'].$params['realprice'].$this->account->params->token));
        if($sign == $params['key']) {
            // 金额异常检测
            if($order->total_price>($params['realprice'] + 0.02)){
                record_file_log('QPayAli_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$params['realprice']}");
                die('金额异常！');
            }
            // 流水号
            $order->transaction_id =$params['platform_trade_no'];
            $this->completeOrder($order);
            record_file_log('QpayAli_notify_success',$order->trade_no);
            echo 'OK';
            return true;
        } else {
            exit('验签失败');
        }
    }

    /**
     * 生成签名
     */
    private function generate_sign($params,$apikey)
    {
        $params['token'] = $apikey;
        ksort($params);
        $sign='';
        foreach ($params as  $value) {
            $sign.=$value;
        }
        $sign=strtolower(md5($sign));
        return $sign;
    }


    private function curlPost( $url, $data='', $headers=[], $agent='')
    {
        $ch = curl_init();
        if($this->account->params->refer){
            curl_setopt($ch, CURLOPT_REFERER, $this->account->params->refer); //防封域名
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        $file_contents = curl_exec($ch);
        //var_dump(curl_error($ch));
        curl_close($ch);
        return $file_contents;
    }
}
?>