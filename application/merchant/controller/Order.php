<?php

namespace app\merchant\controller;

use think\Controller;
use think\Request;
use think\Db;
use app\common\model\Order as OrderModel;
use app\common\model\Goods as GoodsModel;
use app\common\model\OrderCard as OrderCardMoel;
use service\MerchantLogService;

class Order extends Base
{
    // 订单列表
    public function index()
    {
        $this->setTitle('订单列表');
        ////////////////// 查询条件 //////////////////
        $query = [
            'status'  => input('status/s',''),
            'date_range'  => input('date_range/s',''),
            'keywords'  => input('keywords/s', ''),
        	'cid' => input('cid/d', 0),
        	'type' => input('type/d', 0)
        ];
        $paytype = input('paytype/s','');
        if($paytype) {
            $paytypeIds = getPaytypeByProductId($paytype);
            if($paytypeIds) {
                $query['paytype'] = $paytypeIds;
            }
        }
        $where = $this->genereate_where($query);

        $orders=OrderModel::where($where)->order('id desc')->paginate(30,false,[
            'query'=>$query
        ]);
        //获取商品分类
        $categorys = Db::table('goods_category')->where(['user_id'=>$this->user->id])->field('id, name')->select();
        $this->assign('categorys', $categorys);
        // 分页
        $page=$orders->render();
        //支付产品
        $this->assign('pay_product', config('pay_product'));
        $this->assign('page',$page);
        $this->assign('orders',$orders);
        return $this->fetch();
    }
    // 收益分析
    public function analysis()
    {
        $this->setTitle('收益统计');
        $goods = GoodsModel::field('name, id')->where(['user_id'=>$this->user->id])->select();
        ////////////////// 查询条件 //////////////////
        $query = [
            'status'  => 1,
            'date_range'  => input('date_range/s',''),
            'trade_no'  => input('trade_no/s', ''),
            'goods_id'  => input('goods_id/d', '')
        ];
        $paytype = input('paytype/s','');
        if($paytype) {
            $paytypeIds = getPaytypeByProductId($paytype);
            if($paytypeIds) {
                $query['paytype'] = $paytypeIds;
            }
        }
        $where = $this->genereate_where($query);
        //这里用产品总价减去成本价，产品总价不含短信费
        $order_analysis_price_data = OrderModel::where($where)->field('total_price, total_product_price, total_cost_price, sms_payer, sms_price')->select();

        $total_product_price_sum = 0; //产品总价
        $total_cost_price_sum = 0; //产品总成本价
        $sms_price_sum = 0; //短信总价
        foreach($order_analysis_price_data as $item){
            $total_product_price_sum += (float)$item['total_product_price'];
            $total_cost_price_sum += (float)$item['total_cost_price'];
            //是商家承担短信费的，才计算到成本里
            if ($item['sms_payer'] == 1) {
                $sms_price_sum += (float)$item['sms_price'];
            }
        }
        $total_profit = round($total_product_price_sum - $total_cost_price_sum - $sms_price_sum, 2);

        $orders=OrderModel::where($where)->order('id desc')->paginate(30,false,[
            'query'=>$query
        ]);
        // 分页
        $page=$orders->render();
        //支付产品
        $this->assign('pay_product', config('pay_product'));
        $this->assign('page',$page);
        $this->assign('orders',$orders);
        $this->assign('goods',$goods);
        $this->assign('total_price',$total_product_price_sum);
        $this->assign('total_cost_price',$total_cost_price_sum);
        $this->assign('total_profit',$total_profit);
        return $this->fetch();
    }

    /**
     * 生成查询条件
     */
    protected function genereate_where($params)
    {
        $where = [];
        $where['user_id'] = $this->user->id;
        $action=$this->request->action();
        switch($action){
            case 'index':
                if(isset($params['paytype']) && $params['paytype']){
                    $where['paytype']=['in',$params['paytype']];
                }
                if($params['status']!==''){
                    $where['status']=$params['status'];
                }
                if($params['date_range'] && strpos($params['date_range'],' - ')!==false){
                    list($startDate,$endTime)=explode(' - ',$params['date_range']);
                    $where['create_at']=['between',[strtotime($startDate .' 00:00:00'),strtotime($endTime . ' 23:59:59')]];
                }
                
                if ($params['cid'] > 0) {
                	//获取分类下所有的商品id
                	$goods = Db::table('goods')->field('id')->where(['user_id'=>$this->user->id,'cate_id'=>$params['cid']])->select();
                	
                	$gidarr = [];
                	
                	foreach ($goods as $row) {
                		$gidarr[] = $row['id'];
                	}
                	
                	if (!empty($gidarr)){
                		$where['goods_id'] = ['in', $gidarr];
                	}
                }
                if ($params['type'] == 0 && $params['keywords']!=='') {
                	$where['trade_no']=$params['keywords'];
                }
                elseif ($params['type'] == 1 && $params['keywords']!==''){
                	$where['goods_name'] = ['like', "%".$params['keywords']."%"];
                }
                elseif ($params['type'] == 2 && $params['keywords']!==''){
                    $where['contact'] = ['like', "%".$params['keywords']."%"];
                }
            break;
            case 'analysis':
                if(isset($params['paytype']) && $params['paytype']!==''){
                    $where['paytype']=['in',$params['paytype']];
                }
                if($params['status']!==''){
                    $where['status']=$params['status'];
                }
                if($params['date_range'] && strpos($params['date_range'],' - ')!==false){
                    list($startDate,$endTime)=explode(' - ',$params['date_range']);
                    $where['create_at']=['between',[strtotime($startDate .' 00:00:00'),strtotime($endTime . ' 23:59:59')]];
                }
                if($params['trade_no']!==''){
                    $where['trade_no']=$params['trade_no'];
                }
                if($params['goods_id']!==''){
                    $where['goods_id']=$params['goods_id'];
                }
            break;
            case 'channelstatis':
                if($params['date_range'] && strpos($params['date_range'],' - ')!==false){
                    list($startDate,$endTime)=explode(' - ',$params['date_range']);
                    $where['create_at']=['between',[strtotime($startDate .' 00:00:00'),strtotime($endTime . ' 23:59:59')]];
                }
            break;
        }
        return $where;
    }

