<?php
namespace app\common;

class PafbBase extends Pay
{
    const GATEWAY = "https://shq-api-test.51fubei.com/gateway";

    public function generateNonce()
    {
        return substr(md5(time() . rand(10000, 9999)), 8, 16);
    }

    public function request($data)
    {
        $data['app_id'] = $this->account->params->appid;
        $data['format'] = 'json';
        $data['sign_method'] = 'md5';
        $data['nonce'] = $this->generateNonce();
        $key = $this->account->params->key;
        $data['sign'] = static::generateSign($data, $key);
        $result = json_decode(static::mycurl(static::GATEWAY, $data), true);
        return $result;
    }

    protected static function mycurl($url, $params = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $header = array("content-type: application/json");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $reponse = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }

    protected static function generateSign($content, $key)
    {
        return strtoupper(static::sign(static::getSignContent($content) . $key));
    }

    protected static function getSignContent($content)
    {
        ksort($content);
        $signString = "";
        foreach ($content as $key => $val) {
            if (!empty($val)) {
                $signString .= $key . "=" . $val . "&";
            }
        }
        $signString = rtrim($signString, "&");
        return $signString;
    }

    protected static function sign($data)
    {
        return md5($data);
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
        $signature = $params['sign'];
        unset($params['sign']);

        $key = $this->account->params->key;
        $sign = $data['sign'] = static::generateSign($params, $key);

        if ($sign && $sign == $signature) {
            if ($params["status"] == "1") {
                // 金额异常检测
                if (bccomp($params['total_fee'], $order->total_price * 100) >= 0) {
                    record_file_log('Pafb_notify_error', '金额异常！' . "\r\n" . $order->trade_no . "\r\n订单金额：{$order->total_price}，已支付：{$params['total_fee']}");
                    die('金额异常！');
                }

                $this->completeOrder($order);
                echo 'success';
                return true;
            } else {
                exit('fail');
            }
        }
    }
}
