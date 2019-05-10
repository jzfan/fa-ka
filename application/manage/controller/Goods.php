<?php
/**
 * 商品管理
 */

namespace app\manage\controller;

use controller\BasicAdmin;
use think\Db;
use think\Request;
use app\common\model\User as UserModel;
use app\common\model\Goods as GoodsModel;
use service\LogService;

class Goods extends BasicAdmin
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('self_url', '#' . Request::instance()->url());
        $this->assign('self_no_url', Request::instance()->url());
    }

    public function index()
    {
        $this->assign('title', '商品列表');

        ////////////////// 查询条件 //////////////////
        $query = [
            'user_id' => input('user_id/s', ''),
            'username' => input('username/s', ''),
            'name' => input('name/s', ''),
            'status' => input('status/s', ''),
            'date_range' => input('date_range/s', ''),
        ];
        $where = $this->genereate_where($query);

        $goodsList = Db::name('goods')->alias('a')
            ->join('user b', 'a.user_id = b.id')
            ->join('link c', 'a.id = c.relation_id AND c.relation_type = "goods"')
            ->field('a.*,b.username,c.token as link')
            ->where($where)
            ->order('id desc')
            ->paginate(30, false, [
                'query' => $query
            ]);

        // 分页
        $page = str_replace('href="', 'href="#', $goodsList->render());
        $this->assign('page', $page);
        $this->assign('goodsList', $goodsList);

        $sum_money = Db::name('goods')->alias('a')->where($where)->sum('price');
        $this->assign('sum_money', $sum_money);
        $sum_order = Db::name('goods')->alias('a')->where($where)->count();
        $this->assign('sum_order', $sum_order);
        return $this->fetch();
    }

    /**
     * 生成查询条件
     */
    protected function genereate_where($params)
    {
        $where = [];
        $action = Request::instance()->action();
        switch ($action) {
            case 'index':
                if ($params['user_id'] !== '') {
                    $where['a.user_id'] = $params['user_id'];
                }
                if ($params['username']) {
                    $ids = Db::name('User')->field('id')->where(['username' => ['like', '%' . $params['username'] . '%']])->select();
                    if ($ids) {
                        $temp = [];
                        foreach ($ids as $id) {
                            $temp[] = $id['id'];
                        }
                        $temp = implode(',', $temp);
                        $where['a.user_id'] = ['IN', $temp];
                    } else {
                        $where['a.user_id'] = 0;
                    }
                }
                if ($params['name'] !== '') {
                    $where['a.name'] = ['like', '%' . $params['name'] . '%'];
                }
                if ($params['status'] !== '') {
                    $where['a.status'] = $params['status'];
                }
                if ($params['date_range'] && strpos($params['date_range'], ' - ') !== false) {
                    list($startDate, $endTime) = explode(' - ', $params['date_range']);
                    $where['a.create_at'] = ['between', [strtotime($startDate . ' 00:00:00'), strtotime($endTime . ' 23:59:59')]];
                }
                $where['a.delete_at'] = null;
                break;
        }
        return $where;
    }

    /**
     * 改变状态
     */
    public function change_status()
    {
        if (!$this->request->isAjax()) {
            $this->error('错误的提交方式！');
        }
        $id = input('id/d', 0);
        $status = input('value/d', 1);
        $goods = Db::name('Goods')->where([
            'id' => $id,
        ])->find();
        if ($goods['is_freeze'] == 1) {
            $this->error('请先解冻商品后再上架！');
        }

        $res = Db::name('Goods')->where([
            'id' => $id,
        ])->update([
            'status' => $status
        ]);
        $remark = $status == 1 ? '上架' : '下架';
        if ($res !== false) {
            LogService::write('商品管理', '成功' . $remark . '商品，商品ID:' . $id);
            $this->success('更新成功！', '');
        } else {
            $this->error('更新失败，请重试！');
        }
    }

    /**
     * 改变冻结状态
     */
    public function change_freeze()
    {
        if (!$this->request->isAjax()) {
            $this->error('错误的提交方式！');
        }
        $id = input('id/d', 0);
        $status = input('value/d', 1);
        $update_data = [
            'is_freeze' => $status
        ];
        if ($status == 1) {
            $update_data['status'] = !$status;
        }
        $res = Db::name('Goods')->where([
            'id' => $id,
        ])->update($update_data);
        $remark = $status == 1 ? '冻结' : '解冻';
        if ($res !== false) {
            LogService::write('商品管理', '成功' . $remark . '商品，商品ID:' . $id);
            $this->success('更新成功！', '');
        } else {
            $this->error('更新失败，请重试！');
        }
    }

    public function change_trade_no_status()
    {
        if ($this->request->isGet()) {
            return $this->fetch();
        }
        if ($this->request->isPost()) {
            $data = input('');
            if (strlen($data['user_order_profix']) > 3) {
                $this->error('订单前缀不能超过3位');
            }
            sysconf('order_type', $data['order_type']);
            sysconf('user_order_profix', $data['user_order_profix']);
            $this->success('操作成功', '');
        }
    }

    /**
     * 删除商品
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        if ($this->request->post()) {
            $id = input('id/d', 0);
            $goods = GoodsModel::get(['id' => $id]);
            if (!$goods) {
                return J(1, '不存在该商品！');
            }

            $res = $goods->delete();
            if ($res !== false) {
                LogService::write('商品管理', '成功' . '删除商品，商品ID:' . $id);
                return J(200, '删除成功！');
            } else {
                return J(500, '删除失败！');
            }
        }
    }
}
