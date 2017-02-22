<?php

namespace Addons\UserActivity;
use Common\Controller\Addon;

/**
 * 粉丝行为插件
 * @author Alex
 */

    class UserActivityAddon extends Addon{

        public $info = array(
            'name'=>'UserActivity',
            'title'=>'粉丝行为',
            'description'=>'处理、统计粉丝的用户行为，比如关注等。',
            'status'=>1,
            'author'=>'Alex',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/UserActivity/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/UserActivity/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }