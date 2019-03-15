DROP TABLE IF EXISTS `class`;
CREATE TABLE `calss` (
  `id` int(11)  NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(11)  NOT NULL DEFAULT 0 COMMENT '上级ID',
  `name` varchar(50)  NOT NULL DEFAULT '' COMMENT '名字',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `collects`;
CREATE TABLE `collects` (
  `id` int(11)  NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` int(11)  NOT NULL DEFAULT 0 COMMENT '标题',
  `content` text DEFAULT '' COMMENT '内容',
  `time` int(11) DEFAULT '0' COMMENT '采集时间',

  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `collects`;
CREATE TABLE `collects` (
  `id` int(11)  NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` int(11)  NOT NULL DEFAULT 0 COMMENT '标题',
  `content` text DEFAULT '' COMMENT '内容',
  `time` int(11) DEFAULT '0' COMMENT '采集时间',

  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` int(11)  NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `weixin_nickname` int(11)  NOT NULL DEFAULT 0 COMMENT '标题',
  `weixin_id` text DEFAULT '' COMMENT '微信号',
  `time` int(11) DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `new_articles`;
CREATE TABLE `new_articles` (
  `id` int(11)  NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `weixin_id` varchar(50) DEFAULT '' COMMENT '微信号',
  `url` varchar(255) DEFAULT '' COMMENT '链接',
  `title` varchar(50) DEFAULT '',
  `article_idx` int(11) DEFAULT '0' COMMENT '文章次序',
  `view_count` int(11) DEFAULT '0' COMMENT '阅读量',
  `agree_count` int(11) DEFAULT '0' COMMENT '点赞数',
  `publish_time` int(11) DEFAULT '0' COMMENT '发布时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `keywords`;
CREATE TABLE `keywords` (
  `id` int(11)  NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `keyword` varchar(50) DEFAULT '',
  `num` int(11) DEFAULT '0' COMMENT '搜索次数',
  `create_time` int(11) DEFAULT '0' COMMENT '首次时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `keyword` (`keyword`),
  KEY `num` (`num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
