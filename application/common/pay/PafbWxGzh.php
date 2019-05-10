<?php
namespace app\common\pay;

use app\common\PafbBase;

class PafbWxGzh extends PafbBase
{
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $data = [
            'method' => 'openapi.payment.auth.auth',
        ];

        $content = [
            'url' => Request::instance()->domain() . '/pay/auth/' . $outTradeNo,
            'store_id' => $this->account->params->store,
        ];

        $data['biz_content'] = json_encode($content);
        $result = $this->request($data);
        var_dump($result);
    }

    public function realOrder($openid, $subOpenid, $outTradeNo, $subject, $totalAmount)
    {
        $data = [
            'method' => 'openapi.payment.order.h5pay',
        ];

        $content = [
            'merchant_order_sn' => $outTradeNo,
            'open_id' => $openid,
            'sub_open_id' => $subOpenid,
            'total_fee' => $totalAmount,
            'store_id' => $this->account->params->store,
            'body' => $subject,
            'call_back_url' => Request::instance()->domain() . '/pay/notify/PafbWxGzh',
        ];

        $data['biz_content'] = json_encode($content);
        $result = $this->request($data);
        var_dump($result);

        $url = 'https://shq-api.51fubei.com/paypage?prepay_id=' . $result['data']['prepay_id'];
        $url .= '&callback_url=' . Request::instance()->domain() . '/pay/page/PafbWxScan';
        $url .= '&cancel_url=' . Request::instance()->domain() . '/pay/page/PafbWxScan';

        header('location:' . $url);
        exit;
    }
}
