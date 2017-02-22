<?php

namespace Addons\Pjt\Controller;
use Addons\Pjt\Controller\PjtHttpClient;
use Home\Controller\AddonsController;

class PjtController extends AddonsController{
    const PJT_INTERFACE_URL = 'http://xxx.xxx.xxx.xxx:port/API/'; // 需要替换为票景通接口IP
    const PJT_GET_GOODS = 'asGoods'; // 获取商品列表接口
    const PJT_CREATE_ORDER = 'asOrders'; // 订单下单接口
    const PJT_GET_ORDERS = 'asListOrders'; // 获取订单列表接口
    const PJT_CANCEL_ORDER = 'asCancel'; // 撤单接口

    var $config;
    var $model;

    function _initialize() {
        $this->model = $this->getModel ( 'pjt_order_response' );
        $this->config = getAddonConfig ( 'Pjt' );
        parent::_initialize();
    }

    public function createOrder($order) {
        $addressDao = D('Addons://Shop/Address');
        $addressInfo = $addressDao->getInfo($order['address_id']);
        $goods = json_decode ( $order ['goods_datas'], true);
        foreach ( $goods as $good ) {
            $this->createOrderByGood($good, $addressInfo, $order['id']);
        }
    }

    function createOrderByGood($good, $addressInfo, $orderId) {
        require_once ONETHINK_ADDON_PATH.'Pjt/Controller/PjtHttpClient.class.php';
        //addWeixinLog('pjt_create_order_by_good:good', $good);
        $httpClient = new PjtHttpClient();
        $httpClient->setUrl(PjtController::PJT_INTERFACE_URL.PjtController::PJT_CREATE_ORDER);
        //addWeixinLog('pjt_create_order_by_good:url', $this->url);
        $params['id'] = $this->config['cid'];
        $params['key'] = $this->config['ckey'];
        $params['man'] = $addressInfo['truename'];
        $params['tel'] = $addressInfo['mobile'];
        $params['price'] = $good['price'];
        $params['total'] = $good ['num'] * $good ['price'];
        $params['type'] = 1;
        $params['num'] = $good['num'];
        $params['innum'] = $good['num'];
        $params['bedate'] = date('Y-m-d',strtotime($good['pjt_begin_date']));
        $params['endate'] = date('Y-m-d',strtotime($good['pjt_end_date']));
        $params['code'] = $good['pjt_code'];
        $params['thcode'] = $orderId;
        $httpClient->setParams($params);
        //addWeixinLog('pjt_create_order_by_good:params', $params);
        if ($httpClient->call ()) {
            $res = $httpClient->getResContent();
            $this->addPjtResponse($res, $orderId);
            addWeixinLog('pjt_create_order:res', $res);
        } else {
            addWeixinLog('pjt_create_order:err', $httpClient->getErrInfo());
        }
    }

    function  addPjtResponse($res, $orderId) {
        $model = $this->model;
        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
        $datas = json_decode ( $res, true);
        //addWeixinLog('pjt_add_response:datas', $datas['datas']);
        foreach ( $datas['datas'] as $data ) {
            //addWeixinLog('pjt_add_response:data', $data);
            $save['order_id'] = (int)$orderId;
            $save['qr_url'] = $data['QRUrl'];
            $save['ver_code'] = $data['VerCode'];
            $save['pjt_order_code'] = $data['OrderCode'];
            $save['num'] = $data['Num'];
            $save['in_num'] = $data['InNum'];
            $save['price'] = (string)$data['Price'];
            $save['total'] = (string)$data['Total'];
            $save['order_date'] = $data['OrderDate'];
            $save['ticket_code'] = $data['Code'];
            $save['ticket_name'] = $data['Name'];
            //addWeixinLog('pjt_add_response:save', $save);
            if ($Model->create ($save) && $id = $Model->add ($save)) {
                //addWeixinLog('pjt_add_response:id', $id);
                //$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
            } else {
                addWeixinLog('pjt_add_response:err', $Model->getError ());
                $this->error ( $Model->getError () );
            }

        }
    }
}
