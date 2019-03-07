/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50636
Source Host           : localhost:3306
Source Database       : lab8

Target Server Type    : MYSQL
Target Server Version : 50636
File Encoding         : 65001

Date: 2018-01-31 01:36:04
*/

SET FOREIGN_KEY_CHECKS=0;


-- ----------------------------
-- Table structure for gg_ggs
-- ----------------------------
DROP TABLE IF EXISTS `gg_ggs`;
CREATE TABLE `gg_ggs` (
  `gg_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `mod_id` bigint(20) unsigned NOT NULL COMMENT '所属模块(项目)id,可能是公司id,医院id,医生id等,根据type看',
  `mod_type` tinyint(4) DEFAULT NULL COMMENT '模块类型',
  `ggo_id` char(18) DEFAULT NULL COMMENT ' 所在订单id',
  `__ggp_ids` varchar(255) DEFAULT NULL COMMENT '广告位id',
  `title` varchar(100) DEFAULT NULL COMMENT '广告名称',
  `description` tinytext COMMENT '描述',
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '开始（生效）时间',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '结束时间',
  `ggm_id` bigint(20) unsigned NOT NULL COMMENT '素材id',
  `ggm_free_id` bigint(20) unsigned NOT NULL COMMENT '闲置素材id',
  `price_level` tinyint(4) unsigned DEFAULT NULL COMMENT '价格区间等级',
  `gg_state` tinyint(4) unsigned DEFAULT '0' COMMENT '广告状态,1停用2正常',
  `gg_put_state` tinyint(4) unsigned DEFAULT NULL COMMENT '广告投放状态,1未投放2准备投放3投放中4结束投放',
  `is_del` tinyint(4) unsigned DEFAULT NULL COMMENT '是否被删除,1是0否',
  `pay_type` tinyint(4) unsigned DEFAULT '0' COMMENT '收费类型,1免费2付费3标配4额配',
  `order` int(11) unsigned DEFAULT NULL COMMENT '排序',
  `__click_count` int(11) unsigned DEFAULT NULL COMMENT '点击量,每次点都+1?',
  `province` char(6) DEFAULT NULL COMMENT '广告投放地区',
  `dateline` int(11) unsigned DEFAULT NULL COMMENT '发布时间',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`gg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_materials
-- ----------------------------
DROP TABLE IF EXISTS `gg_materials`;
CREATE TABLE `gg_materials` (
  `ggm_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告素材id',
  `mod_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '所属模块(项目)id,可能是公司id,医院id,医生id等,根据type看',
  `mod_type` tinyint(4) DEFAULT NULL COMMENT '模块类型',
  `pdt_id` bigint(20) unsigned DEFAULT '0' COMMENT '产品Id',
  `ggm_type` tinyint(4) unsigned DEFAULT NULL COMMENT '广告素材类型1文字2图片3flash4.flv视频5代码',
  `ggo_id` char(18) DEFAULT NULL COMMENT ' 所在订单id',
  `title` varchar(100) DEFAULT NULL COMMENT '素材名称',
  `gg_title` varchar(255) DEFAULT NULL COMMENT '广告标题',
  `slogan` varchar(255) DEFAULT NULL COMMENT '广告语',
  `src` varchar(255) DEFAULT NULL COMMENT '素材资源地址',
  `face_src` varchar(255) DEFAULT NULL COMMENT '视频截图地址',
  `link_url` varchar(255) DEFAULT NULL COMMENT '目标链接地址',
  `ggs_id` varchar(100) DEFAULT NULL COMMENT '广告素材规格id',
  `__width` smallint(6) unsigned DEFAULT NULL COMMENT '素材宽度',
  `__height` smallint(6) unsigned DEFAULT NULL COMMENT '素材高度',
  `__length` smallint(6) DEFAULT NULL COMMENT '文字长度',
  `__use_count` int(11) DEFAULT '0' COMMENT '素材的引用个数',
  `is_valid` tinyint(4) unsigned DEFAULT NULL COMMENT '是否有效1是0否',
  `audit_state` tinyint(4) unsigned DEFAULT '1' COMMENT '审核状态1未审核2通过审核3未通过',
  `audit_time` int(11) unsigned DEFAULT NULL COMMENT '审核时间',
  `description` text COMMENT '素材描述',
  `dateline` int(11) unsigned DEFAULT NULL COMMENT '素材添加时间',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_del` tinyint(4) DEFAULT '0' COMMENT '是否删除1删除 0未删除',
  `link_url_bak` varchar(255) DEFAULT NULL COMMENT ' 备份',
  PRIMARY KEY (`ggm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1102 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_orders
-- ----------------------------
DROP TABLE IF EXISTS `gg_orders`;
CREATE TABLE `gg_orders` (
  `ggo_id` char(18) NOT NULL COMMENT '订单id',
  `contract_no` varchar(50) DEFAULT NULL COMMENT '合同编号',
  `request_type` tinyint(4) unsigned DEFAULT NULL COMMENT '申请类型1新2续3停',
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT '用户id',
  `mod_type` tinyint(4) unsigned DEFAULT NULL COMMENT '模块类型',
  `mod_id` int(10) unsigned DEFAULT NULL COMMENT '模块id ',
  `user_name` varchar(50) DEFAULT NULL,
  `link_man` varchar(50) DEFAULT NULL COMMENT '联系人',
  `link_phone` varchar(50) DEFAULT NULL COMMENT '联系电话',
  `site_url` varchar(255) DEFAULT NULL COMMENT '客户网址',
  `fax` varchar(50) DEFAULT NULL COMMENT '传真',
  `gg_slogan` varchar(255) DEFAULT NULL COMMENT '广告标语',
  `gg_price` decimal(10,2) DEFAULT NULL COMMENT '广告价格',
  `demand_types` varchar(255) DEFAULT NULL COMMENT '需求类型,1图片2动画3文字链4套红文字链',
  `ggp_ids` varchar(255) DEFAULT NULL COMMENT '广告位置ids',
  `orther_info` text COMMENT '其他信息，配送位置，赠送时间',
  `dateline` int(11) unsigned DEFAULT NULL COMMENT '提交日期',
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '广告开始时间',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '广告结束时间',
  `salesman` varchar(50) DEFAULT NULL COMMENT '业务员',
  `team_leader` varchar(50) DEFAULT NULL COMMENT '队长',
  `manager` varchar(50) DEFAULT NULL COMMENT '经理',
  `publisher` varchar(50) DEFAULT NULL COMMENT '发布人',
  `financial_staff` varchar(50) DEFAULT NULL COMMENT '财务',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ggo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_position
-- ----------------------------
DROP TABLE IF EXISTS `gg_position`;
CREATE TABLE `gg_position` (
  `ggp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位id',
  `gg_id` int(11) DEFAULT NULL COMMENT '广告位放的广告id',
  `title` varchar(100) DEFAULT NULL COMMENT '广告位标题（就是说明）',
  `ggpg_id` int(11) DEFAULT NULL COMMENT '广告位所在大分组(一级,区)',
  `ggpg_id2` int(11) DEFAULT '-1' COMMENT '所属2级分组id -1代表空',
  `ggp_type` tinyint(4) DEFAULT NULL COMMENT '广告位类型1文字2图片3flash4.flv视频5.代码',
  `gg_tpl_id` smallint(6) unsigned NOT NULL DEFAULT '1' COMMENT '广告模版id',
  `width` int(11) DEFAULT NULL COMMENT '广告位宽度',
  `height` int(11) DEFAULT NULL COMMENT '广告位高度',
  `ggp_level` tinyint(4) DEFAULT NULL COMMENT '广告位等级',
  `price_level` tinyint(4) DEFAULT NULL COMMENT '广告位参考价格等级',
  `add_red` tinyint(4) DEFAULT NULL COMMENT '套红,1套红,2未套红',
  `order` smallint(6) unsigned DEFAULT NULL COMMENT '广告位的排序',
  `gg_sale_state` tinyint(4) unsigned DEFAULT '0' COMMENT '广告位销售状态,1付费2配送3免费',
  `is_valid` tinyint(4) unsigned DEFAULT NULL COMMENT '是否有效1是0否',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `is_del` tinyint(4) DEFAULT '0' COMMENT '是否删除 0未删除 1删除',
  `click_count` int(11) unsigned DEFAULT '0' COMMENT '点击总数',
  `refresh_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '广告位刷新时间',
  `dateline` int(11) DEFAULT NULL COMMENT '添加时间',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ggp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1818 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_position_group
-- ----------------------------
DROP TABLE IF EXISTS `gg_position_group`;
CREATE TABLE `gg_position_group` (
  `ggpg_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位的分组id',
  `title` varchar(100) DEFAULT NULL COMMENT '分组名称',
  `parent_id` int(11) unsigned DEFAULT '0' COMMENT '父id(一级)',
  `__width` smallint(6) unsigned DEFAULT NULL,
  `__height` smallint(6) unsigned DEFAULT NULL,
  `dateline` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  `intro` varchar(255) DEFAULT NULL COMMENT '简短说明',
  `___is_del` tinyint(4) unsigned DEFAULT '0' COMMENT '是否被删除1是0否',
  `order` int(11) DEFAULT '100' COMMENT '排序',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ggpg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_position_temp
-- ----------------------------
DROP TABLE IF EXISTS `gg_position_temp`;
CREATE TABLE `gg_position_temp` (
  `ggp_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位id',
  `gg_id` int(11) DEFAULT NULL COMMENT '广告位放的广告id',
  `title` varchar(100) DEFAULT NULL COMMENT '广告位标题（就是说明）',
  `ggpg_id_1` int(11) DEFAULT NULL COMMENT '广告位所在大分组(一级,区)',
  `ggpg_id_2` int(11) DEFAULT NULL COMMENT '广告位所在分组(二级)',
  `ggpg_id_3` int(11) DEFAULT NULL COMMENT '广告位所在分组(三级)，预留',
  `ggp_type` tinyint(4) DEFAULT NULL COMMENT '广告位类型1文字2图片3flash4.flv视频5.代码',
  `gg_tpl_id` smallint(6) unsigned NOT NULL COMMENT '广告模版id',
  `width` int(11) DEFAULT NULL COMMENT '广告位宽度',
  `height` int(11) DEFAULT NULL COMMENT '广告位高度',
  `ggp_level` tinyint(4) DEFAULT NULL COMMENT '广告位等级',
  `price_level` tinyint(4) DEFAULT NULL COMMENT '广告位参考价格等级',
  `add_red` tinyint(4) DEFAULT NULL COMMENT '套红0没得1套红',
  `order` smallint(6) DEFAULT NULL COMMENT '广告位的排序',
  `gg_sale_state` tinyint(4) unsigned DEFAULT '0' COMMENT '广告位销售状态,1空闲2付费3标配4额配',
  `is_valid` tinyint(4) unsigned DEFAULT NULL COMMENT '是否有效1是0否',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `dateline` int(11) DEFAULT NULL COMMENT '添加时间',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ggp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_queue
-- ----------------------------
DROP TABLE IF EXISTS `gg_queue`;
CREATE TABLE `gg_queue` (
  `ggq_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告队列id',
  `ggp_id` int(11) unsigned DEFAULT NULL COMMENT '广告位置id',
  `gg_id` int(11) DEFAULT NULL COMMENT '广告id',
  `cmp_id` int(11) DEFAULT NULL COMMENT '公司ID',
  `contractNo_id` varchar(32) DEFAULT NULL COMMENT '合同编号id',
  `ggm_id` int(11) DEFAULT NULL COMMENT '素材ID',
  `ggp_name` varchar(100) DEFAULT NULL COMMENT '广告位名称',
  `leader` varchar(32) DEFAULT NULL COMMENT '组长',
  `major` varchar(32) DEFAULT NULL COMMENT '业务员',
  `issuer` varchar(32) DEFAULT NULL COMMENT '发布人',
  `manager` varchar(32) DEFAULT NULL COMMENT '经理',
  `finance` varchar(32) DEFAULT NULL COMMENT '财务',
  `remark` text COMMENT '备注',
  `gg_name` varchar(100) DEFAULT NULL,
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '开始时间',
  `end_time` int(11) unsigned DEFAULT NULL COMMENT '结束时间',
  `is_del` tinyint(4) DEFAULT '0' COMMENT '删除状态0 为删除 1删除',
  `audit_status` tinyint(4) DEFAULT '1' COMMENT '审核状态 1待审核 2 审核通过 3审核未通过 4需重新审核',
  `dateline` int(11) DEFAULT NULL COMMENT '添加时间',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `link_man` varchar(32) DEFAULT NULL COMMENT '联系人',
  `link_tel` varchar(32) DEFAULT NULL COMMENT '联系电话',
  PRIMARY KEY (`ggq_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1816 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_standard
-- ----------------------------
DROP TABLE IF EXISTS `gg_standard`;
CREATE TABLE `gg_standard` (
  `ggs_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '素材规格id',
  `ggs_name` varchar(100) DEFAULT NULL COMMENT '规格名称',
  `ggs_type` tinyint(4) unsigned DEFAULT NULL COMMENT '广告规格类型0.综合1文字2图片3flash4.flv视频5代码',
  `width` int(11) unsigned DEFAULT '0' COMMENT '图像宽度',
  `height` int(11) unsigned DEFAULT '0' COMMENT '图像高度',
  `length` smallint(6) unsigned DEFAULT '0' COMMENT '文字类型的长度',
  `remark` text COMMENT '备注',
  `dateline` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_del` tinyint(4) DEFAULT '0' COMMENT '1为删除 0为未删除',
  PRIMARY KEY (`ggs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gg_template
-- ----------------------------
DROP TABLE IF EXISTS `gg_template`;
CREATE TABLE `gg_template` (
  `ggt_id` smallint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ggt_type` tinyint(4) unsigned DEFAULT NULL COMMENT '模版类型1文字2图片3flash4.flv视频5代码',
  `name` varchar(100) DEFAULT NULL COMMENT '模版名称',
  `code` text COMMENT '模版代码',
  `remark` text COMMENT '备注',
  `dateline` int(11) unsigned DEFAULT NULL COMMENT '添加日期',
  `is_del` tinyint(4) DEFAULT '0' COMMENT '删除状态 1为删除 0为未删除',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ggt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
