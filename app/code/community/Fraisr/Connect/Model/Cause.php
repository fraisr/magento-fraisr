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
 * Cause Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Cause extends Mage_Core_Model_Abstract
{
    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('fraisrconnect/cause');
        parent::_construct();
    }

    /**
     * Synchronize cause data - retrieve by API and save them in the local database
     * 
     * @return void
     */
    public function synchronize()
    {
        $helper = Mage::helper('fraisrconnect/adminhtml_data');

        try {
            //Retrieve Cause data
            $causes = Mage::getModel('fraisrconnect/api_request')->requestPaginatedGet(
                Mage::getModel('fraisrconnect/config')->getCauseApiUri()
            );

            //Check is causes were retrieved
            if (0 === count($causes)) {
                $helper->logAndAdminOutputNotice(
                    $helper->__('0 causes retrieved during synchronisation.'),
                    Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
                );
            }

            //Delete current causes
            Mage::getResourceModel('fraisrconnect/cause')->deleteAllCauses();

            //Save new retrieved causes
            $this->saveRetrievedCauses($causes);

            //Success Message
            $helper->logAndAdminOutputSuccess(
                $helper->__(
                    'Cause synchronisation succeeded. Imported %s causes.',
                    count($causes)
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        } catch (Fraisr_Connect_Model_Api_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'Cause synchronisation failed during API request with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        } catch (Fraisr_Connect_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'Cause synchronisation failed with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        } catch (Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'An unknown error during cause synchronisation happened with message: "%s"',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        }
    }

    /**
     * Save retrieved causes
     * 
     * @param  array $retrievedCauses
     * @return void
     */
    protected function saveRetrievedCauses($retrievedCauses)
    {
        //For every retrieved cause
        foreach ($retrievedCauses as $retrievedCause) {
            //Copy instance of this to have a fresh object for every save
            $cause = $this;

            //Add data and save item
            $cause
                ->setId($retrievedCause['_id'])
                ->setName($retrievedCause['name'])
                ->setDescription($retrievedCause['description'])
                ->setUrl($retrievedCause['url'])
                ->setImageUrl($retrievedCause['images']['source'])
                ->setOfficial($retrievedCause['official'])
                ->save();
        }
    }

    /**
     * Check if products exists which causes doesn't exist anymore
     * If some were find, set 'fraisr_enabled' to false
     * 
     * @return void
     */
    public function productCheck()
    {
        try {
            //Get all current cause ids
            $causeIds = $this->getCollection()->getAllIds();

            //Get products which match multiple criterias so that their fraisr-active-status has to be disabled
            $productsToDisableInFraisr = $this->getProductsToDisableInFraisr($causeIds);

            //Stop processing if no products has to be fraisr-disabled
            if ($productsToDisableInFraisr->count() == 0) {
                return;
            }

            //Set fraisr products as inactive
            $this->disableProductsInFraisr($productsToDisableInFraisr);
        } catch (Fraisr_Connect_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'Product cause check failed with message: "%s".',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        } catch (Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    'An unknown error during product cause check happened with message: "%s"',
                    $e->getMessage()
                ),
                Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
            );
        }
    }

    /**
     * Get products which match multiple criterias so 
     * that their fraisr-active-status has to be disabled
     * 
     * @param  array $causeIds 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getProductsToDisableInFraisr($causeIds)
    {
        $products = Mage::getModel('catalog/product')->getCollection();
        $products
            ->addFieldToFilter('fraisr_enabled', 1); //Only products which are enabled for Fraisr sync

        //If causeIds were given, add them as filter
        if (count($causeIds) > 0) {
            $products
                ->addFieldToFilter('fraisr_cause', array('notnull' => true)) //fraisr_cause is not null -> has values
                ->addFieldToFilter('fraisr_cause', array('nin' => $causeIds)); //fraisr_cause is non of the current causes
        }
        return $products;
    }

    /**
     * Set 'fraisr_enabled' to no for all given products
     * 
     * @param  Mage_Catalog_Model_Resource_Product_Collection $productsToDisableInFraisr
     * @return void
     */
    protected function disableProductsInFraisr($productsToDisableInFraisr)
    {
        $helper = Mage::helper('fraisrconnect/adminhtml_data');
        
        $disabledSkus = array();
        foreach ($productsToDisableInFraisr as $product) {
            $product
                ->setFraisrEnabled(0)
                ->save();
            $disabledSkus[] = $product->getSku();
        }

        $helper->logAndAdminOutputNotice(
            $helper->__(
                'Set "Fraisr enabled" to "No" for %s products because their cause is not available anymore. Skus: "%s". In case of questions please the contact fraisr support.',
                count($disabledSkus),
                implode(',', $disabledSkus)
            ),
            Fraisr_Connect_Model_Log::LOG_TASK_CAUSE_SYNC
        );
    }
}