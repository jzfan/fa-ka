<?php

namespace app\api\service;

/**
 *
 * Class LinkService
 * @package app\api\service
 */
class ComplaintService extends BaseService
{

    /**
     * 获取商品链接
     * @param $where
     * @param $config
     */
    static function getList($where, $config)
    {
        $res = self::lists('complaint', $where, $config);

        if ($res['status']) {

            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    return $item;
                });
            } else {
                foreach ($res['data']['data'] as &$item) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                }
            }

            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);

        }
    }

    /**
     * 获取分类链接
     * @param $userId
     * @param $tradeNo
     */
    static function getDetail($userId, $tradeNo)
    {
        $where = [
            'user_id' => $userId,
            'trade_no' => $tradeNo
        ];

        $config = [
            'fields' => 'trade_no,type,qq,mobile,desc,status,create_at,result',
        ];

        $res = self::find('complaint', $where, $config);
        if ($res['status']) {
            $messages = self::lists('complaint_message', ['trade_no' => $tradeNo], [
                'fields' => 'from,content,create_at,content_type,image_width as imageWidth,image_height as imageHeight',
                'order' => 'id'
            ]);

            if ($messages['status']) {
                $res['data']['messages'] = $messages['data']['data'];
                foreach ($res['data']['messages'] as &$item) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                }
            } else {
                $res['data']['message'] = [];
            }
            $res['data']['create_at'] = date('Y-m-d H:i:s', $res['data']['create_at']);
            success($res['data'], $res['msg']);
        } else {
            error($res['code'], $res['msg']);
        }
    }

    /**
     * 发送消息
     * @param $userId
     * @param $tradeNo
     * @param $msg
     * @param $type
     * @param $filename
     */
    static function send($userId, $tradeNo, $msg, $type = 0, $filename='')
    {
        $where = [
            'user_id' => $userId,
            'trade_no' => $tradeNo
        ];

        $config = [
            'fields' => 'trade_no,status,expire_at',
        ];

        $res = self::find('complaint', $where, $config);

        if (!$res['status']) {
            error(414, '投诉不存在');
        }

        $complaint = $res['data'];
        if ($complaint['expire_at'] < time()) {
            error(414, '投诉举证期已过，请等候管理员判决！');
        }

        $data = [
            'from' => $userId,
            'trade_no' => $tradeNo,
            'content' => $msg,
            'status' => 0,
            'create_at' => time(),
            'content_type' => $type,
        ];

        if ($type == 1 && $filename) {
            //图片要补充宽高，方便客户端显示
            $img_info = getimagesize(ROOT_PATH . '/static/upload/' . $filename);
            $data['image_width'] = $img_info[0];
            $data['image_height'] = $img_info[1];
        }

        $res = self::add('complaint_message', $data);
        if ($res['status']) {
            success();
        } else {
            error($res['code'], $res['msg']);
        }
    }
}
