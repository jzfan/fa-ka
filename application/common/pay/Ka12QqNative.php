<?php
namespace app\common\pay;
use think\Request;
use app\common\Ka12Pay;

class Ka12QqNative extends Ka12Pay
{

    public function return_url()
    {
        return Request::instance()->domain().'/pay/page/Ka12QqNative';
    }

    public function select_url()
    {
        return Request::instance()->domain().'/pay/notify/Ka12QqNative';
    }

    public function notify_callback($params,$order, $type = '')
    {
    	return parent::notify_callback($params,$order, 'Ka12QqNative');
    }
    
    public function order($outTradeNo,$subject,$totalAmount,$paytype='')
    {
    	return parent::order($outTradeNo,$subject,$totalAmount,'Qqwallet');
    }
}
?>