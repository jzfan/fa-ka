<?php
namespace app\common\pay;
use think\Request;
use app\common\Ka12Pay;

class Ka12QqWap extends Ka12Pay
{

    public function return_url()
    {
        return Request::instance()->domain().'/pay/page/Ka12QqWap';
    }

    public function select_url()
    {
        return Request::instance()->domain().'/pay/notify/Ka12QqWap';
    }

    public function notify_callback($params,$order, $type = '')
    {
    	return parent::notify_callback($params,$order, 'Ka12QqWap');
    }
    
    public function order($outTradeNo,$subject,$totalAmount,$paytype='')
    {
    	return  parent::order($outTradeNo,$subject,$totalAmount,'Qqwap');
    }
}
?>