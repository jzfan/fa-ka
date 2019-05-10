<?php

namespace app\common;

use think\Request;

class NZF extends Pay
{
    protected $gateway = 'https://api.tianniu.cc/pay/index';

    function requestForm($code, $className, $outTradeNo, $subject, $totalAmount)
    {
        $data = [
            'pay_memberid' => $this->account->params->memberid,
            'pay_appid' => $this->account->params->appid,
            'pay_submchid' => $this->account->params->submchid ? $this->account->params->submchid : 0,
            'pay_orderid' => $outTradeNo,
            'pay_applydate' => date('Y-m-d H:i:s'),
            'pay_bankcode' => $code,
            'pay_notifyurl' => Request::instance()->domain() . '/pay/notify/' . $className,
            'pay_callbackurl' => Request::instance()->domain() . '/pay/page/' . $className,
            'pay_amount' => number_format($totalAmount,2,".",""),
        ];

        $sign = $this->sign($data);

        $data['pay_md5sign'] = $sign;
        $data['pay_productname'] = $subject;
        $data['pay_productdesc'] = $subject;

        //拼装 form 表单
        $form = $this->setForm($data);

        $this->code = 0;
        $obj = new \stdClass();
        $obj->pay_url = $form;
        $obj->content_type = 3;
        return $obj;
    }

    /**
     * @param $data
     * @return string
     */
    public function setForm($data)
    {
        $html = "<form id='pay_form' class=\"form-inline\" method=\"post\" action=\"{$this->gateway}\">";
        foreach ($data as $k => $v) {
            $html .= "<input type=\"hidden\" name=\"$k\" value=\"$v\">";
        }
        $html .= "</form>";
        $html .= "<script>document.forms['pay_form'].submit();</script>";
        return $html;
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
        $signature = $params['sign'];
        $attch = $params['attach'];
        unset($params['sign']);
        unset($params['attach']);

        $sign = $this->sign($params);

        if ($sign && $sign == $signature) {

            if ($params["returncode"] == "00") {
                // 金额异常检测
                if ($order->total_price != $params['amount']) {
                    record_file_log('NZF_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['amount']}");
                    die('金额异常！');
                }

                $this->completeOrder($order);
                echo 'OK';
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
    protected function sign($params)
    {
        ksort($params);

        $keyStr = '';
        foreach ($params as $key => $val) {
            $keyStr .= "$key=$val&";
        }

        $sign = strtoupper(md5($keyStr . "key=" . $this->account->params->key));

        return $sign;
    }
}