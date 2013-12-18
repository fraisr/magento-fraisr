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
     * Ingoring SSL verification?
     *
     * @return boolean
     */
    public function ignoreSSLVerification()
    {
        return (1 == Mage::getStoreConfig('fraisrconnect/general/ignore_ssl_verification'));
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
     * Get commercial register url
     * 
     * @return string
     */
    public function getCommercialRegisterUrl()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/commercial_register_url');
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
    public function getTrustedUri()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/api/trusted');
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

    /**
     * Get plugin identification value
     * 
     * @return string
     */
    public function getPluginIdentificationValue()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/api/plugin_identification_value');
    }

    /**
     * Get product api url
     *
     * @param string $fraisrProductId
     * @return string
     */
    public function getProductApiUri($fraisrProductId = '')
    {
        return (string) sprintf(
            Mage::getStoreConfig('fraisrconnect/static/api/product'),
            $fraisrProductId
        );
    }

    /**
     * Get order api url
     *
     * @param string $fraisrOrderId
     * @return string
     */
    public function getOrderApiUri($fraisrOrderId = '')
    {
        return (string) sprintf(
            Mage::getStoreConfig('fraisrconnect/static/api/order'),
            $fraisrOrderId
        );
    }

    /**
     * Get connect api url
     *
     * @return string
     */
    public function getConnectApiUri()
    {
        return (string) Mage::getStoreConfig('fraisrconnect/static/api/connect');
    }

    /**
     * Get donation label iframe url
     *
     * @param string $fraisrId
     * @return string
     */
    public function getDonationLabelIframeUri($lng = "en", $fraisrId = '')
    {
        return (string) sprintf(
            Mage::getStoreConfig('fraisrconnect/static/api/donation_label_iframe'),
            $lng,
            $fraisrId
        );
    }

    /**
     * Get fraisr widget js uri
     *
     * @return string
     */
    public function getFrontendWidgetJsUri()
    {
        return Mage::getStoreConfig('fraisrconnect/urls/js');
    }

    /**
     * Get fraisr widget css uri
     *
     * @return string
     */
    public function getFrontendWidgetCssUri()
    {
        return Mage::getStoreConfig('fraisrconnect/urls/css');
    }

    /**
     * Get store id for the product synchronisation
     * 
     * @return int
     */
    public function getCatalogExportStoreId()
    {
        return (int) Mage::getStoreConfig('fraisrconnect/catalog_export/scope');
    }

    /**
     * Get allowed order status for order synchronisation
     * 
     * @return array
     */
    public function getOrderExportOrderStatus()
    {
        return explode(',', Mage::getStoreConfig('fraisrconnect/order_export/order_status'));
    }

    /**
     * Get days to check in past for order synchronisation
     * 
     * @return int
     */
    public function getOrderExportDays()
    {
        return (int) Mage::getStoreConfig('fraisrconnect/order_export/synchronisation_days');
    }

    /**
     * Check if invoice item amount should be taken as reference instead of order item amount
     * 
     * @return boolean
     */
    public function getOrderExportInvoiceReference()
    {
        return (1 ==  Mage::getStoreConfig('fraisrconnect/order_export/invoice_items'));
    }

    /**
     * Get product attribute for fraisr description
     * 
     * @return string
     */
    public function getProductDescriptionAttribute()
    {
        return Mage::getStoreConfig('fraisrconnect/catalog_export/description_attribute');
    }

    /**
     * Get donation label
     * 
     * @return string
     */
    public function getDonationLabel()
    {
        return Mage::getStoreConfig('fraisrconnect/frontend/donation_label');
    }


    /**
     * Get donation label icon position
     * 
     * @return string
     */
    public function getDonationLabelIconPosition()
    {
        return Mage::getStoreConfig('fraisrconnect/frontend/icon_position');
    }

    /**
     * Get donation label banderole position
     * 
     * @return string
     */
    public function getDonationLabelBanderolePosition()
    {
        return Mage::getStoreConfig('fraisrconnect/frontend/banderole_position');
    }

    /**
     * Get donation label position
     * 
     * @return string
     */
    public function getDonationLabelPosition()
    {
        if (Fraisr_Connect_Model_System_Config_Source_DonationLabel::DONATION_LABEL_ICON 
            == $this->getDonationLabel()) {
            return $this->getDonationLabelIconPosition();
        } elseif (Fraisr_Connect_Model_System_Config_Source_DonationLabel::DONATION_LABEL_BANDEROLE
                  == $this->getDonationLabel()) {
            return $this->getDonationLabelBanderolePosition();
        }
        return '';
    }

    /**
     * Get fraisr product delete queue
     *
     * returns array(
     *     'sku' => string '<sku>'
     *     'fraisr_id' => '<fraisr_id>''
     * )
     * 
     * @return array
     */
    public function getProductsFromDeleteQueue()
    {
        $productsToDelete = Mage::getStoreConfig('fraisrconnect/dynamic/products_to_delete');

        if (true === is_null($productsToDelete)) {
            return array();
        } elseif (true === is_array(unserialize($productsToDelete))) {
            return unserialize($productsToDelete);
        } else {
            return array();
        }
    }

    /**
     * Add a product to the fraisr delete queue
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    public function addProductToDeleteQueue($product)
    {
        //Get existing queue
        $productsToDelete = $this->getProductsFromDeleteQueue();
        $productsToDelete[$product->getSku()] = array(
            'sku' => $product->getSku(),
            'fraisr_id' => $product->getFraisrId()
        );

        Mage::getModel('core/config')
            ->saveConfig('fraisrconnect/dynamic/products_to_delete', serialize($productsToDelete));

        //Clean configuration cache - otherwise the queue will be outdated until next cache cleaning
        Mage::app()->getCacheInstance()->cleanType('config');
    }

    /**
     * Remove a product from fraisr delete queue
     * 
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    public function removeProductFromDeleteQueue($sku)
    {
        //Get existing queue
        $productsToDelete = $this->getProductsFromDeleteQueue();

        //If sku exists in delete queue, remove it
        if (true === array_key_exists($sku, $productsToDelete)) {
            unset($productsToDelete[$sku]);

            Mage::getModel('core/config')
                ->saveConfig('fraisrconnect/dynamic/products_to_delete', serialize($productsToDelete));
        }

        //Clean configuration cache - otherwise the queue will be outdated until next cache cleaning
        Mage::app()->getCacheInstance()->cleanType('config');
    }
}