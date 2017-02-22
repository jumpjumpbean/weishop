<?php

namespace Addons\Pjt;
use Common\Controller\Addon;

/**
 * 票景通插件
 * @author nf
 */

    class PjtAddon extends Addon{

        public $info = array(
            'name'=>'Pjt',
            'title'=>'票景通',
            'description'=>'票景通交互',
            'status'=>1,
            'author'=>'nf',
            'version'=>'0.1',
            'has_adminlist'=>0
        );

	public function install() {
		$install_sql = './Addons/Pjt/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Pjt/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }