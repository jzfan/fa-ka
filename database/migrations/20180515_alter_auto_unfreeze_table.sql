alter table `auto_unfreeze` change `money` `money` decimal(10,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '冻结金额';