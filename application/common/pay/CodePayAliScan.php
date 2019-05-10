<?php
namespace app\common\pay;
use think\Request;
use app\common\CodePay;

class CodePayAliScan extends CodePay
{

    public function return_url()
    {
        return Request::instance()->domain().'/pay/page/CodePayAliScan';
    }

    public function select_url()
    {
        return Request::instance()->domain().'/pay/notify/CodePayAliScan';
    }

    public function notify_callback($params,$order, $type = '')
    {
    	return parent::notify_callback($params,$order, 'CodePayAliScan');
    }
    
    public function order($outTradeNo,$subject,$totalAmount,$paytype='')
    {
    	return  parent::order($outTradeNo,$subject,$totalAmount,1);
    }
}
?>