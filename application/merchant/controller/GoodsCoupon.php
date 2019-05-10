<?php

namespace app\merchant\controller;

use app\common\model\GoodsCategory as CategoryModel;
use app\common\model\GoodsCoupon as CouponModel;
use service\MerchantLogService;
use think\Controller;
use think\Db;
use think\Request;

class GoodsCoupon extends Base
{
    /**
     * 生成查询条件
     */
    protected function genereate_where($params)
    {
        $where = [];
        $where['user_id'] = $this->user->id;
        $action = $this->request->action();
        switch ($action) {
            case 'index':
                if ($params['cate_id'] !== '') {
                    $where['cate_id'] = $params['cate_id'];
                }
                if ($params['status'] !== '') {
                    $where['status'] = $params['status'];
                }
                break;
            case 'trash':
                $where['expire_at'] = ['>', time()];
                if ($params['cate_id'] !== '') {
                    $where['cate_id'] = $params['cate_id'];
                }
                if ($params['status'] !== '') {
                    $where['status'] = $params['status'];
                }
                break;
        }
        return $where;
    }

    public function index()
    {
        $this->setTitle('优惠券列表');
        ////////////////// 查询条件 //////////////////
        $query = [
            'cate_id' => input('cate_id/s', ''),
            'status' => input('status/s', ''),
        ];
        $where = $this->genereate_where($query);

        // 删除未使用且已过期的优惠券
        CouponModel::where([
            'status' => 1,
            'user_id' => $this->user->id,
        ])->where('expire_at', '< time', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']))
            ->update([
                'delete_at' => time(),
            ]);

        $coupons = CouponModel::where($where)->order('id desc')->paginate(30, false, [
            'query' => $query,
        ]);
        // 分页
        $page = $coupons->render();
        $this->assign('page', $page);
        $this->assign('coupons', $coupons);

        // 商品分类
        $categorys = CategoryModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
        $this->assign('categorys', $categorys);
        return $this->fetch();
    }

    /**
     * 回收站
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function trash()
    {
        $this->setTitle('优惠券回收站');
        ////////////////// 查询条件 //////////////////
        $query = [
            'cate_id' => input('cate_id/s', ''),
            'status' => input('status/s', ''),
        ];
        $where = $this->genereate_where($query);

        // 删除未使用且已过期的优惠券
        CouponModel::where([
            'status' => 1,
            'user_id' => $this->user->id,
        ])->where('expire_at', '< time', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']))
            ->update([
                'delete_at' => time(),
            ]);

        $coupons = CouponModel::onlyTrashed()->where($where)->order('id desc')->paginate(30, false, [
            'query' => $query,
        ]);
        // 分页
        $page = $coupons->render();
        $this->assign('page', $page);
        $this->assign('coupons', $coupons);

        // 商品分类
        $categorys = CategoryModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
        $this->assign('categorys', $categorys);
        return $this->fetch();
    }

    public function add()
    {
        if (!$this->request->isPost()) {
            $this->setTitle('添加优惠券');
            // 商品分类
            $categorys = CategoryModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
            $this->assign('categorys', $categorys);
            return $this->fetch();
        }
        $cate_id = input('cate_id/d', 0);
        if ($cate_id != 0) {
            $cate = CategoryModel::get(['id' => $cate_id, 'user_id' => $this->user->id]);
            if (!$cate) {
                $this->error('不存在该商品分类！');
            }
        }
        $type = input('type/d', 1);
        $amount = input('amount/f', 0);
        if ($amount <= 0) {
            $this->error('折扣不能小于等于0！');
        }
        if ($type == 100 && $amount >= 100) {
            $this->error('折扣不能大于等于100%！');
        }
        $quantity = input('quantity/d', 0);
        if ($quantity <= 0 || $quantity > 200) {
            $this->error('生成数量需在 0 到 200 之间！');
        }
        $expire_day = input('expire_day/d', 0);
        if ($expire_day <= 0) {
            $this->error('有效期不能小于等于0！');
        }
        // 创建优惠券
        $counpons = [];
        for ($i = 0; $i < $quantity; $i++) {
            $counpons[] = [
                'user_id' => $this->user->id,
                'cate_id' => $cate_id,
                'type' => $type,
                'amount' => $amount,
                'code' => strtoupper(substr(md5(uniqid() . $this->user->id), 0, 12) . random_str(4)),
                'remark' => input('remark/s', ''),
                'status' => 1,
                'expire_at' => $expire_day * 86000 + $_SERVER['REQUEST_TIME'],
                'create_at' => $_SERVER['REQUEST_TIME'],
            ];
        }

        $CouponModel = new CouponModel;
        $res = $CouponModel->saveAll($counpons);
        $success = count($res);
        if ($res !== false) {
            MerchantLogService::write('添加优惠券成功', '成功添加' . $success . '张优惠券');
            $this->success("恭喜您，成功添加{$success}张优惠券！", 'index');
        } else {
            $this->error('添加失败！');
        }
    }

    public function del()
    {
        $coupon_id = input('id/d', 0);
        $coupon = CouponModel::get(['id' => $coupon_id, 'user_id' => $this->user->id]);
        if (!$coupon) {
            return J(1, '不存在该优惠券！');
        }
        $res = $coupon->delete();
        if ($res !== false) {
            MerchantLogService::write('删除优惠券成功', '删除优惠券成功，ID:' . $coupon_id);
            return J(0, '删除成功！');
        } else {
            return J(1, '删除失败！');
        }
    }

    public function batch_del()
    {
        $coupon_ids = input('');
        $coupon_ids = isset($coupon_ids['ids']) ? $coupon_ids['ids'] : [];

        if (empty($coupon_ids)) {
            return J(1, '删除失败！');
        }
        $coupons = CouponModel::all(['id' => ['in', $coupon_ids], 'user_id' => $this->user->id]);
        if (!$coupons) {
            return J(1, '不存在该优惠券！');
        }
        Db::startTrans();
        try {
            foreach ($coupons as $key => $coupon) {
                $res = $coupon->delete();
                if ($res !== false) {
                    MerchantLogService::write('删除优惠券成功', '删除优惠券成功，ID:' . $coupon->id);
                } else {
                    throw new \Exception('批量删除失败，ID:' . $coupon->id);
                }
            }
            Db::commit();
            return J(0, '删除成功！');
        } catch (\Exception $e) {
            Db::rollback();
            return J(1, $e->getMessage());
        }
    }

    /**
     * 恢复
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function restore()
    {
        $coupon_id = input('id/d', 0);
        $coupon = CouponModel::onlyTrashed()->where(['id' => $coupon_id, 'user_id' => $this->user->id])->find();
        if (!$coupon) {
            return J(1, '不存在该优惠券！');
        }
        $res = CouponModel::update(['delete_at' => null], ['id' => $coupon_id, 'user_id' => $this->user->id], 'delete_at');
        if ($res !== false) {
            MerchantLogService::write('删除优惠券成功', '删除优惠券成功，ID:' . $coupon_id);
            return J(0, '删除成功！');
        } else {
            return J(1, '删除失败！');
        }
    }

    /**
     * 批量恢复
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function batch_restore()
    {
        $coupon_ids = input('');
        $coupon_ids = isset($coupon_ids['ids']) ? $coupon_ids['ids'] : [];

        if (empty($coupon_ids)) {
            return J(1, '删除失败！');
        }
        $coupons = CouponModel::onlyTrashed()->where(['id' => ['in', $coupon_ids], 'user_id' => $this->user->id])->select();
        if (!$coupons) {
            return J(1, '不存在该优惠券！');
        }
        Db::startTrans();
        try {
            foreach ($coupons as $key => $coupon) {
                $res = $coupon->restore();
                if ($res !== false) {
                    MerchantLogService::write('删除优惠券成功', '删除优惠券成功，ID:' . $coupon->id);
                } else {
                    throw new \Exception('批量删除失败，ID:' . $coupon->id);
                }
            }
            Db::commit();
            return J(0, '删除成功！');
        } catch (\Exception $e) {
            Db::rollback();
            return J(1, $e->getMessage());
        }
    }
}
