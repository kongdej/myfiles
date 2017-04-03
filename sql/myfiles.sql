DROP TABLE IF EXISTS contents;
CREATE TABLE IF NOT EXISTS contents (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `revise_date` date DEFAULT NULL,
  `refno` varchar(255) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `folder_id` int(11) NOT NULL DEFAULT '0',
  `current_version` int(11) NOT NULL DEFAULT '0',
  `creator_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `folder`;
CREATE TABLE IF NOT EXISTS `folder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '1',
  `text` text,
  `title` text,
  `icon` text,
  `orderfield` int(11) NOT NULL DEFAULT '0',
  `expanded` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `folder_user_perm`;
CREATE TABLE IF NOT EXISTS `folder_user_perm` (
  `folder_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `Index 1` (`folder_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `position` varchar(50) DEFAULT 'User',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `Index 2` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8;

INSERT INTO `users` (`uid`, `username`, `name`, `password`, `email`, `position`) VALUES
	(1, 'admin', 'Administrator', '123', '', 'Admin'),
	(2, 'docadmin', 'Document Administrator', '123', '', 'DocAdmin'),
  (3, 'user', 'User', '123', '', 'User');

CREATE TABLE `logging` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `event` VARCHAR(255) NOT NULL,
  `modified` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
