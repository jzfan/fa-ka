<?php

namespace app\common\pay;

use app\common\Wmsk;
use think\Request;

/**
 * 支付宝Wap
 * Class WmskAliWap
 * @package app\common\pay
 */
class WmskAliWap extends Wmsk
{
    /**
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return \stdClass
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        $this->gateWay = 'http://vip.wmcard.cn/intf/wapali.html';
        $this->key = $this->account->params->key;

        $this->data['customerid'] = $this->account->params->customerid;
        $this->data['sdcustomno'] = $outTradeNo;
        $this->data['orderAmount'] = $totalAmount * 100;
        $this->data['noticeurl'] = Request::instance()->domain() . '/pay/notify/WmskAliWap';
        $this->data['backurl'] = Request::instance()->domain() . '/pay/page/WmskAliWap';

        return $this->request();
    }
}