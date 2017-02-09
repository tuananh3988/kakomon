/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : kakomon

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-02-10 00:28:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', 'Hardware', '0');
INSERT INTO `menu` VALUES ('2', 'Software', '0');
INSERT INTO `menu` VALUES ('3', 'Movies', '0');
INSERT INTO `menu` VALUES ('4', 'Clothes', '0');
INSERT INTO `menu` VALUES ('5', 'Printers', '1');
INSERT INTO `menu` VALUES ('6', 'Monitors', '1');
INSERT INTO `menu` VALUES ('7', 'Inkjet printers', '5');
INSERT INTO `menu` VALUES ('8', 'Laserjet Printers', '5');
INSERT INTO `menu` VALUES ('9', 'LCD monitors', '6');
INSERT INTO `menu` VALUES ('10', 'TFT monitors', '6');
INSERT INTO `menu` VALUES ('11', 'Antivirus', '2');
INSERT INTO `menu` VALUES ('12', 'Action movies', '3');
INSERT INTO `menu` VALUES ('13', 'Comedy Movies', '3');
INSERT INTO `menu` VALUES ('14', 'Romantic movie', '3');
INSERT INTO `menu` VALUES ('15', 'Thriller Movies', '3');
INSERT INTO `menu` VALUES ('16', 'Mens', '4');
INSERT INTO `menu` VALUES ('17', 'Womens', '4');
INSERT INTO `menu` VALUES ('18', 'Shirts', '16');
INSERT INTO `menu` VALUES ('19', 'T-shirts', '16');
INSERT INTO `menu` VALUES ('20', 'Shirts', '16');
INSERT INTO `menu` VALUES ('21', 'Jeans', '16');
INSERT INTO `menu` VALUES ('22', 'Accessories', '16');
INSERT INTO `menu` VALUES ('23', 'Tees', '17');
INSERT INTO `menu` VALUES ('24', 'Skirts', '17');
INSERT INTO `menu` VALUES ('25', 'Leggins', '17');
INSERT INTO `menu` VALUES ('26', 'Jeans', '17');
INSERT INTO `menu` VALUES ('27', 'Accessories', '17');
INSERT INTO `menu` VALUES ('28', 'Watches', '22');
INSERT INTO `menu` VALUES ('29', 'Tie', '22');
INSERT INTO `menu` VALUES ('30', 'cufflinks', '22');
INSERT INTO `menu` VALUES ('31', 'Earrings', '27');
INSERT INTO `menu` VALUES ('32', 'Bracelets', '27');
INSERT INTO `menu` VALUES ('33', 'Necklaces', '27');
INSERT INTO `menu` VALUES ('34', 'Pendants', '27');
