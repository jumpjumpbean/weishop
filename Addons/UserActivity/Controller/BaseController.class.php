<?php

namespace Addons\UserActivity\Controller;
use Home\Controller\AddonsController;

class BaseController extends AddonsController {
	var $config;
	protected $order_options;
	protected $curr_order_sql;
	protected $order_sql;

	function _initialize() {
		
		$controller = strtolower ( _CONTROLLER );
		$action = strtolower ( _ACTION );

		$res ['title'] = '粉丝信息';
		$res ['url'] = addons_url ( 'UserActivity://UserActivity/lists' );
		$res ['class'] = ($controller == 'useractivity') ? 'current' : '';
		$nav [] = $res;
		
		//$res ['title'] = '分享统计';
		//$res ['url'] = addons_url ( 'UserActivity://SharedNews/lists' );
		//$res ['class'] = ($controller == 'sharednews') ? 'current' : '';
		//$nav [] = $res;

		$res ['title'] = '系统配置';
		$res ['url'] = addons_url ( 'UserActivity://Config/lists' );
		$res ['class'] = ($controller == 'config') ? 'current' : '';
		$nav [] = $res;
	
		$this->assign ( 'nav', $nav );

		
	}
	
	
	
	
	protected function initVar4Order($selected) {
		if (array_key_exists($selected, $this->order_sql)) {
			$this->curr_order_sql = $this->order_sql[$selected];
		} else {
			$this->curr_order_sql = 'id desc';
		}
		
		foreach ($this->order_options as &$option) {
			$option['selected'] = false;
			if ($option['option'] == $selected) {
				$option['selected'] = true;
			}
		}
	}
	
	protected function sort_fields($orig_fields) {
		$sorted_fields = array();
		
		$sorts = json_decode((string)$this->model ['field_sort'],true);
		$sorts = $sorts[1];

		foreach ($sorts as $sort) {
			foreach ($orig_fields as $vo) {
				if ($sort == $vo['name']) {
					$sorted_fields[] = $vo;
				}
			}
		}
	
		return $sorted_fields;
	}
	
}
