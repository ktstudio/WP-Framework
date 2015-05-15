

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
);

