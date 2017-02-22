<?php

namespace Addons\Coupon\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	
	//$mid= $this->mid();
	// 开始领取页面
	function prev() {
        //dump (  'prev:begin:sessionmid: '.session('mid')  );
        //dump (  'prev:end:sessionid: '.session_id()  );
		$isWeixinBrowser = isWeixinBrowser ();
		if (! $isWeixinBrowser) {
			$this->error ( '请在微信里打开' );
		}
		//dump($isWeixinBrowser);
		/*判断是否关注*/
		$map['token']=$token=get_token();	
		$map['openid']=$openid=get_openid();	
		//$uid = M ( 'public_follow' )->where ( $map )->getField ( 'uid' );
		//dump($token);
		//dump($openid);
		//dump($user);
		//dump(strlen($openid));
		$is_subscribe = $this->is_subscribe();
		//if(!$user||($openid == '-1')||($openid == '-2')) {
		if(!$is_subscribe) {
			addWeixinLog($user,'###---weiguanzhu');
			$msg = '您还没有关注,请先在微信中搜索并关注公众号:慧行天下旅行' ;
			$this->show_error2 ( $msg, '' );
		}
		addWeixinLog($user,'###---yiguanzhu');
		//dump('yiguanzhu');

		//ofc领券
		$target_id = I ( 'id', 0, 'intval' );
		//dump($target_id);
		/*判断该优惠券开关是否可以领取*/
		$hh = D ( 'Coupon' )->getInfo ($target_id);
		//dump($hh);
		if(!$hh['on_off']) {
			$msg = '您来晚了，活动已经结束' ;
			$this->show_error2 ( $msg, '' );
		}
		$list = D ( 'Common/SnCode' )->getMyList ( $this->mid, $target_id );
		//dump($this->mid);
		//dump($list);
		if (! empty ( $_GET ['sn_id'] )) {
			$sn_id = I ( 'sn_id' );
			foreach ( $list as $v ) {
				if ($v ['id'] == $sn_id) {
					$res = $v;
				}
			}
			$list = array (
					$res 
			);
		}
		addWeixinLog($target_id,$list);	
		addWeixinLog($sn_id,$res);	
		$this->assign ( 'my_sn_list', $list );
			
		$data = $this->_detail ();
		$tpl = isset ( $_GET ['has_get'] ) ? 'has_get' : 'prev';
		
		$info = get_followinfo ( $this->mid );
		$config = getAddonConfig ( 'UserCenter' );
		addWeixinLog($info,$config);	
		if ($config ['need_bind'] && (empty ( $info ['mobile'] ) || empty ( $info ['truename'] ))) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			$url = addons_url ( 'UserCenter://Wap/bind_prize_info' );
		} else {
			$url = U ( 'set_sn_code', array (
					'id' => $data ['id'] 
			) );
		}
		
		$this->assign ( 'url', $url );
        //$this->assign ( 'testmid', $this->mid );
        //dump (  'prev:end:mid: '.$this->mid  );
        //dump (  'prev:end:sessionmid: '.session('mid')  );
        //dump (  'prev:end:sessionid: '.session_id()  );
		$this->display ( $tpl );
	}
	function is_subscribe() {
		$result = false;
		$info = get_token_appinfo ();
		//dump($info);
		$param ['appid'] = $info ['appid'];
		//$callback = U ( 'prev' );
		//dump($callback);
		$callback=GetCurUrl();
		//dump($url);

		if ($_GET ['state'] != 'weiphp') {
			$param ['redirect_uri'] = $callback;
			$param ['response_type'] = 'code';
			$param ['scope'] = 'snsapi_userinfo';
			$param ['state'] = 'weiphp';
			$info ['is_bind'] && $param ['component_appid'] = C ( 'COMPONENT_APPID' );
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
			redirect ( $url );
		} elseif ($_GET ['state'] == 'weiphp') {
			if (empty ( $_GET ['code'] )) {
				exit ( 'code获取失败' );
			}
			
			$param ['code'] = I ( 'code' );
			$param ['grant_type'] = 'authorization_code';
			
			if ($info ['is_bind']) {
				$param ['appid'] = I ( 'appid' );
				$param ['component_appid'] = C ( 'COMPONENT_APPID' );
				$param ['component_access_token'] = D ( 'Addons://PublicBind/PublicBind' )->_get_component_access_token ();
				
				$url = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?' . http_build_query ( $param );
			} else {
				$param ['secret'] = $info ['secret'];
				
				$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );
			}
			
			$content = file_get_contents ( $url );
			$content = json_decode ( $content, true );
			//dump($content);
			addWeixinLog('access_token',$content);
			if (! empty ( $content ['errmsg'] )) {
				exit ( $content ['errmsg'] );
			}
			
			$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $content ['access_token'] . '&openid=' . $content ['openid'] . '&lang=zh_CN';
			$data = file_get_contents ( $url );
			$data = json_decode ( $data, true );
			//dump($data);	
			addWeixinLog('userinfo',$data);	
			if (! empty ( $data ['errmsg'] )) {
				exit ( $data ['errmsg'] );
			}
			$map['token']=$info['token'];
			$map['openid']=$data['openid'];	
			$user =  M ( 'public_follow' )->where ( $map )->find ();
			//dump($user);
			addWeixinLog('public_follow',$user);
			if($user) {
				//$this->mid = $user['uid'];
				//dump('user-------------------------------------');
				//dump($this->mid);
				$result = true;
			}
			
			/*
			//$data ['status'] = 2;
			//empty ( $data ['headimgurl'] ) && $data ['headimgurl'] = ADDON_PUBLIC_PATH . '/default_head.png';
			
			//$uid = D ( 'Common/Follow' )->init_follow ( $content ['openid'], $info ['token'] );
			
			$url = Cookie ( '__forward__' );
			if ($url) {
				Cookie ( '__forward__', null );
			} else {
				//$url = U ( 'UserCenter' );
				$url = U ( 'prev' );
			}
			
			//redirect ( $url );
			*/
		}
		return $result;
		
	}
	function qr_code() {
		$id = I ( 'sn_id' );
		$map2 ['uid'] = $this->mid;
		
		$info = D ( 'Common/SnCode' )->getInfoById ( $id );
		if ($info ['uid'] != $this->mid) {
			$this->error ( '非法访问' );
		}
		
		$this->assign ( 'info', $info );
		// dump ( $info );
		
		$this->display ();
	}
	function do_pay() {
		$cTime = I ( 'cTime', 0, 'intval' );
		
		if ($cTime > 0 && (NOW_TIME * 1000 - $cTime) > 30000) {
			$this->error ( '二维码已过期' );
		}
		$id = I ( 'sn_id' );
		$info = D ( 'Common/SnCode' )->getInfoById ( $id );
		if (empty ( $info )) {
			$this->error ( '扫描的二维码不对' );
		}
		
		$this->assign ( 'info', $info );
		$data = D ( 'Coupon' )->getInfo ( $info ['target_id'] );
		$this->assign ( 'coupon', $data );
		// dump ( $info );
		
		if (empty ( $data ['pay_password'] )) { // 通过工作授权来核销
			$map ['token'] = get_token ();
			$map ['uid'] = $this->mid;
			$map ['enable'] = 1;
			
			$role = M ( 'servicer' )->where ( $map )->getField ( 'role' );
			$role = explode ( ',', $role );
			if (! in_array ( 2, $role )) {
				$this->error ( '你需要工作授权才能核销' );
			}
		}
		
		$this->display ();
	}
	function do_pay_ok() {
		$msg = '';
		$dao = D ( 'Common/SnCode' );
		
		$id = I ( 'sn_id' );
		$info = $dao->getInfoById ( $id );
		$data = D ( 'Coupon' )->getInfo ( $info ['target_id'] );
		$this->assign ( 'coupon', $data );
		
		if (! empty ( $data ['pay_password'] )) {
			$pay_password = I ( 'pay_password' );
			if (empty ( $pay_password )) {
				$msg = '核销密码不能为空';
			}
			
			if (empty ( $msg )) {
				if ($data ['pay_password'] != $pay_password) {
					$msg = '核销密码不正确';
				}
			}
		} else {
			$map ['token'] = get_token ();
			$map ['uid'] = $this->mid;
			$map ['enable'] = 1;
			
			$role = M ( 'servicer' )->where ( $map )->getField ( 'role' );
			$role = explode ( ',', $role );
			if (! in_array ( 2, $role )) {
				$msg = '你需要工作授权才能核销';
			}
		}
		
		if (empty ( $msg )) {
			if ($info ['is_use']) {
				$msg = '该优惠券已经使用过，请不要重复使用';
			}
		}
		
		if (empty ( $msg )) {
			$info ['is_use'] = $save ['is_use'] = 1;
			$info ['use_time'] = $save ['use_time'] = time ();
			$save ['admin_uid'] = $this->mid;
			
			$res = $dao->update ( $id, $save );
			
			$map ['is_use'] = 1;
			$map ['target_id'] = $info ['target_id'];
			$map ['addon'] = 'Coupon';
			$data ['use_count'] = $save2 ['use_count'] = intval ( $dao->where ( $map )->count () );
			
			D ( 'Coupon' )->update ( $info ['target_id'], $save2 );
			
			$msg = '核销成功';
		}
		
		$this->assign ( 'msg', $msg );
		$this->assign ( 'conpon', $data );
		
		$this->display ();
	}
	// 过期提示页面
	function over() {
		$this->_detail ();
		$this->display ();
	}
	function show_error($error, $info = '') {
		empty ( $info ) && $info = D ( 'Coupon' )->getInfo ( $id );
		$this->assign ( 'info', $info );
		
		$this->assign ( 'error', $error );
		S ( 'set_sn_code_lock', 0 ); // 解锁
		$this->display ( 'over' );
		exit ();
	}
	function show_error2($error, $info = '') {
		empty ( $info ) && $info = D ( 'Coupon' )->getInfo ( $id );
		$this->assign ( 'info', $info );
		
		$this->assign ( 'error', $error );
		S ( 'set_sn_code_lock', 0 ); // 解锁
		$this->display ( 'over2' );
		exit ();
	}
	function show() {
		//dump ( '22232323232323232');
		//dump ( $this->mid );
		// dump ( $this->mid );
		$id = I ( 'id', 0, 'intval' );
		
		$sn_id = I ( 'sn_id', 0, 'intval' );
		
		$list = D ( 'Common/SnCode' )->getMyList ( $this->mid, $id );
		
		if ($sn_id > 0) {
			foreach ( $list as $vo ) {
				$my_count += 1;
				$vo ['id'] == $sn_id && $sn = $vo;
			}
		} else {
			$sn = $list [0];
		}
		/*
		 * if (empty ( $sn )) {
		 * $param ['source'] = 'Coupon';
		 * $param ['id'] = $id;
		 * redirect ( addons_url ( 'Sucai://Sucai/show', $param ) );
		 *
		 * // $this->error ( '非法访问' );
		 * exit ();
		 * }
		 */
		$maps ['coupon_id'] = $id;
		$list = M ( 'coupon_shop_link' )->where ( $maps )->select ();
		$shop_ids = getSubByKey ( $list, 'shop_id' );
		if (! empty ( $shop_ids )) {
			$map_shop ['id'] = array (
					'in',
					$shop_ids 
			);
			$shop_list = M ( 'coupon_shop' )->where ( $map_shop )->select ();
			$this->assign ( 'shop_list', $shop_list );
		}
		$this->assign ( 'sn', $sn );
		// dump($sn);
		
		$this->_detail ( $my_count );
		
		$this->display ( 'show' );
	}
	function _detail($my_count = false) {
		$id = I ( 'id', 0, 'intval' );
		$data = D ( 'Coupon' )->getInfo ( $id );
		$this->assign ( 'data', $data );
		// dump ( $data );
		
		// 领取条件提示
		$follower_condtion [1] = '关注后才能领取';
		$follower_condtion [2] = '用户绑定后才能领取';
		$follower_condtion [3] = '领取会员卡后才能领取';
		$tips = condition_tips ( $data ['addon_condition'] );
		
		$condition = array ();
		$data ['max_num'] > 0 && $condition [] = '每人最多可领取' . $data ['max_num'] . '张';
		$data ['credit_conditon'] > 0 && $condition [] = '积分中金币值达到' . $data ['credit_conditon'] . '分才能领取';
		$data ['credit_bug'] > 0 && $condition [] = '领取后需扣除金币值' . $data ['credit_bug'] . '分';
		isset ( $follower_condtion [$data ['follower_condtion']] ) && $condition [] = $follower_condtion [$data ['follower_condtion']];
		empty ( $tips ) || $condition [] = $tips;
		
		$this->assign ( 'condition', $condition );
		// dump ( $condition );
		
		$this->_get_error ( $data, $my_count );
		
		return $data;
	}
	function _get_error($data, $my_count = false) {
		$error = '';
		//dump($this->mid);	
		// 抽奖记录
		$my_count === false && $my_count = count ( D ( 'Common/SnCode' )->getMyList ( $this->mid, $data ['id'] ) );
		//dump($my_count);
		
		// 权限判断
		$follow = get_followinfo ( $this->mid );
		// $is_admin = is_login ();
		
		if (! empty ( $data ['end_time'] ) && $data ['end_time'] <= NOW_TIME) {
			$error = '您来晚啦';
		} else if ($data ['max_num'] > 0 && $data ['max_num'] <= $my_count) {
			$error = '您的领取名额已用完啦';
		}
		
		// if ($data ['follower_condtion'] > intval ( $follow ['status'] ) && ! $is_admin) {
		// switch ($data ['follower_condtion']) {
		// case 1 :
		// $error = '关注后才能领取';
		// break;
		// case 2 :
		// $error = '用户绑定后才能领取';
		// break;
		// case 3 :
		// $error = '领取会员卡后才能领取';
		// break;
		// }
		// } else if ($data ['credit_conditon'] > intval ( $follow ['score'] ) && ! $is_admin) {
		// $error = '您的金币值不足';
		// } else if ($data ['credit_bug'] > intval ( $follow ['score'] ) && ! $is_admin) {
		// $error = '您的金币值不够扣除';
		// } else if (! empty ( $data ['addon_condition'] )) {
		// addon_condition_check ( $data ['addon_condition'] ) || $error = '权限不足';
		// }
		$this->assign ( 'error', $error );
		// dump ( $error );
		
		return $error;
	}

	// 记录中奖数据到数据库
	function set_sn_code() {
        //dump (  'set_sn_code:beginning:sessionmid: '.session('mid')  );
        //dump (  'set_sn_code:beginning:testmid: '.I('testmid')  );
        //dump (  'set_sn_code:beginning:mid: '.$this->mid  );
		$id = $param ['id'] = I ( 'id', 0, 'intval' );

		$lock = S ( 'set_sn_code_lock' );
		if ($lock == 1 || isset ( $_GET ['format'] )) {
			$param ['publicid'] = I ( 'publicid' );
			$param ['rand'] = NOW_TIME . rand ( 10, 99 );

			$this->error ( '排队领取中', U ( 'set_sn_code', $param ) );
		} else {
			S ( 'set_sn_code_lock', 1, 30 );
		}

		$follow = get_followinfo ( $this->mid );
		$config = getAddonConfig ( 'UserCenter' );
		//dump (  'set_sn_code:afterFollow:mid: '.$this->mid  );
		// S ( 'set_sn_code_lock', 0 );
		// exit ();
		if ($config ['need_bind'] && ! (defined ( 'IN_WEIXIN' ) && IN_WEIXIN) && ! isset ( $_GET ['is_stree'] ) && $this->mid != 1 && (empty ( $follow ['mobile'] ) || empty ( $follow ['truename'] ))) {
			Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] );
			S ( 'set_sn_code_lock', 0 ); // 解锁
			redirect ( addons_url ( 'UserCenter://Wap/bind_prize_info' ) );
		}
		
		$info = D ( 'Coupon' )->getInfo ( $id );
        //dump (  'set_sn_code:afterGetInfo:mid: '.$this->mid  );
        //dump (  'set_sn_code:afterGetInfo:info: '.$info  );
		$member=explode(',', $info['member']);
		if (!in_array(0, $member)){
		    //判断是否为会员
		    $card_map['token']=get_token();
		    $card_map['uid']=$this->mid;
		    $card=M('card_member')->where($card_map)->find();
		    if (!$card['member']||!in_array(-1, $member)||!in_array($card['level'], $member)){
		        $msg = '您的等级未满足，还不能领取该优惠券！';
		        $this->show_error ( $msg, $info );
		    }
		}
		if ($info ['on_off'] !=1 ) {
			$msg = empty ( $info ['end_tips'] ) ? '您来晚了，活动已经结束' : $info ['end_tips'];
			$this->show_error ( $msg, $info );
		}
		
		if ($info ['collect_count'] >= $info ['num']) {
			$msg = empty ( $info ['empty_prize_tips'] ) ? '您来晚了，优惠券已经领取完' : $info ['empty_prize_tips'];
			$this->show_error ( $msg, $info );
		}
		
		if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
			$msg = empty ( $info ['start_tips'] ) ? '活动在' . time_format ( $info ['start_time'] ) . '开始，请到时再来' : $info ['start_tips'];
			$this->show_error ( $msg, $info );
		}
		if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
			$msg = empty ( $info ['end_tips'] ) ? '您来晚了，活动已经结束' : $info ['end_tips'];
			$this->show_error ( $msg, $info );
		}
		
		$list = D ( 'Common/SnCode' )->getMyList ( $this->mid, $id );
		$this->assign ( 'my_sn_list', $list );
		
		$my_count = count ( $list );
		$error = $this->_get_error ( $info, $my_count );
		if (! empty ( $error )) {
			S ( 'set_sn_code_lock', 0 ); // 解锁
			$this->display ( 'over' );
			exit ();
		}
		
		$data ['target_id'] = $id;
		$data ['uid'] = $this->mid;
		$data ['addon'] = 'Coupon';
		$data ['sn'] = uniqid ();
		$data ['cTime'] = NOW_TIME;
		$data ['token'] = $info ['token'];
		$res = D ( 'Common/SnCode' )->delayAdd ( $data );
		S ( 'set_sn_code_lock', 0 ); // 解锁
		                             
		// 扣除积分
		if (! empty ( $info ['credit_bug'] )) {
			$credit ['score'] = $info ['credit_bug'];
			$credit ['experience'] = 0;
			add_credit ( 'coupon_credit_bug', 5, $credit );
		}
		if (isset ( $_GET ['is_stree'] ))
			return false;
        //dump (  'set_sn_code:beforeRedirect:mid: '.$this->mid  );
		redirect ( U ( 'show', $param ) );
	}
	function coupon_detail() {
		// dump ( get_openid () );
		// dump ( get_token () );
		// dump ( $this->mid );
		$id = $param ['id'] = I ( 'id', 0, 'intval' );
		$info = D ( 'Coupon' )->getInfo ( $id );
		$this->assign ( 'info', $info );
		$this->display ();
	}
	function store_list() {
		$id = $param ['id'] = I ( 'id', 0, 'intval' );
		$maps ['coupon_id'] = $id;
		$list = M ( 'coupon_shop_link' )->where ( $maps )->select ();
		$shop_ids = getSubByKey ( $list, 'shop_id' );
		if (! empty ( $shop_ids )) {
			$map_shop ['id'] = array (
					'in',
					$shop_ids 
			);
			$shop_list = M ( 'coupon_shop' )->where ( $map_shop )->select ();
			foreach ( $shop_list as &$s ) {
				$gpsArr = wp_explode ( $s ['gps'] );
				$s ['gps'] = $gpsArr [1] . ',' . $gpsArr ['0'];
			}
			$this->assign ( 'shop_list', $shop_list );
		}
		$this->display ();
	}
	function get_sn_status() {
		$id = I ( 'sn_id', 0, 'intval' );
		$is_use = D ( 'Common/SnCode' )->getInfoById ( $id, 'is_use' );
		echo $is_use;
	}
	function index() {
		$param ['id'] = $id = I ( 'id' );
		
		// 已领取的直接进入详情页面，不需要再领取（TODO：仅为不需要多次领取的客户使用）
		$mylist = D ( 'Common/SnCode' )->getMyList ( $this->mid, $id );
		if (! empty ( $mylist [0] )) {
			$param ['sn_id'] = $mylist [0] ['id'];
			redirect ( U ( 'show', $param ) );
		}
		
		$info = $public_info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		
		$url = addons_url ( "Coupon://Wap/set_sn_code", $param );
		$this->assign ( 'jumpURL', $url );
		
		$maps ['coupon_id'] = $id;
		$list = M ( 'coupon_shop_link' )->where ( $maps )->select ();
		$shop_ids = getSubByKey ( $list, 'shop_id' );
		if (! empty ( $shop_ids )) {
			$map_shop ['id'] = array (
					'in',
					$shop_ids 
			);
			$shop_list = M ( 'coupon_shop' )->where ( $map_shop )->select ();
			$this->assign ( 'shop_list', $shop_list );
		}
		
		$info = D ( 'Coupon' )->getInfo ( $id );
		$this->assign ( 'info', $info );
		$this->assign ( 'public_info', $public_info );
		
		$this->display ();
	}
	function personal() {
		$isUse = I ( 'get.use' );
		if ($isUse != '') {
			$can_use = $isUse;
		}
		
		$list = D ( 'Common/SnCode' )->getMyAll ( $this->mid, 'Coupon', false, '', $can_use );
		if (! empty ( $list )) {
			foreach ( $list as $k => &$v ) {
				$coupon = ( array ) D ( 'Addons://Coupon/Coupon' )->getInfo ( $v ['target_id'] );
				if ($coupon) {
					$v ['sn_id'] = $v ['id'];
					$v = array_merge ( $v, $coupon );
				} else {
					unset ( $list [$k] );
				}
			}
		}
		 //dump ( $list );
		$this->assign ( 'list', $list );
		
		$this->display ();
	}
	function sn() {
		$map ['token'] = get_token ();
		$map ['target_id'] = I ( 'coupon_id' );
		$map ['addon'] = 'Coupon';
		
		$key = I ( 'search' );
		if (! empty ( $key )) {
			$map ['sn'] = array (
					'like',
					'%' . $key . '%' 
			);
		}
		$is_use = I ( 'is_use' );
		if ($is_use == 1) {
			$map ['is_use'] = $is_use;
		}
		
		$code = M ( 'sn_code' )->where ( $map )->selectPage ();
		// dump($code);
		$this->assign ( $code );
		$this->assign ( 'is_use', $map ['is_use'] );
		
		$this->display ();
	}
	function sn_set() {
		$map ['id'] = I ( 'id' );
		$map ['token'] = get_token ();
		$data = M ( 'sn_code' )->where ( $map )->find ();
		if (! $data) {
			$this->error ( '数据不存在' );
		}
		
		if ($data ['is_use']) {
			$data ['is_use'] = 0;
			$data ['use_time'] = '';
		} else {
			$data ['is_use'] = 1;
			$data ['use_time'] = time ();
			$data ['admin_uid'] = $this->mid;
		}
		
		$res = M ( 'sn_code' )->where ( $map )->save ( $data );
		if ($res) {
			if ($data ['addon'] == 'Coupon') {
				$map2 ['target_id'] = $maps ['id'] = $data ['target_id'];
				$map2 ['addon'] = 'Coupon';
				
				$info = M ( 'sn_code' )->where ( $map2 )->field ( 'sum(is_use) as use_count,count(id) as num' )->find ();
				
				$save ['use_count'] = $info ['use_count'];
				$save ['collect_count'] = $info ['num'];
				M ( 'coupon' )->where ( $maps )->save ( $save );
			}
			$this->success ( '设置成功' );
		} else {
			$this->error ( '设置失败' );
		}
	}
	function lists() {
		// 更新延时插入的缓存
		D ( 'Common/SnCode' )->delayAdd ();
		
		$dao = D ( 'Coupon' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		$order = 'id desc';
		$model = $this->getModel ();
		
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// 搜索条件
		$map = $this->_search_map ( $model, $list_data ['fields'] );
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		//获取用户的会员等级
		$levelInfo=D('Addons://Card/CardLevel')->getCardMemberLevel($this->mid);
		// 读取模型数据列表
		$map['is_del']=0;
		$list = $dao->field ( 'id,member' )->where ( $map )->order ( $order )->page ( $page, $row )->select ();
		foreach ( $list as $d ) {
		    $levelArr=explode(',', $d['member']);
		    if (in_array(0, $levelArr) || in_array(-1, $levelArr) || in_array($levelInfo['id'], $levelArr)){
		        $datas [] = $dao->getInfo ( $d ['id'] );
		    }
		}
		
		/* 查询记录总数 */
		$count = $dao->where ( $map )->count ();
		$list_data ['list'] = $datas;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		$this->assign ( $list_data );
		
		$this->display ();
	}
}
