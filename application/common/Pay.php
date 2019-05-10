<?php
/**
 * 支付基类
 */

namespace app\common;

use app\common\util\notify\Sell;
use think\Db;
use think\Exception;

class Pay {
    //  支付地址类型  1：二维码 2：跳转链接 3：表单 4: 二维码或跳转链接 5：微信原生 6 为了兼容上海银行没有同步回跳的问题
    protected $error = '';

    public function getError() {
        return $this->error;
    }

    /**
     * @var array 缓存的实例
     */
    public static $instance = [];

    public static function load($channel, $account = []) {
        $code = $channel->code;
        if (!isset($account['params']->refer)) {
            $account['params']->refer = '';
        }

        $class = '\\app\\common\\pay\\' . $code;
        if (!isset(SELF::$instance[$code])) {
            // 实例化支付渠道
            SELF::$instance[$code] = new $class();
            // 加载渠道账户
            SELF::$instance[$code]->channel = $channel;
            // 加载渠道账户
            SELF::$instance[$code]->account = $account;
        }
        return SELF::$instance[$code];
    }

    /**
     * 完成订单
     *
     * @param  string $order 订单
     */
    public function completeOrder(&$order) {
        Db::startTrans();
        try {
            $time = time();
            // 完成订单
            $res = Db::table('order')->where(['id' => $order->id, 'status' => 0])->update(['status' => 1, 'success_at' => $time]);
            if (!$res) {
                Db::rollback();
                exit;
            }
            // 用户加钱
            $user = Db::table('user')->lock(true)->where('id', $order->user_id)->find();
            if ($user) {
                $money = $order->total_product_price; //加的钱为产品总价
                Db::table('user')->where('id', $order->user_id)->update(['money' => ['exp', 'money+' . $money]]);
                //当前余额
                $balance = round($user['money'] + $money, 3);
                // 记录金额日志
                record_user_money_log('goods_sold', $user['id'], $money, $balance, "成功售出商品{$order->goods_name}（{$order->quantity}张）");
                // 扣除手续费
                if ($order->fee_payer == 1 && $order->fee > 0) {
                    Db::table('user')->where('id', $order->user_id)->update(['money' => ['exp', 'money-' . $order->fee]]);
                    //当前余额
                    $balance = round($balance - $order->fee, 3);
                    if ($balance < 0) {
                        throw new Exception("商家余额不足以扣除手续费");
                    }
                    // 记录金额日志
                    record_user_money_log('goods_sold', $user['id'], -$order->fee, $balance, "扣除交易手续费，订单：{$order->trade_no}");
                    // 扣除上级佣金
                }
                if ($user['parent_id'] > 0) {
                    $parent           = Db::table('user')->lock(true)->where('id', $user['parent_id'])->find();
                    $spreadRebateRate = get_spread_rebate_rate();
                    $rebate           = round($order->fee * $spreadRebateRate, 3);
                    if ($parent && $rebate > 0) {
                        // 返佣
                        Db::table('user')->where('id', $parent['id'])->update(['money' => ['exp', 'money+' . $rebate], 'rebate' => ['exp', 'rebate+' . $rebate]]);
                        // 记录金额日志
                        record_user_money_log('sub_sold_rebate', $parent['id'], $rebate, round($parent['money'] + $rebate, 3), "下级[{$user['username']}]售出商品，返佣{$rebate}元");
                    }
                }
                //扣除短信费，只在选择“商家承担”短信费时需要扣除，否则是由用户承担，在付款时就付到平台了
                $smsPrice = 0;
                if ($order->sms_payer == 1) {
                    //判断是否发送了短信
                    if ($order->sms_notify == 1) {
                        $smsPrice = get_sms_cost();
                        Db::table('user')->where('id', $order->user_id)->update(['money' => ['exp', 'money-' . $smsPrice]]);
                        //当前余额
                        $balance = round($balance - $smsPrice, 3);
                        if ($balance < 0) {
                            throw new Exception('商家余额不足以扣除短信费');
                        }
                        // 记录金额日志
                        record_user_money_log('goods_sold', $user['id'], -$smsPrice, $balance, "扣除短信费，订单：{$order->trade_no}");

                        //扣完短信费，更新到订单信息中
                        Db::table('order')->where('id', $order->id)->update(['sms_price' => $smsPrice]);
                    }
                }

                //交易完成先冻结资金，T+1日再解冻
                if ($order->fee_payer == 1) {
                    $freezeMoney = round($money - $order->fee - $smsPrice, 3);
                } else {
                    $freezeMoney = round($money - $smsPrice, 3);
                }

                //记录这张订单最终的商家收入是多少
                $order->finally_money = $freezeMoney;
                $order->save();

                if ($freezeMoney >= 0) {
                    //加入自动解冻队列
//                    $unfreezeTime = time() + 86400; //订单冻结24小时
                     $unfreezeTime = strtotime(date('Y-m-d', $time)) + 86400; //次日凌晨解冻

                    if (1 == $order->settlement_type) {
                        // T1 结算
                        //冻结金额
                        Db::table('user')->where('id', $user['id'])->update(['money' => ['exp', 'money-' . $freezeMoney], 'freeze_money' => ['exp', 'freeze_money+' . $freezeMoney]]);
                        //当前余额
                        $balance = round($balance - $freezeMoney, 3);
                        record_user_money_log('freeze', $user['id'], -$freezeMoney, $balance, "冻结订单：{$order->trade_no}，冻结金额：{$freezeMoney}元");

                        Db::table('auto_unfreeze')->insert([
                            'trade_no'      => $order->trade_no,
                            'user_id'       => $user['id'],
                            'money'         => $freezeMoney,
                            'unfreeze_time' => $unfreezeTime,
                            'created_at'    => $time,
                        ]);
                    } elseif (0 == $order->settlement_type) {
                        // T0 结算，补一张0元冻结记录，用于投诉
                        record_user_money_log('freeze', $user['id'], 0, $balance, "冻结订单：{$order->trade_no}，冻结金额：0元(T0 计算)");
                        Db::table('auto_unfreeze')->insert([
                            'trade_no'      => $order->trade_no,
                            'user_id'       => $user['id'],
                            'money'         => 0,
                            'unfreeze_time' => $unfreezeTime,
                            'created_at'    => $time,
                        ]);
                    }
                }
            }

            Db::commit();

            $notify = new Sell();
            $notify->notify($order, $freezeMoney);
        } catch (\Exception $e) {
            Db::rollback();
            // 记录错误订单
            record_file_log('complete_error', $order->trade_no . $e->getMessage());
            record_file_log('complete_error', $e->getTraceAsString());
            die('error');
        }

        // 自动检测开启自动提现
        //自动提现功能改版，每天触发一次
        /* $user= UserModel::get($order->user_id);

        if($user->is_freeze != 1 && $user->collect && sysconf('cash_status') == 1 && sysconf('auto_cash') == 1 && sysconf('auto_cash_money') <= $user->money){
        // 今日可提现次数
        $todayTime=strtotime(date('Y-m-d'));
        $todayCount=CashModel::where(['user_id'=>$user->id,'create_at'=>['>=',$todayTime]])->count();
        $limitNum=(int)sysconf('cash_limit_num');
        $todayCanCashNum = $limitNum - $todayCount;
        $todayCanCashNum = $todayCanCashNum<0?0:$todayCanCashNum;

        // 检测今日提现次数
        if($todayCanCashNum > 0){
        $money=$user->money;
        // 收款信息
        $collect_info='';
        $collect=$user->collect;
        switch($collect->type){
        case 1: //支付宝
        $collect_info .="支付宝账号：{$collect->info['account']}<br>";
        $collect_info .="真实姓名：{$collect->info['realname']}<br>";
        $collect_info .="身份证号：{$collect->info['idcard_number']}";
        break;
        case 2: //微信
        $collect_info .="微信账号：{$collect->info['account']}<br>";
        $collect_info .="真实姓名：{$collect->info['realname']}<br>";
        $collect_info .="身份证号：{$collect->info['idcard_number']}";
        break;
        case 3: //银行
        $collect_info .="开户银行：{$collect->info['bank_name']}<br>";
        $collect_info .="开户地址：{$collect->info['bank_branch']}<br>";
        $collect_info .="收款账号：{$collect->info['bank_card']}<br>";
        $collect_info .="真实姓名：{$collect->info['realname']}<br>";
        $collect_info .="身份证号：{$collect->info['idcard_number']}";
        break;
        }

        // 申请提现
        Db::startTrans();
        try{
        $user->money-=$money;
        $user->save();

        // 记录用户金额变动日志
        $reason = "申请提现金额{$money}元";
        record_user_money_log('apply_cash',$user->id,-$money,$user->money,$reason);
        // 获取提现手续费
        $fee=get_cash_fee($money);
        // 记录提现日志
        CashModel::create([
        'user_id'      =>$user->id,
        'type'         =>$collect->type,
        'collect_info' =>$collect_info,
        'collect_img'  => $collect->collect_img,
        'auto_cash'    => 1,
        'money'        =>$money,
        'fee'          =>$fee,
        'actual_money' =>round($money-$fee,2),
        'status'       =>0,
        'create_at'    =>$_SERVER['REQUEST_TIME'],
        ]);

        Db::commit();
        }catch(\Exception $e){
        Db::rollback();
        record_user_money_log('apply_cash',$user->id,-$money,$user->money, "自动提现失败，原因" . $e->getMessage());
        };
        MerchantLogService::write('提现申请成功',  $reason);
        }
        } */
        return true;
    }
}
