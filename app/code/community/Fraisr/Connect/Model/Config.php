
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
 * Config Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Config
{
    /**
     * Is extension activated
     *
     * @return boolean
     */
    public function isActive()
    {
        return (1 == Mage::getStoreConfig('fraisrconnect/general/active'));
    }

    /**
     * Is sandbox mode on
     *
     * @return boolean
     */
    public function isSandboxMode()
    {
        return (1 == Mage::getStoreConfig('fraisrconnect/general/sandbox'));
    }

    /**
     * Get Api Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/general/key');
    }

    /**
     * Get Api Secret
     *
     * @return string
     */
    public function getApiSecret()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/general/secret');
    }

    /**
     * Get support email address 
     * 
     * @return string
     */
    public function getSupportEmail()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/support_email');
    }

    /**
     * Get API URL depending on Sandbox/Live settings
     * 
     * @return string
     */
    public function getApiUri()
    {
        if (true === $this->isSandboxMode()) {
            return $this->getSandboxApiUri();
        } else {
            return $this->getLiveApiUri();
        }
    }

    /**
     * Get Live API URL
     * 
     * @return string
     */
    public function getLiveApiUri()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/api/live');
    }

    /**
     * Get Sandbox API URL
     * 
     * @return string
     */
    public function getSandboxApiUri()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/api/sandbox');
    }

    /**
     * Get cause api url
     * 
     * @return string
     */
    public function getCauseApiUri()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/api/cause');
    }

    /**
     * Get category api url
     * 
     * @return string
     */
    public function getCategoryApiUri()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/api/category');
    }
}
