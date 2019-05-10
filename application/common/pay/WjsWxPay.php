<?php
/**
 * 微极速微信支付
 * @author mapeijian
 */
namespace app\common\pay;

use think\Db;
use think\Request;
use app\common\Pay;

require_once ROOT_PATH . 'extend/wjspay/lib/epay_submit.class.php';
require_once ROOT_PATH . 'extend/wjspay/lib/epay_notify.class.php';

class WjsWxPay extends Pay{
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

    public function getConfig()
    {
        $config = [
            'partner' => $this->account->params->partner,
            'key' => $this->account->params->key,
            'sign_type' => strtoupper('MD5'),
            'input_charset' => strtoupper('utf-8'),
            'transport' => strtoupper('http'),
            'apiurl' => 'https://pay.v8jisu.cn/',
        ];

        return $config;
    }
    /**
     * 支付
     * @param string $outTradeNo 外部单号
     * @param string $subject 标题
     * @param float $totalAmount 支付金额
     */
    public function order($outTradeNo,$subject,$totalAmount) {
        $notify_url = "http://xxx.xxx/notify_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = "http://xxx.xxx/return_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        
        $config = $this->getConfig();
        $parameter = array(
            "pid" => trim($config['partner']),
            "type" => 'wxpay',
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "out_trade_no"	=> $outTradeNo,
            "name"	=> $subject,
            "money"	=> $totalAmount,
            "sitename"	=> $subject
        );

        $alipaySubmit = new \AlipaySubmit($config);
        $result = $alipaySubmit->buildRequestForm($parameter);

        $this->code    =0;
        $obj           =new \stdClass();
        $obj->pay_url  =$result;
        $obj->content_type = 3;
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

        $config = $this->getConfig();
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //彩虹易支付交易号
            $trade_no = $_GET['trade_no'];
            //交易状态
            $trade_status = $_GET['trade_status'];
            //支付方式
            $type = $_GET['type'];


            if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                    //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                    //如果有做过处理，不执行商户的业务程序
                        
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知

            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
                
            echo "success";		//请不要修改或删除
            return true;
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            record_file_log('wxpay_notify_error','验签错误！'."\r\n".$order->trade_no);
            //验证失败
            echo "fail";
        }
    }
}
