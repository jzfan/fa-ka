<?php
/**
 * 通用资源
 */
namespace app\index\controller;

use think\Controller;
use Endroid\QrCode\QrCode;

class Resource extends Controller
{
    /**
     * 生成二维码
     */
    public function generateQrcode()
    {
        $str     =html_entity_decode(urldecode(input('str/s','')));
        $size    =input('size/f',200);
        $padding =input('padding/f',0);
        if(!$str){
            return J(101,'请输入要生成二维码的字符串！');
        }
        header('Content-type: image/png');
        $qrCode = new QrCode($str);
        $qrCode->setSize($size);
        $qrCode->setPadding($padding);
        $qrCode->render();
        die;
    }
}
