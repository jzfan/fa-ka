<?php

namespace app\api\service;

use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * Class Base
 * @package app\api\service
 */
class BaseService
{

    /**
     * 获取单条数据
     * @param $model
     * @param array $where
     * @param array $config
     * @return array
     */
    public static function find($model, $where = [], $config = [])
    {
        if (empty($model)) {
            return wrong('请指定模块');
        }

        if (empty($where)) {
            $where = '1=1';
        }

        if (!isset($config['fields'])) {
            $config['fields'] = '*';
        }

        if (!isset($config['order'])) {
            $config['order'] = 'id desc';
        }

        try {
            $record = Db::name($model)->field($config['fields'])->where($where)->order($config['order'])->find();
            if ($record) {
                return right($record, '获取成功');
            } else {
                return wrong('记录不存在', 414);
            }
        } catch (DbException $e) {
            return wrong('获取失败，原因：' . $e->getMessage());
        }

    }

    /**
     * 获取数据列表
     * @param $model
     * @param array $where
     * @param $config
     * @return array
     */
    public static function lists($model, $where = [], $config = [])
    {

        if (empty($model)) {
            return wrong('请指定模块');
        }

        if (empty($where)) {
            $where = '1=1';
        }

        if (!isset($config['page'])) {
            $config['page'] = 1;
        }

        if (!isset($config['limit'])) {
            $config['limit'] = 0;
        }

        if (!isset($config['fields'])) {
            $config['fields'] = '*';
        }

        if (!isset($config['order'])) {
            $config['order'] = 'id desc';
        }

        try {
            if ($config['limit'] > 0) {
                $data = Db::name($model)->field($config['fields'])->where($where)->order($config['order'])->paginate($config['limit'], false, [
                    'page' => $config['page'],
                ]);
            } else {
                $records = Db::name($model)->field($config['fields'])->where($where)->order($config['order'])->select();

                $total_page = 1;
                if (empty($records)) {
                    $records = [];
                    $total = 0;
                } else {
                    $total = Db::name($model)->where($where)->count();
                }

                $data = [
                    'data' => $records,
                    'total' => $total,
                    'current_page' => $config['page'],
                    'per_page' => $total,
                    'last_page' => $total_page,
                ];
            }

            return right($data, '获取成功');
        } catch (DbException $e) {
            return wrong('获取失败，原因：' . $e->getMessage());
        }
    }

    /**
     * 新增数据
     * @param $model
     * @param array $data
     * @param bool $replace
     * @param bool $getLastInsID
     * @param null $sequence
     * @return array
     */
    public static function add($model, $data = [], $replace = false, $getLastInsID = true, $sequence = null)
    {
        if (empty($model)) {
            return wrong('请指定模块');
        }

        if (empty($data)) {
            return wrong('请传入数据');
        }

        try {
            $res = Db::name($model)->insert($data, $replace, $getLastInsID, $sequence);
            if ($res) {
                return right($res, '数据新增成功');
            } else {
                return wrong('数据新增失败');
            }
        } catch (DbException $e) {
            return wrong('数据新增失败，原因：' . $e->getMessage());
        } catch (\Throwable $e) {
            return wrong('数据新增失败，原因：' . $e->getMessage());
        }

    }

    /**
     * @param $model
     * @param array $where
     * @param array $data
     * @return array
     */
    public static function edit($model, $where = [], $data = [])
    {
        if (empty($model)) {
            return wrong('请指定模型');
        }

        if (empty($where)) {
            return wrong('请指定条件');
        }

        try {
            Db::name($model)->where($where)->update($data);
            return right([], '数据修改成功');
        } catch (DbException $e) {
            return wrong('数据修改失败，原因：' . $e->getMessage());
        }
    }

    /**
     * @param $model
     * @param array $where
     * @param bool $soft
     * @param string $deleteField
     * @return array
     */
    public static function del($model, $where = [], $soft = false, $deleteField = 'delete_at')
    {

        if (empty($model)) {
            return wrong('请指定模型');
        }

        if (empty($where)) {
            return wrong('请指定条件');
        }

        try {
            if ($soft) {
                //尝试软删
                Db::name($model)->where($where)->update([$deleteField => time()]);
                return right([], '删除成功');
            } else {
                //直接删除
                Db::name($model)->where($where)->delete();
                return right([], '删除成功');
            }
        } catch (DbException $e) {
            return wrong('删除失败，原因：' . $e->getMessage());
        }
    }

    /**
     * 恢复软删数据
     * @param $model
     * @param array $where
     * @param string $deleteField
     * @return array
     */
    public static function restore($model, $where = [], $deleteField = 'delete_at')
    {
        if (empty($model)) {
            return wrong('请指定模型');
        }

        if (empty($where)) {
            return wrong('请指定条件');
        }

        try {
            //尝试恢复
            Db::name($model)->where($where)->update([$deleteField => null]);
            return right([], '恢复成功');
        } catch (DbException $e) {
            return wrong('恢复失败，原因：' . $e->getMessage());
        }
    }

    /**
     * 变更状态
     * @param $model
     * @param $where
     * @param $flag
     * @param string $field
     * @return array
     */
    public static function toggleField($model, $where, $flag = '', $field = 'status')
    {
        $res = self::find($model, $where, ['fields' => $field]);

        if ($res['status']) {
            if (empty($flag)) {
                //没有传入转换状态，确定转换后状态
                $flag = $res['data'][$field] ? 0 : 1;
            }

            $data = [$field => $flag];
            //变更状态
            $res = self::edit($model, $where, $data);
            if ($res['status']) {
                return right($data, '更改成功');
            } else {
                return wrong('更新失败', 500);
            }
        } else {
            return wrong('记录不存在', 414);
        }
    }
}
