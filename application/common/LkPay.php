<?php
/**
 * 拉卡支付
 * 扫码支付
 * @author lhj
 */

namespace app\common;

use think\Request;
use app\common\Pay;
use service\HttpService;

abstract class LkPay extends Pay
{
    protected $code = '';
    protected $error = '';
    protected $api_url = "http://open.la-ka.com/";

    public function getCode()
    {
        return $this->code;
    }

    public function getError()
    {
        return $this->error;
    }

    public function order($outTradeNo, $subject, $totalAmount)
    {
        $pay_url = '';
        $config = $this->get_pay_config($outTradeNo, $subject, $totalAmount);
        $params = $this->buildParams($config);
        $url = $this->buildUrl($params);

        //银行卡
        if (isset($this->channel->bankid)) {
            $pay_url = $url;
        }

        if (!$pay_url) {
            //请求获取二维码
            $response = HttpService::get($url, [], 30, [], $this->account->params->refer);
            $response = \json_decode($response);
            if (isset($response->Msg) && $response->Status == 'error') {
                //超市
                $this->code = 1;
                $this->error = $response->Msg;
                return false;
            } else {
                //$response = \json_decode($response);
                if (isset($response->P_ErrMsg)) {
                    $this->code = 1;
                    $this->error = $response->P_ErrMsg;
                    return false;
                }
                $C_Status = isset($response->Status) ? $response->Status : '';
                if ($C_Status == "success") {
                    $pay_url = $response->ImgUrl;
                    parse_str($pay_url, $query);
                    if (isset($query['url'])) {
                        $pay_url = $query['url'];
                    }
                } else {
                    $this->code = 1;
                    $this->error = '获取二维码失败';
                    return false;
                }
            }
        }

        $this->code = 0;
        $obj = new \stdClass();
        $obj->pay_url = $pay_url;
        $obj->content_type = $this->get_content_type();
        return $obj;
    }

    /**
     * 获取支付的配置
     *
     * @param String $outTradeNo
     * @param String $subject
     * @param Float $totalAmount
     * @return Array
     */
    public function get_pay_config($outTradeNo, $subject, $totalAmount)
    {
        $config = [
            'P_UserId' => $this->account->params->userid,//必填 商户ID
            'P_OrderId' => $outTradeNo,//必填，在商户系统中保持唯一 商户订单号
            'P_CardId' => '',
            'P_CardPass' => '',
            'P_FaceValue' => $totalAmount,//必填 提交金额
            'P_ChannelId' => isset($this->channel->bankid) ? 1 : $this->get_bankid(),//必填 通道ID 3为微信支付
            'P_BankId' => $this->get_bankid(),//
            //'P_Subject'     => urlencode($subject),
            'P_Description' => '',
            'P_Quantity' => 1,//数量
            'P_Price' => $totalAmount,
            'P_Format' => 'json',//返回数据格式json xml ,默认是字符串
            'P_Notic' => '',
            'P_Result_Url' => $this->get_result_url(),
            'P_Notify_Url' => $this->get_notify_url(),//必填,必须带上http: //或https: //，且不能含有问号
            'P_TimesTamp' => '',
            'userkey' => $this->account->params->userkey,
        ];

        return $config;
    }

