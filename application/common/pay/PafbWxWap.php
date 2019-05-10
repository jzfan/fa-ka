<?php
namespace app\common\pay;

use app\common\PafbBase;
use think\Request;

class PafbWxWap extends PafbBase
{
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $data = [
            'method' => 'openapi.payment.order.scan',
        ];

        $content = [
            'type' => 1,
            'merchant_order_sn' => $outTradeNo,
            'total_fee' => $totalAmount,
            'body' => $subject,
            'call_back_url' => Request::instance()->domain() . '/pay/page/PafbWxWap',
            'store_id' => $this->account->params->store,
        ];

        $data['biz_content'] = json_encode($content);
        $result = $this->request($data);
        if ($result !== false && $result['result_code'] == '0000') {
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = $result['code_url'];
            $obj->content_type = 2;
            return $obj;
        } else {
            $this->code = 201;
            $this->error = isset($result['result_message']) ? $result['result_message'] : '支付配置错误，下单失败';
            return false;
        }
    }
}
