<?php

namespace app\common;

/**
 * 免签支付
 * Class Yun
 * @package app\common
 */
class Yun extends Pay
{

    protected $data = [
        'appid' => '',
        'data' => '',
        'money' => '',
        'type' => '',
        'uip' => '',
    ];

    protected $token = [
        "appid" => '',
        "data" => '',
        "money" => '',
        "type" => '',
        "uip" => '',
        "appkey" => '',
    ];

    //网关
    protected $gateWay = 'http://yunpay.waa.cn/';

    /**
     * @return bool
     */
    protected function request()
    {
        $this->token['appid'] = $this->data['appid'] = $this->account->params->appid;
        $this->token['uip'] = $this->data['uip'] = $this->getIp();
        $this->token['data'] = $this->data['data'];
        $this->token['money'] = $this->data['money'] = number_format($this->data['money'],2,".","");
        $this->token['type'] = $this->data['type'];
        $this->token['appkey'] = $this->account->params->appkey;


        $token = strtolower(md5($this->urlparams($this->token)));

        //整合数据拼接token一起发送
        $postdata = $this->urlparams($this->data) . '&token=' . $token;

        $data = $this->post($this->gateWay, $postdata);

        if (isset($data['state'])) {
            if($data['state']){
                $this->code = 0;
                $obj = new \stdClass();
                $obj->pay_url = $data["qrcode"];
                $obj->content_type = 1;
                return $obj;
            }else{
                $this->code = 500;
                $this->error = $data["text"];
                return false;
            }
        } else {
            $this->code = 500;
            $this->error = $data["msg"];
            return false;
        }
    }

    //数组拼接为url参数形式
    function urlparams($params)
    {
        $sign = '';
        foreach ($params AS $key => $val) {
            if (!is_null($val) && $val != ''){
                $sign .= "$key=$val&"; //拼接为url参数形式
            }
        }
        return trim($sign,'&');
    }

    //获取客户端IP地址
    function getIp()
    { //取IP函数
        static $realip;
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $realip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else {
                $realip = getenv('HTTP_CLIENT_IP') ? getenv('HTTP_CLIENT_IP') : getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }

    /**
     * 发送请求
     */
    protected function post($url, $data)
    {
        $curl = curl_init(); // 启动一个CURL会话
        if($this->account->params->refer){
            curl_setopt($curl, CURLOPT_REFERER, $this->account->params->refer); //防封域名
        }
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded;charset=utf-8']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        $tmpInfo = json_decode($tmpInfo, true);//将json代码转换为数组
        return $tmpInfo; // 返回数据
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
        if ($this->checkSign($params)) {
            //充值成功
            if ($params['state'] == 1) {
                //充值金额校验
                if ($params['money'] == $order['amount']) {
                    $this->completeOrder($order);
                    echo "<result>1</result>";
                    return true;
                } else {
                    record_file_log('yun_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['money']}");
                    exit('金额异常！');
                }
            } else {
                exit('支付失败！');
            }
        } else {
            exit('验签失败');
        }
    }

    /**
     * 回调验签
     * @param $params
     * @return bool
     */
    protected function checkSign($params)
    {
        $key = md5('ddh=' . $params['ddh'] . '&name=' . $params['name'] . '&money=' . $params['money'] . '&key=' . $this->account->params->appkey . '');
        if($key == $params['key']){
            return true;
        }else{
            return false;
        }
    }
}
