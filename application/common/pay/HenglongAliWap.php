<?php
/**
 * 恒隆支付宝WAP
 * @author mapeijian
 */
namespace app\common\pay;
use think\Request;
use app\common\Pay;
class HenglongAliWap extends Pay
{

    protected $gateway = 'http://957faka.cn/Pay_Index.html';
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
        $params['pay_memberid'] = $this->account->params->memberid;
        $params['pay_orderid'] = $outTradeNo;
        $params['pay_applydate'] = date('Y-m-d H:i:s');
        $params['pay_bankcode'] = '904';
        $params['pay_notifyurl'] = Request::instance()->domain().'/pay/notify/HenglongAliWap';
        $params['pay_callbackurl'] = Request::instance()->domain().'/pay/page/HenglongAliWap';
        $params['pay_amount'] = number_format($totalAmount,2,'.','');
        $params['pay_productname'] = $subject;
        $params['pay_md5sign'] = $this->sign($params, $this->account->params->key);
        $response = postCurl($this->gateway, $params);
        if(!$response) {
            $this->code    =201;
            $this->error = '调用接口失败';
            return false;
        }
        $response = json_decode($response, true);
        if($response['status'] == '200') {
            $this->code    =0;
            $obj           =new \stdClass();
            $obj->pay_url  =$response['data']['QRCodeUrl'];
            $obj->content_type = 2;
            return $obj;
        } else {
            $this->code    =201;
            $this->error = isset($response['msg']) ? $response['msg'] : '支付失败';
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
        $signature = $params['sign'];
        $attch = $params['attach'];
        unset($params['sign']);
        unset($params['attach']);
        $sign = $this->sign($params, $this->account->params->key);
        if ($sign && $sign == $signature) {
            if ($params["returncode"] == "00") {
                // 金额异常检测
                if ($order->total_price != $params['amount']) {
                    record_file_log('HenglongAliWap_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['amount']}");
                    die('金额异常！');
                }
                $this->completeOrder($order);
                echo 'ok';
                return true;
            } else {
                exit('fail');
            }
        }
    }

    /**
     * @param $params
     * @return string
     */
    protected function sign($params, $apikey)
    {
        ksort($params);
        $keyStr = '';
        foreach ($params as $key => $val) {
            $keyStr .= "$key=$val&";
        }
        $sign = strtoupper(md5($keyStr . "key=" . $apikey));
        return $sign;
    }
}
?>