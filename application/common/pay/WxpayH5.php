<?php
/**
 * 微信H5支付
 * @author mapeijian
 */
namespace app\common\pay;

use think\Db;
use think\Request;
use app\common\Pay;

class WxpayH5 extends Pay{
    protected $code='';
    protected $error='';

    public function getCode()
    {
        return $this->code;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * 支付
     * @param string $outTradeNo 外部单号
     * @param string $subject 标题
     * @param float $totalAmount 支付金额
     */
    public function order($outTradeNo,$subject,$totalAmount) {

        $params = [
            'appid' => $this->account->params->appid,
            'mch_id' => $this->account->params->mch_id,
            'body' => $subject,
            'out_trade_no' => $outTradeNo,
            'total_fee' => $totalAmount*100,
            'notify_url' => Request::instance()->domain().'/pay/notify/WxpayH5',
        ];

        $result = \wxpay\WapPay::getPayUrl($params, Request::instance()->domain().'/orderquery?orderid='.$outTradeNo, $this->account->params->signkey);

        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$result;
        $obj->content_type = 2;
        return $obj;
    }

    /**
     * 支付同步通知处理
     */
    public function page_callback($params,$order) {
        header("Location:" . url('/orderquery',['orderid'=>$order->trade_no]));
    }

    /**
     * 支付异步通知处理
     */
    public function notify_callback($params,$order) {
        if($params['out_trade_no']) {
            $buff = "";
            foreach ($params as $k => $v) {
                if ($k != "sign" && $v != "" && !is_array($v)) {
                    $buff .= $k . "=" . $v . "&";
                }
            }
            $buff = trim($buff, "&");
            $string = $buff . "&key=" . $this->account->params->signkey;
            $string = md5($string);
            $sign = strtoupper($string);
            if ($sign != $params["sign"]) {
                record_file_log('wxpay_notify_error','验签错误！'."\r\n".$order->trade_no);
                die('验签错误！');
            }
            // 金额异常检测
            $money=$params['total_fee']/100;
            if($order->total_price>$money){
                record_file_log('alipay_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$money}");
                die('金额异常！');
            }
            // TODO 这里去完成你的订单状态修改操作
            // 流水号
            $order->transaction_id =$params['transaction_id'];
            $this->completeOrder($order);
            record_file_log('wxpay_notify_success',$order->trade_no);
            echo 'success';
            return true;
        }
    }
}
