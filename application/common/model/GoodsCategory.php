<?php
namespace app\common\model;

use think\Model;

class GoodsCategory extends Model
{
    public function user()
    {
        return $this->belongsTo('User','user_id');
    }

    public function goodsList()
    {
        return $this->hasMany('Goods','cate_id');
    }

    public function coupons()
    {
        return $this->hasMany('GoodsCoupon','cate_id');
    }

	/**
	 * 链接
	 */
	public function link()
	{
        return $this->morphOne('Link', 'relation' ,'goods_category')->order('id desc');
	}

	/**
	 * 获取店铺链接
	 */
    public function getLinkAttr($value,$data)
    {
        $links=$this->link()->find();
        $domain=sysconf('site_shop_domain').'/liebiao/';
        if(!$links){
            $token     =strtoupper(get_uniqid(16));
            $short_url =get_short_domain($domain.$token);
            $this->link()->insert([
                'user_id'       =>$data['user_id'],
                'relation_type' =>'goods_category',
                'relation_id'   =>$data['id'],
                'token'         =>$token,
                'short_url'     =>$short_url,
                'status'        =>1,
                'create_at'     =>$_SERVER['REQUEST_TIME'],
            ]);
        }
        return $domain.$this->link()->value('token');
    }

	/**
	 * 获取店铺短链接
	 */
    public function getShortLinkAttr($value,$data)
    {
        return $this->link()->value('short_url');
    }

	/**
	 * 获取链接状态
	 */
    public function getLinkStatusAttr($value,$data)
    {
        return $this->link()->value('status');
    }
}
