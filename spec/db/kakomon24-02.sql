/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : kakomon

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-02-24 17:04:03
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for answer
-- ----------------------------
DROP TABLE IF EXISTS `answer`;
CREATE TABLE `answer` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `content` text,
  `order` tinyint(4) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of answer
-- ----------------------------

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `cateory_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `level` tinyint(4) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`cateory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category
-- ----------------------------

-- ----------------------------
-- Table structure for comment
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: normal, 2: help, 3: reply',
  `quiz_id` int(11) NOT NULL,
  `relate_id` int(11) DEFAULT NULL,
  `content` varchar(255) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of comment
-- ----------------------------

-- ----------------------------
-- Table structure for exam
-- ----------------------------
DROP TABLE IF EXISTS `exam`;
CREATE TABLE `exam` (
  `exam_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: create, 1: active, 2: end',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: free, 2:paid',
  `total_quiz` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`exam_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of exam
-- ----------------------------

-- ----------------------------
-- Table structure for exam_quiz
-- ----------------------------
DROP TABLE IF EXISTS `exam_quiz`;
CREATE TABLE `exam_quiz` (
  `exam_quiz_id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`exam_quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of exam_quiz
-- ----------------------------

-- ----------------------------
-- Table structure for like
-- ----------------------------
DROP TABLE IF EXISTS `like`;
CREATE TABLE `like` (
  `like_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `delete_flag` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: active, 1: deleted',
  `like_flag` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: like, 2:dislike',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`like_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of like
-- ----------------------------

-- ----------------------------
-- Table structure for member
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `city` varchar(255) DEFAULT NULL,
  `job` varchar(255) DEFAULT NULL,
  `type_blood` tinyint(4) DEFAULT '1' COMMENT '1: O, 2: A, 3: B, 4: AB',
  `favorite_animal` varchar(255) DEFAULT NULL,
  `favorite_film` varchar(255) DEFAULT NULL,
  `birthday` date NOT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: male, 2: female',
  `name` varchar(255) DEFAULT NULL,
  `furigana` varchar(255) DEFAULT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `auth_key` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of member
-- ----------------------------
INSERT INTO `member` VALUES ('1', '1', 'Ha noi', 'IT', '1', 'cho', 'sex', '2017-02-10', '1', 'Tuan Anh', 'aaaa', 'tuananh3988@gmail.com', '', 'Doreman', '123456789', '2017-02-24 11:55:56', null);

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
-- Records of member_devices
-- ----------------------------

-- ----------------------------
-- Table structure for member_quiz_history
-- ----------------------------
DROP TABLE IF EXISTS `member_quiz_history`;
CREATE TABLE `member_quiz_history` (
  `member_quiz_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `correct_flag` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: correct, 2: incorrect, 3: not doing',
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`member_quiz_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of member_quiz_history
-- ----------------------------

-- ----------------------------
-- Table structure for quiz
-- ----------------------------
DROP TABLE IF EXISTS `quiz`;
CREATE TABLE `quiz` (
  `quiz_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: normal, 2: quick quiz, 3: collect',
  `question` text NOT NULL,
  `category_id_1` int(11) DEFAULT NULL,
  `category_id_2` int(11) DEFAULT NULL,
  `category_id_3` int(11) DEFAULT NULL,
  `category_id_4` int(11) DEFAULT NULL,
  `quiz_year` int(11) DEFAULT NULL,
  `staff_create` int(11) DEFAULT NULL,
  `delete_flag` tinyint(4) DEFAULT '0' COMMENT '0: active, 1: delete',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of quiz
-- ----------------------------

-- ----------------------------
-- Table structure for quiz_answer
-- ----------------------------
DROP TABLE IF EXISTS `quiz_answer`;
CREATE TABLE `quiz_answer` (
  `quiz_answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  PRIMARY KEY (`quiz_answer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of quiz_answer
-- ----------------------------

-- ----------------------------
-- Table structure for staffs
-- ----------------------------
DROP TABLE IF EXISTS `staffs`;
CREATE TABLE `staffs` (
  `id` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth_key` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of staffs
-- ----------------------------
INSERT INTO `staffs` VALUES ('1', 'admin', 'John Howard', '$2y$13$xsJAa5yVT9mQ/uTLgxGLTO2T.bYoHAZZnVdtgBqqZtpDHZrxvUdIi', null);
