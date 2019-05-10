<?php

namespace app\api\service;

use app\common\model\User;
use service\MerchantLogService;
use think\Db;
use think\Exception;

/**
 * Class SpreadService
 * @package app\api\service
 */
class SpreadService extends BaseService
{
    /**
     * 获取推广用户列表
     * @param $where
     * @param $config
     */
    static function getLists($where, $config)
    {
        $res = self::lists('user', $where, $config);
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

            $spread_url = generate_qrcode_link('__PUBLIC__/register?user_id=' . $where['parent_id']);

            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 获取推广返佣列表
     * @param $where
     * @param $config
     */
    static function getRebateLists($where, $config)
    {
        $res = self::lists('user_money_log', $where, $config);

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
}