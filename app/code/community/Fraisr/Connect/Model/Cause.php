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
        $helper = Mage::helper("fraisrconnect/adminhtml_data");

        try {
            //Retrieve Cause data
            $causes = Mage::getModel("fraisrconnect/api_request")->requestPaginatedGet(
                Mage::getModel("fraisrconnect/config")->getCauseApiUri()
            );

            //Check is causes were retrieved
            if (0 === count($causes)) {
                throw new Fraisr_Connect_Model_Api_Exception(
                    $helper->__("0 causes retrieved. Abort synchronisation.")
                );
            }

            //Delete current causes
            Mage::getResourceModel("fraisrconnect/cause")->deleteAllCauses();

            //Save new retrieved causes
            $this->saveRetrievedCauses($causes);

            //Success Message
            $helper->logAndAdminOutputSuccess(
                $helper->__(
                    "Cause synchronisation succeeded. Imported %s causes.",
                    count($causes)
                )
            );
        } catch (Fraisr_Connect_Model_Api_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    "Cause synchronisation failed during API request with message: '%s'.",
                    $e->getMessage()
                )
            );
        } catch (Fraisr_Connect_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    "Cause synchronisation failed with message: '%s'.",
                    $e->getMessage()
                )
            );
        } catch (Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    "An unknown error during cause synchronisation happened with message: '%s'",
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Save retrieved causes
     * 
     * @param  array $retrievedCauses
     * @return void
     */
    public function saveRetrievedCauses($retrievedCauses)
    {
        //For every retrieved cause
        foreach ($retrievedCauses as $retrievedCause) {
            //Copy instance of this to have a fresh object for every save
            $cause = $this;

            //Add data and save item
            $cause
                ->setId($retrievedCause["_id"])
                ->setName($retrievedCause["name"])
                ->setDescription($retrievedCause["description"])
                ->setUrl($retrievedCause["url"])
                ->setImageUrl($retrievedCause["images"]["source"])
                ->setOfficial($retrievedCause["official"])
                ->save();
        }
    }
}