<?php

namespace app\merchant\controller;

use app\common\model\ComplaintMessage;
use service\FileService;
use think\Controller;
use think\Request;
use think\Db;
use app\common\model\User as UserModel;
use app\common\model\Complaint as ComplaintModel;
use service\MerchantLogService;
use app\common\util\Sms;

class Complaint extends Base
{
    public function index()
    {
        $this->setTitle('投诉管理');
        ////////////////// 查询条件 //////////////////
        $query = [
            'type'    => input('type/s',''),
            'status'  => input('status/s',''),
        ];
        $where = $this->genereate_where($query);
        $complaints=ComplaintModel::where($where)->order('id desc')->paginate(30,false,[
            'query'=>$query
        ]);
        // 分页
        $page=$complaints->render();
        $this->assign('page',$page);
        $this->assign('complaints',$complaints);
        return $this->fetch();
    }

    /**
     * 投诉详情
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        //获取投诉内容
        $id = input('id/d');
        $complaint = ComplaintModel::where(['id' => $id])->find();
        if ($complaint) {
            $this->assign('complaint', $complaint);

            //获取投诉对话内容
            $messages = DB::name('complaint_message')->where(['trade_no' => $complaint['trade_no']])->select();
            $this->assign('messages', $messages);

            return $this->fetch('detail');
        } else {
            $this->error('投诉不存在');
        }
    }

    /**
     * 发送沟通内容
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function send()
    {
        $content = input('content/s', '');
        if (empty($content)) {
            return J(500, '请输入沟通内容');
        }


        $id = input('id/d', '');
        $complaint = ComplaintModel::where(['id' => $id])->find();
        if ($complaint) {
            $data = [
                'from' => session('merchant.user'),
                'trade_no' => $complaint['trade_no'],
                'content' => $content,
                'create_at' => time(),
            ];
            ComplaintMessage::create($data);
            return J(200, '发送成功');
        } else {
            return J(500, '登录超时，请重新登录');
        }
    }

    /**
     * 发送图片
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendImg()
    {
        //获取上传文件
        $file = $this->request->file('image');

        if($file){
            //检查文件的扩展名
            $ext = strtolower(pathinfo($file->getInfo('name'), PATHINFO_EXTENSION));
            if(in_array($ext,['jpg','jpeg','gif','png'])){
                //检查投诉是否存在
                $id = input('id/d', '');
                $complaint = ComplaintModel::where(['id' => $id])->find();
                if ($complaint) {
                    //保存图片
                    $md5 = [uniqid(), uniqid()];
                    $filename = join('/', $md5) . ".{$ext}";

                    $info = $file->move('static' . DS . 'upload' . DS . $md5[0], $md5[1], true);

                    if ($info){
                        $file_url = FileService::getFileUrl($filename, 'local');
                        $data = [
                            'from' => session('merchant.user'),
                            'trade_no' => $complaint->trade_no,
                            'content' => $file_url,
                            'content_type' => '1',
                            'create_at' => time(),
                        ];
                        ComplaintMessage::create($data);
                        return J(200, '发送成功');
                    }else {
                        return J(500, '发送失败，请稍候再试');
                    }
                } else {
                    return J(500, '登录超时，请重新登录');
                }
            }else{
                return J(500, '发送失败，不支持的图片文件格式');
            }
        }else {
            return J(500, '请上传举证图片');
        }
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
                if($params['status']!==''){
                    $where['status']=$params['status'];
                }
                if($params['type']!==''){
                    $where['type']=$params['type'];
                }
            break;
        }
        return $where;
    }

    // 改变状态
    public function changeStatus()
    {
        if(!$this->request->isPost()){
            return;
        }
        $complaint_id=input('id/d',0);
        $complaint=ComplaintModel::get(['id'=>$complaint_id,'user_id'=>$this->user->id]);
        if(!$complaint){
            $this->error('不存在该记录！');
        }
        $status =input('status/d',0);
        $status =$status?1:0;
        $statusStr = $status == 1 ? '已处理' : '待处理';
        $complaint->status =$status;
        $res           =$complaint->save();
        if($res!==false){
        	if (!empty($complaint['mobile']) && $status == 1) {
        		$sms=new Sms;
        		// 向买家发送投诉结构短信
        		$sms->sendMsg($complaint['mobile'],"您的订单编号：{$complaint['trade_no']}，投诉已经处理。");
        	}
        	
            MerchantLogService::write('修改投诉处理状态',  '将编号为'.$complaint['trade_no'].'的投诉处理状态修改为'.$statusStr);
            return J(0,'success');
        }else{
            return J(1,'error');
        }
    }
}
