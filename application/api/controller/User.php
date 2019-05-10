<?php

namespace app\api\controller;

use app\api\service\LinkService;
use app\api\service\UserService;
use think\Db;
use think\Exception;
use think\exception\DbException;

/**
 * Class User
 *
 * @package app\api\controller
 */
class User extends AuthBase {

    /**
     * User constructor.
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 退出登录
     *
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function logout() {
        $this->limitRequestMethod('POST');

        $platform = input('platform/s');

        Db::name('user_token')->where([
            'user_id'  => $this->userid,
            'platform' => $platform,
            'token'    => $this->loginToken,
        ])->delete();

        success([], '退出登录成功');
    }

    /**
     * 修改密码
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function modifyPassword() {
        $this->limitRequestMethod('POST');

        $old      = input('password_old/s');
        $platform = input('platform/s');
        $new      = input('password/s');
        $confirm  = input('password_confirm/s');

        //检查旧密码
        if (empty($old)) {
            error(414, '请填写旧密码');
        }

        //检查新密码
        if (empty($new)) {
            error(414, '请填写新密码');
        }

        if ($new != $confirm) {
            error(414, '两次密码不一致');
        }

        //校验旧密码
        $old  = md5($this->getRealPassword($platform, $old));
        $user = Db::name('user')->field('id')->where([
            'id'       => $this->userid,
            'password' => $old,
        ])->find();

        if ($user) {
            //重置密码
            $password = $this->getRealPassword($platform, $new);

            $res = Db::name('user')->where(['id' => $this->userid])->update(['password' => md5($password)]);
            if ($res) {
                success([], '密码修改成功');
            } else {
                error(500, '密码修改失败');
            }
        } else {
            error(500, '原密码错误');
        }
    }

    /**
     * 获取登录日志
     */
    public function getLoginLog() {
        $this->limitRequestMethod('GET');

        $config = [
            'order'  => input('order/s', 'id desc'),
            'fields' => 'ip, platform, create_at, user_id',
            'page'   => input('page/d', 1),
            'limit'  => input('limit/d', 0),
        ];

        UserService::getUserLoginLog($this->userid, $config);
    }

    /**
     * 获取收益数据
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStatistic() {
        $this->limitRequestMethod('GET');

        $yesterdayTime = strtotime('-1 day', strtotime(date('Y-m-d')));
        $yesterday     = date('Y-m-d', $yesterdayTime);

        //获取今日统计
        $today = Db::name('order')->field([
            'sum(quantity) as cards_num', //卖出卡数
            'sum(total_price) as amount', //成交额
            'count(id) as order_count', //成交订单笔数
        ])->where([
            'user_id' => $this->userid,
            'status'  => 1,
        ])
                   ->whereTime('success_at', 'today')
                   ->find();

        foreach ($today as &$v) {
            is_numeric($v) ? $v += 0 : $v = 0;
        }

        //获取昨日统计
        $yesterday = Db::name('order')->field([
            'sum(quantity) as cards_num', //卖出卡数
            'sum(total_price) as amount', //成交额
            'count(id) as order_count', //成交订单笔数
        ])->where([
            'user_id' => $this->userid,
            'status'  => 1,
        ])
                       ->whereTime('success_at', 'between', [$yesterday, date('Y-m-d')])
                       ->find();

        foreach ($yesterday as &$v) {
            is_numeric($v) ? $v += 0 : $v = 0;
        }

        //获取菜单项目
        $menu = Db::name('user')->where('id', $this->userid)->value('app_menu');
        if (empty($menu)) {
            //如果为空，获取默认的菜单
            $menus = Db::name('app_menu')->limit(7)->select();
            $menu  = [];
            foreach ($menus as $item) {
                $menu[] = $item['function_id'];
            }
            $menu = json_encode($menu);
        }

        $data = [
            'today'     => $today,
            'yesterday' => $yesterday,
            'menu'      => $menu,
        ];

        success($data, '获取成功');
    }

    /**
     * 获取弹出公告
     */
    public function getPopupNotice() {
        $res      = UserService::getShopInfo($this->userid);
        $announce = [];

        if ($res['status']) {
            if ($res['data']['shop_notice_auto_pop'] == 1) {
                //查找最新的公告
                if (sysconf('announce_push') == 1) {
                    $announce = Db::name('article')->where(['status' => 1, 'cate_id' => 1])->order('id DESC')->find();
                    if (!empty($announce)) {
                        $announce_log = Db::name('announce_log')->where(['user_id' => $this->userid, 'article_id' => $announce['id']])->find();
                        if (empty($announce_log)) {
                            Db::name('announce_log')->insert(['user_id' => $this->userid, 'article_id' => $announce['id'], 'create_at' => time()]);
                        }
                        $announce['content'] = htmlspecialchars_decode($announce['content']);
                        $announce['content'] = strip_tags($announce['content']);
                        $announce['content'] = preg_replace('/&nbsp;/is', ' ', $announce['content']);
                        $announce['content'] = preg_replace('/&ldquo;/is', '"', $announce['content']);
                        $announce['content'] = preg_replace('/&rdquo;/is', '"', $announce['content']);
                        $announce['content'] = preg_replace('/&lt;/is', '', $announce['content']);
                        $announce['content'] = preg_replace('/&rt;/is', '', $announce['content']);

                        // app 不能直接出现 id 键
                        $announce['notice_id'] = $announce['id'];
                        unset($announce['id']);
                    }
                }
            }
        }

        success($announce, empty($announce) ? '暂无公告' : '获取成功');
    }

