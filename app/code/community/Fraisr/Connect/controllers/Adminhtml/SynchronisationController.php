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
 * Synchronisation Controller
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Adminhtml_SynchronisationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check if the admin user is allowed to execute this controller action
     * 
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
                ->isAllowed('system/tools/fraisrconnect_synchronisation');
    }

    /**
     * Check if the admin user is allowed to see this section
     *
     * @param string $section
     * @param boolean
     */
    protected function _checkSectionAllowed($section)
    {
        if (false == Mage::getSingleton('admin/session')
                ->isAllowed('system/tools/fraisrconnect_synchronisation/')) {
            $this->forward('denied');
        }
    }

    /**
     * Trigger cause synchronisation
     * 
     * @return void
     */
    public function causeAction()
    {
        Mage::getModel('fraisrconnect/observer')->synchronizeCauses();
        $this->_redirectReferer();
        return;
    }

    /**
     * Trigger category synchronisation
     * 
     * @return void
     */
    public function categoryAction()
    {
        Mage::getModel('fraisrconnect/observer')->synchronizeCategories();
        $this->_redirectReferer();
        return;
    }

    /**
     * Trigger product synchronisation
     * 
     * @return void
     */
    public function productAction()
    {
        Mage::getModel('fraisrconnect/observer')->synchronizeProducts();
        $this->_redirectReferer();
        return;
    }
}