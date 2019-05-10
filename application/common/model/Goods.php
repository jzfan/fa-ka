<?php
namespace app\common\model;

use app\common\util\notify\Stock;
use think\Db;
use think\Model;
use traits\model\SoftDelete;

class Goods extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_at';

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    public function category()
    {
        return $this->belongsTo('GoodsCategory', 'cate_id');
    }

    public function cards()
    {
        return $this->hasMany('GoodsCard', 'goods_id');
    }

    public function orders()
    {
        return $this->hasMany('Order', 'goods_id');
    }

    // 虚拟卡库存数
    protected function getCardsStockCountAttr($value, $data)
    {
        return $this->cards()->where('status', 1)->count();
    }

    // 虚拟卡售出数
    protected function getCardsSoldCountAttr($value, $data)
    {
        return Db::name('goods_card')->where('goods_id', $data['id'])->where('status', 2)->count();
    }

    // 保存优惠
    protected function setWholesaleDiscountListAttr($value)
    {
        $data = [];
        $n = isset($value['num']) && isset($value['price']) ? count($value['num']) : 0;
        for ($i = 0; $i < $n; $i++) {
            if (!isset($value['num'][$i]) || !isset($value['price'][$i])) {
                continue;
            }
            $num = $value['num'][$i];
            $price = $value['price'][$i];
            if ($num > 0 && $price > 0) {
                $data[] = [
                    'num' => $num,
                    'price' => $price,
                ];
            } else {
                continue;
            }
        }
        return json_encode($data);
    }

    // 获取优惠
    protected function getWholesaleDiscountListAttr($value)
    {
        return json_decode($value, true);
    }

    /**
     * 链接
     */
    public function link()
    {
        return $this->morphOne('Link', 'relation', 'goods')->order('id desc');
    }

    /**
     * 获取店铺链接
     */
    public function getLinkAttr($value, $data)
    {
        $links = $this->link()->find();
        if (!$links) {
            $links = self::makeLink($data['user_id'], $data['id']);
        }
        $domain = sysconf('site_shop_domain') . '/details/';
        return $domain . $links['token'];
    }

    /**
     * 获取店铺短链接
     */
    public function getShortLinkAttr($value, $data)
    {
        $links = $this->link()->find();
        if (!$links) {
            $links = self::makeLink($data['user_id'], $data['id']);
        }
        return $links['short_url'];
    }

    public static function makeLink($userId, $goodsId)
    {
        $domain = sysconf('site_shop_domain') . '/details/';
        while (1) {
            $token = strtoupper(get_uniqid(8));

            //检测token是否存在
            $count = Db::name('link')->where('token', $token)->count();

            if ($count == 0) {
                break;
            }
        }

        $short_url = get_short_domain($domain . $token);
        $link = [
            'user_id' => $userId,
            'relation_type' => 'goods',
            'relation_id' => $goodsId,
            'token' => $token,
            'short_url' => $short_url,
            'status' => 1,
            'create_at' => $_SERVER['REQUEST_TIME'],
        ];
        Db::name('link')->insert($link);
        return $link;
    }

    /**
     * 获取链接状态
     */
    public function getLinkStatusAttr($value, $data)
    {
        return $this->link()->value('status');
    }

    /**
     * @param $trade_no
     * @return \think\response\Json
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    public static function sendOut($trade_no)
    {
        $order = Order::get(['trade_no' => $trade_no]);
        if (!$order) {
            return json([
                'msg' => '订单不存在!',
                'quantity' => 0,
                'status' => 0,
            ]);
        }
        if ($order->status == 0) {
            return json([
                'msg' => '订单未付款，请重新支付，或联系客服处理！',
                'quantity' => 0,
                'status' => 0,
            ]);
        }

        // 获取商品
        $goods = $order->goods;

        //兼容处理产品被删除的情况显示已经产品问题 2018-5-23
        // 出库
        $orderCardsCount = $order->cards()->count(); // 订单已发货数

        if (!$orderCardsCount && !$goods) {
            //再次检查产品是不是存在
            //            if(!$goods){
            return json([
                'msg' => '商品不存在，请联系客服处理！',
                'quantity' => 0,
                'status' => 0,
            ]);
//            }
        }

        // 库存预警 通知
        if ($goods['inventory_notify'] == 1 && $goods['inventory_notify'] > $goods['cards_stock_count']) {
            $notify = new Stock();
            $notify->notify($goods->user, $goods);
        }

        // 如果已发货则返回卡信息
        if ($orderCardsCount >= $order->quantity) {
            $i = 0;
            $msg = '';
            foreach ($order->cards as $card) {
                //兼容以前的订单，如果订单发了超过购买数量的卡密，只显示订单数量的卡密
                if ($i < $order->quantity) {
                    if (empty($card->secret)) {
                        $msg .= '<p>卡密：'.$card->number;
                        $msg .= '<a href="javascript:;" class="btn btn-purple waves-effect waves-light clipboard" data-clipboard-text="'.$card->number.'">复制</a></p>';
                    } else {
                        $msg .= '<p>卡号：'.$card->number;
                        $msg .= '<a href="javascript:;" class="btn btn-purple waves-effect waves-light clipboard" data-clipboard-text="'.$card->number.'">复制</a></p>';
                        $msg .= '<p>卡密：'.$card->secret;
                        $msg .= '<a href="javascript:;" class="btn btn-purple waves-effect waves-light clipboard" data-clipboard-text="'.$card->secret.'">复制</a></p>';
                    }
                    $i++;
                } else {
                    break;
                }
            }
        } else { // 如果发货数小于购买数量，则补发货

            // 如果商品库存不足以发货
            if ($goods->cards_stock_count < $order->quantity) {
                return json([
                    'msg' => '库存不足，请联系客服处理！',
                    'quantity' => 0,
                    'status' => 0,
                ]);
            }
            $n = $order->quantity - $orderCardsCount;
            $cards = $goods->cards()->where('status', 1)->lock(true)->limit($n)->select();
            $data = [];
            $idArr = [];
            $msg = '';
            $sends = 0;
            if ($cards) {
                foreach ($cards as $card) {
                    $data[] = [
                        'order_id' => $order->id,
                        'number' => $card->number,
                        'secret' => $card->secret,
                        'card_id' => $card->id,
                    ];
                    $idArr[] = $card->id;
                    $msg .= '<p>卡号：'.$card->number;
                    $msg .= '<a href="javascript:;" class="btn btn-purple waves-effect waves-light clipboard" data-clipboard-text="'.$card->number.'">复制</a></p>';
                    $msg .= '<p>卡密：'.$card->secret;
                    $msg .= '<a href="javascript:;" class="btn btn-purple waves-effect waves-light clipboard" data-clipboard-text="'.$card->secret.'">复制</a></p>';
                    $sends++;
                }
                // 出货
                Db::startTrans();
                try {
                    $res = $goods->cards()->where(['id' => ['in', $idArr]])->update(['status' => 2, 'sell_time' => time()]);
                    if(!$res){
                        throw new \Exception('服务器繁忙，请稍候刷新页面');
                    }
                    $order->cards()->saveAll($data);
                    $order->sendout = $sends;
                    $res = $order->save();
                    //只有成功更新订单发货数量的请求才能成功发货，其它请求丢弃
                    if ($res) {
                        //确认一下发货数量是否正确
                        $count = Db::name('order_card')->where('order_id', $order->id)->count();
                        if ($count <= $order->quantity) {
                            Db::commit();
                        } else {
                            Db::rollback();
                            return json([
                                'msg' => '出货失败，请刷新页面重试',
                                'quantity' => 0,
                                'status' => 0,
                            ]);
                        }
                    } else {
                        Db::rollback();
                        return json([
                            'msg' => '出货失败，请刷新页面重试',
                            'quantity' => 0,
                            'status' => 0,
                        ]);
                    }
                } catch (\Exception $e) {
                    Db::rollback();
                    // 记录错误订单
                    return json([
                        'msg' => '出货失败，原因：' . $e->getMessage(),
                        'quantity' => 0,
                        'status' => 0,
                    ]);
                }
            }
        }

        $msg .= '<p>使用说明：' . $goods['remark'] . '</p>';
        return json([
            'msg' => $msg,
            'quantity' => $order->quantity,
            'status' => 1,
        ]);
    }
}
