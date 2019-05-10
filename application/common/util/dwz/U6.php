<?php
/**
 * U6短网址
 * @author Veris
 */

namespace app\common\util\dwz;

use app\common\util\DWZ;
use service\HttpService;

class U6 extends DWZ
{
    const API_URL = 'http://api.u6.gg/api.php?format=json&url=';

    public function create($url)
    {
        $res = HttpService::get(self::API_URL . urlencode($url));
        if ($res === false) {
            return false;
        }

        $json = json_decode($res, true);
        if (!$json) {
            return false;
        }
        return $json['url'];
    }
}
