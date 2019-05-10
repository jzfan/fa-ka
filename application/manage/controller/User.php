<?php
/**
 * 商户管理
 */

namespace app\manage\controller;

use app\common\model\RateGroup;
use controller\BasicAdmin;
use think\Db;
use think\Exception;
use think\Request;
use app\common\model\User as UserModel;
use app\common\model\UserCollect as UserCollectModel;
use app\common\model\Channel as ChannelModel;
use app\common\model\UserLoginLog;
use service\LogService;

class User extends BasicAdmin {
    public function index() {
        $this->assign('title', '商户管理');
        ////////////////// 查询条件 //////////////////
        $query   = [
            'status'     => input('status/s', ''),
            'is_freeze'  => input('is_freeze/s', ''),
            'date_range' => input('date_range/s', ''),
        ];
        $field   = input('field/s', '');
        $keyword = input('keyword/s', '');
        if ($field && $field) {
            switch ($field) {
                case '1':
                    $query['user_id'] = $keyword;
                    break;
                case '2':
                    $query['username'] = $keyword;
                    break;
                case '3':
                    $query['shop_name'] = $keyword;
                    break;
                case '4':
                    $query['mobile'] = $keyword;
                    break;
                case '5':
                    $query['qq'] = $keyword;
                    break;
            }
        }
        $where = $this->genereate_where($query);

        $users = UserModel::where($where)->order('id desc')->paginate(30, false, [
            'query' => $query
        ]);
        foreach ($users as $k => $v) {
            $users[$k]['idcard_number'] = '';
            $info                       = Db::name('user_collect')->where(['user_id' => $v['id']])->value('info');
            if ($info) {
                $info                       = json_decode($info, true);
                $users[$k]['idcard_number'] = $info['idcard_number'];
            }
        }
        $this->assign('users', $users);
        // 分页
        $page = str_replace('href="', 'href="#', $users->render());
        $this->assign('page', $page);

        $user_count = UserModel::where($where)->count();
        $this->assign('user_count', $user_count);

        return $this->fetch();
    }

    /**
     * 生成查询条件
     */
    protected function genereate_where($params) {
        $where  = [];
        $action = $this->request->action();
        switch ($action) {
            case 'index':
                if (isset($params['user_id']) && $params['user_id'] !== '') {
                    $where['id'] = $params['user_id'];
                }
                if (isset($params['username']) && $params['username'] !== '') {
                    $where['username'] = ['LIKE', '%' . $params['username'] . '%'];
                }
                if (isset($params['shop_name']) && $params['shop_name'] !== '') {
                    $where['shop_name'] = ['LIKE', '%' . $params['shop_name'] . '%'];
                }
                if (isset($params['mobile']) && $params['mobile'] !== '') {
                    $where['mobile'] = $params['mobile'];
                }
                if (isset($params['qq']) && $params['qq'] !== '') {
                    $where['qq'] = $params['qq'];
                }
                if ($params['status'] !== '') {
                    $where['status'] = $params['status'];
                }
                if ($params['is_freeze'] !== '') {
                    $where['is_freeze'] = $params['is_freeze'];
                }
                if ($params['date_range'] && strpos($params['date_range'], ' - ') !== false) {
                    list($startDate, $endTime) = explode(' - ', $params['date_range']);
                    $where['create_at'] = ['between', [strtotime($startDate . ' 00:00:00'), strtotime($endTime . ' 23:59:59')]];
                }
                break;
            case 'loginlog':
                if ($params['user_id'] !== '') {
                    $where['user_id'] = $params['user_id'];
                }
                if ($params['username'] !== '') {
                    $where['user_id'] = Db::name('User')->where(['username' => $params['username']])->value('id');
                }
                if ($params['ip'] !== '') {
                    $where['ip'] = $params['ip'];
                }
                if ($params['date_range'] && strpos($params['date_range'], ' - ') !== false) {
                    list($startDate, $endTime) = explode(' - ', $params['date_range']);
                    $where['create_at'] = ['between', [strtotime($startDate . ' 00:00:00'), strtotime($endTime . ' 23:59:59')]];
                }
                break;
        }
        return $where;
    }

