# Dump of table post
# ------------------------------------------------------------

CREATE TABLE `post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `post` (`id`, `user_id`, `title`, `text`)
VALUES
	(1,1,'First Post','This is a post about literature.'),
	(2,1,'Second Post','This is a post about literature and history.'),
	(3,2,'Third Post','This is a post about history.'),
	(4,2,'Fourth Post','This is a post about life.');

# Dump of table post_tag
# ------------------------------------------------------------

CREATE TABLE `post_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `post_tag` (`id`, `post_id`, `tag_id`)
VALUES
	(1,1,1),
	(2,2,1),
	(3,2,2),
	(4,3,2),
	(5,4,3);

# Dump of table tag
# ------------------------------------------------------------

CREATE TABLE `tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tag` (`id`, `name`)
VALUES
	(1,'literature'),
	(2,'history'),
	(3,'life');

# Dump of table user
# ------------------------------------------------------------

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `username`, `first_name`, `last_name`)
VALUES
	(1,'john.doe','John','Doe'),
	(2,'jane.doe','Jane','Doe');
