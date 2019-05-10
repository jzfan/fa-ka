<?php
/**
 * 工作配置
 */
use think\Db;
use think\Request;

return [
    // 模板变量
    'view_replace_str' => [
        '__RES__' => Db::name('systemConfig')->where('name', 'site_domain_res')->value('value'), // 静态资源CDN
    ],

    //  支付分类资源服务器
    'paytype_resouce_server' => Request::instance()->domain() . '/static/common/paytype/',

    // 支付页面风格
    'pay_themes' => [
        [
            'name' => '默认',
            'alias' => 'default',
        ],
        [
            'name' => '绝地求生',
            'alias' => 'chiji',
        ],
        [
            'name' => '王者',
            'alias' => 'wangzhe',
        ],
        [
            'name' => '阴阳师',
            'alias' => 'yinyangshi',
        ],
        [
            'name' => '个人',
            'alias' => 'geren',
        ],
        [
            'name' => '地下城与勇士',
            'alias' => 'dnf',
        ]
    ],

    //支付产品
    'pay_product' => [
        1 => '支付宝',
        2 => '微信支付',
        3 => 'QQ钱包',
        4 => '京东钱包',
        5 => '百度钱包',
        6 => '网银',
    ],
];