    /**
     * 改变状态
     */
    public function change_status() {
        if (!$this->request->isAjax()) {
            $this->error('错误的提交方式！');
        }
        $id     = input('id/d', 0);
        $status = input('value/d', 1);
        $res    = Db::name('User')->where([
            'id' => $id,
        ])->update([
            'status' => $status
        ]);
        $remark = $status == 1 ? '审核通过' : '审核驳回';
        if ($res !== false) {
            LogService::write('用户管理', $remark . '成功，商户ID:' . $id);
            $this->success('更新成功！', '');
        } else {
            $this->error('更新失败，请重试！');
        }
    }

    /**
     * 改变冻结状态
     */
    public function change_freeze_status() {
        if (!$this->request->isAjax()) {
            $this->error('错误的提交方式！');
        }
        $id     = input('id/d', 0);
        $status = input('value/d', 1);
        $res    = Db::name('User')->where([
            'id' => $id,
        ])->update([
            'is_freeze' => $status
        ]);
        $remark = $status == 1 ? '冻结' : '解冻';
        if ($res !== false) {
            LogService::write('用户管理', $remark . '商户成功，商户ID:' . $id);
            $this->success('更新成功！', '');
        } else {
            $this->error('更新失败，请重试！');
        }
    }

    /**
     * 详情
     */
    public function detail() {
        $id   = input('user_id/d');
        $user = UserModel::get($id);
        $this->assign('user', $user);
        return $this->fetch();
    }

    /**
     * 添加账号
     */
    public function add() {
        if (!$this->request->isPost()) {
            $users = UserModel::all(['status' => 1]);
            $this->assign('users', $users);
            return $this->fetch('edit');
        }

        $data = [
            'parent_id'   => input('parent_id/d', 0),
            'username'    => input('username/s', ''),
            'email'       => input('email/s', ''),
            'mobile'      => input('mobile/s', ''),
            'qq'          => input('qq/s', ''),
            'subdomain'   => strtolower(input('subdomain/s', '')),
            'shop_name'   => input('shop_name/s', ''),
            'statis_code' => input('statis_code/s', ''),
            'pay_theme'   => input('pay_theme/s', 'default'),
            'password'    => input('password/s', ''),
            'settlement_type'    => input('settlement_type/d', ''),
        ];
        $res  = $this->validate($data, 'User.register');
        if ($res !== true) {
            $this->error($res);
        }
        if (!is_mobile_number($data['mobile'])) {
            $this->error('手机号不合法');
        }
        // 检查子域名
        if ($data['subdomain'] !== '') {
            // 域名过滤
            $domains = explode('|', sysconf('disabled_domains'));
            if (in_array($data['subdomain'], $domains)) {
                $this->error('该子域名禁止使用！');
            }
            $res = UserModel::where(['subdomain' => $data['subdomain']])->count();
            if ($res) {
                $this->error('该子域名已被使用！');
            }
        }
        $data['password']  = md5($data['password']);
        $data['create_at'] = $_SERVER['REQUEST_TIME'];

        $user = new UserModel($data);
        if ($user->allowField(true)->save()) {
            LogService::write('用户管理', '添加商户成功，商户ID:' . $user->id);
            $this->success('添加成功！', '');
        } else {
            $this->error('添加失败！');
        }
    }

