<?php

namespace Addons\UserActivity\Controller;
use Addons\UserActivity\Controller\BaseController;
header("Content-Type: text/html; charset=utf-8");

class UserActivityController extends BaseController{
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'hxtx_user' );
		parent::_initialize ();
		define ( 'EXCEL_LIB_ROUTE', ONETHINK_ADDON_PATH . 'UserActivity/Lib');
	}
	
	// 通用插件的列表模型
	public function lists() {
		$list_data = $this->_get_model_list ( $this->model );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$vo ['subscribe_status'] = $vo ['subscribe_status'] ? '已关注' : '取消关注';
		}
		$this->assign ( $list_data );
		$this->assign ( 'search_button', true );
		$this->assign ( 'add_button', false );
		$this->assign('export_url', addons_url('UserActivity://UserActivity/export'));
		$this->assign('clear_url', addons_url('UserActivity://UserActivity/clear'));
		
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}

	public function export() {
		error_reporting(E_ALL);
		date_default_timezone_set('Asia/Shanghai');
		require_once EXCEL_LIB_ROUTE.'/PHPExcel.php';
		require_once EXCEL_LIB_ROUTE.'/PHPExcel/IOFactory.php';
		require_once EXCEL_LIB_ROUTE.'/PHPExcel/Writer/Excel5.php';
		require_once EXCEL_LIB_ROUTE.'/PHPExcel/Writer/Excel2007.php';

		$objPHPExcel=new \PHPExcel();
		$objPHPExcel->getProperties()->setCreator('Aiyide')
				->setLastModifiedBy('Aiyide')
				->setTitle('Activity apply excel file')
				->setSubject('Activity apply excel file')
				->setDescription('Activity apply excel file')
				->setKeywords('office 2007 openxml php')
				->setCategory('Activity apply');

		// For 一级分类
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1','电话号码')
					->setCellValue('B1','充值金额');
		$map = array();
		$map['token'] = array('eq', get_token());
		$map['money'] = array('gt', 0);
		$map['phone'] = array('neq', '');
		$cate1 = M('hxtx_user')->where($map)->select();	
		foreach ($cate1 as $k=>$v) {
			$i = $k + 2;   
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$i,(string)$v['phone'])
						->setCellValue('B'.$i,$v['money']);
		}
		$objPHPExcel->getActiveSheet()->setTitle('话费充值表');
		//$objPHPExcel->setActiveSheetIndex(0);	

		//生成xls文件
		$filename = '话费充值表';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
			
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save("php://output");
		$this->success( '导出成功！');
	}
	
	public function clear() {
		 M('hxtx_user')->where(array('token'=>get_token()))->setField('money', 0);
		 $this->success( '清空成功！');
	}
	
	public function add() {
		parent::common_add ( $this->model );
	}
	
	public function edit() {
		parent::common_edit ( $this->model );
	}
	
	public function del() {
		parent::common_del ( $this->model );
	}
	
	
	
	
}
