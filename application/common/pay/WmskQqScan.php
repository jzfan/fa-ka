<?php

namespace app\common\pay;

use app\common\Wmsk;
use think\Request;

/**
 * QQ扫码
 * Class WmskQqScan
 * @package app\common\pay
 */
class WmskQqScan extends Wmsk
{
    /**
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return \stdClass
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->gateWay = 'http://vip.wmcard.cn/intf/spay.html';
        $this->key = $this->account->params->key;

        $this->data['customerid'] = $this->account->params->customerid;
        $this->data['sdcustomno'] = $outTradeNo;
        $this->data['orderAmount'] = $totalAmount * 100;
        $this->data['noticeurl'] = Request::instance()->domain() . '/pay/notify/WmskQqScan';
        $this->data['backurl'] = Request::instance()->domain() . '/pay/page/WmskQqScan';

        return $this->request();
    }
}