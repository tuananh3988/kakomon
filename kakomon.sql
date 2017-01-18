/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : kakomon

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-01-18 18:45:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for member
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) NOT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '1',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for member_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `member_access_tokens`;
CREATE TABLE `member_access_tokens` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` mediumint(8) unsigned NOT NULL COMMENT 'members#id',
  `device_id` varchar(64) NOT NULL,
  `access_token` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'ã‚¢ã‚¯ã‚»ã‚',
  `created_date` datetime NOT NULL COMMENT 'ä½œæ',
  `expire_date` datetime NOT NULL COMMENT 'æœ‰å',
  `updated_date` datetime DEFAULT NULL COMMENT 'æœ€çµ‚',
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_token_UNIQUE` (`access_token`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ã‚¢ã‚¯ã‚»ã‚¹ãƒ';

-- ----------------------------
-- Table structure for member_devices
-- ----------------------------
DROP TABLE IF EXISTS `member_devices`;
CREATE TABLE `member_devices` (
  `id` mediumint(8) NOT NULL,
  `member_id` mediumint(8) unsigned NOT NULL COMMENT 'members#id',
  `device_id` varchar(64) DEFAULT NULL,
  `device_type` tinyint(3) unsigned NOT NULL COMMENT 'ãƒ‡ãƒã',
  `device_token` varchar(256) DEFAULT NULL COMMENT 'ãƒ‡ãƒã‚¤ã‚¹ãƒˆãƒ¼ã‚¯ãƒ³',
  `delete_flag` tinyint(1) DEFAULT '0',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ãƒ‡ãƒã‚¤ã';

-- ----------------------------
-- Table structure for quiz
-- ----------------------------
DROP TABLE IF EXISTS `quiz`;
CREATE TABLE `quiz` (
  `quiz_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
