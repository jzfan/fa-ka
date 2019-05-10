<?php
namespace app\common\pay;
use think\Request;
use app\common\CodePay;

class CodePayWxScan extends CodePay
{

    public function return_url()
    {
        return Request::instance()->domain().'/pay/page/CodePayWxScan';
    }

    public function select_url()
    {
        return Request::instance()->domain().'/pay/notify/CodePayWxScan';
    }

    public function notify_callback($params,$order, $type = '')
    {
    	return parent::notify_callback($params,$order, 'CodePayWxScan');
    }
    
    public function order($outTradeNo,$subject,$totalAmount,$paytype='')
    {
    	return  parent::order($outTradeNo,$subject,$totalAmount,3);
    }
}
?>