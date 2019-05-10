<?php
/**
 * 拉卡微信支付
 * @author lhj
 */
namespace app\common\pay;
use think\Request;
use app\common\LkPay;

class LkBankPay extends LkPay
{
    public function get_bankid()
    {
        return $this->channel->bankid;//3为微信支付
    }

    public function get_content_type()
    {
        return 2;//2为地址
    }

    public function get_result_url()
    {
        return Request::instance()->domain().'/pay/page/LkBankPay';
    }

    public function get_notify_url()
    {
        return Request::instance()->domain().'/pay/notify/LkBankPay';
    }

    public function notify_callback($params,$order, $type = '')
    {
        return parent::notify_callback($params,$order, 'LkBankPay');
    }

}