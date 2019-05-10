<?php

namespace app\index\controller;

use app\common\model\Article as ArticleModel;
use app\common\model\ArticleCategory as ArticleCategoryModel;
use think\Controller;
use think\Db;
use think\Request;

class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //订单成交次数
        $stat['orderCount'] = Db::table('order')->where('status', 1)->count();
        //订单总额
        $stat['orderSum'] = Db::table('order')->where('status', 1)->sum('total_price');
        //商户个数
        $stat['userCount'] = Db::table('user')->count();
        foreach($stat as $k => $v) {
            $stat[$k]+=0;
        }
        //结算公告
        $withdrawNotice = [];
        $category = ArticleCategoryModel::get(['alias' => 'settlement', 'status' => 1]);
        if($category) {
            $withdrawNotice = Db::name('article')->where('cate_id', $category->id)->limit(0,6)->order('top desc,id desc')->select();
            foreach ($withdrawNotice as $k => $v) {
                if(time() - $v['create_at'] < 86400) {
                    $withdrawNotice[$k]['is_new'] = 1;
                } else {
                    $withdrawNotice[$k]['is_new'] = 0;
                }
            }
        }

        //新闻动态
        $newsList = [];
        $category = ArticleCategoryModel::get(['alias' => 'news', 'status' => 1]);
        if($category) {
            $newsList = Db::name('article')->where('cate_id', $category->id)->limit(0,5)->order('top desc,id desc')->select();
        }

        //系统公告
        $announceList = [];
        $category = ArticleCategoryModel::get(['alias' => 'notice', 'status' => 1]);
        if($category) {
            $announceList = Db::name('article')->where('cate_id', $category->id)->limit(0,5)->order('top desc,id desc')->select();
        }
        $this->assign('stat', $stat);
        $this->assign('withdrawNotice', $withdrawNotice);
        $this->assign('newsList', $newsList);
        $this->assign('announceList', $announceList);
        return $this->fetch();
    }

    public function app()
    {
        return $this->fetch();
    }

    public function faq()
    {
        $category = ArticleCategoryModel::get(['alias' => 'faq', 'status' => 1]);
        $articles = [];
        $count = 0;
        $pagesize = 10;
        if ($category) {
            $count = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])->count();
            if($count>0) {
                $articles = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])
                    ->order('top desc, id desc')
                    ->page(1, $pagesize)
                    ->select();
            }
        }
        $this->assign('title', '常见问题');
        $this->assign('tab', 'faq');
        $this->assign('more', $count>$pagesize ? 1 :0);
        $this->assign('articles', $articles);
        return $this->fetch();
    }

    //系统公告
    public function notice()
    {
        $category = ArticleCategoryModel::get(['alias' => 'notice', 'status' => 1]);
        $articles = [];
        $count = 0;
        $pagesize = 10;
        if ($category) {
            $count = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])->count();
            if($count>0) {
                $articles = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])
                    ->order('top desc, id desc')
                    ->page(1, $pagesize)
                    ->select();
            }
        }
        foreach ($articles as $k => $v) {
            if(time() - $v['create_at'] < 86400) {
                $articles[$k]['is_new'] = 1;
            } else {
                $articles[$k]['is_new'] = 0;
            }
        }
        $this->assign('title', '系统公告');
        $this->assign('tab', 'notice');
        $this->assign('more', $count>$pagesize ? 1 :0);
        $this->assign('articles', $articles);
        return $this->fetch();
    }

    //新闻资讯
    public function news()
    {
        $category = ArticleCategoryModel::get(['alias' => 'news', 'status' => 1]);
        $articles = [];
        $count = 0;
        $pagesize = 10;
        if ($category) {
            $count = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])->count();
            if($count>0) {
                $articles = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])
                    ->order('top desc, id desc')
                    ->page(1, $pagesize)
                    ->select();
            }
        }
        foreach ($articles as $k => $v) {
            if(time() - $v['create_at'] < 86400) {
                $articles[$k]['is_new'] = 1;
            } else {
                $articles[$k]['is_new'] = 0;
            }
        }
        $this->assign('title', '新闻资讯');
        $this->assign('tab', 'news');
        $this->assign('more', $count>$pagesize ? 1 :0);
        $this->assign('articles', $articles);
        return $this->fetch();
    }

    //结算公告
    public function settlement()
    {
        $category = ArticleCategoryModel::get(['alias' => 'settlement', 'status' => 1]);
        $articles = [];
        $count = 0;
        $pagesize = 10;
        if ($category) {
            $count = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])->count();
            if($count>0) {
                $articles = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])
                    ->order('top desc, id desc')
                    ->page(1, $pagesize)
                    ->select();
            }

        }
        foreach ($articles as $k => $v) {
            if(time() - $v['create_at'] < 86400) {
                $articles[$k]['is_new'] = 1;
            } else {
                $articles[$k]['is_new'] = 0;
            }
        }
        $this->assign('title', '结算公告');
        $this->assign('tab', 'settlement');
        $this->assign('articles', $articles);
        $this->assign('more', $count>$pagesize ? 1 :0);
        return $this->fetch();
    }

    public function contact()
    {
//         $content = htmlspecialchars_decode(sysconf('contact_us'));
        //         $this->assign('content', $content);

        $site_info_qrcode_desc = sysconf('site_info_qrcode_desc');
        $site_info_qrcode_desc = str_replace(PHP_EOL, "<br />", $site_info_qrcode_desc);
        $this->assign('site_info_qrcode_desc', $site_info_qrcode_desc);
        return $this->fetch();
    }

    public function content()
    {
        $id = input('id/d', 0);
        if ($id <= 0) {
            $this->error('参数错误');
        }
        $data = Db::name('article')->where('id', $id)->find();
        if (empty($data)) {
            $this->error('文章不存在');
        }
        $category = ArticleCategoryModel::get(['id' => $data['cate_id']]);
        Db::name('article')->where('id', $id)->setInc('views');
        //上一页
        $pre = Db::name('article')->where(['id' => ['lt', $id], 'cate_id' => $category['id']])->order('id desc')->find();
        //下一页
        $next =  Db::name('article')->where(['id' => ['gt', $id], 'cate_id' => $category['id']])->order('id asc')->find();
        $this->assign('data', $data);
        $this->assign('category', $category);
        $this->assign('pre', $pre);
        $this->assign('next', $next);
        return $this->fetch();
    }
    //注册协议
    public function agreement()
    {
        $data = Db::name('article')->where('id', 13)->find();
        $this->assign('data', $data);
        return $this->fetch();
    }

    //用户协议
    public function service_agreement()
    {
        $data = Db::name('article')->where('id', 15)->find();
        $this->assign('data', $data);
        return $this->fetch('agreement');
    }

    //关于我们
    public function about_us()
    {
        return $this->fetch('aboutus');
    }

    public function vhash()
    {
        echo config('deploy_unique.vhash');
    }

    /**
     * 检查是否正在进行代码更新
     */
    public function is_version_updating()
    {
        $result = (int) is_file(RUNTIME_PATH . 'version_update.lock');
        $this->success('', null, $result);
    }

    public function getMore()
    {
        if (Request::instance()->isAjax()) {
            $alias = input('alias');
            $page = input('page/d', 1);
            $pagesize = 10;
            $category = ArticleCategoryModel::get(['alias' => $alias, 'status' => 1]);
            $articles = [];
            if ($category) {
                $articles = ArticleModel::where(['cate_id' => $category->id, 'status' => 1])
                    ->order('top desc, id desc')
                    ->page($page, $pagesize)
                    ->select();
            }
            foreach ($articles as $k => $v) {
                if(time() - $v['create_at'] < 86400) {
                    $articles[$k]['is_new'] = 1;
                } else {
                    $articles[$k]['is_new'] = 0;
                }
                $articles[$k]['content'] = htmlspecialchars_decode($v['content']);
                if($v['description']) {
                    $articles[$k]['description'] = mb_substr($v['description'], 0, 100,'utf-8');
                }
                $articles[$k]['date'] = $v['create_at'] > 0 ? date('Y-m-d', $v['create_at']) : '';
                $articles[$k]['time'] = $v['create_at'] > 0 ? date('H:i:s', $v['create_at']) : '';
                $articles[$k]['create_at'] = $v['create_at'] > 0 ? date('Y-m-d H:i', $v['create_at']) : '';
            }
            return J(1, '请求成功', $articles);
        }
    }
    public function test()
    {
        $payType = get_paytype_list();

        foreach ($payType as $key => $item) {
            $data = [
                'id' => $key,
                'name' => $item['name'],
                'product_id' => $item['product_id'],
                'logo' => $item['logo'],
                'ico' => $item['ico'],
                'is_mobile' => $item['is_mobile'],
                'is_form_data' => $item['is_form_data'],
            ];
            if (isset($item['sub_lists'])) {
                $data['sub_lists'] = json_encode($item['sub_lists']);
            }
            Db::name('pay_type')->insert($data);
        }
    }
}
