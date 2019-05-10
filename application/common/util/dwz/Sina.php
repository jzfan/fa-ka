<?php
/**
 * 新浪短网址
 * @author Veris
 */

namespace app\common\util\dwz;

use app\common\util\DWZ;
use service\HttpService;

class Sina extends DWZ
{
    const API_URL    = 'http://api.t.sina.com.cn/short_url/shorten.json';
    const APP_KEY    = '490472744';
    const APP_SECRET = '970cf7185bd5f93cdff2ea39de76d480';

    public function create($url)
    {
        $res=HttpService::get(SELF::API_URL,[
            'source'   =>SELF::APP_KEY,
            'url_long' =>$url,
        ]);
        if($res===false){
            return false;
        }
        $json=json_decode($res);
        if(!$json){
            return false;
        }
        return $json[0]->url_short;
    }
}
