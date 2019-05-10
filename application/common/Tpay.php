<?php

namespace app\common;

/**
 * Class Tpay
 * @package app\common
 */
class Tpay extends Pay
{

    protected $gateway = "http://pay.szylyx.cn?format=json";
    protected $error = '';

    public function getError()
    {
        return $this->error;
    }

    /**
     * 页面回调
     */
    public function page_callback($params, $order)
    {
        header("Location:" . url('/orderquery', ['orderid' => $order->trade_no]));
    }

    /**
     * 下单
     * @param $data
     * @return bool|\stdClass
     * @throws \Exception
     */
    protected function request($data, $refer = '')
    {

        foreach ($data as $key => $item) {
            $this->gateway .= "&$key=$item";
        }
        $result = json_decode(postCurl($this->gateway, $data, 30, $refer), 1);

        if ($result['ret'] == 1) {
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = $result['data']['qr_code'];
            $obj->content_type = 1;
            return $obj;
        } else {
            $this->code = 500;
            $this->error = $result['message'];
            return false;
        }
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params, $order)
    {
        $signature = $params['key'];
        unset($params['key']);
        $signature_local = $this->sign($params);
        if ($signature && $signature == $signature_local) {
            // 金额异常检测
            if ($order->total_price != $params['amount']) {
                record_file_log('Topay_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['total_amount']}");
                die('金额异常！');
            }

            $this->completeOrder($order);
            echo 'success';
            return true;
        } else {
            exit('fail');
        }
    }

    /**
     * @param $params
     * @return string
     */
    protected function sign($params)
    {
        ksort($params);
        $keyStr = '';
        foreach ($params as $key => $v) {
            $keyStr .= $v;
        }
        $keyStr .= $this->account->params->merkey;
        return strtoupper(md5($keyStr));
    }


}
