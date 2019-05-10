<?php
/**
 * 订单管理
 */

namespace app\manage\controller;

use app\common\model\Channel as ChannelModel;
use app\common\model\Order as OrderModel;
use controller\BasicAdmin;
use service\LogService;
use think\Db;
use think\Exception;
use think\Request;

class Order extends BasicAdmin
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('self_url', '#' . Request::instance()->url());
        $this->assign('self_no_url', Request::instance()->url());
    }

    public function index()
    {
        $this->assign('title', '订单列表');

        ////////////////// 查询条件 //////////////////
        $query = [
            'date_type' => input('date_type/s', ''),
            'username' => input('username/s', ''),
            'contact' => input('contact/s', ''),
            'trade_no' => input('trade_no/s', ''),
            'transaction_id' => input('transaction_id/s', ''),
            'channel_id' => input('channel_id/s', ''),
            'paytype' => input('paytype/s', ''),
            'status' => input('status/s', ''),
            'is_freeze' => input('is_freeze/s', ''),
            'date_range' => input('date_range/s', ''),
            'card' => input('card/s', ''),
        ];
        $where = $this->genereate_where($query);
        // 订单列表
        $orderModel = new OrderModel;

        if (input('action/s') === 'dump') {
            $title = ['序号', '订单ID', '账号', '支付订单号', '金额', '支付渠道', '支付方式', '订单状态', '下单时间'];
            $data = [];
            $orders = $orderModel->where($where)->order('id desc')->select();
            foreach ($orders as $k => $v) {
                $channel_title = isset($v->channel) ? '' : $v->channel->title;
                $status = $v['status'] == 1 ? '支付成功' : '未支付';
                $data[] = [
                    $k + 1,
                    $v['id'],
                    $v->user->username,
                    $v->trade_no,
                    $v->total_price,
                    $channel_title,
                    get_paytype_name($v->paytype, 1),
                    $status,
                    date('Y-m-d H:i:s', $v->create_at),
                ];
            }
            generate_excel($title, $data, 'orderQuery' . date('YmdHis'), '订单查询');
        }

        $show = input('show/s', 'sheet');
        // 表
        if ($show == 'sheet') {
            $orders = $orderModel->where($where)->order('id desc')->paginate(30, false, [
                'query' => $query,
            ]);
            // 分页
            $page = str_replace('href="', 'href="#', $orders->render());
            $this->assign('page', $page);
            $this->assign('orders', $orders);
        } else {
            // 图表
            $where = $this->genereate_where($query);
            $chart_data = OrderModel::where($where)->select();

            if ($query['date_range'] && strpos($query['date_range'], ' - ') !== false) {
                list($startTime, $endTime) = explode(' - ', $query['date_range']);
                $startTime = strtotime($startTime);
                $endTime = strtotime($endTime);
                $diff = floor(($endTime - $startTime) / 86400);
            } else {
                $endTime = $_SERVER['REQUEST_TIME'];
                if (isset($chart_data[0])) {
                    $startTime = strtotime(date('Y-m-d', $chart_data[0]->create_at));
                    $diff = floor(($endTime - $startTime) / 86400);
                } else {
                    $diff = 0;
                }
            }
            $data = [];
            foreach ($chart_data as $v) {
                $day = date('Y-m-d', $v->create_at);
                if (!isset($data[$day])) {
                    $data[$day]['actual_money'] = 0;
                    $data[$day]['transaction_money'] = 0;
                }
                $data[$day]['actual_money'] += $v->total_price - $v->fee;
                $data[$day]['transaction_money'] += $v->total_price;
            }
            // 补上空数据
            for ($i = 0; $i <= $diff; $i++) {
                $day = date('Y-m-d', strtotime(-$i . 'day', $endTime));
                if (!isset($data[$day])) {
                    $data[$day]['actual_money'] = 0;
                    $data[$day]['transaction_money'] = 0;
                }
            }
            // 排序
            ksort($data);
            $statis_chart['title'] = '"' . join('","', array_keys($data)) . '"';
            $statis_chart['actual_money'] = join(',', array_column($data, 'actual_money'));
            $statis_chart['transaction_money'] = join(',', array_column($data, 'transaction_money'));
            $this->assign('statis_chart', $statis_chart);
        }
        $sum_money = $orderModel->where($where)->sum('total_price');
        $this->assign('sum_money', $sum_money);
        $sum_order = $orderModel->where($where)->count();
        $this->assign('sum_order', $sum_order);

        $channels = ChannelModel::all();
        $this->assign('channels', $channels);
        $this->assign('channel_paytype', get_paytype_list());
        return $this->fetch();
    }

    /**
     * 生成查询条件
     */
    protected function genereate_where($params)
    {
        $where = [];
        $action = Request::instance()->action();
        switch ($action) {
            case 'index':
                switch ($params['date_type']) {
                    case 'day':
                        $where['create_at'] = ['between', [strtotime(date('Y-m-d') . ' 00:00:00'), strtotime(date('Y-m-d') . ' 23:59:59')]];
                        break;
                    case 'week':
                        $where['create_at'] = ['between', [strtotime('-1 week'), strtotime(date('Y-m-d') . ' 23:59:59')]];
                        break;
                    case 'month':
                        $where['create_at'] = ['between', [strtotime(date('Y-m-1') . ' 00:00:00'), strtotime(date('Y-m-d') . ' 23:59:59')]];
                        break;
                    default:
                        break;
                }
                if ($params['username']) {
                    $where['user_id'] = Db::name('User')->where(['username' => $params['username']])->value('id');
                }
                if ($params['contact']) {
                    $where['contact'] = $params['contact'];
                }
                if ($params['trade_no']) {
                    $where['trade_no'] = $params['trade_no'];
                }
                if ($params['transaction_id']) {
                    $where['transaction_id'] = $params['transaction_id'];
                }
                if ($params['channel_id'] !== '') {
                    $where['channel_id'] = $params['channel_id'];
                }
                if ($params['paytype'] !== '') {
                    $where['paytype'] = $params['paytype'];
                }
                if ($params['status'] !== '') {
                    $where['status'] = $params['status'];
                }
                if ($params['is_freeze'] !== '') {
                    $where['is_freeze'] = $params['is_freeze'];
                }
                if ($params['date_range'] && strpos($params['date_range'], ' - ') !== false) {
                    list($startDate, $endTime) = explode(' - ', $params['date_range']);
                    $where['create_at'] = ['between', [strtotime($startDate . ' 00:00:00'), strtotime($endTime . ' 23:59:59')]];
                }
                if ($params['card']) {
                    $res = Db::name('order_card')->where([
                        'number|secret' => ['like', "%{$params['card']}%"],
                    ])->select();
                    if ($res) {
                        $ids = [];
                        foreach ($res as $re) {
                            $ids[] = $re['order_id'];
                        }

                        if (!empty($ids)) {
                            $where['id'] = ['IN', implode(',', $ids)];
                        } else {
                            $where['id'] = ['=', '0'];
                        }
                    } else {
                        $where['id'] = ['=', '0'];
                    }
                }
                break;
            case 'merchant':
                if ($params['username']) {
                    $where['user_id'] = Db::name('User')->where(['username' => $params['username']])->value('id');
                }
                if ($params['channel_id'] !== '') {
                    $where['channel_id'] = $params['channel_id'];
                }
                if ($params['paytype'] !== '') {
                    $where['paytype'] = $params['paytype'];
                }
                if ($params['status'] !== '') {
                    $where['status'] = $params['status'];
                }
                if ($params['date_range'] && strpos($params['date_range'], ' - ') !== false) {
                    list($startDate, $endTime) = explode(' - ', $params['date_range']);
                    $where['create_at'] = ['between', [strtotime($startDate . ' 00:00:00'), strtotime($endTime . ' 23:59:59')]];
                }
                break;
            case 'channel':
                if ($params['username']) {
                    $where['user_id'] = Db::name('User')->where(['username' => $params['username']])->value('id');
                }
                if ($params['channel_id'] !== '') {
                    $where['channel_id'] = $params['channel_id'];
                }
                if ($params['paytype'] !== '') {
                    $where['paytype'] = $params['paytype'];
                }
                if ($params['status'] !== '') {
                    $where['status'] = $params['status'];
                }
                if ($params['date_range'] && strpos($params['date_range'], ' - ') !== false) {
                    list($startDate, $endTime) = explode(' - ', $params['date_range']);
                    $where['create_at'] = ['between', [strtotime($startDate . ' 00:00:00'), strtotime($endTime . ' 23:59:59')]];
                }
                break;
            case 'hour':
                if ($params['username']) {
                    $where['user_id'] = Db::name('User')->where(['username' => $params['username']])->value('id');
                }
                if ($params['date'] !== '') {
                    $startTime = strtotime($params['date']);
                    $endTime = $startTime + 86400 - 1;
                    $where['create_at'] = ['between', [$startTime, $endTime]];
                }
                break;
        }
        return $where;
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $id = input('id/d', 0);
        if ($id) {
            $order = OrderModel::get($id);
        } else {
            $trade = input('trade_no/s', '');
            $order = OrderModel::get(['trade_no' => $trade]);
        }

        if (!$order) {
            $this->error('不存在该订单！');
        }
        $this->assign('order', $order);
        return view();
    }

    /**
     * 商户分析
     */
    public function merchant()
    {
        $this->assign('title', '商户分析');

        ////////////////// 查询条件 //////////////////
        $query = [
            'username' => input('username/s', ''),
            'channel_id' => input('channel_id/s', ''),
            'paytype' => input('paytype/s', ''),
            'status' => input('status/s', ''),
            'date_range' => input('date_range/s', date('Y-m-d - Y-m-d')),
        ];
        $where = $this->genereate_where($query);

        $ordersCount = OrderModel::where($where)->count();
        $statis = [];
        if ($ordersCount < 5000) {
            $orders = OrderModel::where($where)->select();
            foreach ($orders as $v) {
                if (!isset($statis[$v->user_id])) {
                    $statis[$v->user_id]['user_id'] = $v->user_id;
                    $statis[$v->user_id]['username'] = $v->user->username;
                    $statis[$v->user_id]['count'] = 0;
                    $statis[$v->user_id]['paid'] = 0;
                    $statis[$v->user_id]['unpaid'] = 0;
                    $statis[$v->user_id]['sum_money'] = 0;
                    $statis[$v->user_id]['sum_actual_money'] = 0;
                    $statis[$v->user_id]['sum_platform_money'] = 0;
                }
                $statis[$v->user_id]['count']++;
                if ($v->status == 1) {
                    $statis[$v->user_id]['paid']++;
                    $statis[$v->user_id]['sum_money'] += $v->total_price;
                    $statis[$v->user_id]['sum_actual_money'] += $v->total_price - $v->fee;
                    $statis[$v->user_id]['sum_platform_money'] += $v->fee - $v->agent_fee;
                } else {
                    $statis[$v->user_id]['unpaid']++;
                    $statis[$v->user_id]['sum_money'] += $v->total_price;
                }
            }
        }else{
            $index = 0;
            while ($index < $ordersCount) {
                $orders = OrderModel::where($where)->limit($index,5000)->select();
                foreach ($orders as $v) {
                    if (!isset($statis[$v->user_id])) {
                        $statis[$v->user_id]['user_id'] = $v->user_id;
                        $statis[$v->user_id]['username'] = $v->user->username;
                        $statis[$v->user_id]['count'] = 0;
                        $statis[$v->user_id]['paid'] = 0;
                        $statis[$v->user_id]['unpaid'] = 0;
                        $statis[$v->user_id]['sum_money'] = 0;
                        $statis[$v->user_id]['sum_actual_money'] = 0;
                        $statis[$v->user_id]['sum_platform_money'] = 0;
                    }
                    $statis[$v->user_id]['count']++;
                    if ($v->status == 1) {
                        $statis[$v->user_id]['paid']++;
                        $statis[$v->user_id]['sum_money'] += $v->total_price;
                        $statis[$v->user_id]['sum_actual_money'] += $v->total_price - $v->fee;
                        $statis[$v->user_id]['sum_platform_money'] += $v->fee - $v->agent_fee;
                    } else {
                        $statis[$v->user_id]['unpaid']++;
                        $statis[$v->user_id]['sum_money'] += $v->total_price;
                    }
                }
                $index += 5000;
            }
        }
        // 合计

        $counts['user_id'] = '合计';
        $counts['username'] = '共' . count($statis) . '个商户';
        // 提交订单数
        $counts['count'] = array_sum(array_column($statis, 'count'));
        // 已付订单数
        $counts['paid'] = array_sum(array_column($statis, 'paid'));
        // 未付订单数
        $counts['unpaid'] = array_sum(array_column($statis, 'unpaid'));
        // 订单总金额
        $counts['sum_money'] = array_sum(array_column($statis, 'sum_money'));
        // 订单实收金额
        $counts['sum_actual_money'] = array_sum(array_column($statis, 'sum_actual_money'));
        // 平台收入
        $counts['sum_platform_money'] = array_sum(array_column($statis, 'sum_platform_money'));

        // 导出渠道分析
        if (input('action/s', '') == 'dump') {
            $title = ['商户ID', '商户名称', '提交订单', '已付订单', '未付订单', '提交金额', '商户收入', '平台收入'];
            $data = array_map('array_values', $statis);
            $data[] = array_values($counts);
            generate_excel($title, $data, '商户分析_' . date('YmdHis'), '商户分析');
        }

        $this->assign('statis', $statis);
        $this->assign('counts', $counts);

        $channels = ChannelModel::all();
        $this->assign('channels', $channels);
        $this->assign('channel_paytype', get_paytype_list());
        return $this->fetch();
    }

    /**
     * 渠道分析
     */
    public function channel()
    {
        $this->assign('title', '渠道分析');

        ////////////////// 查询条件 //////////////////
        $query = [
            'username' => input('username/s', ''),
            'channel_id' => input('channel_id/s', ''),
            'paytype' => input('paytype/s', ''),
            'status' => input('status/s', ''),
            'date_range' => input('date_range/s', date('Y-m-d - Y-m-d')),
        ];
        $where = $this->genereate_where($query);

        $ordersCount = OrderModel::where($where)->count();
        $statis = [];
        if ($ordersCount < 5000) {
            $orders = OrderModel::where($where)->select();
            foreach ($orders as $v) {
                if (!isset($statis[$v->channel_id])) {
                    $statis[$v->channel_id]['channel_id'] = $v->channel_id;
                    $statis[$v->channel_id]['title'] = $v->channel->title;
                    $statis[$v->channel_id]['count'] = 0;
                    $statis[$v->channel_id]['paid'] = 0;
                    $statis[$v->channel_id]['unpaid'] = 0;
                    $statis[$v->channel_id]['sum_money'] = 0;
                    $statis[$v->channel_id]['sum_actual_money'] = 0;
                    $statis[$v->channel_id]['sum_platform_money'] = 0;
                }
                $statis[$v->channel_id]['count']++;
                if ($v->status == 1) {
                    $statis[$v->channel_id]['paid']++;
                    $statis[$v->channel_id]['sum_money'] += $v->total_price;
                    $statis[$v->channel_id]['sum_actual_money'] += $v->total_price - $v->fee;
                    $statis[$v->channel_id]['sum_platform_money'] += $v->fee - $v->agent_fee;
                } else {
                    $statis[$v->channel_id]['unpaid']++;
                    $statis[$v->channel_id]['sum_money'] += $v->total_price;
                }
            }
        }else{
            $index = 0;
            while ($index < $ordersCount) {
                $orders = OrderModel::where($where)->limit($index,5000)->select();
                foreach ($orders as $v) {
                    if (!isset($statis[$v->channel_id])) {
                        $statis[$v->channel_id]['channel_id'] = $v->channel_id;
                        $statis[$v->channel_id]['title'] = $v->channel->title;
                        $statis[$v->channel_id]['count'] = 0;
                        $statis[$v->channel_id]['paid'] = 0;
                        $statis[$v->channel_id]['unpaid'] = 0;
                        $statis[$v->channel_id]['sum_money'] = 0;
                        $statis[$v->channel_id]['sum_actual_money'] = 0;
                        $statis[$v->channel_id]['sum_platform_money'] = 0;
                    }
                    $statis[$v->channel_id]['count']++;
                    if ($v->status == 1) {
                        $statis[$v->channel_id]['paid']++;
                        $statis[$v->channel_id]['sum_money'] += $v->total_price;
                        $statis[$v->channel_id]['sum_actual_money'] += $v->total_price - $v->fee;
                        $statis[$v->channel_id]['sum_platform_money'] += $v->fee - $v->agent_fee;
                    } else {
                        $statis[$v->channel_id]['unpaid']++;
                        $statis[$v->channel_id]['sum_money'] += $v->total_price;
                    }
                }
                $index += 5000;
            }
        }
        // 合计

        $counts['channel_id'] = '合计';
        $counts['title'] = '共' . count($statis) . '个渠道';
        // 提交订单数
        $counts['count'] = array_sum(array_column($statis, 'count'));
        // 已付订单数
        $counts['paid'] = array_sum(array_column($statis, 'paid'));
        // 未付订单数
        $counts['unpaid'] = array_sum(array_column($statis, 'unpaid'));
        // 订单总金额
        $counts['sum_money'] = array_sum(array_column($statis, 'sum_money'));
        // 订单实收金额
        $counts['sum_actual_money'] = array_sum(array_column($statis, 'sum_actual_money'));
        // 平台收入
        $counts['sum_platform_money'] = array_sum(array_column($statis, 'sum_platform_money'));

        // 导出渠道分析
        if (input('action/s', '') == 'dump') {
            $title = ['渠道ID', '渠道名称', '提交订单', '已付订单', '未付订单', '提交金额', '商户收入', '平台收入'];
            $data = array_map('array_values', $statis);
            $data[] = array_values($counts);
            generate_excel($title, $data, '渠道分析_' . date('YmdHis'), '渠道分析');
        }

        $this->assign('statis', $statis);
        $this->assign('counts', $counts);

        $channels = ChannelModel::all();
        $this->assign('channels', $channels);
        $this->assign('channel_paytype', get_paytype_list());
        return $this->fetch();
    }

    /**
     * 实时数据
     */
    public function hour()
    {
        $this->assign('title', '实时数据');

        ////////////////// 查询条件 //////////////////
        $query = [
            'username' => input('username/s', ''),
            'date' => input('date/s', date('Y-m-d')),
        ];
        $where = $this->genereate_where($query);

        $ordersCount = OrderModel::where($where)->count();
        $statis = [];
        if ($ordersCount < 5000) {
            $orders = OrderModel::where($where)->select();
            foreach ($orders as $v) {
                $hour = date('H:00', $v->create_at);
                if (!isset($statis[$hour])) {
                    $statis[$hour]['hour'] = $hour;
                    $statis[$hour]['count'] = 0;
                    $statis[$hour]['paid'] = 0;
                    $statis[$hour]['unpaid'] = 0;
                    $statis[$hour]['sum_money'] = 0;
                    $statis[$hour]['sum_actual_money'] = 0;
                    $statis[$hour]['sum_platform_money'] = 0;
                }
                $statis[$hour]['count']++;
                if ($v->status == 1) {
                    $statis[$hour]['paid']++;
                    $statis[$hour]['sum_money'] += $v->total_price;
                    $statis[$hour]['sum_actual_money'] += $v->total_price - $v->fee;
                    $statis[$hour]['sum_platform_money'] += $v->fee - $v->agent_fee;
                } else {
                    $statis[$hour]['unpaid']++;
                    $statis[$hour]['sum_money'] += $v->total_price;
                }
            }
        }else{
            $index = 0;
            while ($index < $ordersCount) {
                $orders = OrderModel::where($where)->limit($index, 5000)->select();
                foreach ($orders as $v) {
                    $hour = date('H:00', $v->create_at);
                    if (!isset($statis[$hour])) {
                        $statis[$hour]['hour'] = $hour;
                        $statis[$hour]['count'] = 0;
                        $statis[$hour]['paid'] = 0;
                        $statis[$hour]['unpaid'] = 0;
                        $statis[$hour]['sum_money'] = 0;
                        $statis[$hour]['sum_actual_money'] = 0;
                        $statis[$hour]['sum_platform_money'] = 0;
                    }
                    $statis[$hour]['count']++;
                    if ($v->status == 1) {
                        $statis[$hour]['paid']++;
                        $statis[$hour]['sum_money'] += $v->total_price;
                        $statis[$hour]['sum_actual_money'] += $v->total_price - $v->fee;
                        $statis[$hour]['sum_platform_money'] += $v->fee - $v->agent_fee;
                    } else {
                        $statis[$hour]['unpaid']++;
                        $statis[$hour]['sum_money'] += $v->total_price;
                    }
                }
                $index += 5000;
            }
        }

        // 合计

        $counts['hour'] = '合计';
        // 提交订单数
        $counts['count'] = array_sum(array_column($statis, 'count'));
        // 已付订单数
        $counts['paid'] = array_sum(array_column($statis, 'paid'));
        // 未付订单数
        $counts['unpaid'] = array_sum(array_column($statis, 'unpaid'));
        // 订单总金额
        $counts['sum_money'] = array_sum(array_column($statis, 'sum_money'));
        // 订单实收金额
        $counts['sum_actual_money'] = array_sum(array_column($statis, 'sum_actual_money'));
        // 平台收入
        $counts['sum_platform_money'] = array_sum(array_column($statis, 'sum_platform_money'));

        // 导出渠道分析
        if (input('action/s', '') == 'dump') {
            $title = ['时间段', '提交订单', '已付订单', '未付订单', '提交金额', '商户收入', '平台收入'];
            $data = array_map('array_values', $statis);
            $data[] = array_values($counts);
            generate_excel($title, $data, '实时数据_' . date('YmdHis'), '实时数据');
        }

        $this->assign('statis', $statis);
        $this->assign('counts', $counts);
        return $this->fetch();
    }

    /**
     * 改变冻结状态
     */
    public function change_freeze_status()
    {
        if (!$this->request->isAjax()) {
            $this->error('错误的提交方式！');
        }
        $id = input('id/d', 0);
        $status = input('value/d', 1);
        $order = OrderModel::get(['id' => $id, 'status' => 1]);
        if (!$order) {
            $this->error('不存在该订单！');
        }

        $user = $order->user;

        //检查当前订单是否已经解冻
        $unfreeze = Db::name('auto_unfreeze')->where(['trade_no' => $order->trade_no])->find();
        if (empty($unfreeze)) {
            //订单已解冻，冻结商户余额
            if ($status == 1) {
                $remark = '冻结';
                // 冻结
                if ($user->money < $order->total_price) {
                    $this->error('冻结失败！用户余额不足冻结该订单！');
                }
            } else {
                $remark = '解冻';
                // 解冻
                if ($user->freeze_money < $order->total_price) {
                    $this->error('解冻失败！用户冻结余额不足解冻该订单！');
                }
            }
        }

        Db::startTrans();
        try {
            $order->is_freeze = $status;
            $order->save();
            if ($status == 1) {
                $remark = '冻结';

                if (empty($unfreeze)) {
                    // 已解冻订单，进行余额冻结
                    $user->money -= $order->total_price;
                    $user->freeze_money += $order->total_price;
                    $user->save();
                    // 记录用户金额变动日志
                    record_user_money_log('freeze', $user->id, -$order->total_price, $user->money, "后台冻结订单：{$order->trade_no}，冻结金额：{$order->total_price}元");
                } else {
                    // 未解冻订单，不允许自动解冻
                    Db::table('auto_unfreeze')->where(['trade_no' => $order->trade_no])->update(['status' => -1]);

                    record_user_money_log('freeze', $user->id, -0, $user->money, "后台冻结订单：{$order->trade_no}，冻结金额：0元（订单收入尚未解冻）。");
                }
            } else {
                $remark = '解冻';

                // 如果有投诉
                $complaint = Db::name('complaint')->where(['trade_no' => $order->trade_no])->find();

                if (empty($complaint) || ($complaint['status'] == 1 && $complaint['result'] == 1)) {
                    // 没有投诉,或者有投诉，但是商家胜诉，可以进行解冻
                    if (empty($unfreeze)) {
                        // 如果订单已经解冻，直接返回冻结余额
                        $user->money += $order->total_price;
                        $user->freeze_money -= $order->total_price;
                        $user->save();
                        // 记录用户金额变动日志
                        record_user_money_log('unfreeze', $user->id, $order->total_price, $user->money, "后台解冻订单：{$order->trade_no}，解冻金额：{$order->total_price}元");
                    } else {
                        // 如果订单未解冻，让订单自动解冻
                        Db::table('auto_unfreeze')->where(['trade_no' => $order->trade_no])->update(['status' => 1]);

                        record_user_money_log('unfreeze', $user->id, 0, $user->money, "后台解冻订单：{$order->trade_no}，解冻金额：0元 (订单收入尚未解冻)。");
                    }
                } else {
                    throw new Exception('该订单有投诉，不允许解冻');
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $this->error('更新失败，原因' . $e->getMessage());
        }
        LogService::write('订单管理', $remark . '订单成功，ID:' . $id);
        $this->success('更新成功！', '');
    }

    /**
     * 删除订单
     */
    public function del()
    {
        $id = input('id/d', 0);
        $order = OrderModel::get($id);
        if (!$order) {
            $this->error('不存在该订单！');
        }
        if ($order['status']) {
            $this->error('不能删除支付成功订单！');
        }
        $re = OrderModel::where(['id' => $id])->delete();
        if (false !== $re) {
            LogService::write('订单管理', '删除订单成功，ID:' . $id);
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 获取删除订单短信
     * @throws \think\exception\DbException
     */
    public function sendDelBatchSms()
    {
        if ($this->request->isPost()) {
            $phone = session('user.phone');
            if ($phone) {
                $smsHelper = new \app\common\util\Sms();
                if ($smsHelper->sendCode($phone, 'delete_order')) {
                    $this->success('短信发送成功，请注意查收');
                } else {
                    $this->error('短信发送失败，请重试');
                }
            } else {
                $this->error('请先补充手机号码信息');
            }
        }
    }

    /**
     * 改变清除无效订单
     */
    public function del_batch()
    {
        if (!$this->request->isPost()) {
            $max_date = date('Y-m-d', strtotime("-3 day"));
            $this->assign('max_date', $max_date);
            return view();
        }

        $chcode = input('chcode');
        if ($chcode) {
            $smsHelper = new \app\common\util\Sms();
            if (!$smsHelper->verifyCode(session('user.phone'), $chcode, 'delete_order')) {
                $this->error($smsHelper->getError());
            }
        }

        $date_range = input('order_date_range');
        if ($date_range && strpos($date_range, ' - ') !== false) {
            list($startTime, $endTime) = explode(' - ', $date_range);
            $startTime = strtotime($startTime);
            $endTime = strtotime($endTime);
            if ($startTime > $endTime) {
                $this->error('时间范围错误');
            }
            $date = strtotime(date('Y-m-d', strtotime("-3 day")));
            if ($startTime > $date) {
                $this->error('不可删除3天内订单');
            }
        } else {
            $this->error('请选择时间范围');
        }

        $where['status'] = 0;
        $where['create_at'] = ['BETWEEN', [$startTime, $endTime]];
        $count = Db::name('order')->where($where)->count();
        if ($count == 0) {
            $this->error('该日期范围没有无效订单！');
        }
        $res = Db::name('order')->where($where)->delete();
        if ($res) {
            LogService::write('订单管理', '批量删除订单成功，删除数量：' . $count);
            $this->success('成功删除' . $res . '个无效订单！');
        } else {
            $this->error('删除失败！');
        }
    }
}
