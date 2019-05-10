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
use app\common\model\Channel as ChannelModel;
use app\common\model\ChannelAccount as ChannelAccountModel;
use service\LogService;

class ChannelAccount extends BasicAdmin
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
    	$channel_id = input('channel_id/d', 0);
    	
        $accounts=ChannelAccountModel::where(['channel_id'=>$channel_id])->select();
        $this->assign('accounts',$accounts);
        $this->assign('title', '账号管理');
        
        return $this->fetch();
    }
	//添加账号
    public function add()
    {
    	return $this->post();
    }
    //提交处理
    protected function post(){
    	//获取接口id
    	$channel_id = input('channel_id/d', 0);
    	//获取接口信息
    	$channel = ChannelModel::get($channel_id);
    	
    	if (empty($channel)) {
    		$this->error('接口不存在');
    	}
    	
    	$fields = array();
    	//拼接接口所需字段信息
    	if (!empty($channel['account_fields'])){
    		$channel['account_fields'] = explode('|', $channel['account_fields']);
    		
    		foreach ($channel['account_fields'] as $value) {
    			$value = explode(':', $value);
    			
    			if (isset($value[1])) {
    				$fields[$value[1]] =  array('name' => $value[0]);
    			}
    		}
    	}

    	//获取账号id
    	$account_id = input('account_id/d', 0);
    	
    	if ($account_id > 0){
    		$account = ChannelAccountModel::get($account_id);
    		
    		if (empty($account)) {
    			$account_id = 0;
    		}
    		elseif($account['channel_id'] != $channel_id) {
    			$this->error('支付接口不匹配');
    		}
    		else {
    			$params = (array)$account['params'];
    			
    			foreach ($fields as $key=>$row) {
    				if (isset($params[$key])) {
    					$fields[$key]['value'] = $params[$key];
    				}
    			}
    			$account['lowrate'] *= 1000;
    			$account['highrate'] *= 1000;
    			$account['costrate'] *= 1000;
    			$this->assign('account', $account);
    		}
    	}
    	//post提交数据
    	if($this->request->isPost()){
    		$data = [
    			'name' => input('name/s', ''),
    			'rate_type' => input('rate_type/d', 0),
    			'status' => input('status/d', 0),
    		];
    		
    		if ($data['rate_type'] == 1) {
    			$data['lowrate'] = input('lowrate/d', 0);
    			$data['highrate'] = input('highrate/d', 0);
    			$data['costrate'] = input('costrate/d', 0);
    			
    			$data['lowrate']  /=1000;
    			$data['highrate'] /=1000;
    			$data['costrate'] /=1000;
    		}
    		
    		//获取提交的支付接口参数
    		$params = input('params/a');
    		$data['params'] = array();
    		
    		$default_arr = array();
    		
    		if (!empty($channel['default_fields'])) {
    			$default_fields = explode('|', $channel['default_fields']);
    			 
    			foreach ($default_fields as $value) {
    				$value = explode('=', $value);
    				
    				if (isset($value[1])) {
    					$default_arr[$value[0]] = $value[1];
    				}
    			}
    		}
    		foreach ($fields as $key=>$row) {
    			//检查字段是否有默认值
    			if (isset($default_arr[$key])) {
    				$data['params'][$key] = trim($default_arr[$key]);
    				continue;
    			}
    			
    			if (isset($params[$key])) {
    				$data['params'][$key] = trim($params[$key]);
    			}
    		}
    		
    		if ($account_id > 0) {
    			$res = $account->save($data, ['id'=>$account_id]);
    			
    			if($res!==false){
    				LogService::write('网关通道', '编辑接口账号成功，ID:'.$account_id);
    				$this->success('更新成功！','');
    			}else{
    				$this->error('更新失败！');
    			}
    		}
    		else {
    			$data['channel_id'] = $channel_id;
    			$res=ChannelAccountModel::create($data);
    			if($res!==false){
    				LogService::write('网关通道', '添加接口账号成功，ID:'.$res);
    				$this->success('添加成功！','');
    			}else{
    				$this->error('添加失败！');
    			}
    		}
    		
    	}
    	
    	//前台显示去除默认字段的值
    	if (!empty($channel['default_fields'])) {
    		$channel['default_fields'] = explode('|', $channel['default_fields']);
    	
    		foreach ($channel['default_fields'] as $value) {
    			$value = explode('=', $value);
    			 
    			if (isset($value[0]) && isset($fields[$value[0]])) {
    				unset($fields[$value[0]]);
    			}
    		}
    	}
    	
    	$this->assign('fields', $fields);
    	$this->assign('channel', $channel);
    	return $this->fetch('form');
    }
	//编辑账号
    public function edit($account_id)
    {
    	return $this->post();
    }

    public function del($account_id)
    {
        if(!Request::instance()->isPost()){
            return;
        }
        $account =  ChannelAccountModel::get($account_id);
        
        if (empty($account)) {
        	$this->error('账户不存在');
        }
        $res=ChannelAccountModel::destroy($account_id);
        if($res!==false){
            LogService::write('网关通道', '成功删除供应商账号，ID:'.$account_id);
            $this->success('删除成功！','');
        }else{
            $this->error('删除失败！');
        }
    }

    public function clear($account_id)
    {
        if(!Request::instance()->isPost()){
            return;
        }
        $account=ChannelAccountModel::get($account_id);
        if(!$account){
            $this->error('不存在该账号！');
        }
        // 清除当前额度 & 上线
        $account->cur_money=0;
        $account->status=1;
        $res=$account->save();
        if($res!==false){
            LogService::write('网关通道', '成功清除供应商账号额度，ID:'.$account_id);
            $this->success('操作成功！','');
        }else{
            $this->error('操作失败！');
        }
    }

    public function change_status(){
        if(!Request::instance()->isAjax()){
            $this->error('错误的提交方式！');
        }
        $account_id=input('account_id/d',0);
        
        $account = ChannelAccountModel::where(['id'=>$account_id])->find();
        
        if (empty($account)) {
        	$this->error('账号不存在');
        }
        
        $status=input('status/d',1);
        $account->status = $status;
        $res = $account->save();
        
        $remark = $status == 1 ? '开启' : '关闭';
        if($res!==false){
            LogService::write('网关通道', '成功'.$remark.'供应商账号，ID:'.$account_id);
            $this->success('更新成功！', '');
        }else{
            $this->error('更新失败，请重试！');
        }
    }

    // 获取渠道账户字段
    public function getFields()
    {
        $channel_id=input('channel_id/d',0);
        $fields=[];
        $res=ChannelModel::get($channel_id);
        if($res){
            if($res->account_fields!=''){
                $fields=explode('|',$res->account_fields);
            }
        }
        return J(0,'',$fields);
    }
}
