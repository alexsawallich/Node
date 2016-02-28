CREATE TABLE `node` (
  `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` varchar(255) DEFAULT NULL,
  `node_access_count` int(10) unsigned DEFAULT '0',
  `node_access_date` datetime DEFAULT NULL,
  `node_meta_description` varchar(255) DEFAULT NULL,
  `node_meta_keywords` varchar(255) DEFAULT NULL,
  `node_meta_robots` varchar(255) DEFAULT NULL,
  `node_name` varchar(255) NOT NULL,
  `node_original_route` varchar(255) DEFAULT NULL,
  `node_redirect_code` enum('301','302') DEFAULT '301',
  `node_redirect_target` varchar(255) DEFAULT NULL,
  `node_route` varchar(255) NOT NULL,
  `node_route_config` text,
  `node_type` enum('mvc','content','redirect') NOT NULL DEFAULT 'content',
  PRIMARY KEY (`node_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;