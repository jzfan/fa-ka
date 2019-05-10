<?php

namespace app\merchant\controller;

use app\common\model\Goods as GoodsModel;
use app\common\model\GoodsCard as CardModel;
use app\common\model\GoodsCategory as CategoryModel;
use service\MerchantLogService;
use think\Controller;
use think\Db;
use think\Request;

class GoodsCard extends Base
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
                    $where['goods_id'] = ['in', Db::name('Goods')->where(['cate_id' => $params['cate_id']])->column('id')];
                }
                if ($params['goods_id'] !== '') {
                    $where['goods_id'] = $params['goods_id'];
                }
                if ($params['status'] !== '') {
                    $where['status'] = $params['status'];
                }
                if ($params['trade_no'] !== '') {
                    $orderIds = DB::name('order_card')->alias('a')->field('a.card_id')
                        ->join('order b', 'a.order_id=b.id')
                        ->where(['b.trade_no' => $params['trade_no']])->select();

                    if ($orderIds) {
                        $ids = [];
                        foreach ($orderIds as $v) {
                            $ids[] = $v['card_id'];
                        }
                        if (!empty($ids)) {
                            $where['id'] = ['IN', implode(',', $ids)];
                        } else {
                            $where['id'] = -1;
                        }
                    } else {
                        //没有对应的卡
                        $where['id'] = -1;
                    }
                }
                if ($params['contact'] !== '') {
                    $orderIds = DB::name('order_card')->alias('a')->field('a.card_id')
                        ->join('order b', 'a.order_id=b.id')
                        ->where(['b.contact' => $params['contact']])->select();

                    if ($orderIds) {
                        $ids = [];
                        foreach ($orderIds as $v) {
                            $ids[] = $v['card_id'];
                        }
                        if (!empty($ids)) {
                            $where['id'] = ['IN', implode(',', $ids)];
                        } else {
                            $where['id'] = -1;
                        }
                    } else {
                        //没有对应的卡
                        $where['id'] = -1;
                    }
                }
                break;
            case 'ashbin':
                if ($params['cate_id'] !== '') {
                    $where['goods_id'] = ['in', Db::name('Goods')->where(['cate_id' => $params['cate_id']])->column('id')];
                }
                if ($params['goods_id'] !== '') {
                    $where['goods_id'] = $params['goods_id'];
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
        $this->setTitle('虚拟卡列表');
        ////////////////// 查询条件 //////////////////
        $query = [
            'cate_id' => input('cate_id/s', ''),
            'goods_id' => input('goods_id/s', ''),
            'status' => input('status/s', ''),
            'trade_no' => input('trade_no/s', ''),
            'contact' => input('contact/s', ''),
        ];
        $where = $this->genereate_where($query);

        if (input('action/s') == 'dump') {
            set_time_limit(0);
            ini_set('memory_limit', '500M');
            $cards = CardModel::all([
                'user_id' => $this->user->id,
            ]);
            $title = ['序号', '卡号', '卡密', '状态', '添加时间'];
            $data = [];
            foreach ($cards as $k => $card) {
                if ($card->status == 1) {
                    $statusStr = '未售出';
                } elseif ($card->status == 2) {
                    $statusStr = '已售出';
                } elseif ($card->status == 0) {
                    $statusStr = '不可用';
                }
                $data[] = [
                    $k + 1,
                    $card->number,
                    $card->secret,
                    $statusStr,
                    date('Y-m-d H:i:s', $card->create_at),
                ];
            }
            $filename = "虚拟卡_" . date('Ymd');
            generate_excel($title, $data, $filename, '虚拟卡');
        }

        $cards = CardModel::where($where)->order('id desc')->paginate(30, false, [
            'query' => $query,
        ]);

        // 分页
        $page = $cards->render();
        $this->assign('page', $page);
        $this->assign('cards', $cards);

        // 商品分类
        $categorys = CategoryModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
        $this->assign('categorys', $categorys);
        // 商品列表
        $goodsList = GoodsModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
        $this->assign('goodsList', $goodsList);
        return $this->fetch();
    }

    public function add()
    {
        if (!$this->request->isPost()) {
            $this->setTitle('添加虚拟卡');
            // 商品列表
            $goodsList = GoodsModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
            $this->assign('goodsList', $goodsList);
            return $this->fetch();
        }
        $goods_id = input('goods_id/d', 0);
        $goods = GoodsModel::get(['id' => $goods_id, 'user_id' => $this->user->id]);
        if (!$goods) {
            $this->error('不存在该商品！');
        }
        $import_type = input('import_type/s', 1);
        $split_type = input('split_type/s', ' ');
        $content = input('content/s', '');
        $check_card = input('check_card/d', 0);
        if ($import_type == 2 && isset($_FILES['file']) && $_FILES['file']['size'] <= 102400) {
            $content = iconv("gb2312", "utf-8//IGNORE", file_get_contents($_FILES['file']['tmp_name']));
        }

        $arr = explode(PHP_EOL, trim($content));
        $count = count($arr);
        //去除数组两端的空白字符
        $arr = array_map(function ($v) {
            return trim(str_replace(chr(194) . chr(160),' ', $v));
        }, $arr);

        //检查输入是否重卡
        if ($check_card == 1) {
            $arr = array_values(array_unique($arr));
        }
        if ($split_type == '0') { //自动识别
            if (strpos($arr[0], " ") !== false) {
                $split_type = " ";
            } elseif (strpos($arr[0], ",") !== false) {
                $split_type = ",";
            } elseif (strpos($arr[0], "|") !== false) {
                $split_type = "|";
            } elseif (strpos($arr[0], "----") !== false) {
                $split_type = "----";
            } else {
                $split_type = "";
            }
        }
        $cards = [];
        foreach ($arr as $v) {
            if (!empty($split_type)) {
                $card = explode($split_type, $v);
            } else {
                $card = [$v, ''];
            }
            if (isset($card[0])) {
                $card[0] = trim(html_entity_decode($card[0]), chr(0xc2) . chr(0xa0));
            } else {
                continue;
            }
            if ($card[0] === '') {
                continue;
            }
            if (strlen($card[0]) > 255) {
                continue;
            }
            // if(validateURL($card[0])) {//禁止url
            //     $this->error('虚拟卡内容不能包含链接');
            // }
            $number = $card[0];
            if (isset($card[1])) {
                $card[1] = trim(html_entity_decode($card[1]), chr(0xc2) . chr(0xa0));
            } else {
                continue;
            }
            if ($card[1] !== '') {
                if (strlen($card[1]) > 255) {
                    continue;
                }
                // if(validateURL($card[1])) {
                //     $this->error('虚拟卡内容不能包含链接');
                // }
                $secret = $card[1];
            } else {
                $secret = '';
            }
            // 检查重复
            if ($check_card == 1) {
                $isExist = CardModel::get(['user_id' => $this->user->id, 'number' => $number, 'secret'=>$secret]);
                if ($isExist) {
                    continue;
                }
            }
            $cards[] = [
                'user_id' => $this->user->id,
                'goods_id' => $goods_id,
                'number' => $number,
                'secret' => $secret,
                'status' => 1, // 未使用
                'create_at' => $_SERVER['REQUEST_TIME'],
            ];
        }
        if (empty($cards)) {
            $this->error('虚拟卡内容格式不正确, 或卡密已存在');
        }
        $CardModel = new CardModel;
        $res = $CardModel->saveAll($cards);
        $success = count($res);
        if ($res !== false) {
            MerchantLogService::write('成功添加卡密', '成功添加' . $success . '张卡密');
            $this->success("共{$count}张卡密，成功添加{$success}张卡密！", 'index');
        } else {
            $this->error('添加失败！');
        }
    }

    public function del()
    {
        $card_id = input('id/d', 0);
        $card = CardModel::get(['id' => $card_id, 'user_id' => $this->user->id]);
        if (!$card) {
            return J(1, '不存在该卡！');
        }
        $res = $card->delete();
        if ($res !== false) {
            MerchantLogService::write('成功删除卡密', '成功删除卡密，ID:' . $card_id);
            return J(0, '删除成功！');
        } else {
            return J(1, '删除失败！');
        }
    }

    public function batch_del()
    {
        $card_ids = input('ids/a');

        if (empty($card_ids)) {
            return J(1, '删除失败！');
        }
        $cards = CardModel::all(['id' => ['in', $card_ids], 'user_id' => $this->user->id]);
        if (!$cards) {
            return J(1, '不存在该卡！');
        }
        Db::startTrans();
        try {
            foreach ($cards as $key => $card) {
                $res = $card->delete();
                if ($res !== false) {
                    MerchantLogService::write('成功删除卡密', '成功删除卡密，ID:' . $card->id);
                } else {
                    throw new \Exception('批量删除失败，ID:' . $card->id);
                }
            }
            Db::commit();
            return J(0, '删除成功！');
        } catch (\Exception $e) {
            Db::rollback();
            return J(1, '删除失败！');
        }
    }

    /**
     * 回收站
     */
    public function ashbin()
    {
        $this->setTitle('回收站');
        ////////////////// 查询条件 //////////////////
        $query = [
            'cate_id' => input('cate_id/s', ''),
            'goods_id' => input('goods_id/s', ''),
            'status' => input('status/s', ''),
        ];
        $where = $this->genereate_where($query);

        $cards = CardModel::onlyTrashed()->where($where)->order('delete_at desc, id desc')->paginate(30, false, [
            'query' => $query,
        ]);
        // 分页
        $page = $cards->render();
        $this->assign('page', $page);
        $this->assign('cards', $cards);

        // 商品分类
        $categorys = CategoryModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
        $this->assign('categorys', $categorys);
        // 商品列表
        $goodsList = GoodsModel::where(['user_id' => $this->user->id])->order('sort desc,id desc')->select();
        $this->assign('goodsList', $goodsList);
        return $this->fetch();
    }

    /**
     * 彻底删除
     */
    public function ashbin_delete()
    {
        $card_id = input('id/d', 0);
        $card = CardModel::onlyTrashed()->where(['id' => $card_id, 'user_id' => $this->user->id])->find();
        if (!$card) {
            return J(1, '不存在该卡！');
        }
        $res = $card->delete(true);
        if ($res !== false) {
            MerchantLogService::write('成功彻底删除卡密', '成功彻底删除卡密，ID:' . $card_id);
            return J(0, '删除成功！');
        } else {
            return J(1, '删除失败！');
        }
    }

    public function ashbin_batch_del()
    {
        $card_ids = input('ids/a');

        if (empty($card_ids)) {
            return J(1, '删除失败！');
        }
        $cards = CardModel::onlyTrashed()->where(['id' => ['in', $card_ids], 'user_id' => $this->user->id])->select();
        if (!$cards) {
            return J(1, '不存在该卡！');
        }
        Db::startTrans();
        try {
            foreach ($cards as $key => $card) {
                $res = $card->delete(true);
                if ($res !== false) {
                    MerchantLogService::write('成功删除卡密', '成功删除卡密，ID:' . $card->id);
                } else {
                    throw new \Exception('批量删除失败，ID:' . $card->id);
                }
            }
            Db::commit();
            return J(0, '删除成功！');
        } catch (\Exception $e) {
            Db::rollback();
            return J(1, '删除失败！');
        }
    }

    /**
     * 清空回收站
     */
    public function ashbin_clear()
    {
        // 删除未使用且已过期的优惠券
        $res = CardModel::withTrashed()->where([
            'user_id' => $this->user->id,
        ])->where('delete_at', '>',0)
        ->delete();

        if ($res !== false) {
            MerchantLogService::write('清空虚拟卡回收站成功', '清空虚拟卡回收站成功');
            return J(0, '删除成功！');
        } else {
            return J(1, '删除失败！');
        }
    }

    /**
     * 恢复
     */
    public function ashbin_restore()
    {
        $card_id = input('id/d', 0);
        $card = CardModel::onlyTrashed()->where(['id' => $card_id, 'user_id' => $this->user->id])->find();
        if (!$card) {
            return J(1, '不存在该卡！');
        }
        $res = $card->restore();
        if ($res !== false) {
            MerchantLogService::write('恢复虚拟卡成功', '恢复虚拟卡成功，ID:' . $card_id);
            return J(0, '恢复成功！');
        } else {
            return J(1, '恢复失败！');
        }
    }

    /**
     * 批量恢复
     */
    public function ashbin_batch_restore()
    {
        $ids = input('ids/a', []);
        $cards = CardModel::onlyTrashed()->where(['id' => ['IN', $ids], 'user_id' => $this->user->id])->select();
        if (!$cards) {
            return J(1, '不存在该卡！');
        }

        Db::startTrans();
        try {
            foreach ($ids as $id) {
                $res = CardModel::update(['delete_at' => null], ['id' => $id, 'user_id' => $this->user->id], 'delete_at');
                if ($res !== false) {
                    MerchantLogService::write('恢复虚拟卡成功', '恢复虚拟卡成功，ID:' . $id);
                } else {
                    throw new \Exception('批量恢复失败，ID:' . $id);
                }
            }
            Db::commit();
            return J(0, '恢复成功！');
        } catch (\Exception $e) {
            Db::rollback();
            return J(1, $e->getMessage());
        }
    }

    /**
     * AJAX获取商品分类下的商品
     */
    public function ajax_get_category_goods()
    {
        if (Request::instance()->isAjax()) {
            $cid = input('cid/d', 0);
            $goodsList = [];
            if ($cid) {
                $where['user_id'] = $this->user->id;
                $where['cate_id'] = $cid;
                $goodsList = GoodsModel::where($where)->field('id,name')->order('sort desc,id desc')->select();
            }
            echo json_encode($goodsList);
            exit;
        }
    }

    // 导出商品库存卡密
    public function dumpCards()
    {
        $goods_id = input('goods_id/d', 0);
        if (!$goods_id) {
            $this->error('未指定商品！');
        }
        $goods = GoodsModel::get(['id' => $goods_id, 'user_id' => $this->user->id]);
        if (!$goods) {
            $this->error('不存在该商品！');
        }
        $status = input('status/s', '1,2');
        $range = input('range/d', 0);
        $number = input('number/d', 0);
        $del = input('del/d', 0);
        $need_goods_name = input('need_goods_name/d', 0);
        $file_type = input('file_type/d', 0);
        if ($range == 1 && !$number) {
            $this->error('请输入导出数量！');
        }
        $count = $cards = Db::name('goods_card')
            ->where([
                'goods_id' => $goods_id,
                'user_id' => $this->user->id,
                'delete_at' => null,
            ])->where('status', 'in', $status)->count();

        if ($count == 0) {
            $this->error('该商品暂无库存卡密');
        }
        if ($range == 1) {
            if ($number > $count) {
                $number = $count;
            }
            $cards = Db::name('goods_card')
                ->where([
                    'goods_id' => $goods_id,
                    'user_id' => $this->user->id,
                    'delete_at' => null,
                ])
                ->where('status', 'in', $status)
                ->lock(true)
                ->limit(0, $number)
                ->select();
        } else {
            $cards = Db::name('goods_card')
                ->where([
                    'goods_id' => $goods_id,
                    'user_id' => $this->user->id,
                    'delete_at' => null,
                ])
                ->where('status', 'in', $status)
                ->lock(true)
                ->select();
        }
        $data = [];
        if ($need_goods_name) {
            $title = ['序号', '商品名称', '卡号', '卡密'];
            foreach ($cards as $k => $card) {
                $data[] = [
                    $k + 1,
                    $goods->name,
                    $card['number'],
                    $card['secret'],
                ];
            }
        } else {
            $title = ['序号', '卡号', '卡密'];
            foreach ($cards as $k => $card) {
                $data[] = [
                    $k + 1,
                    $card['number'],
                    $card['secret'],
                ];
            }
        }
        if ($del) {
            foreach ($cards as $k => $card) {
                Db::name('goods_card')->where('id', $card['id'])->update(['delete_at' => time()]);
            }
        }
        $filename = "{$goods->name}的虚拟卡_" . date('Ymd');
        MerchantLogService::write('导出商品库存卡密', '导出ID为' . $goods_id . '的商品库存卡密');
        if ($file_type == 0) { //导出excel文件
            generate_excel($title, $data, $filename, $goods->name);
        } else { //导出Txt文件
            generate_txt($title, $data, $filename, $goods->name);
        }
    }
}
