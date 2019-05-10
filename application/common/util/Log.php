<?php
/**
 * 日志类
 * @author Veris
 */
namespace app\common\util;

use think\Db;
use think\Request;

class Log
{
    /**
     * 表名
     */
    const TABLE_NAME = 'log';

    /**
     * 记录日志
     * @param  string $businessType 业务类型
     * @param  string $content      内容
     * @return boolean              状态
     */
    public static function record($businessType,$content)
    {
        if(is_array($content)){
            $content=json_encode($content);
        }
        return Db::table(self::TABLE_NAME)->insert([
            'business_type' =>$businessType,
            'content'       =>$content,
            'ua'            =>isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'',
            'uri'           =>isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'',
            'create_at'     =>$_SERVER['REQUEST_TIME'],
            'create_ip'     =>Request::instance()->ip(),
        ]);
    }

    /**
     * 返回实例
     */
    public static function instance()
    {
        return Db::table(self::TABLE_NAME);
    }
}
