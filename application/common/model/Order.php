<?php
namespace app\common\model;

use think\Model;

class Order extends Model
{
    public function channel()
    {
        return $this->belongsTo('Channel','channel_id');
    }

    public function channelAccount()
    {
        return $this->belongsTo('ChannelAccount','channel_account_id');
    }

    public function user()
    {
        return $this->belongsTo('User','user_id');
    }

    public function goods()
    {
        return $this->belongsTo('Goods','goods_id');
    }

    public function cards()
    {
        return $this->hasMany('OrderCard','order_id');
    }

    public function getStatusTextAttr($value,$data)
    {
        $status=[
            '0'=>'未支付',
            '1'=>'已支付',
        ];
        return $status[$data['status']];
    }

    public function getCardsCountAttr($value,$data)
    {
        return $this->cards()->count();
    }
}
