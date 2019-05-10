ALTER TABLE `order` ADD `sms_payer` TINYINT  UNSIGNED  NOT NULL  DEFAULT '0'  COMMENT '短信付费方：0买家 1商户';
ALTER TABLE `order` ADD `total_product_price` DECIMAL(10,2)  UNSIGNED  NOT NULL  DEFAULT '0'  COMMENT '商品总价（不含短信费）';
ALTER TABLE `order` CHANGE `total_price` `total_price` DECIMAL(10,2)  UNSIGNED  NOT NULL  DEFAULT '0.00'  COMMENT '总价（买家实付款）';

ALTER TABLE `goods` ADD `sms_payer` TINYINT  UNSIGNED  NOT NULL  DEFAULT '0'  COMMENT '短信付费方：0买家 1商户';
