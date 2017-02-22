<?php

namespace Addons\UserActivity\Controller;
use Addons\UserActivity\Controller\BaseController;
header("Content-Type: text/html; charset=utf-8");

class ConfigController extends BaseController{
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'hxtx_config' );
		parent::_initialize ();
	}

	// 通用插件的列表模型
	public function lists() {
		$list_data = $this->_get_model_list ( $this->model );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$vo ['subscribe_score'] = $vo ['subscribe_score'] ? '开启' : '关闭';
		}
		$this->assign ( $list_data );
		$this->assign ( 'search_button', false );
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
	
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	
	public function add() {
		parent::common_add ( $this->model );
	}
	
	// 通用插件的编辑模型
	public function edit() {
		$model = $this->model;
		$id = I ( 'id' );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create ()) {
				if ($Model->save ()) {
					$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
				} else {
					$this->error ( $Model->getError () );
				} 
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
			
			// 增加图文下拉选项
			$extra = $this->getNewsList ();
			if (! empty ( $extra )) {
				foreach ( $fields [1] as &$vo ) {
					if ($vo ['name'] == 'news') {
						$vo ['extra'] .= "\r\n" . $extra;
					}
				}
			}

			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			$this->display ();
		}
	}
	
	public function del() {
		parent::common_del ( $this->model );
	}
	
	// 获取所属分类
	private function getNewsList() {
		$extra = NULL;
		$condition['token'] = array('eq', get_token ());
		$list = M("custom_reply_news")->where($condition)->select();
		foreach ( $list as $v ) {
			$extra .= $v ['id'] . ':' . $v ['title'] . "\r\n";
		}
		return $extra;
	}
}
