<?php

namespace app\merchant\controller;

use think\Controller;
use think\Request;

use app\common\model\User as UserModel;
use app\common\model\Message as MessageModel;
use service\MerchantLogService;

class Message extends Base
{
    /**
     * 获取收件
     */
    public function index()
    {
        $this->setTitle('站内消息');
        $messages=MessageModel::where(['to_id'=>$this->user->id])->order('id desc')->paginate(30);
        // 分页
        $page=$messages->render();
        $this->assign('page',$page);
        $this->assign('messages',$messages);
        return $this->fetch();
    }

    /**
     * 标记收件状态
     */
    public function changeStatus()
    {
        if(!$this->request->isPost()){
            return;
        }
        $message_id=input('id/d',0);
        $message=MessageModel::get(['id'=>$message_id,'to_id'=>$this->user->id]);
        if(!$message){
            $this->error('不存在该消息！');
        }
        $status =input('status/d',0);
        $status =$status?1:0;
        $statusStr = $status == 1 ? '已读' : '未读';
        $message->status =$status;
        $res           =$message->save();
        if($res!==false){
            MerchantLogService::write('标记站内消息收件状态成功',  '标记为'.$statusStr.'，ID:'.$message_id);
            return J(0,'success');
        }else{
            return J(1,'error');
        }
    }

    /**
     * 删除收件
     */
    public function del()
    {
        $message_id=input('id/d',0);
        $message=MessageModel::get(['id'=>$message_id,'to_id'=>$this->user->id]);
        if(!$message){
            return J(1,'不存在该消息！');
        }
        $res=$message->delete();
        if($res!==false){
            MerchantLogService::write('删除站内消息成功',  '删除站内消息成功，ID:'.$message_id);
            return J(0,'删除成功！');
        }else{
            return J(1,'删除失败！');
        }
    }
}
