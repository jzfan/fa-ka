<?php

namespace app\manage\controller;

use controller\BasicAdmin;
use service\LogService;
use think\Db;

/**
 * Class Content
 *
 * @package app\manage\controller
 */
class Content extends BasicAdmin {

    /**
     * 删除数据
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function del() {
        if ($this->request->isPost()) {

            //令牌验证
            if (!\think\Validate::token('', '', input(''))) {
                $this->error('非法请求');
            }

            //数据过滤
            $model = input('model');
            switch ($model) {
                case 'order':
                case 'cash':
                    break;
                default:
                    $this->error('不支持的数据操作类型');
            }

            //短信验证码验证
            $chcode = input('chcode');
            if ($chcode) {
                $smsHelper = new \app\common\util\Sms();
                if (!$smsHelper->verifyCode(session('user.phone'), $chcode, 'delete_order')) {
                    $this->error($smsHelper->getError());
                }
            }

            //日期验证
            $date_range = input('order_date_range');
            if ($date_range && strpos($date_range, ' - ') !== false) {
                list($startTime, $endTime) = explode(' - ', $date_range);
                $startTime = strtotime($startTime);
                $endTime = strtotime($endTime);
                if ($startTime > $endTime) {
                    $this->error('时间范围错误');
                }
                $date = strtotime(date('Y-m-d', strtotime("-30 day")));
                if ($startTime > $date) {
                    $this->error('不可删除30天内数据');
                }
            } else {
                $this->error('请选择时间范围');
            }

            $where['create_at'] = ['BETWEEN', [$startTime, $endTime]];
            $count = Db::name($model)->where($where)->count();
            if ($count == 0) {
                $this->error('该日期范围没有数据！');
            }
            //删除前进行一次数据备份
            $this->backup($model, $where);
            $res = Db::name($model)->where($where)->delete();
            if ($res) {
                LogService::write('数据管理', '批量删除' . $model . '表数据成功，删除数量：' . $count);
                $this->success('成功删除' . $res . '条数据！');
            }

            $this->error('删除失败！');
        }

        $max_date = date('Y-m-d', strtotime("-3 day"));
        $this->assign('max_date', $max_date);
        return view();
    }

    /**
     * 备份数据
     *
     * @param $model
     * @param $where
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function backup($model, $where) {
        $count = Db::name($model)->where($where)->count('id');
        if ($count < 5000) {
            $datas = Db::name($model)->where($where)->select();
            foreach ($datas as $data) {
                $keys = array_keys($data);
                $values = [];

                foreach ($keys as $key) {
                    $values[] = $data[$key];
                }

                $keys = join('`,`', $keys);
                $keys = "`" . $keys . "`";

                $values = array_map(function ($v) {
                    return htmlspecialchars($v);
                }, $values);
                $values = join('\',\'', $values);
                $values = "'" . $values . "'";


                $mysql = '';
                $mysql .= "INSERT INTO `$model`($keys) VALUES($values);\r\n";

                file_put_contents(ROOT_PATH . 'backup/' . $model . time() . '.sql', $mysql, FILE_APPEND);
            }
        } else {
            $index = 0;
            while ($index < $count) {
                $datas = Db::name($model)->where($where)->limit($index, 5000)->select();
                foreach ($datas as $data) {
                    $keys = array_keys($data);
                    $values = [];

                    foreach ($keys as $key) {
                        $values[] = $data[$key];
                    }

                    $keys = join('`,`', $keys);
                    $keys = "`" . $keys . "`";

                    $values = array_map(function ($v) {
                        return htmlspecialchars($v);
                    }, $values);
                    $values = join('\',\'', $values);
                    $values = "'" . $values . "'";


                    $mysql = '';
                    $mysql .= "INSERT INTO `$model`($keys) VALUES($values);\r\n";

                    file_put_contents(ROOT_PATH . 'backup/' . $model . time() . '.sql', $mysql, FILE_APPEND);
                }
                $index += 5000;
            }
        }
    }
}