DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='pjt_order_response' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='pjt_order_response' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_pjt_order_response`;