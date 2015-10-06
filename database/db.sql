DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sourcedomain` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ogimage` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewcount` int(10) unsigned NOT NULL DEFAULT 0,
  `commentcount` int(10) unsigned NOT NULL DEFAULT 0,
  `sharecount` int(10) unsigned NOT NULL DEFAULT 0,
  `upvote` int(10) unsigned NOT NULL DEFAULT 0,
  `downvote` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `creator` varchar(100) COLLATE utf8_unicode_ci DEFAULT '',
  `score` double NOT NULL DEFAULT 0.0,
  `isfeatured` tinyint(1) NOT NULL DEFAULT 0,
  `ispublished` tinyint(1) NOT NULL DEFAULT 0,
  `isapproved` tinyint(1) NOT NULL DEFAULT 0,
  `hasvideo` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `url` (`url`),
  INDEX `title` (`title`),
  INDEX `description` (`description`),
  INDEX `tags` (`tags`),
  INDEX `content` (`content`(2000)),
  INDEX `sourcedomain` (`sourcedomain`),
  INDEX `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sex` tinyint(1) NOT NULL DEFAULT 0,
  `language` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'zh_CN',
  `headimgurl` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `groupid` int(10) unsigned NOT NULL DEFAULT 0,
  `unionid` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `role` tinyint(1)  unsigned NOT NULL DEFAULT 1,
  `status` int(3) unsigned NOT NULL DEFAULT 200,
  `lastpush_time` TIMESTAMP DEFAULT 0,
  `subscribe_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `openid` (`openid`),
  INDEX `nickname` (`nickname`),
  INDEX `language` (`language`),
  INDEX `groupid` (`groupid`),
  INDEX `unionid` (`unionid`),
  INDEX `role` (`role`),
  INDEX `status` (`status`),
  INDEX `lastpush_time` (`lastpush_time`),
  INDEX `subscribe_time` (`subscribe_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `wxmedia`;
CREATE TABLE `wxmedia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newsid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `postids` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thumbids` longtext COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mediaids` longtext COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `iskeep` tinyint(1) NOT NULL DEFAULT 0,
  `issent` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` TIMESTAMP DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `newsid` (`newsid`),
  INDEX `created_at` (`created_at`),
  INDEX `sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
