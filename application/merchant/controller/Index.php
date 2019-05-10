<?php

namespace app\merchant\controller;

use think\Controller;
use think\Db;
use think\Request;

use app\common\model\UserChannel as UserChannelModel;
use app\common\model\Channel as ChannelModel;
use app\common\model\Cash as CashModel;
use app\common\model\Order as OrderModel;
use app\common\model\Article as ArticleModel;
use app\common\model\ArticleCategory as ArticleCategoryModel;
use think\Config;

class Index extends Base {
    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index() {
        // 今日交易统计
        date_default_timezone_set('Asia/Shanghai');
        $todayTime = strtotime(date('Y-m-d'));

        // 总收入
        $today['transaction_money'] = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['>=', $todayTime],
        ])->sum('total_price');

        // 总手续费
        $today['total_fee'] = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['>=', $todayTime],
            'fee_payer'  => 1,
        ])->sum('`fee`');

        // 总短信费
        $today['total_sms_price'] = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['>=', $todayTime],
            'sms_payer'  => 1,
            'sms_price'  => ['>=', 0],
        ])->sum('`sms_price`');

        // 总成本
        $today['total_cost_price'] = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['>=', $todayTime],
        ])->sum('`total_cost_price`');

        // 计算利润
        $today['profit'] = bcsub($today['transaction_money'], $today['total_fee'], 4);
        $today['profit'] = bcsub($today['transaction_money'], $today['total_sms_price'], 4);
        $today['profit'] = bcsub($today['transaction_money'], $today['total_cost_price'], 4);

        $today['count'] = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['>=', $todayTime],
        ])->count();
        $this->assign('today', $today);


        //今日支付通道统计
        //获取商家已选支付通道
        $payChannelIds = UserChannelModel::where([
            'user_id' => $this->user->id,
            'status'  => UserChannelModel::$OFF
        ])->field('channel_id')->select();

        $ids = [];
        foreach ($payChannelIds as $v) {
            $ids[] = $v['channel_id'];
        }

        $payChannel = [];

        if (!empty($ids)) {
            $payChannel = ChannelModel::where('id', 'NOT IN', implode(',', $ids))->where('status', '=', 1)->field('id,title')->select();
        } else {
            $payChannel = ChannelModel::where('status', '=', 1)->field('id,title')->select();
        }

        foreach ($payChannel as $k => &$v) {
            $v['total'] = $dealStat['ordernum'] = OrderModel::where([
                    'user_id'    => $this->user->id,
                    'status'     => 1,
                    'success_at' => ['>=', $todayTime],
                    'channel_id' => $v['id'],
                ])->count() + 0.0;
        }

        $this->assign('payStatis', $payChannel);

        // 月度统计
        $monthStartTime   = strtotime(date('Y-m-1'));
        $monthOrdersCount = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['>=', $monthStartTime],
        ])->count();
        if ($monthOrdersCount < 5000) {
            $monthOrders = OrderModel::where([
                'user_id'    => $this->user->id,
                'status'     => 1,
                'success_at' => ['>=', $monthStartTime],
            ])->select();
            $monthStatis = [];
            foreach ($monthOrders as $order) {
                $day = date('Y-m-d', $order->create_at);
                if (isset($monthStatis[$day])) {
                    $monthStatis[$day]['profit']            += $order->total_price - $order->fee - $order->sms_price;
                    $monthStatis[$day]['transaction_money'] += $order->total_price;
                } else {
                    $monthStatis[$day]['profit']            = $order->total_price - $order->fee - $order->sms_price;
                    $monthStatis[$day]['transaction_money'] = $order->total_price;
                }
            }
        } else {
            $index = 0;
            while ($index < $monthOrdersCount) {
                $monthOrders = OrderModel::where([
                    'user_id'    => $this->user->id,
                    'status'     => 1,
                    'success_at' => ['>=', $monthStartTime],
                ])->limit($index, 5000)->select();
                $monthStatis = [];
                foreach ($monthOrders as $order) {
                    $day = date('Y-m-d', $order->create_at);
                    if (isset($monthStatis[$day])) {
                        $monthStatis[$day]['profit']            += $order->total_price - $order->fee - $order->sms_price;
                        $monthStatis[$day]['transaction_money'] += $order->total_price;
                    } else {
                        $monthStatis[$day]['profit']            = $order->total_price - $order->fee - $order->sms_price;
                        $monthStatis[$day]['transaction_money'] = $order->total_price;
                    }
                }
                $index += 5000;
            }
        }
        $month = date('Y-m');
        $n     = date('d');
        for ($i = 1; $i <= $n; $i++) {
            $day = $month . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            if (!isset($monthStatis[$day])) {
                $monthStatis[$day]['profit']            = 0;
                $monthStatis[$day]['transaction_money'] = 0;
            }
        }
        $this->assign('monthStatis', $monthStatis);

        // 最近10笔订单
        $orders = OrderModel::where([
            'user_id' => $this->user->id,
        ])->order('id desc')->limit(10)->select();
        $this->assign('orders', $orders);

        // 公告
        $category = ArticleCategoryModel::get(['alias' => 'notice', 'status' => 1]);
        $articles = [];
        if ($category) {
            $articles = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])
                                    ->order('top desc,id desc')->limit(20)->select();
        }

        //今日成交统计
        $dealStat['cardnum']  = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['>=', $todayTime],
        ])->sum('quantity');

        //昨日订单数
        $dealStat['yesterday_order_num'] = OrderModel::where([
            'user_id'    => $this->user->id,
            'status'     => 1,
            'success_at' => ['between', [$todayTime - (24 * 3600), $todayTime]],
        ])->count();

        //用户余额
        $dealStat['money'] = $this->user->money;

        //最后一次提现金额
        $dealStat['last_cash'] = CashModel::where([
            'user_id' => $this->user->id,
            'status'  => 1,
        ])->order('id desc')->value('money');

        $this->assign('dealStat', $dealStat);
        $this->assign('articles', $articles);
        return $this->fetch();
    }

    // 公告
    public function notice() {
        $article_id = input('article_id/d', 0);
        $article    = ArticleModel::get(['id' => $article_id, 'status' => 1]);
        if (!$article) {
            $this->error('不存在该文章！');
        }
        $this->assign('article', $article);
        echo $this->fetch();
    }
}
