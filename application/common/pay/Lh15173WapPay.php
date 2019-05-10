<?php
/**
 * 漯河15173wap微信支付
 * @author lhj
 */
namespace app\common\pay;
use think\Request;
use app\common\Lh15173Pay;

class Lh15173WapPay extends Lh15173Pay
{
    public function pay_api_url()
    {
        return 'http://wx.15173.com/WechatPayInterfacewap.aspx';
    }

    public function return_url()
    {
        return Request::instance()->domain().'/pay/page/Lh15173WapPay';
    }

    public function select_url()
    {
        return Request::instance()->domain().'/pay/notify/Lh15173WapPay';
    }

    public function notify_callback($params,$order, $type = '')
    {
        return parent::notify_callback($params,$order, 'wap');
    }

}