    /**
     * 页面回调
     */
    public function page_callback($params, $order)
    {
        header("Location:" . Request::instance()->domain() . '/orderquery?orderid=' . $order->trade_no);
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params, $order, $type = '')
    {
        $UserId = $this->checkstr($params["P_UserId"]);
        $SysOrderId = $this->checkstr($params["P_SysOrderId"]);
        $OrderId = $this->checkstr($params["P_OrderId"]);
        $CardId = $this->checkstr($params["P_CardId"]);
        $CardPass = $this->checkstr($params["P_CardPass"]);
        $FaceValue = $this->checkstr($params["P_FaceValue"]);
        $PayMoney = $this->checkstr($params["P_PayMoney"]);
        $SubMoney = $this->checkstr($params["P_SubMoney"]);
        $ChannelId = $this->checkstr($params["P_ChannelId"]);
        $BankId = $this->checkstr($params["P_BankId"]);
        $Subject = $this->checkstr($params["P_Subject"]);
        $Description = $this->checkstr($params["P_Description"]);
        $ChannelId = $this->checkstr($params["P_ChannelId"]);
        $Quantity = $this->checkstr($params["P_Quantity"]);
        $Price = $this->checkstr($params["P_Price"]);
        $Notic = iconv("GB2312", "UTF-8//IGNORE", rawurldecode($this->checkstr($params["P_Notic"])));
        $B_Result_Url = $this->checkstr($params["P_Result_Url"]);
        $B_Notify_Url = $this->checkstr($params["P_Notify_Url"]);
        $ErrCode = $this->checkstr($params["P_ErrCode"]);
        $ErrMsg = $this->checkstr($params["P_ErrMsg"]);
        $TimesTamp = $this->checkstr($params["P_TimesTamp"]);
        $PostKey = $this->checkstr($params["P_PostKey"]);
        $Sparter = $this->account->params->userid;
        $Suserkey = $this->account->params->userkey;
        $description = '';

        $preEncodeStr = $Sparter . $SysOrderId . $OrderId . $CardId . $CardPass . $FaceValue . $PayMoney . $SubMoney . $ChannelId . $BankId . $Subject . $description . $Quantity . $Price . rawurlencode(iconv("UTF-8", "GB2312//IGNORE", $Notic)) . $B_Result_Url . $B_Notify_Url . $ErrCode . $TimesTamp . $Suserkey;


        $encodeStr = md5($preEncodeStr);

        if ($PostKey == $encodeStr) {
            if ($ErrCode == "1000") {//ErrCode为1000订单成功
                if ($Price != $order->total_price) {
                    record_file_log('LkPay' . $type . 'pay_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$Price}");
                    die('金额异常！');
                }

                $order->transaction_id = $SysOrderId;
                $this->completeOrder($order);
                record_file_log('LkPay' . $type . 'pay_notify_success', $order->trade_no);
                echo "success";
                return true;
            } else {//支付失败
                record_file_log('LkPay' . $type . 'pay_notify_error', '支付失败！' . "\r\n" . $order->trade_no);
                die('支付失败！');
            }
        } else {
            record_file_log('LkPay' . $type . 'pay_notify_error', '验签错误！' . "\r\n" . $order->trade_no);
            die('验签失败！');
        }
    }

    public function buildParams($config)
    {
        //签名顺序
        /*md5(
            P_UserId+
            P_OrderId+
            P_CardId+
            P_CardPass+
            P_FaceValue+
            P_ChannelId+
            P_BankId+
            P_Subject+
            P_Description+
            P_Quantity+
            P_Price+
            P_Format+
            P_Notic+
            P_Result_Url+
            P_Notify_Url+
            P_TimesTamp+
            UserKey
         ) */
        extract($config);
        $preEncodeStr = $P_UserId . $P_OrderId . $P_CardId . $P_CardPass . $P_FaceValue . $P_ChannelId . $P_BankId . $P_Description . $P_Quantity . $P_Price . $P_Format . $P_Notic . $P_Result_Url . $P_Notify_Url . $P_TimesTamp . $userkey;//拼接字符串再进行MD5加密

        $P_PostKey = strtolower(md5($preEncodeStr));//加密后的值必须为小写

        unset($config['userkey']);

        $config['P_WeiXinType'] = 'img';
        $config['P_PostKey'] = $P_PostKey;

        return $config;
    }

    /**
     * 获取url
     *
     * @param Array $params
     * @return String
     */
    public function buildUrl($params)
    {
        $query = '';
        foreach ($params as $key => $value) {
            $query .= $key . "=" . $value . "&";
        }

        $query = trim($query, '&');

        return $this->api_url . '?' . $query;
    }

    protected function checkstr($str)
    {

        $html_string = array("&amp;", "&nbsp;", "'", '"', "<", ">", "\t", "\r");

        $html_clear = array("&", " ", "&#39;", "&quot;", "&lt;", "&gt;", "&nbsp; &nbsp; ", "");

        $js_string = array("/<script(.*)<\/script>/isU");

        $js_clear = array("");


        $frame_string = array("/<frame(.*)>/isU", "/<\/fram(.*)>/isU", "/<iframe(.*)>/isU", "/<\/ifram(.*)>/isU",);

        $frame_clear = array("", "", "", "");


        $style_string = array("/<style(.*)<\/style>/isU", "/<link(.*)>/isU", "/<\/link>/isU");

        $style_clear = array("", "", "");


        $str = trim($str);

        //过滤字符串

        $str = str_replace($html_string, $html_clear, $str);

        //过滤JS

        $str = preg_replace($js_string, $js_clear, $str);

        //过滤ifram

        $str = preg_replace($frame_string, $frame_clear, $str);

        //过滤style

        $str = preg_replace($style_string, $style_clear, $str);


        return $str;

    }

    abstract public function get_bankid();

    abstract public function get_content_type();

    abstract public function get_result_url();

    abstract public function get_notify_url();
}