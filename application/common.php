<?php

use service\DataService;
use service\FileService;
use service\NodeService;
use think\Db;
use Wechat\Loader;

/**
 * 打印输出数据到文件
 *
 * @param mixed       $data
 * @param bool        $replace
 * @param string|null $pathname
 */
function p($data, $replace = false, $pathname = null) {
    is_null($pathname) && $pathname = RUNTIME_PATH . date('Ymd') . '.txt';
    $str = (is_string($data) ? $data : (is_array($data) || is_object($data)) ? print_r($data, true) : var_export($data, true)) . "\n";
    $replace ? file_put_contents($pathname, $str) : file_put_contents($pathname, $str, FILE_APPEND);
}

/**
 * 获取mongoDB连接
 *
 * @param string $col   数据库集合
 * @param bool   $force 是否强制连接
 *
 * @return \think\db\Query|\think\mongo\Query
 */
function mongo($col, $force = false) {
    return Db::connect(config('mongo'), $force)->name($col);
}

/**
 * 获取微信操作对象
 *
 * @param string $type
 *
 * @return \Wechat\WechatMedia|\Wechat\WechatMenu|\Wechat\WechatOauth|\Wechat\WechatPay|\Wechat\WechatReceive|\Wechat\WechatScript|\Wechat\WechatUser|\Wechat\WechatExtends|\Wechat\WechatMessage
 * @throws Exception
 */
function &load_wechat($type = '') {
    static $wechat = [];
    $index = md5(strtolower($type));
    if (!isset($wechat[$index])) {
        $config         = [
            'token'          => sysconf('wechat_token'),
            'appid'          => sysconf('wechat_appid'),
            'appsecret'      => sysconf('wechat_appsecret'),
            'encodingaeskey' => sysconf('wechat_encodingaeskey'),
            'mch_id'         => sysconf('wechat_mch_id'),
            'partnerkey'     => sysconf('wechat_partnerkey'),
            'ssl_cer'        => sysconf('wechat_cert_cert'),
            'ssl_key'        => sysconf('wechat_cert_key'),
            'cachepath'      => CACHE_PATH . 'wxpay' . DS,
        ];
        $wechat[$index] = Loader::get($type, $config);
    }
    return $wechat[$index];
}

/**
 * UTF8字符串加密
 *
 * @param string $string
 *
 * @return string
 */
function encode($string) {
    list($chars, $length) = ['', strlen($string = iconv('utf-8', 'gbk', $string))];
    for ($i = 0; $i < $length; $i++) {
        $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
    }
    return $chars;
}

/**
 * UTF8字符串解密
 *
 * @param string $string
 *
 * @return string
 */
function decode($string) {
    $chars = '';
    foreach (str_split($string, 2) as $char) {
        $chars .= chr(intval(base_convert($char, 36, 10)));
    }
    return iconv('gbk', 'utf-8', $chars);
}

/**
 * 网络图片本地化
 *
 * @param string $url
 *
 * @return string
 */
function local_image($url) {
    if (is_array(($result = FileService::download($url)))) {
        return $result['url'];
    }
    return $url;
}

/**
 * 日期格式化
 *
 * @param string $date   标准日期格式
 * @param string $format 输出格式化date
 *
 * @return false|string
 */
function format_datetime($date, $format = 'Y年m月d日 H:i:s') {
    return empty($date) ? '' : date($format, strtotime($date));
}

/**
 * 设备或配置系统参数
 *
 * @param string $name  参数名称
 * @param bool   $value 默认是null为获取值，否则为更新
 *
 * @return string|bool
 */
function sysconf($name, $value = null) {
    static $config = [];
    if ($value !== null) {
        list($config, $data) = [[], ['name' => $name, 'value' => $value]];
        return DataService::save('SystemConfig', $data, 'name');
    }
    if (empty($config)) {
        $config = Db::name('SystemConfig')->column('name,value');
    }
    return isset($config[$name]) ? $config[$name] : '';
}

/**
 * RBAC节点权限验证
 *
 * @param string $node
 *
 * @return bool
 */
function auth($node) {
    return NodeService::checkAuthNode($node);
}

/**
 * array_column 函数兼容
 */
if (!function_exists("array_column")) {
    function array_column(array &$rows, $column_key, $index_key = null) {
        $data = [];
        foreach ($rows as $row) {
            if (empty($index_key)) {
                $data[] = $row[$column_key];
            } else {
                $data[$row[$index_key]] = $row[$column_key];
            }
        }
        return $data;
    }
}

/**
 * 返回接口数据
 *
 * @param int    $code 状态码
 * @param string $msg  信息
 * @param array  $data 数据
 *
 * @return string json数据
 */
