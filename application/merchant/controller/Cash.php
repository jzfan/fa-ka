<?php

namespace app\merchant\controller;

use app\common\model\Cash as CashModel;
use service\MerchantLogService;
use think\Controller;
use think\Db;
use think\Request;

class Cash extends Base
{
    // 提现列表
    public function index()
    {
        $this->setTitle('提现列表');
        ////////////////// 查询条件 //////////////////
        $query = [
        ];
        $where = $this->genereate_where($query);

        $cashs = CashModel::where($where)->order('id desc')->paginate(30, false, [
            'query' => $query,
        ]);
        // 分页
        $page = $cashs->render();
        $this->assign('page', $page);
        $this->assign('cashs', $cashs);
        return $this->fetch();
    }

    /**
     * 生成查询条件
     */
    protected function genereate_where($params)
    {
        $where = [];
        $where['user_id'] = $this->user->id;
        $action = $this->request->action();
        switch ($action) {
            case 'index':
                break;
        }
        return $where;
    }

    /**
     * 申请提现
     */
    public function apply()
    {
        $user = $this->user;
        if ($user->is_freeze == 1) {
            $this->error('无法申请提现操作，您的账户已被冻结！');
        }
        if (!$user->collect) {
            $this->error('您还未设置收款信息！', 'user/settings');
        }

        // 今日可提现次数
        $todayTime = strtotime(date('Y-m-d'));
        $todayCount = CashModel::where(['user_id' => $this->user->id, 'create_at' => ['>=', $todayTime]])->count();
        $limitNum = (int) sysconf('cash_limit_num');
        $todayCanCashNum = $limitNum - $todayCount;
        $todayCanCashNum = $todayCanCashNum < 0 ? 0 : $todayCanCashNum;
        $this->assign('todayCanCashNum', $todayCanCashNum);

        if (!$this->request->isPost()) {
            $this->setTitle('申请提现');
            return $this->fetch();
        }

        // 提现关闭
        if (sysconf('cash_status') == 0) {
            die(sysconf('cash_close_tips'));
        }

        // 检测提现时间
        $startTime = (int) sysconf('cash_limit_time_start');
        $endTime = (int) sysconf('cash_limit_time_end') - 1;
        $curTime = (int) date('H');
        if ($curTime < $startTime || $curTime > $endTime) {
            $this->error("不在允许提现时间（{$startTime}:00 ~ {$endTime}:00）");
        }

        // 检测今日提现次数
        if ($todayCanCashNum <= 0) {
            $this->error(sysconf('cash_limit_num_tips'));
        }

        $money = input('money/f', 0);
        if ($money <= 0) {
            $this->error('提现金额不能小于等于0！');
        }
        // 检测最低提现金额
        if ($money < sysconf('cash_min_money')) {
            $this->error('提现金额不能小于最低提现金额！');
        }
        if ($user->money < $money) {
            $this->error('您的余额不足以提现');
        }

        // 收款信息
        $collect_info = '';
        $collect = $user->collect;
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
        Db::startTrans();
        try {
            //锁定用户
            $realUser          = Db::name('user')->where('id', $user->id)->field('money')->lock(true)->find();
            $realUser['money'] -= $money;

            // 记录用户金额变动日志
            $reason = "申请提现金额{$money}元";
            record_user_money_log('apply_cash', $user->id, -$money, $realUser['money'], $reason);
            // 获取提现手续费
            $fee = get_cash_fee($money);
            // 记录提现日志
            $cashData = [
                'user_id' => $user->id,
                'type' => $collect->type,
                'collect_info' => $collect_info,
                'collect_img' => $collect->collect_img,
                'auto_cash' => 0,
                'money' => $money,
                'fee' => $fee,
                'actual_money' => round($money - $fee, 2),
                'status' => 0,
                'create_at' => $_SERVER['REQUEST_TIME'],
            ];
            switch (intval($collect->type)) {
                case 2:
                case 1:
                    $cashData = array_merge($cashData, [
                        'bank_card' => $collect->info['account'],
                        'realname' => $collect->info['realname'],
                        'idcard_number' => $collect->info['idcard_number'],
                    ]);
                    break;
                case 3:
                    $cashData = array_merge($cashData, [
                        'bank_name' => $collect->info['bank_name'],
                        'bank_branch' => $collect->info['bank_branch'],
                        'bank_card' => $collect->info['bank_card'],
                        'realname' => $collect->info['realname'],
                        'idcard_number' => $collect->info['idcard_number'],
                    ]);
                    break;
            }

            CashModel::create($cashData);

            // 创建提现记录成功，更新用户余额，释放锁
            Db::name('user')->where('id', $user->id)->update($realUser);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error('申请提现失败，原因：' . $e->getMessage());
        };
        MerchantLogService::write('提现申请成功', $reason);
        $this->success('申请成功', 'merchant/cash/index');
    }
}
