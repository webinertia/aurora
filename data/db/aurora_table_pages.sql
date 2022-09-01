CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(10) UNSIGNED DEFAULT NULL,
  `ownerId` int(11) UNSIGNED DEFAULT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iconClass` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `params` json DEFAULT NULL,
  `rel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rev` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resource` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `privilege` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visible` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `query` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isGroupPage` tinyint(1) UNSIGNED DEFAULT NULL,
  `allowComments` tinyint(1) UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `isLandingPage` tinyint(1) UNSIGNED DEFAULT NULL,
  `cmsType` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `createdDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updatedDate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `showOnLandingPage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `pages` (`id`, `parentId`, `ownerId`, `label`, `title`, `class`, `iconClass`, `order`, `params`, `rel`, `rev`, `resource`, `privilege`, `visible`, `route`, `uri`, `action`, `query`, `isGroupPage`, `allowComments`, `content`, `isLandingPage`, `cmsType`, `createdDate`, `updatedDate`, `keywords`, `description`, `showOnLandingPage`) VALUES
(1, 0, 1, 'HomeLandingPage', 'homelandingpage', 'nav-link', NULL, 1, '{\"title\": \"homelandingpage\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, NULL, NULL, NULL, NULL, '<p>Congratulations! You have successfully installed <a href=\"https://github.com/Tyrsson/aurora-2.0/wiki\" target=\"_blank\" rel=\"noopener\">ACMS</a>. testing</p>', 1, NULL, '07-23-2022 5:12:21', '07-23-2022 9:26:01', NULL, NULL, 0),
(6, 1, 1, 'Follow Development', 'follow-development', 'nav-link', NULL, 2, '{\"title\": \"follow-development\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, 'page', NULL, NULL, NULL, '<p>Follow Development.</p>\r\n<p>Keep up to date on all the changes, or bugs lol... Testing.. Again. and Again...</p>', 0, NULL, '07-23-2022 6:28:31', '07-24-2022 11:59:42', 'Aurora, Php, Custom Development', 'Follow Aurora Development!!', 1),
(9, 0, 1, 'About Us', 'about-us', 'nav-link', NULL, 3, '{\"title\": \"about-us\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, 'page', NULL, NULL, NULL, '<p>This will be the about us page. Testing Edit</p>', 0, NULL, '07-23-2022 10:25:00', '08-3-2022 12:27:13', NULL, NULL, 0),
(10, 0, 1, 'Test', 'test', 'nav-link', NULL, 4, '{\"title\": \"test\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, NULL, NULL, NULL, NULL, '', 0, NULL, '07-27-2022 8:01:53', NULL, NULL, NULL, 0),
(11, 0, 1, 'New test', 'new-test', 'nav-link', NULL, 5, '{\"title\": \"new-test\", \"action\": \"page\"}', NULL, NULL, 'page', 'view', 1, 'page', NULL, NULL, NULL, NULL, NULL, '<p>This is to test pages creation and url generation.</p>', 0, NULL, '08-6-2022 9:23:28', NULL, NULL, NULL, 0);