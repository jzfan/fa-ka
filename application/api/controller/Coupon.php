<?php

namespace app\api\controller;

use app\api\service\CouponService;

/**
 * Class Coupon
 * @package app\api\controller
 */
class Coupon extends AuthBase
{
    /**
     * 获取优惠券列表
     */
    public function lists()
    {
        $this->limitRequestMethod('GET');

        $where = [
            'user_id' => ['=', $this->userid],
        ];
        $trash = input('trash/d', 0);
        if ($trash) {
            $where['delete_at'] = ['>', 0];
        } else {
            $where['delete_at'] = null;
        }

        //状态查找
        $status = input('status/s', '');
        if ($status != '') {
            $where['status'] = ['=', $status];
        }

        //分类查找
        $category = input('category_id/s', '');
        if ($category) {
            $where['cate_id'] = ['=', $category];
        }

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'id as coupon_id, cate_id, amount, code, type, remark, status, create_at, expire_at',
            'page' => input('page/d', 1),
            'limit' => input('limit/d', 0),
        ];

        CouponService::getList($where, $config);
    }

    /**
     * 新增优惠券
     */
    public function add()
    {
        $this->limitRequestMethod('POST');

        $count = input('count/d', 0);
        if (empty($count) || $count <= 0 || $count > 200) {
            error(414, '生成优惠券数量范围应在 1~200 之间');
        }

        $amount = input('amount/f', 0);
        if ($amount <= 0) {
            $this->error('折扣不能小于等于0！');
        }
        $type = input('type/s', '');
        if ($type == 100 && $amount >= 100) {
            $this->error('折扣不能大于等于100%！');
        }

        $expire = input('expire/d', 0);
        if ($expire <= 0) {
            $this->error('有效期不能小于等于0！');
        }
        $expire = time() + ($expire * 86400);

        $category = input('category_id/s', '');
        if (empty($category)) {
            error(414, '请指定分类');
        }

        $data = [
            'user_id' => $this->userid,
            'cate_id' => $category,
            'type' => $type,
            'amount' => $amount,
            'remark' => input('remark/s', ''),
            'status' => 1,
            'create_at' => time(),
            'expire_at' => $expire,
        ];

        CouponService::addCoupon($this->userid, $data, $count);
    }

    /**
     * 删除到回收站
     */
    public function del()
    {
        $this->limitRequestMethod('POST');

        $id = input('coupon_id', '');
        if (empty($id)) {
            error(414, '请指定优惠券');
        }

        $where = [
            'id' => $id,
            'user_id' => $this->userid,
            'delete_at' => null,
        ];

        $goods = CouponService::find('goods_coupon', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = CouponService::del('goods_coupon', $where, true);
            if ($res['status']) {
                success([], '删除成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '优惠券不存在');
        }
    }

    /**
     * 从回收站恢复
     */
    public function restore()
    {
        $this->limitRequestMethod('POST');

        $id = input('coupon_id', '');
        if (empty($id)) {
            error(414, '请指定优惠券');
        }

        $where = [
            'id' => ['=', $id],
            'user_id' => ['=', $this->userid],
            'delete_at' => ['>', 0],
        ];

        $goods = CouponService::find('goods_coupon', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = CouponService::restore('goods_coupon', $where);
            if ($res['status']) {
                success([], '恢复成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '优惠券不存在');
        }
    }

    /**
     * 从回收站删除
     */
    public function hardDel()
    {
        $this->limitRequestMethod('POST');

        $id = input('coupon_id', '');
        if (empty($id)) {
            error(414, '请指定优惠券');
        }

        $where = [
            'id' => ['=', $id],
            'user_id' => ['=', $this->userid],
            'delete_at' => ['>', 0],
        ];

        $goods = CouponService::find('goods_coupon', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = CouponService::del('goods_coupon', $where);
            if ($res['status']) {
                success([], '删除成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '优惠券不存在');
        }
    }
}
