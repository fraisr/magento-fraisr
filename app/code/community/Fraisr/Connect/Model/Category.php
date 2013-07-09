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
 * Category Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('fraisrconnect/category');
        parent::_construct();
    }

    /**
     * Synchronize category data - retrieve by API and save them in the local database
     * 
     * @return void
     */
    public function synchronize()
    {
        $helper = Mage::helper("fraisrconnect/adminhtml_data");

        try {
            //Retrieve category data
            $categories = Mage::getModel("fraisrconnect/api_request")->requestPaginatedGet(
                Mage::getModel("fraisrconnect/config")->getCategoryApiUri()
            );

            //Check is categories were retrieved
            if (0 === count($categories)) {
                throw new Fraisr_Connect_Model_Api_Exception(
                    $helper->__("0 categories retrieved. Abort synchronisation.")
                );
            }

            //Delete current categories
            Mage::getResourceModel("fraisrconnect/category")->deleteAllCategories();

            //Save new retrieved categories
            $this->saveRetrievedCategories($categories);

            //Success Message
            $helper->logAndAdminOutputSuccess(
                $helper->__(
                    "Category synchronisation succeeded. Imported %s categories.",
                    count($categories)
                )
            );
        } catch (Fraisr_Connect_Model_Api_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    "Category synchronisation failed during API request with message: '%s'.",
                    $e->getMessage()
                )
            );
        } catch (Fraisr_Connect_Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    "Category synchronisation failed with message: '%s'.",
                    $e->getMessage()
                )
            );
        } catch (Exception $e) {
            $helper->logAndAdminOutputException(
                $helper->__(
                    "An unknown error during category synchronisation happened with message: '%s'",
                    $e->getMessage()
                )
            );
        }
    }


    /**
     * Save retrieved categories
     * 
     * @param  array $retrievedCategories
     * @return void
     */
    public function saveRetrievedCategories($retrievedCategories)
    {
        //For every retrieved category
        foreach ($retrievedCategories as $retrievedCategorie) {
            //Copy instance of this to have a fresh object for every save
            $category = $this;

            //Add data
            $category
                ->setId($retrievedCategorie["_id"])
                ->setName($retrievedCategorie["name"]);

            //Check if parent category is given
            if (true === array_key_exists("parent", $retrievedCategorie)
                && true === is_array($retrievedCategorie["parent"])
                && true === array_key_exists("_id", $retrievedCategorie["parent"])) {
                $category->setParentId($retrievedCategorie["parent"]["_id"]);
            } else {
                $category->setParentId(null);
            }
            
            //save item
            $category->save();
        }
    }
}