<?php

namespace app\api\controller;

use think\Config;
use think\Controller;

/**
 * Api 基础类
 * Class Base
 * @package app\api\controller
 */
class ApiBase extends Controller
{

    protected $params;

    function __construct()
    {
        parent::__construct();

        //保证时区
        date_default_timezone_set("Asia/Shanghai");

        //检查当前网站是否已经关闭
        $siteStatus = sysconf('site_status');
        if(1 != $siteStatus){
            error(500, '网站维护中，请稍候再试');
        }

        //检查参数
        $res = $this->checkSign();
        if (!$res['status']) {
            error($res['code'], $res['msg']);
        }

        //读取配置文件
        Config::load(APP_PATH . 'api/configs.php');
    }

    /**
     * 限制请求方法
     * @param $method array|string 请求方法
     * @return bool
     */
    protected function limitRequestMethod($method)
    {
        if (is_array($method)) {
            if (in_array($this->request->method(), $method)) {
                return true;
            }
        }

        if (is_string($method)) {
            if ($this->request->method() == strtoupper($method)) {
                return true;
            }
        }

        error(500, '服务拒绝');
        return false;
    }

    /**
     * 签名验证
     */
    protected function checkSign()
    {
        $params = input('');

        if (empty($params)) {
            $params = json_decode(file_get_contents('php://input'), 1);
        }

        $this->params = $params;

        record_file_log('request_params', 'url :' . $this->request->url(true));

        record_file_log('request_params', 'params :' . json_encode($params));

        //校验请求平台
        if (!isset($params['platform'])) {
            return [
                'status' => false,
                'code' => 411,
                'msg' => '请指定平台',
            ];
        }

        switch ($params['platform']) {
            case 'ios'://苹果
            case 'web'://网页
            case 'android'://安卓
            case 'wxapp'://小程序
                break;
            default:
                return [
                    'status' => false,
                    'code' => 411,
                    'msg' => '暂不支持该平台',
                ];
        }

        //过滤超前请求和延迟达到30秒的请求
        if (!isset($params['request_time'])
//            || $params['request_time'] > time()
            || $params['request_time'] < (time() - 300)) {
            return [
                'status' => false,
                'code' => 412,
                'msg' => '请求超时，请重试',
            ];
        }


        if (isset($params['sign'])) {
            //拼装参数
            $postInfo = '';

            ksort($params);

            foreach ($params as $key => $param) {
                if ($key != 'sign') {
                    if (!is_null($param) && $param !== '') {
                        if(is_array($param)){
                            $param = json_encode($param);
                        }
                        $param = htmlspecialchars_decode($param);
                        $postInfo .= "$key=$param&";
                    }
                }
            }
            //删除多余字符
            $postInfo = trim($postInfo, '&');

            record_file_log('request_params', 'postinfo :' . $postInfo);

            //签名
            $localSign = md5($postInfo);

            record_file_log('request_params', 'localsign :' . $localSign);

            //签名比对
            if (strtolower($localSign) == strtolower($params['sign'])) {
                return [
                    'status' => true
                ];

            }
        }

        return [
            'status' => false,
            'code' => 4101,
            'msg' => '非法请求'
        ];
    }

    /**
     * 获取头部信息
     * @param $name
     * @return string
     */
    protected function getHeader($name)
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            return $headers[$name];
        } elseif (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }

        return '';
    }

    /**
     * 获取真实的密码
     * @param $platform
     * @param $password
     * @return string
     */
    protected function getRealPassword($platform, $password)
    {
        return $this->decrypt($platform, $password);
    }

    /**
     * 加密数据
     * @param $platform
     * @param $data
     * @return string
     */
    protected function encrypt($platform, $data)
    {
        $key = config($platform . '_crypt_key');
        return openssl_encrypt($data, 'AES-128-ECB', $key);
    }

    /**
     * 解密数据
     * @param $platform
     * @param $data
     * @return string
     */
    protected function decrypt($platform, $data)
    {
        $key = config($platform . '_crypt_key');
        return openssl_decrypt($data, 'AES-128-ECB', $key);
    }
}