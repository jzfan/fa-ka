<?php

namespace app\shop\controller;

use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsCoupon as CouponModel;
use app\common\model\Link;
use app\common\model\User as UserModel;
use think\Config;
use think\Controller;
use think\Request;

class Shop extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getProtocol()
    {
        
        //判断用户是否开了购卡协议自动弹出
        $userid = input('userid/s');
        $user = UserModel::get(['id' => $userid]);

        if ($user['shop_gouka_protocol_pop']) {
            $protocol = sysconf('buy_protocol');
            if (!empty($protocol)) {
                success(htmlspecialchars_decode($protocol));
            } else {
                success();
            }
        }

        success();
    }

    public function popNotice()
    {
        
        //判断用户是否开了购卡协议自动弹出
        $userid = input('userid/s');
        $user = UserModel::get(['id' => $userid]);

        if ($user['user_notice_auto_pop']) {
            success();
        }

        error();
    }

    /**
     * 店铺购买页
     */
    public function index()
    {
        $subdomain = input('subdomain/s', '');
        if ($subdomain !== '') {
            $shop = UserModel::get(['subdomain' => $subdomain, 'status' => 1]);
        } else {
            $token = input('token/s', '');
            $domain = Link::get(['token' => $token]);
            if (!$domain) {
                $this->error('店铺链接不存在');
            }
            if ($domain->status === 0) {
                $this->error('店铺链接已关闭，暂不能购买。');
            }
            $shop = $domain->relation;
        }
        if (!$shop || $shop->status == 0) {
            $this->error('不存在该店铺！');
        }
        $is_freeze = $shop->is_freeze || $shop->is_close;
        if ($is_freeze) {
            $this->error('店铺链接已关闭，暂不能购买。');
        }
        if ($this->request->isMobile()) {
            if (!empty($shop['shop_notice'])) {
                $shop['shop_notice'] = str_replace(["\r\n", "\n", "\r"], "<br />", $shop['shop_notice']);
            }
        }

        if (0 == $shop['fee_payer']){
            // 获取系统配置
            $shop['fee_payer'] = sysconf('fee_payer');
        }

        $this->assign('shop', $shop);
        $this->assign('qrcode', $shop->link);

        // 商品分类
        $categorys = $shop->categorys()->where('status', 1)->order('sort DESC')->select();
        $this->assign('categorys', $categorys);

        // 获取支付渠道
        $this->getChannel($shop);

        // 移动端访问
        if ($this->request->isMobile()) {
            if ($shop->pay_theme == 'custom' && file_exists(APP_PATH . "/templates/mobile/shop/custom")) {
                $this->view->config('view_theme', $shop->pay_theme . DS);
            } else {
                $this->view->config('view_theme', 'default' . DS);
            }
        } else {
            $this->view->config('view_theme', $shop->pay_theme . DS);
        }

        // 加载模板
        return $this->fetch('/index');
    }

    /**
     * 店铺分类购买页
     */
    public function category()
    {
        $token = input('token/s', '');
        $domain = Link::get(['token' => $token]);
        if (!$domain) {
            $this->error('商品分类链接不存在');
        }
        if ($domain->status === 0) {
            $this->error('商品分类链接已关闭，暂不能购买。');
        }
        $category = $domain->relation;
        if (!$category || $category->status == 0) {
            $this->error('不存在该店铺分类！');
        }
        $shop = $category->user;
        if (!$shop) {
            $this->error('不存在该店铺！');
        }
        $is_freeze = $shop->is_freeze || $shop->is_close;
        if ($is_freeze) {
            $this->error('商品分类链接已关闭，暂不能购买。');
        }
        if (0 == $shop['fee_payer']){
            // 获取系统配置
            $shop['fee_payer'] = sysconf('fee_payer');
        }
        $this->assign('category', $category);
        $this->assign('shop', $shop);
        $this->assign('qrcode', $category->link);

        // 商品分类
        $categorys = $shop->categorys()->where('status', 1)->select();
        $this->assign('categorys', $categorys);

        // 支付渠道
        $this->getChannel($shop);

        // 移动端访问
        if ($this->request->isMobile()) {
            if ($category->theme == 'custom' && file_exists(APP_PATH . "/templates/mobile/shop/custom")) {
                $this->view->config('view_theme', $category->theme . DS);
            } else {
                $this->view->config('view_theme', 'default' . DS);
            }
        } else {
            $this->view->config('view_theme', $category->theme . DS);
        }

        // 加载模板
        return $this->fetch('/category');
    }

    /**
     * 店铺商品购买页
     */
    public function goods()
    {
        $token = input('token/s', '');
        $domain = Link::get(['token' => $token]);
        if (!$domain) {
            $this->error('商品链接不存在');
        }
        if ($domain->status === 0) {
            $this->error('商品链接已关闭，暂不能购买。');
        }
        $goods = $domain->relation;
        if (!$goods || $goods->status == 0) {
            $this->error('不存在该店铺商品！');
        }
        $shop = $goods->user;
        if (!$shop) {
            $this->error('不存在该店铺！');
        }
        $is_freeze = $shop->is_freeze || $shop->is_close;
        if ($is_freeze) {
            $this->error('商品链接已关闭，暂不能购买。');
        }

        if ($this->request->isMobile()) {
            if (!empty($shop['shop_notice'])) {
                $shop['shop_notice'] = str_replace(["\r\n", "\n", "\r"], "<br />", $shop['shop_notice']);
            }
        }
        if (0 == $shop['fee_payer']){
            // 获取系统配置
            $shop['fee_payer'] = sysconf('fee_payer');
        }
        $this->assign('goods', $goods);
        $this->assign('shop', $shop);
        $this->assign('qrcode', $goods->link);

        // 支付渠道
        $this->getChannel($shop);

        // 移动端访问
        if ($this->request->isMobile()) {
            if ($goods->theme == 'custom' && file_exists(APP_PATH . "/templates/mobile/shop/custom")) {
                $this->view->config('view_theme', $goods->theme . DS);
            } else {
                $this->view->config('view_theme', 'default' . DS);
            }
        } else {
            $this->view->config('view_theme', $goods->theme . DS);
        }
        // 加载模板
        return $this->fetch('/goods');
    }

    // 获取商品列表
    public function getGoodsList()
    {
        $cate_id = input('cateid/d', 0);
        $str = '';
        $goodsList = GoodsModel::where(['cate_id' => $cate_id, 'status' => 1])->order('sort DESC')->select();
        foreach ($goodsList as $v) {
            $str .= "<option value=\"{$v->id}\">{$v->name}</option>";
        }
        return json($str);
    }

    public function getGoodsInfo()
    {
        $goods_id = input('goodid/d', 0);
        $goods = GoodsModel::get(['id' => $goods_id, 'status' => 1]);
        if (!$goods) {
            return '不存在该商品！';
        }
        $cardsCount = $goods->cards_stock_count;
        $stockStr = '库存' . $cardsCount . '张';
        // 如果库存显示类型为范围库存
        if ($goods->user->stock_display == 2) {
            if ($cardsCount >= 100) {
                $stockStr = '库存非常多';
            } elseif ($cardsCount >= 30) {
                $stockStr = '库存很多';
            } elseif ($cardsCount >= 10) {
                $stockStr = '库存一般';
            } elseif ($cardsCount > 0) {
                $stockStr = '库存少量';
            } else {
                $stockStr = '库存不足';
            }
        }
        $data = [
            // 'gonggao'         =>'测试',
            'goodinvent' => '<span style="color:green">' . $stockStr . '</span><input type="hidden" name="kucun" value="' . $cardsCount . '">',
            'is_coupon' => $goods->coupon_type,
            'is_discount' => $goods->wholesale_discount,
            'is_pwdforbuy' => $goods->visit_type,
            'is_pwdforsearch' => $goods->take_card_type,
            'limit_quantity' => $goods->limit_quantity,
            'price' => $goods->price,
            'remark' => $goods->content,
            'contact_limit' => $goods->contact_limit,
        ];
        return json($data);
    }

    public function getRate()
    {
        return 100;
    }

    // 获取优惠列表
    public function getDiscounts()
    {
        $goods_id = input('goodid/d', 0);
        $goods = GoodsModel::get(['id' => $goods_id, 'status' => 1]);
        if (!$goods) {
            return '不存在该商品！';
        }
        $str = '<table class="registera"><tr><th>购买数量</th><th>优惠价格</th></tr>';
        foreach ($goods->wholesale_discount_list as $v) {
            $str .= "<tr><td>{$v['num']}张</td><td>{$v['price']}元</td></tr>";
        }
        $str .= '</table>';
        return $str;
    }

    // 获取优惠信息
    public function getDiscount()
    {
        $goods_id = input('goodid/d', 0);
        $goods = GoodsModel::get(['id' => $goods_id, 'status' => 1]);
        if (!$goods) {
            return '不存在该商品！';
        }
        $quantity = input('quantity/d', 0);
        return $this->get_discount_price($goods, $quantity);
    }

    // 获取优惠价
    private function get_discount_price($goods, $quantity)
    {
        $price = $goods->price;
        $list = $goods->wholesale_discount_list;
        $sort = array_column($list, 'num');
        array_multisort($sort, SORT_DESC, $list);
        foreach ($list as $v) {
            if ($quantity >= $v['num']) {
                $price = $v['price'];
                break;
            }
        }
        return $price;
    }

    // 检查访问密码
    public function checkVisitPassword()
    {
        $goods_id = input('goodid/d', 0);
        $password = input('pwdforbuy/s', '');
        $goods = GoodsModel::get(['id' => $goods_id, 'status' => 1]);
        if (!$goods) {
            return '不存在该商品！';
        }
        if ($goods->visit_password != $password) {
            return '密码验证失败！请重试';
        }
        return 'ok';
    }

    // 检查优惠券
    public function checkCoupon()
    {
        $user_id = input('userid/d', 0);
        $cate_id = input('cateid/d', 0);
        $code = input('couponcode/s', '');

        $shop = UserModel::get(['id' => $user_id, 'status' => 1]);
        if (!$shop) {
            return json([
                'result' => 0,
                'msg' => '不存在该店铺',
            ]);
        }
        // 获取优惠券信息
        $coupon = CouponModel::get([
            'user_id' => $shop->id,
            'cate_id' => ['in', [0, $cate_id]],
            'code' => $code,
        ]);
        if (!$coupon || $coupon->status != 1 || $coupon->expire_at < $_SERVER['REQUEST_TIME']) {
            return json([
                'result' => 0,
                'msg' => '优惠券不存在或已过期',
            ]);
        }
        return json([
            'result' => 1,
            'coupon' => round($coupon->amount, 2),
            'ctype' => $coupon->type,
        ]);
    }

    /**
     * @param $shop
     */
    protected function getChannel($shop)
    {
        $userChannels = get_user_channels($shop->id);

        foreach ($userChannels as $key => $row) {
            //移动端只能显示移动端通道
            if ($this->request->isMobile()) {
                if ($row['is_available'] == 2) {
                    unset($userChannels[$key]);
                }
            } //电脑端只能显示电脑端通道
            elseif (!$this->request->isMobile() && $row['is_available'] == 1) {
                unset($userChannels[$key]);
            }
        }

        $userChannels = array_values($userChannels);

        $this->assign('userChannels', $userChannels);
    }
}