function J($code, $msg = '', $data = [], $url = null) {
    $return = [
        'code'      => $code,
        'msg'       => $msg,
        'data'      => $data,
        'url'       => $url,
        'timestamp' => time(),
    ];
    // 兼容default_ajax_return=json，防止被双重json_encode，添加appliaction/json头
    return json($return);
}

/**
 * 判断是否为合法的身份证号码
 *
 * @param $vStr
 *
 * @return int
 */
function is_idcard_number($vStr) {
    $vCity = array(
        '11', '12', '13', '14', '15', '21', '22',
        '23', '31', '32', '33', '34', '35', '36',
        '37', '41', '42', '43', '44', '45', '46',
        '50', '51', '52', '53', '54', '61', '62',
        '63', '64', '65', '71', '81', '82', '91',
    );
    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) {
        return false;
    }

    if (!in_array(substr($vStr, 0, 2), $vCity)) {
        return false;
    }

    $vStr    = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);
    if ($vLength == 18) {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }
    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) {
        return false;
    }

    if ($vLength == 18) {
        $vSum = 0;
        for ($i = 17; $i >= 0; $i--) {
            $vSubStr = substr($vStr, 17 - $i, 1);
            $vSum    += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
        }
        if ($vSum % 11 != 1) {
            return false;
        }
    }
    return true;
}

/**
 * 判断是否为正常的手机号
 *
 * @param  string $mobile 手机号
 *
 * @return boolean
 */
function is_mobile_number($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }

    //return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,1,3,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;

    return preg_match('#^1[3-9]\d{9}$#', $mobile) ? true : false;
}

/**
 * 生成订单号
 *
 * @return string 订单号
 */
function generate_trade_no($flag = 'A', $userid = 0) {
    //订单自定义
    if (sysconf('order_type') == 1) {
        $trade_no = sysconf('user_order_profix') . date('ymdHis') . str_pad(abs($userid - 10000), 4, 0) . str_pad(mt_rand(0, 99), 2, '0');
    } else {
        $trade_no = $flag . date('ymdHis') . str_pad(abs($userid - 10000), 4, 0) . str_pad(mt_rand(0, 99), 2, '0');
    }

    //校验是否有重复订单号
    $res = Db::name('order')->where(['trade_no' => $trade_no])->find();
    if ($res['status']) {
        //需要重新生成订单号
        $trade_no = generate_trade_no($flag, $userid);
    }

    //检查订单号是否唯一
    $res = Db::name('unique_orderno')->insert(['trade_no' => $trade_no]);
    if ($res != 1) {
        //需要重新生成订单号
        $trade_no = generate_trade_no($flag, $userid);
    }


    return $trade_no;
}

/**
 * 友盟推送
 */
function push_umeng($device_tokens, $ticker = '', $title = '', $text = '', $extra = []) {
    $UPush = new \umeng\UPush(sysconf('upush_appkey'), sysconf('upush_appMasterSecret'));
    $res   = $UPush->sendAndroidUnicast($device_tokens, $ticker, $title, $text, $extra);
    return $res;
}

/**
 * 生成随机字符串
 */
