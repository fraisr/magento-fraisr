
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
 * Main Helper
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Helper_Adminhtml_Data extends Fraisr_Connect_Helper_Data
{
    /**
     * Log a message to the logging system and output the message to admin session as an error
     * 
     * @param  string $message
     * @param  string $task
     * @return void
     */
    public function logAndAdminOutputException($message, $task = "")
    {
        //Add admin error message
        Mage::getSingleton("adminhtml/session")->addError($message);

        //Log the message
        //TODO
    }

    /**
     * Log a message to the logging system and output the message to admin session as success
     * 
     * @param  string $message
     * @param  string $task
     * @return void
     */
    public function logAndAdminOutputSuccess($message, $task = "")
    {
        //Add admin success message
        Mage::getSingleton("adminhtml/session")->addSuccess($message);

        //Log the message
        //TODO
    }

    /**
     * Log a message to the logging system and output the message to admin session as notice
     * 
     * @param  string $message
     * @param  string $task
     * @return void
     */
    public function logAndAdminOutputNotice($message, $task = "")
    {
        //Add admin notice message
        Mage::getSingleton("adminhtml/session")->addNotice($message);

        //Log the message
        //TODO
    }

    /**
     * Check is extension is active and output message
     * 
     * @param  boolean $withMessage
     * @return boolean
     */
    public function isActive($withMessage = false)
    {
        //If activated, just return
        if (true === Mage::getModel("fraisrconnect/config")->isActive()) {
            return true;
        }

        //Add message
        if (true === $withMessage) {
            Mage::getSingleton("adminhtml/session")->addError(
                $this->__("The fraisr-extension was disabled in the configuration.")
            );
        }

        return false;
    }
}
