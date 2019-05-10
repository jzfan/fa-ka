<?php

namespace app\api\service;

use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * Class CouponService
 * @package app\api\service
 */
class CouponService extends BaseService
{

    /**
     * 获取商品优惠券列表
     * @param $where
     * @param $config
     */
    public static function getList($where, $config)
    {
        $res = self::lists('goods_coupon', $where, $config);
        if ($res['status']) {
            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    $item['expire_at_desc'] = self::getSpace($item['expire_at']);
                    $item['category_name'] = Db::name('goods_category')->where('id', '=', $item['cate_id'])->value('name');
                    return $item;
                });
            } else {
                foreach ($res['data']['data'] as &$item) {
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    $item['expire_at_desc'] = self::getSpace($item['expire_at']);
                    $item['category_name'] = Db::name('goods_category')->where('id', '=', $item['cate_id'])->value('name');
                }
            }
            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 获取时间间隔
     * @param $time
     * @return string
     */
    public static function getSpace($time)
    {
        $space = $time - time();
        $expire = '';
        if ($space / 86400) {
            $day = floor($space / 86400);
            $space -= $day * 86400;
            $expire .= $day . '天';
        }

        if ($space / 3600) {
            $expire .= floor($space / 3600) . '小时';
        }

        if ($expire) {
            $expire .= '后';
        }

        return $expire;
    }

    /**
     * 增加
     */
    public static function addCoupon($userId, $data, $count)
    {
        $i = 1;
        try {
            Db::startTrans();
            while ($i <= $count) {
                $data['code'] = strtoupper(substr(md5(uniqid() . $userId), 0, 12) . random_str(4));
                $res = self::add('goods_coupon', $data);
                if (!$res['status']) {
                    Db::rollback();
                    error(500, $res['msg']);
                }
                $i++;
            }
            Db::commit();
            success([], '生成成功');
        } catch (DbException $e) {
            Db::rollback();
            error(500, $res['msg']);
        }
    }
}
