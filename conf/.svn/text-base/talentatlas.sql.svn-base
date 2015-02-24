-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 13, 2012 at 02:18 PM
-- Server version: 5.1.61
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `talent_atlas`
--

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE IF NOT EXISTS `company` (
  `companypk` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` binary(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`companypk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `industry`
--

DROP TABLE IF EXISTS `industry`;
CREATE TABLE IF NOT EXISTS `industry` (
  `industrypk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`industrypk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `jobpk` int(11) NOT NULL AUTO_INCREMENT,
  `data` text COLLATE utf8_bin NOT NULL,
  `date_create` date NOT NULL,
  `raw_content` text COLLATE utf8_bin NOT NULL,
  `weburlfk` int(11) NOT NULL,
  `websitefk` int(11) NOT NULL,
  `status` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`jobpk`),
  KEY `weburlfk` (`weburlfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `job_application`
--

DROP TABLE IF EXISTS `job_application`;
CREATE TABLE IF NOT EXISTS `job_application` (
  `job_applicationpk` int(11) NOT NULL AUTO_INCREMENT,
  `positionfk` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `resume` varchar(255) COLLATE utf8_bin NOT NULL,
  `coverletter` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`job_applicationpk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `loginpk` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(128) COLLATE utf8_bin NOT NULL,
  `password` varchar(256) COLLATE utf8_bin NOT NULL,
  `gender` int(11) NOT NULL,
  `courtesy` varchar(32) COLLATE utf8_bin NOT NULL,
  `email` varchar(256) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(255) COLLATE utf8_bin NOT NULL,
  `firstname` varchar(255) COLLATE utf8_bin NOT NULL,
  `position` varchar(255) COLLATE utf8_bin NOT NULL,
  `phone` varchar(255) COLLATE utf8_bin NOT NULL,
  `phone_ext` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) NOT NULL,
  `hashcode` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `date_create` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `date_expire` datetime DEFAULT NULL,
  `date_reset` datetime DEFAULT NULL,
  `date_last_log` datetime DEFAULT NULL,
  `webmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `webpassword` varchar(255) COLLATE utf8_bin NOT NULL,
  `mailport` varchar(255) COLLATE utf8_bin NOT NULL,
  `Imap` varchar(255) COLLATE utf8_bin NOT NULL,
  `aliasName` varchar(255) COLLATE utf8_bin NOT NULL,
  `signature` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`loginpk`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `login_access_history`
--

DROP TABLE IF EXISTS `login_access_history`;
CREATE TABLE IF NOT EXISTS `login_access_history` (
  `login_access_historypk` int(11) NOT NULL AUTO_INCREMENT,
  `history` text CHARACTER SET utf8 COLLATE utf8_bin,
  `ip_address` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `loginfk` int(11) NOT NULL,
  `date_start` datetime NOT NULL,
  `nb_page` int(11) NOT NULL,
  `session_uid` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`login_access_historypk`),
  KEY `loginfk` (`loginfk`,`session_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `login_preference`
--

DROP TABLE IF EXISTS `login_preference`;
CREATE TABLE IF NOT EXISTS `login_preference` (
  `login_preferencepk` int(11) NOT NULL AUTO_INCREMENT,
  `loginfk` int(11) NOT NULL,
  `user_preferencefk` int(11) NOT NULL,
  `value` text COLLATE utf8_bin NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_preferencepk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

DROP TABLE IF EXISTS `position`;
CREATE TABLE IF NOT EXISTS `position` (
  `positionpk` int(11) NOT NULL AUTO_INCREMENT,
  `jobfk` int(11) NOT NULL,
  `visibility` int(11) NOT NULL COMMENT '0-hidden, 1-Normal, 2- Top jobs',
  `category` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '0-Normal,1-Featured 2-Paid ',
  `career_level` text COLLATE utf8_bin NOT NULL,
  `position_title` varchar(255) COLLATE utf8_bin NOT NULL,
  `position_desc` text COLLATE utf8_bin NOT NULL,
  `requirements` text COLLATE utf8_bin NOT NULL,
  `companyfk` int(11) NOT NULL,
  `status` binary(1) NOT NULL,
  `posted_date` varchar(255) COLLATE utf8_bin NOT NULL,
  `location` text COLLATE utf8_bin NOT NULL,
  `salary` text COLLATE utf8_bin NOT NULL,
  `english` varchar(255) COLLATE utf8_bin NOT NULL,
  `japanese` varchar(255) COLLATE utf8_bin NOT NULL,
  `industryfk` int(11) NOT NULL,
  `holidays` text COLLATE utf8_bin NOT NULL,
  `station` text COLLATE utf8_bin NOT NULL,
  `work_hours` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`positionpk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_preference`
--

DROP TABLE IF EXISTS `user_preference`;
CREATE TABLE IF NOT EXISTS `user_preference` (
  `preferencepk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `uid` varchar(255) COLLATE utf8_bin NOT NULL,
  `action` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin NOT NULL,
  `default_value` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`preferencepk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `website`
--

DROP TABLE IF EXISTS `website`;
CREATE TABLE IF NOT EXISTS `website` (
  `websitepk` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `list_url` text COLLATE utf8_bin NOT NULL,
  `search_url` text COLLATE utf8_bin NOT NULL,
  `language` varchar(255) COLLATE utf8_bin NOT NULL,
  `update_frequency` int(11) NOT NULL,
  `last_update` datetime NOT NULL,
  `last_update_status` int(11) NOT NULL,
  `status` binary(1) NOT NULL,
  `name_parser` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`websitepk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `website_joburl`
--

DROP TABLE IF EXISTS `website_joburl`;
CREATE TABLE IF NOT EXISTS `website_joburl` (
  `website_joburlpk` int(11) NOT NULL AUTO_INCREMENT,
  `websitefk` int(11) NOT NULL,
  `parentfk` int(11) NOT NULL,
  `url` text COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`website_joburlpk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;



-- Amit update the position table for the salary fields

ALTER TABLE `position` ADD `salary_low` INT NOT NULL AFTER `salary` ,
ADD `salary_high` INT NOT NULL AFTER `salary_low` ;

ALTER TABLE `industry` ADD `parentfk` INT NOT NULL ;

-- Amit update the position table for change in japanese and english level(21 AUg 2012)

ALTER TABLE `position` CHANGE `english` `english` INT NOT NULL ,
CHANGE `japanese` `japanese` INT NOT NULL ;

ALTER TABLE `position` CHANGE `category` `category` INT NOT NULL COMMENT '0-Normal,1-Featured 2-Paid ';

ALTER TABLE `position` CHANGE `status` `status` INT NOT NULL ;
ALTER TABLE `position` CHANGE `posted_date` `posted_date` DATE NOT NULL ;

-- Amit update postion table for job_type, i.e part time or full time

ALTER TABLE `position` ADD `job_type` INT NOT NULL AFTER `location` ;

ALTER TABLE `position` ADD `work_hours` VARCHAR( 255 ) NOT NULL ,
ADD `lang` VARCHAR( 255 ) NOT NULL ,
ADD `parentfk` INT NOT NULL

-- rights for talent atlas

INSERT INTO `right` (`label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES
('Admin talent atlas', 'Access all the features of talent atlas', 'static', '150-163', '', '', 0),
('Static Login Access', 'Static access right for the login component', 'static', '579-704', '', '', 0),
('Admin User Section', 'Administrator access for the user section. Have rights to perform all the activities.', 'right', '654-321', 'right_admin', '', 0),
('User Section', 'Limited access like manage resume, view jobs, apply job .', 'right', '654-321', 'right_user', '', 0),
('Display home page', 'Access right to see the home page', 'alias', '654-321', 'ppal', 'ppaj', 0),
('Manage Jobs', 'Access right to manage jobs', 'alias', '654-321', 'ppae', 'ppaj', 0),
('Delete positions', 'Access right to delete positions', 'alias', '654-321', 'ppad', 'ppaj', 0),
('Save the created position', 'Access right to save the created position', 'alias', '654-321', 'ppasa', 'ppaj', 0),
('Create new position', 'Access right to create new position', 'alias', '654-321', 'ppaa', 'ppaj', 0),
( 'Company Selector', 'Access right to select the companies from company selector', 'alias', '654-321', 'ppal', 'cmpl', 0);



DROP TABLE IF EXISTS `right_user`;
CREATE TABLE IF NOT EXISTS `right_user` (
  `right_userpk` int(11) NOT NULL AUTO_INCREMENT,
  `rightfk` int(11) NOT NULL,
  `loginfk` int(11) NOT NULL,
  `callback` varchar(255) COLLATE utf8_bin NOT NULL,
  `callback_params` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`right_userpk`),
  KEY `rightfk` (`rightfk`),
  KEY `loginfk` (`loginfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=17 ;

--
-- Dumping data for table `right_user`
--

INSERT INTO `right_user` (`right_userpk`, `rightfk`, `loginfk`, `callback`, `callback_params`) VALUES
(1, 3, 18, '', ''),
(2, 4, 18, '', ''),
(3, 5, 18, '', ''),
(4, 6, 18, '', ''),
(5, 7, 18, '', ''),
(6, 8, 18, '', ''),
(7, 9, 18, '', ''),
(8, 10, 18, '', ''),
(9, 3, 1, '', ''),
(10, 4, 1, '', ''),
(11, 5, 1, '', ''),
(12, 6, 1, '', ''),
(13, 7, 1, '', ''),
(14, 8, 1, '', ''),
(15, 9, 1, '', ''),
(16, 10, 1, '', '');


 --today
ALTER TABLE `position` ADD `temp_industry` VARCHAR(255) NOT NULL ;

-- Amit oct 24

ALTER TABLE `position` ADD `page_title` VARCHAR(255) NOT NULL ,
ADD `meta_desc` TEXT NOT NULL ,
ADD `meta_keywords` VARCHAR(255) NOT NULL ;


-- Stef 29oct: solve slow query when displaying list of jobs
CREATE INDEX idx_company_name ON company(company_name) USING BTREE;

CREATE INDEX idx_parentfk ON industry(parentfk) USING BTREE;
CREATE INDEX idx_status ON industry(status) USING BTREE;

CREATE INDEX idx_companyfk ON `position`(companyfk) USING BTREE;
CREATE INDEX idx_industryfk ON `position`(industryfk) USING BTREE;
CREATE INDEX idx_parentfk ON `position`(parentfk) USING BTREE;
CREATE INDEX idx_jobfk ON `position`(jobfk) USING BTREE;
CREATE INDEX idx_salary_low ON `position`(salary_low) USING BTREE;
CREATE INDEX idx_salary_high ON `position`(salary_high) USING BTREE;

CREATE INDEX idx_websitefk ON `job`(websitefk) USING BTREE;

