<?php

namespace app\common;

/**
 * 优畅上海
 * Class YcshPay
 * @package app\common
 * @doc http://docs.uline.cc/#uline
 */
class YcshPay extends Pay
{

    protected $nonce_str = '3c3d78b13ba865fb5617f021aca9f9d8';

    protected $gateway = 'http://mapi.bosc.uline.cc';

    protected $error = '';

    /**
     * 页面回调
     */
    public function page_callback($params, $order)
    {
        header("Location:" . url('/orderquery', ['orderid' => $order->trade_no]));
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params, $order)
    {
        if ($params['return_code'] == 'SUCCESS' && $params['result_code'] == 'SUCCESS') {
            //验证签名
            if ($params['sign']) {
                if ($this->checkSign($params)) {
                    //验证金额
                    if (0 == bccomp($order->total_price * 100, $params['total_fee'], 2)) {
                        $this->completeOrder($order);
                        echo 'success';
                        return true;
                    } else {
                        record_file_log('YcshPay_notify_error', '金额异常！' . "\r\n" . $params['total_fee'] . "\r\n" . $order->total_price * 100 . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}");
                        die('金额异常！');
                    }
                } else {
                    record_file_log('YcshPay_notify_error', '签名异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}");
                    die('签名异常！');
                }
            }
        } else {
            record_file_log('YcshPay_notify_error', '支付业务返回异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}");
            die('支付业务返回异常！');
        }
    }

    /**
     * 发送请求
     *
     * @param $url
     * @param $param
     * @return array|mixed|object
     * @throws \Exception
     */
    protected function curlPost($url, $param)
    {
        $xml = $this->getQueryString($param);

        $result = xmlToArray(postCurl($url, $xml, 30, $this->account->params->refer));

        return $result;
    }

    /**
     * @param $param
     * @return string
     */
    protected function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param $param
     * @return string
     */
    protected function getQueryString($param)
    {
        $sign = $this->getSign($param);
        $param['sign'] = $sign;

        ksort($param, SORT_ASC);

        return arrayToXml($param);
    }

    /**
     * 签名
     * @param $param
     * @return string
     */
    protected function getSign($param)
    {
        $sign = '';
        if (is_array($param)) {
            $orderInfo = '';
            ksort($param, SORT_ASC);
            foreach ($param as $k => $v) {
                if (!(empty($v) && $v != 0) && !is_null($v)) {
                    $orderInfo .= "$k=$v&";
                }
            }

            $orderInfo .= 'key=' . $this->account->params->appsecret;

            $sign = strtoupper(md5(trim($orderInfo, "&")));
        }

        return $sign;
    }

    /**
     * 获取客户端地址
     * @return null|string
     */
    protected function getAddress()
    {
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'REMOTE_ADDR'] as $header) {
            if (!isset($_SERVER[$header]) || ($spoof = $_SERVER[$header]) === null) {
                continue;
            }
            sscanf($spoof, '%[^,]', $spoof);
            if (!filter_var($spoof, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $spoof = null;
            } else {
                return $spoof;
            }
        }
        return '0.0.0.0';
    }

    /**
     * 获取支付信息
     * @param $result
     * @return bool
     */
    protected function getPayUrl($result)
    {
        if ($result['return_code']) {
            if ($result['result_code']) {
                switch ($result['trade_type']) {
                    case 'JSAPI':
                        return $result['js_prepay_info'];
                    case 'NATIVE':
                        return $result['code_url'];
                    case 'APP':
                        return $result['app_prepay_info'];
                    case 'WMWEB':
                        return $result['mweb_url'];
                    default:
                        return false;
                }
            }
        }
        return false;
    }

    /**
     * 验签
     *
     * @param $params
     * @return bool
     */
    protected function checkSign($params)
    {

        if ($params['sign']) {
            $sign = $params['sign'];

            unset($params['sign']);
            $localSign = $this->getSign($params);

            if ($sign == $localSign) {
                return true;
            }
        }

        return false;
    }
}