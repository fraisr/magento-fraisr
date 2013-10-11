<?php

require_once "AbstractController.php";

class Fraisr_Connect_OrdersController extends Fraisr_Connect_AbstractController{
    public function indexAction(){
        $helper = Mage::helper("fraisrconnect/synchronisation_order");
        $orders = $helper->getOrderItemsToSynchronize();
        $body = array();

        foreach ($orders as $order) {
            if(!$helper->isOrderItemValid($order))
                continue;

            array_push($body, $helper->getJsonObject($order));
        }

        $this->_send($body);
    }
}

?>