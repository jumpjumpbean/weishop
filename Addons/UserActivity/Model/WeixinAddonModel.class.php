<?php
        	
namespace Addons\UserActivity\Model;
use Home\Model\WeixinModel;
        	
/**
 * UserActivity的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'UserActivity' ); // 获取后台插件的配置参数	
		//dump($config);

	} 

	// 关注公众号事件
	public function subscribe() {
		$user = M('hxtx_user')->where(array('token'=>get_token(), 'openid'=>get_openid()))->find();
		if (NULL === $user) {
			$data['token'] = get_token();
			$data['openid'] = get_openid();
			$data['subscribe_time'] = time();
			$data['subscribe_status'] = 1;
			$data['money'] = 0;

			$config = M('hxtx_config')->where(array('token'=>get_token()))->find();
			if ($config['subscribe_score'] == 1) {
				$data['money'] = $config['subscribe_money'];
			}
			
			$one['token']=get_token();
			$kkkn=M("member_public")->where($one)->find();
			$appid=$kkkn['appid'];
			$secret=$kkkn['secret'];
			$wecha_access_token=$this->get_access_token($appid,$secret);
			
			$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$wecha_access_token."&openid=".get_openid()."&lang=zh_CN";
			$weixin_user=json_decode($this->curlGet($url), true);
			//$data['image']=$weixin_user['headimgurl'];	
			$data['name']=$weixin_user['nickname'];
			
			M('hxtx_user')->add($data);
		} else {
			$data['subscribe_status'] = 1;
			M('hxtx_user')->where(array('token'=>get_token(), 'openid'=>get_openid()))->save($data);
		}
		
		return true;
	}
	
	public function get_access_token($appid,$secret){  
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;  
        $json=$this->curlGet($url);//这个地方不能用file_get_contents  
        $data=json_decode($json,true);  
        if($data['access_token']){  
            return $data['access_token'];  
        }else{  
            return "获取access_token错误";  
        }         
    } 
	
	public function curlGet($url){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }
	
	// 取消关注公众号事件
	public function unsubscribe() {
		$data['unsubscribe_time'] = time();
		$data['subscribe_status'] = 0;
		M('hxtx_user')->where(array('token'=>get_token(), 'openid'=>get_openid()))->save($data);
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
}
        	
