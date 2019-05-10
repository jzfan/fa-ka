<?php

namespace app\api\controller;

use think\Controller;
use think\Db;

/**
 * api 页面服务
 * Class Pages
 *
 * @package app\api\controller
 */
class Pages extends Controller {

    /**
     * 联系我们
     */
    public function contactUs() {
        $data = [
            'mobile' => sysconf('site_info_tel'),
            'qq' => sysconf('site_info_qq'),
            'mobile_desc' => sysconf('site_info_tel_desc'),
            'qq_desc' => sysconf('site_info_qq_desc'),
            'qrcode' => sysconf('site_info_qrcode'),
            'qrcode_desc' => sysconf('site_info_qrcode_desc'),
            'address' => sysconf('site_info_address'),
            'email' => sysconf('site_info_email'),
        ];

        $this->assign($data);
        $res = $this->fetch();
//        success($res, '获取成功');
        return $res;
    }

    /**
     * 展示文章
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function article() {
        //获取指定内容

        $id = input('article_id/d', 0);
        if (empty($id)) {
            $this->error('请指定文章');
        }

        $article = Db::name('article')->where('id', $id)->find();
        $article['content'] = htmlspecialchars_decode($article['content']);
        $this->assign('article', $article);
        return $this->fetch();
    }

    
    public function privacyPolicy() {
        return $this->fetch();
    }
}
