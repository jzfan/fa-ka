<?php

namespace app\common\pay;

use Think\Request;

class HkyhWxGzh extends Hkyh
{
    public function order($outTradeNo, $subject, $totalAmount)
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $openid = '';

            if ($this->account->params->usePublic) {
                $openid = session('openid');
            }

            $data = [
                'store_id' => $this->account->params->store, //多个门店可选传，如不传系统默认已创建最早的门店为主
                'total' => $totalAmount * 100, //总金额 单位:分
                'nonce_str' => $this->getRandom(), //随机字符串 字符范围a-zA-Z0-9
                'openid' => $openid, //如果不传openid属于原生JS支付
                'body' => $outTradeNo, //商品名称
                'out_trade_no' => $outTradeNo, //订单号
                'return_url' => Request::instance()->domain() . '/orderquery?orderid=' . $outTradeNo, //支付完成后跳转地址
            ];
            $res = parent::request('wx_jsapi', $data);
            if ($res['code'] == 100 && $res['data']['result_code'] == '0000') {
                if ($openid) {
                    $this->code = 0;
                    $obj = new \stdClass();
                    $obj->pay_url = json_encode([
                        'appId' => $res['data']['code_url'],
                        'timeStamp' => $res['data']['timeStamp'],
                        'nonceStr' => $res['data']['nonceStr'],
                        'package' => $res['data']['package'],
                        'signType' => $res['data']['signType'],
                        'paySign' => $res['data']['paySign'],
                    ]);
                    $obj->content_type = 5;
                    return $obj;
                } else {
                    //跳转支付
                    $this->code = 0;
                    $obj = new \stdClass();
                    $obj->pay_url = $res['data']['code_url'];
                    $obj->content_type = 2;
                    return $obj;
                }
            } else {
                $this->code = 500;
                $this->error = $res['msg'];
                return false;
            }
        } else {
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = url('index/pay/wx_jspay_page') . '?trade_no=' . $outTradeNo . '&url=' .
            base64_encode(Request::instance()->domain() . '/index/pay/payment?trade_no=' . $outTradeNo);
            $obj->content_type = 2;
            return $obj;
        }
    }
}
