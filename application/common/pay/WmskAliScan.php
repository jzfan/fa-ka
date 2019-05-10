<?php

namespace app\common\pay;

use app\common\Wmsk;
use think\Request;

/**
 * 支付宝扫码
 * Class WmskAliScan
 * @package app\common\pay
 */
class WmskAliScan extends Wmsk
{
    /**
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return \stdClass
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->gateWay = 'http://vip.wmcard.cn/intf/wapzpay.html';
        $this->key = $this->account->params->key;

        $this->data['customerid'] = $this->account->params->customerid;
        $this->data['sdcustomno'] = $outTradeNo;
        $this->data['orderAmount'] = $totalAmount * 100;
        $this->data['noticeurl'] = Request::instance()->domain() . '/pay/notify/WmskAliScan';
        $this->data['backurl'] = Request::instance()->domain() . '/pay/page/WmskAliScan';

        return $this->request();
    }
}