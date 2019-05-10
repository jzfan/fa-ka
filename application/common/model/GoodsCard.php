<?php
namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class GoodsCard extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_at';

    public function goods()
    {
        return $this->belongsTo('Goods','goods_id');
    }

    protected function getStatusTextAttr($value,$data)
    {
        $status=[
            1 => '未售出',
            2 => '已售出',
        ];
        return $status[$data['status']];
    }
}
