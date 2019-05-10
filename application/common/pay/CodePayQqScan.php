<?php
namespace app\common\pay;
use think\Request;
use app\common\CodePay;

class CodePayQqScan extends CodePay
{

    public function return_url()
    {
        return Request::instance()->domain().'/pay/page/CodePayQqScan';
    }

    public function select_url()
    {
        return Request::instance()->domain().'/pay/notify/CodePayQqScan';
    }

    public function notify_callback($params,$order, $type = '')
    {
    	return parent::notify_callback($params,$order, 'CodePayQqScan');
    }
    
    public function order($outTradeNo,$subject,$totalAmount,$paytype='')
    {
    	return  parent::order($outTradeNo,$subject,$totalAmount,2);
    }
}
?>