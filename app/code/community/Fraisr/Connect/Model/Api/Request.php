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
    const API_KEY_LIMIT = "limit";

    /**
     * @const API_KEY_PAGE Limit Page Key
     */
    const API_KEY_PAGE = "page";

    /**
     * @const API_KEY_KEY Api Key
     */
    const API_KEY_KEY = "key";

    /**
     * @const API_KEY_SECRET Api Secret
     */
    const API_KEY_SECRET = "secret";

    /**
     * Paginated data
     * @var array
     */
    protected $paginateData = array();

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
            ->validate();

        //Set result data
        $this->paginateData = array_merge($this->paginateData, $responseHandler->getJsonResponseData());

        //If there is a next page let's run the request again
        if (true === $responseHandler->isPaginateNextPage()) {
            $this->requestPaginatedGet($taskApiUri, $page+1);
        }
        return $this->paginateData;
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
        return Mage::getModel("fraisrconnect/config");
    }

    /**
     * Get Response Model
     * 
     * @return Fraisr_Connect_Model_Api_Response
     */
    protected function getResponseHandler()
    {
        return Mage::getModel("fraisrconnect/api_response");
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
            || "" == $this->getConfig()->getApiKey()) {
            throw new Fraisr_Connect_Exception(
                Mage::helper("fraisrconnect/data")->__("Api key is empty. Please complete the configuration.")
            );
        }

        if (true === is_null($this->getConfig()->getApiSecret())
            || "" == $this->getConfig()->getApiSecret()) {
            throw new Fraisr_Connect_Exception(
                Mage::helper("fraisrconnect/data")->__("Api secret is empty. Please complete the configuration.")
            );
        }

        $this->setHeaders(
            array(
                self::API_KEY_KEY => $this->getConfig()->getApiKey(),
                self::API_KEY_SECRET => $this->getConfig()->getApiSecret()
            )
        );
    }
}