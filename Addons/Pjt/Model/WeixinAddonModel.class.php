<?php
        	
namespace Addons\Pjt\Model;
use Home\Model\WeixinModel;
        	
/**
 * Pjt的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Pjt' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	