function random_str($length = 32) {
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str   = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * 生成excel
 *
 * @param  array  $title    标题字段
 * @param  array  $data     数据
 * @param  string $filename 文件名
 * @param  string $type     生成类型
 */
function generate_excel($title, $data, $filename, $type = 'file') {
    $file_name = $filename . ".csv";
    if ($type == 'file') {
        $file = fopen(TEMP_PATH . $file_name, "a");
    } else {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        $file = fopen('php://output', "a");
    }
    $limit = 10000;
    $calc  = 0;
    //列名
    foreach ($title as $v) {
        $tit[] = iconv('UTF-8', 'GB2312//IGNORE', $v);
    }
    //将数据通过fputcsv写到文件句柄
    fputcsv($file, $tit);

    foreach ($data as $v) {
        $calc++;
        if ($limit == $calc) {
            ob_flush();
            flush();
            $calc = 0;
        }
        foreach ($v as $t) {
            $tarr[] = "\t" . iconv('UTF-8', 'GB2312//IGNORE', $t) . '';
        }
        fputcsv($file, $tarr);
        unset($tarr);
    }
    unset($data);
    ob_flush();
    flush();
    fclose($file);
    // return TEMP_PATH . $file_name;
    exit();
}

/**
 * 邮件发送
 *
 * @param  string $to              收件方
 * @param  string $title           标题
 * @param  string $content         内容
 * @param  string $filePath        附件
 * @param  bool   $throwExceptions 是否抛出异常
 *
 * @return bool          发送状态
 */
function sendMail($to, $title, $content, $filePath = '', $throwExceptions = false) {
    //步骤
    //1复制文件到当前项目下的Thinkphp/libary/Org/Util (class.pop3.php class.smtp.php class.phpmailer.php)
    //2.修改类文件的名称
    //3.修改命名空间
    //4.注意在PHPMailer中最后一个继承
    $mail = new Util\Mailer\PHPMailer($throwExceptions);
    // $mail->SMTPDebug  = 1;
    $mail->CharSet = "utf-8"; //设置采用utf8中文编码
    $mail->IsSMTP(); //设置采用SMTP方式发送邮件

    $max   = 2;
    $index = rand(0, 2);

    $tried = explode(',', trim(',', session('last_try_email')));
    if(empty($tried)) $tried = [];

    while (in_array($index, $tried)) {
        $index = rand(0, 2);
    }
    if (empty($index)) {
        $configIndex = '';
    } else {
        $configIndex = $index;
    }

    $host     = sysconf('email_smtp' . $configIndex);
    $port     = sysconf('email_port' . $configIndex);
    $from     = sysconf('email_user' . $configIndex);
    $password = sysconf('email_pass' . $configIndex);

    $mail->Host       = $host; //设置邮件服务器的地址
    $mail->Port       = $port; //设置邮件服务器的端口，默认为25
    $mail->From       = $from; //设置发件人的邮箱地址
    $mail->FromName   = sysconf('email_name'); //设置发件人的姓名
    $mail->SMTPAuth   = true; //设置SMTP是否需要密码验证，true表示需要
    $mail->SMTPSecure = "ssl";
    $mail->Username   = $from;
    $mail->Password   = $password;
    $mail->Subject    = $title; //设置邮件的标题
    $mail->AltBody    = "text/html"; // optional, comment out and test
    $mail->Body       = $content;
    $mail->IsHTML(true); //设置内容是否为html类型
    $mail->AddAddress(trim($to), ''); //设置收件的地址
    if (!empty($filePath)) {
        $mail->AddAttachment($filePath);
    }
    $try = session('email_try');
    if (empty($try)) {
        session('email_try', 0);
    }
    if (!$mail->Send()) {
        //发送邮件
        //echo '发送失败:' . $mail->ErrorInfo;
        record_file_log('email_error', 'index: ' . $index . "\r\n" . $mail->ErrorInfo);
        //更新可用的邮箱，保证下一封邮件可以发出
        if ($try >= $max) {
            session('email_try', 0);
            session('last_try_email', '');
            return false;
        } else {
            session('email_try', $try + 1);
            session('last_try_email', session('last_try_email') . ',' . $index);
            return sendMail($to, $title, $content, $filePath, $throwExceptions);
        }
    } else {
        // echo "发送成功";
        session('email_try', 0);
        session('last_try_email', '');
        return true;
    }
}

/**
 * 获取支付类型列表
 *
 * @return array 支付类型列表
 */
function get_paytype_list() {
    $pay_type = null;
    try {
        $pay_type = Db::name('pay_type')->select();

        foreach ($pay_type as $item) {
            if ($item['sub_lists']) {
                $item['sub_lists'] = json_decode($item['sub_lists']);
            }
        }
    } catch (\think\Exception $e) {
        $pay_type = null;
    }
    if (!$pay_type) {
        return config('paytype_config');
    } else {
        return $pay_type;
    }
}

/**
 * 获取支付类型信息
 *
 * @param int $paytype 支付类型
 *
 * @return array 支付类型信息
 */
function get_paytype_info($paytype) {
    $pay_type = null;
    try {
        $pay_type = Db::name('pay_type')->find(['id' => $paytype]);

        if ($pay_type['sub_lists']) {
            $pay_type['sub_lists'] = json_decode($pay_type['sub_lists']);
        }
    } catch (\think\Exception $e) {
        $pay_type = null;
    }
    if (!$pay_type) {
        return isset(config('paytype_config')[$paytype]) ? config('paytype_config')[$paytype] : [];
    } else {
        return $pay_type;
    }
}

/**
 * 获取支付类型名称
 *
 * @param int $paytype 支付类型
 * @param int $type    返回值类型 0：支付产品名 1：渠道名
 *
 * @return string 支付类型名称
 */
function get_paytype_name($paytype, $type = 0) {
    $pay_type = null;

    try {
        $pay_type = Db::name('pay_type')->find(['id' => $paytype]);

        if ($pay_type['sub_lists']) {
            $pay_type['sub_lists'] = json_decode($pay_type['sub_lists']);
        }
    } catch (\think\Exception $e) {
        $pay_type = null;
    }

    if (!$pay_type) {
        $pay_type = isset(config('paytype_config')[$paytype]) ? config('paytype_config')[$paytype] : null;
    }

    if (!$pay_type) {
        return '';
    }

    if ($type == 0) {
        $product_id = $pay_type['product_id'];
        if ($product_id) {
            return isset(config('pay_product')[$product_id]) ? config('pay_product')[$product_id] : '';
        } else {
            return '';
        }
    } else {
        return $pay_type['name'];
    }
}

/**
 * 生成二维码链接
 */
function generate_qrcode_link($url) {
    return 'http://qr.liantu.com/api.php?&w=180&text=' . $url;
}

/**
 * 记录用户金额变动日志
 */
function record_user_money_log($business_type, $user_id, $money, $balance, $reason) {
    $businessTypes = [
        'unfreeze'        => '解冻金额',
        'freeze'          => '冻结金额',
        'sub_sold_rebate' => '下级卖出商品返佣',
        'sub_fee_rebate'  => '下级手续费返佣',
        'cash_notpass'    => '提现未通过',
        'cash_success'    => '提现成功',
        'apply_cash'      => '申请提现',
        'admin_inc'       => '后台操作加钱',
        'admin_dec'       => '后台操作扣钱',
        'fee'             => '手续费',
        'goods_sold'      => '卖出商品',
        'sub_register'    => '推广注册奖励',
    ];
    $tag           = isset($businessTypes[$business_type]) ? "【{$businessTypes[$business_type]}】" : '';
    $res           = Db::name('UserMoneyLog')->insert([
        'business_type' => $business_type,
        'user_id'       => $user_id,
        'money'         => round($money, 3),
        'balance'       => round($balance, 3),
        'reason'        => $tag . $reason,
        'create_at'     => time(),
    ]);
    return $res;
}

/**
 * 获取用户费率
 *
 * @param  int $user_id    用户ID
 * @param  int $channel_id 渠道ID
 *
 * @return double          费率
 */
function get_user_rate($user_id, $channel_id) {
    $rate     = 0;
    $userRate = Db::name('userRate')->where(['user_id' => $user_id, 'channel_id' => $channel_id])->find();
    // 如果用户没有设置费率则走默认充值费率
    if (!$userRate || $userRate['rate'] == 0) {
        $rate = Db::name('channel')->where(['id' => $channel_id])->value('lowrate');
    } else {
        $rate = $userRate['rate'];
    }
    return round($rate, 4);
}

/**
 * 获取用户对应渠道状态
 *
 * @param  int $user_id    用户ID
 * @param  int $channel_id 渠道ID
 *
 * @return double          费率
 */
function get_user_channel_status($user_id, $channel_id) {
    $status      = 1;
    $userChannel = Db::name('userChannel')->where(['user_id' => $user_id, 'channel_id' => $channel_id])->find();
    // 如果用户没有设置则走全局状态
    if (!$userChannel) {
        $rate = Db::name('channel')->where(['id' => $channel_id])->value('status');
    } else {
        $status = $userChannel['status'];
    }
    return $status;
}

/**
 * 获取支付产品的名称
 *
 * @param int $type 产品类型
 *
 * @return string 产品的名称
 */
function get_product_name($type) {
    $product_name = \app\common\model\Product::field('title')->where(['paytype' => $type])->find();
    return isset($product_name['title']) ? $product_name->title : get_paytype_name($type);
}

/**
 * 获取用户渠道信息
 *
 * @param  [type] $user_id [description]
 *
 * @return [type]          [description]
 */
function get_user_channels($user_id) {
    $channels     = \app\common\model\Channel::order('sort desc')->where(['status' => 1])->select();
    //检查是否设置了分组费率
    $rate_group_user = Db::name('rate_group_user')->where('user_id', $user_id)->find();
    $channel_ids = [];
    if(!empty($rate_group_user)) {
        //选择费率分组中开启的渠道
        $channel_ids_temp = Db::name('rate_group_rule')
            ->where(['group_id' => $rate_group_user['group_id'], 'status' => 1])
            ->field('channel_id')
            ->select();
        foreach ($channel_ids_temp as $channel) {
            array_push($channel_ids, $channel['channel_id']);
        }
    }
    $userChannels = [];
    foreach ($channels as &$v) {
        if(!empty($channel_ids)) {
            if(!in_array($v->id, $channel_ids)) {
                //过滤费率分组中已关闭的渠道
                continue;
            }
        }
        $userChannels[] = [
            'user_id'      => $user_id,
            'channel_id'   => $v->id,
            'paytype'      => $v->paytype,
            'title'        => $v->title,
            'show_name'    => $v->show_name,
            'rate'         => get_user_rate($user_id, $v->id),
            'status'       => get_user_channel_status($user_id, $v->id),
            'product_name' => get_product_name($v->paytype), //添加了支付产品名称,
            'is_available' => $v->is_available, //支付产品可用的地方
        ];
    }
    return $userChannels;
}

/**
 * 发送站内信
 */
function sendMessage($from_id, $to_id, $title, $content) {
    $res = Db::name('Message')->insert([
        'from_id'   => $from_id,
        'to_id'     => $to_id,
        'title'     => $title,
        'content'   => $content,
        'status'    => 0,
        'create_at' => $_SERVER['REQUEST_TIME'],
    ]);
    return $res;
}

/**
 * 获取提现手续费
 */
function get_cash_fee($money) {
    $type = (int)sysconf('cash_fee_type');
    $fee  = sysconf('cash_fee');
    switch ($type) {
        case 1: // 固定
            return $fee >= 0 ? round($fee, 2) : 0;
            break;
        case 100: // 百分比
            if ($fee < 0 || $fee > 100) {
                return 0;
            } else {
                return round($fee / 100 * $money, 2);
            }
            break;
        default:
            return 0;
            break;
    }
}

/**
 * 获取提现手续费
 */
function get_auto_cash_fee($money) {
    $type = (int)sysconf('auto_cash_fee_type');
    $fee  = sysconf('auto_cash_fee');
    switch ($type) {
        case 1: // 固定
            return $fee >= 0 ? round($fee, 2) : 0;
            break;
        case 100: // 百分比
            if ($fee < 0 || $fee > 100) {
                return 0;
            } else {
                return round($fee / 100 * $money, 2);
            }
            break;
        default:
            return 0;
            break;
    }
}

/**
 * 获取推广返佣比率
 */
function get_spread_rebate_rate() {
    $rate = sysconf('spread_rebate_rate');
    if ($rate === null || $rate < 0 || $rate > 100) {
        return 0;
    }
    return round($rate / 100, 4);
}

/**
 * 字词过滤
 *
 * @param string $str 待检测字符串
 *
 * @return bool/string FALSE/敏感词汇
 */
function check_wordfilter($str) {
    if ($str !== '' && sysconf('site_wordfilter_status') == 1) {
        $words = explode('|', trim(sysconf('site_wordfilter_danger'), '|'));
        foreach ($words as $word) {
            if ($word) {
                if (strpos($str, $word) !== false) {
                    return $word;
                    break;
                }
            }
        }
    }
    return true;
}

/**
 * 获取短网址
 *
 * @param string $url url地址
 */
function get_short_domain($url, $type = '') {
    if ($type === '') {
        $type = sysconf('site_domain_short');
        if ($type === '') {
            return '';
        }
    }
    $dwz = \app\common\util\DWZ::load($type);
    switch ($type) {
        case 'Baidu':
            $shortDomain = $dwz->create($url);
            break;
        case 'Sina':
            $shortDomain = $dwz->create($url);
            break;
        case 'U6':
            $shortDomain = $dwz->create($url);
            break;
        default:
            return '';
            break;
    }
    if ($shortDomain === false) {
        return '';
    }
    return $shortDomain;
}

/**
 * 获取唯一ID
 */
function get_uniqid($len = 32) {
    return substr(md5(uniqid(md5(microtime(true)), true)), 0, $len);
}

// 检测输入的验证码是否正确，$code为用户输入的验证码字符串，$id多个验证码标识
function verify_code($code, $id = '') {
    $captcha = new \think\captcha\Captcha();
    return $captcha->check($code, $id);
}

/**
 * 记录文件日志
 */
function record_file_log($filename, $content) {
    file_put_contents(LOG_PATH . $filename . '.log', date('【Y-m-d H:i:s】') . "\r\n{$content}\r\n\r\n", FILE_APPEND);
}

/**
 * 获取短信运营成本
 */
function get_sms_cost() {
    switch (sysconf('sms_notify_channel')) {
        case 'alidayu':
            $cost = sysconf('alidayu_cost');
            break;
        case 'smsbao':
            $cost = sysconf('smsbao_cost');
            break;
        case 'yixin': // 弈新
            $cost = sysconf('yixin_sms_cost');
            break;
        case '1cloudsp': //天瑞云
            $cost = sysconf('1cloudsp_cost');
            break;
        case '253sms': //创蓝253
            $cost = sysconf('253sms_cost');
            break;
        default:
            $cost = 0;
            break;
    }
    return round($cost, 2);
}

/**
 * 将秒数转换为时间（年、天、小时、分、秒）
 *
 * @param number $time 秒数
 *
 * @return string
 */
function sec2Time($time) {
    if (is_numeric($time)) {
        $value = array(
            "years"   => 0, "days" => 0, "hours" => 0,
            "minutes" => 0, "seconds" => 0,
        );
        if ($time >= 31556926) {
            $value["years"] = floor($time / 31556926);
            $time           = ($time % 31556926);
        }
        if ($time >= 86400) {
            $value["days"] = floor($time / 86400);
            $time          = ($time % 86400);
        }
        if ($time >= 3600) {
            $value["hours"] = floor($time / 3600);
            $time           = ($time % 3600);
        }
        if ($time >= 60) {
            $value["minutes"] = floor($time / 60);
            $time             = ($time % 60);
        }
        $value["seconds"] = floor($time);
        $t                = '';
        if ($value["years"] > 0) {
            $t .= $value["years"] . "年";
        }
        if ($value["days"] > 0) {
            $t .= $value["days"] . "天";
        }
        if ($value["hours"] > 0) {
            $t .= $value["hours"] . "小时";
        }
        if ($value["minutes"] > 0) {
            $t .= $value["minutes"] . "分";
        }
        if ($value["seconds"] > 0) {
            $t .= $value["seconds"] . "秒";
        }
        return $t;
    } else {
        return (bool)false;
    }
}

//获取系统版本
function getVersion() {
    return \think\Config::get('version.VERSION');
}

//验证是否URL
function validateURL($URL) {
    if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $URL)) {
        return false;
    }
    return true;
}

