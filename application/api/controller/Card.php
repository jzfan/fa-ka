<?php

namespace app\api\controller;

use app\api\service\BaseService;
use app\api\service\CardService;
use service\MerchantLogService;
use think\Db;

/**
 * Class Card
 *
 * @package app\api\controller
 */
class Card extends AuthBase {
    /**
     * 获取卡密列表
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists() {
        $this->limitRequestMethod('GET');

        $where = [
            'user_id' => ['=', $this->userid],
        ];

        //是否查找回收站
        $trash = input('trash/d', 0);
        if ($trash) {
            $where['delete_at'] = ['>', 0];
        } else {
            $where['delete_at'] = null;
        }

        //商品查找，默认只返回库存卡密
        $status = input('status/s', 1);
        if ($status != '') {
            $where['status'] = ['=', $status];
        }

        //商品查找
        $goodsId = input('goods_id/d', '');
        if ($goodsId) {
            $where['goods_id'] = ['=', $goodsId];
        }else {
            //分类查找
            $category = input('category_id/d', '');
            if ($category) {
                // 获取分类下所有的商品
                $res = BaseService::lists('goods', [
                    'user_id' => $this->userid,
                    'cate_id' => $category,
                ], [
                    'limit' => 0,
                    'fields' => 'id',
                ]);

                if ($res['status']) {
                    $goodsIds = [];
                    foreach ($res['data']['data'] as $item) {
                        $goodsIds[] = $item['id'];
                    }
                    if (!empty($goodsIds)) {
                        $where['goods_id'] = ['IN', implode(',', $goodsIds)];
                    } else {
                        $where['goods_id'] = ['=', 0];
                    }
                } else {
                    $where['goods_id'] = ['=', 0];
                }
            }
        }

        $config = [
            'order' => input('order/s', 'id desc'),
            'fields' => 'id as card_id, goods_id, number, secret, status, create_at',
            'page' => input('page/d', 1),
            'limit' => input('limit/d', 0),
        ];

        CardService::getLists($where, $config);
    }

    /**
     * 新增卡密
     */
    public function add() {
        $this->limitRequestMethod('POST');

        $goodsId = input('goods_id/s', '');
        if ($goodsId) {
            $goods = Db::name('goods')->where(['id' => $goodsId, 'user_id' => $this->userid])->count();
            if ($goods <= 0) {
                error(414, '不存在该商品！');
            }
        } else {
            error(414, '请指定商品');
        }

        $import_type = input('import_type/s', 1);
        $split_type = input('split_type/s', 1);
        if ($split_type == 1) {
            $split_type = ' ';
        }
        $content = input('content/s', '');
        $check_card = input('check_card/d', 0);
        if ($import_type == 2 && isset($_FILES['file']) && $_FILES['file']['size'] <= 102400) {
            $content = iconv("gb2312", "utf-8", file_get_contents($_FILES['file']['tmp_name']));
        }

        $arr = explode(PHP_EOL, trim($content));
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
            // if (validateURL($card[0])) { //禁止url
            //     error(414, '虚拟卡内容不能包含链接');
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
                // if (validateURL($card[1])) {
                //     error(414, '虚拟卡内容不能包含链接');
                // }
                $secret = $card[1];
            } else {
                $secret = '';
            }
            // 检查重复
            if ($check_card == 1) {
                $isExist = Db::name('goods_card')->where([
                    'user_id' => $this->userid,
                    'number' => $number,
                    'secret' => $secret,
                ])->count();
                if ($isExist > 0) {
                    continue;
                }
            }
            $cards[] = [
                'user_id' => $this->userid,
                'goods_id' => $goodsId,
                'number' => $number,
                'secret' => $secret,
                'status' => 1, // 未使用
                'create_at' => $_SERVER['REQUEST_TIME'],
            ];
        }

        record_file_log('request_params', 'params :' . json_encode($cards));

        if (empty($cards)) {
            error(414, '虚拟卡内容格式不正确，或卡密已存在');
        }

        CardService::addCards($cards);
    }

    /**
     * 删除到回收站
     */
    public function del() {
        $this->limitRequestMethod('POST');

        $id = input('card_id/s', '');
        if (empty($id)) {
            error(414, '请指定卡密');
        }

        $where = [
            'id' => ['IN', $id],
            'user_id' => ['=', $this->userid],
            'delete_at' => null,
        ];

        $goods = CardService::find('goods_card', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = CardService::del('goods_card', $where, true);
            if ($res['status']) {
                if (is_array($id)) {
                    foreach ($id as $i) {
                        MerchantLogService::write('成功删除卡密', '成功删除卡密，ID:' . $i);
                    }
                } else {
                    MerchantLogService::write('成功删除卡密', '成功删除卡密，ID:' . $id);
                }
                success([], '删除成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '卡密不存在');
        }
    }

    /**
     * 从回收站恢复
     */
    public function restore() {
        $this->limitRequestMethod('POST');

        $id = input('card_id/s', '');
        if (empty($id)) {
            error(414, '请指定卡密');
        }

        $where = [
            'id' => ['IN', $id],
            'user_id' => ['=', $this->userid],
            'delete_at' => ['>', 0],
        ];

        $goods = CardService::find('goods_card', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = CardService::restore('goods_card', $where);
            if ($res['status']) {
                if (is_array($id)) {
                    foreach ($id as $i) {
                        MerchantLogService::write('恢复虚拟卡成功', '恢复虚拟卡成功:' . $i);
                    }
                } else {
                    MerchantLogService::write('恢复虚拟卡成功', '恢复虚拟卡成功:' . $id);
                }

                success([], '恢复成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '卡密不存在');
        }
    }

    /**
     * 从回收站删除
     */
    public function hardDel() {
        $this->limitRequestMethod('POST');

        $id = input('card_id/s', '');
        if (empty($id)) {
            error(414, '请指定卡密');
        }

        $where = [
            'id' => ['IN', $id],
            'user_id' => ['=', $this->userid],
            'delete_at' => ['>', 0],
        ];

        $goods = CardService::find('goods_card', $where, ['fields' => 'id']);
        if ($goods['status']) {
            $res = CardService::del('goods_card', $where);
            if ($res['status']) {
                if (is_array($id)) {
                    foreach ($id as $i) {
                        MerchantLogService::write('成功彻底删除卡密', '成功彻底删除卡密:' . $i);
                    }
                } else {
                    MerchantLogService::write('成功彻底删除卡密', '成功彻底删除卡密:' . $id);
                }
                success([], '删除成功');
            } else {
                error(500, $res['msg']);
            }
        } else {
            error(414, '卡密不存在');
        }
    }
}