    /**
     * 获取店铺信息
     */
    public function getShopInfo() {
        $this->limitRequestMethod('GET');

        $res = UserService::getShopInfo($this->userid);

        if ($res['status']) {
            success($res['data'], '获取成功');
        } else {
            error(500, '获取失败');
        }
    }

    /**
     * 设置店铺信息
     */
    public function setShopInfo() {
        $this->limitRequestMethod('POST');

        $data = [
            'qq'                   => input('qq/s', ''),
            'website'              => input('website/s', ''),
            'subdomain'            => input('subdomain/s', ''),
            'shop_name'            => input('shop_name/s', ''),
            'pay_theme'            => input('pay_theme/s', 'default'),
            'stock_display'        => input('stock_display/s', ''),
            'shop_notice'          => input('shop_notice/s', ''),
            'shop_notice_auto_pop' => input('shop_notice_auto_pop/d', 1),
        ];

        UserService::setShopInfo($this->userid, $data);
    }

    /**
     * 店铺开关
     *
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function toggleShopStatus() {

        // is_close = 1 时，表示店铺关闭，is_close = 0 时表示店铺开启
        $this->limitRequestMethod('POST');
        $status = input('status/s', '');
        if (!empty($status)) {
            $status = $status ? 0 : 1;
            $model  = new \app\common\model\User();
            $res    = $model->where('id', '=', $this->userid)
                            ->update(['is_close' => $status]);

            //兼容没有变更的情况
            if (!$res) {
                $res = empty($model->getError());
            }
        } else {
            $record = Db::name('user')->field('is_close')->where('id', '=', $this->userid)->find();
            $status = $record['is_close'] ? 0 : 1;
            $res    = Db::name('user')->where('id', '=', $this->userid)->update(['is_close' => $status]);
        }

        if ($res) {
            success(['is_close' => $status], '切换成功');
        } else {
            error(500, '切换失败');
        }
    }

    /**
     * 获取收款信息
     */
    public function getCollect() {
        $this->limitRequestMethod('GET');

        $res = UserService::getCollect($this->userid);

        if ($res['status']) {
            $res['data']['info'] = json_decode($res['data']['info'], 1);
            success($res['data'], '获取成功');
        } else {
            error(500, '暂无收款信息');
        }
    }

