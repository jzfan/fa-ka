<?php

namespace app\common\pay;
use think\Request;
use app\common\PYFPay;

class PYFalipay extends PYFPay
{
    public function get_bankid()
    {
        return 'alipay';
    }

    public function get_content_type()
    {
        return 2;//2为地址
    }

    public function get_result_url()
    {
        return Request::instance()->domain().'/pay/page/PYFalipay';
    }

    public function get_notify_url()
    {
        return Request::instance()->domain().'/pay/notify/PYFalipay';
    }

    public function page_callback($params,$order, $type = '')
    {
        return parent::page_callback($params,$order, 'PYFalipay');
    }

    public function notify_callback($params,$order, $type = '')
    {
        return parent::notify_callback($params,$order, 'PYFalipay');
    }

}