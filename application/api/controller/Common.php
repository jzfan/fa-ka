<?php

namespace app\api\controller;

use app\common\util\Sms;
use service\FileService;
use think\Db;
use think\Exception;
use think\exception\DbException;
use think\Request;

/**
 * 通用方法
 * Class Common
 *
 * @package app\api\controller
 */
class Common extends ApiBase {
    /**
     * 发送短信
     *
     * @throws \think\exception\DbException
     */
    public function sendSms() {
        $this->limitRequestMethod('POST');

        $mobile = input('mobile/s', '');
        $screen = input('screen/s', '');

        //检查手机
        if (empty($mobile)) {
            error(414, '请填写手机号');
        }

        //检查场景
        switch ($screen) {
            case 'register':
            case 'forgot':
                break;
            default:
                error(414, '暂不支持的短信场景');
        }

        //发送短信
        $sms = new Sms();
        if ($sms->sendCode($mobile, $screen)) {
            success([], '发送成功，请注意查收');
        } else {
            error(500, $sms->getError());
        }
    }


    /**
     * 获取协议
     *
     * @throws \think\exception\HttpResponseException
     */
    public function getAgreement() {
        $this->limitRequestMethod('GET');

        try {
            $data = Db::name('article')
                      ->field('title,content,create_at')
                      ->where('id', 13)->find();

            $data['create_at'] = date('Y-m-d H:i:s', $data['create_at']);
            success($data, '获取成功');
        } catch (DbException $e) {
            error(500, '协议不存在');
        }
    }

    /**
     * 获取页面风格
     */
    public function getTheme() {
        $this->limitRequestMethod('GET');

        success(config('pay_themes'), '获取成功');
    }

    /**
     * 获取银行列表
     */
    public function getBankList() {
        $this->limitRequestMethod('GET');

        success([
            ['name' => '中国工商银行', 'value' => '中国工商银行'],
            ['name' => '中国建设银行', 'value' => '中国建设银行'],
            ['name' => '中国农业银行', 'value' => '中国农业银行'],
            ['name' => '中国邮政储蓄银行', 'value' => '中国邮政储蓄银行'],
            ['name' => '招商银行', 'value' => '招商银行'],
            ['name' => '农村信用合作社', 'value' => '农村信用合作社'],
            ['name' => '兴业银行', 'value' => '兴业银行'],
            ['name' => '广东发展银行', 'value' => '广东发展银行'],
            ['name' => '深圳发展银行', 'value' => '深圳发展银行'],
            ['name' => '民生银行', 'value' => '民生银行'],
            ['name' => '交通银行', 'value' => '交通银行'],
            ['name' => '中信银行', 'value' => '中信银行'],
            ['name' => '光大银行', 'value' => '光大银行'],
            ['name' => '中国银行', 'value' => '中国银行'],
        ], '获取成功');
    }

    /**
     * 获取系统公告
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNotices() {
        $this->limitRequestMethod('GET');

        $notices = Db::name('article')->where(['cate_id' => 1, 'status' => 1])
                     ->field('id as notice_id,title')
                     ->limit(5)->order('id desc')->select();

        if (empty($notices)) {
            $notices = [];
        } else {
            foreach ($notices as &$notice) {
                $notice['url'] = Request::instance()->domain() . '/api/pages/article/' . $notice['notice_id'];
            }
        }

        success($notices, '获取成功');
    }


    /**
     * 获取公告详情
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNoticeDetail() {
        $this->limitRequestMethod('GET');

        $id = input('notice_id/d', '');
        if (empty($id)) {
            error(414, '请指定公告');
        }

        $notice = Db::name('article')->where(['id' => $id, 'cate_id' => 1, 'status' => 1])
                    ->field('id as notice_id,title,content,create_at')
                    ->find();

        if (empty($notice)) {
            error(414, '公告不存在');
        } else {
            $notice['create_at'] = date('Y-m-d H:i:s', $notice['create_at']);
        }

        success($notice, '获取成功');
    }

    /**
     * 检查是否有版本更新
     */
    public function getLastVersion() {
        $this->limitRequestMethod('GET');

        $platform = input('platform/s', '');
        $version  = input('version/s');

        //获取最新发布的版本
        $lastVersion = Db::name('app_version')->field('version,create_at,package,remark')->where(['platform' => $platform])->order('version desc, id desc')->find();

        $is_last = $this->versiongt($version, $lastVersion['version']);

        $lastVersion['create_at'] = date('Y-m-d H:i:s', $lastVersion['create_at']);

        success(['last_version' => $lastVersion, 'is_last' => $is_last]);
    }

    function versiongt($version1, $version2) {
        $version1 = explode(".", $version1);
        $version2 = explode(".", $version2);
        foreach ($version1 as $key => $value) {
            if ($version1[$key] > $version2[$key]) {
                return true;
            } elseif ($version1[$key] == $version2[$key]) {
                continue;
            } else {
                return false;
            }
        }

        //来到这里就证明前面都一样，比较长度
        if (count($version1) >= count($version2)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 上传文件，返回 url
     */
    function upload() {
        $upload = getUploadFile('file', true);
        if ($upload['status']) {
            success($upload['data']['file']);
        } else {
            error(500, $upload['msg']);
        }
    }
}
