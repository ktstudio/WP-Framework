

CREATE TABLE IF NOT EXISTS `kt_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `level_id` int(5) unsigned NOT NULL,
  `scope` varchar(30) DEFAULT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL,
  `logged_user_name` varchar(60) DEFAULT NULL,
  `file` varchar(300) DEFAULT NULL,
  `line` int(15) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `kt_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ktterm_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `ktterm_id` (`ktterm_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

