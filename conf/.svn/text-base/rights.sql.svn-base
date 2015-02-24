CREATE TABLE IF NOT EXISTS `right` (
  `rightpk` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `type` varchar(128) COLLATE utf8_bin NOT NULL,
  `cp_uid` varchar(64) COLLATE utf8_bin NOT NULL,
  `cp_action` varchar(64) COLLATE utf8_bin NOT NULL,
  `cp_type` varchar(64) COLLATE utf8_bin NOT NULL,
  `cp_pk` int(11) NOT NULL,
  PRIMARY KEY (`rightpk`),
  KEY `cp_uid` (`cp_uid`),
  KEY `cp_action` (`cp_action`),
  KEY `cp_type` (`cp_type`),
  KEY `cp_uid_2` (`cp_uid`,`cp_action`,`cp_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `right`
--

INSERT INTO `right` (`rightpk`, `label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES
(1, 'AB admin', 'Full access to all address book', 'right', '777-249', 'right_admin', '', 0),
(2, 'AB manager', 'Can access most of the features. Can''t delete connection and companies', 'right', '777-249', 'right_manager', '', 0),
(3, 'AB viewer', 'Can view address book', 'right', '777-249', 'right_viewer', '', 0),
(4, 'Alias view connection', 'View connection ', 'alias', '777-249', 'ppav', 'ct', 0),
(5, 'Alias list connection', 'list / search connection', 'alias', '777-249', 'ppal', 'ct', 0),
(6, 'Alias view company', 'View company ', 'alias', '777-249', 'ppav', 'cp', 0),
(7, 'Alias list companiies', 'list / search companies', 'alias', '777-249', 'ppal', 'cp', 0),
(8, 'Login static access', 'Allow to access login', 'static', '579-704', '', '', 0),
(9, 'Project Admin', 'Full access to the project component ', 'right', '456-789', 'right_admin', '', 0),
(10, 'Project Manager', 'Can access most of the features. Can not remove the projects and tasks', 'right', '456-789', 'right_manager', '', 0),
(11, 'Project Viewer', 'Can View the list of the projects and view the project information', 'right', '456-789', 'right_viewer', '', 0),
(12, 'Alias project view', 'View Project ', 'alias', '456-789', 'ppav', 'prj', 0),
(13, 'Alias project list', 'List project ', 'alias', '456-789', 'ppal', 'prj', 0),
(14, 'Sharedspace admin', 'Full access to sharedspace component', 'right', '999-111', 'right_admin', '', 0),
(15, 'Sharedspace manager', 'Access to most of the features but can not remove the shared document', 'right', '999-111', 'right_manager', '', 0),
(16, 'Alias sharedspace list', 'List Shared Documents ', 'alias', '999-111', 'ppal', 'shdoc', 0),
(17, 'Alias sharedspace download', 'Download Shared Documents ', 'alias', '999-111', 'ppasen', 'shdoc', 0),
(18, 'Add Company', 'Access rights to add a new company', 'alias', '777-249', 'ppaa', 'cp', 0),
(19, 'Add Connection', 'Access rights to add a new connection', 'alias', '777-249', 'ppaa', 'ct', 0),
(20, 'Edit Company', 'Access Rights to edit company', 'alias', '777-249', 'ppae', 'cp', 0),
(21, 'Edit Connection', 'Access Rights to edit connection', 'alias', '777-249', 'ppae', 'ct', 0),
(22, 'Save Company', 'Access rights to save Company', 'alias', '777-249', 'ppasa', 'cp', 0),
(23, 'Save Connection', 'Access rights to save Connection', 'alias', '777-249', 'ppasa', 'ct', 0),
(24, 'Save Company Edit', 'Access rights to save Edited Company', 'alias', '777-249', 'ppase', 'cp', 0),
(25, 'Save Connection Edit', 'Access rights to save Edited Connection', 'alias', '777-249', 'ppase', 'ct', 0),
(26, 'Change Company Account Manager', 'Access Right to change company account manager', 'alias', '777-249', 'ppat', 'cp', 0),
(27, 'Change Connection Account Manager', 'Access Right to change connection account manager', 'alias', '777-249', 'ppat', 'ct', 0),
(28, 'Manage Company ', 'Access right to link company', 'alias', '777-249', 'ppam', 'cp', 0),
(29, 'Manage Connection ', 'Access right to link connection', 'alias', '777-249', 'ppam', 'ct', 0),
(30, 'Save Company Account Manager', 'Access right to save company account manager', 'alias', '777-249', 'ppast', 'cp', 0),
(31, 'Save Connection Account Manager', 'Access right to save connection account manager', 'alias', '777-249', 'ppast', 'ct', 0),
(32, 'Save Link Company', 'Access right to save link company ', 'alias', '777-249', 'ppasm', 'cp', 0),
(33, 'Save Link Connection ', 'Access right to save link connection', 'alias', '777-249', 'ppasm', 'ct', 0),
(34, 'Webmail Static Access', 'Static access for the webmail component', 'static', '009-724', '', '', 0),
(35, 'Delete Company', 'Right to remove the company', 'alias', '777-249', 'ppad', 'cp', 0),
(36, 'Delete Connection', 'Right to remove the connection', 'alias', '777-249', 'ppad', 'ct', 0),
(37, 'Add Business Profile', 'Add business profile to  connection', 'alias', '777-249', 'ppaa', 'cpr', 0),
(38, 'Save Business Profile', 'Save business profile to  connection', 'alias', '777-249', 'ppacpr', 'cpr', 0),
(39, 'Upload document', 'Access to upload document', 'alias', '777-249', 'ppaa', 'doc', 0),
(40, 'save uploaded document', 'Access to save the uploaded document', 'alias', '777-249', 'ppasa', 'doc', 0),
(41, 'Download  connection document', 'Access to download connection document', 'alias', '777-249', 'ppasen', 'ct', 0),
(42, 'Download company document', 'Access to download company document', 'alias', '777-249', 'ppasen', 'cp', 0),
(47, 'Add Event ', 'Access to add the events ', 'alias', '007-770', 'ppaa', 'event', 0),
(48, 'Edit Event ', 'Access to edit the events ', 'alias', '007-770', 'ppae', 'event', 0),
(49, 'Delete Event ', 'Access to remove the events ', 'alias', '007-770', 'ppad', 'event', 0),
(50, 'Save Event', 'Access Right to save the events', 'alias', '007-770', 'ppasa', 'event', 0),
(51, 'Save Project', 'Access right to save project', 'alias', '456-789', 'ppasa', 'prj', 0),
(52, 'Save Edited Project', 'Access right to save edited project', 'alias', '456-789', 'ppase', 'prj', 0),
(53, 'Delete Project', 'Access right to remove the project', 'alias', '456-789', 'ppad', 'prj', 0),
(54, 'Listing Tasks', 'Access right to list task ', 'alias', '456-789', 'ppal', 'task', 0),
(55, 'Add Task', 'Access right to add task ', 'alias', '456-789', 'ppaa', 'task', 0),
(56, 'Save Added Task', 'Access right to save added task ', 'alias', '456-789', 'ppasa', 'task', 0),
(57, 'Save Edited Task', 'Access right to save edited task ', 'alias', '456-789', 'ppase', 'task', 0),
(58, 'Edit Task', 'Access right to edit task ', 'alias', '456-789', 'ppae', 'task', 0),
(59, 'Update Task', 'Access right to update task ', 'alias', '456-789', 'ppaupd', 'task', 0),
(60, 'Update Task Status', 'Access right to update task status', 'alias', '456-789', 'ppado', 'task', 0),
(61, 'Delete Task', 'Access right to remove task', 'alias', '456-789', 'ppad', 'task', 0),
(62, 'Upload attachment to project ', 'Access right to upload attachment to project', 'alias', '456-789', 'ppaa', 'attch', 0),
(63, 'Save Project Actors ', 'Access right to save project actors', 'alias', '456-789', 'ppase', 'prjacr', 0),
(64, 'View Graphical View ', 'Access right to view graphical view ', 'alias', '456-789', 'ppavd', 'prj', 0),
(65, 'View Task ', 'Access right to view task ', 'alias', '456-789', 'ppav', 'task', 0),
(66, 'Save Added Attachment for Project ', 'Access right to save added attachment for the project ', 'alias', '456-789', 'ppasa', 'attch', 0),
(67, 'Save Edited Attachment for Project ', 'Access right to save edited attachment for the project ', 'alias', '456-789', 'ppase', 'attch', 0),
(68, 'Edit Project Actors ', 'Access right to edit project actors ', 'alias', '456-789', 'ppae', 'prjacr', 0),
(69, 'Static Mail Access', 'Static access for the BCMedia email link', 'static', '845-187', '', '', 0),
(70, 'Search Companies List ', 'Access right to search companies list in the selector ', 'alias', '777-249', 'ppasea', 'cp', 0),
(71, 'Save Business Add Profile ', 'Access to save business add profile', 'alias', '777-249', 'ppasa', 'cpr', 0),
(72, 'Save Business Edit Profile ', 'Access to save business edit profile', 'alias', '777-249', 'ppase', 'cpr', 0),
(73, 'Edit Business Profile', 'Access right to edit business profile', 'alias', '777-249', 'ppae', 'cpr', 0);

-- --------------------------------------------------------
--
-- Table structure for table `right_tree`
--

CREATE TABLE IF NOT EXISTS `right_tree` (
  `right_treepk` int(11) NOT NULL AUTO_INCREMENT,
  `rightfk` int(11) NOT NULL,
  `parentfk` int(11) NOT NULL,
  PRIMARY KEY (`right_treepk`),
  UNIQUE KEY `rightfk_2` (`rightfk`,`parentfk`),
  KEY `rightfk` (`rightfk`),
  KEY `parentfk` (`parentfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `right_tree`
--

INSERT INTO `right_tree` (`right_treepk`, `rightfk`, `parentfk`) VALUES
(1, 2, 1),
(2, 3, 1),
(3, 4, 1),
(4, 5, 1),
(5, 6, 1),
(6, 7, 1),
(7, 3, 2),
(8, 4, 2),
(9, 5, 2),
(10, 6, 2),
(11, 7, 2),
(12, 5, 3),
(13, 7, 3),
(14, 10, 9),
(15, 11, 9),
(16, 12, 9),
(17, 13, 9),
(18, 11, 10),
(19, 12, 10),
(20, 13, 10),
(21, 12, 11),
(22, 13, 11),
(23, 15, 14),
(24, 16, 14),
(25, 17, 14),
(26, 16, 15),
(27, 17, 15),
(28, 18, 1),
(29, 19, 1),
(30, 20, 1),
(31, 21, 1),
(32, 18, 2),
(33, 19, 2),
(34, 20, 2),
(35, 21, 2),
(49, 27, 1),
(48, 26, 1),
(40, 22, 1),
(41, 23, 1),
(42, 24, 1),
(43, 25, 1),
(44, 22, 2),
(45, 23, 2),
(46, 24, 2),
(47, 25, 2),
(52, 28, 1),
(53, 29, 1),
(54, 30, 1),
(55, 31, 1),
(56, 32, 1),
(57, 33, 1),
(58, 28, 2),
(59, 29, 2),
(62, 32, 2),
(63, 33, 2),
(64, 35, 1),
(65, 36, 1),
(66, 37, 1),
(67, 37, 2),
(68, 38, 1),
(69, 38, 2),
(70, 39, 1),
(71, 40, 1),
(72, 39, 2),
(73, 40, 2),
(74, 41, 1),
(75, 42, 1),
(76, 41, 2),
(77, 42, 2),
(81, 47, 2),
(82, 48, 2),
(83, 49, 1),
(86, 47, 1),
(87, 48, 1),
(88, 49, 2),
(89, 50, 2),
(90, 50, 1),
(91, 51, 9),
(92, 52, 9),
(93, 53, 9),
(94, 51, 10),
(95, 52, 10),
(96, 54, 9),
(97, 54, 10),
(98, 54, 11),
(99, 55, 9),
(100, 55, 10),
(101, 56, 9),
(102, 56, 10),
(103, 57, 9),
(104, 57, 10),
(105, 58, 9),
(106, 58, 10),
(107, 59, 9),
(108, 59, 10),
(109, 60, 9),
(110, 60, 10),
(111, 61, 9),
(112, 62, 9),
(113, 62, 10),
(114, 63, 9),
(115, 63, 10),
(116, 64, 9),
(117, 64, 10),
(118, 64, 11),
(119, 65, 9),
(120, 65, 10),
(121, 65, 11),
(122, 66, 9),
(123, 67, 9),
(124, 66, 10),
(125, 67, 10),
(126, 68, 9),
(127, 68, 10),
(128, 70, 1),
(129, 70, 2),
(130, 71, 1),
(131, 71, 2),
(132, 72, 1),
(133, 72, 2),
(134, 73, 1),
(135, 73, 2);

-- --------------------------------------------------------

--
-- Table structure for table `right_user`
--

CREATE TABLE IF NOT EXISTS `right_user` (
  `right_userpk` int(11) NOT NULL AUTO_INCREMENT,
  `rightfk` int(11) NOT NULL,
  `loginfk` int(11) NOT NULL,
  `callback` varchar(255) COLLATE utf8_bin NOT NULL,
  `callback_params` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`right_userpk`),
  KEY `rightfk` (`rightfk`),
  KEY `loginfk` (`loginfk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `right_user`
--


-- Amit (sep 20)

INSERT INTO `right` (`rightpk`, `label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES
(NULL, 'Edit the attachment of company or connection', 'Access right to edit the attachment of company or connection', 'alias', '777-249', 'ppae', 'doc', '');

INSERT INTO `right` (`rightpk`, `label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES
(NULL, 'Delete the attachment of company or connection', 'Access right to delete attachment of company or connection', 'alias', '777-249', 'ppad', 'doc', '');

INSERT INTO `right_tree` (`right_treepk`, `rightfk`, `parentfk`)
 VALUES (NULL, '83', '1'), (NULL, '83', '2');

INSERT INTO `right_tree` (`right_treepk`, `rightfk`, `parentfk`)
 VALUES (NULL, '84', '1');


-- Amit (sep 24, CF Component)


INSERT INTO `right` (`rightpk`, `label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES
(85, 'Custom Field Admin', 'Admin right for custom field component', 'right', '180-290', 'right_admin', '', 0),
(86, 'Custom Field Manager', 'Can add one field at one time. Can not update the existing custom fields', 'right', '180-290', 'right_manager', '', 0),
(88, ' Add custom fields', 'Access right to add custom fields ', 'alias', '180-290', 'ppaa', 'csm', 0),
(89, ' Edit custom fields', 'Access right to edit custom fields ', 'alias', '180-290', 'ppae', 'csm', 0),
(90, ' Save custom fields', 'Access right to save added custom fields ', 'alias', '180-290', 'ppasa', 'csm', 0),
(91, ' Update custom fields', 'Access right to update  custom fields ', 'alias', '180-290', 'ppau', 'csm', 0),
(92, 'Add custom fields for all items', 'Access right to add custom field for all the entries', 'alias', '180-290', 'ppaall', 'csm', 0);



INSERT INTO `right_tree` (`right_treepk`, `rightfk`, `parentfk`) VALUES
(155, 88, 85),
(156, 88, 86),
(157, 90, 85),
(158, 90, 86),
(159, 89, 85),
(160, 91, 85),
(161, 92, 85),
(162, 86, 85);


INSERT INTO `right_tree` (`right_treepk`, `rightfk`, `parentfk`) VALUES
(NULL, '89', '86'), (NULL, '91', '86');


-- rights for portal and charts component

INSERT INTO `right` (`rightpk`, `label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES (NULL, 'Static right for portal', 'Static access for portal component', 'static', '111-111', '', '', '');
INSERT INTO `right` (`rightpk`, `label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES (NULL, 'Static right for charts', 'Static access for charts component', 'static', '222-222', '', '', '');
INSERT INTO `right` (`rightpk`, `label`, `description`, `type`, `cp_uid`, `cp_action`, `cp_type`, `cp_pk`) VALUES (NULL, 'Static right for zimbra', 'Static access for zimbra component', 'static', '400-650', '', '', '');