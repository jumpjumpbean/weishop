DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='hxtx_user' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='hxtx_user' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_hxtx_user`;

DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='hxtx_config' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='hxtx_config' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_hxtx_config`;

