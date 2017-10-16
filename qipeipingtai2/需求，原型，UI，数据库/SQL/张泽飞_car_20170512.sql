/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : car

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-05-12 18:32:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `article_activity`
-- ----------------------------
DROP TABLE IF EXISTS `article_activity`;
CREATE TABLE `article_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '促销活动',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `art_ID` varchar(20) DEFAULT NULL COMMENT '活动ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `face_img` varchar(255) DEFAULT NULL COMMENT '仅支持jpg、png格式图片',
  `content` text COMMENT '正文',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='促销活动';

-- ----------------------------
-- Records of article_activity
-- ----------------------------

-- ----------------------------
-- Table structure for `article_newbie`
-- ----------------------------
DROP TABLE IF EXISTS `article_newbie`;
CREATE TABLE `article_newbie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '新手上路',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `art_ID` varchar(20) DEFAULT NULL COMMENT '问题ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '详情正文',
  `create_time` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新手上路';

-- ----------------------------
-- Records of article_newbie
-- ----------------------------

-- ----------------------------
-- Table structure for `article_news`
-- ----------------------------
DROP TABLE IF EXISTS `article_news`;
CREATE TABLE `article_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '新闻资讯',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `art_ID` varchar(20) DEFAULT NULL COMMENT '资讯ID',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `face_img` varchar(255) DEFAULT NULL COMMENT '仅支持jpg、png格式图片',
  `content` text COMMENT '正文',
  `create_time` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新闻资讯';

-- ----------------------------
-- Records of article_news
-- ----------------------------

-- ----------------------------
-- Table structure for `banner`
-- ----------------------------
DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'banner管理',
  `type` tinyint(1) DEFAULT NULL COMMENT '1顶部banner 2腰部banner',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `img` varchar(255) DEFAULT NULL COMMENT '顶部banner：仅支持jpg、png格式图片，建议尺寸w=750px，h=330px，不得超过1M\r\n腰部banner：仅支持jpg、png格式图片，建议尺寸w=1200px,h=100px不得超过1M',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `url_type` tinyint(1) DEFAULT '5' COMMENT '1促销活动 2新闻资讯 3新手上路 4外部链接 5无连接',
  `url` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='banner管理';

-- ----------------------------
-- Records of banner
-- ----------------------------

-- ----------------------------
-- Table structure for `base_ini`
-- ----------------------------
DROP TABLE IF EXISTS `base_ini`;
CREATE TABLE `base_ini` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '基础配置',
  `name` varchar(60) DEFAULT NULL COMMENT '配置名称',
  `value` text,
  `create_time` datetime DEFAULT NULL,
  `updata_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='基础配置';

-- ----------------------------
-- Records of base_ini
-- ----------------------------

