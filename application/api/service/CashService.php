<?php

namespace app\api\service;

use app\common\model\User;
use service\MerchantLogService;
use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * Class CashService
 *
 * @package app\api\service
 */
class CashService extends BaseService {
    /**
     * 获取用户可提现次数
     *
     * @param $userId
     *
     * @return int|string
     */
    public static function getCashTime($userId) {
        // 今日可提现次数
        $todayCount = Db::name('cash')
                        ->whereTime('create_at', 'today')
                        ->where(['user_id' => $userId])->count();

        $limitNum        = (int)sysconf('cash_limit_num');
        $todayCanCashNum = $limitNum - $todayCount;

        $todayCanCashNum = $todayCanCashNum < 0 ? 0 : $todayCanCashNum;

        return $todayCanCashNum;
    }

    /**
     * 获取提现信息
     *
     * @param $userId
     */
    public static function getInfo($userId) {
        try {
            $model = new User();
            $user  = $model->where('id', '=', $userId)
                           ->field('id,is_freeze,money')->find();

            if ($user->is_freeze == 1) {
                error(500, '无法申请提现操作，您的账户已被冻结！');
            }
            if (!$user->collect) {
                error(4040, '您还未设置收款信息');
            }

            //提现手续费类型
            if (sysconf('cash_fee_type') == 100) {
                $cash_fee = sysconf('cash_fee') . '%';
            } else {
                $cash_fee = sysconf('cash_fee') . '元';
            }

            //提现最低额度
            $min = sysconf('cash_min_money');

            $data = [
                'cash_times' => self::getCashTime($userId),
                'cash_fee'   => $cash_fee,
                'cash'       => $user->money,
                'min'        => $min,
            ];

            success($data, '获取成功');
        } catch (DbException $e) {
            error(500, '获取提现设置失败，原因：' . $e->getMessage());
        }
    }

    /**
     * 申请提现
     *
     * @param $userId
     * @param $money
     */
    public static function apply($userId, $money) {
        // 提现关闭
        if (sysconf('cash_status') == 0) {
            die(sysconf('cash_close_tips'));
        }

        try {
            Db::startTrans();

            $model = new User();
            $user  = $model->where('id', '=', $userId)
                           ->field('id,is_freeze,money')->lock(true)->find();

            if ($user->is_freeze == 1) {
                Db::rollback();
                error(500, '无法申请提现操作，您的账户已被冻结！');
            }
            if (!$user->collect) {
                Db::rollback();
                error(500, '您还未设置收款信息');
            }

            // 检测提现时间
            $startTime = (int)sysconf('cash_limit_time_start');
            $endTime   = (int)sysconf('cash_limit_time_end');
            $curTime   = (int)date('H');
            if ($curTime < $startTime || $curTime > $endTime) {
                Db::rollback();
                error(500, "提现申请失败，提现开放时间为{$startTime}:00 ~ {$endTime}:00");
            }

            // 检测今日提现次数
            if (self::getCashTime($userId) <= 0) {
                Db::rollback();
                error(500, sysconf('cash_limit_num_tips'));
            }

            if ($money <= 0) {
                Db::rollback();
                error(414, '提现金额不能小于等于0！');
            }
            // 检测最低提现金额
            $min = sysconf('cash_min_money');
            if ($money < $min) {
                Db::rollback();
                error(414, "提现金额不能小于最低提现金额: $min 元！");
            }
            if ($user->money < $money) {
                Db::rollback();
                error(414, "余额不足！");
            }

            // 收款信息
            $collect_info = '';
            $collect      = $user->collect;
            switch ($collect->type) {
                case 1: //支付宝
                    $collect_info .= "支付宝账号：{$collect->info['account']}<br>";
                    $collect_info .= "真实姓名：{$collect->info['realname']}<br>";
                    $collect_info .= "身份证号：{$collect->info['idcard_number']}";
                    break;
                case 2: //微信
                    $collect_info .= "微信账号：{$collect->info['account']}<br>";
                    $collect_info .= "真实姓名：{$collect->info['realname']}<br>";
                    $collect_info .= "身份证号：{$collect->info['idcard_number']}";
                    break;
                case 3: //银行
                    $collect_info .= "开户银行：{$collect->info['bank_name']}<br>";
                    $collect_info .= "开户地址：{$collect->info['bank_branch']}<br>";
                    $collect_info .= "收款账号：{$collect->info['bank_card']}<br>";
                    $collect_info .= "真实姓名：{$collect->info['realname']}<br>";
                    $collect_info .= "身份证号：{$collect->info['idcard_number']}";
                    break;
            }

            // 申请提现
            $user->money -= $money;
            $user->save();

            // 记录用户金额变动日志
            $reason = "申请提现金额{$money}元";
            record_user_money_log('apply_cash', $userId, -$money, $user->money, $reason);
            // 获取提现手续费
            $fee = get_cash_fee($money);
            // 记录提现日志
            $cashData = [
                'user_id'      => $userId,
                'type'         => $collect->type,
                'collect_info' => $collect_info,
                'collect_img'  => $collect->collect_img,
                'auto_cash'    => 0,
                'money'        => $money,
                'fee'          => $fee,
                'actual_money' => round($money - $fee, 2),
                'status'       => 0,
                'create_at'    => $_SERVER['REQUEST_TIME'],
            ];
            if ($collect->type == 3) {
                $cashData = array_merge($cashData, [
                    'bank_name'     => $collect->info['bank_name'],
                    'bank_branch'   => $collect->info['bank_branch'],
                    'bank_card'     => $collect->info['bank_card'],
                    'realname'      => $collect->info['realname'],
                    'idcard_number' => $collect->info['idcard_number'],
                ]);
            }

            $res = self::add('cash', $cashData);
            if ($res['status']) {
                Db::commit();
                MerchantLogService::write('提现申请成功', $reason);
                success([], '申请成功');
            } else {
                Db::rollback();
                error(500, $res['msg']);
            }
        } catch (DbException $e) {
            Db::rollback();
            error(500, '申请提现失败，原因：' . $e->getMessage());
        };
    }

    /**
     * 获取提现申请列表
     */
    public static function getLists($where, $config) {
        $res = self::lists('cash', $where, $config);
        if ($res['status']) {
            //补全数据
            if ($config['limit'] > 0) {
                $res['data']->each(function ($item, $key) {
                    $item['create_at']   = date('Y-m-d H:i:s', $item['create_at']);
                    $item['complete_at'] = date('Y-m-d H:i:s', $item['complete_at']);
                    return $item;
                });
            } else {
                foreach ($res['data']['data'] as &$item) {
                    $item['create_at']   = date('Y-m-d H:i:s', $item['create_at']);
                    $item['complete_at'] = date('Y-m-d H:i:s', $item['complete_at']);
                }
            }

            success($res['data'], $res['msg']);
        } else {
            error(500, '获取失败');
        }
    }
}