//验证邮箱格式
function is_email($email) {
    $regx = "/^([\w-.]+)@(([[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.)|(([\w-]+.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(]?)$/";
    if (!preg_match($regx, $email)) {
        return false;
    }
    return true;
}

/**
 * 删除目录下的文件
 *
 * @param [type] $path
 *
 * @return void
 */
function delFileUnderDir($path, $delDir = false) {
    if (is_array($path)) {
        foreach ($path as $subPath) {
            delFileUnderDir($subPath, $delDir);
        }
    } elseif (is_dir($path)) {
        $handle = opendir($path);
        if ($handle) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    is_dir("$path/$item") ? delFileUnderDir("$path/$item", $delDir) : unlink("$path/$item");
                }
            }
            closedir($handle);
            if ($delDir) {
                return rmdir($path);
            }
        }
    } else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return false;
        }
    }
}

/**
 * POST 请求
 *
 * @param string $url
 * @param array  $param
 *
 * @return string content
 */
function http_post($url, $param) {
    $oCurl = curl_init();
    if (stripos($url, "https://") !== false) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (is_string($param)) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach ($param as $key => $val) {
            $aPOST[] = $key . "=" . urlencode($val);
        }
        $strPOST = join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus  = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

//获取顶级域名
function get_host() {
    $url   = $_SERVER['HTTP_HOST'];
    $data  = explode('.', $url);
    $co_ta = count($data);
    //判断是否是双后缀
    $zi_tow  = true;
    $host_cn = 'com.cn,net.cn,org.cn,gov.cn';
    $host_cn = explode(',', $host_cn);
    foreach ($host_cn as $host) {
        if (strpos($url, $host)) {
            $zi_tow = false;
        }
    }
    //如果是返回FALSE ，如果不是返回true
    if ($zi_tow == true) {
        $host = $data[$co_ta - 2] . '.' . $data[$co_ta - 1];
    } else {
        $host = $data[$co_ta - 3] . '.' . $data[$co_ta - 2] . '.' . $data[$co_ta - 1];
    }
    return $host;
}

//返回：获取代码部署系统中版本列表接口的URL
function get_version_list_url() {
    $versionListUrl = config('deploy_common.version_list_url');
    if (!$versionListUrl) {
        throw new Exception('配置错误');
    }
    return $versionListUrl . DS . config('deploy_unique.project_id') . DS . config('deploy_unique.environment_id');
}

//返回：代码更新URL
function get_version_update_url($versionHash) {
    $versionUpdateUrl = config('deploy_common.version_update_url');
    if (!$versionUpdateUrl) {
        throw new Exception('配置错误');
    }
    return $versionUpdateUrl . DS . $versionHash . DS . config('deploy_unique.environment_id');
}

/**
 * 生成txt
 *
 * @param  array  $title    标题字段
 * @param  array  $data     数据
 * @param  string $filename 文件名
 * @param  string $type     生成类型
 */
function generate_txt($title, $data, $filename, $type = 'file') {
    $file_name = $filename . ".txt";
    if ($type == 'file') {
        $file = fopen(TEMP_PATH . $file_name, "a");
    } else {
        header("Content-type:   application/octet-stream ");
        header("Accept-Ranges:   bytes ");
        header("Content-Disposition:   attachment;   filename=" . $filename . ".txt ");
        header("Expires:   0 ");
        header("Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 ");
        header("Pragma:   public ");
        //列名
        foreach ($title as $v) {
            echo iconv('UTF-8', 'GB2312//IGNORE', trim($v)) . "\t";
        }
        echo "\r\n";
        echo PHP_EOL;
        foreach ($data as $v) {
            foreach ($v as $t) {
                echo iconv('UTF-8', 'GB2312//IGNORE', trim($t)) . "\t";
            }
            echo PHP_EOL;
            echo "\r\n";
        }
        unset($data);
        exit();
    }
}

/**
 * 根据支付产品获取支付类型
 *
 * @param  array $productId 支付产品ID
 * @param  array $paytype   支付类型
 */
function getPaytypeByProductId($productId) {
    $payTypes = null;
    try {
        $payTypes = Db::name('pay_type')->select();

        foreach ($payTypes as $item) {
            if ($item['sub_lists']) {
                $item['sub_lists'] = json_decode($item['sub_lists']);
            }
        }
    } catch (\think\Exception $e) {
        $payTypes = null;
    }

    if (empty($payTypes)) {
        $payTypes = config('paytype_config');
    }

    $paytype = [];
    foreach ($payTypes as $k => $v) {
        if ($v['product_id'] == $productId) {
            array_push($paytype, $k);
        }
    }
    return $paytype;
}

/**
 * 判断是否QQ号
 *
 * @param b
 */
function isQQ($qq) {
    if (preg_match("/^[1-9][0-9]{4,11}$/", $qq)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 数组转换成xml字符串
 *
 * @param $arr
 *
 * @return string
 */
function arrayToXml($arr) {
    $xml = "<?xml version='1.0' encoding='UTF-8'?><xml>";
    foreach ($arr as $key => $val) {
        $xml .= "<$key><![CDATA[$val]]></$key>";
    }
    $xml .= "</xml>";
    return $xml;
}

/**
 * xml转换成数组
 *
 * @param $xml
 *
 * @return array|mixed|object
 */
function xmlToArray($xml) {
    libxml_disable_entity_loader(true);
    $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $arr;
}

/**
 * post 请求
 *
 * @param     $param
 * @param     $url
 * @param int $second
 *
 * @return mixed
 * @throws Exception
 */
function postCurl($url, $param, $second = 30, $refer = '') {
    $ch = curl_init();

    //设置 Refer
    if ($refer) {
        curl_setopt($ch, CURLOPT_REFERER, $refer); //防封域名
    }

    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

    $header[] = 'ContentType:application/json;charset=UTF-8';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch, CURLOPT_URL, $url);
    //设置header
    curl_setopt($ch, CURLOPT_HEADER, false);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //post提交方式
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    //运行curl
    $data = curl_exec($ch);

    //返回结果
    if ($data) {
        curl_close($ch);
        return $data;
    } else {
        $error = curl_errno($ch);
        curl_close($ch);
        throw new Exception("curl出错，错误码:$error");
    }
}

/**
 * 上传文件
 *
 * @param string $name
 *
 * @return array
 */
function upload($name = 'file') {
    $file = \think\Request::instance()->file($name);
    if ($file) {
        $ext      = pathinfo($file->getInfo('name'), 4);
        $md5      = [uniqid(), uniqid()];
        $filename = join('/', $md5) . ".{$ext}";
        if (strtolower($ext) == 'php' || !in_array(strtolower($ext), explode(',', strtolower(sysconf('storage_local_exts'))))) {
            return ['code' => 'ERROR', 'msg' => '文件上传类型受限'];
        }
        // 文件上传处理
        if (($info = $file->move('static' . DS . 'upload' . DS . $md5[0], $md5[1], true))) {
            $site_url = FileService::getFileUrl($filename, 'local');
            if ($site_url) {
                return ['site_url' => $site_url, 'code' => 'SUCCESS', 'msg' => '文件上传成功'];
            } else {
                return ['code' => 'ERROR', 'msg' => '获取上传文件' . $filename . '失败'];
            }
        }
    }

    return ['code' => 'ERROR', 'msg' => '文件上传失败'];
}

/**
 * 检查 ip 当日注册次数
 *
 * @param $ip
 *
 * @return bool
 */
function registerIpCheck($ip) {
    $count = Db::name('user')
               ->where('ip', '=', $ip)
               ->whereTime('create_at', 'today')->count('id');

    if ($count >= sysconf('ip_register_limit')) {
        return false;
    } else {
        return true;
    }
}

/**
 * 正确返回
 *
 * @param $data
 * @param $msg
 *
 * @return array
 */
function right($data, $msg) {
    return [
        'status' => true,
        'data'   => $data,
        'msg'    => $msg,
    ];
}

/**
 * 错误返回
 *
 * @param $msg
 * @param $code
 *
 * @return array
 */
function wrong($msg, $code = 500) {
    return [
        'status' => false,
        'code'   => $code,
        'msg'    => $msg,
    ];
}

/**
 * 请求成功
 *
 * @param     $data
 * @param     $msg
 * @param int $code
 *
 * @throws \think\exception\HttpResponseException
 */
function success($data = [], $msg = '提交成功！', $code = 200) {
    ob_clean();

    record_file_log('request_params', 'result :' . json_encode($data));

    $type = \think\Config::get('default_ajax_return');

    $result = [
        'code' => $code,
        'data' => !empty($data) ? $data : new \stdClass(),
        'msg'  => $msg,
    ];

    $response = \think\Response::create($result, $type);

    throw new \think\exception\HttpResponseException($response);
}

/**
 * request error
 *
 * @param int    $code
 * @param string $msg
 *
 * @throws \think\exception\HttpResponseException
 */
function error($code = 500, $msg = '提交失败') {
    ob_clean();

    record_file_log('request_params', 'result :' . $msg);

    $type = \think\Config::get('default_ajax_return');

    $result = [
        'code' => $code,
        'data' => new \stdClass(),
        'msg'  => $msg,
    ];

    $response = \think\Response::create($result, $type);

    throw new \think\exception\HttpResponseException($response);
}

/**
 * 获取上传文件
 *
 * @param      $field
 * @param bool $url
 *
 * @return array
 */
function getUploadFile($field, $url = false, $extison = []) {
    $request = \think\Request::instance();
    $file    = $request->file($field);
    if (!$file) {
        return wrong('上传文件不存在');
    }

    $ext      = pathinfo($file->getInfo('name'), 4);
    $md5      = [uniqid(), uniqid()];
    $filename = join('/', $md5) . ".{$ext}";
    if (!empty($extison)) {
        if (!in_array($ext, $extison)) {
            return wrong('文件上传类型受限');
        }
    } else {
        if ($ext == 'php' || !in_array(strtolower($ext), explode(',', strtolower(sysconf('storage_local_exts'))))) {
            return wrong('文件上传类型受限');
        }
    }

    // 文件上传处理
    if (($info = $file->move('static' . DS . 'upload' . DS . $md5[0], $md5[1], true))) {
        if ($url) {
            $site_url = FileService::getFileUrl($filename, 'local');
            if ($site_url) {
                return right(['file' => $site_url, 'filename' => $filename, 'resource' => $info], '上传成功');
            } else {
                return wrong('获取上传文件' . $filename . '失败');
            }
        } else {
            return right(['file' => ROOT_PATH . '/static/upload/' . $filename, 'filename' => $filename, 'resource' => $info], '上传成功');
        }
    } else {
        return wrong('保存上传文件' . $filename . '失败');
    }
}

/**
 * 二要素校验
 *
 * @param $idcard
 * @param $name
 *
 * @return bool
 */
function idcardAuth($idcard, $name) {
    $path    = sysconf('idcard_auth_path');
    $method  = "GET";
    $appcode = sysconf('idcard_auth_appcode');
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "idCard=$idcard&name=$name";
    $bodys  = "";
    $url    = $path . "?" . $querys;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    //curl_setopt($curl, CURLOPT_HEADER, true);   如不输出json, 请打开这行代码，打印调试头部状态码。
    //状态码: 200 正常；400 URL无效；401 appCode错误； 403 次数用完； 500 API网管错误
    if (1 == strpos("$" . $path, "https://")) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $out_put            = curl_exec($curl);
    $CURLINFO_HTTP_CODE = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($CURLINFO_HTTP_CODE == 200) {
        if ($out_put) {
            $out_put = json_decode($out_put, 1);
            if ($out_put[sysconf('idcard_auth_status_field')] == sysconf('idcard_auth_status_code')) {
                return true;
            }
        }
    } else if ($CURLINFO_HTTP_CODE == 403) {
        record_file_log('idcard_auth', 'result :' . $CURLINFO_HTTP_CODE . '剩余次数不足');
    } else if ($CURLINFO_HTTP_CODE == 400) {
        record_file_log('idcard_auth', 'result :' . $CURLINFO_HTTP_CODE . 'URL无效');
    } else if ($CURLINFO_HTTP_CODE == 401) {
        record_file_log('idcard_auth', 'result :' . $CURLINFO_HTTP_CODE . 'APPCODE错误');
    } else {
        record_file_log('idcard_auth', 'result :' . $CURLINFO_HTTP_CODE . '未知错误');
    }


    return false;
}

function idcardnoMask($idcardno){
    if(strlen($idcardno) == 15) {
        $idcardno = substr_replace($idcardno,"****",6,6);
    } elseif(strlen($idcardno) == 18) {
        $idcardno = substr_replace($idcardno,"********",6,8);
    } else {
        $idcardno = '';
    }
    return $idcardno;
}