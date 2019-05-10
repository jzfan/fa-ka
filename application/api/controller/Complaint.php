<?php

namespace app\api\controller;

use app\api\service\ComplaintService;

/**
 * Class Complaint
 * @package app\api\controller
 */
class Complaint extends AuthBase
{

    /**
     * 获取投诉列表
     */
    public function lists()
    {
        $this->limitRequestMethod('GET');

        $where = [
            'user_id' => $this->userid,
        ];

        $status = input('status/d');
        if ($status != '') {
            $where['status'] = $status;
        }

        $config = [
            'fields' => 'trade_no,type,qq,mobile,desc,status,create_at,result',
            'page' => input('page/s', 1),
            'limit' => input('limit/s', 0),
            'order' => input('order/s', 'id desc')
        ];

        ComplaintService::getList($where, $config);
    }

    /**
     * 获取投诉详情
     */
    public function detail()
    {
        $this->limitRequestMethod('GET');

        $tradeNo = input('trade_no/s', '');
        if (empty($tradeNo)) {
            error(414, '请指定投诉');
        }

        ComplaintService::getDetail($this->userid, $tradeNo);
    }

    /**
     * 发送投诉消息
     */
    public function sendTxt()
    {
        $this->limitRequestMethod('POST');

        $tradeNo = input('trade_no/s', '');
        if (empty($tradeNo)) {
            error(414, '缺少投诉订单号');
        }

        $txt = input('content/s', '');
        if (empty($txt)) {
            error(414, '请输入举证内容');
        }

        $type = input('type/d',0);

        ComplaintService::send($this->userid, $tradeNo, $txt,$type);
    }

    /**
     * 发送投诉图片
     */
    public function sendImg()
    {
        $this->limitRequestMethod('POST');

        $tradeNo = input('trade_no/s', '');
        if (empty($tradeNo)) {
            error(414, '缺少投诉订单号');
        }

        $res = getUploadFile('img', true);
        if (!$res['status']) {
            error($res['code'], $res['msg']);
        }

        ComplaintService::send($this->userid, $tradeNo, $res['data']['file'], 1, $res['data']['filename']);
    }
}
