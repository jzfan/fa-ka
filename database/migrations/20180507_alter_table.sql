INSERT INTO `channel` (`id`, `title`, `code`, `status`, `mch_id`, `signkey`, `appid`, `appsecret`, `gateway`, `return_url`, `notify_url`, `lowrate`, `highrate`, `costrate`, `accounting_date`, `max_money`, `min_money`, `limit_time`, `account_fields`, `polling`, `account_id`, `weight`, `updatetime`, `paytype`, `show_name`, `is_available`, `default_fields`, `applyurl`) VALUES (1, '点缀QQ扫码', 'DzQqScan', 1, '', '', '', '', '', '', '', '0.0000', '0.0000', '0.0000', 1, '0.00', '0.00', '', 'appid:appid|appsecret:appsecret', 0, 0, '[]', 0, 8, '点缀支付PC', 2, '', '');
ALTER TABLE `complaint` CHANGE `trade_no` `trade_no` CHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `user` CHANGE `money` `money` DECIMAL(10,3) UNSIGNED NOT NULL DEFAULT '0.00';
ALTER TABLE `order` CHANGE `fee` `fee` DECIMAL(10,3) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '手续费';
ALTER TABLE `order` CHANGE `agent_fee` `agent_fee` DECIMAL(10,3) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '代理佣金';
ALTER TABLE `user_money_log` CHANGE `money` `money` DECIMAL(10,3) NOT NULL COMMENT '变动金额';
ALTER TABLE `user_money_log` CHANGE `balance` `balance` DECIMAL(10,3) UNSIGNED NOT NULL COMMENT '剩余';