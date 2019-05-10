<?php

namespace app\api\service;

use think\Db;

/**
 * Class UserService
 * @package app\api\service
 */
class UserService extends BaseService
{

    /**
     * 获取用户登录日志
     * @param string $userId
     * @param array $config
     */
    public static function getUserLoginLog($userId, $config)
    {
        $where = [
            'user_id' => $userId,
        ];

        $res = self::lists('user_login_log', $where, $config);
        if ($res['status']) {
            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['username'] = Db::name('user')
                        ->where('id', '=', $item['user_id'])->value('username');
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                    return $item;
                });
            } else {
                $username = Db::name('user')
                    ->where('id', '=', $userId)->value('username');
                foreach ($res['data']['data'] as &$item) {
                    $item['username'] = $username;
                    $item['create_at'] = date('Y-m-d H:i:s', $item['create_at']);
                }
            }

            success($res['data'], $res['msg']);
        } else {
            error($res['msg']);
        }
    }

    /**
     * 获取商家信息
     * @param $userId
     * @return array
     */
    public static function getShopInfo($userId)
    {
        $where = [
            'id' => $userId,
        ];

        $config = [
            "fields" => [
                'id as user_id',
                'username', 'mobile', 'qq', 'email', 'subdomain', 'shop_notice_auto_pop',
                'shop_name', 'shop_notice', 'statis_code', 'pay_theme',
                'stock_display', 'money', 'rebate', 'freeze_money',
                'website', 'is_close', 'cash_type', 'login_auth', 'login_auth_type',
            ],
        ];

        $res = self::find('user', $where, $config);

        //补充收款信息
        if ($res['status']) {
            $collect = self::getCollect($userId);
            if ($collect['status']) {
                $res['data']['collect'] = $collect['data'];
                $res['data']['collect']['info'] = json_decode($res['data']['collect']['info'], 1);
            }
        }

        //补充链接信息
        $link = LinkService::getShopLink($userId)['data'];
        $res['data']['link'] = $link['link'];
        $res['data']['short_link'] = $link['short_link'];

        //补充页面风格名称
        if ($res['data']['pay_theme']) {
            $pay_themes = config('pay_themes');
            $pay_theme_name = '';

            foreach ($pay_themes as $theme) {
                if ($theme['alias'] == $res['data']['pay_theme']) {
                    $pay_theme_name = $theme['name'];
                    break;
                }
            }
        } else {
            $pay_theme_name = '默认';
        }

        $res['data']['pay_theme_name'] = $pay_theme_name;

        return $res;
    }

    /**
     * 设置商家信息
     */
    public static function setShopInfo($userId, $data)
    {
        $res = self::edit('user', ['id' => $userId], $data);

        if ($res['status']) {
            success($res['data'], $res['msg']);
        } else {
            error(500, $res['msg']);
        }
    }

    /**
     * 获取收款信息
     * @param $userId
     * @return array
     */
    public static function getCollect($userId)
    {
        $res = self::find('user_collect', ['user_id' => $userId], ['fields' => 'user_id,type,info,collect_img,allow_update']);

        if ($res['status']) {
            $collect = $res['data'];
            $collect['info'] = json_decode($collect['info'], 1);
        }

        return $res;
    }

    /**
     * 用户收款信息
     * @param $userId
     * @param $data
     */
    public static function setCollect($userId, $data)
    {
        $res = self::find('user_collect', ['user_id' => $userId], ['fields' => 'id,allow_update']);
        if ($res['status']) {
            //检查是否允许更新
            if ($res['data']['allow_update'] == 1) {
                $res = self::edit('user_collect', ['user_id' => $userId], $data);
                if ($res['status']) {
                    success();
                } else {
                    error(500, $res['msg']);
                }
            } else {
                error(500, '不允许修改');
            }
        } else {
            $res = self::add('user_collect', $data);
            if ($res['status']) {
                success();
            } else {
                error(500, $res['msg']);
            }
        }
    }
}
