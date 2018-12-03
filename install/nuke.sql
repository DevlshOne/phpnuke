-- PHPNUKE MT-Edition 8.4 Sql file
-- phpMyAdmin SQL Dump
-- version 2.11.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 25, 2011 at 04:50 PM
-- Server version: 5.0.67
-- PHP Version: 5.4..

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `nuke_admins_menu`
--

CREATE TABLE IF NOT EXISTS `nuke_admins_menu` (
`amid` int(10) NOT NULL,
  `atitle` text COLLATE utf8mb4_unicode_ci,
  `admins` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_admins_menu`
--

INSERT INTO `nuke_admins_menu` (`amid`, `atitle`, `admins`) VALUES
(1, 'authors', ''),
(2, 'backup', ''),
(3, 'blocks', ''),
(4, 'bookmarks', ''),
(5, 'cache', ''),
(7, 'groups', ''),
(8, 'meta_tags', ''),
(9, 'modules', ''),
(10, 'mtsn', ''),
(11, 'patch', ''),
(12, 'referrers', ''),
(13, 'settings', ''),
(14, 'upgrade', ''),
(15, 'media', ''),
(16, 'language', ''),
(17, 'nav_menus', '');

-- --------------------------------------------------------

--
-- Table structure for table `nuke_articles`
--

CREATE TABLE IF NOT EXISTS `nuke_articles` (
`sid` int(11) NOT NULL,
  `status` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'article',
  `aid` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_lead` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_color` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hometext` text COLLATE utf8mb4_unicode_ci,
  `bodytext` text COLLATE utf8mb4_unicode_ci,
  `article_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` int(11) DEFAULT '0',
  `counter` mediumint(8) unsigned DEFAULT '0',
  `cat` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `informant` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ihome` int(1) NOT NULL DEFAULT '0',
  `alanguage` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allow_comment` int(1) NOT NULL DEFAULT '0',
  `position` int(2) NOT NULL,
  `article_pass` text COLLATE utf8mb4_unicode_ci,
  `article_image` text COLLATE utf8mb4_unicode_ci,
  `cat_link` int(5) NOT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `score` int(11) DEFAULT NULL,
  `ratings` int(11) DEFAULT NULL,
  `micro_data` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_authors`
--

CREATE TABLE IF NOT EXISTS `nuke_authors` (
  `aid` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realname` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rule` text COLLATE utf8mb4_unicode_ci,
  `pwd` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter` int(11) NOT NULL DEFAULT '0',
  `radminsuper` tinyint(1) NOT NULL DEFAULT '1',
  `admlanguage` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadminsuper` int(1) DEFAULT NULL,
  `password_reset` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_banned_ip`
--

CREATE TABLE IF NOT EXISTS `nuke_banned_ip` (
`id` int(11) NOT NULL,
  `ip_address` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_blocks`
--

CREATE TABLE IF NOT EXISTS `nuke_blocks` (
`bid` int(10) NOT NULL,
  `title` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refresh` int(11) DEFAULT NULL,
  `last_refresh` int(11) DEFAULT NULL,
  `blockfile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_blocks_boxes`
--

CREATE TABLE IF NOT EXISTS `nuke_blocks_boxes` (
  `box_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `box_blocks` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `box_blocks_data` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `box_status` int(11) DEFAULT NULL,
  `box_theme_location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `box_theme_priority` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_blocks_boxes`
--

INSERT INTO `nuke_blocks_boxes` (`box_id`, `box_blocks`, `box_blocks_data`, `box_status`, `box_theme_location`, `box_theme_priority`) VALUES
('bottomcenter', '', '', 1, '', 0),
('comments', '', '', 1, '', 0),
('left', '', '', 1, '', 0),
('right', '', '', 1, '', 0),
('topcenter', '', '', 1, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `nuke_blocks_themes`
--

CREATE TABLE IF NOT EXISTS `nuke_blocks_themes` (
`sideid` int(10) NOT NULL,
  `sidename` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_blocks_themes`
--

INSERT INTO `nuke_blocks_themes` (`sideid`, `sidename`) VALUES
(1, 'nuke');

-- --------------------------------------------------------

--
-- Table structure for table `nuke_bookmarksite`
--

CREATE TABLE IF NOT EXISTS `nuke_bookmarksite` (
`bid` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci,
  `iconpath` text COLLATE utf8mb4_unicode_ci,
  `active` smallint(5) unsigned NOT NULL DEFAULT '0',
  `url` text COLLATE utf8mb4_unicode_ci,
  `weight` int(2) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_bookmarksite`
--

INSERT INTO `nuke_bookmarksite` (`bid`, `title`, `iconpath`, `active`, `url`, `weight`) VALUES
(1, 'گوگل', 'images/share/gimages.jpg', 1, 'http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk={URL}&title={TITLE}', 1),
(2, 'بالاترین', 'images/share/bal.png', 1, 'http://www.balatarin.com/links/submit?phase=2&url={URL}&title={TITLE}', 2),
(3, 'کلوب', 'images/share/cloob.gif', 1, 'http://www.cloob.com/share/link/add?url={URL}&title={TITLE}', 3),
(4, 'viwio', 'images/share/viwio.png', 1, 'http://www.viwio.com/home/?status={URL}&subject={TITLE}', 4),
(5, 'دنباله', 'images/share/donbaleh.gif', 1, 'https://donbaleh.com/submit.php?url={URL}&subject={TITLE}', 5),
(6, 'تویتر', 'images/share/twitter.png', 1, 'http://twitter.com/home?status={URL} - {TITLE}', 6),
(7, 'فیس بوک', 'images/share/facebook.png', 1, 'http://facebook.com/sharer.php?u={URL}&amp;t={TITLE}', 7),
(8, 'Google Buzz', 'images/share/google-buzz.png', 1, 'http://www.google.com/reader/link?url={URL}&title={TITLE}&srcURL={URL}', 8),
(9, 'Google Bookmarks', 'images/share/google.png', 1, 'http://google.com/bookmarks/mark?op=add&amp;bkmk={URL}&amp;title={TITLE}', 9),
(10, 'Digg', 'images/share/digg.png', 1, 'http://digg.com/submit?phase=2&amp;url={URL}&amp;title={TITLE}', 10),
(11, 'یاهو مسنجر', 'images/share/yahoo.gif', 1, 'ymsgr:im?msg=ino bebin - {URL} - {TITLE}', 12),
(12, 'Technorati', 'images/share/Technorati.png', 1, 'http://technorati.com/faves?add={URL}&title={TITLE}', 13),
(13, 'delicious', 'images/share/delicious.png', 1, 'http://delicious.com/post?url={URL}&title={TITLE}', 11);

-- --------------------------------------------------------

--
-- Table structure for table `nuke_categories`
--

CREATE TABLE IF NOT EXISTS `nuke_categories` (
`catid` int(10) NOT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catname` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catimage` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cattext` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catdesc` text COLLATE utf8mb4_unicode_ci,
  `parent_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_comments`
--

CREATE TABLE IF NOT EXISTS `nuke_comments` (
`cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `main_parent` int(11) DEFAULT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_id` int(11) NOT NULL DEFAULT '0',
  `post_title` varchar(600) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `ratings` int(11) DEFAULT NULL,
  `score` tinyint(4) NOT NULL DEFAULT '0',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `last_moderation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `reported` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_config`
--

CREATE TABLE IF NOT EXISTS `nuke_config` (
`config_id` int(11) NOT NULL,
  `config_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `config_value` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_config`
--

INSERT INTO `nuke_config` (`config_id`, `config_name`, `config_value`) VALUES
(1, 'sitename', 'نیوک 8.4.1 مشهد تیم'),
(2, 'nukeurl', ''),
(3, 'site_logo', ''),
(4, 'site_description', 'شرح مختصری در مورد سایت'),
(5, 'slogan', ''),
(6, 'startdate', ''),
(7, 'adminmail', ''),
(8, 'adminmail_name', 'مدیریت سایت'),
(9, 'anonpost', '1'),
(10, 'Default_Theme', 'Mashhadteam-Caspian'),
(11, 'overwrite_theme', '0'),
(12, 'footer_message', ''),
(13, 'commentlimit', '40960'),
(14, 'anonymous', 'ميهمان'),
(15, 'minpass', '5'),
(16, 'broadcast_msg', '1'),
(17, 'my_headlines', '1'),
(18, 'top', '0'),
(19, 'home_pagination', '20'),
(20, 'user_pagination', '0'),
(21, 'oldnum', '20'),
(22, 'banners', '1'),
(23, 'backend_title', ''),
(24, 'backend_language', 'en-us'),
(25, 'language', 'farsi'),
(26, 'locale', 'fa_IR'),
(27, 'multilingual', '1'),
(28, 'useflags', '0'),
(29, 'notify', '0'),
(30, 'notify_subject', 'خبر جدید ارسال شده است'),
(31, 'notify_message', 'خبر جدیدی در سایت به منظور تائید مدیر ارسال شده است.'),
(32, 'notify_from', 'webmaster'),
(33, 'moderate', '0'),
(34, 'admingraphic', '1'),
(35, 'httpref', '1'),
(36, 'httprefmax', '1000'),
(37, 'httprefmode', '1'),
(38, 'copyright', 'VUVkU2NHUnBRbkJhUkRCcFZGWlJkRkV5T1hkbFdFcHdXakpvTUVscU5WRlRSa0YwVkc1V2NscFRRbEZqYlRseFdsZE9NRWxGU2pWSlJIaG9TVWRvZVZwWFdUbEpiV2d3WkVoQk5reDVPVE5rTTJOMVkwZG9kMkp1Vm5KYVV6VndZMmxKWjJSSFJubGFNbFl3VUZOS1psbHRlR2hpYlhOcFNVaEtiR0pFTUdsWk1qbDNaVmhLY0ZveWFEQkphalZSVTBaQ1QyUlhkR3hNYld4NVVFTTVhRkJxZDNaYVIyd3lVR2M5UFE9PQ=='),
(39, 'Version_Num', '8.4.1'),
(40, 'nuke_editor', '1'),
(41, 'display_errors', '0'),
(42, 'gtset', '1'),
(43, 'userurl', '1'),
(44, 'align', 'rtl'),
(45, 'show_links', '1'),
(46, 'datetype', '1'),
(47, 'show_effect', '1'),
(48, 'votetype', '3'),
(49, 'mobile_mode', '0'),
(50, 'filemaneger_pass', ''),
(51, 'sitecookies', '/'),
(52, 'site_meta_tags', ''),
(53, 'site_keywords', 'کلمات,کلیدی,سایت'),
(54, 'suspend_site', '0'),
(55, 'suspend_start', ''),
(56, 'suspend_expire', ''),
(57, 'suspend_template', '<!DOCTYPE html>\r\n<html xmlns="http://www.w3.org/1999/xhtml">\r\n	<head>\r\n		<title>{SITENAME}</title>\r\n	</head>\r\n	<body>\r\n		<h1>Not Found</h1>\r\n		The requested URL /404.shtml was not found on this server.\r\n		<hr>\r\n		<i>{NUKEURL}</i>\r\n	</body>\r\n</html>'),
(58, 'upload_allowed_info', ''),
(59, 'upload_pagesitems', '5'),
(60, 'pagination_number', '1'),
(61, 'comments', 'a:12:{s:5:"allow";s:1:"1";s:9:"anonymous";s:1:"1";s:12:"confirm_need";s:1:"1";s:5:"limit";s:1:"0";s:6:"editor";s:1:"2";s:6:"inputs";a:4:{s:8:"name_act";s:1:"1";s:9:"email_act";s:1:"1";s:9:"email_req";s:1:"1";s:7:"url_act";s:1:"1";}s:6:"notify";a:2:{s:5:"email";s:1:"1";s:3:"sms";s:1:"1";}s:8:"order_by";s:1:"1";s:12:"allow_rating";s:1:"1";s:15:"allow_reporting";s:1:"1";s:13:"item_per_page";s:2:"20";s:5:"depth";s:1:"2";}'),
(62, 'max_log_numbers', '500'),
(63, 'smtp_email_server', ''),
(64, 'smtp_email_user', ''),
(65, 'smtp_email_pass', ''),
(66, 'smtp_secure', ''),
(67, 'smtp_port', '0'),
(68, 'smtp_debug', '0'),
(69, 'is_html_mail', '1'),
(70, 'allow_attachement_mail', '1'),
(71, 'mtsn_text_file', '1'),
(72, 'mtsn_status', '1'),
(73, 'mtsn_show_alarm', '1'),
(74, 'mtsn_send_mail', '1'),
(75, 'mtsn_admin_mail', 'attack@sitename.com'),
(76, 'mtsn_string_filter', '1'),
(77, 'mtsn_html_filter', '1'),
(78, 'mtsn_injection_filter', '1'),
(79, 'mtsn_block_ip', '0'),
(80, 'mtsn_version', '4.3.0'),
(81, 'mtsn_ddos_filter', '0'),
(82, 'mtsn_CensorMode', '0'),
(83, 'mtsn_CensorWords', 'سکس'),
(84, 'mtsn_CensorReplace', '*****'),
(85, 'mtsn_login_attempts', '1'),
(86, 'mtsn_login_attempts_time', '3600'),
(87, 'mtsn_requests_mintime', '5'),
(88, 'mtsn_requests_pages', '7'),
(89, 'seccode_type', '2'),
(90, 'google_recaptcha_sitekey', ''),
(91, 'google_recaptcha_secretkey', ''),
(92, 'mtsn_gfx_chk', 'admin_login,user_login,comments,send_post,feedback,user_sign_up'),
(93, 'gverify', ''),
(94, 'alexverify', ''),
(95, 'yverify', ''),
(96, 'gcse', ''),
(97, 'ganalytic', ''),
(98, 'ping_sites', 'http://rpc.pingomatic.com\nhttp://rpc.twingly.com\nhttp://rpc.weblogs.com/RPC2\nhttp://ping.blo.gs/\nhttp://ping.feedburner.com'),
(99, 'meta_Tags', ''),
(100, 'active_pings', '1'),
(101, 'last_ping_time', ''),
(102, 'ping_options', 'a:3:{s:12:"limit_number";s:1:"1";s:10:"limit_time";s:1:"3";s:10:"limit_ping";i:1;}'),
(103, 'future_pings', ''),
(104, 'future_ping_time', ''),
(105, 'ping_num', '0'),
(106, 'have_forum', '0'),
(107, 'forum_path', 'Forum'),
(108, 'forum_system', 'phpbb'),
(109, 'forum_prefix', 'phpbb_'),
(110, 'forum_db', 'phpbb3'),
(111, 'mtsn_search_skipwords', 'است,این'),
(112, 'feedbacks', 'a:13:{s:10:"letreceive";s:1:"1";s:5:"delay";s:3:"600";s:6:"notify";a:1:{s:3:"sms";s:1:"1";}s:11:"description";s:77:"<p>به سیستم مدیریت محتوای نیوک خوش آمدید</p>\n";s:5:"phone";s:0:"";s:6:"mobile";s:0:"";s:3:"fax";s:0:"";s:7:"address";s:0:"";s:16:"meta_description";s:86:"بخش ارتباط با ما سیستم مدیریت محتوای نیوک فارسی";s:13:"meta_keywords";a:3:{i:0;s:22:"ارتباط با ما";i:1;s:18:"تماس با ما";i:2;s:11:"فید بک";}s:10:"map_active";s:1:"1";s:10:"google_api";s:0:"";s:12:"map_position";s:35:"36.28795445718431,59.61575198173523";}'),
(113, 'forum_GTlink_active', '0'),
(114, 'forum_collation', 'utf8'),
(115, 'website_index_theme', '0'),
(116, 'session_last_gc', '1498217142'),
(117, 'mtsn_captcha_charset', ''),
(118, 'sessions_prefix', 'pnSession_'),
(119, 'mtsn_block_ip_expire', '3600'),
(120, 'session_timeout', '3600'),
(121, 'forum_seo_post_link', 'post{P}.html#p{P}'),
(122, 'forum_seo_topic_link', 'forum-f{F}/topic-t{T}.html'),
(123, 'forum_seo_forum_link', 'forum-f{F}/'),
(124, 'forum_seo_profile_link', 'member/{UN}/'),
(125, 'forum_seo_pm_link', ''),
(126, 'forum_seo_login_link', ''),
(127, 'forum_seo_logout_link', ''),
(128, 'forum_seo_ucp_link', ''),
(129, 'forum_seo_register_link', ''),
(130, 'forum_seo_passlost_link', ''),
(131, 'timthumb_allowed', 'phpnuke.ir'),
(132, 'lock_siteurl', '0'),
(136, 'smilies', 'a:21:{i:0;a:4:{s:4:"name";s:10:"icon_arrow";s:4:"code";s:2:";)";s:3:"url";s:28:"images/smiles/icon_arrow.gif";s:10:"dimentions";s:5:"19*19";}i:1;a:4:{s:4:"name";s:13:"icon_confused";s:4:"code";s:2:"|)";s:3:"url";s:31:"images/smiles/icon_confused.gif";s:10:"dimentions";s:5:"19*19";}i:2;a:4:{s:4:"name";s:9:"icon_cool";s:4:"code";s:2:":-";s:3:"url";s:27:"images/smiles/icon_cool.gif";s:10:"dimentions";s:5:"19*19";}i:3;a:4:{s:4:"name";s:8:"icon_cry";s:4:"code";s:2:":(";s:3:"url";s:26:"images/smiles/icon_cry.gif";s:10:"dimentions";s:5:"19*19";}i:4;a:4:{s:4:"name";s:8:"icon_eek";s:4:"code";s:2:":0";s:3:"url";s:26:"images/smiles/icon_eek.gif";s:10:"dimentions";s:5:"19*19";}i:5;a:4:{s:4:"name";s:9:"icon_evil";s:4:"code";s:2:":#";s:3:"url";s:27:"images/smiles/icon_evil.gif";s:10:"dimentions";s:5:"19*19";}i:6;a:4:{s:4:"name";s:12:"icon_exclaim";s:4:"code";s:2:"*)";s:3:"url";s:30:"images/smiles/icon_exclaim.gif";s:10:"dimentions";s:5:"19*19";}i:7;a:4:{s:4:"name";s:9:"icon_razz";s:4:"code";s:2:"^)";s:3:"url";s:27:"images/smiles/icon_razz.gif";s:10:"dimentions";s:5:"19*19";}i:8;a:4:{s:4:"name";s:14:"icon_surprised";s:4:"code";s:3:"+))";s:3:"url";s:32:"images/smiles/icon_surprised.gif";s:10:"dimentions";s:5:"19*19";}i:9;a:4:{s:4:"name";s:10:"icon_smile";s:4:"code";s:2:":}";s:3:"url";s:28:"images/smiles/icon_smile.gif";s:10:"dimentions";s:5:"19*19";}i:10;a:4:{s:4:"name";s:8:"icon_sad";s:4:"code";s:3:"|((";s:3:"url";s:26:"images/smiles/icon_sad.gif";s:10:"dimentions";s:5:"19*19";}i:11;a:4:{s:4:"name";s:13:"icon_rolleyes";s:4:"code";s:2:"@:";s:3:"url";s:31:"images/smiles/icon_rolleyes.gif";s:10:"dimentions";s:5:"19*19";}i:12;a:4:{s:4:"name";s:12:"icon_redface";s:4:"code";s:3:"(:)";s:3:"url";s:30:"images/smiles/icon_redface.gif";s:10:"dimentions";s:5:"19*19";}i:13;a:4:{s:4:"name";s:13:"icon_question";s:4:"code";s:2:":?";s:3:"url";s:31:"images/smiles/icon_question.gif";s:10:"dimentions";s:5:"19*19";}i:14;a:4:{s:4:"name";s:5:"heart";s:4:"code";s:3:")*(";s:3:"url";s:23:"images/smiles/heart.gif";s:10:"dimentions";s:5:"19*19";}i:15;a:4:{s:4:"name";s:4:"kiss";s:4:"code";s:3:"#%^";s:3:"url";s:22:"images/smiles/kiss.gif";s:10:"dimentions";s:5:"19*19";}i:16;a:4:{s:4:"name";s:9:"thumbs_up";s:4:"code";s:3:"@@#";s:3:"url";s:27:"images/smiles/thumbs_up.gif";s:10:"dimentions";s:5:"19*19";}i:17;a:4:{s:4:"name";s:11:"thumbs_down";s:4:"code";s:4:")))&";s:3:"url";s:29:"images/smiles/thumbs_down.gif";s:10:"dimentions";s:5:"19*19";}i:18;a:4:{s:4:"name";s:16:"embaressed_smile";s:4:"code";s:3:"^^*";s:3:"url";s:34:"images/smiles/embaressed_smile.gif";s:10:"dimentions";s:5:"19*19";}i:19;a:4:{s:4:"name";s:13:"regular_smile";s:4:"code";s:2:"!^";s:3:"url";s:31:"images/smiles/regular_smile.gif";s:10:"dimentions";s:5:"19*19";}i:20;a:4:{s:4:"name";s:10:"wink_smile";s:4:"code";s:3:"%&^";s:3:"url";s:28:"images/smiles/wink_smile.gif";s:10:"dimentions";s:5:"19*19";}}');
;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_feedbacks`
--

CREATE TABLE IF NOT EXISTS `nuke_feedbacks` (
`fid` int(10) NOT NULL,
  `sender_name` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sender_email` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `custom_fields` text COLLATE utf8mb4_unicode_ci,
  `responsibility` int(10) DEFAULT NULL,
  `replys` text COLLATE utf8mb4_unicode_ci,
  `added_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_headlines`
--

CREATE TABLE IF NOT EXISTS `nuke_headlines` (
`hid` int(11) NOT NULL,
  `sitename` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headlinesurl` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_languages`
--

CREATE TABLE IF NOT EXISTS `nuke_languages` (
`lid` int(11) NOT NULL,
  `main_word` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `equals` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_log`
--

CREATE TABLE IF NOT EXISTS `nuke_log` (
`lid` int(11) NOT NULL,
  `log_type` tinyint(1) DEFAULT NULL,
  `log_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_time` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_ip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_message` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_modules`
--

CREATE TABLE IF NOT EXISTS `nuke_modules` (
`mid` int(10) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang_titles` text COLLATE utf8mb4_unicode_ci,
  `active` int(1) NOT NULL DEFAULT '0',
  `mod_permissions` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admins` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `all_blocks` int(1) DEFAULT NULL,
  `main_module` int(1) DEFAULT NULL,
  `in_menu` int(1) DEFAULT NULL,
  `module_boxes` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_modules`
--

INSERT INTO `nuke_modules` (`mid`, `title`, `lang_titles`, `active`, `mod_permissions`, `admins`, `all_blocks`, `main_module`, `in_menu`, `module_boxes`) VALUES
(1, 'Articles', 'a:2:{s:7:"english";s:8:"Articles";s:5:"farsi";s:10:"مطالب";}', 1, '0', '', 1, 1, 1, 'a:5:{s:12:"send-article";s:14:"right|left||||";s:5:"index";s:35:"right|left|topcenter|bottomcenter||";s:10:"categories";s:5:"|||||";s:11:"article-seo";s:43:"right|left|topcenter|comments||bottomcenter";s:7:"archive";s:5:"|||||";}'),
(2, 'Search', 'a:2:{s:7:"english";s:6:"Search";s:5:"farsi";s:10:"جستجو";}', 1, '0', '', 1, 0, 1, 'a:1:{s:5:"index";s:10:"right|||||";}'),
(3, 'Surveys', 'a:2:{s:7:"english";s:7:"Surveys";s:5:"farsi";s:20:"نظر سنجی ها";}', 1, '0', '', 1, 0, 1, 'a:1:{s:5:"index";s:10:"right|||||";}'),
(4, 'Feedback', 'a:2:{s:7:"english";s:10:"Contact Us";s:5:"farsi";s:18:"تماس با ما";}', 1, '0', '', 1, 0, 1, 'a:1:{s:5:"index";s:10:"right|||||";}'),
(6, 'Statistics', 'a:2:{s:7:"english";s:10:"Statistics";s:5:"farsi";s:21:"آمار بازدید";}', 1, '0', '', 1, 0, 0, 'a:1:{s:5:"index";s:10:"right|||||";}'),
(8, 'Feed', 'a:2:{s:7:"english";s:0:"";s:5:"farsi";s:0:"";}', 1, '0', '', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nuke_mtsn`
--

CREATE TABLE IF NOT EXISTS `nuke_mtsn` (
`id` int(11) NOT NULL,
  `server` char(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` char(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` int(12) NOT NULL DEFAULT '0',
  `method` char(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_mtsn_ipban`
--

CREATE TABLE IF NOT EXISTS `nuke_mtsn_ipban` (
`id` int(11) NOT NULL,
  `blocker` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipaddress` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `time` int(12) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `expire` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_nav_menus`
--

CREATE TABLE IF NOT EXISTS `nuke_nav_menus` (
`nav_id` int(11) NOT NULL,
  `nav_title` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang_nav_title` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nav_location` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `date` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_nav_menus`
--

INSERT INTO `nuke_nav_menus` (`nav_id`, `nav_title`, `lang_nav_title`, `nav_location`, `status`, `date`) VALUES
(1, 'فهرست اصلی', 'a:2:{s:7:"english";s:11:"main navbar";s:5:"farsi";s:19:"فهرست سایت";}', 'primary', 1, '1496770474');

-- --------------------------------------------------------

--
-- Table structure for table `nuke_nav_menus_data`
--

CREATE TABLE IF NOT EXISTS `nuke_nav_menus_data` (
`nid` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `nav_id` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attributes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `part_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_points_groups`
--

CREATE TABLE IF NOT EXISTS `nuke_points_groups` (
`id` int(10) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `points` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_points_groups`
--

INSERT INTO `nuke_points_groups` (`id`, `type`, `title`, `description`, `points`) VALUES
(1, 1, 'نوشتن وبلاگ', 'نوشتن مطلب در وبلاگ ', 0),
(2, 1, 'ارائه نظر در مورد وبلاگها', 'ارسال نظرات براي مطالب وبلاگهاي کاربران', 0),
(3, 1, 'معرفي سايت به دوستان', 'ارسال لينک براي سايت و پيشنهاد اين سايت به دوستان', 0),
(4, 1, 'ارسال مطلب به سايت', 'اخبار ارسالي توسط کاربر که مدير تائيد نموده است', 0),
(5, 1, 'نظر در مورد مطالب سايت', 'ارسال نظر براي اخبار', 0),
(6, 1, 'مطالب ارسالي به دوستان', 'ارسال اخبار سايت توسط کاربر براي ديگران', 0),
(7, 1, 'امتياز دهي به مطالب سايت', 'راي به اخبار', 0),
(8, 1, 'دفعات مشاركت كاربر در نظرسنجي ها', 'راي در نظرسنجي هاي سايت', 0),
(9, 1, 'ارائه نظر در مورد نظرسنجي ها', 'نظرات ارسالي براي تمامي نظرسنجي هاي فعلي و قديمي سايت', 0),
(10, 1, 'ارسال مطلب به انجمنها', 'ارسال مطلب جديد در انجمن ها', 0),
(11, 1, 'ارسال جوابيه در انجمنها', 'پاسخ به مطالب انجمن ها', 0),
(12, 1, 'ارائه نظر در مورد نقدنامه ها', 'ارسال نظرات براي بخش نقدنامه', 0),
(13, 1, 'بازديد صفحات', 'رفتن به صفحات مختلف اين سايت ', 0),
(14, 1, 'بازديد يك لينك', 'پيشهاد و رفتن به سايت هاي معرفي شده در بخش آدرس سايت ها', 0),
(15, 1, 'راي به لينكها', 'راي به سايت هاي بخش آدرس سايت ها', 0),
(16, 1, 'نظر در مورد لينك ها', 'ارسال نظر براي لينک هاي موجود در آدرس سايت ها', 0),
(17, 1, 'دريافت يك فايل', 'دريافت و دانلود فايل از سايت', 0),
(18, 1, 'راي به فايلهاي موجود سايت', 'راي به فايل هاي بخش دريافت فايل', 0),
(19, 1, 'نظر در مورد فايلهاي موجود سايت', 'ارسال نظرات در بخش دريافت فايل', 0),
(20, 1, 'پيغام همگاني', 'ارسال پيغام همگاني در سايت', 0),
(21, 1, 'كليك روي بنرهاي تبليغاتي', 'کليک بر روي هر يک از تبليغات موجود در سايت', 0),
(22, 1, ' معرفی کاربر به سایت ', 'معرفی سایت به دیگران برای ثبت نام در سایت', 100);

-- --------------------------------------------------------

--
-- Table structure for table `nuke_referrer`
--

CREATE TABLE IF NOT EXISTS `nuke_referrer` (
`rid` int(11) NOT NULL,
  `url` text COLLATE utf8mb4_unicode_ci,
  `path` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_reports`
--

CREATE TABLE IF NOT EXISTS `nuke_reports` (
`rid` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_title` varchar(750) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_link` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_scores`
--

CREATE TABLE IF NOT EXISTS `nuke_scores` (
`id` int(11) NOT NULL,
  `votetype` int(11) NOT NULL DEFAULT '1',
  `post_id` int(11) DEFAULT NULL,
  `db_table` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating_ip` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vote_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `score` tinyint(1) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `gust` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_sessions`
--

CREATE TABLE IF NOT EXISTS `nuke_sessions` (
  `session_id` varchar(100) COLLATE utf8_bin NOT NULL,
  `session_user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `session_time` int(11) unsigned NOT NULL DEFAULT '0',
  `session_ip` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  `session_browser` varchar(150) COLLATE utf8_bin NOT NULL DEFAULT '',
  `session_page` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_statistics`
--

CREATE TABLE IF NOT EXISTS `nuke_statistics` (
`id` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `hourly_info` text COLLATE utf8mb4_unicode_ci,
  `visitor_ips` longtext COLLATE utf8mb4_unicode_ci,
  `visitors` int(11) DEFAULT NULL,
  `hits` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_statistics_counter`
--

CREATE TABLE IF NOT EXISTS `nuke_statistics_counter` (
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `var` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nuke_statistics_counter`
--

INSERT INTO `nuke_statistics_counter` (`type`, `var`, `count`) VALUES
('os', 'win 10', 0),
('os', 'win 8.1', 0),
('os', 'win 8', 0),
('os', 'win 7', 0),
('os', 'win Vista', 0),
('os', 'win Server 2003/XP x64', 0),
('os', 'win XP', 0),
('os', 'win 2000', 0),
('os', 'win ME', 0),
('os', 'win 98', 0),
('os', 'win 95', 0),
('os', 'win 3.11', 0),
('os', 'Mac OS X', 0),
('os', 'Mac OS 9', 0),
('os', 'Linux', 0),
('os', 'Ubuntu', 0),
('os', 'iPhone', 0),
('os', 'iPod', 0),
('os', 'iPad', 0),
('os', 'Android', 0),
('os', 'BlackBerry', 0),
('os', 'Mobile', 0),
('browser', 'Msie', 0),
('browser', 'Firefox', 0),
('browser', 'Safari', 0),
('browser', 'Chrome', 0),
('browser', 'Opera', 0),
('browser', 'Netscape', 0),
('browser', 'Maxthon', 0),
('browser', 'Konqueror', 0),
('browser', 'Handheld Browser', 0),
('total', 'hits', 0),
('browser', 'Others', 0),
('os', 'Others OS', 0),
('mosts', 'total', 0),
('mosts', 'members', 0),
('mosts', 'guests', 0),
('mosts', 'date', 0);

-- --------------------------------------------------------

--
-- Table structure for table `nuke_surveys`
--

CREATE TABLE IF NOT EXISTS `nuke_surveys` (
`pollID` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `aid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canVote` tinyint(1) DEFAULT NULL,
  `main_survey` tinyint(1) DEFAULT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_id` int(10) NOT NULL DEFAULT '0',
  `pollTitle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pollUrl` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `planguage` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voters` int(11) NOT NULL DEFAULT '0',
  `start_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_main` tinyint(1) DEFAULT NULL,
  `allow_comment` tinyint(1) DEFAULT NULL,
  `comments` int(11) DEFAULT '0',
  `options` text COLLATE utf8mb4_unicode_ci,
  `multi_vote` tinyint(1) DEFAULT NULL,
  `show_voters_num` tinyint(1) DEFAULT NULL,
  `permissions` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_surveys_check`
--

CREATE TABLE IF NOT EXISTS `nuke_surveys_check` (
`id` int(11) NOT NULL,
  `ip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pollID` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_tags`
--

CREATE TABLE IF NOT EXISTS `nuke_tags` (
`tag_id` int(11) NOT NULL,
  `tag` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `counter` int(20) NOT NULL DEFAULT '0',
  `visits` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nuke_users`
--

CREATE TABLE IF NOT EXISTS `nuke_users` (
`user_id` int(11) NOT NULL,
  `user_status` int(10) NOT NULL DEFAULT '1',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_password` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_ip` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_regdate` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_birthday` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_realname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_lastvisit` int(11) unsigned NOT NULL DEFAULT '0',
  `user_lastpage` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_login_attempts` tinyint(4) NOT NULL DEFAULT '0',
  `user_login_block_expire` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_inactive_reason` tinyint(2) NOT NULL DEFAULT '0',
  `user_inactive_time` int(11) unsigned NOT NULL DEFAULT '0',
  `user_lang` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_allow_viewonline` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `user_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_avatar_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `user_sig` mediumtext COLLATE utf8mb4_unicode_ci,
  `user_address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_website` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_interests` text COLLATE utf8mb4_unicode_ci,
  `user_femail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_newsletter` int(1) NOT NULL DEFAULT '0',
  `user_points` int(10) DEFAULT '0',
  `check_num` int(11) DEFAULT NULL,
  `user_gender` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_about` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nuke_admins_menu`
--
ALTER TABLE `nuke_admins_menu`
 ADD PRIMARY KEY (`amid`);

--
-- Indexes for table `nuke_articles`
--
ALTER TABLE `nuke_articles`
 ADD PRIMARY KEY (`sid`), ADD KEY `title` (`title`(191)), ADD KEY `status` (`status`), ADD KEY `post_type` (`post_type`), ADD KEY `time` (`time`), ADD KEY `article_url` (`article_url`(191)), ADD KEY `cat` (`cat`(191)), ADD KEY `tags` (`tags`(191)), ADD KEY `ihome` (`ihome`), ADD KEY `alanguage` (`alanguage`), ADD KEY `position` (`position`), ADD KEY `cat_link` (`cat_link`);

--
-- Indexes for table `nuke_authors`
--
ALTER TABLE `nuke_authors`
 ADD PRIMARY KEY (`aid`), ADD KEY `aid` (`aid`), ADD KEY `name` (`name`);

--
-- Indexes for table `nuke_banned_ip`
--
ALTER TABLE `nuke_banned_ip`
 ADD PRIMARY KEY (`id`), ADD KEY `id` (`id`), ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `nuke_blocks`
--
ALTER TABLE `nuke_blocks`
 ADD PRIMARY KEY (`bid`), ADD KEY `bid` (`bid`), ADD KEY `title` (`title`), ADD KEY `blockfile` (`blockfile`(191));

--
-- Indexes for table `nuke_blocks_boxes`
--
ALTER TABLE `nuke_blocks_boxes`
 ADD PRIMARY KEY (`box_id`), ADD KEY `box_theme_location` (`box_theme_location`);

--
-- Indexes for table `nuke_blocks_themes`
--
ALTER TABLE `nuke_blocks_themes`
 ADD PRIMARY KEY (`sideid`);

--
-- Indexes for table `nuke_bookmarksite`
--
ALTER TABLE `nuke_bookmarksite`
 ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `nuke_categories`
--
ALTER TABLE `nuke_categories`
 ADD PRIMARY KEY (`catid`), ADD KEY `catid` (`catid`), ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `nuke_comments`
--
ALTER TABLE `nuke_comments`
 ADD PRIMARY KEY (`cid`), ADD KEY `cid` (`cid`), ADD KEY `pid` (`pid`), ADD KEY `post_id` (`post_id`), ADD KEY `main_parent` (`main_parent`), ADD KEY `module` (`module`), ADD KEY `status` (`status`), ADD KEY `reported` (`reported`);

--
-- Indexes for table `nuke_config`
--
ALTER TABLE `nuke_config`
 ADD PRIMARY KEY (`config_id`);

--
-- Indexes for table `nuke_feedbacks`
--
ALTER TABLE `nuke_feedbacks`
 ADD PRIMARY KEY (`fid`), ADD KEY `ip` (`ip`);

--
-- Indexes for table `nuke_headlines`
--
ALTER TABLE `nuke_headlines`
 ADD PRIMARY KEY (`hid`), ADD KEY `hid` (`hid`);

--
-- Indexes for table `nuke_languages`
--
ALTER TABLE `nuke_languages`
 ADD PRIMARY KEY (`lid`), ADD KEY `main_word` (`main_word`(191));

--
-- Indexes for table `nuke_log`
--
ALTER TABLE `nuke_log`
 ADD PRIMARY KEY (`lid`), ADD KEY `log_type` (`log_type`);

--
-- Indexes for table `nuke_modules`
--
ALTER TABLE `nuke_modules`
 ADD PRIMARY KEY (`mid`), ADD KEY `title` (`title`(191)), ADD KEY `active` (`active`), ADD KEY `main_module` (`main_module`), ADD KEY `in_menu` (`in_menu`);

--
-- Indexes for table `nuke_mtsn`
--
ALTER TABLE `nuke_mtsn`
 ADD PRIMARY KEY (`id`), ADD KEY `ip` (`ip`);

--
-- Indexes for table `nuke_mtsn_ipban`
--
ALTER TABLE `nuke_mtsn_ipban`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `ipaddress` (`ipaddress`), ADD KEY `id` (`id`), ADD KEY `blocker` (`blocker`(191)), ADD KEY `status` (`status`), ADD KEY `expire` (`expire`);

--
-- Indexes for table `nuke_nav_menus`
--
ALTER TABLE `nuke_nav_menus`
 ADD PRIMARY KEY (`nav_id`), ADD KEY `status` (`status`);

--
-- Indexes for table `nuke_nav_menus_data`
--
ALTER TABLE `nuke_nav_menus_data`
 ADD PRIMARY KEY (`nid`), ADD KEY `status` (`status`), ADD KEY `nav_id` (`nav_id`), ADD KEY `pid` (`pid`), ADD KEY `weight` (`weight`), ADD KEY `type` (`type`(191)), ADD KEY `module` (`module`), ADD KEY `part_id` (`part_id`);

--
-- Indexes for table `nuke_points_groups`
--
ALTER TABLE `nuke_points_groups`
 ADD PRIMARY KEY (`id`), ADD KEY `type` (`type`);

--
-- Indexes for table `nuke_referrer`
--
ALTER TABLE `nuke_referrer`
 ADD PRIMARY KEY (`rid`), ADD KEY `rid` (`rid`), ADD KEY `ip` (`ip`);

--
-- Indexes for table `nuke_reports`
--
ALTER TABLE `nuke_reports`
 ADD PRIMARY KEY (`rid`), ADD KEY `post_id` (`post_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `nuke_scores`
--
ALTER TABLE `nuke_scores`
 ADD PRIMARY KEY (`id`), ADD KEY `post_id` (`post_id`), ADD KEY `db_table` (`db_table`(191)), ADD KEY `rating_ip` (`rating_ip`), ADD KEY `score` (`score`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `nuke_sessions`
--
ALTER TABLE `nuke_sessions`
 ADD PRIMARY KEY (`session_id`), ADD KEY `session_time` (`session_time`), ADD KEY `session_user_id` (`session_user_id`), ADD KEY `session_ip` (`session_ip`);

--
-- Indexes for table `nuke_statistics`
--
ALTER TABLE `nuke_statistics`
 ADD PRIMARY KEY (`id`), ADD KEY `id` (`id`), ADD KEY `year` (`year`), ADD KEY `month` (`month`), ADD KEY `day` (`day`);

--
-- Indexes for table `nuke_surveys`
--
ALTER TABLE `nuke_surveys`
 ADD PRIMARY KEY (`pollID`), ADD KEY `pollID` (`pollID`), ADD KEY `status` (`status`), ADD KEY `canVote` (`canVote`), ADD KEY `main_survey` (`main_survey`), ADD KEY `module` (`module`), ADD KEY `post_id` (`post_id`), ADD KEY `pollUrl` (`pollUrl`(191)), ADD KEY `pollTitle` (`pollTitle`);

--
-- Indexes for table `nuke_surveys_check`
--
ALTER TABLE `nuke_surveys_check`
 ADD PRIMARY KEY (`id`);
 
--
-- Indexes for table `nuke_tags`
--
ALTER TABLE `nuke_tags`
 ADD PRIMARY KEY (`tag_id`), ADD KEY `tag` (`tag`(191));

--
-- Indexes for table `nuke_users`
--
ALTER TABLE `nuke_users`
 ADD PRIMARY KEY (`user_id`), ADD KEY `username` (`username`(191));

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nuke_admins_menu`
--
ALTER TABLE `nuke_admins_menu`
MODIFY `amid` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `nuke_articles`
--
ALTER TABLE `nuke_articles`
MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_banned_ip`
--
ALTER TABLE `nuke_banned_ip`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_blocks`
--
ALTER TABLE `nuke_blocks`
MODIFY `bid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_blocks_themes`
--
ALTER TABLE `nuke_blocks_themes`
MODIFY `sideid` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `nuke_bookmarksite`
--
ALTER TABLE `nuke_bookmarksite`
MODIFY `bid` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `nuke_categories`
--
ALTER TABLE `nuke_categories`
MODIFY `catid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_comments`
--
ALTER TABLE `nuke_comments`
MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_config`
--
ALTER TABLE `nuke_config`
MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=133;
--
-- AUTO_INCREMENT for table `nuke_feedbacks`
--
ALTER TABLE `nuke_feedbacks`
MODIFY `fid` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_headlines`
--
ALTER TABLE `nuke_headlines`
MODIFY `hid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `nuke_languages`
--
ALTER TABLE `nuke_languages`
MODIFY `lid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_log`
--
ALTER TABLE `nuke_log`
MODIFY `lid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_modules`
--
ALTER TABLE `nuke_modules`
MODIFY `mid` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `nuke_mtsn`
--
ALTER TABLE `nuke_mtsn`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_mtsn_ipban`
--
ALTER TABLE `nuke_mtsn_ipban`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_nav_menus`
--
ALTER TABLE `nuke_nav_menus`
MODIFY `nav_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `nuke_nav_menus_data`
--
ALTER TABLE `nuke_nav_menus_data`
MODIFY `nid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_points_groups`
--
ALTER TABLE `nuke_points_groups`
MODIFY `id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `nuke_referrer`
--
ALTER TABLE `nuke_referrer`
MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_reports`
--
ALTER TABLE `nuke_reports`
MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_scores`
--
ALTER TABLE `nuke_scores`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_statistics`
--
ALTER TABLE `nuke_statistics`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `nuke_surveys`
--
ALTER TABLE `nuke_surveys`
MODIFY `pollID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_tags`
--
ALTER TABLE `nuke_tags`
MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `nuke_users`
--
ALTER TABLE `nuke_users`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;