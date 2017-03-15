/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : kakomon

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-03-15 11:10:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for log_csv
-- ----------------------------
DROP TABLE IF EXISTS `log_csv`;
CREATE TABLE `log_csv` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(2) DEFAULT '1' COMMENT '1:wait, 2: process, 3: done',
  `file_name` varchar(255) NOT NULL,
  `content_log` text,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
