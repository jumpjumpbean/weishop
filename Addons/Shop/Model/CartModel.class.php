<?php

namespace Addons\Shop\Model;

use Think\Model;

/**
 * Shop模型
 */
class CartModel extends Model {
	protected $tableName = 'shop_cart';

	function getMyCart($uid, $update = false) {
		$key = 'Cart_getMyCart_' . $uid;
		$info = S ( $key );
		if ($info === false || $update) {
			$map ['uid'] = $uid;
			$info = $this->where ( $map )->select ();
			$goodsDao = D ( 'Addons://Shop/Goods' );
			$shopDao = D ( 'Addons://Shop/Shop' );
			foreach ( $info as &$v ) {
				// $res [$v ['goods_id']] = $v;
				$v ['goods'] = $goodsDao->getInfo ( $v ['goods_id'] );
				$v ['shop'] = $shopDao->getInfo ( $v ['shop_id'] );
				$v ['goods_name'] = $v ['goods'] ['title'];
				$v ['shop_name'] = $v ['shop'] ['title'];
			}
			S ( $key, $info );
		}
		
		return $info;
	}
	function addToCart($goods) {
		$myList = $this->getMyCart ( $goods ['uid'] );
        // Fix多次添加相同商品在购物车中不能合并的bug
        $exist = false;
        foreach ( $myList as $v ) {
            if ( $v ['goods_id'] == $goods ['goods_id']) {
                $num = $v ['num'] + $goods ['num'];
                $map ['id'] = $v ['id'];
                $this->where ( $map )->setField ( 'num', $num );
                $exist = true;
                break;
            }
        }
        if(!$exist) {
            $goods ['openid'] = get_openid ();
            $this->add ( $goods );
        }

		return count ( $this->getMyCart ( $goods ['uid'], true ) );
	}
	function delCart($ids) {
		$ids = array_filter ( explode ( ',', $ids ) );
		if (empty ( $ids ))
			return 0;
		
		$map ['id'] = array (
				'in',
				$ids 
		);
		return $this->where ( $map )->delete ();
	}
	function delUserCart($uid, $goods_ids) {
		$map ['goods_id'] = array (
				'in',
				$goods_ids 
		);
		$map ['uid'] = $uid;
		$res = $this->where ( $map )->delete ();
		
		$this->getMyCart ( $goods ['uid'], true );
		return $res;
	}
}
