/*
Navicat MySQL Data Transfer

Source Server         : 10.240.0.61
Source Server Version : 50622
Source Host           : 10.240.0.61:3306
Source Database       : easyswoole

Target Server Type    : MYSQL
Target Server Version : 50622
File Encoding         : 65001

Date: 2019-08-17 10:58:53
*/

CREATE Database IF NOT EXISTS easyswoole;

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wl_member`
-- ----------------------------
DROP TABLE IF EXISTS `wl_member`;
CREATE TABLE `wl_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` char(11) NOT NULL COMMENT '手机号',
  `password` varchar(20) NOT NULL COMMENT '密码',
  `name` varchar(20) NOT NULL COMMENT '用户名',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1、正常)',
  `image_url` varchar(255) NOT NULL COMMENT '头像',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of wl_member
-- ----------------------------
INSERT INTO `wl_member` VALUES ('1', '18511111111', '123456', '秦始皇-赢政', '11111@qq.com', '1', 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo_top_ca79a146.png', '2019-07-06 15:21:24', '2019-07-14 10:50:50');
INSERT INTO `wl_member` VALUES ('1', '18522222222', '123456', '隨文帝-杨坚', '22222@qq.com', '1', 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo_top_ca79a146.png', '2019-07-06 15:21:24', '2019-07-14 10:50:50');
INSERT INTO `wl_member` VALUES ('2', '18533333333', '123456', '唐太宗-李世民', '33333@qq.cm', '1', 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo_top_ca79a146.png', '2019-07-09 12:44:01', '2019-07-14 10:50:58');
INSERT INTO `wl_member` VALUES ('2', '18555555555', '123456', '宋太祖-赵匡湚', '55555@qq.cm', '1', 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo_top_ca79a146.png', '2019-07-09 12:44:01', '2019-07-14 10:50:58');
INSERT INTO `wl_member` VALUES ('3', '18566666666', '123456', '成吉思汗-铁木真', '66666@qq.com', '1', 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo_top_ca79a146.png', '2019-07-09 12:44:16', '2019-07-14 10:51:05');

-- ----------------------------
-- Table structure for `wl_member_friend`
-- ----------------------------
DROP TABLE IF EXISTS `wl_member_friend`;
CREATE TABLE `wl_member_friend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `friend_id` int(10) unsigned NOT NULL COMMENT '好友的ID',
  `member_id` int(10) unsigned NOT NULL COMMENT '我的ID',
  PRIMARY KEY (`id`),
  KEY `friend_id` (`friend_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='好友表';

-- ----------------------------
-- Records of wl_member_friend
-- ----------------------------

-- ----------------------------
-- Table structure for `wl_member_group`
-- ----------------------------
DROP TABLE IF EXISTS `wl_member_group`;
CREATE TABLE `wl_member_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL COMMENT '创建人用户ID',
  `group_name` varchar(20) NOT NULL COMMENT '用户名',
  `group_hand_url` varchar(255) NOT NULL COMMENT '组头像地址',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1、正常)',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户组表';

-- ----------------------------
-- Records of wl_member_group
-- ----------------------------

-- ----------------------------
-- Table structure for `wl_member_group_item`
-- ----------------------------
DROP TABLE IF EXISTS `wl_member_group_item`;
CREATE TABLE `wl_member_group_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '组ID',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='组的用户列表';

-- ----------------------------
-- Records of wl_member_group_item
-- ----------------------------

-- ----------------------------
-- Table structure for `wl_member_group_record`
-- ----------------------------
DROP TABLE IF EXISTS `wl_member_group_record`;
CREATE TABLE `wl_member_group_record` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) NOT NULL COMMENT '组IP',
  `member_id` int(10) NOT NULL COMMENT '用户名',
  `content` tinytext NOT NULL COMMENT '邮箱',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1、正常)',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`,`status`,`create_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户组聊天记录表';

-- ----------------------------
-- Records of wl_member_group_record
-- ----------------------------

-- ----------------------------
-- Table structure for `wl_member_message`
-- ----------------------------
DROP TABLE IF EXISTS `wl_member_message`;
CREATE TABLE `wl_member_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `content` varchar(255) NOT NULL COMMENT '消息内容',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1、正常)',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户消息表';

-- ----------------------------
-- Records of wl_member_message
-- ----------------------------

-- ----------------------------
-- Table structure for `wl_member_record`
-- ----------------------------
DROP TABLE IF EXISTS `wl_member_record`;
CREATE TABLE `wl_member_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `content` tinytext NOT NULL COMMENT '用户名',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1、正常)',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户聊天记录表';

-- ----------------------------
-- Records of wl_member_record
-- ----------------------------

-- ----------------------------
-- Table structure for `wl_room`
-- ----------------------------
DROP TABLE IF EXISTS `wl_room`;
CREATE TABLE `wl_room` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `room_name` varchar(30) NOT NULL COMMENT '房间名称',
  `cover_image` varchar(255) NOT NULL COMMENT '封面图',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='房间表';

-- ----------------------------
-- Records of wl_room
-- ----------------------------
INSERT INTO `wl_room` VALUES ('1', '大风车', 'static/images/room1.png');
INSERT INTO `wl_room` VALUES ('2', '童年时光', 'static/images/room2.png');
INSERT INTO `wl_room` VALUES ('3', '七彩气球', 'static/images/room3.png');