    /**
     * 导出虚拟卡
     */
    public function dumpCards()
    {
        $trade_no=input('trade_no/s','');
        $order=OrderModel::get(['user_id'=>$this->user->id,'trade_no'=>$trade_no]);
        if(!$order){
            $this->error('不存在该订单！');
        }
        $content   =[];
        $cards     =$order->cards;
        $count     =count($cards);
        $content[] ="订单号：{$trade_no}，导出内容共计{$count}条记录";
        foreach($cards as $card){
            $content[]="卡号：{$card->number}\t卡密{$card->secret}";
        }
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-Disposition:attachment;filename=".'order_'.$trade_no.".txt");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Pragma:public");
        echo implode("\r\n",$content);
    }

    /**
     * 渠道分析
     */
    public function channelStatis()
    {
        $this->setTitle('渠道分析');
        ////////////////// 查询条件 //////////////////
        $query = [
            'date_range'  => input('date_range/s',date('Y-m-d - Y-m-d')),
        ];
        $where = $this->genereate_where($query);

        $orders=OrderModel::where($where)->select();
        $statis=[];
        foreach($orders as $v){
            if(!isset($statis[$v->channel_id])){
                $statis[$v->channel_id]['title']            =$v->channel->title;
                $statis[$v->channel_id]['count']            =0;
                $statis[$v->channel_id]['paid']             =0;
                $statis[$v->channel_id]['unpaid']           =0;
                $statis[$v->channel_id]['sum_money']        =0;
                $statis[$v->channel_id]['sum_actual_money'] =0;
            }
            $statis[$v->channel_id]['count']++;
            if($v->status==1){
                $statis[$v->channel_id]['paid']++;
                $statis[$v->channel_id]['sum_money']        +=$v->total_price;
                $statis[$v->channel_id]['sum_actual_money'] +=$v->total_price - $v->fee;
            }else{
                $statis[$v->channel_id]['sum_money']        +=$v->total_price;
                $statis[$v->channel_id]['unpaid']++;
            }
        }
        // 合计

        $counts['title']            ='合计';
        // 提交订单数
        $counts['count']            =array_sum(array_column($statis,'count'));
        // 已付订单数
        $counts['paid']             =array_sum(array_column($statis,'paid'));
        // 未付订单数
        $counts['unpaid']           =array_sum(array_column($statis,'unpaid'));
        // 订单总金额
        $counts['sum_money']        =array_sum(array_column($statis,'sum_money'));
        // 订单实收金额
        $counts['sum_actual_money'] =array_sum(array_column($statis,'sum_actual_money'));

        // 导出渠道分析
        if(input('action/s','')=='dump'){
            $title=['支付方式','提交订单数','已付订单数','未付订单数','订单总金额','订单实收金额'];
            $data=array_map('array_values',$statis);
            $data[]=array_values($counts);
            generate_excel($title,$data,'channelStatis'.date('YmdHis'),'渠道分析');
        }

        $this->assign('statis',$statis);
        $this->assign('counts',$counts);
        return $this->fetch();
    }

    /**
     * 提卡
     */
    public function fetchCard()
    {
        $this->setTitle('提卡');
        $id=input('id/d',0);
        if(!$id) {
            $this->error('参数错误！');
        }
        $order=OrderModel::get(['user_id'=>$this->user->id,'id'=>$id]);
        if(!$order){
            $this->error('不存在该订单！');
        }
        $card = OrderCardMoel::where(['order_id'=>$id])->select();
        if(empty($card)) {
            $this->error('虚拟卡不存在！');
        }
        $data['trade_no'] = $order['trade_no'];
        $data['card'] = $card;
        MerchantLogService::write('商户提卡成功',  '商户提卡成功，订单号:'.$order['trade_no']);
        $this->assign('data', $data);
        return $this->fetch();
    }
}
