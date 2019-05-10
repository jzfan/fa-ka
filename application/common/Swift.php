<?php

namespace app\common;

/**
 * 威富通支付渠道
 * Class Swift
 * @package app\common
 */
class Swift extends Pay
{

    /** 网关url地址 */
    protected $gateUrl = 'https://pay.swiftpass.cn/pay/gateway';

    protected $version = '1.0';

    protected $sign_type = 'MD5';
    //错误信息
    public $errInfo = '';

    //超时时间
    public $timeOut = 120;

    //http状态码
    public $responseCode = 0;

    /**
     * 提交订单信息
     */
    public function request($type, $data, $notify)
    {
        $params = array_merge([
            'service' => $type, //接口类型：pay.alipay.native  表示支付宝扫码
            'mch_id' => $this->account->params->mch_id, //必填项，商户号，由威富通分配
            'version' => $this->version,
            'sign_type' => $this->sign_type,
            'notify_url' => $notify,
            'nonce_str' => $data['out_trade_no'],
        ], $data);
        $params = $this->setReqParams($params, array('method'));
        $params['sign'] = $this->createMD5Sign($params); //创建签名

        $params = $this->toXml($params);

        $res = $this->call($this->gateUrl, $params);
        if ($res && $res['status'] == 0 && $this->isTenpaySign($res)) {
            return $res;
        } else {
            return [
                'status' => 500,
            ];
        }
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
        if ($this->isTenpaySign($params)) {
            if ($params['status'] == 0 && $params['result_code'] == 0) {
                // 11;
                //更改订单状态
                $this->completeOrder($order);
                echo 'success';
                return true;
            } else {
                echo 'failure1';
                exit();
            }
        } else {
            echo 'failure2';
            exit;
        }
    }

    /**
     * 一次性设置参数
     */
    public function setReqParams($post, $filterField = null)
    {
        if ($filterField !== null) {
            foreach ($filterField as $k => $v) {
                unset($post[$v]);
            }
        }

        //判断是否存在空值，空值不提交
        foreach ($post as $k => $v) {
            if (empty($v)) {
                unset($post[$k]);
            }
        }

        return $post;
    }

    public function isTenpaySign($params)
    {
        $swiftpassSign = strtolower($params["sign"]);

        return $this->createMD5Sign($params) == $swiftpassSign;
    }

    public function createMD5Sign($params)
    {
        $signPars = "";
        ksort($params);
        foreach ($params as $k => $v) {
            if ("sign" != $k && "" != $v) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $this->account->params->key;

        return strtolower(md5($signPars));
    }

    //获取xml编码
    public function getXmlEncode($xml)
    {
        $ret = preg_match("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
        if ($ret) {
            return strtoupper($arr[1]);
        } else {
            return "";
        }
    }

    //执行http调用
    public function call($url, $data)
    {
        //启动一个CURL会话
        $ch = curl_init();

        //防封域名
        if ($this->account->params->refer) {
            curl_setopt($ch, CURLOPT_REFERER, $this->account->params->refer);
        }

        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeOut);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // 执行操作
        $res = curl_exec($ch);
        $this->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($res == null) {
            $this->errInfo = "call http err :" . curl_errno($ch) . " - " . curl_error($ch);
            curl_close($ch);
            return false;
        } else if ($this->responseCode != "200") {
            $this->errInfo = "call http err httpcode=" . $this->responseCode;
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $this->parseXML($res);
    }

    /**
     * 将数据转为XML
     */
    public static function toXml($array)
    {
        $xml = '<xml>';
        foreach ($array as $k => $v) {
            $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
        }
        $xml .= '</xml>';
        return $xml;
    }

    public function parseXML($xmlSrc)
    {
        if (empty($xmlSrc)) {
            return false;
        }
        $array = array();
        libxml_disable_entity_loader(true);
        $xml = simplexml_load_string($xmlSrc);
        $encode = $this->getXmlEncode($xmlSrc);

        if ($xml && $xml->children()) {
            foreach ($xml->children() as $node) {
                //有子节点
                if ($node->children()) {
                    $k = $node->getName();
                    $nodeXml = $node->asXML();
                    $v = substr($nodeXml, strlen($k) + 2, strlen($nodeXml) - 2 * strlen($k) - 5);

                } else {
                    $k = $node->getName();
                    $v = (string) $node;
                }

                if ($encode != "" && $encode != "UTF-8") {
                    $k = iconv("UTF-8", $encode, $k);
                    $v = iconv("UTF-8", $encode, $v);
                }
                $array[$k] = $v;
            }
        }
        return $array;
    }

    public function getAddress()
    {
        foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'REMOTE_ADDR') as $header) {
            if (!isset($_SERVER[$header]) || ($spoof = $_SERVER[$header]) === null) {
                continue;
            }
            sscanf($spoof, '%[^,]', $spoof);
            if (!filter_var($spoof, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $spoof = null;
            } else {
                return $spoof;
            }
        }
        return '0.0.0.0';
    }
}
