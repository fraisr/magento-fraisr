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
class Fraisr_Connect_Model_Api_Response
{
    /**
     * Response of Zend_Http_Client
     * @var Zend_Http_Response
     */
    protected $response = null;

    /**
     * Validate response
     * 
     * @return void
     * @throws Fraisr_Connect_Model_Api_Exception
     */
    public function validate()
    {
        if (!$this->response instanceOf Zend_Http_Response) {
            throw new Fraisr_Connect_Model_Api_Exception(
                Mage::helper('fraisrconnect/data')->__(
                    'Api response class is "%s" instead of "%s".',
                    gettype($this->response),
                    'Zend_Http_Response'
                )
            );
        }

        if ($this->response->getStatus() != 200) {
            throw new Fraisr_Connect_Model_Api_Exception(
                Mage::helper('fraisrconnect/data')->__(
                    'Api response code is "%s" instead of "%s".',
                    $this->response->getStatus(),
                    200
                )
            );
        }

        try {
            $jsonData = Zend_Json::decode($this->response->getBody());
            if (false === is_array($jsonData)
                || false === array_key_exists('data', $jsonData)
                || false === is_array($jsonData['data'])) {
                throw new Fraisr_Connect_Model_Api_Exception(
                    Mage::helper('fraisrconnect/data')->__('Api response is no valid JSON.')
                );
            }
        } catch (Exception $e) {
            throw new Fraisr_Connect_Model_Api_Exception(
                Mage::helper('fraisrconnect/data')->__('Api response is no valid JSON.')
            );
        }
    }

    /**
     * Get complete Json Response
     * 
     * @return array
     */
    public function getJsonResponse()
    {
        return Zend_Json::decode($this->response->getBody());
    }

    /**
     * Get Json Response from 'data'-node
     * 
     * @return array
     */
    public function getJsonResponseData()
    {
        $jsonData = $this->getJsonResponse();
        return $jsonData['data'];
    }

    /**
     * Check if the response contains a 'next_url'
     * 
     * @return boolean
     */
    public function isPaginateNextPage()
    {
        return array_key_exists('next_url', $this->getJsonResponse());
    }

    /**
     * Set response
     * @param Zend_Http_Response $response
     * @return Fraisr_Connect_Model_Api_Response
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

}