<?php

namespace app\common\pay;

class HkyhAliScan extends Hkyh
{
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $data = [
            'store_id' => $this->account->params->store, //多个门店可选传，如不传系统默认已创建最早的门店为主
            'total' => $totalAmount * 100, //总金额 单位:分
            'nonce_str' => $this->getRandom(), //随机字符串 字符范围a-zA-Z0-9
            'out_trade_no' => $outTradeNo, //订单号
            'body' => $outTradeNo, //商品名称
        ];
        $res = parent::request('ali_native', $data);
        if ($res['code'] == 100 && $res['data']['result_code'] == '0000') {
            //跳转支付
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = $res['data']['code_url'];
            $obj->content_type = 1;
            return $obj;
        } else {
            $this->code = 500;
            $this->error = $res['msg'];
            return false;
        }
    }
}
