CREATE TABLE IF NOT EXISTS `wp_hxtx_user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`nickname`  varchar(255) NULL  COMMENT '微信名',
`headimage`  varchar(255) NULL  COMMENT '微信头像',
`token`  varchar(255) NULL  COMMENT 'Token',
`openid`  varchar(255) NULL  COMMENT 'Openid',
`subscribe_status`  tinyint(2) NULL  DEFAULT 0 COMMENT '关注状态',
`subscribe_time`  int(10) NULL  COMMENT '关注时间',
`name`  varchar(255) NULL  COMMENT '姓名',
`phone`  varchar(255) NULL  COMMENT '手机号',
`unsubscribe_time`  int(10) NULL  COMMENT '取消关注时间',
`uid`  int(10) NULL  COMMENT 'uid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('hxtx_user','慧行天下用户','0','','1','["phone"]','1:基础','','','','','name:微信昵称\r\nsubscribe_status:关注状态\r\nsubscribe_time|time_format:关注时间\r\nphone:客户电话 \r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','name','','1451137499','1451630107','1','MyISAM','UserActivity');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('nickname','微信名','varchar(255) NULL','string','','','0','','0','0','1','1451137924','1451137924','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('headimage','微信头像','varchar(255) NULL','string','','','0','','0','0','1','1451137956','1451137956','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(255) NULL','string','','','0','','0','0','1','1451137981','1451137981','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('openid','Openid','varchar(255) NULL','string','','','0','','0','0','1','1451139134','1451138005','','3','','regex','get_openid','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('subscribe_status','关注状态','tinyint(2) NULL','bool','0','','0','0:取消关注\r\n1:已关注','0','0','1','1451138079','1451138079','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('subscribe_time','关注时间','int(10) NULL','datetime','','','0','','0','0','1','1451138134','1451138134','','3','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','姓名','varchar(255) NULL','string','','','0','','0','0','1','1451138200','1451138188','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('phone','手机号','varchar(255) NULL','string','','','1','','0','0','1','1451138238','1451138238','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('unsubscribe_time','取消关注时间','int(10) NULL','datetime','','','0','','0','0','1','1451138349','1451138349','','3','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','uid','int(10) NULL','num','','','1','','0','0','1','1451630218','1451630218','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;

CREATE TABLE IF NOT EXISTS `wp_hxtx_config` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`token`  varchar(255) NULL  COMMENT 'Token',
`subscribe_score`  char(10) NULL  DEFAULT 0 COMMENT '关注送优惠券开关',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('hxtx_config','系统配置','0','','1','["subscribe_score"]','1:基础','','','','','subscribe_score:关注送优惠券开关\r\nid:操作:[EDIT]|编辑','10','','','1451137530','1451139331','1','MyISAM','UserActivity');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(255) NULL','string','','','0','','0','0','1','1451139109','1451139109','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('subscribe_score','关注送优惠券开关','char(10) NULL','radio','0','','0','0:关闭\r\n1:开启','0','0','1','1451630051','1451139290','','3','','regex','','3','function');
UPDATE `wp_attribute` SET model_id= (SELECT MAX(id) FROM `wp_model`) WHERE model_id=0;

