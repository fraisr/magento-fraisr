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
class Fraisr_Connect_Model_Cause
{
    /**
     * Synchronize cause data - retrieve by API and save them in the local database
     * 
     * @return array
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
                    $helper->__("0 causes retrieved. Abort synchronisation because of possible error.")
                );
            }

            //Delete current causes
            die("delete causes");

        } catch (Fraisr_Connect_Model_Api_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    "Cause synchronisation failed during API request with message: '%s'",
                    $e->getMessage()
                )
            );
        } catch (Fraisr_Connect_Exception $e) {
            $helper->logAndAdminOutputException(
                $e->getMessage()
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
}