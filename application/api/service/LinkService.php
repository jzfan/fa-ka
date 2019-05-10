<?php

namespace app\api\service;

use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 *
 * Class LinkService
 *
 * @package app\api\service
 */
class LinkService extends BaseService {

    /**
     * 获取商品链接
     *
     * @param $userId
     * @param $goodsId
     *
     * @return array
     */
    static function getGoodsLink($userId, $goodsId) {
        return self::get('goods', $userId, $goodsId);
    }

    /**
     * 获取分类链接
     *
     * @param $userId
     * @param $categoryId
     *
     * @return array
     */
    static function getCategoryLink($userId, $categoryId) {
        return self::get('goods_category', $userId, $categoryId);
    }


    /**
     * 获取商家链接
     *
     * @param $userId
     *
     * @return array
     */
    static function getShopLink($userId) {
        return self::get('user', $userId, $userId);
    }

    /**
     * 刷新链接
     *
     * @param $userId
     * @param $type
     * @param $id
     *
     * @return array
     */
    static function refresh($userId, $type, $id) {
        $where = [
            'user_id'       => $userId,
            'relation_type' => $type,
            'relation_id'   => $id,
        ];

        try {
            Db::name('link')->where($where)->delete();
            return self::get($type, $userId, $id);
        } catch (DbException $e) {
            return wrong('重置链接失败，原因:' . $e->getMessage());
        }
    }

    /**
     * 获取连接
     *
     * @param $type
     * @param $userId
     * @param $relationId
     *
     * @return array
     */
    static function get($type, $userId, $relationId) {
        $where = [
            'user_id'       => $userId,
            'relation_type' => $type,
            'relation_id'   => $relationId,
        ];

        $domain = sysconf('site_shop_domain');

        switch ($type) {
            case 'user':
                $domain .= '/links/';
                break;
            case 'goods_category':
                $domain .= '/liebiao/';
                break;
            case 'goods':
                $domain .= '/details/';
                break;
        }

        $res = LinkService::find('link', $where);
        if ($res['status']) {
            return right(['link' => $domain . $res['data']['token'], 'short_link' => $res['data']['short_url']], '获取成功');
        } else {
            $token     = strtoupper(get_uniqid(8));
            $short_url = get_short_domain($domain . $token);
            LinkService::add('link', [
                'user_id'       => $userId,
                'relation_type' => $type,
                'relation_id'   => $relationId,
                'token'         => $token,
                'short_url'     => $short_url,
                'status'        => 1,
                'create_at'     => $_SERVER['REQUEST_TIME'],
            ]);

            return right(['link' => $domain . $token, 'short_link' => $short_url], '获取成功');
        }
    }
}