<?php

namespace Util\FnPay;
/**
 * Created by PhpStorm.
 * User: violet
 * Date: 2016/11/15
 * Time: 11:15
 */

class ApiClient
{
    //应用ID
    public $appId;
    //秘钥
    public $secret;
    //网关
    public $gatewayUrl = "Https://api.jzpay.cn/gateway";
    //返回数据格式
    public $format = "json";
    //api版本
    public $apiVersion = "1.0";
    //接口名称
    public $method = '';

    // 表单提交字符集编码
    public $postCharset = "UTF-8";

    private $fileCharset = "UTF-8";

    public function __construct()
    {
        date_default_timezone_set("Asia/Shanghai");
    }

    public function md5Sign($params)
    {
        return md5(static::getSignContent($params) . '&key=' . $this->secret);
    }

    public function rsaSign($params, $rsaPrivateKey)
    {
        $data = static::getSignContent($params) . '&key=' . $this->secret;
        $res = openssl_get_privatekey($rsaPrivateKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    private function rsaVerify($data, $sign, $rsaPublicKey, $signType)
    {
        $res = openssl_get_publickey($rsaPublicKey);
        ($res) or die('公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值

        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }

        //释放资源
        openssl_free_key($res);

        return $result;
    }

    protected function getSignContent($params)
    {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);
                $v = htmlspecialchars_decode($v);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    public function requestSignVerify($params)
    {
        $sign = $params['sign'];
        unset($params['sign']);
        unset($params['sign_type']);
        return $sign == $this->md5Sign($params);
    }

    protected function curl($url, $postFields = null, $refer = '')
    {
        $ch = curl_init();
        //设置 Refer
        if ($refer) {
            curl_setopt($ch, CURLOPT_REFERER, $refer); //防封域名
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $postBodyString = "";
        $encodeArray = Array();

        $postMultipart = false;

        if (is_array($postFields) && 0 < count($postFields)) {


            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) //判断是不是文件上传
                {

                    $postBodyString .= "$k=" . urlencode($this->characet($v, $this->postCharset)) . "&";
                    $encodeArray[$k] = $this->characet($v, $this->postCharset);
                } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                    $encodeArray[$k] = new \CURLFile(substr($v, 1));

                }

            }

            unset ($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }

        if ($postMultipart) {
            $headers = array('content-type: multipart/form-data;charset=' . $this->postCharset . ';boundary=' . $this->getMillisecond());
        } else {

            $headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->postCharset);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $reponse = curl_exec($ch);

//        if (curl_errno($ch)) {
//            throw new \Exception(curl_error($ch), 0);
//        } else {
//            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//            if (200 !== $httpStatusCode) {
//                throw new \Exception($reponse, $httpStatusCode);
//            }
//        }
        curl_close($ch);
        return $reponse;
    }

    protected function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    public function call($method, $content, $refer = '')
    {
        $this->method = $method;
        $response = $this->execute(['content' => json_encode($content)], $refer);
        $response = json_decode($response, true);

        return $response;
        if ($response['error_code'] == 0)
            return $response;
        else {
            $code = $response['error_code'];
            throw new \Exception($response['error_msg'], intval($code));
        }
    }

    public function uploadAuthImg($externalId, $fileType, $img, $refer)
    {
        if ($this->checkEmpty($this->postCharset)) {
            $this->postCharset = "UTF-8";
        }

        $this->fileCharset = mb_detect_encoding($this->appId, "UTF-8,GBK");


        //		//  如果两者编码不一致，会出现签名验签或者乱码
        if (strcasecmp($this->fileCharset, $this->postCharset)) {

            // writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
            throw new \Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
        }

        $iv = $this->apiVersion;


        //组装系统参数
        $sysParams["app_id"] = $this->appId;
        $sysParams["version"] = $iv;

        //获取业务参数
        $apiParams = [
            'external_id' => $externalId,
            'file_type' => $fileType,
        ];

        //签名
        $sysParams = array_merge($apiParams, $sysParams);
        $sysParams["sign"] = $this->generateSign($sysParams);

        $sysParams['file'] = '@' . $img;

        //系统参数放入GET请求串
        $requestUrl = $this->uploadAuthImgUrl;

        //发起HTTP请求
        $resp = $this->curl($requestUrl, $sysParams, $refer);

        // 将返回结果转换本地文件编码
        $respObject = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);

        $response = json_decode($respObject, true);
        if ($response['success'] === true)
            return array_get($response, 'return_value', true);
        else
            throw new \ErrorException(array_get($response, 'error_message'), array_get($response, 'error_code', 0));
    }

    public function execute($data, $refer = '')
    {


        if ($this->checkEmpty($this->postCharset)) {
            $this->postCharset = "UTF-8";
        }

        $this->fileCharset = mb_detect_encoding($this->appId, "UTF-8,GBK");


        //		//  如果两者编码不一致，会出现签名验签或者乱码
        if (strcasecmp($this->fileCharset, $this->postCharset)) {

            // writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
            throw new Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
        }

        $iv = $this->apiVersion;


        //组装系统参数
        $sysParams["app_id"] = $this->appId;
        $sysParams["version"] = $iv;
        $sysParams['method'] = $this->method;

        //获取业务参数
        $apiParams = $data;

        //签名
        $sysParams = array_merge($apiParams, $sysParams);
        $sysParams["sign"] = $this->md5Sign($sysParams);
        $sysParams["sign_type"] = "MD5";

//        $sysParams['sign'] = $this->rsaSign($sysParams, file_get_contents("../key/rsa_private_key.pem"));
//        $sysParams["sign_type"] = "RSA";

        //系统参数放入GET请求串
        $requestUrl = $this->gatewayUrl;

        //发起HTTP请求
        try {
            $resp = $this->curl($requestUrl, $sysParams, $refer);
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }

        //解析AOP返回结果
        $respWellFormed = false;


        // 将返回结果转换本地文件编码
        $respObject = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);

        return $respObject;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset)
    {


        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {

                $data = mb_convert_encoding($data, $targetCharset);
                //				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }

        return $data;
    }


    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }
}