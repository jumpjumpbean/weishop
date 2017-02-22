<?php

namespace Addons\Pjt\Model;
use Think\Model;

/**
 * Pjt模型
 */
class PjtModel extends Model{
    protected $tableName = 'pjt_order_response';
    function getInfo($id, $update = false, $data = array()) {
        $key = 'PjtOrderResponse_getInfo_' . $id;
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (count ( $data )==0 ? $this->find ( $id ) : $data);
            S ( $key, $info );
        }
        return $info;
    }

    function update($id, $save) {
        $map ['id'] = $id;
        $this->where ( $map )->save ( $save );
        $this->getInfo ( $id, true );
    }

    function getInfoByCode($ticket_code, $order_id, $update = false, $data = array()) {
        $key = 'PjtOrderResponse_getInfoByCode_' . $ticket_code . $order_id;
        $info = S ( $key );
        $map['ticket_code'] = $ticket_code;
        $map['order_id'] = $order_id;
        if ($info === false || $update) {
            $info = ( array ) (count ( $data )==0 ? $this->where($map)->find () : $data);
            //addWeixinLog('pjt_get_info_by_code:info', $info);
            S ( $key, $info );
        }
        return $info;
    }
}
