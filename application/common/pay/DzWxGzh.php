<?php
/**
 * 点缀支付微信公众号
 * @author mapeijian
 */
namespace app\common\pay;
use think\Request;
use app\common\Pay;
require_once (EXTEND_PATH.'Util/DzPay/Data.php');
require_once (EXTEND_PATH.'Util/DzPay/Api.php');
class DzWxGzh extends Pay
{

    protected $gateway = '';
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

    public function order($outTradeNo,$subject,$totalAmount)
    {
        $appid = $this->account->params->appid;
        $notify_url = Request::instance()->domain().'/pay/notify/DzWxGzh';
        $return_url = Request::instance()->domain().'/pay/page/DzWxGzh?MerchantOrderNo='.$outTradeNo;
        $input = new \WXPublic($appid, $notify_url);
        $input->SetProductName($subject);
        $input->SetProductDescription($subject);
        $input->SetAmount($totalAmount);
        $input->SetMerchantOrderNo($outTradeNo);
        $input->SetReturnUrl($return_url);
        if(isset($this->account->params->usepublic) && !empty($this->account->params->usepublic)) {
            $input->SetPromptView("0");
            $input->SetWechatAppId(sysconf('wechat_appid'));
            $input->SetBuyerId(session('openid'));
        }else{
            $input->SetPromptView("1");
            $input->SetWechatAppId('');
            $input->SetBuyerId('');
        }
        $pay = new \Pay();
        $result = $pay->Payment($input,10, $this->account->params->appsecret, $this->account->params->refer);
        if($result) {
            if($result->GetSuccess($this->account->params->appsecret)) {
                if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
                    if(isset($this->account->params->usepublic) && !empty($this->account->params->usepublic)) {
                        $this->code = 0;
                        $obj = new \stdClass();
                        $obj->pay_url = $result->GetData("ToPayData");
                        $obj->content_type = 5;
                        return $obj;
                    }else{
                        $this->code = 0;
                        $obj = new \stdClass();
                        $obj->pay_url = $result->GetData("ToPayData");
                        $obj->content_type = 2;
                        return $obj;
                    }
                } else {
                    $this->code    =0;
                    $obj           =new \stdClass();
                    $obj->pay_url  =url('index/pay/wx_jspay_page') . '?trade_no=' . $outTradeNo . '&url=' . base64_encode($result->GetData("ToPayData"));
                    $obj->content_type = 2;
                    return $obj;
                }

            } else  {
                $this->code    =201;
                $this->error = $result->GetData("RespMessage");
                return false;
            }
        } else {
            $this->code    =202;
            $this->error = $pay->error;
            return false;
        }


    }

    /**
     * 页面回调
     */
    public function page_callback($params,$order)
    {
        header("Location:" . url('/orderquery',['orderid'=>$order->trade_no]));
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params,$order)
    {
        $result = \Respbase::InitFromArray($params);
        if($result->GetSuccess($this->account->params->appsecret,false)) {
            $platformOrderNo = $result->GetData("PlatformOrderNo");//平台单号
            $merchantOrderNo = $result->GetData("MerchantOrderNo");//商户单号
            $amount = $result->GetData("Amount");//订单金额
            $status = $result->GetData("Status");//订单状态
            // 金额异常检测
            if($order->total_price!=$amount){
                record_file_log('DzWxGzh_notify_error','金额异常！'."\r\n".$order->trade_no."\r\n订单金额：{$order->total_price}，已支付：{$amount}");
                die('金额异常！');
            }
            // 流水号
            $order->transaction_id =$platformOrderNo;
            $this->completeOrder($order);
            record_file_log('DzWxGzh_notify_success',$order->trade_no);
            echo 'ok';
            return true;
        }
        else {}
            exit('fail');
        }

}
?>