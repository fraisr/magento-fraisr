<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @copyright  Copyright (c) 2013 das MedienKombinat Gmbh <kontakt@das-medienkombinat.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */

/**
 * Abstract Api
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Api_Request extends Zend_Http_Client
{
    /**
     * @const GET_LIMIT General Get Limit
     */
    const GET_LIMIT = 100;

    /**
     * @const API_KEY_LIMIT Limit Get Key
     */
    const API_KEY_LIMIT = 'limit';

    /**
     * @const API_KEY_PAGE Limit Page Key
     */
    const API_KEY_PAGE = 'page';

    /**
     * @const API_KEY_KEY Api Key
     */
    const API_KEY_KEY = 'key';

    /**
     * @const API_KEY_SECRET Api Secret
     */
    const API_KEY_SECRET = 'secret';

    /**
     * @const PLUGIN_IDENTIFICATION_KEY Plugin identification header key
     */
    const PLUGIN_IDENTIFICATION_KEY = 'fraisr-plugin';

    /**
     * Paginated data
     * @var array
     */
    protected $_paginateData = array();

    /**
     * Workarround to avoid Mage::getModel passed wrong/unwanted parameters to Zend_Http_Client
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(null, null);
    }

    /**
     * Run GET Request to Fraisr
     * 
     * @param string $taskApiUri
     * @param int $page
     * @return array
     */
    public function requestPaginatedGet($taskApiUri, $page = 0)
    {
        //Set Api Uri
        $this->setUri($this->buildUri($taskApiUri));

        //Set Adapter
        $this->setAdapter('Zend_Http_Client_Adapter_Curl');
        $adapter = $this->getAdapter();

        //Set Authentication Header
        $this->setAuthenticationHeader();

        //Set Plugin Infomation Header
        $this->setPluginInformationHeader();

        //Set GET-Method
        $this->setMethod(Zend_Http_Client::GET);

        //Set Page and Limit
        $this->setParameterGet(
            array(
                self::API_KEY_LIMIT => self::GET_LIMIT,
                self::API_KEY_PAGE => $page
            )
        );

        //Trigger request
        parent::request();

        //Validate and parse response
        $responseHandler = $this->getResponseHandler();
        $responseHandler
            ->setResponse($this->getLastResponse()) //Zend_Http_Response
            ->validateGet();

        //Set result data
        $this->_paginateData = array_merge($this->_paginateData, $responseHandler->getJsonResponseData());

        //If there is a next page let's run the request again
        if (true === $responseHandler->isPaginateNextPage()) {
            $this->requestPaginatedGet($taskApiUri, $page+1);
        }
        return $this->_paginateData;
    }

    /**
     * Run POST Request to Fraisr
     * 
     * @param string $taskApiUri
     * @param array $postParameter
     * @return array
     */
    public function requestPost($taskApiUri, $postParameter)
    {
        //Set Api Uri
        $this->setUri($this->buildUri($taskApiUri));

        //Set Adapter
        $this->setAdapter('Zend_Http_Client_Adapter_Curl');
        $adapter = $this->getAdapter();

        //Set Authentication Header
        $this->setAuthenticationHeader();

        //Set Plugin Infomation Header
        $this->setPluginInformationHeader();

        //Set POST-Method
        $this->setMethod(Zend_Http_Client::POST);

        //Set POST data
        $this->setParameterPost($postParameter);

        //Trigger request
        parent::request();

        //Validate and parse response
        $responseHandler = $this->getResponseHandler();
        $responseHandler
            ->setResponse($this->getLastResponse()) //Zend_Http_Response
            ->validatePost();

        return Zend_Json::decode($this->getLastResponse()->getBody());
    }

    /**
     * Run PUT Request to Fraisr
     * 
     * @param string $taskApiUri
     * @param array $postParameter
     * @return array
     */
    public function requestPut($taskApiUri, $postParameter)
    {
        //Set Api Uri
        $this->setUri($this->buildUri($taskApiUri));

        //Set Adapter
        $this->setAdapter('Zend_Http_Client_Adapter_Curl');
        $adapter = $this->getAdapter();

        //Set Authentication Header
        $this->setAuthenticationHeader();

        //Set Plugin Infomation Header
        $this->setPluginInformationHeader();

        //Set PUT-Method
        $this->setMethod(Zend_Http_Client::PUT);
        $this->setEncType(Zend_Http_Client::ENC_URLENCODED);

        //Set POST data
        $this->setParameterPost($postParameter);

        //Trigger request
        parent::request();

        //Validate response
        $responseHandler = $this->getResponseHandler();
        $responseHandler
            ->setResponse($this->getLastResponse()) //Zend_Http_Response
            ->validate();
    }

    /**
     * Run DELETE Request to Fraisr
     * 
     * @param string $taskApiUri
     * @return array
     */
    public function requestDelete($taskApiUri)
    {
        //Set Api Uri
        $this->setUri($this->buildUri($taskApiUri));

        //Set Adapter
        $this->setAdapter('Zend_Http_Client_Adapter_Curl');
        $adapter = $this->getAdapter();

        //Set Authentication Header
        $this->setAuthenticationHeader();

        //Set Plugin Infomation Header
        $this->setPluginInformationHeader();

        //Set DELETE-Method
        $this->setMethod(Zend_Http_Client::DELETE);

        //Trigger request
        parent::request();

        //Validate and parse response
        $responseHandler = $this->getResponseHandler();
        $responseHandler
            ->setResponse($this->getLastResponse()) //Zend_Http_Response
            //400 - Bad request is allowed too in case that the product is already deleted in fraisr
            ->setAllowedHttpStatusCodes(array(200, 400))
            ->validate();

        return Zend_Json::decode($this->getLastResponse()->getBody());
    }

    /**
     * Build request Uri
     * 
     * @param string $taskApiUri
     * @return string
     */
    protected function buildUri($taskApiUri)
    {
        return $this->getConfig()->getApiUri().$taskApiUri;
    }

    /**
     * Get Config
     * 
     * @return Fraisr_Connect_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getModel('fraisrconnect/config');
    }

    /**
     * Get Response Model
     * 
     * @return Fraisr_Connect_Model_Api_Response
     */
    protected function getResponseHandler()
    {
        return Mage::getModel('fraisrconnect/api_response');
    }

    /**
     * Set Authentication Header
     *
     * @throws Fraisr_Connect_Exception
     * @return void
     */
    protected function setAuthenticationHeader()
    {
        if (true === is_null($this->getConfig()->getApiKey())
            || '' == $this->getConfig()->getApiKey()) {
            throw new Fraisr_Connect_Exception(
                Mage::helper('fraisrconnect/data')->__('Api key is empty. Please complete the configuration.')
            );
        }

        if (true === is_null($this->getConfig()->getApiSecret())
            || '' == $this->getConfig()->getApiSecret()) {
            throw new Fraisr_Connect_Exception(
                Mage::helper('fraisrconnect/data')->__('Api secret is empty. Please complete the configuration.')
            );
        }

        $this->setAuth(
            $this->getConfig()->getApiKey(),
            $this->getConfig()->getApiSecret(),
            self::AUTH_BASIC
        );
    }

    /**
     * Set plugin information header to indentify the request as "Magento" in fraisr
     *
     * @return void
     */
    protected function setPluginInformationHeader()
    {
        $this->setHeaders(
            self::PLUGIN_IDENTIFICATION_KEY,
            $this->getConfig()->getPluginIdentificationValue()
        );
    }
}