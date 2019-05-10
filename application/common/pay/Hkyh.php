<?php

namespace app\common\pay;

use app\common\Pay;

class Hkyh extends Pay
{
    protected $gateway = 'http://pay.1000pays.com/Gateway/api';

    public function request($type, $postData)
    {
        $url = $this->gateway;

        $sign = $this->sign($postData);

        $data = [
            'appid' => $this->account->params->appid,
            'method' => $type,
            'data' => $postData,
            'sign' => $sign,
        ];

        $jsonStr = json_encode($data);

        $result = json_decode($this->curl_post($url, $jsonStr), true);
        if (!$result) {
            return [
                'code' => 500,
                'msg' => $this->error,
            ];
        } else {
            return $result;
        }
    }

    //签名
    public function sign($value)
    {
        $value = array_filter($value);
        ksort($value);
        $str = '';
        foreach ($value as $k => $v) {
            if ($k == 'sign' || $v == null || $v == '') {
                continue;
            }
            $str .= $k . '=' . $v . '&';
        }
        $str = $str . 'key=' . $this->account->params->appkey;
        $str = strtoupper(md5($str));
        return $str;
    }

    //随机字符串
    public function getRandom()
    {
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $key = "";
        for ($i = 0; $i < 16; $i++) {
            $key .= $str{
                mt_rand(0, 32)}; //生成随机数
        }
        return $key;
    }

    /**
     * 页面回调
     */
    public function page_callback($params, $order)
    {
        header("Location:" . url('/orderquery', ['orderid' => $order->trade_no]));
    }

    /**
     * 异步回调
     *
     * @param array $params
     * @param array $order
     * @return void
     */
    public function notify_callback($params, $order)
    {
        $sign = $params['sign'];
        unset($params['sign']);
        if ($this->sign($params) == $sign) {
            if ($params['status'] != 1) {
                die('订单未支付！');
            }
            // 金额异常检测
            if (0 != bccomp($order->total_price * 100, $params['total_fee'], 2)) {
                record_file_log('Hkyh_notify_error', '金额异常！' . "\r\n" . $order->trade_no .
                    "\r\n订单金额：{$order->total_price}，已支付：{$params['total_fee']} 分");
                die('金额异常！');
            }

            $this->completeOrder($order);
            record_file_log('Hkyh_notify_success', $order->trade_no);
            echo 'success';
            return true;
        } else {
            exit('FAIL');
        }
    }

    /* CURL POST*/
    public function curl_post($url, $data = array())
    {
        $curl = curl_init(); //创建一个新CURL资源 返回一个CURL句柄，出错返回 FALSE。

        //设置 Refer
        $refer = isset($this->account->params->refer) ? $this->account->params->refer : '';
        if ($refer) {
            curl_setopt($curl, CURLOPT_REFERER, $refer); //防封域名
        }

        curl_setopt($curl, CURLOPT_REFERER, $_SERVER['HTTP_HOST']); //构造来源
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); //模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300); //在发起连接前等待的时间，如果设置为0，则无限等待。
        curl_setopt($curl, CURLOPT_TIMEOUT, 300); //设置CURL允许执行的最长秒数
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //获取的信息以文件流的形式返回，而不是直接输出。
        //设置为https请求，不验证证书和hosts
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $header[] = 'ContentType:application/json;charset=UTF-8';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //用来设置HTTP头字段的数组
        curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
        curl_setopt($curl, CURLOPT_POST, true); //发送POST请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); //发送的POST数据
        curl_setopt($curl, CURLINFO_HEADER_OUT, true); //启用时追踪句柄的请求字符串
        $result = curl_exec($curl); //执行CURL
        if (curl_errno($curl)) { //检查是否有错误发生
            $this->error = 'Curl error: ' . curl_error($curl); //返回最后一次的错误号
        }
        curl_close($curl); //关闭CURL 并且释放系统资源
        return $result;
    }
}
