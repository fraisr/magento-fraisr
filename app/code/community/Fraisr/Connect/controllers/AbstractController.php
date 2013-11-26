<?php

abstract class Fraisr_Connect_AbstractController extends Mage_Core_Controller_Front_Action {
    /**
     * @type Fraisr_Connect_Model_Config
     */
    protected $_config = null;

    /**
     * @override
     * @param Zend_Controller_Request_Abstract  $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array                             $invokeArgs
     */
    public function __construct(
        Zend_Controller_Request_Abstract $request, 
        Zend_Controller_Response_Abstract $response, 
        array $invokeArgs = array()
    ){

        parent::__construct($request, $response, $invokeArgs);

        $this->getResponse()->setHeader("Content-Type", "application/json", true);

        $this->_config = Mage::getModel("fraisrconnect/config");

        try{
            $this->_checkActive();
            $this->_checkRequest();
        }catch(Exception $error){
            $this->_sendError($error);
        }
    }

    /**
     * checks whether the plugin is enabled
     * @throws Exception when plugin is disabled
     */
    protected function _checkActive(){
        if (!$this->_config->isActive()) {
            throw new Exception("The fraisr plugin is currently disabled");
        }
    }

    /**
     * compares token
     * @throws Exception when token is invalid
     */
    protected function _checkRequest(){
        if (null === ($token = $this->getRequest()->getParam("token", null))) {
            throw new Exception("Missing param 'token'");
        }

        if ($token !== $this->_getToken()) {
            throw new Exception("Param 'token' is invalid.");
        }
    }

    /**
     * returns token
     * @throws Exception if API key and/or secret are not defined
     */
    protected function _getToken(){
        $key = $this->_config->getApiKey();
        $secret = $this->_config->getApiSecret();

        if (strlen($key) > 0 && strlen($secret) > 0) {
            return hash("sha512", implode('|', array($key, $secret)));
        }

        throw new Exception("API key and/or secret are not defined");
    }

    /**
     * Sends an error response
     * @param  Exception $error
     */
    protected function _sendError(Exception $error){
        $this->getResponse()->setHttpResponseCode(400);
        $body = array(
            "message" => $error->getMessage()
        );
        $this->_send($body);
    }

    /**
     * Sends the response
     * @param Array|Object $body
     */
    protected function _send($body){
        $response = $this->getResponse();
        $body = Zend_Json::encode($body);
        $response->setBody($body);
        exit($response->sendResponse());
    }
}