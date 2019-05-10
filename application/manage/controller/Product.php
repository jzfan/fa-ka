<?php
/**
 * Created by Atom.
 * User: Veris
 * Date: 2017-10-23
 * Time: 14:00
 */

namespace app\manage\controller;

use controller\BasicAdmin;
use think\Db;
use think\Request;
use service\LogService;

class Product extends BasicAdmin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $productList=Db::name('Product')->select();
        $this->assign('productList',$productList);
        $this->assign('paytype',get_paytype_list());
        return $this->fetch();
    }

    public function add()
    {
        if(!Request::instance()->isPost()){
            $this->assign('paytype',get_paytype_list());
            $this->assign('channelList',[]);
            return $this->fetch('form');
        }
        $data=Request::instance()->post();
        $arr=[];
        if(isset($data['weight'])){
            foreach($data['weight'] as $k => $v){
                if(isset($v['pid']) && $v['pid']==1){
                    $arr[$k]=(int)$v['weight'];
                }
            }
        }
        $data['weight']=json_encode($arr);
        unset($data['product_id']);
        $res=Db::name('Product')->insert($data);
        if($res!==false){
            LogService::write('网关通道', '添加支付产品成功，ID：'.$res);
            $this->success('添加成功！','');
        }else{
            $this->error('添加失败！');
        }
    }

    public function edit($product_id)
    {
        if(!Request::instance()->isPost()){
            $product=Db::name('Product')->find($product_id);
            if($product){
                $product['weight']=json_decode($product['weight'],true);
            }
            $this->assign('product',$product);
            $this->assign('paytype',get_paytype_list());
            $channelList=Db::name('channel')->where([
                'paytype'=>$product['paytype'],
                'status'=>1,
            ])->select();
            $this->assign('channelList',$channelList);
            $this->assign('product_id',$product_id);
            return $this->fetch('form');
        }
        $data=Request::instance()->post();
        $arr=[];
        if(isset($data['weight'])){
            foreach($data['weight'] as $k => $v){
                if(isset($v['pid']) && $v['pid']==1){
                    $arr[$k]=(int)$v['weight'];
                }
            }
        }
        $data['weight']=json_encode($arr);
        $product_id=$data['product_id'];
        unset($data['product_id']);
        $res=Db::name('Product')->where('id',$product_id)->update($data);
        if($res!==false){
            LogService::write('网关通道', '修改支付产品成功，ID：'.$product_id);
            $this->success('保存成功！','');
        }else{
            $this->error('保存失败！');
        }
    }

    public function del($product_id)
    {
        if(!Request::instance()->isPost()){
            return;
        }
        $res=Db::name('Product')->delete($product_id);
        if($res!==false){
            LogService::write('网关通道', '删除支付产品成功，ID：'.$product_id);
            $this->success('删除成功！','');
        }else{
            $this->error('删除失败！');
        }
    }

    public function change_status()
    {
        if(!Request::instance()->isAjax()){
            $this->error('错误的提交方式！');
        }
        $product_id=input('product_id/d',0);
        $status=input('status/d',1);
        $res=Db::name('Product')->where([
            'id'=>$product_id,
        ])->update([
            'status'=>$status
        ]);
        $remark = $status == 1 ? '开启' : '关闭';
        if($res!==false){
            LogService::write('网关通道', $remark.'支付产品成功，ID：'.$product_id);
            $this->success('更新成功！', '');
        }else{
            $this->error('更新失败，请重试！');
        }
    }
}
