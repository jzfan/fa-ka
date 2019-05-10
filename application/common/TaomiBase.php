<?php
/**
 * 支付宝支付
 */

namespace app\common;

use think\Request;

/**
 * Class TaomiBase
 * @notice 所有传入参数必须为字符串类型
 * @package app\common\pay
 */
class TaomiBase extends Pay
{
    // 支付结果
    const PAY_SUCCESS = 1;
    const PAY_NOTPAY = 0;
    const PAY_FAIL = 2;
    const PAY_REFUND = 3;

    // 回调地址
    protected $request_url = 'https://gateway.taomipay.com/gateway/pay';

    //支付参数
    protected $version = '1.0'; //  版本号
    protected $mch_id = ''; //    商户号
    protected $pay_type = ''; //    通道类型
    protected $fee_type = 'CNY'; //    货币类型
    protected $total_amount = ''; //    订单金额
    protected $out_trade_no = ''; //    商户订单号
    protected $device_info = ''; //    设备号
    protected $notify_url = ''; //    通知地址
    protected $body = ''; //    商品描述
    protected $attach = ''; //    附加信息
    protected $time_start = ''; //    订单生成时间
    protected $time_expire = ''; //    订单失效时间
    protected $limit_credit_pay = '0'; //    支付方式限制
    protected $hb_fq_num = ''; //    花呗分期
    protected $hb_fq_percent = ''; //    手续费承担方
    protected $key = ''; // key
    protected $minipg = '0'; //是否小程序支付
    protected $openid = ''; //openid， 只有公众号支付需要
    protected $appid = ''; //appid， 只有微信公众号支付需要

    protected $code = '';
    protected $error = '';

    public function getCode()
    {
        return $this->code;
    }

    public function getError()
    {
        return $this->error;
    }

    public function __construct()
    {

    }

    protected function setAccountInfo()
    {
        $this->mch_id = $this->account->params->mch_id;
        $this->key = $this->account->params->key;

        $this->appid = sysconf('wechat_appid') ? sysconf('wechat_appid') : '';
        $this->openid = session('openid') ? session('openid') : '';
    }

    /**
     * 参数签名
     * @return string
     */
    protected function sign()
    {
        $this->setAccountInfo();
        $signSource = sprintf("version={$this->version}&mch_id={$this->mch_id}&pay_type={$this->pay_type}&total_amount={$this->total_amount}&out_trade_no={$this->out_trade_no}&notify_url={$this->notify_url}&key={$this->key}");
        $sign = md5($signSource);
        return $sign;
    }

    /**
     * 请求参数封装
     * @return array
     */
    protected function getPostData()
    {
        $this->setAccountInfo();
        $data = [
            'version' => $this->version,
            'minipg' => $this->minipg,
            'mch_id' => $this->mch_id,
            'pay_type' => $this->pay_type,
            'fee_type' => $this->fee_type,
            'total_amount' => $this->total_amount,
            'out_trade_no' => $this->out_trade_no,
            'device_info' => $this->device_info,
            'notify_url' => $this->notify_url,
            'body' => $this->body,
            'attach' => $this->attach,
            'time_start' => $this->time_start,
            'time_expire' => $this->time_expire,
            'limit_credit_pay' => $this->limit_credit_pay,
            'hb_fq_num' => $this->hb_fq_num,
            'hb_fq_percent' => $this->hb_fq_percent,
            'sp_client_ip' => $_SERVER['REMOTE_ADDR'],
            'appid' => $this->appid,
            'openid' => $this->openid,
            'sign' => $this->sign(),
        ];

        return $data;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $method string 提交方式。两个值可选：post、get
     * @param $button_name string  确认按钮显示文字
     * @return string 提交表单HTML文本
     */
    public function buildRequestForm($method = 'POST', $button_name = '正在跳转')
    {
        //待请求参数数组
        $params = $this->getPostData();

        $sHtml = "<form id='payform' name='payform' action='" . $this->request_url . "' method='" . $method . "'>";
        foreach ($params as $key => $param) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $param . "'/>";
        }

        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit' value='" . $button_name . "'></form>";

        $sHtml = $sHtml . "<script>document.forms['payform'].submit();</script>";

        return $sHtml;
    }

    /**
     * 请求
     * @param $url
     * @param $data
     * @return mixed
     */
    protected function post($url, $data)
    {
        $ch = curl_init();
        if ($this->account->params->refer) {
            curl_setopt($ch, CURLOPT_REFERER, $this->account->params->refer); //防封域名
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);
//        echo '<pre>';
        //        echo $response;
        //        var_dump($data);
        $res = json_decode($response, true);

        return $res;
    }

    /**
     * 下单
     * @return mixed
     */
    protected function request()
    {

        $result = $this->post($this->request_url, $this->getPostData());

        if ($result['result_code'] == 1) {
            $this->code = 0;
            $obj = new \stdClass();
            $obj->pay_url = $result['code_url'];
            $obj->content_type = 1;
            return $obj;
        } else {
            $this->code = 500;
            $this->error = $result['return_msg'];
            return false;
        }
    }

    /**
     * 验签
     * @param $result
     * @return bool
     */
    protected function checkSign($result)
    {
        $this->setAccountInfo();
        $out_trade_no = $result['out_trade_no']; //商户系统订单号，原样返回
        $fee_type = 'CNY'; //默认人民币：CNY
        $pay_type = $result['pay_type']; //支付类型 ，比如 201 202
        $total_amount = $result['total_amount']; //订单金额，单位为分
        $device_info = $result['device_info']; //终端设备号
        $postSign = $result['sign']; //MD5签名，32位小写

        $signSource = sprintf("mch_id=%s&out_trade_no=%s&fee_type=%s&pay_type=%s&total_amount=%s&device_info=%s&key=%s", $this->mch_id, $out_trade_no, $fee_type, $pay_type, $total_amount, $device_info, $this->key); //连接字符串加密处理
        $sign = md5($signSource);

        return $sign == $postSign;
    }

    /**
     * 支付后续操作
     * @param $result
     * @param $order
     */
    protected function complete($result, &$order)
    {
        $this->setAccountInfo();
        $trade_state = $result['trade_state']; //1：支付成功，0：未支付，2：失败，3：已退款
        if ($trade_state == self::PAY_SUCCESS) {
            return $this->completeOrder($order);
        }

        return false;
    }

    /**
     * 页面回调
     */
    public function page_callback($params, $order)
    {
        header("Location:" . url('/orderquery', ['orderid' => $order->trade_no]));
    }

    /**
     * 服务器回调
     */
    public function notify_callback($params, $order)
    {
        if ($this->checkSign($params)) {
            //此处作逻辑处理
            if ($this->complete($params, $order)) {
                echo ('success');
                return true;
            }
        }
        echo ('FAIL');
        exit;
    }

}
