
-- Changes in the login table
-- ------

ALTER  TABLE  `login` ADD  `webmail` VARCHAR(255) NOT NULL,
ADD `webpassword` VARCHAR (255) NOT NULL,
ADD `mailport` VARCHAR (128) NOT NULL,
ADD `Imap` VARCHAR (255) NOT NULL,
ADD `aliasName` VARCHAR (255) NOT NULL,
ADD `signature` TEXT NOT NULL;


-- Add webmail table
-- -------

CREATE TABLE IF NOT EXISTS `webmail` (
  `webmailpk` int NOT NULL AUTO_INCREMENT,
  `loginfk` int NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `status` int NOT NULL,
  `date_sent` date NOT NULL,
  PRIMARY KEY (`webmailpk`),
  KEY `webmailpk` (`webmailpk`),
  KEY `webmailpk_2` (`webmailpk`),
  KEY `loginfk` (`loginfk`),
  KEY `date_sent` (`date_sent`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;


-- Add the webmail_recipent table
-- ----
CREATE TABLE IF NOT EXISTS `webmail_recipent` (
  `webmail_recipentpk` int NOT NULL AUTO_INCREMENT,
  `webmailfk` int NOT NULL,
  `loginfk` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `date_sent` date NOT NULL,
  PRIMARY KEY (`webmail_recipentpk`),
  KEY `webmailfk` (`webmailfk`),
  KEY `loginfk` (`loginfk`),
  KEY `email` (`email`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;


-- Add status field in the project table
-- --------
ALTER TABLE `project` ADD `status` INT NOT NULL COMMENT '0-Not Finished,1-Finished';


-- Add the addressbook_document table
-- --------

CREATE TABLE `bcmedia`.`addressbook_document` (
`addressbook_documentpk` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 255 ) NOT NULL ,
`description` TEXT NOT NULL ,
`loginfk` INT NOT NULL ,
`date_create` TIMESTAMP NOT NULL ,
`filename` VARCHAR( 255 ) NOT NULL ,
`path_name` VARCHAR(255) NOT NULL ,
`content` TEXT NOT NULL
) ENGINE = MYISAM ;


--Add the addressbook_document_info table
DROP TABLE IF EXISTS `addressbook_document_info`;
CREATE TABLE IF NOT EXISTS `addressbook_document_info` (
  `addressbook_document_infopk` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `itemfk` int(11) NOT NULL,
  `docfk` int(11) NOT NULL,
  PRIMARY KEY (`addressbook_document_infopk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;


-- STEF 2012-04-13 17h
ALTER TABLE `event_link` CHANGE `cp_uid` `cp_uid` VARCHAR( 255 ) NOT NULL ,
CHANGE `cp_action` `cp_action` VARCHAR( 255 ) NOT NULL ,
CHANGE `cp_type` `cp_type` VARCHAR( 255 ) NOT NULL;

-- Add the shared_document_log table

CREATE TABLE  `shared_document_log` (
`shared_document_logpk` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`docfk` INT NOT NULL ,
`loginfk` INT NOT NULL ,
`status` INT NOT NULL ,
`date` TIMESTAMP NOT NULL
) ENGINE = MYISAM ;

-- Add the company_industry table (Amit - 20/4/2012)

CREATE TABLE  `company_industry` (
`company_industry_pk` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`companyfk` INT NOT NULL ,
`industryfk` INT NOT NULL
) ENGINE = MYISAM ;

-- Add the industry table

CREATE TABLE  `industry` (
`industrypk` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`industry_name` VARCHAR( 255 ) NOT NULL ,
`industry_desc` TEXT NOT NULL
) ENGINE = MYISAM ;


--Add the user_log_activity table (Amit => 24-04-2012)

CREATE TABLE IF NOT EXISTS `user_log_activity` (
  `user_log_activitypk` int(11) NOT NULL AUTO_INCREMENT,
  `loginfk` int(11) NOT NULL,
  `cp_uid` varchar(255) NOT NULL,
  `cp_action` varchar(255) NOT NULL,
  `cp_type` varchar(255) NOT NULL,
  `cp_pk` int(11) NOT NULL,
  `text` text NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_log_activitypk`)
) ENGINE=MyISAM;



-- Stef: 25/04/2012. Table is linked to login, should have login in the name
RENAME TABLE `user_log_activity` TO `login_activity` ;
ALTER TABLE `login_activity` CHANGE `user_log_activitypk` `login_activitypk` INT( 11 ) NOT NULL AUTO_INCREMENT ;


--Add the profil table (Amit => 25-04-2012)

DROP TABLE IF EXISTS `profil`;
CREATE TABLE IF NOT EXISTS `profil` (
  `profilpk` int(11) NOT NULL AUTO_INCREMENT,
  `contactfk` int(11) NOT NULL,
  `companyfk` int(11) NOT NULL,
  `date_end` datetime DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `industryfk` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(128) NOT NULL,
  `fax` varchar(128) NOT NULL,
  `address_1` text NOT NULL,
  `address_2` text NOT NULL,
  `postcode` varchar(128) NOT NULL,
  `cityfk` int(11) NOT NULL,
  `countryfk` int(11) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`profilpk`),
  KEY `countryfk` (`countryfk`),
  KEY `cityfk` (`cityfk`),
  KEY `contactfk` (`contactfk`),
  KEY `companyfk` (`companyfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;



-- Stef: 27/04/2012 : add the date of task completion to be able to send notifications of finished project
ALTER TABLE `task` ADD `date_status_change` DATETIME NULL AFTER `status`;

--Amit 27/04/2012 : add the contact_relation field

ALTER TABLE  `contact` ADD  `contact_relation` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL


-- All the tables query (Amit - 27/04/2012)

--
-- Table structure for table `addressbook_document`
--

DROP TABLE IF EXISTS `addressbook_document`;
CREATE TABLE IF NOT EXISTS `addressbook_document` (
  `addressbook_documentpk` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `loginfk` int(11) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `filename` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `path_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`addressbook_documentpk`),
  KEY `loginfk` (`loginfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `addressbook_document_info`
--

DROP TABLE IF EXISTS `addressbook_document_info`;
CREATE TABLE IF NOT EXISTS `addressbook_document_info` (
  `addressbook_document_infopk` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `itemfk` int(11) NOT NULL,
  `docfk` int(11) NOT NULL,
  PRIMARY KEY (`addressbook_document_infopk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `citypk` int(11) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name_full` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name_kana` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name_kanji` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `postcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `countryfk` int(11) NOT NULL,
  PRIMARY KEY (`citypk`),
  KEY `city_name` (`city_name`),
  KEY `postcode` (`postcode`),
  KEY `name_full` (`name_full`(333)),
  KEY `name_kana` (`name_kana`(333)),
  KEY `name_kanji` (`name_kanji`(333)),
  KEY `countryfk` (`countryfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE IF NOT EXISTS `company` (
  `companypk` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `corporate_name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `website` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `phone` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `fax` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_1` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_2` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `postcode` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cityfk` int(11) NOT NULL,
  `countryfk` int(11) NOT NULL,
  `parentfk` int(11) NOT NULL,
  `followerfk` int(11) NOT NULL,
  `creatorfk` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `date_update` datetime DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `company_relation` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`companypk`),
  KEY `cityfk` (`cityfk`,`countryfk`),
  KEY `parentfk` (`parentfk`),
  KEY `creatorfk` (`creatorfk`),
  KEY `updated_by` (`updated_by`),
  KEY `company_name` (`company_name`(333)),
  KEY `corporate_name` (`corporate_name`(333)),
  KEY `followerfk` (`followerfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `company_industry`
--

DROP TABLE IF EXISTS `company_industry`;
CREATE TABLE IF NOT EXISTS `company_industry` (
  `company_industry_pk` int(11) NOT NULL AUTO_INCREMENT,
  `companyfk` int(11) NOT NULL,
  `industryfk` int(11) NOT NULL,
  PRIMARY KEY (`company_industry_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `contactpk` int(11) NOT NULL AUTO_INCREMENT,
  `courtesy` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `firstname` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `lastname` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_1` text CHARACTER SET utf8 COLLATE utf8_bin,
  `address_2` text CHARACTER SET utf8 COLLATE utf8_bin,
  `postcode` text CHARACTER SET utf8 COLLATE utf8_bin,
  `cityfk` int(11) NOT NULL,
  `countryfk` int(11) NOT NULL,
  `phone` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `cellphone` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `fax` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `loginfk` int(11) NOT NULL DEFAULT '0',
  `followerfk` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_update` datetime NOT NULL,
  `updated_by` int(11) NOT NULL,
  `contact_relation` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`contactpk`),
  KEY `countryfk` (`countryfk`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `lastname_idx` (`lastname`),
  KEY `firstname_idx` (`firstname`),
  KEY `followerfk` (`followerfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `countrypk` int(11) NOT NULL AUTO_INCREMENT,
  `iso` char(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `country_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `printable_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `iso3` char(3) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`countrypk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `eventpk` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_create` datetime NOT NULL,
  `date_display` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_update` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`eventpk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_link`
--

DROP TABLE IF EXISTS `event_link`;
CREATE TABLE IF NOT EXISTS `event_link` (
  `event_linkpk` int(11) NOT NULL AUTO_INCREMENT,
  `eventfk` int(11) NOT NULL,
  `cp_uid` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cp_action` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cp_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cp_pk` int(11) NOT NULL,
  PRIMARY KEY (`event_linkpk`),
  KEY `eventfk` (`eventfk`),
  KEY `cp_uid` (`cp_uid`),
  KEY `cp_action` (`cp_action`),
  KEY `cp_type` (`cp_type`),
  KEY `cp_pk` (`cp_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `industry`
--

DROP TABLE IF EXISTS `industry`;
CREATE TABLE IF NOT EXISTS `industry` (
  `industrypk` int(11) NOT NULL AUTO_INCREMENT,
  `industry_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `industry_desc` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`industrypk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
CREATE TABLE IF NOT EXISTS `login` (
  `loginpk` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(128) COLLATE utf8_bin NOT NULL,
  `password` varchar(256) COLLATE utf8_bin NOT NULL,
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
  `webmail` varchar(25) COLLATE utf8_bin NOT NULL,
  `webpassword` varchar(25) COLLATE utf8_bin NOT NULL,
  `mailport` varchar(10) COLLATE utf8_bin NOT NULL,
  `Imap` varchar(25) COLLATE utf8_bin NOT NULL,
  `aliasName` varchar(25) COLLATE utf8_bin NOT NULL,
  `signature` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`loginpk`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=0 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `login_activity`
--

DROP TABLE IF EXISTS `login_activity`;
CREATE TABLE IF NOT EXISTS `login_activity` (
  `login_activitypk` int(11) NOT NULL AUTO_INCREMENT,
  `loginfk` int(11) NOT NULL,
  `cp_uid` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cp_action` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cp_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cp_pk` int(11) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log_link` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`login_activitypk`),
  KEY `loginfk` (`loginfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `profil`
--

DROP TABLE IF EXISTS `profil`;
CREATE TABLE IF NOT EXISTS `profil` (
  `profilpk` int(11) NOT NULL AUTO_INCREMENT,
  `contactfk` int(11) NOT NULL,
  `companyfk` int(11) NOT NULL,
  `date_end` datetime DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `industryfk` int(11) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `phone` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `fax` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_1` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_2` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `postcode` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cityfk` int(11) NOT NULL,
  `countryfk` int(11) NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`profilpk`),
  KEY `countryfk` (`countryfk`),
  KEY `cityfk` (`cityfk`),
  KEY `contactfk` (`contactfk`),
  KEY `companyfk` (`companyfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `projectpk` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `progress` double NOT NULL,
  `ownerfk` int(11) NOT NULL,
  `creatorfk` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0-Not Finished,1-Finished',
  PRIMARY KEY (`projectpk`),
  KEY `ownerfk` (`ownerfk`),
  KEY `creatorfk` (`creatorfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_actors`
--

DROP TABLE IF EXISTS `project_actors`;
CREATE TABLE IF NOT EXISTS `project_actors` (
  `projectfk` int(11) NOT NULL,
  `loginfk` int(11) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `invited_by` int(11) NOT NULL,
  `date_invited` datetime NOT NULL,
  PRIMARY KEY (`projectfk`,`loginfk`),
  KEY `invited_by` (`invited_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_task`
--

DROP TABLE IF EXISTS `project_task`;
CREATE TABLE IF NOT EXISTS `project_task` (
  `projectfk` int(10) unsigned NOT NULL,
  `loginfk` int(10) unsigned NOT NULL,
  `taskfk` int(10) unsigned NOT NULL,
  `position` int(11) NOT NULL,
  `date_affected` datetime NOT NULL,
  PRIMARY KEY (`projectfk`,`loginfk`,`taskfk`),
  KEY `loginfk` (`loginfk`),
  KEY `taskfk` (`taskfk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shared_document`
--

DROP TABLE IF EXISTS `shared_document`;
CREATE TABLE IF NOT EXISTS `shared_document` (
  `shared_documentpk` int(11) NOT NULL AUTO_INCREMENT,
  `parentfk` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_bin,
  `mime_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_path` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `creatorfk` int(11) NOT NULL,
  `is_public` int(11) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  PRIMARY KEY (`shared_documentpk`),
  KEY `parentfk` (`parentfk`),
  KEY `date_update` (`date_update`),
  KEY `creatorfk` (`creatorfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `shared_document_log`
--

DROP TABLE IF EXISTS `shared_document_log`;
CREATE TABLE IF NOT EXISTS `shared_document_log` (
  `shared_document_logpk` int(11) NOT NULL AUTO_INCREMENT,
  `docfk` int(11) NOT NULL,
  `loginfk` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shared_document_logpk`),
  KEY `loginfk` (`loginfk`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `shared_document_user`
--

DROP TABLE IF EXISTS `shared_document_user`;
CREATE TABLE IF NOT EXISTS `shared_document_user` (
  `shared_document_userpk` int(11) NOT NULL AUTO_INCREMENT,
  `documentfk` int(11) NOT NULL,
  `userfk` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`shared_document_userpk`),
  UNIQUE KEY `documentfk_2` (`documentfk`,`userfk`),
  KEY `documentfk` (`documentfk`),
  KEY `userfk` (`userfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `taskpk` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `progress` double NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `creatorfk` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `affected_to` int(11) NOT NULL,
  `date_affected` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `date_status_change` datetime DEFAULT NULL,
  PRIMARY KEY (`taskpk`),
  KEY `taskpk` (`taskpk`,`affected_to`),
  KEY `creatorfk` (`creatorfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_attachment`
--

DROP TABLE IF EXISTS `task_attachment`;
CREATE TABLE IF NOT EXISTS `task_attachment` (
  `task_attachmentpk` int(11) NOT NULL AUTO_INCREMENT,
  `taskfk` int(11) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `mime_type` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_path` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_upload` datetime NOT NULL,
  `parentfk` int(11) NOT NULL,
  PRIMARY KEY (`task_attachmentpk`),
  KEY `taskfk` (`taskfk`),
  KEY `parentfk` (`parentfk`)
) ENGINE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `webmail`
--

DROP TABLE IF EXISTS `webmail`;
CREATE TABLE IF NOT EXISTS `webmail` (
  `webmailpk` int(11) NOT NULL AUTO_INCREMENT,
  `loginfk` int(11) NOT NULL,
  `subject` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` int(11) NOT NULL,
  `date_sent` date NOT NULL,
  PRIMARY KEY (`webmailpk`),
  KEY `loginfk` (`loginfk`),
  KEY `date_sent` (`date_sent`)
) ENGINE=MyISAM ;

-- --------------------------------------------------------

--
-- Table structure for table `webmail_recipent`
--

DROP TABLE IF EXISTS `webmail_recipent`;
CREATE TABLE IF NOT EXISTS `webmail_recipent` (
  `webmail_recipentpk` int(11) NOT NULL AUTO_INCREMENT,
  `webmailfk` int(11) NOT NULL,
  `loginfk` int(11) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_sent` date NOT NULL,
  PRIMARY KEY (`webmail_recipentpk`),
  KEY `webmailfk` (`webmailfk`),
  KEY `loginfk` (`loginfk`),
  KEY `email` (`email`),
  KEY `type` (`type`)
) ENGINE=MyISAM ;


-- STEF: for migration purpose, to remove later
ALTER TABLE `contact` ADD `externalkey` INT NOT NULL , ADD INDEX ( `externalkey` ) ;
ALTER TABLE `company` ADD `externalkey` INT NOT NULL , ADD INDEX ( `externalkey` ) ;

--Update statement for the live database (Amit - 15/5/2012)

ALTER TABLE `login` CHANGE `lastname` `lastname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `firstname` `firstname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;

ALTER TABLE `login` ADD `webmail` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
ADD `webpassword` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
ADD `mailport` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
ADD `Imap` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
ADD `aliasName` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
ADD `signature` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;


ALTER TABLE `login_access_history` CHANGE `history` `history` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `ip_address` `ip_address` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `session_uid` `session_uid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;

ALTER TABLE `project` ADD `status` INT NOT NULL ;

ALTER TABLE `project` CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;


ALTER TABLE `project_actors` CHANGE `type` `type` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `shared_document` CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `mime_type` `mime_type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `file_name` `file_name` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `file_path` `file_path` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `task` ADD `date_status_change` DATETIME NOT NULL ;

ALTER TABLE `task` CHANGE `title` `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `type` `type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;

ALTER TABLE `task_attachment` CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL ,
CHANGE `type` `type` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `mime_type` `mime_type` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `file_name` `file_name` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
CHANGE `file_path` `file_path` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;

-- AmitB (5/21/2012)

ALTER TABLE `contact` ADD `birthdate` DATE NOT NULL AFTER `email` ;
ALTER TABLE `contact` ADD `grade` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;

-- AmitB (5/21/2012) update queries for the path of the file

update  addressbook_document set path_name = replace (path_name,'/opt/projects/BCM/','/home/BCAdmin/public_html/bc_crm/');
update  shared_document set file_path = replace (file_path,'/var/www/bcmedia/','/home/BCAdmin/public_html/bc_crm/');
update  task_attachment set file_path = replace (file_path,'/var/www/bcmedia/','/home/BCAdmin/public_html/bc_crm/');


ALTER TABLE `addressbook_document` CHANGE `date_create` `date_create` DATETIME NOT NULL ;
ALTER TABLE `login` ADD `valid_status` INT NOT NULL;


-- AmitB (Account_manager table 29/5/2012)

CREATE TABLE IF NOT EXISTS `account_manager` (
  `account_managerpk` int(11) NOT NULL AUTO_INCREMENT,
  `companyfk` int(11) NOT NULL,
  `contactfk` int(11) NOT NULL,
  `loginfk` int(11) NOT NULL,
  PRIMARY KEY (`account_managerpk`),
  KEY `companyfk` (`companyfk`,`contactfk`,`loginfk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


-- AmitB (Contact table 1/6/2012)

ALTER TABLE `contact` ADD `nationalityfk` INT NOT NULL ,
ADD `langfk` INT NOT NULL;

-- AmitB (Language table 1/6/2012)

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `languagepk` int(11) NOT NULL AUTO_INCREMENT,
  `language_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`languagepk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `nationality`;
CREATE TABLE IF NOT EXISTS `nationality` (
  `nationalitypk` int(11) NOT NULL AUTO_INCREMENT,
  `nationality_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`nationalitypk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- AmitB (5/6/2012) -- Change the department

ALTER TABLE `profil` CHANGE `departmentfk` `department` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

-- AmitB (12/6/2012) -- Change the language

ALTER TABLE `contact` CHANGE `langfk` `language` INT( 11 ) NOT NULL;


INSERT INTO locations_tbl (location_name, location_abv) VALUES ('Hiroshima', 'HIR');


-- stef : index / speed optimization
ALTER TABLE `profil` ADD INDEX `speed_idx_date_end` ( `date_end` );
ALTER TABLE `company_industry` ADD INDEX `idx_speed_companyfk` ( `companyfk` ) ;
ALTER TABLE `company_industry` ADD INDEX `speed_idx_industryfk` ( `industryfk` ) ;
ALTER TABLE `account_manager` ADD INDEX `speed_idx_companyfk` ( `companyfk` ) ;
ALTER TABLE `account_manager` ADD INDEX `speed_idx_contactfk` ( `contactfk` ) ;
ALTER TABLE `account_manager` ADD INDEX `speed_idx_loginfk` ( `loginfk` ) ;


-- Amit : Two new tables for the preferences (6/15/2012)

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `login_preference`;
CREATE TABLE IF NOT EXISTS `login_preference` (
  `login_preferencepk` int(11) NOT NULL AUTO_INCREMENT,
  `loginfk` int(11) NOT NULL,
  `user_preferencefk` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`login_preferencepk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


ALTER TABLE `login_preference` CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;


-- Stef 05-07: fix the contcat relation field
ALTER TABLE `contact` CHANGE `contact_relation` `relationfk` INT( 11 ) NOT NULL;
ALTER TABLE `contact` ADD INDEX `ct_relation_speed_idx` (`relationfk`) ;

DELETE FROM login_preference WHERE loginfk  > 0;

/*New things need to be updated in db */

-- Amit 8 -01 add 2 fields in login table

ALTER TABLE `login` ADD `gender` INT NOT NULL AFTER `password` ,
ADD `courtesy` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL AFTER `gender` ;

 -- Amit 8 -03

ALTER TABLE `task` DROP `affected_to`  ; --(Not executed in live till now)

DROP TABLE IF EXISTS `project_user`;
CREATE TABLE IF NOT EXISTS `project_user` (
  `project_userpk` int(11) NOT NULL AUTO_INCREMENT,
  `loginfk` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`project_userpk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


-- Amit  8- 29


CREATE TABLE IF NOT EXISTS `settings` (
  `settingspk` int(11) NOT NULL AUTO_INCREMENT,
  `fieldname` varchar(255) COLLATE utf8_bin NOT NULL,
  `fieldtype` varchar(255) COLLATE utf8_bin NOT NULL,
  `value` text COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`settingspk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


-- Amit 9 - 3

ALTER TABLE `login`
  DROP `valid_status`;  --(Not executed in live till now)


INSERT INTO `settings` (`settingspk`, `fieldname`, `fieldtype`, `value`, `description`) VALUES
(1, 'css', 'text', 'bcm.css', 'Name of the css of the website'),
(2, 'meta_tags', 'text', 'crm, content management system, customer relation, client, connection,company,manager', 'Meta tags and description for website'),
(9, 'footer', 'serialized', 'a:3:{i:0;a:7:{s:4:"name";s:4:"Home";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"579-704";s:4:"type";s:3:"usr";s:6:"action";s:4:"list";s:2:"pk";i:0;}i:1;a:7:{s:4:"name";s:6:"Google";s:4:"link";s:21:"http://www.google.com";s:6:"target";s:6:"_blank";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;}i:2;a:7:{s:4:"name";s:12:"Report a bug";s:4:"link";s:31:"mailto:sboudoux@bulbouscell.com";s:6:"target";s:6:"_blank";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;}}', 'Footer Parameters'),
(17, 'menu', 'serialized', 'a:8:{i:0;a:8:{s:4:"name";s:4:"Home";s:4:"link";s:0:"";s:4:"icon";s:20:"pictures/home_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;}i:1;a:9:{s:4:"name";s:16:"Connections List";s:4:"link";s:0:"";s:4:"icon";s:26:"pictures/connection_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"ct";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"child";a:2:{i:0;a:9:{s:4:"name";s:14:"My Connections";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"ct";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"loginpk";i:1;s:7:"onclick";s:0:"";}i:1;a:8:{s:4:"name";s:18:"Search Connections";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"ct";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"onclick";s:21:"resetContactSearch();";}}}i:2;a:9:{s:4:"name";s:14:"Companies List";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/company_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"cp";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"child";a:2:{i:0;a:9:{s:4:"name";s:12:"My Companies";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"cp";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"loginpk";i:1;s:7:"onclick";s:0:"";}i:1;a:8:{s:4:"name";s:16:"Search Companies";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"cp";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"onclick";s:21:"resetCompanySearch();";}}}i:3;a:9:{s:4:"name";s:8:"Projects";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/project_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:3:"prj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"child";a:3:{i:0;a:9:{s:4:"name";s:8:"My tasks";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:4:"task";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"loginpk";i:1;s:7:"onclick";s:0:"";}i:1;a:8:{s:4:"name";s:13:"Projects List";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:3:"prj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"onclick";s:0:"";}i:3;a:8:{s:4:"name";s:13:"Project Users";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:6:"prjacr";s:6:"action";s:4:"ppae";s:2:"pk";i:0;s:7:"onclick";s:0:"";}}}i:4;a:8:{s:4:"name";s:20:"Shared document List";s:4:"link";s:0:"";s:4:"icon";s:28:"pictures/shared_space_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"999-111";s:4:"type";s:5:"shdoc";s:6:"action";s:4:"ppal";s:2:"pk";i:0;}i:5;a:8:{s:4:"name";s:8:"Contacts";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/contact_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"579-704";s:4:"type";s:3:"usr";s:6:"action";s:4:"ppal";s:2:"pk";i:0;}i:6;a:9:{s:4:"name";s:4:"Mail";s:4:"link";s:37:"https://mail.bulbouscell.com/webmail/";s:4:"icon";s:20:"pictures/mail_48.png";s:6:"target";s:6:"_blank";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:9:"embedLink";i:1;}i:7;a:10:{s:4:"name";s:8:"Web Mail";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/webmail_48.png";s:6:"target";s:0:"";s:3:"uid";s:7:"009-724";s:4:"type";s:7:"webmail";s:6:"action";s:4:"ppaa";s:2:"pk";i:0;s:9:"ajaxpopup";i:1;s:7:"loginpk";i:1;}}', 'Menu Parameters'),
(20, 'meta_desc', 'text', 'bcm is bcm master', 'Meta description for website'),
(24, 'title', 'text', 'Bulbouscell Master', 'Title of the website'),
(25, 'logo', 'image', 'bcm_logo.png', 'Website logo'),
(26, 'sitename', 'text', 'BCM', 'Site Name '),
(27, 'site_email', 'text', 'info@bcm.com', 'Site Email Address');



-- Amit 9 - 7 Create table shared_document_editor

CREATE TABLE IF NOT EXISTS `shared_document_editor` (
  `shared_document_editorpk` int(11) NOT NULL AUTO_INCREMENT,
  `documentfk` int(11) NOT NULL,
  `userfk` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`shared_document_editorpk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- Amit 9 - 7 Add one field in the shared_document table to know if it can be edited publicly

ALTER TABLE `shared_document` ADD `is_edit_public` INT NOT NULL AFTER `is_public` ;
ALTER TABLE `shared_document` CHANGE `is_public` `is_public` INT( 11 ) NOT NULL COMMENT '0-private,1-public,2-custom';


-- Amit 9-10 Add one field in the project table to know if it can be edited publicly

ALTER TABLE `project` ADD `is_edit_public` INT NOT NULL ;


-- Amit 9/20


--
-- Table structure for table `customfield`
--

DROP TABLE IF EXISTS `customfield`;
CREATE TABLE IF NOT EXISTS `customfield` (
  `customfieldpk` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) COLLATE utf8_bin NOT NULL,
  `action` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin NOT NULL,
  `pk` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `label` varchar(255) COLLATE utf8_bin NOT NULL,
  `fieldtype` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`customfieldpk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customfield_value`
--

DROP TABLE IF EXISTS `customfield_value`;
CREATE TABLE IF NOT EXISTS `customfield_value` (
  `customfield_valuepk` int(11) NOT NULL AUTO_INCREMENT,
  `customfieldfk` int(11) NOT NULL,
  `itemfk` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`customfield_valuepk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


ALTER TABLE `customfield` ADD `description` VARCHAR( 255 ) NOT NULL AFTER `label` ;
ALTER TABLE `customfield_value` ADD INDEX `customfieldfk` ( `customfieldfk` );
ALTER TABLE `customfield_value` ADD INDEX `itemfk` ( `itemfk` );
ALTER TABLE `customfield_value` CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;

ALTER TABLE `login` ADD `log_hash` TEXT NOT NULL AFTER `date_last_log` ;


--- Amit (9/26)

ALTER TABLE `login` ADD `pseudo` VARCHAR( 255 ) AFTER `password` ;
ALTER TABLE `login` ADD `birthdate` DATE NOT NULL AFTER `pseudo` ;

-- Update for v2.2 starting here
-- 29oct zimbra

CREATE TABLE IF NOT EXISTS `zimbra_attendees` (
  `zimbra_attendeespk` int(11) NOT NULL AUTO_INCREMENT,
  `zcalfk` int(11) NOT NULL,
  `loginfk` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`zimbra_attendeespk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `zimbra_cal`;
CREATE TABLE IF NOT EXISTS `zimbra_cal` (
  `zimbra_calpk` int(11) NOT NULL AUTO_INCREMENT,
  `creatorfk` int(11) NOT NULL,
  `invId` varchar(255) COLLATE utf8_bin NOT NULL,
  `apptId` int(11) NOT NULL,
  `msId` int(11) NOT NULL,
  `starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `endtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `data` text COLLATE utf8_bin NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`zimbra_calpk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `zimbra_user` (
  `zimbra_userpk` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `authkey` varchar(255) COLLATE utf8_bin NOT NULL,
  `calendarIds` varchar(255) COLLATE utf8_bin NOT NULL,
  `isAdmin` binary(1) NOT NULL DEFAULT '0',
  `timezone` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`zimbra_userpk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;


ALTER TABLE `login` ADD `teamfk` INT NOT NULL AFTER `status`;
ALTER TABLE `login` CHANGE `teamfk` `teamfk` INT( 11 ) NOT NULL COMMENT '1: sales, 2:It, 3:Manag, 4:Prod, 5:Admin, 6:other ';
ALTER TABLE `event_link` ADD `cp_params` TEXT NULL;


UPDATE `settings` SET `value` = 'a:8:{i:0;a:8:{s:4:"name";s:4:"Home";s:4:"link";s:0:"";s:4:"icon";s:20:"pictures/home_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"579-704";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;}i:1;a:9:{s:4:"name";s:16:"Connections List";s:4:"link";s:0:"";s:4:"icon";s:26:"pictures/connection_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"ct";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"child";a:3:{i:0;a:9:{s:4:"name";s:14:"My Connections";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"ct";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"loginpk";i:1;s:7:"onclick";s:0:"";}i:1;a:8:{s:4:"name";s:18:"Search Connections";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"ct";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"onclick";s:19:"addParameter(this);";}i:2;a:9:{s:4:"name";s:15:"Add Connections";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"ct";s:6:"action";s:4:"ppaa";s:2:"pk";i:0;s:7:"onclick";s:0:"";s:4:"icon";s:55:"/component/addressbook/resources/pictures/ct_add_16.png";}}}i:2;a:9:{s:4:"name";s:14:"Companies List";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/company_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"cp";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"child";a:3:{i:0;a:9:{s:4:"name";s:12:"My Companies";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"cp";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"loginpk";i:1;s:7:"onclick";s:0:"";}i:1;a:8:{s:4:"name";s:16:"Search Companies";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"cp";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"onclick";s:43:" addParameter(this); resetCompanySearch(); ";}i:2;a:9:{s:4:"name";s:11:"Add Company";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"777-249";s:4:"type";s:2:"cp";s:6:"action";s:4:"ppaa";s:2:"pk";i:0;s:7:"onclick";s:0:"";s:4:"icon";s:55:"/component/addressbook/resources/pictures/cp_add_16.png";}}}i:3;a:9:{s:4:"name";s:8:"Projects";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/project_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:3:"prj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:5:"child";a:3:{i:0;a:9:{s:4:"name";s:8:"My tasks";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:4:"task";s:6:"action";s:5:" ppal";s:2:"pk";i:0;s:7:"loginpk";i:1;s:7:"onclick";s:0:"";}i:1;a:8:{s:4:"name";s:13:"Projects List";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:4:" prj";s:6:"action";s:4:"ppal";s:2:"pk";i:0;s:7:"onclick";s:0:"";}i:3;a:8:{s:4:"name";s:13:"Project Users";s:4:"link";s:0:"";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"456-789";s:4:"type";s:6:"prjacr";s:6:"action";s:4:"ppae";s:2:"pk";i:0;s:7:"onclick";s:0:"";}}}i:4;a:8:{s:4:"name";s:20:"Shared document List";s:4:"link";s:0:"";s:4:"icon";s:28:"pictures/shared_space_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"999-111";s:4:"type";s:5:"shdoc";s:6:"action";s:4:"ppal";s:2:"pk";i:0;}i:5;a:8:{s:4:"name";s:8:"Contacts";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/contact_48.png";s:6:"target";s:7:"_parent";s:3:"uid";s:7:"579-704";s:4:"type";s:3:"usr";s:6:"action";s:5:" ppal";s:2:"pk";i:0;}i:8;a:11:{s:4:"name";s:8:"Calendar";s:4:"link";s:0:"";s:4:"icon";s:24:"pictures/calendar_48.png";s:6:"target";s:0:"";s:3:"uid";s:7:"400-650";s:4:"type";s:8:"calendar";s:6:"action";s:4:"ppav";s:2:"pk";i:-1;s:9:"ajaxpopup";i:0;s:7:"loginpk";i:1;s:5:"child";a:3:{i:0;a:7:{s:4:"name";s:11:"My calendar";s:3:"uid";s:7:"400-650";s:4:"type";s:8:"calendar";s:6:"action";s:4:"ppav";s:2:"pk";i:-1;s:9:"ajaxpopup";i:0;s:4:"icon";s:38:"/common/pictures/items/calendar_16.png";}i:1;a:7:{s:4:"name";s:15:"Shared calendar";s:3:"uid";s:7:"400-650";s:4:"type";s:8:"calendar";s:6:"action";s:4:"ppav";s:2:"pk";i:0;s:9:"ajaxpopup";i:0;s:4:"icon";s:38:"/common/pictures/items/calendar_16.png";}i:2;a:7:{s:4:"name";s:29:"Add an event in your calendar";s:3:"uid";s:7:"400-650";s:4:"type";s:8:"calendar";s:6:"action";s:4:"ppaa";s:2:"pk";i:0;s:9:"ajaxpopup";i:0;s:4:"icon";s:42:"/common/pictures/items/calendar_add_16.png";}}}i:9;a:11:{s:4:"name";s:14:"Other features";s:4:"link";s:12:"javascript:;";s:4:"icon";s:26:"pictures/menu_other_48.png";s:6:"target";s:0:"";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:9:"ajaxpopup";i:0;s:7:"loginpk";i:1;s:5:"child";a:2:{i:0;a:9:{s:4:"name";s:7:"Webmail";s:4:"link";s:28:"https://mail.bulbouscell.com";s:4:"icon";s:20:"pictures/mail_24.png";s:6:"target";s:6:"_blank";s:3:"uid";s:0:"";s:4:"type";s:0:"";s:6:"action";s:0:"";s:2:"pk";i:0;s:9:"embedLink";i:0;}i:1;a:10:{s:4:"name";s:8:"BCM Mail";s:4:"link";s:0:"";s:4:"icon";s:23:"pictures/webmail_24.png";s:6:"target";s:0:"";s:3:"uid";s:7:"009-724";s:4:"type";s:7:"webmail";s:6:"action";s:4:"ppaa";s:2:"pk";i:0;s:9:"ajaxpopup";i:1;s:7:"loginpk";i:1;}}}}' WHERE `settings`.`settingspk` = xxx;
--

INSERT INTO `right` (`label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES
('Static right for portal', 'Static access for portal component', 'static', '111-111', '', '', 0),
('Static right for charts', 'Static access for charts component', 'static', '222-222', '', '', 0),
('Static right for zimbra', 'Static access for zimbra component', 'static', '400-650', '', '', 0);


-- fix zimbra
ALTER TABLE `zimbra_user` ADD `loginfk` INT NOT NULL AFTER `zimbra_userpk` ,
ADD INDEX ( `loginfk` );



-- stef 2013-01-15

ALTER TABLE `profil` ADD `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;


-- stef 2013-01-17: add doc type in shared space
ALTER TABLE `shared_document` ADD `doc_typefk` integer NOT NULL DEFAULT 0;
ALTER TABLE `shared_document` ADD INDEX ( `doc_typefk` ) ;


CREATE TABLE IF NOT EXISTS `shared_doc_type` (
  `shared_doc_typepk` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL,
  `parentfk` int(11) NOT NULL,
  PRIMARY KEY (`shared_doc_typepk`),
  KEY `parentfk` (`parentfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Dumping data for table `shared_doc_type`
--

INSERT INTO `shared_doc_type` (`shared_doc_typepk`, `label`, `description`, `parentfk`) VALUES
(1, 'Media', 'Contains all the design and documents created by the production team', 0),
(2, 'Proposals', '', 1),
(3, 'Mockups', '', 1),
(4, 'Mediakit & sales tools', '', 1),
(5, 'IT & technical', '', 0),
(6, 'Administrative', '', 0);


-- stef: 2013-01-18: db optimization for addressbook document tables
ALTER TABLE `addressbook_document_info` ADD INDEX ( `type` ) ;
ALTER TABLE `addressbook_document_info` ADD INDEX ( `itemfk` ) ;
ALTER TABLE `addressbook_document_info` ADD INDEX ( `docfk` ) ;

-- right to delete event reminders
INSERT INTO `right` (`label` ,`description` ,`type` ,`cp_uid` ,`cp_action` ,`cp_type` ,`cp_pk`)
VALUES ('Alias delete event remind', 'Allow to delete reminders set on events', 'alias', '007-770', 'ppad', 'evtrem', '');
INSERT INTO `right_tree` (rightfk, parentfk)
VALUES ((SELECT rightpk FROM `right` WHERE 1 ORDER BY rightpk DESC LIMIT 1), 2);


--
-- Table structure for table `event_reminder`
--

CREATE TABLE IF NOT EXISTS `event_reminder` (
  `event_reminderpk` int(11) NOT NULL AUTO_INCREMENT,
  `eventfk` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_reminder` datetime NOT NULL,
  `notify_delay` varchar(128) COLLATE utf8_bin NOT NULL,
  `loginfk` int(11) NOT NULL,
  `message` text COLLATE utf8_bin,
  `sent` int(11) NOT NULL,
  PRIMARY KEY (`event_reminderpk`),
  KEY `eventfk` (`eventfk`),
  KEY `loginfk` (`loginfk`),
  KEY `sent` (`sent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;