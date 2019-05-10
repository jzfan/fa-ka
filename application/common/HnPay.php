<?php

namespace app\common;

class HnPay extends Pay
{

    protected $gateway = "http://api.918pay.com/";
    protected $error = "";

    public function getError()
    {
        return $this->error;
    }

    /**
     * 页面回调
     * @param $params
     * @param $order
     */
    public function page_callback($params, $order)
    {
        header("Location:" . url('/orderquery', ['orderid' => $order->trade_no]));
    }

    /**
     * 支付回调
     * @param $params
     * @param $order
     * @return bool
     */
    public function notify_callback($params, $order)
    {
        $postSign = $params['sign'];
        unset($params['sign']);
        $localSign = self::sign($params, $this->account->params->key);
        if ($postSign == $localSign) {
            // 金额异常检测
            if ($order->total_price != $params['total_amount']) {
                record_file_log('HnPay_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['total_amount']}");
                die('金额异常！');
            }

            if ($params["pay_status"] == 10) {
                $this->completeOrder($order);
                echo('SUCCESS');
                return true;
            }
        }
        echo('FAIL');
        exit;
    }

    /**
     * 签名
     * @param $params
     * @return string
     */
    static public function sign($params, $key)
    {
        ksort($params);
        $stringToBeSigned = "";
        foreach ($params as $k => $v) {
            $stringToBeSigned .= "$k=$v&";
        }
        $stringToBeSigned .= "key=$key";
        unset ($k, $v);
        return md5($stringToBeSigned);
    }

    protected function request($requestUrl, $data, $refer = '')
    {
        $ch = curl_init();
        if($refer) {
            curl_setopt($ch, CURLOPT_REFERER, $refer); //防封域名
        }
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($output, true);
        return $res;

    }
}