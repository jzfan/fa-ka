<?php

namespace app\common;

/**
 * 完美数卡综合支付渠道
 * Class Wmsk
 * @package app\common
 */
class Wmsk extends Pay
{

    protected $data = [
        'customerid' => '',
        'sdcustomno' => '',
        'orderAmount' => '',
        'cardno' => 32,
        'noticeurl' => '',
        'backurl' => '',
    ];

    //秘钥
    protected $key = '';

    //网关
    protected $gateWay = '';

    /**
     * 获取参数字符串
     * @return string
     */
    protected function getParamStr()
    {
        $signStr = '';
        foreach ($this->data as $key => $v) {
            $signStr .= "$key=$v&";
        }
        $signStr = trim($signStr, '&');
        return $signStr;
    }

    /**
     * 签名
     */
    protected function sign()
    {
        $signStr = $this->getParamStr() . $this->key;
        $sign = strtoupper(md5($signStr));
        return $sign;
    }

    /**
     * 发送请求
     */
    protected function request()
    {
        $this->gateWay;
        $sign = $this->sign();
        $params = $this->getParamStr() . "&sign=$sign";

        $this->code = 0;
        $obj = new \stdClass();
        $obj->pay_url = $this->gateWay . '?' . $params;
        $obj->content_type = 2;
        return $obj;
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
                if ($params['ordermoney'] == $order['total_price']) {
                    $this->completeOrder($order);
                    echo "<result>1</result>";
                    return true;
                } else {
                    record_file_log('wmsk_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['ordermoney']}");
                    exit('金额异常！');
                }
            } else {
                record_file_log('wmsk_notify_error', '支付失败！' . "\r\n" . $order->trade_no);
                exit('支付失败！');
            }
        } else {
            record_file_log('wmsk_notify_error', '验签失败！' . "\r\n" . $order->trade_no);
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
        $originSign = $params['resign'];
        $signStr = "sign={$params['sign']}&customerid={$params['customerid']}&ordermoney={$params['ordermoney']}&sd51no={$params['sd51no']}&state={$params['state']}&key=" . $this->account->params->key;
        return $originSign == strtoupper(md5($signStr));
    }
}
