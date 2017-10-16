ALTER TABLE `msg`
ADD COLUMN `city`  varchar(255) NULL DEFAULT 0 AFTER `createTime`;

ALTER TABLE `product_list`
ADD COLUMN `shelve_time` datetime NULL DEFAULT NULL COMMENT '产品上架时间' AFTER `refresh_time`;