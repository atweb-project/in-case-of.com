DROP TABLE IF EXISTS `#__xdldr_stock`;
CREATE TABLE IF NOT EXISTS `#__xdldr_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu` varchar(255) DEFAULT NULL,
  `guest` tinyint(2) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_group` int(11) NOT NULL DEFAULT '0',
  `link_groups` varchar(255) NOT NULL,
  `user_alias` varchar(255) DEFAULT NULL,
  `user_ip` int(11) unsigned NOT NULL DEFAULT '0',
  `ip_location` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `filename` varchar(19) DEFAULT NULL,
  `filepath` varchar(255) DEFAULT NULL,
  `dwn_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;