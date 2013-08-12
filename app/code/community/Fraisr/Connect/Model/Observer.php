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
 * Observer
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Observer
{
    /**
     * Initiate cause synchronisation
     * & check for products with non-existing causes
     * 
     * @return void
     */
    public function synchronizeCauses()
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            return;
        }

        //Retrieve and save causes
        Mage::getModel('fraisrconnect/cause')->synchronize();

        /**
         * Check if products exists which causes doesn't exist anymore
         * If some were find, set 'fraisr_enabled' to false
         */
        Mage::getModel('fraisrconnect/cause')->productCheck();
    }

    /**
     * Initiate category synchronisation
     * 
     * @return void
     */
    public function synchronizeCategories()
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            return;
        }
        Mage::getModel('fraisrconnect/category')->synchronize();
    }

    /**
     * Initiate product synchronisation
     * 
     * @return void
     */
    public function synchronizeProducts()
    {
        //Check if extension is active
        if (false === Mage::helper('fraisrconnect/adminhtml_data')->isActive(true)) {
            return;
        }
        Mage::getModel('fraisrconnect/product')->synchronize();
    }

    /**
     * Add a product to the fraisr delete queue
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function addProductToQueue($observer)
    {
        $product = $observer->getEvent()->getProduct();

        //Check if given product is valid and if the product has a fraisr_id
        if ($product instanceof Mage_Catalog_Model_Product
            && false === is_null($product->getFraisrId())) {
            //Add the product to the fraisr delete queue
            Mage::getModel('fraisrconnect/config')->addProductToDeleteQueue($product);
        }
    }
}