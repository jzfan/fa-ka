<?php
namespace app\manage\controller;

use controller\BasicAdmin;
use think\Db;
use think\Request;

use app\common\model\User as UserModel;
use app\common\model\Order as OrderModel;
use app\common\model\Cash as CashModel;
use app\common\model\Channel as ChannelModel;

class Index extends BasicAdmin
{
    public function main()
    {
        $todayTime  =strtotime(date('Y-m-d'));
        $yesterTime =$todayTime-86400;
        /////////////////// 用户信息 ///////////////////

        // 今日注册
        $userStatis['today_register']  =UserModel::where(['create_at' =>['>',$todayTime]])->count();
        // 昨日注册
        $userStatis['yester_register'] =UserModel::where(['create_at' =>['between',[$yesterTime,$todayTime-1]]])->count();
        // 未审核
        $userStatis['notpass_count']   =UserModel::where(['status'=>0])->count();
        // 已冻结
        $userStatis['is_freeze_count'] =UserModel::where(['is_freeze' =>1])->count();

        /////////////////// 订单信息 ///////////////////

        // 今日提交
        $orderStatis['today_quantity'] =OrderModel::where(['create_at' =>['>',$todayTime]])->count();
        // 今日未付款
        $orderStatis['today_unpaid']   =OrderModel::where(['status'=>0,'create_at'=>['>',$todayTime]])->count();
        // 今日成功订单
        $orderStatis['today_paid']     =OrderModel::where(['status'=>1,'create_at'=>['>',$todayTime]])->count();
        // 昨日成功订单
        $orderStatis['yester_paid']    =OrderModel::where(['status'=>1,'create_at' =>['between',[$yesterTime,$todayTime-1]]])->count();

        /////////////////// 收入信息 ///////////////////

        // 今日付款总额
        $incomeStatis['today_sum'] =OrderModel::where(['status'=>1,'create_at'=>['>',$todayTime]])->sum('total_price');
        // 今日用户收入
        $incomeStatis['today_actual_sum'] =OrderModel::where(['status'=>1,'create_at'=>['>',$todayTime]])->sum('total_price-fee');
        // 今日用户总利润
        $incomeStatis['today_profit_sum'] =OrderModel::where(['status'=>1,'create_at'=>['>',$todayTime]])->sum('total_product_price-total_cost_price-(sms_price*sms_payer)');
        // 昨日付款总额
        $incomeStatis['yester_sum'] =OrderModel::where(['status'=>1,'create_at' =>['between',[$yesterTime,$todayTime-1]]])->sum('total_price');
        // 昨日用户收入
        $incomeStatis['yester_actual_sum'] =OrderModel::where(['status'=>1,'create_at' =>['between',[$yesterTime,$todayTime-1]]])->sum('total_price-fee');

        /////////////////// 提现信息 ///////////////////

        // 今日提现总额
        $cashStatis['today_sum']=CashModel::where(['create_at'=>['>',$todayTime]])->sum('money');
        // 今日付款总额
        $cashStatis['today_ok_sum']=CashModel::where(['status'=>1,'create_at'=>['>',$todayTime]])->sum('money');
        // 昨日提现总额
        $cashStatis['yester_sum']=CashModel::where(['create_at' =>['between',[$yesterTime,$todayTime-1]]])->sum('money');
        // 昨日付款总额
        $cashStatis['yester_ok_sum']=CashModel::where(['status'=>1,'create_at' =>['between',[$yesterTime,$todayTime-1]]])->sum('money');

        /////////////////// 支付通道信息 ///////////////////

        $channelStatis['channel']=ChannelModel::where(['status'=>1])->column('title','id');
        $channelStatis['today']=[];
        foreach($channelStatis['channel'] as $id => $title){
            $channelStatis['today'][$id]['title']=$title;
            $channelStatis['today'][$id]['money']=0;
        }

        $orderCount = OrderModel::where(['status' => 1,'create_at' => ['>',$todayTime]])->count();
        if($orderCount < 5000) {
            $todayOrders = OrderModel::all(['status' => 1, 'create_at' => ['>', $todayTime]]);
            foreach ($todayOrders as $order) {
                if (!isset($channelStatis['today'][$order->channel_id])) {
					if(!isset($order->channel->title))continue;
                    $channelStatis['today'][$order->channel_id]['title'] = $order->channel->title;
                    $channelStatis['today'][$order->channel_id]['money'] = $order->total_price;
                } else {
                    $channelStatis['today'][$order->channel_id]['money'] += $order->total_price;
                }
            }
        }else{
            $index = 0;
            while ($index < $orderCount) {
                $todayOrders = OrderModel::where(['status' => 1, 'create_at' => ['>', $todayTime]])->limit($index, 5000)->select();
                foreach ($todayOrders as $order) {
                    if (!isset($channelStatis['today'][$order->channel_id])) {
                        $channelStatis['today'][$order->channel_id]['title'] = $order->channel->title;
                        $channelStatis['today'][$order->channel_id]['money'] = $order->total_price;
                    } else {
                        $channelStatis['today'][$order->channel_id]['money'] += $order->total_price;
                    }
                }
                $index += 5000;
            }
        }

        /////////////////// 最近30日交易信息 ///////////////////

        // 30日统计
		$month_data=OrderModel::where(['status'=>1,'success_at'=>['between',[strtotime('-1 month'),strtotime(date('Y-m-d'). ' 23:59:59')]]])
            ->field('FROM_UNIXTIME(success_at,"%Y-%m-%d") as success_at,sum(total_price) as transaction_money,sum(total_price-fee-sms_price) as actual_money')
            ->group('FROM_UNIXTIME(success_at,"%Y-%m-%d")')->select();
        $data=[];
        foreach($month_data as $v)
        {
            $day=$v->success_at;
            $data[$day]['actual_money']=$v->actual_money;
            $data[$day]['transaction_money']=$v->transaction_money;
        }

        // 补上空数据
        for($i=1; $i <=30 ; $i++) {
            $day=date('Y-m-d',strtotime(-$i.'day'));
            if(!isset($data[$day])){
                $data[$day]['actual_money']=0;
                $data[$day]['transaction_money']=0;
            }
        }
        // 排序
        ksort($data);
        $monthStatis['title']             ='"'.join('","',array_keys($data)).'"';
        $monthStatis['actual_money']      =join(',',array_column($data,'actual_money'));
        $monthStatis['transaction_money'] =join(',',array_column($data,'transaction_money'));

        return $this->fetch('',[
            'yesterday'    =>date('Y-m-d',strtotime('-1 day')),
            'today'        =>date('Y-m-d'),
            'channelStatis'=>$channelStatis,
            'userStatis'   =>$userStatis,
            'orderStatis'  =>$orderStatis,
            'incomeStatis' =>$incomeStatis,
            'cashStatis'   =>$cashStatis,
            'monthStatis'   =>$monthStatis,
            'version_list_url' => get_version_list_url(),
        ]);
    }
}
