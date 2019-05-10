/* This file is created by MySQLReback 2019-02-28 23:05:00 */
 /* 创建表结构 `announce_log`  */
 DROP TABLE IF EXISTS `announce_log`;/* MySQLReback Separation */ CREATE TABLE `announce_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `article_id` int(11) NOT NULL DEFAULT '0' COMMENT '公告ID',
  `create_at` int(11) NOT NULL DEFAULT '0' COMMENT '阅读时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `app_menu`  */
 DROP TABLE IF EXISTS `app_menu`;/* MySQLReback Separation */ CREATE TABLE `app_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `function_id` int(11) NOT NULL COMMENT '唯一值',
  `menu` text NOT NULL COMMENT '菜单项',
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_menu_id_uindex` (`id`),
  UNIQUE KEY `app_menu_function_id_uindex` (`function_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='客户端菜单';/* MySQLReback Separation */
 /* 插入数据 `app_menu` */
 INSERT INTO `app_menu` VALUES ('1','0','{\"title\":\"\\u5546\\u54c1\\u5206\\u7c7b\",\"img_url\":\"http://api.faka.zuy.cn/static/upload/5b5ad8346b8f7/5b5ad8346b933.png\",\"function_id\":\"0\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.goods.GoodsGenreActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"ClassifyListViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/goodsCategory/lists/lists\"}'),('2','1','{\"title\":\"\\u6dfb\\u52a0\\u5546\\u54c1\",\"img_url\":\"http://api.faka.zuy.cn/static/upload/5b5ad8d9671f5/5b5ad8d96722e.png\",\"function_id\":\"1\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.goods.AddGoodsActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"AddGoodsViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/goods/add/add\"}'),('3','2','{\"title\":\"\\u5546\\u54c1\\u5217\\u8868\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5ad920eed40\\/5b5ad920eed7a.png\",\"function_id\":\"2\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.goods.GoodsListActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"GoodsListViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/goods/lists/lists\"}'),('4','3','{\"title\":\"\\u5361\\u5bc6\\u5217\\u8868\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5ada3c8c52f\\/5b5ada3c8c568.png\",\"function_id\":\"5\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.CardListActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Cards\",\"iOS_ViewController\":\"CardListViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/goodsCard/lists/lists\"}'),('5','4','{\"title\":\"\\u5e97\\u94fa\\u94fe\\u63a5\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5adc07170e2\\/5b5adc071711c.png\",\"function_id\":\"3\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.goods.StoreLinksActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"StoreLinkViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/user/link/link\"}'),('6','5','{\"title\":\"\\u6dfb\\u52a0\\u5361\\u5bc6\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5adc6b692fb\\/5b5adc6b69335.png\",\"function_id\":\"4\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.CardAddActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Cards\",\"iOS_ViewController\":\"AddCardsViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/goodsCard/edit/edit\"}'),('7','6','{\"title\":\"\\u8ba2\\u5355\\u5217\\u8868\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5adce9bdd2e\\/5b5adce9bdd68.png\",\"function_id\":\"6\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.order.OrderListActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Order\",\"iOS_ViewController\":\"OrderListViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/order/lists/lists\"}'),('8','7','{\"title\":\"\\u4f18\\u60e0\\u5238\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5add2b1ed3c\\/5b5add2b1ed75.png\",\"function_id\":\"7\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.CouponsActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Coupon\",\"iOS_ViewController\":\"CouponListViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/goodsCoupon/lists/lists\"}'),('9','8','{\"title\":\"\\u63d0\\u73b0\\u7ba1\\u7406\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5add6aafc89\\/5b5add6aafcc3.png\",\"function_id\":\"8\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.ApplyMoneyListActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"EnchashmentListViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/user/cashList/cashList\"}'),('10','9','{\"title\":\"\\u4ed8\\u6b3e\\u65b9\\u5f0f\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5addb00d1b5\\/5b5addb00d1ee.png\",\"function_id\":\"9\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.PayWayActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"PayTypeListViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/pay/lists/lists\"}');/* MySQLReback Separation */
 /* 插入数据 `app_menu` */
 INSERT INTO `app_menu` VALUES ('11','10','{\"title\":\"\\u5361\\u5bc6\\u56de\\u6536\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5addcf3fab3\\/5b5addcf3faec.png\",\"function_id\":\"10\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.RecycleBinActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Cards\",\"iOS_ViewController\":\"CardRecycleViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/goodsCard/trash/trash\"}');/* MySQLReback Separation */
 /* 插入数据 `app_menu` */
 INSERT INTO `app_menu` VALUES ('12','11','{\"title\":\"\\u6536\\u76ca\\u5206\\u6790\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5addf6a6548\\/5b5addf6a6581.png\",\"function_id\":\"11\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.IncomeAnalysisActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Order\",\"iOS_ViewController\":\"InComeAnalyzeViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/user/statics/statics\"}'),('13','12','{\"title\":\"\\u6e20\\u9053\\u5206\\u6790\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5ade8db3465\\/5b5ade8db349e.png\",\"function_id\":\"12\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.ChannelAnalysisActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"ChannelAnalysisViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/pay/detail/detail\"}'),('16','14','{\"title\":\"\\u767b\\u5f55\\u65e5\\u5fd7\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5b4e6037138\\/5b5b4e6037171.png\",\"function_id\":\"14\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.LoginDiaryActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"LoginDiaryViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/user/loginLogs/loginLogs\"}'),('17','15','{\"title\":\"\\u63a8\\u5e7f\\u7ba1\\u7406\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5bccc8c9960\\/5b5bccc8c99a0.png\",\"function_id\":\"15\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.SpreadManageActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"PromoteViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/spread/lists/lists\"}'),('18','16','{\"title\":\"\\u6295\\u8bc9\\u7ba1\\u7406\",\"img_url\":\"http:\\/\\/api.faka.zuy.cn\\/static\\/upload\\/5b5bce9b6e4ed\\/5b5bce9b6e526.png\",\"function_id\":\"16\",\"sort\":0,\"is_show\":true,\"function_links\":\"cc.zuy.faka_android.ui.activity.menu.ComplainActivity\",\"iOS_ViewType\":\"1\",\"iOS_sotryBoard\":\"Home\",\"iOS_ViewController\":\"ComplainViewController\",\"android_Ver\":\"1.0\",\"iOS_Ver\":\"1.0\",\"wxapp_page\":\"/pages/complaint/lists/lists\"}');/* MySQLReback Separation */
 /* 创建表结构 `app_version`  */
 DROP TABLE IF EXISTS `app_version`;/* MySQLReback Separation */ CREATE TABLE `app_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` enum('android','ios') NOT NULL COMMENT '平台',
  `package` varchar(255) NOT NULL COMMENT '安装包下载地址',
  `create_at` int(10) NOT NULL COMMENT '发布时间',
  `version` varchar(255) NOT NULL COMMENT '安装包版本',
  `remark` text NOT NULL COMMENT '更新说明',
  `create_ip` varchar(255) NOT NULL COMMENT '上传 IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户端版本';/* MySQLReback Separation */
 /* 创建表结构 `article`  */
 DROP TABLE IF EXISTS `article`;/* MySQLReback Separation */ CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL DEFAULT '',
  `title_img` varchar(255) NOT NULL DEFAULT '' COMMENT '标题图',
  `description` text NOT NULL COMMENT '文章描述',
  `content` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `create_at` int(10) unsigned NOT NULL,
  `update_at` int(10) unsigned NOT NULL DEFAULT '0',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1表示系统调用到的页面，禁止删除',
  `top` int(10) NOT NULL DEFAULT '0' COMMENT '置顶时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `article` */
 INSERT INTO `article` VALUES ('4','2','1.怎么入驻自动发卡平台,成为商户?','','','&lt;p&gt;通过平台的账户注册功能，即可免费入驻自动发卡平台。&lt;/p&gt;
','1','0','1520271835','0','0','0'),('5','2','2.怎么登录自动发卡平台商户后台?','','','&lt;p&gt;点此右上角&amp;ldquo;登录&amp;rdquo;按钮进入&lt;/p&gt;
','1','0','1520271854','0','0','0'),('6','2','3.怎么用平台销售虚拟商品？','','','&lt;p&gt;商户通过后台可以添加商品，每个商品自动发卡平台都会分配一个购买链接，商户只要将这个链接发送给买家，买家付款后平台自动发货，即可完成交易。&lt;/p&gt;
','1','0','1520271993','0','0','0'),('7','2','4.平台可以卖些什么？','','','&lt;p&gt;虚拟商品(例如软件注册码，论坛帐号等等)，不可以卖实物（例如衣服，水果等等）。&lt;/p&gt;
','1','0','1520272006','0','0','0'),('8','2','5.账户的金额满多少自动结算？','','','&lt;p&gt;商户账户金额满100.00元，当天晚上12点后，系统自动帮您提现，财务将于第二天12点前结算到您预留的账户，不满100.00元可以申请手动提现。&lt;/p&gt;
','1','0','1520272019','0','0','0'),('9','2','6.财务结算商户方式有那些？','','','&lt;p&gt;支持支付宝、银行卡，后期我们还会增加微信结算。&lt;/p&gt;
','1','0','1520272029','0','0','0'),('10','2','7.如果买家已经付款,但是他说没有收到卡密该怎么办？','','','&lt;p&gt;请直接联系自动发卡平台客服QQ解决。&lt;/p&gt;
','1','0','1520272041','0','0','0'),('11','2','8.自动发卡平台安全吗？','','','&lt;p&gt;非常安全，自动发卡平台运用先进的安全技术保护用户在自动发卡平台账户中存储的个人信息、账户信息以及交易记录的安全。&lt;/p&gt;

&lt;p&gt;自动发卡平台拥有完善的全监测系统，可以及时发现网站的非正常访问并做相应的安全响应。&lt;span style=&quot;display: none;&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;/p&gt;
','1','0','1521202911','0','0','0');/* MySQLReback Separation */
 /* 插入数据 `article` */
 INSERT INTO `article` VALUES ('12','3','平台禁售商品类目','','','&lt;table&gt;
	&lt;colgroup&gt;
		&lt;col width=&quot;17%&quot; /&gt;
		&lt;col width=&quot;22%&quot; /&gt;
		&lt;col width=&quot;61%&quot; /&gt;
	&lt;/colgroup&gt;
	&lt;tbody&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;4&quot;&gt;政治类&lt;/td&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;反动信息&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;含有反动、破坏国家统一、破坏主权及领土完整、破坏社会稳定，涉及国家机密、扰乱社会秩序，宣扬邪教迷信，宣扬宗教、种族歧视、藏独、法轮功、违反伦理等信息，或法律法规禁止出版发行的书籍、音像制品、视频、文件资料等；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;其他反动物品及言论等；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;政治物品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;国家机密文件资料等；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;带有宗教歧视、种族歧视的相关商品或信息。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;军警类&lt;/td&gt;
			&lt;td align=&quot;center&quot;&gt;军火武器/枪械及配件&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;枪支、弹药、军火及其相关器材、配件、附属产品，仿制品的衍生工艺品等；包括枪瞄、枪套等枪支配件以及90%大小相似的仿真枪；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;军警用品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;冒用警用、军用制服、标志、设备及制品；带有警用标志（警徽）物品；描述为军队、警队使用物品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;8&quot;&gt;治安类（危险品）&lt;/td&gt;
			&lt;td align=&quot;center&quot;&gt;危险品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;烟花爆竹、易燃、易爆物品；介绍制作易燃易爆品方法的相关教程、书籍。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;危险武器&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;可致使他人暂时失去反抗能力，对他人身体造成重大伤害的管制器具；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;甩棍、电棍、电击棍等危险物品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;危险化学品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;剧毒化学品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;国家名录中禁止出售的危险化学品（剧毒化学品除外）；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;剧毒物品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;毒品、制毒原料、制毒化学品及致瘾性药物；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;毒品吸食工具及配件；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;毒品、毒品检测工具&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;毒品检测试剂准入只允许有资质的戒毒所机构。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;8&quot;&gt;黄赌毒类&lt;/td&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;3&quot;&gt;黄色低俗色情服务及信息&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;含有色情淫秽内容的音像制品及视频；色情陪聊服务；成人网站论坛的帐号及邀请码；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;用于传播色情信息的软件及图片；含有情色、暴力、低俗内容的音像制品；原味内衣及相关产品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;含有情色、暴力、低俗内容的动漫、读物、游戏和图片；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;赌博器具&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;如电子老虎机、百家乐桌子，或者百家乐的筹码等；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;赌博/博彩服务&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;棋牌类网站，游戏货币能直接兑换现金。或者棋牌类网站，游戏货币能直接通过账户流转，且有专门银商收购贩卖游戏币。如：捕鱼游戏、金鲨银鲨、彩金、牛牛等；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;私彩&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;私彩是指私人坐庄，由个人或组织发行的，以诈取钱财为目的的非法彩票。一般以公益彩票的开奖结果进行赌博，骗取高额利润，如地下六合彩，以香港六合彩的开奖号码进行变相赌博，以1赔40左右的高额赔率欺骗民众；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;毒品、吸毒工具、毒品检测工具&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;毒品检测试剂准入只允许有资质的戒毒所机构；毒品检测工具，不允许出售。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;其他黄色低俗物品或服务&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;泡妞秘籍、恋足、恋童、人体摄影、丝袜、原味、捆绑等带有不正当引导倾向的；可致使他人暂时失去反抗能力、意识模糊的口服或外用的催情类商品及人造处女膜。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;7&quot;&gt;侵害隐私类&lt;/td&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;4&quot;&gt;间谍器材&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;用于监听、窃取隐私或机密的软件及设备；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;用于非法摄像、录音、取证等用途的设备；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;身份证及身份证验证、阅读设备；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;盗取或破解账号密码的软件、工具、教程及产物。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;身份信息等其他侵犯个人隐私的信息&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;个人隐私信息及企业内部数据买卖；提供个人手机定位、电话清单查询、银行账户查询等服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;出售、转让、回收，包括已作废或者作为收藏用途的银行卡；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;其他危害隐私的物品或服务&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;如间谍服务，私人侦探等。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;6&quot;&gt;医药器械类&lt;/td&gt;
			&lt;td align=&quot;center&quot;&gt;麻醉药品和精神类药品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;精神类、麻醉类、有毒类、放射类、兴奋剂类、计生类药品；非药品添加药品成分；国家公示已查处、药品监督管理局认定禁止生产、使用的药品；用于预防、治疗人体疾病的药物、血液制品或医疗器械；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;毒性药品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;灭鼠药、蟑螂药、兽药不做限制&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;处方药&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;参照《互联网药品交易服务审批暂行规定》&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;无批号药品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;未经药品监督管理部门批准生产、进口，或未经检验即销售的药品&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;胎儿性别鉴定&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;违反国家及世界卫生组织的人道精神，任何涉及胎儿性别鉴定商品、服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;成人药品（春药）&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;口服催情类药品。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;7&quot;&gt;国家保护动植物&lt;/td&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;7&quot;&gt;国家保护动植物&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;人体器官、遗体；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;国家重点保护类动物、濒危动物的活体、内脏、任何肢体、皮毛、标本或其他制成品，已灭绝动物与现有国家二级以上保护动物的化石。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;国家保护类植物活体（树苗除外）；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;国家保护的有益的或者有重要经济、科学研究价值的陆生野生动物的活体、内脏、任何肢体、皮毛、标本或其他制成品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;捕鱼器相关设备及配件；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;其他动物捕杀工具；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;猫狗肉、猫狗皮毛、鱼翅、熊胆及其制品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;26&quot;&gt;虚拟类&lt;/td&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;10&quot;&gt;网络服务&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;未经国家备案的网络游戏、游戏点卡、货币等相关服务类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;外挂、私服相关的网游类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;官方已停止经营的游戏点卡或平台卡商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;时间不可查询的虚拟服务类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;网络账户死保帐号以及腾讯QQ帐号；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;Itunes帐号及用户充值类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;各类短信、邮件、QQ/微信群发设备、软件及服务，如短信轰炸机；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;虚拟代刷、炒信、恶意刷店铺流量等服务类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;黑客相关&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;涉外婚介&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;5&quot;&gt;电信欺诈&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;不可查询的分期返还话费类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;不限时间与流量的、时间不可查询的以及被称为漏洞卡、集团卡、内部卡、测试卡的上网资费卡或资费套餐及SIM 卡；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;一卡多号；有蹭网功能的无线网卡，以及描述信息中有告知会员能用于蹭网的设备；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;带破解功能的手机卡贴；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;SP业务自消费类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;6&quot;&gt;非法服务、票证&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;伪造变造国家机关或特定机构颁发的文件、证书、公章、防伪标签等，仅限国家机关或特定机构方可提供的服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;伪造各类公章、图章扰乱市场业务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;尚可使用或用于报销的票据（及服务）,尚可使用的外贸单证以及代理报关、清单、商检、单证手续的服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;未公开发行的国家级正式考试答案；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;代写论文、代考试类相关服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;炒作博客人气、炒作网站人气、代投票类商品或信息；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;5&quot;&gt;其他高风险的服务&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;弹窗广告虚假中奖信息；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;友情链接名称涉及禁售不合作内容；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;模板网站且网站可以打开，只有框架（无商品、无效商品、无帖子）；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;恶意舞弊投票类型；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;微交易，云交易类商户；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;20&quot;&gt;金融类&lt;/td&gt;
			&lt;td align=&quot;center&quot;&gt;非法传销&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;传销，金融互助平台。虚假宣传致富，出售书籍、碟片、成功学、消费返利、多级分销、发展下线&amp;ldquo;金字塔&amp;rdquo;型提成等；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;彩票销售&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;除O2O类的平台彩票销售；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;虚拟货币&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;虚拟货币，比特币、莱特币、元宝币等虚拟货币交易。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;10&quot;&gt;货币业务&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;假币或制造机器；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;买卖银行账户（银行卡）；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;非法集资；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;外汇兑换服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;境外账户中的虚拟货币；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;期货；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;有价证券或凭证；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;P2P等理财类网站；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;大量流通中的外币及外币兑换服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;借贷平台，融资租赁平台，非实物众筹；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;二清无牌机构&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;未取得《支付业务许可证》、非法开展资金支付结算业务的机构，存在挪用、占用资金的风险；禁止随意变更使用场景和范围，出租、出借、出售接口；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;4&quot;&gt;支付业务&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;禁止网上销售POS机具和提供POS收单业务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;随意变更使用场景和范围；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;信用卡套现服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;违规聚合支付业务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;非法交易所&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;微盘类交易；省级及以上政府批文且经营范围不符；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;一元购&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;购买抽奖码，商户摇奖公布中奖号码，可能获得实物或是贵金属饰品等物品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;烟草类&lt;/td&gt;
			&lt;td align=&quot;center&quot;&gt;烟草类&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;烟草专卖品及烟草专用机械（线上禁售）。可参照《烟草专用机械名录》（国烟法[2004]）。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;2&quot;&gt;收藏类&lt;/td&gt;
			&lt;td align=&quot;center&quot;&gt;考古文物&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;《中华人民共和国文物保护法》第五十一条第五十二条，国有文物不得买卖&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot;&gt;收藏品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;国家禁止的集邮票品以及未经邮政行业管理部门批准制作的集邮品，以及一九四九年之后发行的包含&amp;ldquo;中华民国&amp;rdquo;字样的邮品。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;3&quot;&gt;商品质量类&lt;/td&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;3&quot;&gt;假冒伪劣产品&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;由不具备生产资质的生产商生产的或不符合国家、地方、行业、企业强制性标准的商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;经权威质检部门或生产商认定、公布或召回的商品，国家明令淘汰或停止销售的商品，过期、失效、变质的商品，以及含有罂粟籽的食品、调味品、护肤品等制成品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;商品本身或外包装上所注明的产品标准、认证标志、成份及含量不符合国家规定的商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;21&quot;&gt;其他类&lt;/td&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;8&quot;&gt;非法所得及非法用工具&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;走私、盗窃、抢劫等非法所得；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;赌博用具、考试作弊工具、汽车跑表器材等非法用途工具；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;摩卡等，蹭网卡、一卡多号的手机卡等&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;卫星信号收发装置及软件；用于无线电信号屏蔽的仪器或设备；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;撬锁工具、开锁服务及其相关教程、书籍；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;涉嫌欺诈等非法用途的软件；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;可能用于逃避交通管理的商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;猫狗肉、猫狗皮毛、鱼翅、熊胆及其制品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;center&quot; rowspan=&quot;13&quot;&gt;特殊时期特殊规定&lt;/td&gt;
			&lt;td align=&quot;left&quot;&gt;未经许可的募捐类商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;原油；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;代孕服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;替考、代考、代写论文服务；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;未经许可发布的奥林匹克运动会、世界博览会、亚洲运动会等特许商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;邮局包裹、EMS 专递、快递等物流单据凭证及单号；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;国家补助或无偿发放的不得私自转让的商品；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;军需、国家机关专供、特供等商品。&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;股权类众筹商户；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;购物返利类商户；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;虚假交易；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;套现行为；&lt;/td&gt;
		&lt;/tr&gt;
		&lt;tr&gt;
			&lt;td align=&quot;left&quot;&gt;其它违反法律法规、社会公序良俗而不宜接入有卡啦的物品或服务&lt;/td&gt;
		&lt;/tr&gt;
	&lt;/tbody&gt;
&lt;/table&gt;
','1','0','1522739045','0','0','0');/* MySQLReback Separation */
 /* 插入数据 `article` */
 INSERT INTO `article` VALUES ('13','3','注册协议','','','&lt;p&gt;&lt;b&gt;注册协议&lt;/b&gt;&lt;/p&gt;

&lt;p&gt;&lt;strong&gt;【审慎阅读】&lt;/strong&gt;您在申请注册流程中点击同意前，应当认真阅读以下协议。&lt;strong&gt;请您务必审慎阅读、充分理解协议中相关条款内容，其中包括：&lt;/strong&gt;&lt;/p&gt;

&lt;p&gt;1、&lt;strong&gt;与您约定免除或限制责任的条款；&lt;/strong&gt;&lt;/p&gt;

&lt;p&gt;2、&lt;strong&gt;与您约定法律适用和管辖的条款；&lt;/strong&gt;&lt;/p&gt;

&lt;p&gt;3、&lt;strong&gt;其他以粗体下划线标识的重要条款。&lt;/strong&gt;&lt;/p&gt;

&lt;p&gt;如您对协议有任何疑问，可向平台客服咨询。&lt;/p&gt;

&lt;p&gt;&lt;strong&gt;【特别提示】&lt;/strong&gt;当您按照注册页面提示填写信息、阅读并同意协议且完成全部注册程序后，即表示您已充分阅读、理解并接受协议的全部内容。如您因平台服务与自动发卡发生争议的，适用《自动发卡平台服务协议》处理。如您在使用平台服务过程中与其他用户发生争议的，依您与其他用户达成的协议处理。&lt;/p&gt;

&lt;p&gt;&lt;strong&gt;阅读协议的过程中，如果您不同意相关协议或其中任何条款约定，您应立即停止注册程序。&lt;/strong&gt;&lt;/p&gt;

&lt;p&gt;《自动发卡平台服务协议》&lt;/p&gt;

&lt;p&gt;《法律声明及隐私权政策》&lt;/p&gt;

&lt;p&gt;&lt;a href=&quot;/index/index/content/id/12&quot; target=&quot;_blank&quot;&gt;《平台禁售商品目录》&lt;/a&gt;&lt;/p&gt;
','1','0','1522762435','0','1','0'),('14','3','企业资质','','','&lt;img src=&quot;http://oqwrj8igk.bkt.clouddn.com/WX20180324-190756.png&quot;/&gt;','1','3','1521889791','0','0','0'),('15','3','用户协议','','','&lt;p&gt;用户协议&lt;/p&gt;
','1','0','1524237953','0','1','0');/* MySQLReback Separation */
 /* 创建表结构 `article_category`  */
 DROP TABLE IF EXISTS `article_category`;/* MySQLReback Separation */ CREATE TABLE `article_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(1024) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `alias` varchar(30) NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `create_at` int(10) unsigned NOT NULL,
  `update_at` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `article_category` */
 INSERT INTO `article_category` VALUES ('1','0','0','系统公告','notice','系统公告','1','1520268395','0'),('2','0','0','常见问题','faq','常见问题','1','1520268562','0'),('3','0','0','系统单页','single','系统单页禁止删除','1','1524220912','0');/* MySQLReback Separation */
 /* 创建表结构 `auto_unfreeze`  */
 DROP TABLE IF EXISTS `auto_unfreeze`;/* MySQLReback Separation */ CREATE TABLE `auto_unfreeze` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `money` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '冻结金额',
  `unfreeze_time` int(11) NOT NULL DEFAULT '0' COMMENT '解冻时间',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `trade_no` varchar(255) NOT NULL DEFAULT '0' COMMENT '冻结资金来源订单号',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '冻结资金记录状态，1：可用，-1：不可用（订单申诉中等情况）',
  PRIMARY KEY (`id`),
  KEY `unfreeze_time` (`unfreeze_time`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='订单金额T+1日自动解冻表';/* MySQLReback Separation */
 /* 插入数据 `auto_unfreeze` */
 INSERT INTO `auto_unfreeze` VALUES ('1','10001','0.090','1539014400','1538990812','T1810081726376105','1');/* MySQLReback Separation */
 /* 创建表结构 `cash`  */
 DROP TABLE IF EXISTS `cash`;/* MySQLReback Separation */ CREATE TABLE `cash` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL COMMENT '收款产品类型 1支付宝 2微信',
  `collect_info` varchar(1024) NOT NULL DEFAULT '' COMMENT '提现信息',
  `money` decimal(10,2) unsigned NOT NULL COMMENT '提现金额',
  `fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `actual_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实际到账',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0审核中 1审核通过 2审核未通过',
  `create_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `complete_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '完成时间',
  `collect_img` tinytext COMMENT '收款二维码',
  `auto_cash` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1 表示自动提现',
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bank_branch` varchar(150) NOT NULL DEFAULT '' COMMENT '开户地址',
  `bank_card` varchar(50) NOT NULL DEFAULT '' COMMENT '银行卡号',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `idcard_number` varchar(50) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `orderid` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `account` int(11) NOT NULL DEFAULT '0' COMMENT '代付账号',
  `daifu_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '代付状态（0，未申请，1，已申请）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `cash_channel`  */
 DROP TABLE IF EXISTS `cash_channel`;/* MySQLReback Separation */ CREATE TABLE `cash_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '代付渠道名',
  `code` varchar(255) NOT NULL COMMENT '代付渠道代码',
  `account_fields` text NOT NULL COMMENT '代付所需的字段',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1支付宝，2微信，3银行',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '渠道状态，0关闭，1开启',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `cash_channel` */
 INSERT INTO `cash_channel` VALUES ('1','千游银行卡代付','qianyoupay','appKey:appKey|screctKey:screctKey','3','1'),('2','千游支付宝代付','QianyouAlipay','appKey:appKey|screctKey:screctKey','1','1');/* MySQLReback Separation */
 /* 创建表结构 `cash_channel_account`  */
 DROP TABLE IF EXISTS `cash_channel_account`;/* MySQLReback Separation */ CREATE TABLE `cash_channel_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(10) unsigned NOT NULL COMMENT '渠道ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '账户名',
  `params` text NOT NULL COMMENT '参数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 1启用 0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `channel`  */
 DROP TABLE IF EXISTS `channel`;/* MySQLReback Separation */ CREATE TABLE `channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '通道ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '通道名称',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '通道代码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 1 开启 0 关闭',
  `mch_id` varchar(50) NOT NULL DEFAULT '' COMMENT '商户号',
  `signkey` varchar(255) NOT NULL DEFAULT '' COMMENT '签名KEY',
  `appid` varchar(50) NOT NULL DEFAULT '' COMMENT 'APPID(账号)',
  `appsecret` varchar(50) NOT NULL DEFAULT '' COMMENT 'APPSECRET(密钥)',
  `gateway` varchar(255) NOT NULL DEFAULT '' COMMENT '网关地址',
  `return_url` varchar(255) NOT NULL DEFAULT '' COMMENT '页面通知（优先级）',
  `notify_url` varchar(255) NOT NULL DEFAULT '' COMMENT '服务器通知（优先级）',
  `lowrate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '充值费率',
  `highrate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '封顶费率',
  `costrate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '成本费率',
  `accounting_date` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '结算时间 1、D0 2、D1 3、T0 4、T1',
  `max_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单笔限额 0为最高不限额',
  `min_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单笔限额',
  `limit_time` varchar(19) NOT NULL DEFAULT '' COMMENT '限时',
  `account_fields` varchar(1024) NOT NULL DEFAULT '' COMMENT '账户字段',
  `polling` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '接口模式 0单独 1轮询',
  `account_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '账号ID',
  `weight` text COMMENT '权重',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `paytype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付类型 1 微信扫码 2 微信公众号 3 支付宝扫码 4 支付宝手机 5 网银支付 6',
  `show_name` varchar(255) NOT NULL DEFAULT '' COMMENT '前台展示名称',
  `is_available` tinyint(4) NOT NULL DEFAULT '0' COMMENT '接口可用 0通用 1手机 2电脑',
  `default_fields` varchar(1024) NOT NULL DEFAULT '' COMMENT '字段默认值',
  `applyurl` varchar(255) NOT NULL DEFAULT '' COMMENT '申请地址',
  `is_install` tinyint(4) NOT NULL DEFAULT '0',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '渠道排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `channel_code_uindex` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8 COMMENT='支付供应商';/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('58','支付宝扫码','AlipayScan','0','','','','','','','','0.0300','0.0000','0.0040','1','0.00','0.00','','alipay_public_key:alipay_public_key|merchant_private_key:merchant_private_key|app_id:app_id|防封域名（可选）:refer','0','4','[]','0','3','','2','charset=UTF-8','https://open.alipay.com/platform/homeRoleSelection.htm','1','0'),('59','支付宝H5','AlipayWap','0','','','','','','','','0.0300','0.0000','0.0040','1','0.00','0.00','','alipay_public_key:alipay_public_key|merchant_private_key:merchant_private_key|app_id:app_id|防封域名（可选）:refer','0','5','[]','0','4','','1','charset=UTF-8','https://open.alipay.com/platform/homeRoleSelection.htm','1','0'),('60','点缀微信扫码','DzWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appsecret:appsecret|防封域名（可选）:refer','0','0','[]','0','1','','0','','','1','0'),('61','点缀支付宝扫码','DzAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appsecret:appsecret|防封域名（可选）:refer','0','0','[]','0','3','','0','','','1','0'),('62','点缀微信公众号','DzWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appsecret:appsecret|是否使用自有公众号（0否，1是）:usepublic|防封域名（可选）:refer','0','0','[]','0','2','','0','','','1','0'),('63','15173Wap微信支付','Lh15173WapPay','0','','','','','','','','0.0100','0.0100','0.0100','1','0.00','0.00','','bargainor_id:bargainor_id|key:key|防封域名（可选）:refer','0','16','[]','0','18','','1','','','1','0'),('64','拉卡微信支付','LkWxPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','userid:userid|userkey:userkey|防封域名（可选）:refer','0','17','[]','0','19','','0','','','1','0'),('65','拉卡微信H5支付','LkWxH5Pay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','userid:userid|userkey:userkey|防封域名（可选）:refer','0','18','[]','0','20','','0','','','1','0'),('66','拉卡支付宝扫码支付','LkAlipayScanPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','userid:userid|userkey:userkey|防封域名（可选）:refer','0','19','[]','0','21','','0','','','1','0'),('67','快接微信扫码支付','KjWxSanPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','merchant_no:merchant_no|appkey:appkey|api_url:api_url|防封域名（可选）:refer','0','20','[]','0','22','','0','','','1','0'),('68','快接微信H5支付','KjWxH5Pay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','merchant_no:merchant_no|appkey:appkey|api_url:api_url|防封域名（可选）:refer','0','21','[]','0','23','','0','','','1','0'),('69','快接支付宝即时到账','KjAlipayScanPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','merchant_no:merchant_no|appkey:appkey|api_url:api_url|防封域名（可选）:refer','0','22','[]','0','24','','0','','','1','0'),('70','快接微信H5支付','KjAlipayH5Pay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','merchant_no:merchant_no|appkey:appkey|api_url:api_url|防封域名（可选）:refer','0','0','[]','0','26','','0','','','1','0'),('71','微信官方H5支付','WxpayH5','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|mch_id:mch_id|signkey:signkey|防封域名（可选）:refer','0','0','[]','0','27','','0','','','1','0'),('72','微信扫码','WxpayScan','0','','','','','','','','0.0300','0.0000','0.0040','1','0.00','0.00','','appid:appid|mch_id:mch_id|signkey:signkey|notify_url:notify_url|防封域名（可选）:refer','0','6','[]','0','1','','0','','','1','0'),('73','12kaQQ钱包扫码','Ka12QqNative','0','','','','','','','','0.0300','0.0500','0.0200','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','30','12kaQQ钱包扫码','1','','','1','0'),('74','12kaQQ钱包wap','Ka12QqWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','31','12kaQQ钱包wap','2','','','1','0'),('75','12ka网银快捷','Ka12QuickBank','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','32','12ka网银快捷','1','','','1','0'),('76','12卡网银快捷WAP','Ka12QuickWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','33','12卡网银快捷WAP','1','','','1','0'),('77','12ka支付宝扫码','Ka12AlipayScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','34','12ka支付宝扫码','2','','','1','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('78','12ka支付宝wap','Ka12AlipayWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','35','12ka支付宝wap','0','','','1','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('79','12ka微信扫码','Ka12WxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','36','12ka微信扫码','2','','','1','0'),('80','12ka微信wap','Ka12WxWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户编号:customerid|api秘钥:apikey|防封域名（可选）:refer','0','0','','0','37','12ka微信wap','2','','','1','0'),('81','15173QQ扫码支付','Lh15173QqPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','bargainor_id:bargainor_id|key:key|防封域名（可选）:refer','0','0','','0','39','15173QQ扫码支付','0','','','1','0'),('82','码支付微信扫码','CodePayWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','码支付ID:id|通信秘钥:key|防封域名（可选）:refer','0','0','','0','38','码支付微信扫码','2','','','1','0'),('83','码支付qq扫码','CodePayQqScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','码支付ID:id|通信秘钥:key|防封域名（可选）:refer','0','0','','0','40','码支付qq扫码','0','','','1','0'),('84','码支付支付宝扫码','CodePayAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','码支付ID:id|通信秘钥:key|防封域名（可选）:refer','0','0','','0','41','码支付支付宝扫码','0','','','1','0'),('85','点缀QQ扫码','DzQqScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appsecret:appsecret|防封域名（可选）:refer','0','0','[]','0','8','点缀支付PC','1','','','1','0'),('86','黔贵金服支付宝扫码','QgjfAlipayScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:mch_id|密钥:key|防封域名（可选）:refer','0','0','[]','0','51','黔贵金服支付宝扫码','2','','','1','0'),('87','黔贵金服微信扫码','QgjfWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:mch_id|密钥:key|防封域名（可选）:refer','0','0','[]','0','52','黔贵金服微信扫码','2','','','1','0'),('88','黔贵金服QQ钱包扫码','QgjfQqNative','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:mch_id|密钥:key|防封域名（可选）:refer','0','0','[]','0','53','黔贵金服QQ钱包扫码','2','','','1','0'),('89','黔贵金服公众号','QgjfWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:mch_id|密钥:key|防封域名（可选）:refer','0','0','[]','0','54','黔贵金服公众号','2','','','1','0'),('90','黔贵金服支付宝Wap','QgjfAlipayWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:mch_id|密钥:key|防封域名（可选）:refer','0','0','[]','0','55','黔贵金服支付宝WAP','2','','','1','0'),('91','黔贵金服微信WAP','QgjfWxWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:mch_id|密钥:key|防封域名（可选）:refer','0','0','[]','0','56','黔贵金服微信WAP','2','','','1','0'),('92','点缀支付宝即时到账','DzAliToPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appsecret:appsecret|防封域名（可选）:refer','0','0','[]','0','57','点缀支付宝即时到账','1','','','1','0'),('93','点缀支付京东钱包扫码','DzJdScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appsecret:appsecret|防封域名（可选）:refer','0','0','[]','0','58','点缀支付京东钱包扫码','2','','','1','0'),('94','掌灵付微信H5','ZlfWxH5','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|防封域名（可选）:refer','0','0','[]','0','59','掌灵付微信H5','0','','','1','0'),('95','掌灵付京东H5','ZlfJdH5','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|防封域名（可选）:refer','0','0','[]','0','60','掌灵付京东H5','0','','','1','0'),('96','掌灵付京东扫码','ZlfJdScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|防封域名（可选）:refer','0','0','[]','0','89','掌灵付京东扫码','2','','','1','0'),('97','掌灵付微信扫码','ZlfWxScan','0','','','','','','','','0.0350','0.0000','0.0100','1','0.00','0.00','','appid:appid|key:key|防封域名（可选）:refer|防封域名（可选）:refer','0','0','[]','0','47','掌灵付微信扫码','2','','','1','0'),('98','掌灵付QQ钱包扫码','ZlfQqScan','0','','','','','','','','0.0350','0.0000','0.0100','1','0.00','0.00','','appid:appid|key:key|防封域名（可选）:refer|防封域名（可选）:refer','0','0','[]','0','48','掌灵付QQ扫码','0','','','1','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('99','QQ钱包扫码（官方）','QqNative','0','','','','','','','','0.0300','0.0000','0.0060','1','0.00','0.00','','mch_id:mch_id|key:key|防封域名（可选）:refer','0','0','[]','0','8','官方QQ扫码','0','','','1','0'),('100','点缀微信H5','DzWxH5','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appsecret:appsecret|防封域名（可选）:refer','0','0','[]','0','61','点缀微信H5','0','','','1','0'),('101','优畅上海微信H5','YcshWxH5','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:appsecret|防封域名（可选）:refer','0','0','[]','0','62','优畅微信H5','0','','','1','0'),('102','优畅上海微信扫码','YcshWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:appsecret|防封域名（可选）:refer','0','0','[]','0','63','优畅上海微信扫码','0','','','1','0'),('103','优畅上海微信公众号','YcshWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:appsecret|微信公众号appid:appid|防封域名（可选）:refer','0','0','[]','0','64','优畅上海微信公众号','0','','','1','0'),('104','海鸟微信公众号','HnPayWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:appid| 商户号:merchant|商户密钥:key|是否使用自有公众号（0否，1是）:publicappid|防封域名（可选）:refer','0','0','[]','0','67','海鸟微信公众号','0','','','1','0'),('105','海鸟微信扫码','HnPayWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:appid|商户号:merchant|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','68','海鸟微信扫码','0','','','1','0'),('106','海鸟微信H5','HnPayWxH5','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:appid|商户号:merchant|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','69','海鸟微信H5','0','','','1','0'),('107','海鸟微信qq扫码','HnPayQqScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:appid|商户号:merchant|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','70','海鸟微信qq扫码','0','','','1','0'),('108','海鸟支付宝扫码','HnPayAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:appid|商户号:merchant|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','71','海鸟支付宝扫码','0','','','1','0'),('109','完美数卡微信扫码','WmskWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:customerid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','78','完美数卡微信扫码','0','','','0','0'),('110','完美数卡支付宝扫码','WmskAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:customerid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','79','完美数卡支付宝扫码','0','','','0','0'),('111','完美数卡QQ扫码','WmskQqScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:customerid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','80','完美数卡QQ扫码','0','','','0','0'),('112','完美数卡QQWap','WmskQqWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:customerid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','81','完美数卡QQWap','0','','','0','0'),('113','完美数卡微信Wap','WmskWxWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:customerid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','82','完美数卡微信Wap','0','','','0','0'),('114','完美数卡支付宝Wap','WmskAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:customerid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','83','完美数卡支付宝Wap','0','','','0','0'),('115','掌灵付支付宝扫码','ZlfAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|防封域名（可选）:refer','0','0','[]','0','87','掌灵付支付宝扫码','0','','','0','0'),('116','QPay支付宝','QPayAli','0','','','','','','','','0.0300','0.0000','0.0040','1','0.00','0.00','','网关:gateway|用户id:uid|token:token|防封域名（可选）:refer','0','7','[]','0','14','','0','','','0','0'),('117','QPay微信','QPayWx','0','','','','','','','','0.0100','0.0000','0.0010','1','0.00','0.00','','网关:gateway|用户id:uid|token:token|防封域名（可选）:refer','0','0','[]','0','15','','0','gateway=https://pay.qpayapi.com','','0','0'),('118','15173微信扫描支付','Lh15173PcPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','bargainor_id:bargainor_id|key:key|防封域名（可选）:refer','0','15','[]','0','17','','0','','','0','0'),('119','蜂鸟支付宝扫码支付','FnAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|secret:secret|merchant_no:merchant_no|防封域名（可选）:refer','0','0','[]','0','42','','2','','','0','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('120','蜂鸟支付宝WAP支付','FnAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|secret:secret|merchant_no:merchant_no|防封域名（可选）:refer','0','0','[]','0','43','','1','','','0','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('121','蜂鸟微信扫码支付','FnWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|secret:secret|merchant_no:merchant_no|sub_appid:sub_appid|防封域名（可选）:refer','0','0','[]','0','44','','2','','','0','0'),('122','蜂鸟微信公众号支付','FnWxJspay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|secret:secret|merchant_no:merchant_no|wx_appid:wx_appid|wx_secret:wx_secret|防封域名（可选）:refer','0','0','[]','0','45','','1','','','0','0'),('123','蜂鸟QQ钱包扫码支付','FnQqScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|secret:secret|merchant_no:merchant_no|防封域名（可选）:refer','0','0','[]','0','46','','2','','','0','0'),('124','优畅上海支付宝扫码','YcshAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:appsecret|防封域名（可选）:refer','0','0','[]','0','90','优畅上海支付宝扫码','0','','','0','0'),('125','优畅上海支付宝Wap','YcshAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:appsecret|防封域名（可选）:refer','0','0','[]','0','91','优畅上海支付宝Wap','0','','','0','0'),('126','汉口银行微信公众号','HkyhWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appkey:appkey|是否使用自有公众号(可选，1是，0否):usePublic|门店（可选）:store|防封域名（可选）:refer','0','0','[]','0','98','','1','','','0','0'),('127','汉口银行微信扫码','HkyhWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appkey:appkey|门店（可选）:store|防封域名（可选）:refer','0','0','[]','0','99','','0','','','0','0'),('128','汉口银行微信wap','HkyhWxWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appkey:appkey|门店（可选）:store|防封域名（可选）:refer','0','0','[]','0','100','','0','','','0','0'),('129','汉口银行支付宝扫码','HkyhAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appkey:appkey|门店（可选）:store|防封域名（可选）:refer','0','0','[]','0','101','','0','','','0','0'),('130','汉口银行支付宝Wap','HkyhAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|appkey:appkey|门店（可选）:store|防封域名（可选）:refer','0','0','[]','0','102','','0','','','0','0'),('131','优畅上海QQ扫码','YcshQqScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:appsecret|防封域名（可选）:refer','0','0','[]','0','103','','0','','','0','0'),('132','优畅上海QQWap','YcshQqWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:appsecret|防封域名（可选）:refer','0','0','[]','0','104','','0','','','0','0'),('133','平安付微信扫码','PafbWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|门店:store|防封域名（可选）:refer','0','0','[]','0','105','','0','','','0','0'),('134','平安付支付宝扫码','PafbAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|门店:store|防封域名（可选）:refer','0','0','[]','0','106','','1','','','0','0'),('135','平安付支付宝wap','PafbAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|门店:store|防封域名（可选）:refer','0','0','[]','0','107','','0','','','0','0'),('136','平安付微信wap','PafbWxWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','appid:appid|key:key|门店:store|防封域名（可选）:refer','0','0','[]','0','108','','0','','','0','0'),('137','网商银行微信','WsyhWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','机构AppId:Appid|机构号:IsvOrgId|浙商私钥:private_key|浙商公钥:public_key|商户号:merchantid|清算方式:settleType|服务商类型（咨询上游，不确定填03）:ProviderType|防封域名（可选）:refer','0','0','[]','0','109','','0','','','0','0'),('138','网商银行支付宝','WsyhAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','机构AppId:Appid|机构号:IsvOrgId|浙商私钥:private_key|浙商公钥:public_key|商户号:merchantid|清算方式:settleType|服务商类型（咨询上游，不确定填03）:ProviderType|防封域名（可选）:refer','0','0','[]','0','110','','0','','','0','0'),('139','牛支付支付宝扫码','NZFAliqrcode','1','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:memberid|appid:appid|app 密钥:key|子商户 id(没有请留空):submchid|防封域名（可选）:refer|防封域名（可选）:refer','0','0','[]','0','88','牛支付支付宝扫码','0','','','1','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('152','吉易支付微信扫码','JyWxPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:key|key:refer|渠道号:channelcode','0','0','[]','0','111','','2','','','0','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('153','吉易支付微信公众号','JyWxGzhPay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:key|key:refer|渠道号:channelcode','0','0','[]','0','112','','1','','','0','0'),('154','PayApi支付宝','PayapiAli','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:uid|秘钥:key','0','0','[]','0','113','','0','','','0','0'),('155','PayApi微信','PayapiWx','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户号:uid|秘钥:key','0','0','[]','0','114','','0','','','0','0'),('156','威富通支付宝扫码','SwiftAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','92','威富通支付宝扫码','0','','','0','0'),('157','威富通支付宝Wap','SwiftAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','93','威富通支付宝Wap','0','','','0','0'),('158','威富通微信扫码','SwiftWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','94','威富通微信扫码','0','','','0','0'),('159','威富通京东扫码','SwiftJd','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','95','威富通京东扫码','0','','','0','0'),('160','威富通微信 Wap','SwiftWxWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','96','威富通微信Wap','0','','','0','0'),('161','威富通微信公众号','SwiftWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','97','威富通微信公众号','0','','','0','0'),('162','易云支付宝扫码','YiyunAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:userid|商户密钥:userkey|防封域名（可选）:refer','0','0','[]','0','115','易云支付宝扫码','0','','','0','0'),('163','易云支付宝WAP','YiyunAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:userid|商户密钥:userkey|防封域名（可选）:refer','0','0','[]','0','116','易云支付宝WAP','0','','','0','0'),('164','易云微信扫码','YiyunWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:userid|商户密钥:userkey|防封域名（可选）:refer','0','0','[]','0','117','易云微信扫码','0','','','0','0'),('165','易云微信WAP','YiyunWxWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:userid|商户密钥:userkey|防封域名（可选）:refer','0','0','[]','0','118','易云微信WAP','0','','','0','0'),('166','易云微信公众号','YiyunWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:userid|商户密钥:userkey|防封域名（可选）:refer','0','0','[]','0','119','易云微信公众号','0','','','0','0'),('167','恒隆支付宝扫码','HenglongAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:memberid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','120','恒隆支付宝扫码','0','','','0','0'),('168','恒隆支付宝WAP','HenglongAliWap','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:memberid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','121','恒隆支付宝WAP','0','','','0','0'),('169','恒隆微信公众号支付','HenglongWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:memberid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','122','恒隆微信公众号支付','0','','','0','0'),('170','恒隆微信扫码','HenglongWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:memberid|商户密钥:key|防封域名（可选）:refer','0','0','[]','0','123','恒隆微信扫码','0','','','0','0'),('171','深度支付宝扫码','ShenduAliScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:md5_key|防封域名（可选）:refer','0','0','[]','0','124','深度支付宝扫码','0','','','0','0'),('172','深度支付宝服务窗支付','ShenduAliJspay','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:md5_key|防封域名（可选）:refer','0','0','[]','0','125','深度支付宝服务窗支付','0','','','0','0'),('173','深度微信扫码','ShenduWxScan','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:md5_key|防封域名（可选）:refer','0','0','[]','0','126','深度微信扫码','0','','','0','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('174','深度微信公众号','ShenduWxGzh','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:mch_id|商户密钥:md5_key|防封域名（可选）:refer','0','0','[]','0','127','深度微信公众号','0','','','0','0');/* MySQLReback Separation */
 /* 插入数据 `channel` */
 INSERT INTO `channel` VALUES ('175','聚合支付','Juhezhifu','0','','','','','','','','0.0000','0.0000','0.0000','1','0.00','0.00','','商户Id:memberid|商户密钥:key|产品编号:bankcode|网关地址:geteway|防封域名（可选）:refer','0','0','[]','0','128','聚合支付','0','','','0','0');/* MySQLReback Separation */
 /* 创建表结构 `channel_account`  */
 DROP TABLE IF EXISTS `channel_account`;/* MySQLReback Separation */ CREATE TABLE `channel_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(10) unsigned NOT NULL COMMENT '渠道ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '账户名',
  `params` text NOT NULL COMMENT '参数',
  `max_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '下线额度',
  `cur_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '当前额度',
  `limit_time` varchar(19) NOT NULL DEFAULT '' COMMENT '限时',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 1启用 0禁用',
  `lowrate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '充值费率',
  `highrate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '封顶费率',
  `costrate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '成本费率',
  `rate_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '费率设置 0 继承接口  1单独设置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `channel_account` */
 INSERT INTO `channel_account` VALUES ('1','139','演示账号','{\"memberid\":\"2\",\"appid\":\"12340004\",\"key\":\"0e0a47c54df24e3015eb78475bfae915\",\"submchid\":\"\",\"refer\":\"\"}','0.00','0.00','','1','0.0000','0.0000','0.0000','0');/* MySQLReback Separation */
 /* 创建表结构 `complaint`  */
 DROP TABLE IF EXISTS `complaint`;/* MySQLReback Separation */ CREATE TABLE `complaint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `trade_no` char(50) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `qq` varchar(15) NOT NULL DEFAULT '',
  `mobile` varchar(15) NOT NULL DEFAULT '',
  `desc` varchar(1000) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0待处理 1已处理',
  `admin_read` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员查看状态',
  `create_at` int(10) unsigned NOT NULL,
  `create_ip` varchar(15) NOT NULL DEFAULT '',
  `pwd` char(10) NOT NULL DEFAULT '123456' COMMENT '投诉单查询密码',
  `result` tinyint(4) NOT NULL DEFAULT '0' COMMENT '申诉结果',
  `expire_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申诉过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `complaint_message`  */
 DROP TABLE IF EXISTS `complaint_message`;/* MySQLReback Separation */ CREATE TABLE `complaint_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trade_no` varchar(255) NOT NULL DEFAULT '0' COMMENT '投诉所属订单',
  `from` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送人，0为管理员发送的消息',
  `content` varchar(1024) NOT NULL DEFAULT '' COMMENT '对话内容',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0未读  1已读',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `content_type` varchar(255) NOT NULL DEFAULT '0' COMMENT '投诉内容类型：0：文本消息，1：图片消息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投诉会话信息';/* MySQLReback Separation */
 /* 创建表结构 `email_code`  */
 DROP TABLE IF EXISTS `email_code`;/* MySQLReback Separation */ CREATE TABLE `email_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `screen` varchar(30) NOT NULL DEFAULT '' COMMENT '场景',
  `code` char(6) NOT NULL DEFAULT '' COMMENT '验证码',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0：未使用 1：已使用',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `goods`  */
 DROP TABLE IF EXISTS `goods`;/* MySQLReback Separation */ CREATE TABLE `goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `cate_id` int(10) unsigned NOT NULL,
  `theme` varchar(15) NOT NULL DEFAULT 'default' COMMENT '主题',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(500) NOT NULL DEFAULT '',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `cost_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `wholesale_discount` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '批发优惠',
  `wholesale_discount_list` text NOT NULL COMMENT '批发价',
  `limit_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '起购数量',
  `inventory_notify` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '库存预警 0表示不报警',
  `inventory_notify_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '库存预警通知方式 1站内信 2邮件',
  `coupon_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券 0不支持 1支持',
  `sold_notify` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '售出通知',
  `take_card_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '取卡密码 0关闭 1必填 2选填',
  `visit_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '访问密码',
  `visit_password` varchar(30) NOT NULL DEFAULT '' COMMENT '访问密码',
  `contact_limit` enum('mobile','email','qq','any','default') NOT NULL DEFAULT 'default',
  `content` varchar(200) NOT NULL DEFAULT '' COMMENT '商品说明',
  `remark` varchar(200) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0下架 1上架',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0',
  `is_freeze` tinyint(4) DEFAULT '0',
  `sms_payer` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '短信付费方：0买家 1商户',
  `delete_at` int(11) DEFAULT NULL COMMENT '删除标记',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `goods` */
 INSERT INTO `goods` VALUES ('1','10001','1','chiji','0','绝地求生卡密','0.10','0.00','0','[]','1','0','1','0','0','0','0','','default','这个自动发卡系统的商品说明','这个自动发卡系统的使用说明','1','1538990072','0','0',''),('2','10001','1','wangzhe','0','王者卡密','0.10','0.00','0','[]','1','0','1','0','0','0','0','','default','这个是自动发卡系统王者卡密的卡密说明','这个是自动发卡系统王者卡密的使用说明','1','1538990308','0','0',''),('3','10001','1','yinyangshi','0','阴阳师卡密','0.10','0.00','0','[]','1','0','1','0','0','0','0','','default','这个是阴阳师卡密商品说明','这个是阴阳师卡密使用说明','1','1538990374','0','0','');/* MySQLReback Separation */
 /* 创建表结构 `goods_card`  */
 DROP TABLE IF EXISTS `goods_card`;/* MySQLReback Separation */ CREATE TABLE `goods_card` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `goods_id` int(10) unsigned NOT NULL,
  `number` varchar(500) NOT NULL DEFAULT '',
  `secret` varchar(500) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '0不可用 1可用 2已使用',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0',
  `delete_at` int(11) DEFAULT NULL COMMENT '删除标记',
  `sell_time` int(11) DEFAULT NULL COMMENT '售出时间',
  PRIMARY KEY (`id`),
  KEY `goods_card_user_id_index` (`user_id`),
  KEY `goods_card_goods_id_index` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `goods_card` */
 INSERT INTO `goods_card` VALUES ('1','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','2','1538990090','','1538990812'),('2','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('3','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('4','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('5','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('6','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('7','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('8','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('9','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('10','10001','1','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990090','',''),('11','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('12','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('13','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('14','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('15','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('16','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('17','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('18','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('19','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('20','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('21','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('22','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('23','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('24','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('25','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('26','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('27','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('28','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('29','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('30','10001','2','AAAAAAAAAAA','BBBBBBBBBBBBAAAAAAAAAAA','1','1538990394','',''),('31','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('32','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('33','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('34','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('35','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('36','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('37','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('38','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('39','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('40','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('41','10001','2','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990394','',''),('42','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('43','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('44','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('45','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('46','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('47','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('48','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('49','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('50','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('51','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('52','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('53','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('54','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('55','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('56','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('57','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('58','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('59','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('60','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('61','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('62','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('63','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('64','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('65','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('66','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('67','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('68','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('69','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('70','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('71','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','','');/* MySQLReback Separation */
 /* 插入数据 `goods_card` */
 INSERT INTO `goods_card` VALUES ('72','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','','');/* MySQLReback Separation */
 /* 插入数据 `goods_card` */
 INSERT INTO `goods_card` VALUES ('73','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('74','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('75','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('76','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('77','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('78','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('79','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('80','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('81','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('82','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('83','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('84','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('85','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('86','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('87','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('88','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('89','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('90','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('91','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('92','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('93','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('94','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('95','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('96','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('97','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('98','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('99','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('100','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('101','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('102','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('103','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('104','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('105','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('106','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('107','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('108','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('109','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('110','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('111','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('112','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','',''),('113','10001','3','AAAAAAAAAAA','BBBBBBBBBBBB','1','1538990414','','');/* MySQLReback Separation */
 /* 创建表结构 `goods_category`  */
 DROP TABLE IF EXISTS `goods_category`;/* MySQLReback Separation */ CREATE TABLE `goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL,
  `create_at` int(10) unsigned NOT NULL,
  `theme` varchar(15) NOT NULL DEFAULT 'default' COMMENT '主题',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `goods_category` */
 INSERT INTO `goods_category` VALUES ('1','10001','游戏卡密','0','1','1538989994','default');/* MySQLReback Separation */
 /* 创建表结构 `goods_coupon`  */
 DROP TABLE IF EXISTS `goods_coupon`;/* MySQLReback Separation */ CREATE TABLE `goods_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '全部',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型 1、元  100、%',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `code` varchar(255) NOT NULL DEFAULT '',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1未使用 2已用',
  `expire_at` int(10) unsigned NOT NULL,
  `create_at` int(10) unsigned NOT NULL,
  `delete_at` int(11) DEFAULT NULL COMMENT '删除标记',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `invite_code`  */
 DROP TABLE IF EXISTS `invite_code`;/* MySQLReback Separation */ CREATE TABLE `invite_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '所有者ID',
  `code` char(32) NOT NULL DEFAULT '' COMMENT '邀请码',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '使用状态 0未使用 1已使用',
  `invite_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '受邀用户ID',
  `invite_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '邀请时间',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `expire_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `link`  */
 DROP TABLE IF EXISTS `link`;/* MySQLReback Separation */ CREATE TABLE `link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `relation_type` varchar(20) NOT NULL DEFAULT '',
  `relation_id` int(10) unsigned NOT NULL DEFAULT '0',
  `token` char(16) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `short_url` varchar(30) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `create_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`relation_type`,`relation_id`),
  UNIQUE KEY `token_uindex` (`token`),
  UNIQUE KEY `user_link_index` (`relation_id`,`relation_type`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `link` */
 INSERT INTO `link` VALUES ('2','10001','goods','1','4A9BAC76','','http://t.cn/EhdF9gu','1','1538990191'),('3','10001','goods','2','9512A536','','http://t.cn/EhdsVgM','1','1538990308'),('4','10001','goods','3','410BB69A','','http://t.cn/EhdsnoQ','1','1538990374'),('5','10001','user','10001','3320B5AF','','http://t.cn/EhgZPLY','1','1538991202');/* MySQLReback Separation */
 /* 创建表结构 `log`  */
 DROP TABLE IF EXISTS `log`;/* MySQLReback Separation */ CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `business_type` varchar(20) NOT NULL DEFAULT '' COMMENT '业务类型',
  `content` text NOT NULL COMMENT '内容',
  `ua` varchar(255) NOT NULL DEFAULT '',
  `uri` varchar(255) NOT NULL DEFAULT '',
  `create_at` int(10) unsigned NOT NULL COMMENT '记录时间',
  `create_ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'ip',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `merchant_log`  */
 DROP TABLE IF EXISTS `merchant_log`;/* MySQLReback Separation */ CREATE TABLE `merchant_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作者IP地址',
  `node` char(200) NOT NULL DEFAULT '' COMMENT '当前操作节点',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人用户ID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `action` varchar(200) NOT NULL DEFAULT '' COMMENT '操作行为',
  `content` text NOT NULL COMMENT '操作内容描述',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='系统操作日志表';/* MySQLReback Separation */
 /* 插入数据 `merchant_log` */
 INSERT INTO `merchant_log` VALUES ('1','61.242.114.84','merchant/goodscategory/add','10001','demo','添加商品分类成功','添加商品分类成功，ID:1，名称:游戏卡密','2018-10-08 17:13:15'),('2','61.242.114.84','merchant/goods/add','10001','demo','添加商品成功','添加商品成功，商品ID:1,名称:绝地求生卡密,价格:0.1,成本价:0','2018-10-08 17:14:33'),('3','61.242.114.84','merchant/goodscard/add','10001','demo','成功添加卡密','成功添加10张卡密','2018-10-08 17:14:50'),('4','61.242.114.84','merchant/goods/add','10001','demo','添加商品成功','添加商品成功，商品ID:2,名称:王者卡密,价格:0.1,成本价:0','2018-10-08 17:18:29'),('5','61.242.114.84','merchant/goods/add','10001','demo','添加商品成功','添加商品成功，商品ID:3,名称:阴阳师卡密,价格:0.1,成本价:0','2018-10-08 17:19:34'),('6','61.242.114.84','merchant/goodscard/add','10001','demo','成功添加卡密','成功添加31张卡密','2018-10-08 17:19:54'),('7','61.242.114.84','merchant/goodscard/add','10001','demo','成功添加卡密','成功添加72张卡密','2018-10-08 17:20:15'),('8','61.242.114.84','index/user/dologin','10001','demo','登录成功','登录成功','2018-10-08 17:22:23');/* MySQLReback Separation */
 /* 创建表结构 `message`  */
 DROP TABLE IF EXISTS `message`;/* MySQLReback Separation */ CREATE TABLE `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0为管理员',
  `to_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(60) NOT NULL DEFAULT '',
  `content` varchar(1024) NOT NULL DEFAULT '',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0未读  1已读',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `migrations`  */
 DROP TABLE IF EXISTS `migrations`;/* MySQLReback Separation */ CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `migration` (`migration`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;/* MySQLReback Separation */
 /* 插入数据 `migrations` */
 INSERT INTO `migrations` VALUES ('53','20180428_alter_migrations_table.sql','0'),('54','20180428_create_foo_table.sql','0'),('55','20180428_drop_foo_table.sql','0'),('56','20180502_create_foo_table.sql','1'),('57','20180502_drop_foo_table.sql','2'),('58','20180507_alter_table.sql','3'),('59','20180509_alter_order_table_goods_table.sql','3'),('60','20180512_alter_user_money_log_table.sql','4'),('61','20180512_alter_user_table.sql','4'),('62','20180513_add_auto_unfreeze_table.sql','4'),('63','20180513_alter_user_table.sql','5'),('64','20180513alter_goods_card_table.sql','5'),('65','20180515_alter_auto_unfreeze_table.sql','5'),('66','20180523alter_order_card_table.sql','6');/* MySQLReback Separation */
 /* 创建表结构 `nav`  */
 DROP TABLE IF EXISTS `nav`;/* MySQLReback Separation */ CREATE TABLE `nav` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `node` varchar(200) NOT NULL DEFAULT '' COMMENT '节点代码',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url` varchar(400) NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(500) DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '菜单排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `create_by` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_system_menu_node` (`node`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='前台导航表';/* MySQLReback Separation */
 /* 插入数据 `nav` */
 INSERT INTO `nav` VALUES ('1','0','网站首页','','','/','','0','10','1','0','2018-03-23 09:20:50'),('2','0','常见问题','','','/company/faq','','0','3','1','0','2018-03-23 09:21:11'),('3','0','联系我们','','','/company/contact','','0','4','1','0','2018-03-23 09:21:35'),('4','0','订单查询','','','/orderquery','','0','0','1','0','2018-03-23 09:22:09'),('5','0','企业资质','','','/article/14.html','','1','0','1','0','2018-03-23 11:28:55'),('6','0','用户注册','','','/register','','1','0','1','0','2018-05-10 12:24:37'),('7','0','投诉查询','','','/complaintquery','','_self','0','1','0','2018-06-21 10:53:00');/* MySQLReback Separation */
 /* 创建表结构 `order`  */
 DROP TABLE IF EXISTS `order`;/* MySQLReback Separation */ CREATE TABLE `order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `goods_id` int(10) unsigned NOT NULL,
  `trade_no` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(60) NOT NULL DEFAULT '' COMMENT '流水号',
  `paytype` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `channel_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付渠道',
  `channel_account_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '渠道账号',
  `pay_url` varchar(10240) NOT NULL DEFAULT '' COMMENT '付款地址',
  `pay_content_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付参数类型 1：二维码 2：跳转链接 3：表单',
  `goods_name` varchar(500) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品单价',
  `goods_cost_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `coupon_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用优惠券',
  `coupon_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券ID',
  `coupon_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '优惠价格',
  `total_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总价（买家实付款）',
  `total_cost_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总成本价',
  `sold_notify` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '售出通知（买家）',
  `take_card_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要取卡密码',
  `take_card_password` varchar(20) NOT NULL DEFAULT '' COMMENT '取卡密码',
  `contact` varchar(20) NOT NULL DEFAULT '' COMMENT '联系方式',
  `email_notify` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否邮件通知',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮箱号',
  `sms_notify` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否短信通知',
  `rate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '手续费率',
  `fee` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '手续费',
  `agent_rate` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '代理费率',
  `agent_fee` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '代理佣金',
  `sms_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '订单状态 0未支付 1已支付 2已关闭',
  `is_freeze` tinyint(4) NOT NULL DEFAULT '0',
  `create_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `create_ip` varchar(15) NOT NULL DEFAULT '',
  `success_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付成功时间',
  `first_query` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单第一次查询无需验证码',
  `sms_payer` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '短信付费方：0买家 1商户',
  `total_product_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总价（不含短信费）',
  `sendout` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已发货数量',
  `fee_payer` tinyint(4) NOT NULL DEFAULT '1' COMMENT '订单手续费支付方，1：商家承担，2买家承担',
  `settlement_type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '结算方式，1:T1结算，0:T0结算',
  `finally_money` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '商户订单最终收入（已扣除短信费，手续费）',
  PRIMARY KEY (`id`),
  KEY `order_create_at_index` (`create_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `order` */
 INSERT INTO `order` VALUES ('1','10001','1','T1810081726376105','','88','139','1','<form id=\'pay_form\' class=\"form-inline\" method=\"post\" action=\"https://api.tianniu.cc/pay/index\"><input type=\"hidden\" name=\"pay_memberid\" value=\"2\"><input type=\"hidden\" name=\"pay_appid\" value=\"12340004\"><input type=\"hidden\" name=\"pay_submchid\" value=\"0\"><input type=\"hidden\" name=\"pay_orderid\" value=\"T1810081726376105\"><input type=\"hidden\" name=\"pay_applydate\" value=\"2018-10-08 17:26:37\"><input type=\"hidden\" name=\"pay_bankcode\" value=\"Aliqrcode\"><input type=\"hidden\" name=\"pay_notifyurl\" value=\"https://ka.tianniu.cc/pay/notify/NZFAliqrcode\"><input type=\"hidden\" name=\"pay_callbackurl\" value=\"https://ka.tianniu.cc/pay/page/NZFAliqrcode\"><input type=\"hidden\" name=\"pay_amount\" value=\"0.10\"><input type=\"hidden\" name=\"pay_md5sign\" value=\"663739E9035E4E5C33B21C3DE38807DF\"><input type=\"hidden\" name=\"pay_productname\" value=\"投诉QQ：12344321 订单：T1810081726376105\"><input type=\"hidden\" name=\"pay_productdesc\" value=\"投诉QQ：12344321 订单：T1810081726376105\"></form><script>document.forms[\'pay_form\'].submit();</script>','3','绝地求生卡密','0.10','0.00','1','0','0','0.00','0.10','0.00','0','0','','18620528362','0','','0','0.0000','0.010','0.0000','0.000','0.00','1','0','1538990796','61.242.114.84','1538990812','1','0','0.10','1','1','1','0.0000');/* MySQLReback Separation */
 /* 创建表结构 `order_card`  */
 DROP TABLE IF EXISTS `order_card`;/* MySQLReback Separation */ CREATE TABLE `order_card` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `number` varchar(500) NOT NULL DEFAULT '',
  `secret` varchar(500) NOT NULL DEFAULT '',
  `card_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_card_order_id_index` (`order_id`),
  KEY `order_card_card_id_index` (`card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `order_card` */
 INSERT INTO `order_card` VALUES ('1','1','AAAAAAAAAAA','BBBBBBBBBBBB','1');/* MySQLReback Separation */
 /* 创建表结构 `pay_type`  */
 DROP TABLE IF EXISTS `pay_type`;/* MySQLReback Separation */ CREATE TABLE `pay_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '支付名',
  `product_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '支付类型',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT 'logo',
  `ico` varchar(100) NOT NULL DEFAULT '' COMMENT '图标',
  `is_mobile` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否手机支付',
  `is_form_data` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否 form 提交',
  `target` tinyint(3) NOT NULL DEFAULT '0' COMMENT '银行支付使用，未知作用',
  `sub_lists` text COMMENT '银行支付使用，指定支持的银行列表',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8 COMMENT='支付类型表';/* MySQLReback Separation */
 /* 插入数据 `pay_type` */
 INSERT INTO `pay_type` VALUES ('1','微信扫码','2','icon_wx','icon_wx','0','0','0',''),('2','微信H5','2','icon_wx','icon_wx','1','0','0',''),('3','支付宝扫码','1','icon_zfb','icon_zfb','0','1','0',''),('4','支付宝H5','1','icon_zfb','icon_zfb','1','0','0',''),('5','网银跳转','6','icon_bank','icon_bank','0','0','0',''),('6','网银直连','6','icon_bank','icon_bank','0','0','0',''),('7','百度钱包','5','baidu_logo','baidu_ico','0','0','0',''),('8','QQ钱包扫码','3','qqrcode','qqrcode','0','0','0',''),('9','京东钱包','4','jd_logo','jd_ico','0','0','0',''),('10','QQ钱包H5','3','icon_qq','icon_qq','1','0','0',''),('11','支付宝PC','1','icon_zfb','icon_zfb','0','0','0',''),('12','微信刷卡','2','icon_wx','icon_wx','0','0','0',''),('13','支付宝刷卡','1','icon_zfb','icon_zfb','0','0','0',''),('14','支付宝免签','1','icon_zfb','icon_zfb','0','0','0',''),('15','微信免签','2','icon_wx','icon_wx','0','0','0',''),('16','微极速微信支付','2','icon_wx','icon_wx','1','1','0',''),('17','15173PC微信支付','2','icon_wx','icon_wx','0','0','0',''),('18','15173Wap微信支付','2','icon_wx','icon_wx','1','0','0',''),('19','拉卡微信扫码支付','2','icon_wx','icon_wx','0','0','0',''),('20','拉卡微信h5支付','2','icon_wx','icon_wx','1','0','0',''),('21','拉卡支付宝扫码支付','1','icon_zfb','icon_zfb','0','0','0',''),('22','快接微信扫码支付','2','icon_wx','icon_wx','0','0','0',''),('23','快接微信H5支付','2','icon_wx','icon_wx','1','0','0',''),('24','快接支付宝扫码支付','1','icon_zfb','icon_zfb','0','0','0',''),('25','快接支付宝扫码支付(线上二维码)','1','icon_zfb','icon_zfb','0','0','0',''),('26','快接支付宝H5支付','1','icon_zfb','icon_zfb','1','0','0',''),('27','微信官方H5支付','2','icon_wx','icon_wx','1','0','0',''),('28','拉卡QQ支付','3','icon_qq','icon_qq','0','0','0',''),('29','拉卡银联快捷','6','icon_bank','icon_bank','0','0','0','{\"1\":{\"name\":\"\\u4e2d\\u56fd\\u5de5\\u5546\\u94f6\\u884c\",\"logo\":\"icon_bank\",\"ico\":\"icon_bank\",\"is_mobile\":0,\"target\":0,\"is_form_data\":0,\"code\":10001},\"2\":{\"name\":\"\\u4e2d\\u56fd\\u519c\\u4e1a\\u94f6\\u884c\",\"logo\":\"icon_zgnyyh\",\"ico\":\"icon_zgnyyh\",\"is_mobile\":0,\"target\":0,\"is_form_data\":0,\"code\":10002}}'),('30','12kaQQ钱包扫码','3','icon_qq','icon_qq','0','0','0',''),('31','12kaQQWap','3','icon_qq','icon_qq','0','0','0',''),('32','12ka网银快捷','6','icon_bank','icon_bank','0','0','0',''),('33','12ka网银Wap','6','icon_bank','icon_bank','0','0','0',''),('34','12ka支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('35','12ka支付宝wap','1','icon_zfb','icon_zfb','0','0','0',''),('36','12ka微信扫码','2','icon_wx','icon_wx','0','0','0',''),('37','12ka微信wap','2','icon_wx','icon_wx','0','0','0',''),('38','码支付微信扫码','2','icon_wx','icon_wx','0','0','0',''),('39','15173Qq扫码支付','3','icon_qq','icon_qq','0','0','0',''),('40','码支付QQ扫码','3','icon_qq','icon_qq','0','0','0',''),('41','码支付支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('42','蜂鸟支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('43','蜂鸟支付宝WAP','1','icon_zfb','icon_zfb','0','0','0',''),('44','蜂鸟微信扫码','2','icon_wx','icon_wx','0','0','0',''),('45','蜂鸟微信公众号支付','2','icon_wx','icon_wx','0','0','0',''),('46','蜂鸟QQ钱包扫码','3','icon_qq','icon_qq','0','0','0',''),('47','掌灵付微信扫码','2','icon_wx','icon_wx','0','1','0',''),('48','掌灵付QQ扫码','3','qqrcode','qqrcode','0','1','0',''),('49','掌灵付支付宝扫码','1','icon_zfb','icon_zfb','0','1','0',''),('50','掌灵付微信公众号支付','2','icon_wx','icon_wx','0','1','0',''),('51','黔贵金服支付宝扫码','1','icon_zfb','icon_zfb','0','1','0',''),('52','黔贵金服微信扫码','2','icon_wx','icon_wx','0','1','0',''),('53','黔贵金服QQ扫码','3','qqrcode','qqrcode','0','1','0',''),('54','黔贵金服微信公众号支付','2','icon_wx','icon_wx','0','1','0',''),('55','黔贵金服支付宝Wap','1','icon_zfb','icon_zfb','0','1','0',''),('56','黔贵金服微信WAP','2','icon_wx','icon_wx','0','1','0',''),('57','点缀支付宝即时到账','1','icon_zfb','icon_zfb','0','0','0',''),('58','点缀支付京东钱包扫码','4','icon_jd','icon_jd','0','0','0',''),('59','掌灵付微信H5','2','icon_wx','icon_wx','0','0','0',''),('60','掌灵付京东H5','4','icon_jd','icon_jd','0','0','0',''),('61','点缀微信 H5','2','icon_wx','icon_wx','0','0','0',''),('62','优畅上海微信 H5','2','icon_wx','icon_wx','0','0','0',''),('63','优畅上海微信扫码','2','icon_wx','icon_wx','0','0','0',''),('64','优畅上海微信公众号','2','icon_wx','icon_wx','0','0','0',''),('65','Topay微信','2','icon_wx','icon_wx','0','0','0',''),('66','Topay 支付宝','1','icon_zfb','icon_zfb','0','0','0',''),('67','海鸟微信公众号','2','icon_wx','icon_wx','0','0','0',''),('68','海鸟微信扫码','2','icon_wx','icon_wx','0','0','0',''),('69','海鸟微信 H5','2','icon_wx','icon_wx','0','0','0',''),('70','海鸟 QQ 扫码','3','icon_qq','icon_qq','0','0','0','');/* MySQLReback Separation */
 /* 插入数据 `pay_type` */
 INSERT INTO `pay_type` VALUES ('71','海鸟支付宝扫码','1','icon_zfb','icon_zfb','0','0','0','');/* MySQLReback Separation */
 /* 插入数据 `pay_type` */
 INSERT INTO `pay_type` VALUES ('72','淘米支付宝扫码','1','icon_zfb','icon_zfb','0','1','0',''),('73','淘米微信扫码','2','icon_wx','icon_wx','0','1','0',''),('74','淘米QQ扫码','3','qqrcode','qqrcode','0','1','0',''),('75','淘米微信公众号支付','2','icon_wx','icon_wx','0','1','0',''),('76','淘米支付宝Wap','1','icon_zfb','icon_zfb','0','1','0',''),('77','淘米微信WAP','2','icon_wx','icon_wx','0','1','0',''),('78','完美数卡微信扫码','2','icon_wx','icon_wx','0','1','0',''),('79','完美数卡支付宝扫码','1','icon_zfb','icon_zfb','0','1','0',''),('80','完美数卡QQ扫码','3','icon_qq','icon_qq','0','1','0',''),('81','完美数卡QQWap','3','icon_qq','icon_qq','0','1','0',''),('82','完美数卡微信Wap','2','icon_wx','icon_wx','0','1','0',''),('83','完美数卡支付宝Wap','1','icon_zfb','icon_zfb','0','1','0',''),('84','支付宝免签','1','icon_zfb','icon_zfb','0','1','0',''),('85','qq免签','3','icon_qq','icon_qq','0','1','0',''),('86','微信免签','2','icon_wx','icon_wx','0','1','0',''),('87','掌灵付支付宝扫码','1','icon_zfb','icon_zfb','0','1','0',''),('88','牛支付支付宝扫码','1','icon_zfb','icon_zfb','0','1','0',''),('89','掌灵付京东扫码','4','icon_jd','icon_jd','0','0','0',''),('90','优畅上海支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('91','优畅上海支付宝 Wap','1','icon_zfb','icon_zfb','0','0','0',''),('92','威富通支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('93','威富通支付宝Wap','1','icon_zfb','icon_zfb','0','0','0',''),('94','威富通微信扫码','2','icon_wx','icon_wx','0','0','0',''),('95','威富通京东扫码','4','icon_jd','icon_jd','0','0','0',''),('96','威富通微信wap','2','icon_wx','icon_wx','0','0','0',''),('97','威富通微信公众号','2','icon_wx','icon_wx','0','0','0',''),('98','汉口银行微信公众号','2','icon_wx','icon_wx','0','0','0',''),('99','汉口银行微信扫码','2','icon_wx','icon_wx','0','0','0',''),('100','汉口银行微信wap','2','icon_wx','icon_wx','0','0','0',''),('101','汉口银行支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('102','汉口银行支付宝Wap','1','icon_zfb','icon_zfb','0','0','0',''),('103','优畅上海QQ扫码','3','icon_qq','icon_qq','0','0','0',''),('104','优畅上海QQWap','3','icon_qq','icon_qq','0','0','0',''),('105','平安付微信扫码','2','icon_wx','icon_wx','0','0','0',''),('106','平安付支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('107','平安付支付宝wap','1','icon_zfb','icon_zfb','0','0','0',''),('108','平安付微信wap','2','icon_wx','icon_wx','0','0','0',''),('109','网商银行微信','2','icon_wx','icon_wx','0','0','0',''),('110','网商银行支付宝','1','icon_zfb','icon_zfb','0','0','0',''),('111','吉易支付微信扫码','2','icon_wx','icon_wx','0','0','0',''),('112','吉易支付微信公众号','2','icon_wx','icon_wx','0','0','0',''),('113','PayApi支付宝','2','icon_zfb','icon_zfb','0','0','0',''),('114','PayApi微信','2','icon_wx','icon_wx','0','0','0',''),('115','易云支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('116','易云支付宝WAP','1','icon_zfb','icon_zfb','0','0','0',''),('117','易云微信扫码','2','icon_wx','icon_wx','0','0','0',''),('118','易云微信WAP','2','icon_wx','icon_wx','0','0','0',''),('119','易云微信公众号','2','icon_wx','icon_wx','0','0','0',''),('120','恒隆支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('121','恒隆支付宝WAP','1','icon_zfb','icon_zfb','0','0','0',''),('122','恒隆微信公众号支付','2','icon_wx','icon_wx','0','0','0',''),('123','恒隆微信扫码','2','icon_wx','icon_wx','0','0','0',''),('124','深度支付宝扫码','1','icon_zfb','icon_zfb','0','0','0',''),('125','深度支付宝服务窗支付','1','icon_zfb','icon_zfb','0','0','0',''),('126','深度微信扫码','2','icon_wx','icon_wx','0','0','0',''),('127','深度微信公众号','2','icon_wx','icon_wx','0','0','0',''),('128','聚合支付','1','icon_zfb','icon_zfb','0','0','0','');/* MySQLReback Separation */
 /* 创建表结构 `product`  */
 DROP TABLE IF EXISTS `product`;/* MySQLReback Separation */ CREATE TABLE `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '通道名称',
  `code` varchar(50) NOT NULL COMMENT '通道代码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 1 开启 0 关闭',
  `polling` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '接口模式 0单独 1轮询',
  `channel_id` int(10) unsigned NOT NULL,
  `weight` text NOT NULL COMMENT '权重',
  `paytype` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付类型 1 微信扫码 2 微信公众号 3 支付宝扫码 4 支付宝手机 5 网银支付 6',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=908 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `product` */
 INSERT INTO `product` VALUES ('901','微信公众号','WXJSAPI','0','0','0','[]','2'),('902','微信扫码支付','WXSCAN','0','0','0','[]','1'),('903','支付宝扫码支付','ALISCAN','0','0','14','[]','3'),('904','支付宝手机','ALIWAP','0','0','15','[]','4'),('907','网银支付','DBANK','0','0','0','[]','5');/* MySQLReback Separation */
 /* 创建表结构 `rate_group`  */
 DROP TABLE IF EXISTS `rate_group`;/* MySQLReback Separation */ CREATE TABLE `rate_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '分组名',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `delete_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='费率分组表';/* MySQLReback Separation */
 /* 创建表结构 `rate_group_rule`  */
 DROP TABLE IF EXISTS `rate_group_rule`;/* MySQLReback Separation */ CREATE TABLE `rate_group_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL COMMENT '分组 ID',
  `channel_id` int(11) NOT NULL COMMENT '渠道 ID',
  `rate` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '渠道费率',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1：开启 0：关闭',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分组费率规则';/* MySQLReback Separation */
 /* 创建表结构 `rate_group_user`  */
 DROP TABLE IF EXISTS `rate_group_user`;/* MySQLReback Separation */ CREATE TABLE `rate_group_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL COMMENT '分组 ID',
  `user_id` int(11) NOT NULL COMMENT '用户 ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分组内用户表';/* MySQLReback Separation */
 /* 创建表结构 `sms_code`  */
 DROP TABLE IF EXISTS `sms_code`;/* MySQLReback Separation */ CREATE TABLE `sms_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号',
  `screen` varchar(30) NOT NULL DEFAULT '' COMMENT '场景',
  `code` char(6) NOT NULL DEFAULT '' COMMENT '验证码',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0：未使用 1：已使用',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '请求 ip',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `system_auth`  */
 DROP TABLE IF EXISTS `system_auth`;/* MySQLReback Separation */ CREATE TABLE `system_auth` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(1:禁用,2:启用)',
  `sort` smallint(6) unsigned DEFAULT '0' COMMENT '排序权重',
  `desc` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_by` bigint(11) unsigned DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_auth_title` (`title`) USING BTREE,
  KEY `index_system_auth_status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='系统权限表';/* MySQLReback Separation */
 /* 插入数据 `system_auth` */
 INSERT INTO `system_auth` VALUES ('3','超级管理员','1','0','超级管理员','0','2018-04-11 03:24:49');/* MySQLReback Separation */
 /* 创建表结构 `system_auth_node`  */
 DROP TABLE IF EXISTS `system_auth_node`;/* MySQLReback Separation */ CREATE TABLE `system_auth_node` (
  `auth` bigint(20) unsigned DEFAULT NULL COMMENT '角色ID',
  `node` varchar(200) DEFAULT NULL COMMENT '节点路径',
  KEY `index_system_auth_auth` (`auth`) USING BTREE,
  KEY `index_system_auth_node` (`node`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色与节点关系表';/* MySQLReback Separation */
 /* 插入数据 `system_auth_node` */
 INSERT INTO `system_auth_node` VALUES ('3','admin'),('3','admin/config'),('3','admin/config/index'),('3','admin/config/file'),('3','admin/log'),('3','admin/log/index'),('3','admin/log/del'),('3','admin/menu'),('3','admin/menu/index'),('3','admin/menu/add'),('3','admin/menu/edit'),('3','admin/menu/del'),('3','admin/menu/forbid'),('3','admin/menu/resume'),('3','admin/mlog'),('3','admin/mlog/index'),('3','admin/mlog/del'),('3','admin/nav'),('3','admin/nav/index'),('3','admin/nav/add'),('3','admin/nav/edit'),('3','admin/nav/del'),('3','admin/nav/forbid'),('3','admin/nav/resume'),('3','admin/user'),('3','admin/user/index'),('3','admin/user/auth'),('3','admin/user/add'),('3','admin/user/edit'),('3','admin/user/del'),('3','admin/user/forbid'),('3','admin/user/resume'),('3','manage'),('3','manage/article'),('3','manage/article/index'),('3','manage/article/add'),('3','manage/article/edit'),('3','manage/article/change_status'),('3','manage/article/del'),('3','manage/articlecategory'),('3','manage/articlecategory/index'),('3','manage/articlecategory/add'),('3','manage/articlecategory/edit'),('3','manage/articlecategory/change_status'),('3','manage/articlecategory/del'),('3','manage/backup'),('3','manage/backup/index'),('3','manage/backup/tablist'),('3','manage/backup/backall'),('3','manage/backup/backtables'),('3','manage/backup/recover'),('3','manage/backup/deletebak'),('3','manage/backup/downloadbak'),('3','manage/cash'),('3','manage/cash/index'),('3','manage/cash/config'),('3','manage/cash/detail'),('3','manage/channel'),('3','manage/channel/index'),('3','manage/channel/add'),('3','manage/channel/edit'),('3','manage/channel/del'),('3','manage/channel/change_status'),('3','manage/channel/getlistbypaytype'),('3','manage/channelaccount'),('3','manage/channelaccount/index'),('3','manage/channelaccount/add'),('3','manage/channelaccount/edit'),('3','manage/channelaccount/del'),('3','manage/channelaccount/clear'),('3','manage/channelaccount/change_status'),('3','manage/channelaccount/getfields'),('3','manage/complaint'),('3','manage/complaint/index'),('3','manage/complaint/change_status'),('3','manage/complaint/change_admin_read'),('3','manage/complaint/del'),('3','manage/email'),('3','manage/email/index'),('3','manage/email/test'),('3','manage/goods'),('3','manage/goods/index'),('3','manage/goods/change_status'),('3','manage/index'),('3','manage/index/main'),('3','manage/invitecode'),('3','manage/invitecode/index'),('3','manage/invitecode/add'),('3','manage/invitecode/del'),('3','manage/invitecode/delnum'),('3','manage/log'),('3','manage/log/user_money'),('3','manage/order'),('3','manage/order/index'),('3','manage/order/detail'),('3','manage/order/merchant'),('3','manage/order/channel'),('3','manage/order/hour'),('3','manage/order/change_freeze_status'),('3','manage/order/del'),('3','manage/order/del_batch'),('3','manage/product'),('3','manage/product/index'),('3','manage/product/add'),('3','manage/product/edit'),('3','manage/product/del'),('3','manage/product/change_status'),('3','manage/site'),('3','manage/site/info'),('3','manage/site/domain'),('3','manage/site/register'),('3','manage/site/wordfilter'),('3','manage/site/spread'),('3','manage/sms'),('3','manage/sms/index'),('3','manage/user'),('3','manage/user/index'),('3','manage/user/change_status'),('3','manage/user/change_freeze_status'),('3','manage/user/detail'),('3','manage/user/add'),('3','manage/user/edit'),('3','manage/user/del'),('3','manage/user/manage_money'),('3','manage/user/rate'),('3','manage/user/login'),('3','manage/user/message'),('3','manage/user/loginlog'),('3','manage/user/unlock'),('3','wechat'),('3','wechat/config'),('3','wechat/config/index'),('3','wechat/config/pay'),('3','wechat/fans'),('3','wechat/fans/index'),('3','wechat/fans/back'),('3','wechat/fans/backadd'),('3','wechat/fans/tagset'),('3','wechat/fans/backdel'),('3','wechat/fans/tagadd'),('3','wechat/fans/tagdel'),('3','wechat/fans/sync'),('3','wechat/keys'),('3','wechat/keys/index'),('3','wechat/keys/add'),('3','wechat/keys/edit'),('3','wechat/keys/del'),('3','wechat/keys/forbid'),('3','wechat/keys/resume'),('3','wechat/keys/subscribe'),('3','wechat/keys/defaults'),('3','wechat/menu'),('3','wechat/menu/index'),('3','wechat/menu/edit'),('3','wechat/menu/cancel'),('3','wechat/news'),('3','wechat/news/index'),('3','wechat/news/select'),('3','wechat/news/image'),('3','wechat/news/add'),('3','wechat/news/edit'),('3','wechat/news/del'),('3','wechat/news/push'),('3','wechat/tags'),('3','wechat/tags/index'),('3','wechat/tags/add'),('3','wechat/tags/edit'),('3','wechat/tags/sync'),('3','admin/node'),('3','admin/node/save'),('3','admin/node/save'),('3','admin/auth'),('3','admin/auth/index'),('3','admin/auth/apply'),('3','admin/auth/add'),('3','admin/auth/edit'),('3','admin/auth/forbid'),('3','admin/auth/resume'),('3','admin/auth/del'),('3','admin/auth/bindgoogle'),('3','admin/node'),('3','admin/node/save'),('3','admin/node/index');/* MySQLReback Separation */
 /* 创建表结构 `system_config`  */
 DROP TABLE IF EXISTS `system_config`;/* MySQLReback Separation */ CREATE TABLE `system_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '配置编码',
  `value` varchar(1024) DEFAULT NULL COMMENT '配置值',
  PRIMARY KEY (`id`),
  KEY `index_system_config_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统参数配置';/* MySQLReback Separation */
 /* 插入数据 `system_config` */
 INSERT INTO `system_config` VALUES ('148','site_name','自动发卡平台'),('149','site_copy','自动发卡平台 © 2014~2018 版权'),('164','storage_type','local'),('165','storage_qiniu_is_https','1'),('166','storage_qiniu_bucket','static'),('167','storage_qiniu_domain','static.ctolog.com'),('168','storage_qiniu_access_key','OAFHGzCgZjod2-s4xr-g5ptkXsNbxDO_t2fozIEC'),('169','storage_qiniu_secret_key','gy0aYdSFMSayQ4kMkgUeEeJRLThVjLpUJoPFxd-Z'),('170','storage_qiniu_region','华东'),('173','app_name','自动发卡平台'),('174','app_version','2.00 dev'),('176','browser_icon','/static/img/c23d983c0e1a7682.ico'),('184','wechat_appid','wxbd2715b339faa4b6'),('185','wechat_appsecret','7e8055384f9c4ff5991c46cacd336ad9'),('186','wechat_token','mytoken'),('187','wechat_encodingaeskey','KHyoWLoS7oLZYkB4PokMTfA5sm6Hrqc9Tzgn9iXc0YH'),('188','wechat_mch_id','1332187001'),('189','wechat_partnerkey','A82DC5BD1F3359081049C568D8502BC5'),('194','wechat_cert_key',''),('196','wechat_cert_cert',''),('197','tongji_baidu_key',''),('198','tongji_cnzz_key','1261854404'),('199','storage_oss_bucket','think-oss'),('200','storage_oss_keyid','WjeX0AYSfgy5VbXQ'),('201','storage_oss_secret','hQTENHy6MYVUTgwjcgfOCq5gckm2Lp'),('202','storage_oss_domain','think-oss.oss-cn-shanghai.aliyuncs.com'),('203','storage_oss_is_https','1'),('204','sms_channel','smsbao'),('205','smsbao_user','smsbaousername'),('206','smsbao_pass','smsbaopassword'),('207','smsbao_signature','自动发卡'),('208','alidayu_key','alidayu_key'),('209','alidayu_secret','dalidayu_secret'),('210','alidayu_smstpl','SMS_111795375'),('211','alidayu_signature','自动发卡'),('212','yixin_sms_user','Daniel'),('213','yixin_sms_pass','AAA14712345678'),('214','yixin_sms_signature','自动发卡'),('215','email_name','自动发卡'),('216','email_smtp','smtp.qq.com'),('217','email_port','465'),('218','email_user','562854011@qq.com'),('219','email_pass','wtyifjljjgtnbcja'),('220','cash_min_money','100'),('221','transaction_min_fee','0.01'),('222','cash_fee_type','100'),('223','cash_fee','2'),('224','spread_rebate_rate','0.3'),('225','site_keywords','自动发卡平台,发卡平台,自动发卡,弈新自动发卡平台'),('226','site_desc','极受用户欢迎的自动发卡平台，满50元结算无手续费，自动发卡平台支持APP管理、提供API接口、微信公众号在线管理！'),('227','site_status','1'),('228','site_subtitle','【24小时稳定提供自动发卡服务】'),('229','site_close_tips','站点维护中'),('230','complaint_limit_num','1'),('231','cash_status','1'),('232','cash_close_tips','满100每天12点自动结算，无须手动结算。'),('233','cash_limit_time_start','0'),('234','cash_limit_time_end','23'),('235','cash_limit_num','5'),('236','cash_limit_num_tips','满100每天12点自动结算，无须手动结算。'),('237','site_info_tel','131-1234-5678'),('238','site_info_qq','12344321'),('239','site_info_address','广州市海珠区琶洲'),('240','site_info_email','12345678@qq.com'),('241','site_info_copyright','Copyright © 2018-2020 自动发卡系统 All rights reserved. 版权所有'),('242','site_info_icp','粤ICP备：12345678号'),('243','site_domain','https://ka.tianniu.cc'),('244','site_domain_res','/static'),('245','site_register_verify','1'),('246','site_register_status','1'),('247','site_register_smscode_status','1'),('248','site_wordfilter_status','1'),('249','site_wordfilter_danger','习近平|毛泽东|胡锦涛|江泽民|援交|傻逼|二逼|SB|脑残!111|徐才厚|郭伯雄'),('250','disabled_domains','www|ftp|bbs|blog|tes'),('251','site_domain_short','Sina'),('252','storage_local_exts','jpg,jpeg,gif,png,ico'),('253','site_logo','/static/img/logo.png'),('254','site_shop_domain','https://ka.tianniu.cc'),('255','yixin_sms_cost','0.2'),('256','smsbao_cost','0.2'),('257','alidayu_cost','0.2'),('258','index_theme','t3blue'),('259','1cloudsp_key','AccessKey'),('260','1cloudsp_secret','AccessSecret'),('261','1cloudsp_smstpl','3934'),('262','1cloudsp_signature','自动发卡'),('263','1cloudsp_cost','0.1'),('264','253sms_key','N3451234'),('265','253sms_secret','1'),('266','253sms_signature','1'),('267','253sms_cost','1'),('268','ip_register_limit','5'),('269','is_google_auth','0'),('270','sms_error_limit','5'),('271','sms_error_time','10'),('272','wrong_password_times','20'),('273','site_statistics','&lt;script src=&quot;https://s4.cnzz.com/z_stat.php?id=1261189048&amp;web_id=1261189048&quot; language=&quot;JavaScript&quot;&gt;&lt;/script&gt;&lt;a target=&quot;_blank&quot; href=&quot;https://v.pinpaibao.com.cn/cert/site/?site=www.vipkm.com&amp;at=realname&quot; &gt;&lt;img src=&quot;https://static.anquan.org/static/outer/image/sm_83x30.png&quot;&gt;&lt;/img&gt;&lt;/a&gt;'),('274','admin_login_path','admin'),('276','announce_push','1'),('278','spread_reward','0'),('279','spread_reward_money','5'),('280','spread_invite_code','1');/* MySQLReback Separation */
 /* 插入数据 `system_config` */
 INSERT INTO `system_config` VALUES ('281','contact_us','&lt;dl&gt;
	&lt;dt style=&quot;text-align: center;&quot;&gt;联系电话： &lt;font&gt;133-1234-1234&lt;/font&gt;&lt;/dt&gt;
	&lt;dd style=&quot;text-align: center;&quot;&gt;客服 QQ： &lt;font&gt;12345678&lt;/font&gt;&lt;/dd&gt;
	&lt;dd style=&quot;text-align: center;&quot;&gt;公司地址： 自动发卡系统&lt;/dd&gt;
	&lt;dd style=&quot;text-align: center;&quot;&gt;公司名称： 自动发卡系统&lt;/dd&gt;
&lt;/dl&gt;
');/* MySQLReback Separation */
 /* 插入数据 `system_config` */
 INSERT INTO `system_config` VALUES ('282','qqgroup',''),('283','wx_auto_login','1'),('284','is_need_invite_code','1'),('285','site_register_code_type','sms'),('286','auto_cash','1'),('287','auto_cash_money','100'),('288','sms_notify_channel','smsbao'),('289','merchant_logo','/static/img/logo2.png'),('290','site_info_qrcode','/static/img/qrcode.png'),('291','cash_type','[\"1\",\"2\",\"3\"]'),('292','goods_min_price','0'),('293','goods_max_price','1000'),('294','auto_cash_time','1'),('295','auto_cash_fee_type','1'),('296','auto_cash_fee','0'),('297','order_query_chkcode','1'),('298','buy_protocol',''),('299','login_auth','0'),('300','login_auth_type','0'),('301','fee_payer','1'),('302','available_email','0'),('303','idcard_auth_type','0'),('304','idcard_auth_path',''),('305','idcard_auth_appcode',''),('306','idcard_auth_status_field','status'),('307','idcard_auth_status_code','01'),('308','settlement_type','1'),('309','settlement_type','1'),('310','settlement_type','1'),('311','settlement_type','1');/* MySQLReback Separation */
 /* 创建表结构 `system_log`  */
 DROP TABLE IF EXISTS `system_log`;/* MySQLReback Separation */ CREATE TABLE `system_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作者IP地址',
  `node` char(200) NOT NULL DEFAULT '' COMMENT '当前操作节点',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '操作人用户名',
  `action` varchar(200) NOT NULL DEFAULT '' COMMENT '操作行为',
  `content` text NOT NULL COMMENT '操作内容描述',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='系统操作日志表';/* MySQLReback Separation */
 /* 插入数据 `system_log` */
 INSERT INTO `system_log` VALUES ('1','61.242.114.84','admin/login/index','demouser','系统管理','用户登录系统成功','2018-10-08 17:08:28'),('2','61.242.114.84','admin/login/out','admin','系统管理','用户退出系统成功','2018-10-08 17:09:27'),('3','61.242.114.84','admin/login/index','admin','系统管理','用户登录系统成功','2018-10-08 17:09:58'),('4','61.242.114.84','manage/user/add','admin','用户管理','添加商户成功，商户ID:10001','2018-10-08 17:12:45'),('5','61.242.114.84','manage/user/login','admin','用户管理','登录商户平台成功，商户ID:10001','2018-10-08 17:12:49'),('6','61.242.114.84','admin/config/index','admin','系统管理','系统参数配置成功','2018-10-08 17:14:28'),('7','61.242.114.84','manage/site/domain','admin','系统管理','修改域名设置成功','2018-10-08 17:16:13'),('8','61.242.114.84','manage/site/domain','admin','系统管理','修改域名设置成功','2018-10-08 17:16:21'),('9','61.242.114.84','manage/channel/change_status','admin','网关通道','成功关闭接口，ID:98','2018-10-08 17:23:04'),('10','61.242.114.84','manage/channel/change_status','admin','网关通道','成功关闭接口，ID:94','2018-10-08 17:23:09'),('11','61.242.114.84','manage/channel/change_status','admin','网关通道','成功关闭接口，ID:95','2018-10-08 17:23:13'),('12','61.242.114.84','manage/channel/change_status','admin','网关通道','成功关闭接口，ID:96','2018-10-08 17:23:17'),('13','61.242.114.84','manage/channel/change_status','admin','网关通道','成功开启接口，ID:139','2018-10-08 17:25:04'),('14','61.242.114.84','manage/channelaccount/add','admin','网关通道','添加接口账号成功，ID:{\"name\":\"演示账号\",\"rate_type\":0,\"status\":1,\"params\":{\"memberid\":\"2\",\"appid\":\"12340004\",\"key\":\"0e0a47c54df24e3015eb78475bfae915\",\"submchid\":\"\",\"refer\":\"\"},\"channel_id\":139,\"id\":\"1\"}','2018-10-08 17:26:22'),('15','61.242.114.84','manage/user/login','admin','用户管理','登录商户平台成功，商户ID:10001','2018-10-08 17:31:28'),('16','113.200.140.114','admin/login/index','admin','系统管理','用户登录系统成功','2019-02-15 22:32:07'),('17','113.200.140.114','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-15 22:32:55'),('18','113.134.39.75','admin/login/index','admin','系统管理','用户登录系统成功','2019-02-15 23:10:24'),('19','113.200.140.114','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-15 23:11:32'),('20','113.200.140.114','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-15 23:12:00'),('21','113.134.39.75','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-15 23:27:42'),('22','113.134.39.75','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-15 23:28:14'),('23','113.200.140.114','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-15 23:47:22'),('24','113.134.39.75','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-16 00:05:32'),('25','113.134.39.75','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-16 00:06:23'),('26','113.200.140.114','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-16 00:07:59'),('27','113.200.140.114','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-16 00:09:02'),('28','113.200.140.114','admin/config/index','admin','系统管理','系统参数配置成功','2019-02-16 00:10:17'),('29','113.200.140.114','manage/site/register','admin','系统管理','修改注册设置成功','2019-02-16 00:12:38'),('30','113.134.39.75','manage/site/register','admin','系统管理','修改注册设置成功','2019-02-16 00:13:04'),('31','113.134.39.75','manage/site/register','admin','系统管理','修改注册设置成功','2019-02-16 00:13:44'),('32','106.111.218.115','admin/login/index','admin','系统管理','用户登录系统成功','2019-02-17 20:59:49'),('33','106.111.218.115','manage/user/login','admin','用户管理','登录商户平台成功，商户ID:10001','2019-02-17 21:10:33'),('34','194.156.231.124','admin/login/index','admin','系统管理','用户登录系统成功','2019-02-28 22:52:53');/* MySQLReback Separation */
 /* 创建表结构 `system_menu`  */
 DROP TABLE IF EXISTS `system_menu`;/* MySQLReback Separation */ CREATE TABLE `system_menu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `node` varchar(200) NOT NULL DEFAULT '' COMMENT '节点代码',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url` varchar(400) NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(500) DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '菜单排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `create_by` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_system_menu_node` (`node`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8 COMMENT='系统菜单表';/* MySQLReback Separation */
 /* 插入数据 `system_menu` */
 INSERT INTO `system_menu` VALUES ('2','0','系统管理','','fa fa-gear','#','','_self','1000','1','0','2015-11-16 11:15:38'),('4','2','系统配置','','','#','','_self','100','1','0','2016-03-14 10:12:55'),('5','4','网站参数','','fa fa-apple','admin/config/index','','_self','20','1','0','2016-05-06 06:36:49'),('6','4','文件存储','','fa fa-save','admin/config/file','','_self','30','1','0','2016-05-06 06:39:43'),('9','20','后台操作日志','','glyphicon glyphicon-console','admin/log/index','','_self','50','1','0','2017-03-24 07:49:31'),('19','20','权限管理','','fa fa-user-secret','admin/auth/index','','_self','10','1','0','2015-11-17 05:18:12'),('20','2','系统权限','','','#','','_self','200','1','0','2016-03-14 10:11:41'),('21','20','系统菜单','','glyphicon glyphicon-menu-hamburger','admin/menu/index','','_self','30','1','0','2015-11-16 11:16:16'),('22','20','节点管理','','fa fa-ellipsis-v','admin/node/index','','_self','20','1','0','2015-11-16 11:16:16'),('29','20','系统用户','','fa fa-users','admin/user/index','','_self','40','1','0','2016-10-31 06:31:40'),('61','0','微信管理','','fa fa-wechat','#','','_self','2000','1','0','2017-03-29 03:00:21'),('62','61','微信对接配置','','','#','','_self','100','1','0','2017-03-29 03:03:38'),('63','62','微信接口配置
','','fa fa-usb','wechat/config/index','','_self','10','1','0','2017-03-29 03:04:44'),('65','61','微信粉丝管理','','','#','','_self','300','1','0','2017-03-29 03:08:32'),('66','65','粉丝标签','','fa fa-tags','wechat/tags/index','','_self','10','1','0','2017-03-29 03:09:41'),('67','65','已关注粉丝','','fa fa-wechat','wechat/fans/index','','_self','20','1','0','2017-03-29 03:10:07'),('68','61','微信订制','','','#','','_self','200','1','0','2017-03-29 03:10:39'),('69','68','微信菜单定制','','glyphicon glyphicon-phone','wechat/menu/index','','_self','40','1','0','2017-03-29 03:11:08'),('70','68','关键字管理','','fa fa-paw','wechat/keys/index','','_self','10','1','0','2017-03-29 03:11:49'),('71','68','关注自动回复','','fa fa-commenting-o','wechat/keys/subscribe','','_self','20','1','0','2017-03-29 03:12:32'),('81','68','无配置默认回复','','fa fa-commenting-o','wechat/keys/defaults','','_self','30','1','0','2017-04-21 06:48:25'),('82','61','素材资源管理','','','#','','_self','300','1','0','2017-04-24 03:23:18'),('83','82','添加图文','','fa fa-folder-open-o','wechat/news/add?id=1','','_self','20','1','0','2017-04-24 03:23:40'),('85','82','图文列表','','fa fa-file-pdf-o','wechat/news/index','','_self','10','1','0','2017-04-24 03:25:45'),('86','65','粉丝黑名单','','fa fa-reddit-alien','wechat/fans/back','','_self','30','1','0','2017-05-05 08:17:03'),('87','0','插件案例','','','#','','_self','3000','0','0','2017-07-10 07:10:16'),('88','87','第三方插件','','','#','','_self','0','0','0','2017-07-10 07:10:37'),('90','88','PCAS 省市区','','','demo/plugs/region','','_self','0','0','0','2017-07-10 10:45:47'),('91','87','内置插件','','','#','','_self','0','0','0','2017-07-10 10:56:59'),('92','91','文件上传','','','demo/plugs/file','','_self','0','0','0','2017-07-10 10:57:22'),('93','88','富文本编辑器','','','demo/plugs/editor','','_self','0','0','0','2017-07-28 07:19:44'),('95','0','网关通道','','fa fa-product-hunt','#','','_self','4000','1','0','2018-01-18 03:08:57'),('97','95','支付接口管理','','fa fa-futbol-o','manage/channel/index','','_self','0','1','0','2018-01-18 03:09:53'),('99','4','短信配置','','fa fa-envelope-o','manage/sms/index','','_self','50','1','0','2018-01-18 03:36:42'),('100','4','邮件配置','','fa fa-envelope-o','manage/email/index','','_self','60','1','0','2018-01-19 05:45:19'),('101','0','用户管理','','fa fa-users','#','','_self','5000','1','0','2018-01-23 02:46:59'),('102','101','商户管理','','fa fa-users','manage/user/index','','_self','0','1','0','2018-01-23 02:47:40'),('103','0','交易明细','','fa fa-bar-chart','#','','_self','6000','1','0','2018-01-23 08:47:46'),('104','103','订单列表','','fa fa-list-ol','manage/order/index','','_self','0','1','0','2018-01-23 08:48:09'),('105','103','金额变动记录','','fa fa-exchange','manage/log/user_money','','_self','100','1','0','2018-01-24 07:02:50'),('106','0','财务管理','','fa fa-google-wallet','#','','_self','8000','1','0','2018-01-24 07:17:39'),('107','106','提现管理','','fa fa-cny','manage/cash/index','','_self','0','1','0','2018-01-24 07:18:06'),('108','4','后台主页','','fa fa-area-chart','manage/index/main','','_self','0','1','0','2018-01-26 06:19:38'),('109','106','提现配置','','fa fa-google-wallet','manage/cash/config','','_self','70','1','0','2018-01-29 07:29:43'),('110','4','站点信息','','glyphicon glyphicon-info-sign','manage/site/info','','_self','21','1','0','2018-01-29 08:26:24'),('111','4','域名设置','','fa fa-at','manage/site/domain','','_self','25','1','0','2018-01-29 08:47:15'),('112','4','注册设置','','fa fa-cog','manage/site/register','','_self','27','1','0','2018-01-29 10:45:43');/* MySQLReback Separation */
 /* 插入数据 `system_menu` */
 INSERT INTO `system_menu` VALUES ('113','4','字词过滤','','fa fa-filter','manage/site/wordfilter','','_self','90','1','0','2018-01-30 06:50:48');/* MySQLReback Separation */
 /* 插入数据 `system_menu` */
 INSERT INTO `system_menu` VALUES ('114','103','商户分析','','fa fa-area-chart','manage/order/merchant','','_self','40','1','0','2018-01-31 03:32:00'),('115','103','渠道分析','','fa fa-area-chart','manage/order/channel','','_self','50','1','0','2018-01-31 03:32:29'),('116','103','实时数据','','fa fa-area-chart','manage/order/hour','','_self','60','1','0','2018-01-31 03:32:57'),('117','0','商品管理','','fa fa-shopping-bag','#','','_self','5500','1','0','2018-02-01 10:43:51'),('118','117','商品管理','','fa fa-shopping-bag','manage/goods/index','','_self','0','1','0','2018-02-01 10:44:10'),('119','103','投诉管理','','fa fa-bullhorn','manage/complaint/index','','_self','20','1','0','2018-02-02 07:34:06'),('121','0','内容管理','','fa fa-file-text','#','','_self','4500','1','0','2018-02-09 02:38:43'),('122','121','内容管理','','fa fa-file-text','manage/article/index','','_self','0','1','0','2018-02-09 02:44:51'),('123','121','分类管理','','fa fa-bars','manage/article_category/index','','_self','0','1','0','2018-03-05 16:13:26'),('124','4','备份管理','','fa fa-database','manage/backup/index','','_self','100','1','0','2018-03-12 11:31:11'),('125','4','前台导航','','fa fa-navicon','admin/nav/index','','_self','110','1','0','2018-03-23 09:08:38'),('126','101','登录解锁','','fa fa-unlock','/manage/user/unlock','','_self','0','1','0','2018-03-27 03:15:00'),('127','20','商户操作日志','','fa fa-thumb-tack','/admin/mlog/index','','_self','60','1','0','2018-03-27 08:19:10'),('128','4','推广设置','','fa fa-bullhorn','/manage/site/spread','','_self','120','1','0','2018-03-27 11:19:29'),('129','101','邀请码管理','','fa fa-user-plus','/manage/invite_code/index','','_self','0','1','0','2018-03-27 11:54:29'),('130','117','订单自定义','','fa fa-credit-card-alt','manage/goods/change_trade_no_status','','_self','0','1','0','2018-04-20 01:03:50'),('131','101','费率分组管理','','','manage/rate/index','','_self','0','1','0','2018-06-20 02:53:02'),('132','4','数据删除','','','manage/content/del','','_self','0','1','0','2018-10-08 16:49:42'),('133','95','安装支付接口','','fa fa-futbol-o','manage/channel/index?is_install=0','','_self','0','1','0','2018-01-18 03:09:53'),('135','106','代付渠道管理','','fa fa-futbol-o','manage/cashChannel/index','','_self','1000','1','0','2018-08-07 15:50:38');/* MySQLReback Separation */
 /* 创建表结构 `system_node`  */
 DROP TABLE IF EXISTS `system_node`;/* MySQLReback Separation */ CREATE TABLE `system_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `node` varchar(100) DEFAULT NULL COMMENT '节点代码',
  `title` varchar(500) DEFAULT NULL COMMENT '节点标题',
  `is_menu` tinyint(1) unsigned DEFAULT '0' COMMENT '是否可设置为菜单',
  `is_auth` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启动RBAC权限控制',
  `is_login` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启动登录控制',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_system_node_node` (`node`)
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统节点表';/* MySQLReback Separation */
 /* 插入数据 `system_node` */
 INSERT INTO `system_node` VALUES ('131','admin/auth/index','权限列表','1','1','1','2017-08-23 07:45:42'),('132','admin','后台管理','0','1','1','2017-08-23 07:45:44'),('133','admin/auth/apply','节点授权','0','1','1','2017-08-23 08:05:18'),('134','admin/auth/add','添加授权','0','1','1','2017-08-23 08:05:19'),('135','admin/auth/edit','编辑权限','0','1','1','2017-08-23 08:05:19'),('136','admin/auth/forbid','禁用权限','0','1','1','2017-08-23 08:05:20'),('137','admin/auth/resume','启用权限','0','1','1','2017-08-23 08:05:20'),('138','admin/auth/del','删除权限','0','1','1','2017-08-23 08:05:21'),('139','admin/config/index','参数配置','1','1','1','2017-08-23 08:05:22'),('140','admin/config/file','文件配置','1','1','1','2017-08-23 08:05:22'),('141','admin/log/index','日志列表','1','1','1','2017-08-23 08:05:23'),('142','admin/log/del','删除日志','0','1','1','2017-08-23 08:05:24'),('143','admin/menu/index','菜单列表','1','1','1','2017-08-23 08:05:25'),('144','admin/menu/add','添加菜单','0','1','1','2017-08-23 08:05:25'),('145','admin/menu/edit','编辑菜单','0','1','1','2017-08-23 08:05:26'),('146','admin/menu/del','删除菜单','0','1','1','2017-08-23 08:05:26'),('147','admin/menu/forbid','禁用菜单','0','1','1','2017-08-23 08:05:27'),('148','admin/menu/resume','启用菜单','0','1','1','2017-08-23 08:05:28'),('149','admin/node/index','节点列表','1','1','1','2017-08-23 08:05:29'),('150','admin/node/save','节点更新','0','1','1','2017-08-23 08:05:30'),('151','admin/user/index','用户管理','1','1','1','2017-08-23 08:05:31'),('152','admin/user/auth','用户授权','0','1','1','2017-08-23 08:05:32'),('153','admin/user/add','添加用户','0','1','1','2017-08-23 08:05:33'),('154','admin/user/edit','编辑用户','0','1','1','2017-08-23 08:05:33'),('155','admin/user/pass','用户密码','0','1','1','2017-08-23 08:05:34'),('156','admin/user/del','删除用户','0','1','1','2017-08-23 08:05:34'),('157','admin/user/forbid','禁用用户','0','1','1','2017-08-23 08:05:34'),('158','admin/user/resume','启用用户','0','1','1','2017-08-23 08:05:35'),('159','demo/plugs/file','文件上传','1','0','0','2017-08-23 08:05:36'),('160','demo/plugs/region','区域选择','1','0','0','2017-08-23 08:05:36'),('161','demo/plugs/editor','富文本器','1','0','0','2017-08-23 08:05:37'),('162','wechat/config/index','微信参数','1','1','1','2017-08-23 08:05:37'),('163','wechat/config/pay','微信支付','0','1','1','2017-08-23 08:05:38'),('164','wechat/fans/index','粉丝列表','1','1','1','2017-08-23 08:05:39'),('165','wechat/fans/back','粉丝黑名单','1','1','1','2017-08-23 08:05:39'),('166','wechat/fans/backadd','移入黑名单','0','1','1','2017-08-23 08:05:40'),('167','wechat/fans/tagset','设置标签','0','1','1','2017-08-23 08:05:41'),('168','wechat/fans/backdel','移出黑名单','0','1','1','2017-08-23 08:05:41'),('169','wechat/fans/tagadd','添加标签','0','1','1','2017-08-23 08:05:41'),('170','wechat/fans/tagdel','删除标签','0','1','1','2017-08-23 08:05:42'),('171','wechat/fans/sync','同步粉丝','0','1','1','2017-08-23 08:05:43'),('172','wechat/keys/index','关键字列表','1','1','1','2017-08-23 08:05:44'),('173','wechat/keys/add','添加关键字','0','1','1','2017-08-23 08:05:44'),('174','wechat/keys/edit','编辑关键字','0','1','1','2017-08-23 08:05:45'),('175','wechat/keys/del','删除关键字','0','1','1','2017-08-23 08:05:45'),('176','wechat/keys/forbid','禁用关键字','0','1','1','2017-08-23 08:05:46'),('177','wechat/keys/resume','启用关键字','0','1','1','2017-08-23 08:05:46'),('178','wechat/keys/subscribe','关注默认回复','0','1','1','2017-08-23 08:05:48'),('179','wechat/keys/defaults','默认响应回复','0','1','1','2017-08-23 08:05:49'),('180','wechat/menu/index','微信菜单','1','1','1','2017-08-23 08:05:51'),('181','wechat/menu/edit','发布微信菜单','0','1','1','2017-08-23 08:05:51'),('182','wechat/menu/cancel','取消微信菜单','0','1','1','2017-08-23 08:05:52'),('183','wechat/news/index','微信图文列表','1','1','1','2017-08-23 08:05:52'),('184','wechat/news/select','微信图文选择','0','1','1','2017-08-23 08:05:53'),('185','wechat/news/image','微信图片选择','0','1','1','2017-08-23 08:05:53'),('186','wechat/news/add','添加图文','0','1','1','2017-08-23 08:05:54'),('187','wechat/news/edit','编辑图文','0','1','1','2017-08-23 08:05:56'),('188','wechat/news/del','删除图文','0','1','1','2017-08-23 08:05:56'),('189','wechat/news/push','推送图文','0','1','1','2017-08-23 08:05:56'),('190','wechat/tags/index','微信标签列表','1','1','1','2017-08-23 08:05:58'),('191','wechat/tags/add','添加微信标签','0','1','1','2017-08-23 08:05:58'),('192','wechat/tags/edit','编辑微信标签','0','1','1','2017-08-23 08:05:58'),('193','wechat/tags/sync','同步微信标签','0','1','1','2017-08-23 08:05:59'),('194','admin/auth','权限管理','0','1','1','2017-08-23 08:06:58'),('195','admin/config','系统配置','0','1','1','2017-08-23 08:07:34'),('196','admin/log','系统日志','0','1','1','2017-08-23 08:07:46'),('197','admin/menu','系统菜单','0','1','1','2017-08-23 08:08:02');/* MySQLReback Separation */
 /* 插入数据 `system_node` */
 INSERT INTO `system_node` VALUES ('198','admin/node','系统节点','0','1','1','2017-08-23 08:08:44');/* MySQLReback Separation */
 /* 插入数据 `system_node` */
 INSERT INTO `system_node` VALUES ('199','admin/user','系统用户','0','1','1','2017-08-23 08:09:43'),('200','demo','插件案例','0','1','1','2017-08-23 08:10:43'),('201','demo/plugs','插件案例','0','1','1','2017-08-23 08:10:51'),('202','wechat','微信管理','0','1','1','2017-08-23 08:11:13'),('203','wechat/config','微信配置','0','1','1','2017-08-23 08:11:19'),('204','wechat/fans','粉丝管理','0','1','1','2017-08-23 08:11:36'),('205','wechat/keys','关键字管理','0','1','1','2017-08-23 08:13:00'),('206','wechat/menu','微信菜单管理','0','1','1','2017-08-23 08:14:11'),('207','wechat/news','微信图文管理','0','1','1','2017-08-23 08:14:40'),('208','wechat/tags','微信标签管理','0','1','1','2017-08-23 08:15:25'),('209','manage/channel/index','供应商管理','0','1','1','2018-01-19 05:53:53'),('210','manage/channel/add','添加供应商','0','1','1','2018-01-19 05:53:54'),('211','manage/channel/edit','修改供应商','0','1','1','2018-01-19 05:53:54'),('212','manage/channel/del','删除供应商','0','1','1','2018-01-19 05:53:54'),('213','manage/channel/change_status','修改供应商状态','0','1','1','2018-01-19 05:53:55'),('214','manage/channel/getlistbypaytype','根据支付类型获取支付供应商列表','0','1','1','2018-01-19 05:53:55'),('215','manage/channelaccount/add','添加供应商账号','0','1','1','2018-01-19 05:54:03'),('216','manage/channelaccount/index','供应商账号管理','0','1','1','2018-01-19 05:54:04'),('217','manage/channelaccount/edit','修改供应商账号','0','1','1','2018-01-19 05:54:05'),('218','manage/channelaccount/del','删除供应商账号','0','1','1','2018-01-19 05:54:06'),('219','manage/channelaccount/clear','清除供应商账号额度','0','1','1','2018-01-19 05:54:07'),('220','manage/channelaccount/change_status','修改供应商账号状态','0','1','1','2018-01-19 05:54:07'),('221','manage/channelaccount/getfields','获取渠道账户字段','0','1','1','2018-01-19 05:54:08'),('222','manage/email/index','邮件配置','0','1','1','2018-01-19 05:54:10'),('223','manage/email/test','发信测试','0','1','1','2018-01-19 05:54:10'),('224','manage/product/index','支付产品管理','0','1','1','2018-01-19 05:54:11'),('225','manage/product/add','添加支付产品','0','1','1','2018-01-19 05:54:12'),('226','manage/product/edit','编辑支付产品','0','1','1','2018-01-19 05:54:12'),('227','manage/product/del','删除支付产品','0','1','1','2018-01-19 05:54:14'),('228','manage/product/change_status','修改支付产品状态','0','1','1','2018-01-19 05:54:14'),('229','manage/sms/index','短信配置','0','1','1','2018-01-19 05:54:15'),('230','manage/cash/index','提现列表','0','1','1','2018-01-25 08:47:20'),('231','manage/cash/detail','提现申请详情','0','1','1','2018-01-25 08:47:20'),('232','manage/cash/payqrcode','','0','1','1','2018-01-25 08:47:21'),('233','manage/log/user_money','金额变动记录','0','1','1','2018-01-25 08:47:24'),('234','manage/order/index','订单列表','0','1','1','2018-01-25 08:47:25'),('235','manage/order/detail','订单详情','0','1','1','2018-01-25 08:47:26'),('236','manage/user/index','商户管理','0','1','1','2018-01-25 08:47:29'),('237','manage/user/change_status','修改商户审核状态','0','1','1','2018-01-25 08:47:30'),('238','manage/user/detail','查看商户详情','0','1','1','2018-01-25 08:47:30'),('239','manage/user/add','添加商户','0','1','1','2018-01-25 08:47:31'),('240','manage/user/edit','编辑商户','0','1','1','2018-01-25 08:47:31'),('241','manage/user/del','删除商户','0','1','1','2018-01-25 08:47:32'),('242','manage/user/manage_money','商户资金管理','0','1','1','2018-01-25 08:47:32'),('243','manage/user/rate','设置商户费率','0','1','1','2018-01-25 08:47:33'),('244','manage/cash/config','提现配置','0','1','1','2018-02-01 09:00:28'),('245','manage/index/main','主页','0','1','1','2018-02-01 09:00:33'),('246','manage/order/merchant','商户分析','0','1','1','2018-02-01 09:00:35'),('247','manage/order/channel','渠道分析','0','1','1','2018-02-01 09:00:36'),('248','manage/order/hour','实时数据','0','1','1','2018-02-01 09:00:36'),('249','manage/site/info','站点信息配置','0','1','1','2018-02-01 09:00:40'),('250','manage/site/domain','域名设置','0','1','1','2018-02-01 09:00:40'),('251','manage/site/register','注册设置','0','1','1','2018-02-01 09:00:41'),('252','manage/site/wordfilter','字词过滤','0','1','1','2018-02-01 09:00:41'),('253','manage/user/change_freeze_status','修改商户冻结状态','0','1','1','2018-02-01 09:00:43'),('254','manage/user/login','商户登录','0','1','1','2018-02-01 09:00:45'),('255','manage/user/message','商户站内信','0','1','1','2018-02-01 09:00:45'),('256','merchant/cash/index','','0','0','0','2018-02-01 09:00:48'),('257','manage/goods/index','商品管理','0','1','1','2018-02-01 11:33:28'),('258','manage/goods/change_status','修改商品上架状态','0','1','1','2018-02-01 11:33:29'),('259','manage/complaint/index','投诉管理','0','1','1','2018-02-02 11:46:10'),('260','manage/complaint/change_status','修改投诉处理状态','0','1','1','2018-02-02 11:46:11');/* MySQLReback Separation */
 /* 插入数据 `system_node` */
 INSERT INTO `system_node` VALUES ('261','manage/complaint/change_admin_read','修改投诉读取状态','0','1','1','2018-02-02 11:46:11');/* MySQLReback Separation */
 /* 插入数据 `system_node` */
 INSERT INTO `system_node` VALUES ('262','manage/complaint/del','删除投诉','0','1','1','2018-02-02 11:46:12'),('263','manage/order/change_freeze_status','修改订单冻结状态','0','1','1','2018-02-05 10:24:23'),('264','manage/user/loginlog','商户登录日志','0','1','1','2018-02-05 10:24:31'),('265','merchant/user/closelink','','0','0','0','2018-03-20 06:22:03'),('266','merchant/goodscategory','','0','0','0','2018-03-20 06:22:32'),('267','merchant/cash/apply','','0','0','0','2018-03-20 06:22:35'),('268','merchant/cash','','0','0','0','2018-03-20 06:22:38'),('269','merchant','','0','0','0','2018-03-20 06:23:00'),('270','manage/article/add','添加文章','0','1','1','2018-03-20 06:23:38'),('271','manage/article/edit','编辑文章','0','1','1','2018-03-20 06:23:39'),('272','manage/article/index','内容管理','0','1','1','2018-03-20 06:23:39'),('273','manage/article/change_status','修改文章状态','0','1','1','2018-03-20 06:23:40'),('274','manage/article/del','删除文章','0','1','1','2018-03-20 06:23:41'),('275','manage/articlecategory/index','文章分类管理','0','1','1','2018-03-20 06:23:53'),('276','manage/articlecategory/add','添加文章分类','0','1','1','2018-03-20 06:23:53'),('277','manage/articlecategory/edit','编辑文章分类','0','1','1','2018-03-20 06:23:54'),('278','manage/articlecategory/change_status','修改文章分类状态','0','1','1','2018-03-20 06:23:54'),('279','manage/articlecategory/del','删除文章分类','0','1','1','2018-03-20 06:23:55'),('280','manage/backup/index','备份管理','0','1','1','2018-03-20 06:24:04'),('281','manage/backup/tablist','获取数据表','0','1','1','2018-03-20 06:24:05'),('282','manage/backup/backall','备份数据库','0','1','1','2018-03-20 06:24:06'),('283','manage/backup/backtables','按表备份','0','1','1','2018-03-20 06:24:07'),('284','manage/backup/recover','还原数据库','0','1','1','2018-03-20 06:24:07'),('285','manage/backup/downloadbak','下载备份文件','0','1','1','2018-03-20 06:24:08'),('286','manage/backup/deletebak','删除备份','0','1','1','2018-03-20 06:24:09'),('287','manage/article','内容管理','0','1','1','2018-03-22 08:32:51'),('288','admin/auth/google','','0','0','0','2018-03-22 08:33:13'),('289','admin/auth/bindgoogle','生成绑定谷歌身份验证器二维码','0','0','0','2018-03-22 08:39:13'),('290','manage/user','用户管理','0','1','1','2018-03-22 08:41:20'),('291','manage/sms','短信配置','0','1','1','2018-03-22 08:44:54'),('292','manage/site','站点信息','0','1','1','2018-03-22 08:45:04'),('293','manage/product','支付产品管理','0','1','1','2018-03-22 08:47:47'),('294','manage/order/del_batch','批量删除无效订单','0','1','1','2018-03-22 08:48:42'),('295','manage/order/del','删除无效订单','0','1','1','2018-03-22 08:48:43'),('296','manage/order','交易明细','0','1','1','2018-03-22 08:50:10'),('297','manage/log','金额变动记录','0','1','1','2018-03-22 08:51:25'),('298','manage/index','主页','0','1','1','2018-03-22 08:51:55'),('299','manage/goods','商品管理','0','1','1','2018-03-22 08:52:09'),('300','manage/email','邮件配置','0','1','1','2018-03-22 08:53:07'),('301','manage/complaint','投诉管理','0','1','1','2018-03-22 08:54:06'),('302','manage/channelaccount','供应商账号管理','0','1','1','2018-03-22 08:54:52'),('303','manage/channel','供应商管理','0','1','1','2018-03-22 10:45:06'),('304','manage/cash','提现管理','0','1','1','2018-03-22 10:46:43'),('305','manage/backup','备份管理','0','1','1','2018-03-22 10:49:14'),('306','manage/articlecategory','文章分类管理','0','1','1','2018-03-22 10:53:43'),('307','manage/goods/change_trade_no_status','','0','1','1','2018-04-20 01:04:48'),('308','shop/shop/index','','0','0','0','2018-06-21 10:19:27'),('309','shop/shop/category','','0','0','0','2018-06-21 10:19:28'),('310','shop/shop/goods','','0','0','0','2018-06-21 10:20:39'),('311','shop/shop/getgoodslist','','0','0','0','2018-06-21 10:20:40'),('312','shop/shop/getgoodsinfo','','0','0','0','2018-06-21 10:20:41'),('313','shop/shop/getrate','','0','0','0','2018-06-21 10:20:41'),('314','shop/shop/getdiscounts','','0','0','0','2018-06-21 10:20:42'),('315','shop/shop/getdiscount','','0','0','0','2018-06-21 10:20:43'),('316','shop/shop/checkvisitpassword','','0','0','0','2018-06-21 10:20:43'),('317','shop/shop/checkcoupon','','0','0','0','2018-06-21 10:20:44');/* MySQLReback Separation */
 /* 创建表结构 `system_sequence`  */
 DROP TABLE IF EXISTS `system_sequence`;/* MySQLReback Separation */ CREATE TABLE `system_sequence` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT NULL COMMENT '序号类型',
  `sequence` char(50) NOT NULL COMMENT '序号值',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_sequence_unique` (`type`,`sequence`) USING BTREE,
  KEY `index_system_sequence_type` (`type`),
  KEY `index_system_sequence_number` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统序号表';/* MySQLReback Separation */
 /* 创建表结构 `system_user`  */
 DROP TABLE IF EXISTS `system_user`;/* MySQLReback Separation */ CREATE TABLE `system_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户登录名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户登录密码',
  `qq` varchar(16) DEFAULT NULL COMMENT '联系QQ',
  `mail` varchar(32) DEFAULT NULL COMMENT '联系邮箱',
  `phone` varchar(16) DEFAULT NULL COMMENT '联系手机号',
  `desc` varchar(255) DEFAULT '' COMMENT '备注说明',
  `login_num` bigint(20) unsigned DEFAULT '0' COMMENT '登录次数',
  `login_at` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `authorize` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除状态(1:删除,0:未删)',
  `create_by` bigint(20) unsigned DEFAULT NULL COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `google_secret_key` varchar(128) DEFAULT '' COMMENT '谷歌令牌密钥',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_system_user_username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10006 DEFAULT CHARSET=utf8 COMMENT='系统用户表';/* MySQLReback Separation */
 /* 插入数据 `system_user` */
 INSERT INTO `system_user` VALUES ('10005','admin','21232f297a57a5a743894a0e4a801fc3','','12345678@qq.com','13800138000','demo','260','2019-02-28 22:52:53','1','3','0','','2018-05-02 08:40:09','');/* MySQLReback Separation */
 /* 创建表结构 `unique_orderno`  */
 DROP TABLE IF EXISTS `unique_orderno`;/* MySQLReback Separation */ CREATE TABLE `unique_orderno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trade_no` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '订单号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_trade_no` (`trade_no`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;/* MySQLReback Separation */
 /* 创建表结构 `user`  */
 DROP TABLE IF EXISTS `user`;/* MySQLReback Separation */ CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级ID',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '微信openid',
  `username` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号',
  `qq` varchar(16) NOT NULL,
  `email` varchar(50) NOT NULL,
  `subdomain` varchar(250) NOT NULL DEFAULT '' COMMENT '子域名',
  `shop_name` varchar(20) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `shop_notice` varchar(200) NOT NULL DEFAULT '' COMMENT '公告通知',
  `statis_code` varchar(1024) NOT NULL DEFAULT '' COMMENT '统计代码',
  `pay_theme` varchar(255) NOT NULL DEFAULT 'default' COMMENT '支付页风格',
  `stock_display` tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '库存展示方式 1实际库存 2库存范围',
  `money` decimal(10,3) NOT NULL DEFAULT '0.000',
  `rebate` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `freeze_money` decimal(10,3) NOT NULL DEFAULT '0.000',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '0未审核 1已审核',
  `is_freeze` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否冻结 0否 1是',
  `create_at` int(10) unsigned NOT NULL,
  `ip` varchar(50) DEFAULT '' COMMENT 'IP地址',
  `website` varchar(255) NOT NULL DEFAULT '' COMMENT '商户网站',
  `is_close` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭店铺 0否 1是',
  `shop_notice_auto_pop` tinyint(4) NOT NULL DEFAULT '1' COMMENT '商家公告是否自动弹出',
  `cash_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '提现方式',
  `login_auth` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否开启安全登录',
  `login_auth_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '安全登录验证方式，1：短信，2：邮件，3：谷歌密码验证',
  `google_secret_key` varchar(128) DEFAULT '' COMMENT '谷歌令牌密钥',
  `shop_gouka_protocol_pop` tinyint(4) NOT NULL DEFAULT '1' COMMENT '购卡协议是否自动弹出',
  `user_notice_auto_pop` tinyint(4) NOT NULL DEFAULT '1' COMMENT '商家是否自动弹出',
  `login_key` int(11) NOT NULL DEFAULT '0' COMMENT '用户登录标记',
  `fee_payer` tinyint(4) NOT NULL DEFAULT '0' COMMENT '订单手续费支付方，0：跟随系统，1：商家承担，2买家承担',
  `settlement_type` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '结算方式，-1：跟随系统，1:T1结算，0:T0结算',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_mobile_uindex` (`mobile`),
  UNIQUE KEY `user_email_uindex` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10002 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `user` */
 INSERT INTO `user` VALUES ('10001','0','','demo','25d55ad283aa400af464c76d713c07ad','13800138000','5021314','demo@qq.com','','','','','default','2','0.000','0.000','0.090','1','0','1538989965','','','0','1','1','0','1','','1','1','0','0','-1');/* MySQLReback Separation */
 /* 创建表结构 `user_channel`  */
 DROP TABLE IF EXISTS `user_channel`;/* MySQLReback Separation */ CREATE TABLE `user_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `channel_id` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `user_collect`  */
 DROP TABLE IF EXISTS `user_collect`;/* MySQLReback Separation */ CREATE TABLE `user_collect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型 1支付宝 2微信 3银行卡',
  `info` text NOT NULL,
  `create_at` int(10) unsigned NOT NULL DEFAULT '0',
  `collect_img` tinytext,
  `allow_update` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1为允许用户修改收款信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `user_login_error_log`  */
 DROP TABLE IF EXISTS `user_login_error_log`;/* MySQLReback Separation */ CREATE TABLE `user_login_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(50) NOT NULL DEFAULT '' COMMENT '登录名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '尝试密码',
  `user_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：普通用户 1：后台管理员账号',
  `login_from` int(1) NOT NULL DEFAULT '0' COMMENT '登录来源：0：前台 1：总后台',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `user_login_log`  */
 DROP TABLE IF EXISTS `user_login_log`;/* MySQLReback Separation */ CREATE TABLE `user_login_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `create_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `user_login_log` */
 INSERT INTO `user_login_log` VALUES ('1','10001','61.242.114.84','1538990543');/* MySQLReback Separation */
 /* 创建表结构 `user_money_log`  */
 DROP TABLE IF EXISTS `user_money_log`;/* MySQLReback Separation */ CREATE TABLE `user_money_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_type` enum('sub_sold_rebate','sub_fee_rebate','cash_notpass','cash_success','apply_cash','admin_dec','admin_inc','goods_sold','fee','sub_register','freeze','unfreeze') NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `money` decimal(10,3) NOT NULL COMMENT '变动金额',
  `balance` decimal(10,3) NOT NULL COMMENT '剩余',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '变动原因',
  `create_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 插入数据 `user_money_log` */
 INSERT INTO `user_money_log` VALUES ('1','goods_sold','10001','0.100','0.100','【卖出商品】成功售出商品绝地求生卡密（1张）','1538990812'),('2','goods_sold','10001','-0.010','0.090','【卖出商品】扣除交易手续费，订单：T1810081726376105','1538990812'),('3','freeze','10001','-0.090','0.000','【冻结金额】冻结订单：T1810081726376105，冻结金额：0.09元','1538990812');/* MySQLReback Separation */
 /* 创建表结构 `user_rate`  */
 DROP TABLE IF EXISTS `user_rate`;/* MySQLReback Separation */ CREATE TABLE `user_rate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `channel_id` int(10) unsigned NOT NULL COMMENT '渠道ID',
  `rate` decimal(10,4) unsigned NOT NULL COMMENT '费率',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `user_token`  */
 DROP TABLE IF EXISTS `user_token`;/* MySQLReback Separation */ CREATE TABLE `user_token` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户 id',
  `token` varchar(255) NOT NULL COMMENT '用户登录凭证',
  `platform` varchar(20) NOT NULL COMMENT '用户登录平台',
  `refresh_token` varchar(255) NOT NULL COMMENT '登录凭证刷新凭证',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间，即登录时间',
  `expire_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '凭证过期时间',
  KEY `index_login_user` (`user_id`) USING BTREE,
  KEY `index_login_token` (`token`) USING BTREE,
  KEY `index_login_platform` (`platform`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `verify_email_error_log`  */
 DROP TABLE IF EXISTS `verify_email_error_log`;/* MySQLReback Separation */ CREATE TABLE `verify_email_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT '' COMMENT '前台用户名',
  `admin` varchar(50) DEFAULT '' COMMENT '管理员用户名',
  `email` varchar(20) DEFAULT '' COMMENT '邮箱',
  `code` varchar(10) DEFAULT '' COMMENT '输入验证码',
  `screen` varchar(20) DEFAULT '' COMMENT '使用场景',
  `type` tinyint(1) DEFAULT '0' COMMENT '1：短信验证码 2：谷歌身份验证, 0:邮箱',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `verify_error_log`  */
 DROP TABLE IF EXISTS `verify_error_log`;/* MySQLReback Separation */ CREATE TABLE `verify_error_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT '' COMMENT '前台用户名',
  `admin` varchar(50) DEFAULT '' COMMENT '管理员用户名',
  `mobile` varchar(20) DEFAULT '' COMMENT '手机号码',
  `code` varchar(10) DEFAULT '' COMMENT '输入验证码',
  `screen` varchar(20) DEFAULT '' COMMENT '使用场景',
  `type` tinyint(1) DEFAULT '0' COMMENT '1：短信验证码 2：谷歌身份验证',
  `ctime` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* MySQLReback Separation */
 /* 创建表结构 `wechat_fans`  */
 DROP TABLE IF EXISTS `wechat_fans`;/* MySQLReback Separation */ CREATE TABLE `wechat_fans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '粉丝表ID',
  `appid` varchar(50) DEFAULT NULL COMMENT '公众号Appid',
  `groupid` bigint(20) unsigned DEFAULT NULL COMMENT '分组ID',
  `tagid_list` varchar(100) DEFAULT '' COMMENT '标签id',
  `is_back` tinyint(1) unsigned DEFAULT '0' COMMENT '是否为黑名单用户',
  `subscribe` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户是否订阅该公众号，0：未关注，1：已关注',
  `openid` char(100) NOT NULL DEFAULT '' COMMENT '用户的标识，对当前公众号唯一',
  `spread_openid` char(100) DEFAULT NULL COMMENT '推荐人OPENID',
  `spread_at` datetime DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL COMMENT '用户的昵称',
  `sex` tinyint(1) unsigned DEFAULT NULL COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
  `country` varchar(50) DEFAULT NULL COMMENT '用户所在国家',
  `province` varchar(50) DEFAULT NULL COMMENT '用户所在省份',
  `city` varchar(50) DEFAULT NULL COMMENT '用户所在城市',
  `language` varchar(50) DEFAULT NULL COMMENT '用户的语言，简体中文为zh_CN',
  `headimgurl` varchar(500) DEFAULT NULL COMMENT '用户头像',
  `subscribe_time` bigint(20) unsigned DEFAULT NULL COMMENT '用户关注时间',
  `subscribe_at` datetime DEFAULT NULL COMMENT '关注时间',
  `unionid` varchar(100) DEFAULT NULL COMMENT 'unionid',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `expires_in` bigint(20) unsigned DEFAULT '0' COMMENT '有效时间',
  `refresh_token` varchar(200) DEFAULT NULL COMMENT '刷新token',
  `access_token` varchar(200) DEFAULT NULL COMMENT '访问token',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_fans_spread_openid` (`spread_openid`) USING BTREE,
  KEY `index_wechat_fans_openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信粉丝';/* MySQLReback Separation */
 /* 创建表结构 `wechat_fans_tags`  */
 DROP TABLE IF EXISTS `wechat_fans_tags`;/* MySQLReback Separation */ CREATE TABLE `wechat_fans_tags` (
  `id` bigint(20) unsigned NOT NULL COMMENT '标签ID',
  `appid` char(50) DEFAULT NULL COMMENT '公众号APPID',
  `name` varchar(35) DEFAULT NULL COMMENT '标签名称',
  `count` int(11) unsigned DEFAULT NULL COMMENT '总数',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期',
  KEY `index_wechat_fans_tags_id` (`id`) USING BTREE,
  KEY `index_wechat_fans_tags_appid` (`appid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信会员标签';/* MySQLReback Separation */
 /* 创建表结构 `wechat_keys`  */
 DROP TABLE IF EXISTS `wechat_keys`;/* MySQLReback Separation */ CREATE TABLE `wechat_keys` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` char(50) DEFAULT NULL COMMENT '公众号APPID',
  `type` varchar(20) DEFAULT NULL COMMENT '类型，text 文件消息，image 图片消息，news 图文消息',
  `keys` varchar(100) DEFAULT NULL COMMENT '关键字',
  `content` text COMMENT '文本内容',
  `image_url` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `voice_url` varchar(255) DEFAULT NULL COMMENT '语音链接',
  `music_title` varchar(100) DEFAULT NULL COMMENT '音乐标题',
  `music_url` varchar(255) DEFAULT NULL COMMENT '音乐链接',
  `music_image` varchar(255) DEFAULT NULL COMMENT '音乐缩略图链接',
  `music_desc` varchar(255) DEFAULT NULL COMMENT '音乐描述',
  `video_title` varchar(100) DEFAULT NULL COMMENT '视频标题',
  `video_url` varchar(255) DEFAULT NULL COMMENT '视频URL',
  `video_desc` varchar(255) DEFAULT NULL COMMENT '视频描述',
  `news_id` bigint(20) unsigned DEFAULT NULL COMMENT '图文ID',
  `sort` bigint(20) unsigned DEFAULT '0' COMMENT '排序字段',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '0 禁用，1 启用',
  `create_by` bigint(20) unsigned DEFAULT NULL COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=' 微信关键字';/* MySQLReback Separation */
 /* 创建表结构 `wechat_menu`  */
 DROP TABLE IF EXISTS `wechat_menu`;/* MySQLReback Separation */ CREATE TABLE `wechat_menu` (
  `id` bigint(16) unsigned NOT NULL AUTO_INCREMENT,
  `index` bigint(20) DEFAULT NULL,
  `pindex` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `type` varchar(24) NOT NULL DEFAULT '' COMMENT '菜单类型 null主菜单 link链接 keys关键字',
  `name` varchar(256) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `content` text NOT NULL COMMENT '文字内容',
  `sort` bigint(20) unsigned DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(0禁用1启用)',
  `create_by` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_menu_pindex` (`pindex`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信菜单配置';/* MySQLReback Separation */
 /* 创建表结构 `wechat_news`  */
 DROP TABLE IF EXISTS `wechat_news`;/* MySQLReback Separation */ CREATE TABLE `wechat_news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` varchar(100) DEFAULT NULL COMMENT '永久素材MediaID',
  `local_url` varchar(300) DEFAULT NULL COMMENT '永久素材显示URL',
  `article_id` varchar(60) DEFAULT NULL COMMENT '关联图文ID，用，号做分割',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_by` bigint(20) DEFAULT NULL COMMENT '创建人',
  PRIMARY KEY (`id`),
  KEY `index_wechat_new_artcle_id` (`article_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信图文表';/* MySQLReback Separation */
 /* 创建表结构 `wechat_news_article`  */
 DROP TABLE IF EXISTS `wechat_news_article`;/* MySQLReback Separation */ CREATE TABLE `wechat_news_article` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '素材标题',
  `local_url` varchar(300) DEFAULT NULL COMMENT '永久素材显示URL',
  `show_cover_pic` tinyint(4) unsigned DEFAULT '0' COMMENT '是否显示封面 0不显示，1 显示',
  `author` varchar(20) DEFAULT NULL COMMENT '作者',
  `digest` varchar(300) DEFAULT NULL COMMENT '摘要内容',
  `content` longtext COMMENT '图文内容',
  `content_source_url` varchar(200) DEFAULT NULL COMMENT '图文消息原文地址',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_by` bigint(20) DEFAULT NULL COMMENT '创建人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信素材表';/* MySQLReback Separation */
 /* 创建表结构 `wechat_news_image`  */
 DROP TABLE IF EXISTS `wechat_news_image`;/* MySQLReback Separation */ CREATE TABLE `wechat_news_image` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `md5` varchar(32) DEFAULT NULL COMMENT '文件md5',
  `local_url` varchar(300) DEFAULT NULL COMMENT '本地文件链接',
  `media_url` varchar(300) DEFAULT NULL COMMENT '远程图片链接',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_news_image_md5` (`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信服务器图片';/* MySQLReback Separation */
 /* 创建表结构 `wechat_news_media`  */
 DROP TABLE IF EXISTS `wechat_news_media`;/* MySQLReback Separation */ CREATE TABLE `wechat_news_media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `appid` varchar(200) DEFAULT NULL COMMENT '公众号ID',
  `md5` varchar(32) DEFAULT NULL COMMENT '文件md5',
  `type` varchar(20) DEFAULT NULL COMMENT '媒体类型',
  `media_id` varchar(100) DEFAULT NULL COMMENT '永久素材MediaID',
  `local_url` varchar(300) DEFAULT NULL COMMENT '本地文件链接',
  `media_url` varchar(300) DEFAULT NULL COMMENT '远程图片链接',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信素材表';/* MySQLReback Separation */
 /* 创建表结构 `wechat_pay_notify`  */
 DROP TABLE IF EXISTS `wechat_pay_notify`;/* MySQLReback Separation */ CREATE TABLE `wechat_pay_notify` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(50) DEFAULT NULL COMMENT '公众号Appid',
  `bank_type` varchar(50) DEFAULT NULL COMMENT '银行类型',
  `cash_fee` bigint(20) DEFAULT NULL COMMENT '现金价',
  `fee_type` char(20) DEFAULT NULL COMMENT '币种，1人民币',
  `is_subscribe` char(1) DEFAULT 'N' COMMENT '是否关注，Y为关注，N为未关注',
  `mch_id` varchar(50) DEFAULT NULL COMMENT '商户MCH_ID',
  `nonce_str` varchar(32) DEFAULT NULL COMMENT '随机串',
  `openid` varchar(50) DEFAULT NULL COMMENT '微信用户openid',
  `out_trade_no` varchar(50) DEFAULT NULL COMMENT '支付平台退款交易号',
  `sign` varchar(100) DEFAULT NULL COMMENT '签名',
  `time_end` datetime DEFAULT NULL COMMENT '结束时间',
  `result_code` varchar(10) DEFAULT NULL,
  `return_code` varchar(10) DEFAULT NULL,
  `total_fee` varchar(11) DEFAULT NULL COMMENT '支付总金额，单位为分',
  `trade_type` varchar(20) DEFAULT NULL COMMENT '支付方式',
  `transaction_id` varchar(64) DEFAULT NULL COMMENT '订单号',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '本地系统时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_pay_notify_openid` (`openid`) USING BTREE,
  KEY `index_wechat_pay_notify_out_trade_no` (`out_trade_no`) USING BTREE,
  KEY `index_wechat_pay_notify_transaction_id` (`transaction_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付日志表';/* MySQLReback Separation */
 /* 创建表结构 `wechat_pay_prepayid`  */
 DROP TABLE IF EXISTS `wechat_pay_prepayid`;/* MySQLReback Separation */ CREATE TABLE `wechat_pay_prepayid` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `appid` char(50) DEFAULT NULL COMMENT '公众号APPID',
  `from` char(32) DEFAULT 'shop' COMMENT '支付来源',
  `fee` bigint(20) unsigned DEFAULT NULL COMMENT '支付费用(分)',
  `trade_type` varchar(20) DEFAULT NULL COMMENT '订单类型',
  `order_no` varchar(50) DEFAULT NULL COMMENT '内部订单号',
  `out_trade_no` varchar(50) DEFAULT NULL COMMENT '外部订单号',
  `prepayid` varchar(500) DEFAULT NULL COMMENT '预支付码',
  `expires_in` bigint(20) unsigned DEFAULT NULL COMMENT '有效时间',
  `transaction_id` varchar(64) DEFAULT NULL COMMENT '微信平台订单号',
  `is_pay` tinyint(1) unsigned DEFAULT '0' COMMENT '1已支付，0未支退款',
  `pay_at` datetime DEFAULT NULL COMMENT '支付时间',
  `is_refund` tinyint(1) unsigned DEFAULT '0' COMMENT '是否退款，退款单号(T+原来订单)',
  `refund_at` datetime DEFAULT NULL COMMENT '退款时间',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '本地系统时间',
  PRIMARY KEY (`id`),
  KEY `index_wechat_pay_prepayid_outer_no` (`out_trade_no`) USING BTREE,
  KEY `index_wechat_pay_prepayid_order_no` (`order_no`) USING BTREE,
  KEY `index_wechat_pay_is_pay` (`is_pay`) USING BTREE,
  KEY `index_wechat_pay_is_refund` (`is_refund`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付订单号对应表';/* MySQLReback Separation */