<?php

namespace controller;

use service\ToolsService;
use think\Request;
use think\Response;

/**
 * 数据接口通用控制器
 * Class BasicApi
 * @package controller
 */
class BasicApi
{

    /**
     * 访问请求对象
     * @var Request
     */
    public $request;

    /**
     * 当前访问身份
     * @var string
     */
    public $token;

    /**
     * 基础接口SDK
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
        // CORS 跨域 Options 检测响应
        ToolsService::corsOptionsHandler();
        // 输入对象
        $this->request = is_null($request) ? Request::instance() : $request;
    }

    /**
     * 输出返回数据
     * @param string $msg 提示消息内容
     * @param string $code 业务状态码
     * @param mixed $data 要返回的数据
     * @param string $type 返回类型 JSON XML
     * @return Response
     */
    public function response($msg, $code = 'SUCCESS', $data = [], $type = 'json')
    {
        $result = ['msg' => $msg, 'code' => $code, 'data' => $data, 'type' => strtolower($type)];
        return Response::create($result, $type)->header(ToolsService::corsRequestHander())->code(200);
    }

}
