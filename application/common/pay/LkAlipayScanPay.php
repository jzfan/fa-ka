<?php
/**
 * 拉卡微信支付
 * @author lhj
 */
namespace app\common\pay;
use think\Request;
use app\common\LkPay;

class LkAlipayScanPay extends LkPay
{
    public function get_bankid()
    {
        return 2;//2为支付宝支付
    }

    public function get_content_type()
    {
        return 1;//1为扫码支付
    }

    public function get_result_url()
    {
        return Request::instance()->domain().'/pay/page/LkAlipayScanPay';
    }

    public function get_notify_url()
    {
        return Request::instance()->domain().'/pay/notify/LkAlipayScanPay';
    }

    public function notify_callback($params,$order, $type = '')
    {
        return parent::notify_callback($params,$order, 'AlipayScanPay');
    }

}