    /**
     * 编辑账号
     */
    public function edit() {
        $user_id = input('user_id/d', 0);
        $user    = UserModel::get($user_id);
        if (!$user) {
            $this->error('不存在该用户！');
        }
        if (!$this->request->isPost()) {
            $this->assign('user', $user);
            $users = UserModel::all(['id' => ['<>', $user_id], 'parent_id' => ['<>', $user_id], 'status' => 1]);
            $this->assign('users', $users);
            return $this->fetch('edit');
        }
        $data = [
            'parent_id'   => input('parent_id/d', 0),
            'username'    => input('username/s', ''),
            'email'       => input('email/s', ''),
            'mobile'      => input('mobile/s', ''),
            'qq'          => input('qq/s', ''),
            'subdomain'   => strtolower(input('subdomain/s', '')),
            'shop_name'   => input('shop_name/s', ''),
            'statis_code' => input('statis_code/s', ''),
            'pay_theme'   => input('pay_theme/s', 'default'),
            'password'    => input('password/s', ''),
            'settlement_type'    => input('settlement_type/d', ''),
        ];
        if ($data['username'] == $user->username) {
            unset($data['username']);
        }
        if ($data['password'] === '') {
            unset($data['password']);
        }
        // 检查子域名
        if ($data['subdomain'] !== '') {
            // 域名过滤
            $domains = explode('|', sysconf('disabled_domains'));
            if (in_array($data['subdomain'], $domains)) {
                $this->error('该子域名禁止使用！');
            }
            $res = UserModel::where(['id' => ['<>', $user_id], 'subdomain' => $data['subdomain']])->count();
            if ($res) {
                $this->error('该子域名已被使用！');
            }
        }
        $res = $this->validate($data, 'User.edit');
        if ($res !== true) {
            $this->error($res);
        }
        if (isset($data['password'])) {
            $data['password'] = md5($data['password']);
            $data['login_key'] = rand(1000000, 9999999);
        }
        if (!is_mobile_number($data['mobile'])) {
            $this->error('手机号不合法');
        }
        $res = $user->update($data, ['id' => $user_id]);
        if ($res !== false) {
            LogService::write('用户管理', '编辑商户成功，商户ID:' . $user_id);
            $this->success('保存成功！', '');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除用户
     */
    public function del() {
        if ($this->request->isPost()) {
            $id  = input('id/d', 0);
            $res = Db::name('user')->where('id', $id)->delete();
            if (false !== $res) {
                LogService::write('用户管理', '删除商户成功，商户ID:' . $id);
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    /*
     * 允许用户修改收款信息
     */
    public function allow_update() {
        if ($this->request->isPost()) {
            $id   = input('id/d', 0);
            $info = Db::name('user_collect')->where(['user_id' => $id])->find();

            if (empty($info)) {
                $this->error('未设置收款信息');
            }

            if ($info['allow_update'] == 1) {
                $this->success('设置成功');
            }
            $res = UserCollectModel::where(['user_id' => $id])->update(['allow_update' => 1]);
            if (false !== $res) {
                LogService::write('用户管理', '允许修改收款信息，商户ID:' . $id);
                $this->success('设置成功');
            } else {
                $this->error('设置失败');
            }
        }
    }

    /**
     * 资金管理
     */
    public function manage_money() {
        $user_id = input('user_id/d', 0);
        $user    = UserModel::get($user_id);
        if (!$user) {
            $this->error('不存在该用户！');
        }
        if (!$this->request->isPost()) {
            $this->assign('user', $user);
            return $this->fetch();
        }
        $action = input('action/s', '');
        $money  = input('money/f', 0);
        $mark   = input('mark/s', '');
        if ($money <= 0) {
            $this->error('操作金额不能小于等于零！');
        }
        switch ($action) {
            case 'inc':
                $user->money   += $money;
                $reason        = "增加金额{$money}元，备注：{$mark}";
                $business_type = 'admin_inc';
                $type          = 1;
                break;
            case 'dec':
                if ($user->money < $money) {
                    $this->error('可用余额不足！');
                }
                $user->money   -= $money;
                $reason        = "扣除金额{$money}元，备注：{$mark}";
                $business_type = 'admin_dec';
                $type          = -1;
                break;
            case 'unfreeze':
                if ($user->freeze_money < $money) {
                    $this->error('可用冻结余额不足！');
                }
                $user->money        += $money;
                $user->freeze_money -= $money;
                $reason             = "解冻金额{$money}元，备注：{$mark}";
                $business_type      = 'unfreeze';
                $type               = 1;
                break;
            case 'freeze':
                if ($user->money < $money) {
                    $this->error('可用余额不足！');
                }
                $user->money        -= $money;
                $user->freeze_money += $money;
                $reason             = "冻结金额{$money}元，备注：{$mark}";
                $business_type      = 'freeze';
                $type               = -1;
                break;
            default:
                $this->error('未知操作！');
                break;
        }

        Db::startTrans();
        try {
            // 变动金额
            $user->save();
            // 记录用户金额变动日志
            record_user_money_log($business_type, $user->id, $type * $money, $user->money, $reason);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return J(-1, '操作失败，原因：' . $e->getMessage());
        };
        LogService::write('用户管理', $reason . '，商户ID:' . $user_id);
        $this->success('操作成功！', '');
    }

    /**
     * 设置费率
     */
    public function rate() {
        $user_id = input('user_id/d', 0);
        $this->assign('user_id', $user_id);
        //渠道
        $channels = ChannelModel::all(['is_install' => 1]);
        $this->assign('channels', $channels);
        //费率分组
        $rateGroup = RateGroup::all();
        $this->assign('rate_group', $rateGroup);

        if (!Request::instance()->isPost()) {
            $user = UserModel::get($user_id);
            if (!$user) {
                return $this->error('不存在该商户！');
            }

            $userGroup = Db::name('rate_group_user')->where('user_id', $user_id)->value('group_id');
            $this->assign('userGroup', $userGroup ? $userGroup : '');

            $userRate = [];

            //获取所有的渠道
            foreach ($channels as $channel) {
                $userRate[$channel['id']] = $channel['lowrate'] * 1000;
            }

            foreach ($user->rate as $v) {
                $userRate[$v['channel_id']] = $v['rate'] * 1000;
            }
            foreach ($channels as $k => $v) {
                if (!isset($userRate[$v['id']])) {
                    $userRate[$v['id']] = $v['lowrate'] * 1000;
                }
            }
            $this->assign('user', $user);
            $this->assign('userRate', $userRate);
            return $this->fetch();
        }
        $channel_ids = input('channel_ids/a');
        $data        = [];
        foreach ($channels as $channel) {
            if (isset($channel_ids[$channel['id']])) {
                $channel_id = $channel['id'];
                $rate       = $channel_ids[$channel['id']];
                if (!empty($rate)) {
                    $lowrate  = $channel['lowrate'] * 1000;
                    $highrate = $channel['highrate'] * 1000;
                    if ($rate == 0 || $rate == '') {
                        continue;
                    }
                    // 判断费率是否超出界定范围
                    if ($rate < $lowrate) {
                        // $this->error($channel['title'].'费率不能低于'.$lowrate.'‰');
                    }
                    if ($rate > $highrate) {
                        // $this->error($channel['title'].'费率不能超过'.$highrate.'‰');
                    }
                    $data[] = [
                        'user_id'    => $user_id,
                        'channel_id' => $channel_id,
                        'rate'       => $rate / 1000,
                    ];
                } else {
                    Db::name('userRate')->where([
                        'user_id'    => $user_id,
                        'channel_id' => $channel_id,
                    ])->delete();
                }
            }
        }

        Db::startTrans();
        try {
            foreach ($data as $v) {
                $where    = ['user_id' => $v['user_id'], 'channel_id' => $v['channel_id']];
                $isExists = Db::name('userRate')->where($where)->count();
                if ($isExists) {
                    Db::name('userRate')->where($where)->update($v);
                } else {
                    Db::name('userRate')->insert($v);
                }
            }

            //保存用户分组
            $group = input('group/d', '');
            if ($group) {
                $isExists = Db::name('rate_group_user')->where('user_id', $user_id)->count();
                if ($isExists) {
                    Db::name('rate_group_user')->where('user_id', $user_id)->update(['group_id' => $group]);
                } else {
                    Db::name('rate_group_user')->insert(['user_id' => $user_id, 'group_id' => $group]);
                }
            } else {
                //删除分组
                Db::name('rate_group_user')->where('user_id', $user_id)->delete();
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error('费率更新失败');
        }

        LogService::write('用户管理', '设置费率成功，商户ID:' . $user_id);
        $this->success('保存成功！', '');
    }

    /**
     * 获取指定的费率分组信息
     *
     * @return string
     * @throws \think\exception\DbException
     */
    public function getRateRouteInfo() {
        $id = input('id/d', '');
        if ($id) {

            $group        = RateGroup::get($id);
            $group->rules = $group->rateRules()->select();

            if ($group) {
                return J(200, '获取成功', $group);
            }
        } else {
            //没有传递费率分组，就是不设定分组，传回默认分组费率
            $rules    = [];
            $channels = ChannelModel::all(['is_install' => 1]);
            foreach ($channels as $channel) {
                $rules[$channel['id']] = [
                    'rate'       => $channel['lowrate'],
                    'channel_id' => $channel['id'],
                ];
            }

            return J(200, '获取成功', ['rules' => $rules]);
        }
    }

    /**
     * 登录
     */
    public function login() {
        $user_id = input('user_id/d', 0);
        $user    = UserModel::get([
            'id' => $user_id,
        ]);
        if (!$user) {
            $this->error('不存在该用户！');
        }
        //后台管理员进行登录不需要二次验证
        session('merchant.login_auth', null);
        session('merchant.login_key', $user->login_key);
        session('merchant.user', $user->id);
        session('merchant.username', $user->username);
        //记住7天
        session('merchant.login_expire', time() + 86400 * 7);
        LogService::write('用户管理', '登录商户平台成功，商户ID:' . $user_id);
        $this->redirect('@merchant');
    }

    /**
     * 发送站内信
     */
    public function message() {
        if (!$this->request->isPost()) {
            return $this->fetch();
        }
        $user_id = input('user_id/d', 0);
        $title   = input('title/s', '');
        $content = input('content/s', '');
        $user    = UserModel::get([
            'id' => $user_id,
        ]);
        if (!$user) {
            $this->error('不存在该用户！');
        }
        if (!$title) {
            $this->error('请输入标题！');
        }
        if (!$content) {
            $this->error('请输入内容！');
        }
        $res = sendMessage(0, $user_id, $title, $content);
        if ($res !== false) {
            LogService::write('用户管理', '发送站内信成功，商户ID:' . $user_id);
            $this->success('发送成功！', '');
        } else {
            $this->error('发送失败，请重试！');
        }
    }

    /**
     * 登录日志
     */
    public function loginlog() {
        $this->assign('title', '登录日志');
        ////////////////// 查询条件 //////////////////
        $query = [
            'user_id'    => input('user_id/s', ''),
            'username'   => input('username/s', ''),
            'ip'         => input('ip/s', ''),
            'date_range' => input('date_range/s', ''),
        ];
        $where = $this->genereate_where($query);
        $logs  = UserLoginLog::where($where)->order('id desc')->paginate(30);
        $this->assign('logs', $logs);
        $this->assign('page', str_replace('href="', 'href="#', $logs->render()));

        $log_count = UserLoginLog::where($where)->count();
        $this->assign('log_count', $log_count);

        return $this->fetch();
    }

    //解锁登录限制
    public function unlock() {
        if (Request::instance()->isPost()) {
            $user_type  = input('usertype/d', 0);
            $login_name = input('login_name/s', '');
            if ($login_name == '') {
                $this->error('用户名不能为空');
            }
            if ($user_type == 0) {//普通用户
                $user = Db::name('user')->where('username', $login_name)->find();
            } elseif ($user_type == 1) {//后台管理员
                $user = Db::name('system_user')->where('username', $login_name)->find();
            } else {
                $this->error('参数错误');
            }
            if (empty($user)) {
                $this->error('用户不存在');
            }
            $count = Db::name('user_login_error_log')->where(['user_type' => $user_type, 'login_name' => $login_name])->count();
            if ($count < 3) {
                $this->error('该用户未被锁定');
            } else {
                $re = Db::name('user_login_error_log')->where(['user_type' => $user_type, 'login_name' => $login_name])->delete();
                if (FALSE !== $re) {
                    LogService::write('用户管理', '解锁登录限制成功，商户ID:' . $user['id']);
                    $this->success('解锁成功', '');
                } else {
                    $this->error('解锁失败');
                }
            }
        } else {
            return view();
        }
    }
}
