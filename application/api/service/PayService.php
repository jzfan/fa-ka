<?php

namespace app\api\service;

use think\Db;

/**
 *
 * Class LinkService
 * @package app\api\service
 */
class PayService extends BaseService
{

    /**
     * 获取支付方式列表
     * @param $userId
     */
    static function getLists($userId)
    {
        $config = [
            'fields' => 'id,paytype,title,show_name,is_available'
        ];
        $res = self::lists('channel', ['status' => 1], $config);
        if ($res['status']) {
            $channels = $res['data']['data'];

            $userChannels = [];
            foreach ($channels as &$v) {
                $userChannels[] = [
                    'channel_id' => $v['id'],
                    'title' => $v['title'],
                    'rate' => get_user_rate($userId, $v['id']),
                    'status' => get_user_channel_status($userId, $v['id']),
                ];
            }

            success($userChannels, '获取成功');
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 更改渠道状态
     * @param $userId
     * @param $id
     * @param $status
     */
    static function toggleStatus($userId, $id, $status)
    {
        if (empty($id)) {
            error(414, '请指定渠道');
        }

        $where = [
            'user_id' => $userId,
            'channel_id' => $id,
        ];

        //确定渠道存在
        $res = self::find('channel', ['id' => $id], ['fields' => 'status']);
        if ($res['status']) {
            if ($res['data']['status'] != 1) {
                error(500, '管理员已关闭渠道');
            }
        } else {
            error($res['code'], $res['msg']);
        }

        //确定用户渠道信息存在
        $res = self::find('user_channel', $where, ['fields' => 'id']);
        if (!$res['status']) {
            $res = self::add('user_channel', array_merge($where, [
                'status' => $status
            ]));
        } else {
            $res = self::toggleField('user_channel', $where, $status);
        }
        if ($res['status']) {
            success($res['data'], $res['msg']);
        } else {
            error($res['code'], $res['msg']);
        }
    }

    /**
     * 获取渠道分析数据
     * @param $where
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    static function getStatistic($userId, $where)
    {
        //总订单数
        $lists = Db::name('order')->where($where)
            ->field('channel_id, count(id) as total')
            ->group('channel_id')->select();

        if ($lists) {
            foreach ($lists as &$item) {
                //已付，实收
                $paid = Db::name('order')->where([
                    'user_id' => ['=', $userId],
                    'status' => ['=', 1],
                    'channel_id' => $item['channel_id'],
                ])
                    ->field('count(id) as paid, sum(total_price) as paid_amount')
                    ->find();

                $item = array_merge($item, $paid);


                //未付，未收
                $notPaid = Db::name('order')->where([
                    'user_id' => ['=', $userId],
                    'status' => ['<>', 1],
                    'channel_id' => $item['channel_id'],
                ])
                    ->field('count(id) as not_paid, sum(total_price) as not_paid_amount')
                    ->find();
                $item = array_merge($item, $notPaid);

                foreach ($item as &$v) {
                    if ($v == null) {
                        $v = 0;
                    }
                }

                $item['channel_name'] = Db::name('channel')->where('id', '=', $item['channel_id'])->value('title');

                //总额
                $item['amount'] = bcadd($item['paid_amount'], $item['not_paid_amount'], 4);
            }

            success($lists, '获取成功');
        }

        error(500, '暂无统计数据');
    }
}