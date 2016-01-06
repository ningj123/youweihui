drop table if exists `jfsd_line`;
create table if not exists `jfsd_line` (
    `line_id` int unsigned not null auto_increment comment '自增id',
    `site_id` tinyint unsigned not null default 0 comment '站点id',
    `title` varchar(150) not null default '' comment '线路主标题',
    `sub_title` varchar(150) not null default '' comment '线路副标题',
    `image` varchar(150) not null default '' comment '线路描述图片',
    `images` varchar(999) not null default '' comment '线路描述图集',
    `traffic` varchar(150) not null default '' comment '交通方式',
    `daynum` varchar(150) not null default '' comment '行程天数',
    `daytime` varchar(150) not null default '' comment '行程日期',
    `remark` varchar(999) not null default '' comment '备注',
    `stroke` varchar(999) not null default '' comment '行程',
    `status` tinyint unsigned not null default 0 comment '线路状态0隐藏，1显示',
    primary key (`line_id`),
    index (`site_id`),
    index (`title`)
) engine innodb charset utf8 comment '线路表';
drop table if exists `jfsd_line_type`;
create table if not exists `jfsd_line_type` (
    `line_id` int unsigned not null default 0 comment '',
    `type_id` int unsigned not null default 0 comment '',
    primary key (`line_id`, `type_id`)
) engine innodb charset utf8 comment '线路分类';

drop table if exists `jfsd_line_tc`;
create table if not exists `jfsd_line_tc` (
    `tc_id` int unsigned not null auto_increment comment '自增id',
    `line_id` int unsigned not null default 0 comment '线路id',
    `tc_name` varchar(150) not null default '' comment '价格类型名称',
    `retail_price` int unsigned not null default 0 comment '门市价',
    `tc_desc` varchar(9999) not null default '' comment '套餐描述',
    `is_default` tinyint unsigned not null default 0 comment '是否默认',
    `status` tinyint unsigned not null default 0 comment '套餐状态',
    primary key (`tc_id`),
    index (`line_id`)
)  engine innodb charset utf8 comment '线路套餐';

drop table if exists `jfsd_line_price`;
create table if not exists `jfsd_line_price` (
    `tc_id` int unsigned not null default 0 comment '套餐id',
    `cr_price` int unsigned not null default 0 comment '成人价',
    `xh_price` int unsigned not null default 0 comment '小孩价',
    `date` date not null  comment '日期',
    index (`tc_id`)
)  engine innodb charset utf8 comment '线路价格';



CREATE TABLE IF NOT EXISTS `jfsd_product_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `meta_title` varchar(50) NOT NULL DEFAULT '' COMMENT 'SEO的网页标题',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '可见性',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '数据状态',
  `icon` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类图标',
  `groups` varchar(255) NOT NULL DEFAULT '' COMMENT '分组定义',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=INNODB  DEFAULT CHARSET=utf8 COMMENT='产品分类表';

drop table if exists `jfsd_order`;
create table if not exists `jfsd_order` (
    `order_id` char(20) not null default '' comment '订单id',
    `site_id` int unsigned not null default 0 comment '站点id',
    `user_id` int unsigned not null default 0 comment '用户id',
    `order_type` char(10) not null default '' comment '订单类型line,visa',
    `product_id` int unsigned not null default 0 comment '产品id',
    `product_num` int unsigned not null default 0 comment '产品购买数量',
    `reserve_info` varchar(999) not null default '' comment '预定信息',
    `truename` varchar(20) not null default '' comment '用户名',
    `mobile` varchar(20) not null default '' comment '手机号',
    `user_intro` varchar(255) not null default '' comment '用户备注',
    `kefu_intro` varchar(255) not null default '' comment '客服备注',
    `product_price` double(10,2) unsigned not null default 0 comment '产品总价格',
    `order_price` double(10,2) unsigned not null default 0 comment '订单价格',
    `order_status` int unsigned not null default 0 comment '订单状态',
    `pay_status` int unsigned not null default 0 comment '订单状态',
    `create_time` int unsigned not null default 0 comment '创建时间',
    `update_time` int unsigned not null default 0 comment '更新时间',
    `is_read` tinyint unsigned not null default 0 comment '是否阅读',
    primary key (`order_id`),
    index (`site_id`),
    index (`user_id`),
    index (`order_type`),
    index (`product_id`),
    index (`order_status`),
    index (`pay_status`)
) engine innodb charset utf8 comment '订单记录表';

create table `jfsd_sms_log` (
	`id` int unsigned not null auto_increment comment '',
	`type` char(20) not null default '' comment '类型',
	`mobile` char(11) not null default '' comment '手机号',
	`code` char(4) not null default '' comment '验证码',
	`ctime` int unsigned not null default 0 comment '创建时间',
	`utime` int unsigned not null default 0 comment '更新时间',
	`status` tinyint unsigned not null default 0 comment '状态',
	primary key (`id`),
	index (`type`),
	index (`mobile`),
	index (`code`)
) engine innodb charset utf8;
