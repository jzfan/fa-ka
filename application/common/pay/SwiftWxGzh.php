<?php

namespace app\common\pay;

use app\common\Swift;
use think\Request;

/**
 * Class SwiftWxGzh
 * @package app\common\pay
 */
class SwiftWxGzh extends Swift
{

    /**
     * 下单
     * @param $outTradeNo
     * @param $subject
     * @param $totalAmount
     * @return bool|\stdClass|string
     * @throws \Exception
     */
    public function order($outTradeNo, $subject, $totalAmount)
    {
        if(!$this->account->params->sub_appid || !$this->account->params->sub_appsecret) {
            $this->error = '请先配置微信公众号AppId和AppSecret';
            return false;
        }
        $redirect_uri = urlencode(Request::instance()->domain().'/index/pay/wx_js_api_call');
        $pay_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->account->params->sub_appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=".$outTradeNo."#wechat_redirect";
        if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $this->code    =0;
            $obj           =new \stdClass();
            $obj->pay_url  =$pay_url;
            $obj->content_type = 2;
            return $obj;
        } else {
            $this->code    =0;
            $obj           =new \stdClass();
            $obj->pay_url  =url('index/pay/wx_jspay_page').'?trade_no=' . $outTradeNo . '&url='.base64_encode($pay_url);
            $obj->content_type = 2;
            return $obj;
        }
    }

    /*
     * 获取用户微信openid并发起微信公众号支付
     */
    public function js_api_call($code, $order)
    {
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->account->params->sub_appid."&secret=".$this->account->params->sub_appsecret."&code=".$code."&grant_type=authorization_code");
        $array = json_decode($weixin, true);
        $openid = isset($array['openid']) ? $array['openid'] : '';
        if(!$openid) {
            exit('获取微信openid失败');
        }
        $paramter = [
            'out_trade_no' => $order['trade_no'],
            'body' => $order['trade_no'],
            'total_fee' => $order['total_price'] * 100,
            'mch_create_ip' => $this->getAddress(),
            'is_raw' => 1,
            'sub_openid' => $openid,
            'sub_appid' => $this->account->params->sub_appid,
        ];

        $notify = Request::instance()->domain() . '/pay/notify/SwiftWxGzh';
        $result = parent::request('pay.weixin.jspay', $paramter, $notify);

        if ($result['status'] == 0 && $result['result_code'] == 0) {
            $json = $result['pay_info'];
            $url =  url('/orderquery', ['orderid' => $order['trade_no']]);
            ?>
            <html>
            <head>
                <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1"/>
                <title>微信支付</title>
                <script type="text/javascript">
                    function jsApiCall() {
                        WeixinJSBridge.invoke(
                            'getBrandWCPayRequest',
                            <?php echo $json; ?>,
                            function (res) {
                                astr = res.err_msg;
                                if (astr.indexOf("ok") > 0) {
                                    window.location.href = "<?php echo $url; ?>";
                                }

                            }
                        );
                    }
                    function callpay() {
                        if (typeof WeixinJSBridge == "undefined") {
                            if (document.addEventListener) {
                                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                            } else if (document.attachEvent) {
                                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                            }
                        } else {
                            jsApiCall();
                        }
                    }
                    callpay();
                </script>
            </head>
            <body>
            </body>
            </html>
            <?php
        } else {
            if ($result['status'] != 0) {
                $error = isset($result['message']) ? $result['message'] : '';
            } else {
                $error = isset($result['err_msg']) ? $result['err_msg'] : '';
            }
            $this->error = '获取支付信息失败!' . $error;
            exit( $this->error);
        }
    }
}
