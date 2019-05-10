<?php

namespace app\merchant\controller;

use think\Controller;
use think\Request;
use app\common\model\Order as OrderModel;
use app\common\model\User as UserModel;
use app\common\model\UserMoneyLog as UserMoneyLogModel;

class Spread extends Base
{
    // 推广列表
    public function index()
    {
        $this->setTitle('推广列表');
        ////////////////// 查询条件 //////////////////
        $query = [
        ];
        $where = $this->genereate_where($query);

        $subUsers=UserModel::where($where)->order('id desc')->paginate(30,false,[
            'query'=>$query
        ]);
        // 分页
        $page=$subUsers->render();
        $this->assign('page',$page);
        $this->assign('subUsers',$subUsers);
        //推广二维码
        $spread_url = generate_qrcode_link('__PUBLIC__/register?user_id='.$this->user->id);
        $this->assign('spread_url', $spread_url);
        return $this->fetch();
    }

    /**
     * 生成查询条件
     */
    protected function genereate_where($params)
    {
        $where = [];
        $where['parent_id'] = $this->user->id;
        $action=$this->request->action();
        switch($action){
            case 'index':
                break;
        }
        return $where;
    }

    /**
     * 返佣
     */
    public function rebate()
    {
        $this->setTitle('推广返利');
        $where=[];
        $where['user_id']=$this->user->id;
        $where['business_type']='sub_sold_rebate';

        $logs=UserMoneyLogModel::where($where)->order('id desc')->paginate(30);
        // 分页
        $page=$logs->render();
        $this->assign('page',$page);
        $this->assign('logs',$logs);
        return $this->fetch();
    }
}