-- ----------------------------
-- Table structure for `car_group`
-- ----------------------------
DROP TABLE IF EXISTS `car_group`;
CREATE TABLE `car_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '经销商及车系表',
  `type` tinyint(1) DEFAULT NULL COMMENT '1.轿车商家 2.货车商家 3.物流货运',
  `pid` int(11) DEFAULT NULL COMMENT '父级id',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `name` varchar(60) DEFAULT NULL,
  `level` tinyint(1) DEFAULT NULL COMMENT '层级 0,1,2,3,4',
  `img` varchar(0) DEFAULT NULL COMMENT '二级分类需要图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='经销商及车系表';

-- ----------------------------
-- Records of car_group
-- ----------------------------

-- ----------------------------
-- Table structure for `circle`
-- ----------------------------
DROP TABLE IF EXISTS `circle`;
CREATE TABLE `circle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '圈子及评论',
  `vid` varchar(20) DEFAULT NULL COMMENT '圈子ID',
  `content` varchar(255) DEFAULT NULL COMMENT '评论和回复的限制字数长度为200字',
  `imgs` text COMMENT '上传图片最多9张(回复不能上传图片)',
  `parent_id` int(11) DEFAULT NULL,
  `level` tinyint(3) DEFAULT NULL COMMENT '层级',
  `type` tinyint(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `comments` int(11) DEFAULT '0' COMMENT '评论数',
  `create_time` datetime DEFAULT NULL,
  `collection` int(11) DEFAULT NULL COMMENT '收藏数',
  `area` varchar(30) DEFAULT NULL COMMENT '发布城市',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='圈子及评论';

-- ----------------------------
-- Records of circle
-- ----------------------------

-- ----------------------------
-- Table structure for `collect_circle`
-- ----------------------------
DROP TABLE IF EXISTS `collect_circle`;
CREATE TABLE `collect_circle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '圈子收藏表',
  `circle_id` int(11) DEFAULT NULL COMMENT 'circle表id',
  `type` tinyint(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='圈子收藏表';

-- ----------------------------
-- Records of collect_circle
-- ----------------------------

-- ----------------------------
-- Table structure for `collect_firms`
-- ----------------------------
DROP TABLE IF EXISTS `collect_firms`;
CREATE TABLE `collect_firms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏店铺',
  `type` tinyint(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `firms_id` int(11) DEFAULT NULL COMMENT '收藏的厂商id  firms表',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收藏店铺';

-- ----------------------------
-- Records of collect_firms
-- ----------------------------

-- ----------------------------
-- Table structure for `collect_product`
-- ----------------------------
DROP TABLE IF EXISTS `collect_product`;
CREATE TABLE `collect_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏的收藏产品',
  `type` int(1) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT 'type=1:关联厂商表;\r\ntype=2:关联业务员表',
  `pro_id` int(11) DEFAULT NULL COMMENT '收藏的产品表id product_list表',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收藏产品';

-- ----------------------------
-- Records of collect_product
-- ----------------------------

-- ----------------------------
-- Table structure for `core_auth`
-- ----------------------------
DROP TABLE IF EXISTS `core_auth`;
CREATE TABLE `core_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限表',
  `modName` varchar(32) DEFAULT NULL COMMENT '模块名',
  `modCode` varchar(32) DEFAULT NULL COMMENT '模块标示',
  `modIco` varchar(20) DEFAULT NULL COMMENT '模块ico',
  `funName` varchar(32) DEFAULT NULL COMMENT '方法名',
  `funCode` varchar(32) DEFAULT NULL COMMENT '方法标示',
  `funIco` varchar(20) DEFAULT NULL COMMENT '方法ico',
  `isMenu` smallint(1) DEFAULT '1' COMMENT '模块是否用于菜单',
  `sort` smallint(3) DEFAULT '999' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of core_auth
-- ----------------------------
INSERT INTO `core_auth` VALUES ('1', '系统配置', 'plat.sys.mod', 'fa-home', '模块管理', 'mods', 'fa-gears', '1', '100');
INSERT INTO `core_auth` VALUES ('2', '管理员配置', 'plat.sys.suUser', 'fa-home', '管理员配置', 'suUser', 'fa-user-plus', '1', '101');
INSERT INTO `core_auth` VALUES ('3', 'VIP到期提醒', 'plat.firms.firms', 'fa-home', 'VIP到期提醒', 'vips', 'fa-users', '1', '102');
INSERT INTO `core_auth` VALUES ('4', '操作日志', 'plat.sys.actionLog', 'fa-home', '操作日志', 'actionLog', 'fa-users', '1', '103');
INSERT INTO `core_auth` VALUES ('5', '系统配置', 'plat.report.monthReport', 'fa-home', '经销商及车系配置', 'monthReport', 'fa-bar-chart', '1', '104');
INSERT INTO `core_auth` VALUES ('6', '系统配置', 'plat.sys.notice', 'fa-home', '公告管理', 'index', 'fa-newspaper-o', '1', '105');
INSERT INTO `core_auth` VALUES ('7', 'banner管理', 'plat.audit.reportforms', 'fa-home', '头部banner', 'suIndex', 'fa-bar-chart', '1', '1');
INSERT INTO `core_auth` VALUES ('8', 'banner管理', 'plat.supplier.visa.order', 'fa-home', '腰部banner', 'index', 'fa-cc-visa', '1', '2');
INSERT INTO `core_auth` VALUES ('9', '业务员工资配置', 'plat.audit.manager', 'fa-home', '工资记录', 'mIndex', 'fa-money', '1', '3');
INSERT INTO `core_auth` VALUES ('10', '业务员工资配置', 'plat.audit.manager', 'fa-home', '财务统计', 'gmIndex', 'fa-money', '1', '4');
INSERT INTO `core_auth` VALUES ('11', '业务员工资配置', 'plat.audit.manager', 'fa-home', '财务流水', 'fdIndex', 'fa-money', '1', '5');
INSERT INTO `core_auth` VALUES ('12', '业务员工资配置', 'plat.supplier.oversee', 'fa-home', '员工个人工资记录', 'starDetail', 'fa-shield', '1', '6');
INSERT INTO `core_auth` VALUES ('13', '业务员管理', 'Plat.sales.sales', 'fa-home', '业务员管理', 'salesIndex', 'fa-vimeo', '1', '7');
INSERT INTO `core_auth` VALUES ('14', '业务员管理', 'supplier.create', 'fa-home', '圈子记录', 'supplier.create', 'fa-shield', '1', '8');
INSERT INTO `core_auth` VALUES ('15', '业务员管理', 'plat.custom.notice', 'fa-home', '关联厂商', 'noticePage', 'fa-vimeo', '1', '9');
INSERT INTO `core_auth` VALUES ('16', '互动圈子', 'plat.supplier.lines', 'fa-home', '评论管理', 'lists', 'fa-cog', '1', '10');
INSERT INTO `core_auth` VALUES ('17', '人工收费', 'plat.supplier.order', 'fa-home', '收费记录', 'index', 'fa-shopping-cart', '1', '11');
INSERT INTO `core_auth` VALUES ('18', '厂商管理', 'plat.custom.line', 'fa-home', '来访记录(经销商)', 'customAudit', 'fa-edit', '1', '12');
INSERT INTO `core_auth` VALUES ('19', '厂商管理', 'plat.supplier.visaProduct', 'fa-home', '求购记录', 'auditIndex', 'fa-edit', '1', '13');
INSERT INTO `core_auth` VALUES ('20', '厂商管理', 'plat.wechat.banner', 'fa-home', '产品信息', 'banner', 'fa-cog', '1', '14');
INSERT INTO `core_auth` VALUES ('21', '厂商管理', 'plat.sys.coupon', 'fa-home', '访问记录', 'couponIndex', 'fa-map', '1', '15');
INSERT INTO `core_auth` VALUES ('22', '厂商管理', 'plat.product.line', 'fa-home', '基础信息', 'lists', 'fa-edit', '1', '16');
INSERT INTO `core_auth` VALUES ('23', '厂商管理', 'plat.wechat.tags', 'fa-home', '刷新点记录', 'tagsIndex', 'fa-tag', '1', '17');
INSERT INTO `core_auth` VALUES ('24', '厂商管理', 'plat.custom.demand', 'fa-home', '关联业务员', 'suDemandIndex', 'fa-vimeo', '1', '18');
INSERT INTO `core_auth` VALUES ('25', '厂商管理', 'plat.product.line', 'fa-home', '圈子记录', 'price', 'fa-edit', '1', '19');
INSERT INTO `core_auth` VALUES ('26', '厂商管理', 'plat.custom.demand', 'fa-home', '认证信息', 'zbDemandPage', 'fa-edit', '1', '20');
INSERT INTO `core_auth` VALUES ('27', '厂商管理', 'plat.product.line', 'fa-home', '邀请记录', 'show', 'fa-edit', '1', '21');
INSERT INTO `core_auth` VALUES ('28', '厂商管理', 'plat.wechat.tags', 'fa-home', 'VIP记录(经销商)', 'tagsAuth', 'fa-tag', '1', '22');
INSERT INTO `core_auth` VALUES ('29', '推送消息', 'plat.supplier.notice', 'fa-home', '推送记录', 'noticePage', 'fa-envelope', '1', '23');
INSERT INTO `core_auth` VALUES ('30', '文章管理', 'plat.supplier.visaProduct', 'fa-home', '查看文章', 'index', 'fa-cc-visa', '1', '24');
INSERT INTO `core_auth` VALUES ('31', '文章管理', 'plat.wechat.attention', 'fa-home', '添加/编辑文章', 'showDataPage', 'fa-user-secret', '1', '25');
INSERT INTO `core_auth` VALUES ('32', '文章管理', 'plat.wechat.honBao', 'fa-home', '新手上路', 'showListToPage', 'fa-money', '1', '26');
INSERT INTO `core_auth` VALUES ('33', '文章管理', 'plat.wechat.honBao', 'fa-home', '其他', 'showListPage', 'fa-money', '1', '27');
INSERT INTO `core_auth` VALUES ('34', '求购记录', 'plat.supplier.manager', 'fa-home', '查看记录', 'index', 'fa-map', '1', '28');
INSERT INTO `core_auth` VALUES ('35', '活动营销', 'plat.supplier.lines', 'fa-home', 'PC友情链接配置', 'leader', 'fa-shield', '1', '29');
INSERT INTO `core_auth` VALUES ('36', '活动营销', 'plat.report.monthReport', 'fa-home', 'PC推荐经销商', 'monthReportChild', 'fa-bar-chart', '1', '30');
INSERT INTO `core_auth` VALUES ('37', '统计分析-厂商统计', 'plat.supplier.visaProduct', 'fa-home', '圈子统计', 'visaMaterialIndex', 'fa-cc-visa', '1', '31');
INSERT INTO `core_auth` VALUES ('38', '统计分析-厂商统计', 'plat.supplier', 'fa-home', '活跃度统计', 'auth', 'fa-shield', '1', '32');
INSERT INTO `core_auth` VALUES ('39', '统计分析-厂商统计', 'plat.supplier.lines', 'fa-home', '访问统计', 'show', 'fa-shield', '1', '33');
INSERT INTO `core_auth` VALUES ('40', '统计分析-厂商统计', 'plat.audit.report', 'fa-home', '产品统计', 'suIndex', 'fa-money', '0', '34');
INSERT INTO `core_auth` VALUES ('41', '认证申请', 'plat.audit.reportforms', 'fa-home', '认证申请', 'suDataPage', 'fa-bar-chart', '0', '35');
INSERT INTO `core_auth` VALUES ('42', '系统配置', null, 'fa-home', 'test', null, 'fa-shield', '0', '36');
INSERT INTO `core_auth` VALUES ('43', '系统配置', null, 'fa-home', 'test', null, 'fa-shield', '0', '37');

-- ----------------------------
-- Table structure for `core_auth_copy`
-- ----------------------------
DROP TABLE IF EXISTS `core_auth_copy`;
CREATE TABLE `core_auth_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限表',
  `modName` varchar(32) DEFAULT NULL COMMENT '模块名',
  `modCode` varchar(32) DEFAULT NULL COMMENT '模块标示',
  `modIco` varchar(20) DEFAULT NULL COMMENT '模块ico',
  `funName` varchar(32) DEFAULT NULL COMMENT '方法名',
  `funCode` varchar(32) DEFAULT NULL COMMENT '方法标示',
  `funIco` varchar(20) DEFAULT NULL COMMENT '方法ico',
  `isMenu` smallint(1) DEFAULT '1' COMMENT '模块是否用于菜单',
  `sort` smallint(3) DEFAULT '999' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of core_auth_copy
-- ----------------------------
INSERT INTO `core_auth_copy` VALUES ('1', '系统配置', 'plat.sys.suUser', 'fa-gears', '管理员配置', 'suUser', 'fa-user-plus', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('2', '系统配置', 'plat.sys.job', 'fa-gears', '操作日志', 'job', 'fa-users', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('3', '系统配置', 'plat.sys.auth', 'fa-gears', 'VIP到期提醒', 'auth', 'fa-users', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('4', '系统配置', 'plat.sys.mod', 'fa-gears', '模块管理', 'mods', 'fa-gears', '1', '5');
INSERT INTO `core_auth_copy` VALUES ('6', '未使用', 'plat.store.sale.line', 'fa-shopping-cart', '线路-销售', 'lists', 'fa-shopping-cart', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('7', '未使用', 'plat.store', 'fa-shopping-cart', '子门市全部订单', 'leader', 'fa-shield', '0', '666');
INSERT INTO `core_auth_copy` VALUES ('8', '未使用', 'plat.store.order', 'fa-shopping-cart', '线路-订单', 'index', 'fa-shopping-cart', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('10', '未使用', 'plat.store.manager', 'fa-shopping-cart', '门市管理', 'index', 'fa-map', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('11', '未使用', 'plat.server.handle', 'fa-shield', '投诉处理', 'handle', 'fa-warning', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('12', '未使用', 'plat.server.oversee', 'fa-shield', '质量跟踪', 'oversee', 'fa-shield', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('13', '求购记录', 'plat.supplier.manager', 'fa-user-secret', '查看记录', 'index', 'fa-map', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('14', '互动圈子', 'plat.supplier.lines', 'fa-user-secret', '评论管理', 'lists', 'fa-cog', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('15', '人工收费', 'plat.supplier.order', 'fa-user-secret', '收费记录', 'index', 'fa-shopping-cart', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('16', '统计分析-厂商统计', 'plat.supplier', 'fa-user-secret', '活跃度统计', 'auth', 'fa-shield', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('18', '统计分析-厂商统计', 'plat.supplier.lines', 'fa-user-secret', '访问统计', 'show', 'fa-shield', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('20', '未使用', 'plat.report.order', 'fa-bar-chart', '线路-订单台帐', 'index', 'fa-bar-chart', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('21', '活动营销', 'plat.supplier.lines', 'fa-user-secret', 'PC友情链接配置', 'leader', 'fa-shield', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('22', '未使用', 'plat.base.dest', 'fa-cog', '线路-目的地', 'index', 'fa-cog', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('23', '未使用', 'plat.base.dest', null, '线路-目的地类型', 'type', 'fa-shield', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('24', '未使用', 'plat.base.city', 'fa-cog', '线路-出发城市', 'index', 'fa-cog', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('25', '未使用', 'plat.base.subject', 'fa-cog', '线路-旅游专题', 'index', 'fa-cog', '0', '0');
INSERT INTO `core_auth_copy` VALUES ('26', '厂商管理', 'plat.product.line', 'fa-edit', '基础信息', 'lists', 'fa-edit', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('27', '厂商管理', 'plat.product.line', 'fa-edit', '邀请记录', 'show', 'fa-edit', '1', '555');
INSERT INTO `core_auth_copy` VALUES ('28', '厂商管理', 'plat.product.line', 'fa-edit', '圈子记录', 'price', 'fa-edit', '1', '555');
INSERT INTO `core_auth_copy` VALUES ('29', '未使用', 'plat.store', 'fa-shopping-cart', '本门市全部订单', 'leader', 'fa-shield', '0', '666');
INSERT INTO `core_auth_copy` VALUES ('30', '业务员工资配置', 'plat.audit.manager', 'fa-money', '工资记录', 'mIndex', 'fa-money', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('31', '业务员工资配置', 'plat.audit.manager', 'fa-money', '财务统计', 'gmIndex', 'fa-money', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('32', '业务员工资配置', 'plat.audit.manager', 'fa-money', '财务流水', 'fdIndex', 'fa-money', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('33', '未使用', 'plat.audit.manager', 'fa-money', '线-会计结款', 'acctIndex', 'fa-money', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('34', '未使用', 'plat.audit.report', 'fa-bar-chart', '线路-结算报表', 'index', 'fa-bar-chart', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('38', '统计分析-厂商统计', 'plat.audit.report', 'fa-user-secret', '产品统计', 'suIndex', 'fa-money', '1', '7');
INSERT INTO `core_auth_copy` VALUES ('39', '系统配置', 'plat.audit.manager', 'fa-user-secret', '产品分类配置', 'suList', 'fa-bar-chart', '1', '6');
INSERT INTO `core_auth_copy` VALUES ('40', '系统配置', 'plat.sys.notice', 'fa-gears', '公告管理', 'index', 'fa-newspaper-o', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('41', '未使用', 'plat.report.monthReport', 'fa-bar-chart', '线路-月统计报表', 'monthReport', 'fa-bar-chart', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('42', '系统配置', 'plat.report.monthReport', 'fa-bar-chart', '经销商及车系配置', 'monthReport', 'fa-bar-chart', '1', '5');
INSERT INTO `core_auth_copy` VALUES ('43', '活动营销', 'plat.report.monthReport', 'fa-bar-chart', 'PC推荐经销商', 'monthReportChild', 'fa-bar-chart', '1', '666');
INSERT INTO `core_auth_copy` VALUES ('47', '未使用', 'plat.audit.manager', 'fa-bar-chart', '线路-供应商对账单', 'suList', 'fa-bar-chart', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('48', '未使用', 'plat.contract.manager', 'fa-file-text-o', '合同创建', 'createIndex', 'fa-file-text-o', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('49', '未使用', 'plat.contract.manager', 'fa-file-text-o', '合同审核', 'auditIndex', 'fa-file-text-o', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('50', '未使用', 'plat.report.commission', 'fa-bar-chart', '线路-代理手续费', 'commission', 'fa-bar-chart', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('51', '未使用', 'plat.contract.invoice', 'fa-file-text-o', '发票审核', 'auditIndex', 'fa-file-text-o', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('52', '未使用', 'plat.contract.invoice', 'fa-file-text-o', '发票开据', 'billingIndex', 'fa-file-text-o', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('53', '未使用', 'plat.report.order', 'fa-legal', '线路-财务退款', 'tuiList', 'fa-legal', '0', '2');
INSERT INTO `core_auth_copy` VALUES ('54', '推送消息', 'plat.supplier.notice', 'fa-user-secret', '推送记录', 'noticePage', 'fa-envelope', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('55', '未使用', 'plat.report.detail', 'fa-bar-chart', '线路-交易明细', 'index', 'fa-bar-chart', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('56', '厂商管理', 'plat.wechat.banner', 'fa-gears', '产品信息', 'banner', 'fa-cog', '1', '1');
INSERT INTO `core_auth_copy` VALUES ('57', '厂商管理', 'plat.wechat.tags', 'fa-gears', '刷新点记录', 'tagsIndex', 'fa-tag', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('58', '未使用', 'plat.custom.demand', 'fa-bar-chart', '定制游-需求', 'demandIndex', 'fa-vimeo', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('59', '厂商管理', 'plat.custom.demand', 'fa-user-secret', '关联业务员', 'suDemandIndex', 'fa-vimeo', '1', '8');
INSERT INTO `core_auth_copy` VALUES ('60', '厂商管理', 'plat.custom.line', 'fa-edit', '来访记录(经销商)', 'customAudit', 'fa-edit', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('61', '厂商管理', 'plat.sys.coupon', 'fa-gears', '访问记录', 'couponIndex', 'fa-map', '1', '3');
INSERT INTO `core_auth_copy` VALUES ('62', '未使用', 'plat.custom.order', 'fa-shopping-cart', '定制游-订单', 'stOrderPage', 'fa-vimeo', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('63', '业务员管理', 'plat.custom.order', 'fa-bar-chart', '基础信息', 'suOrderPage', 'fa-vimeo', '1', '9');
INSERT INTO `core_auth_copy` VALUES ('64', '未使用', 'plat.custom.order', 'fa-legal', '定制游-财务退款', 'tuiPage', 'fa-legal', '0', '4');
INSERT INTO `core_auth_copy` VALUES ('65', '未使用', 'plat.audit.audit', 'fa-money', '经理审核', 'mIndex', 'fa-money', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('66', '未使用', 'plat.audit.audit', 'fa-money', '总经理审核', 'gmIndex', 'fa-money', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('67', '未使用', 'plat.audit.audit', 'fa-money', '财务审核', 'fdIndex', 'fa-money', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('68', '未使用', 'plat.audit.audit', 'fa-money', '会计结款', 'acctIndex', 'fa-money', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('69', 'banner管理', 'plat.audit.reportforms', 'fa-user-secret', '底部banner', 'suIndex', 'fa-bar-chart', '1', '15');
INSERT INTO `core_auth_copy` VALUES ('70', '未使用', 'plat.audit.reportforms', 'fa-user-secret', '供应商-结算报表', 'index', 'fa-bar-chart', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('71', '未使用', 'plat.store.visa.order', 'fa-shopping-cart', '签证-订单', 'index', 'fa-cc-visa', '0', '9');
INSERT INTO `core_auth_copy` VALUES ('72', '未使用', 'plat.base.visaDest', 'fa-cog', '签证-目的地', 'index', 'fa-cog', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('73', '未使用', 'plat.base.visaPersonType', 'fa-cog', '签证-人群类型', 'index', 'fa-cog', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('74', '未使用', 'plat.base.visaPapersType', 'fa-cog', '签证-类型', 'index', 'fa-cog', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('75', '未使用', 'plat.base.visaMaterialTemplate', 'fa-cog', '签证-材料模板', 'index', 'fa-cog', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('76', '未使用', 'plat.store.sale.visa', 'fa-bar-chart', '签证-办理', 'listPage', 'fa-cc-visa', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('78', '未使用', 'plat.report.visa.order', 'fa-bar-chart', '签证-订单台帐', 'index', 'fa-bar-chart', '0', '9');
INSERT INTO `core_auth_copy` VALUES ('79', '业务员管理', 'plat.custom.notice', 'fa-user-secret', '关联厂商', 'noticePage', 'fa-vimeo', '1', '10');
INSERT INTO `core_auth_copy` VALUES ('80', '文章管理', 'plat.supplier.visaProduct', 'fa-user-secret', '查看文章', 'index', 'fa-cc-visa', '1', '12');
INSERT INTO `core_auth_copy` VALUES ('81', '厂商管理', 'plat.supplier.visaProduct', 'fa-edit', '求购记录', 'auditIndex', 'fa-edit', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('82', '厂商管理', 'plat.custom.demand', 'fa-edit', '认证信息', 'zbDemandPage', 'fa-edit', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('83', 'banner管理', 'plat.supplier.visa.order', 'fa-cc-visa', '头部banner', 'index', 'fa-cc-visa', '1', '13');
INSERT INTO `core_auth_copy` VALUES ('84', '未使用', 'plat.custom.order', 'fa-legal', '定制游-退款审核', 'tuiAuditPage', 'fa-legal', '0', '3');
INSERT INTO `core_auth_copy` VALUES ('85', '文章管理', 'plat.wechat.attention', 'fa-gears', '添加/编辑文章', 'showDataPage', 'fa-user-secret', '1', '4');
INSERT INTO `core_auth_copy` VALUES ('86', '未使用', 'plat.report.custom', 'fa-user-secret', '定制游-订单', 'index', 'fa-bar-chart', '0', '7');
INSERT INTO `core_auth_copy` VALUES ('87', '未使用', 'plat.report.order', 'fa-legal', '线路-退款审核', 'tuiApply', 'fa-legal', '0', '1');
INSERT INTO `core_auth_copy` VALUES ('88', '未使用', 'plat.report.visa.order', 'fa-legal', '签证-退款审核', 'refundApply', 'fa-legal', '0', '5');
INSERT INTO `core_auth_copy` VALUES ('89', '未使用', 'plat.report.visa.order', 'fa-legal', '签证-财务退款', 'refundMoneyPage', 'fa-legal', '0', '6');
INSERT INTO `core_auth_copy` VALUES ('90', '未使用', 'plat.server.baoXian', 'fa-shield', '保险查看', 'showPage', 'fa-medkit', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('91', '文章管理', 'plat.wechat.honBao', 'fa-gears', '其他', 'showListPage', 'fa-money', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('92', '文章管理', 'plat.wechat.honBao', 'fa-gears', '新手上路', 'showListToPage', 'fa-money', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('93', '厂商管理', 'plat.wechat.tags', 'fa-gears', 'VIP记录(经销商)', 'tagsAuth', 'fa-tag', '1', '2');
INSERT INTO `core_auth_copy` VALUES ('94', '未使用', 'plat.report.channel', 'fa-user-secret', '渠道销售数据', 'saleIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('95', '未使用', 'plat.report.channel', 'fa-user-secret', '网点月度统计-端口', 'MonthPortIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('96', '未使用', 'plat.report.channel', 'fa-user-secret', '网点月度统计-产品', 'MonthProTypeIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('97', '未使用', 'plat.report.channel', 'fa-user-secret', '网点月度统计-维码', 'QRCoderIndex', 'fa-bar-chart', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('98', '统计分析-厂商统计', 'plat.supplier.visaProduct', 'fa-user-secret', '圈子统计', 'visaMaterialIndex', 'fa-cc-visa', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('99', '未使用', 'plat.server.oversee', 'fa-shield', '旅游星级统计', 'star', 'fa-shield', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('100', '业务员工资配置', 'plat.supplier.oversee', 'fa-shield', '员工个人工资记录', 'starDetail', 'fa-shield', '1', '999');
INSERT INTO `core_auth_copy` VALUES ('101', '认证申请', 'plat.audit.reportforms', 'fa-user-secret', '认证记录', 'suDataPage', 'fa-bar-chart', '1', '15');
INSERT INTO `core_auth_copy` VALUES ('102', '未使用', 'plat.audit.reportforms', 'fa-user-secret', '供应商-结算对账单', 'suDataPage', 'fa-bar-chart', '0', '8');
INSERT INTO `core_auth_copy` VALUES ('103', '未使用', 'store.create', '', '门店创建', 'store.create', '', '0', '9');
INSERT INTO `core_auth_copy` VALUES ('104', '业务员管理', 'supplier.create', 'fa-shield', '圈子记录', 'supplier.create', 'fa-shield', '1', '10');
INSERT INTO `core_auth_copy` VALUES ('105', '未使用', 'plat.report.plane', 'fa-bar-chart', '送机报表', 'planeList', 'fa-plane', '0', '999');
INSERT INTO `core_auth_copy` VALUES ('106', '系统配置', 'plat.sys.ini', 'fa-gears', '系统配置', 'index', 'fa-gear', '1', '6');

-- ----------------------------
-- Table structure for `core_user_auth`
-- ----------------------------
DROP TABLE IF EXISTS `core_user_auth`;
CREATE TABLE `core_user_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '职位-权限管理表',
  `userId` int(11) DEFAULT NULL COMMENT '用户id',
  `authId` int(11) DEFAULT NULL COMMENT '权限id',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `authId` (`authId`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=18838 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of core_user_auth
-- ----------------------------
INSERT INTO `core_user_auth` VALUES ('18836', '2', '8');
INSERT INTO `core_user_auth` VALUES ('18835', '2', '7');
INSERT INTO `core_user_auth` VALUES ('18834', '2', '6');
INSERT INTO `core_user_auth` VALUES ('18833', '2', '4');
INSERT INTO `core_user_auth` VALUES ('18807', '5', '1');
INSERT INTO `core_user_auth` VALUES ('18808', '5', '2');
INSERT INTO `core_user_auth` VALUES ('18809', '6', '1');
INSERT INTO `core_user_auth` VALUES ('18810', '6', '2');
INSERT INTO `core_user_auth` VALUES ('18811', '6', '3');
INSERT INTO `core_user_auth` VALUES ('18812', '6', '4');
INSERT INTO `core_user_auth` VALUES ('18813', '6', '40');
INSERT INTO `core_user_auth` VALUES ('18814', '6', '106');
INSERT INTO `core_user_auth` VALUES ('18815', '7', '1');
INSERT INTO `core_user_auth` VALUES ('18816', '7', '2');
INSERT INTO `core_user_auth` VALUES ('18817', '7', '3');
INSERT INTO `core_user_auth` VALUES ('18818', '7', '4');
INSERT INTO `core_user_auth` VALUES ('18819', '7', '40');
INSERT INTO `core_user_auth` VALUES ('18820', '7', '106');
INSERT INTO `core_user_auth` VALUES ('18821', '8', '1');
INSERT INTO `core_user_auth` VALUES ('18822', '8', '2');
INSERT INTO `core_user_auth` VALUES ('18823', '8', '3');
INSERT INTO `core_user_auth` VALUES ('18824', '8', '4');
INSERT INTO `core_user_auth` VALUES ('18825', '8', '40');
INSERT INTO `core_user_auth` VALUES ('18826', '8', '106');
INSERT INTO `core_user_auth` VALUES ('18832', '2', '3');
INSERT INTO `core_user_auth` VALUES ('18837', '2', '17');

-- ----------------------------
-- Table structure for `firms`
-- ----------------------------
DROP TABLE IF EXISTS `firms`;
CREATE TABLE `firms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '厂商表',
  `EnterpriseID` varchar(10) NOT NULL COMMENT '企业ID(唯一)',
  `phone` varchar(16) DEFAULT NULL COMMENT '手机号(企业账号)',
  `password` varchar(255) DEFAULT NULL COMMENT '企业登录密码(默认7777777)',
  `type` tinyint(1) DEFAULT NULL COMMENT '1.经销商  2.修理厂',
  `classification` tinyint(1) DEFAULT NULL COMMENT '企业分类 (和type字段相关)\r\n经销商：1.轿车商家 2.货车商家 3.物流货运\r\n汽修厂：4.修理厂    5.快修保养 6.美容店',
  `business` varchar(255) DEFAULT NULL COMMENT '经营范围(当type=1，经销商使用)\r\n和经销商及车系表car_group关联\r\n1个经销商自己最多可以选择3个二级分类经营范围，但后台可配置多个，前端进行动态布局\r\n车系是指同一个经销商下是有4级分类的，只是在用户界面只提供到2级的筛选，该2级就是经营范围，最多可以选3个，但经营范围下的3、4级就不限数量',
  `is_showfactry` tinyint(1) DEFAULT NULL COMMENT '汽修厂轨迹权限  1有 2无',
  `scale` varchar(4) DEFAULT NULL COMMENT '企业规模(大，中，小)(当type=2，修理厂使用)',
  `companyname` varchar(60) DEFAULT NULL COMMENT '企业名称',
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `city` varchar(20) DEFAULT NULL COMMENT '区',
  `district` varchar(20) DEFAULT NULL COMMENT '区',
  `coordinate` varchar(30) DEFAULT NULL COMMENT '企业坐标',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `face_pic` varchar(255) DEFAULT NULL COMMENT '封面  建议200*200px',
  `major` varchar(255) DEFAULT NULL COMMENT '主营',
  `linkMan` varchar(30) DEFAULT NULL COMMENT '联系人',
  `linkPhone` varchar(255) DEFAULT NULL COMMENT '联系手机号(可填多个)',
  `linkTel` varchar(255) DEFAULT NULL COMMENT '座机(可填多个)',
  `qq` varchar(255) DEFAULT NULL COMMENT 'QQ号(可填多个)',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `last_time` datetime DEFAULT NULL COMMENT '最近一次登陆时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态 1.正常 2.禁用',
  `salesman_ids` varchar(255) DEFAULT NULL COMMENT '关联业务员id(sales_user表)，例如:  ,1,2,31,',
  `wechat_pic` varchar(255) DEFAULT NULL COMMENT '微信二维码',
  `info` text COMMENT '企业介绍',
  `is_vip` tinyint(1) DEFAULT NULL COMMENT '是否vip   1是  2否 (汽修厂没有VIP会员)',
  `is_check` tinyint(1) DEFAULT NULL COMMENT '是否认证  1是 2否',
  `refresh_point` int(11) DEFAULT NULL COMMENT '刷新点',
  `is_sales` tinyint(1) DEFAULT '0' COMMENT '是否是推荐经销商 1是 0否',
  `refresh_time` datetime DEFAULT NULL COMMENT '刷新点时间',
  `invite_code` varchar(30) DEFAULT NULL COMMENT '邀请码(唯一)',
  `vip_time` datetime DEFAULT NULL COMMENT 'vip到期时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `EnterpriseID` (`EnterpriseID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='厂商表';

-- ----------------------------
-- Records of firms
-- ----------------------------
INSERT INTO `firms` VALUES ('1', '464646', '135513', '2342342342', '1', '1', '1', '1', null, '青羊汽贸', '四川', '成都', '青羊区', '[464,464]', 'asfsdgfd', null, '玛莎拉蒂', '战神', '135513', '234', '356346', '2017-05-12 10:48:13', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-13 11:03:59');
INSERT INTO `firms` VALUES ('2', '12314324', '46456', null, '1', '2', '2', '2', null, '成华汽贸', '四川', '成都', '成华区', '[464,464]', 'gdf', null, '兰博基尼', '粉丝', '2532', '53', '6456', '2017-05-12 10:48:15', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-18 11:04:02');
INSERT INTO `firms` VALUES ('5', '12312', '464', null, '1', '3', '3', '1', null, '超音速汽贸', '四川', null, null, '[464,464]', 'gsdfgs', null, '大众', '大华股份', '523', '4546', '45', '2017-05-12 10:48:17', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-27 11:04:06');
INSERT INTO `firms` VALUES ('6', '234234', '23424', null, '2', '1', '4', '2', '大', '武侯汽修', '四川', '成都', '武侯区', '[464,464]', 'g', null, '北京现代', '三哥vf', '42', '4564', '363', '2017-05-12 10:48:20', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-20 11:04:10');
INSERT INTO `firms` VALUES ('7', '234242', '24234534', null, '2', '2', '1', '1', '中', '特快汽修', '四川', '成都', null, '[464,464]', 'dsfgsg', null, '起亚,北京现代', '荣达', '3423', '5353', '346', '2017-05-12 10:48:23', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-18 11:04:13');
INSERT INTO `firms` VALUES ('8', '34252', '65765', null, '2', '3', '2', '2', '小', '金堂汽修', '四川', '成都', '金堂县', '[464,464]', 'sfg', null, '奔驰,宝马,保时捷,劳斯莱斯,起亚,北京现代', '津树多枫橘', '4234', '3453', '345', '2017-05-12 10:48:25', null, '2017-05-12 10:48:25', '1', null, null, null, '1', null, null, '0', null, null, '2017-05-19 13:51:19');

-- ----------------------------
-- Table structure for `firms_banner`
-- ----------------------------
DROP TABLE IF EXISTS `firms_banner`;
CREATE TABLE `firms_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '经销商banner',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `banner_url` varchar(255) DEFAULT NULL COMMENT '建议750*310px',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='经销商banner';

-- ----------------------------
-- Records of firms_banner
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_call_log`
-- ----------------------------
DROP TABLE IF EXISTS `firms_call_log`;
CREATE TABLE `firms_call_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '拨打记录',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `to_firms_id` int(11) DEFAULT NULL COMMENT '拨打的厂商id',
  `create_time` datetime DEFAULT NULL,
  `call_type` tinyint(1) DEFAULT NULL COMMENT '1电话  2qq',
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示，1显示 2不显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='厂商拨打记录';

-- ----------------------------
-- Records of firms_call_log
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_card`
-- ----------------------------
DROP TABLE IF EXISTS `firms_card`;
CREATE TABLE `firms_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '企业名片',
  `firms_type` tinyint(1) DEFAULT NULL COMMENT '厂商类型，1经销商  2修理厂',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `firms_name` varchar(255) DEFAULT NULL COMMENT '企业名称',
  `firms_linkMan` varchar(60) DEFAULT NULL COMMENT '联系人',
  `firms_phone` varchar(255) DEFAULT NULL COMMENT '手机号（多个用 , 分开）',
  `firms_tel` varchar(255) DEFAULT NULL COMMENT '电话号（多个用 , 分开）',
  `firms_QQ` varchar(255) DEFAULT NULL COMMENT 'QQ（多个用 , 分开）',
  `firms_address` varchar(255) DEFAULT NULL COMMENT '地址',
  `firms_QR` varchar(255) DEFAULT NULL COMMENT '二维码',
  `create_time` datetime DEFAULT NULL,
  `template_type` int(11) DEFAULT NULL,
  `main_icon_1` varchar(255) DEFAULT NULL COMMENT '主营图标1 （经销商）',
  `main_icon_2` varchar(255) DEFAULT NULL COMMENT '主营图标2（经销商）',
  `main_icon_3` varchar(255) DEFAULT NULL COMMENT '主营图标3（经销商）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='企业名片';

-- ----------------------------
-- Records of firms_card
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_check`
-- ----------------------------
DROP TABLE IF EXISTS `firms_check`;
CREATE TABLE `firms_check` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '企业认证申请记录表',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `firmsName` varchar(60) DEFAULT NULL COMMENT '公司全称',
  `firmsMan` varchar(255) DEFAULT NULL COMMENT '联系人',
  `firmsTel` varchar(16) DEFAULT NULL COMMENT '手机号码',
  `province` varchar(20) DEFAULT NULL COMMENT '省',
  `city` varchar(20) DEFAULT NULL COMMENT '市',
  `district` varchar(20) DEFAULT NULL COMMENT '区',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `licence_pic` varchar(255) DEFAULT NULL COMMENT '营业执照',
  `taxes_pic` varchar(255) DEFAULT NULL COMMENT '纳税认证',
  `field_pic` varchar(255) DEFAULT NULL COMMENT '实地认证',
  `brand_pic` varchar(255) DEFAULT NULL COMMENT '商标认证',
  `agents_pic` varchar(255) DEFAULT NULL COMMENT '产品代理认证',
  `create_time` datetime DEFAULT NULL COMMENT '申请时间',
  `update_time` datetime DEFAULT NULL,
  `audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '审核状态，1待审 2通过 3拒绝',
  `reason` varchar(255) DEFAULT NULL COMMENT '审核被拒绝的原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='企业认证申请记录表';

-- ----------------------------
-- Records of firms_check
-- ----------------------------

-- ----------------------------
-- Table structure for `firms_sales_user`
-- ----------------------------
DROP TABLE IF EXISTS `firms_sales_user`;
CREATE TABLE `firms_sales_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '厂商业务员关联表',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `sales_user_di` int(11) DEFAULT NULL COMMENT '业务员id',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='厂商业务员关联表';

-- ----------------------------
-- Records of firms_sales_user
-- ----------------------------
INSERT INTO `firms_sales_user` VALUES ('1', '1', '1', null);
INSERT INTO `firms_sales_user` VALUES ('2', '1', '1', null);
INSERT INTO `firms_sales_user` VALUES ('3', '2', '2', null);
INSERT INTO `firms_sales_user` VALUES ('4', '3', '3', null);
INSERT INTO `firms_sales_user` VALUES ('5', '1', '2', null);

-- ----------------------------
-- Table structure for `firms_visit_log`
-- ----------------------------
DROP TABLE IF EXISTS `firms_visit_log`;
CREATE TABLE `firms_visit_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '厂商访问记录',
  `firms_id` int(11) DEFAULT NULL COMMENT '本厂商id (没有为0)',
  `to_firms_id` int(11) DEFAULT NULL COMMENT '访问的厂商id',
  `create_time` datetime DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示，1显示 2不显示',
  `visit_type` tinyint(1) DEFAULT NULL COMMENT '访问终端（1PC web端  2移动端）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='厂商访问记录';

-- ----------------------------
-- Records of firms_visit_log
-- ----------------------------

-- ----------------------------
-- Table structure for `friendly_link`
-- ----------------------------
DROP TABLE IF EXISTS `friendly_link`;
CREATE TABLE `friendly_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'pc友情链接',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `vname` varchar(20) DEFAULT NULL COMMENT '名称',
  `vurl` varchar(255) DEFAULT NULL COMMENT '友情链接',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='pc友情链接';

-- ----------------------------
-- Records of friendly_link
-- ----------------------------

-- ----------------------------
-- Table structure for `invite_log`
-- ----------------------------
DROP TABLE IF EXISTS `invite_log`;
CREATE TABLE `invite_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '邀请记录',
  `type` int(11) DEFAULT NULL COMMENT '用户类型 1企业厂商，2业务员',
  `fu_id` int(11) DEFAULT NULL COMMENT '邀请人id：\r\ntype=1:关联厂商表;\r\ntype=2:关联业务员表',
  `firms_id` int(11) DEFAULT NULL COMMENT '被邀请厂商id',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邀请记录';

-- ----------------------------
-- Records of invite_log
-- ----------------------------

-- ----------------------------
-- Table structure for `notice`
-- ----------------------------
DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '推送消息',
  `msg` varchar(255) DEFAULT NULL COMMENT '请简要说明，限制100字',
  `start_time` datetime DEFAULT NULL COMMENT '发布时间',
  `create_time` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='推送消息';

-- ----------------------------
-- Records of notice
-- ----------------------------

-- ----------------------------
-- Table structure for `pay_history`
-- ----------------------------
DROP TABLE IF EXISTS `pay_history`;
CREATE TABLE `pay_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '收费记录表',
  `type` tinyint(1) DEFAULT NULL COMMENT '充值类型  1充值VIP  2充值刷新点 3刷新点消费 4获取新点消费',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `money` decimal(10,2) DEFAULT NULL COMMENT '充值金额（元）',
  `refresh_point` int(11) DEFAULT NULL COMMENT '刷新点数  +100或-100',
  `info` varchar(255) DEFAULT NULL COMMENT '详情',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id 没有为0',
  `payway` tinyint(4) DEFAULT NULL COMMENT '支付方式 1微信支付  2支付宝支付 3人工收费（当type=3刷新点消费 4获取新点消费 为0）',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态 1成功 2失败',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收费记录表';

-- ----------------------------
-- Records of pay_history
-- ----------------------------

-- ----------------------------
-- Table structure for `product_category`
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品分类表',
  `pid` int(11) DEFAULT NULL COMMENT '父级ID',
  `vid` int(11) DEFAULT NULL COMMENT '序号',
  `name` varchar(60) DEFAULT NULL COMMENT '分级名称',
  `level` tinyint(1) DEFAULT NULL COMMENT '层级',
  `img` varchar(255) DEFAULT NULL COMMENT '二级分类需要图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品分类表';

-- ----------------------------
-- Records of product_category
-- ----------------------------

-- ----------------------------
-- Table structure for `product_list`
-- ----------------------------
DROP TABLE IF EXISTS `product_list`;
CREATE TABLE `product_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品表',
  `proId` varchar(20) DEFAULT NULL COMMENT ' 产品ID(唯一)',
  `proName` varchar(60) DEFAULT NULL COMMENT '产品名称',
  `pro_type` varchar(10) DEFAULT NULL COMMENT '产品类别(新品促销，库存清仓)',
  `pro_cate_1` int(11) DEFAULT NULL COMMENT '关联product_category的一级类别',
  `pro_cate_2` int(11) DEFAULT NULL COMMENT '关联product_category的二级类别',
  `pro_price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `pro_refresh` int(11) DEFAULT NULL COMMENT '今日刷新数',
  `firms_id` int(11) DEFAULT NULL COMMENT '厂商id',
  `pro_status` tinyint(1) DEFAULT NULL COMMENT '状态  1上架中 2未上架',
  `pro_no` varchar(30) DEFAULT NULL COMMENT '厂商编码',
  `pro_brand` varchar(255) DEFAULT NULL COMMENT '产品品牌',
  `pro_area` varchar(255) DEFAULT NULL COMMENT '产品产地',
  `pro_weight` varchar(255) DEFAULT NULL COMMENT '产品毛重',
  `pro_spec` text COMMENT '产品规格',
  `pro_memo` text COMMENT '备注说明',
  `pro_text` longtext COMMENT '正文',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `pro_info` varchar(255) DEFAULT NULL COMMENT '产品信息：(原厂件，副厂件，高仿件，品牌件)',
  `refresh_time` datetime DEFAULT NULL COMMENT '刷新点时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `proId` (`proId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品表';

-- ----------------------------
-- Records of product_list
-- ----------------------------

-- ----------------------------
-- Table structure for `sales_call_log`
-- ----------------------------
DROP TABLE IF EXISTS `sales_call_log`;
CREATE TABLE `sales_call_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务员拨打记录',
  `sales_user_id` int(11) DEFAULT NULL COMMENT '业务员id',
  `firms_is` int(11) DEFAULT NULL COMMENT '厂商id',
  `create_time` datetime DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示，1显示 2不显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='业务员拨打记录';

-- ----------------------------
-- Records of sales_call_log
-- ----------------------------

-- ----------------------------
-- Table structure for `sales_user`
-- ----------------------------
DROP TABLE IF EXISTS `sales_user`;
CREATE TABLE `sales_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务员',
  `uId` varchar(10) DEFAULT NULL COMMENT '业务员ID',
  `uname` varchar(30) DEFAULT NULL COMMENT '昵称',
  `area` varchar(255) DEFAULT NULL COMMENT '管辖区域',
  `password` varchar(255) DEFAULT NULL COMMENT '密码 7777777',
  `phone` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `realname` varchar(255) DEFAULT NULL COMMENT '姓名',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `last_time` datetime DEFAULT NULL COMMENT '最近登录时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态 1正常 2禁止',
  `facepic` varchar(255) DEFAULT NULL COMMENT '头像',
  `base_wage` decimal(10,2) DEFAULT NULL COMMENT '基本工资',
  `subsidies` decimal(10,2) DEFAULT NULL COMMENT '补贴',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uId` (`uId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='业务员';

-- ----------------------------
-- Records of sales_user
-- ----------------------------
INSERT INTO `sales_user` VALUES ('1', '46464', '46', '464', 'f1882db0904583fba94712b0a0a5db4e', '65', '46', '2017-05-12 16:46:03', '2017-05-12 16:45:59', '2017-05-12 16:45:54', '1', '46', '46.00', '4.00');
INSERT INTO `sales_user` VALUES ('2', '50301895', '萨芬公开', '', 'f1882db0904583fba94712b0a0a5db4e', '', '', '2017-05-12 17:58:27', '2017-05-12 17:58:27', null, '1', null, null, null);
INSERT INTO `sales_user` VALUES ('3', '66825572', '撒发生', '四川', 'f1882db0904583fba94712b0a0a5db4e', '242342', '归属地', '2017-05-12 18:03:21', '2017-05-12 18:03:21', null, '1', null, null, null);

-- ----------------------------
-- Table structure for `sales_wage_log`
-- ----------------------------
DROP TABLE IF EXISTS `sales_wage_log`;
CREATE TABLE `sales_wage_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '业务员工资记录',
  `sales_user_id` int(11) DEFAULT NULL COMMENT '业务员id',
  `year_month` date DEFAULT NULL COMMENT '年月份',
  `base_wage` decimal(10,2) DEFAULT NULL COMMENT '基本工资',
  `subsidies` decimal(10,2) DEFAULT NULL COMMENT '补助',
  `new_firms_money` decimal(10,2) DEFAULT NULL COMMENT '新增关联提成 \r\n明细从firms_sales_user表中获取',
  `factory_use_money` decimal(10,2) DEFAULT NULL COMMENT '汽修厂厂使用频率提成  \r\n针对汽修厂使用频率的提成，是统计关联后30天内，该月达到对应使用等级的关联汽修厂的数量提成，等级同验证厂商中的汽修厂等级定义，如3月1日，关联修理厂满30天的，达到等级1的汽修厂有200个，达到等级2的有60个，达到等级3的有40个，乘以对应的提成单价，已提成的修理厂就不再计入下一个月的提成；\r\n到1号时，没有关联到30天的，这部分汽修厂的提成计入到下一个月的提成',
  `factory_call_money` decimal(10,2) DEFAULT NULL COMMENT '针对汽修厂关联提成，是按照该月来电数（移动端拨打电话+QQ）乘以固定系数算出的',
  `firms_pay_money` decimal(10,2) DEFAULT NULL COMMENT '关联厂商充值提成，是按照该月经销商和汽修厂充值金额乘以固定系数算出的，充值包括了买VIP、充值点数和后台人工增加的充值金额（人工开通VIP或增加刷新点，需增加财务数据）',
  `total` decimal(10,2) DEFAULT NULL COMMENT '合计',
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示给业务员  1是 2否\r\n\r\n每月1号统计上1个月的提成和工资；每个月工资由后台审核/确认后才会更新，未更新统计为“0”',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='业务员工资记录';

-- ----------------------------
-- Records of sales_wage_log
-- ----------------------------

-- ----------------------------
-- Table structure for `su_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `su_action_log`;
CREATE TABLE `su_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户操作表',
  `userId` int(11) DEFAULT NULL COMMENT '操作用户id',
  `user` varchar(16) DEFAULT NULL COMMENT '操作用户',
  `code` varchar(20) DEFAULT NULL COMMENT '操作用户帐号',
  `action` varchar(128) DEFAULT NULL COMMENT '操作',
  `result` char(2) DEFAULT NULL COMMENT '结果',
  `time` datetime DEFAULT NULL COMMENT '操作时间',
  `ip` char(15) DEFAULT NULL COMMENT '操作ip',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of su_action_log
-- ----------------------------
INSERT INTO `su_action_log` VALUES ('89', '1', '超级管理员', 'admin', '停用管理员', '成功', '2017-05-03 10:59:54', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('90', '1', '超级管理员', 'admin', '启用管理员', '成功', '2017-05-04 10:59:57', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('91', '1', '超级管理员', 'admin', '启用管理员', '成功', '2017-05-06 11:01:00', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('92', '1', '超级管理员', 'admin', '重置密码', '成功', '2017-05-08 11:01:04', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('88', '1', '超级管理员', 'admin', '登录系统', '成功', '2017-05-11 10:57:31', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('93', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-11 11:03:16', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('94', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-11 11:34:44', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('95', '1', '超级管理员', 'admin', '编辑权限', '成功', '2017-05-11 14:37:30', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('96', '1', '超级管理员', 'admin', '编辑权限', '成功', '2017-05-12 10:32:41', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('97', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-12 10:32:49', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('98', '1', '超级管理员', 'admin', '停用管理员', '成功', '2017-05-12 10:32:54', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('99', '1', '超级管理员', 'admin', '重置密码', '成功', '2017-05-12 10:33:27', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('100', '1', '超级管理员', 'admin', '启用管理员', '成功', '2017-05-12 14:56:25', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('101', '1', '超级管理员', 'admin', '修改管理员名称', '成功', '2017-05-12 14:56:34', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('102', '1', '超级管理员', 'admin', '停用管理员', '成功', '2017-05-12 14:56:41', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('103', '1', '超级管理员', 'admin', '停用业务员', '成功', '2017-05-12 17:06:40', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('104', '1', '超级管理员', 'admin', '启用业务员', '成功', '2017-05-12 17:07:08', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('105', '1', '超级管理员', 'admin', '重置业务员密码', '成功', '2017-05-12 17:10:09', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('106', '1', '超级管理员', 'admin', '添加业务员', '成功', '2017-05-12 17:58:27', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('107', '1', '超级管理员', 'admin', '添加业务员', '成功', '2017-05-12 18:03:21', '127.0.0.1');
INSERT INTO `su_action_log` VALUES ('108', '1', '超级管理员', 'admin', '启用业务员', '成功', '2017-05-12 18:12:04', '127.0.0.1');

-- ----------------------------
-- Table structure for `su_user`
-- ----------------------------
DROP TABLE IF EXISTS `su_user`;
CREATE TABLE `su_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户表',
  `code` varchar(20) DEFAULT NULL COMMENT '帐号',
  `name` varchar(20) DEFAULT NULL COMMENT '账号名称',
  `pwd` char(32) DEFAULT NULL COMMENT '密码 md5(sha1(''123456'').''sw'')',
  `status` smallint(1) DEFAULT '1' COMMENT '用户禁用与否 1 未禁用(正常) 2 已禁用',
  `session_id` char(26) DEFAULT NULL COMMENT 'sessionId',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of su_user
-- ----------------------------
INSERT INTO `su_user` VALUES ('1', 'admin', '超级管理员', 'c233ea59bbe6f12cec6d48e956ca91bb', '1', '9p8joiuvpcvuh4cfid2mea6h04', null, null);
INSERT INTO `su_user` VALUES ('2', 'admin1', '管理员', '', '1', 'rkvllk9mdudq3k4siir2hrhrn2', '2017-04-28 12:06:42', '2017-04-28 23:26:13');
INSERT INTO `su_user` VALUES ('3', 'admin2', '管理员', '', '2', null, '2017-04-28 15:47:16', '2017-04-28 23:26:02');
INSERT INTO `su_user` VALUES ('4', 'admin3', '管理员', '', '2', null, '2017-04-28 15:47:37', '2017-04-28 15:47:37');
INSERT INTO `su_user` VALUES ('5', 'admin4', '管理员', '54c7db55654c6d4adf6b1ea73a86e159', '2', null, '2017-05-10 18:01:03', '2017-05-10 18:01:03');
INSERT INTO `su_user` VALUES ('6', 'admin5', '管理员', '', '1', null, '2017-05-10 18:05:42', '2017-05-10 18:05:42');
INSERT INTO `su_user` VALUES ('7', 'admin6', '管理员', '', '1', null, '2017-05-10 18:06:26', '2017-05-10 18:06:26');
INSERT INTO `su_user` VALUES ('8', 'admin7', '管理员', '', '1', null, '2017-05-10 18:08:56', '2017-05-10 18:08:56');

-- ----------------------------
-- Table structure for `want_buy`
-- ----------------------------
DROP TABLE IF EXISTS `want_buy`;
CREATE TABLE `want_buy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '求购表 firms表',
  `firms_id` int(11) DEFAULT NULL COMMENT '商家id',
  `car_group_id` int(11) DEFAULT NULL COMMENT '选择车系  car_group表id',
  `frame_number` varchar(255) DEFAULT NULL COMMENT '车架号  不一定会有',
  `limitation` tinyint(1) DEFAULT NULL COMMENT '时效 天数 (1，2，3) 最多3天',
  `vin_pic` varchar(255) DEFAULT NULL COMMENT 'VIN照片',
  `memo` text COMMENT '备注',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求购表';

-- ----------------------------
-- Records of want_buy
-- ----------------------------

-- ----------------------------
-- Table structure for `want_buy_list`
-- ----------------------------
DROP TABLE IF EXISTS `want_buy_list`;
CREATE TABLE `want_buy_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '求购表采购清单',
  `want_buy_id` int(11) DEFAULT NULL COMMENT 'want_buy表id',
  `pro_cate1` int(11) DEFAULT NULL COMMENT '产品分类 一级分类',
  `pro_cate2` int(11) DEFAULT NULL COMMENT '产品分类 二级分类',
  `amount` int(11) DEFAULT NULL COMMENT '数量',
  `list_memo` text COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求购表采购清单';

-- ----------------------------
-- Records of want_buy_list
-- ----------------------------

-- ----------------------------
-- Table structure for `want_buy_pic`
-- ----------------------------
DROP TABLE IF EXISTS `want_buy_pic`;
CREATE TABLE `want_buy_pic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '求购相关照片',
  `want_buy_id` int(11) DEFAULT NULL COMMENT 'want_buy表id',
  `pic_url` varchar(255) DEFAULT NULL COMMENT '图片地址',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求购相关照片';

-- ----------------------------
-- Records of want_buy_pic
-- ----------------------------