    /**
     * 添加收款信息
     */
    public function setCollect() {
        $this->limitRequestMethod('POST');

        $type = input('type/d', 0);
        if (empty($type)) {
            error(414, '请指定账户类型');
        }

        $account = $this->getCollectInfo();

        $data = [
            'type'         => $type,
            'info'         => $account['info'],
            'user_id'      => $this->userid,
            'create_at'    => time(),
            'collect_img'  => $account['img'],
            'allow_update' => 0,
        ];

        if (1 == sysconf('idcard_auth_type')) {
            //二要素校验
            $infoTemp = json_decode($data['info'], true);
            if (!idcardAuth($infoTemp['idcard_number'], $infoTemp['realname'])) {
                error(414, '身份证，姓名校验失败，请重试！');
            }
        }

        UserService::setCollect($this->userid, $data);
    }

    /**
     * 检查账户类型
     *
     * @return array|bool
     */
    protected function getCollectInfo() {
        $type        = input('type/d', 0);
        $collectInfo = ['info' => '', 'img' => ''];

        if (!in_array($type, [1, 2, 3])) {
            error(414, '请选择账户类型');
        }

        $realname = input('realname/s', '');
        if (empty($realname)) {
            error(414, '请填写姓名');
        }

        $idcard = input('idcard_number/s', '');
        if (empty($idcard)) {
            error(414, '请填写身份证信息');
        }

        switch ($type) {
            case 1:
            case 2:
                $account = input('account/s', '');
                if (empty($account)) {
                    error(414, '请填写账号');
                }

                $accountInfo         = [
                    'account'       => $account,
                    'realname'      => $realname,
                    'idcard_number' => $idcard,
                ];
                $collectInfo['info'] = json_encode($accountInfo);
                $img                 = getUploadFile('collect_img', true);
                if ($img['status']) {
                    $collectInfo['img'] = $img['data']['file'];
                }
                return $collectInfo;
                break;
            case 3:
                $bank = input('bank_name/s', '');
                if (empty($bank)) {
                    error(414, '请选择银行');
                }
                $bankBranch = input('bank_branch/s', '');
                if (empty($bankBranch)) {
                    error(414, '请选择支行');
                }
                $bankCard = input('bank_card/s', '');
                if (empty($bankCard)) {
                    error(414, '请填写银行卡号');
                }
                $accountInfo         = [
                    'bank_name'     => $bank,
                    'bank_branch'   => $bankBranch,
                    'bank_card'     => $bankCard,
                    'realname'      => $realname,
                    'idcard_number' => $idcard,
                ];
                $collectInfo['info'] = json_encode($accountInfo);
                return $collectInfo;
                break;
            default:
                error(414, '请选择账户类型');
                return false;
        }
    }

    /**
     * 设置菜单信息
     */
    public function setMenu() {
        $this->limitRequestMethod('POST');

        $menu = input('menu/s', '');
        if (empty($menu)) {
            error(414, '菜单信息不能为空');
        }
        try {
            Db::name('user')->where('id', $this->userid)->update(['app_menu' => $menu]);
            success();
        } catch (DbException $e) {
            error(500, $e->getMessage());
        }
    }

    /**
     * 获取菜单信息
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCustomMenu() {
        $this->limitRequestMethod('GET');

        $menu = Db::name('user')->where('id', $this->userid)->value('app_menu');
        if (empty($menu)) {
            //如果为空，获取默认的菜单
            $menus = Db::name('app_menu')->limit(7)->select();
            $menu  = [];
            foreach ($menus as $item) {
                $menu[] = $item['function_id'];
            }
            $menu = json_encode($menu);
        }

        success($menu);
    }

    /**
     * 获取菜单信息
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMenu() {
        $this->limitRequestMethod('GET');

        $menus  = Db::name('app_menu')->select();
        $result = [];
        foreach ($menus as $item) {
            $result[] = json_decode($item['menu'], 1);
        }

        success($result);
    }

    /**
     * 店铺重置短链接
     */
    public function refreshLink() {
        $this->limitRequestMethod('POST');

        $res = LinkService::refresh($this->userid, 'user', $this->userid);
        if ($res['status']) {
            success($res['data'], '重置成功');
        } else {
            error(500, $res['msg']);
        }

    }
}
