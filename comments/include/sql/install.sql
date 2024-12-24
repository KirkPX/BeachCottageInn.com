CREATE TABLE IF NOT EXISTS `{prefix}comment` (
  `comment_id` int(12) NOT NULL,
  `comment_author_name` text,
  `comment_author_email` text,
  `comment_author_homepage` text,
  `comment_author_city` text,
  `comment_author_state` text,
  `comment_author_country` text,
  `comment_author_ip` varchar(20) default NULL,
  `comment_author_host` varchar(250) default NULL,
  `comment_author_user_agent` text,
  `comment_text` text,
  `comment_hash` char(40) NOT NULL,
  `comment_timestamp` int(10) NOT NULL,
  `comment_status` int(3) NOT NULL default '0',
  `comment_domain_id` int(12) NOT NULL default '0',
  KEY `comment_id` (`comment_id`),
  KEY `status_domain_id` (`comment_domain_id`,`comment_status`),
  KEY `comment_hash` (`comment_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{prefix}domain` (
  `domain_id` int(12) unsigned NOT NULL auto_increment,
  `domain_name` varchar(255) NOT NULL default '',
  `domain_hash` char(40) NOT NULL,
  PRIMARY KEY  (`domain_id`),
  KEY `domain_hash` (`domain_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{prefix}setting` (
  `setting_id` int(12) NOT NULL auto_increment,
  `setting_name` varchar(250) NOT NULL,
  `setting_value` text,
  `setting_domain_id` int(12) NOT NULL default '0',
  PRIMARY KEY  (`setting_id`),
  KEY `setting_name` (`setting_name`),
  KEY `setting_domain_id` (`setting_domain_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
