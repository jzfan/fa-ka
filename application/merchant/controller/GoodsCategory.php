<?php

namespace app\merchant\controller;

use think\Controller;
use think\Request;
use app\common\model\GoodsCategory as CategoryModel;
use service\MerchantLogService;

class GoodsCategory extends Base
{
    public function index()
    {
        $this->setTitle('商品分类');
        $categorys=CategoryModel::where(['user_id'=>$this->user->id])->order('sort desc,id desc')->select();
        $this->assign('categorys',$categorys);
        return $this->fetch();
    }

    public function add()
    {
        if(!$this->request->isPost()){
            return;
        }
        $name=input('name/s','');
        $sort=input('sort/d',0);
        if(!$name){
            $this->error('请填写分类名称！');
        }
        // 字词检查
        $res=check_wordfilter($name);
        if($res!==true){
            $this->error('分类名包含敏感词汇“'.$res.'”！');
        }
        $res=CategoryModel::create([
            'user_id'   =>$this->user->id,
            'name'      =>$name,
            'sort'      =>$sort,
            'status'    =>1,
            'create_at' =>$_SERVER['REQUEST_TIME'],
        ]);
        if($res!==false){
            MerchantLogService::write('添加商品分类成功',  '添加商品分类成功，ID:'.$res->id.'，名称:'.$res->name);
            $this->redirect('index');
        }else{
            $this->error('添加失败！');
        }
    }

    public function edit()
    {
        $cate_id=input('id/d',0);
        $category=CategoryModel::get(['id'=>$cate_id,'user_id'=>$this->user->id]);
        if(!$category){
            $this->error('不存在该分类！');
        }
        if(!$this->request->isPost()){
            $this->setTitle('编辑分类');
            $this->assign('category',$category);
            return $this->fetch();
        }
        $name=input('name/s','');
        $sort=input('sort/d',0);
        $theme=input('theme/s','default');
        if(!$name){
            $this->error('请填写分类名称！');
        }
        // 字词检查
        $res=check_wordfilter($name);
        if($res!==true){
            $this->error('分类名包含敏感词汇“'.$res.'”！');
        }
        $category->name=$name;
        $category->sort=$sort;
        $category->theme=$theme;
        $res=$category->save();
        if($res!==false){
            MerchantLogService::write('编辑商品分类成功',  '编辑商品分类成功，ID:'.$cate_id);
            $this->redirect('index');
        }else{
            $this->error('保存失败！');
        }
    }

    public function del()
    {
        $cate_id=input('id/d',0);
        $category=CategoryModel::get(['id'=>$cate_id,'user_id'=>$this->user->id]);
        if(!$category){
            return J(1,'不存在该分类！');
        }
        if($category->goodsList()->count()){
            return J(1,'该分类下存在商品，暂时不能删除！');
        }
        $res=$category->delete();
        if($res!==false){
            MerchantLogService::write('删除商品分类成功',  '删除商品分类成功，ID:'.$cate_id);
            return J(0,'删除成功！');
        }else{
            return J(1,'删除失败！');
        }
    }

    public function changeStatus()
    {
        if(!$this->request->isPost()){
            return;
        }
        $cate_id=input('id/d',0);
        $category=CategoryModel::get(['id'=>$cate_id,'user_id'=>$this->user->id]);
        if(!$category){
            $this->error('不存在该分类！');
        }
        $status           =input('status/d',0);
        $status =$status?1:0;
        $statusStr = $status == 1 ? '启用' : '禁用';
        $category->status =$status;
        $res              =$category->save();
        if($res!==false){
            MerchantLogService::write('修改商品分类状态成功',  $statusStr.'商品分类成功，ID:'.$cate_id);
            return J(0,'success');
        }else{
            return J(1,'error');
        }
    }

    // 商品分类购买链接
    public function link()
    {
        $cate_id=input('id/d',0);
        $category=CategoryModel::get(['id'=>$cate_id,'user_id'=>$this->user->id]);
        if(!$category){
            return J(1,'不存在该分类！');
        }
        $this->setTitle('商品分类【'.$category->name.'】的购买链接');
        $this->assign('category',$category);
        return $this->fetch();
    }
}
