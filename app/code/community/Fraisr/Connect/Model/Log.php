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
 * Log Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_Log extends Mage_Core_Model_Abstract
{
    /**
     * @const TYPE_CODE_SUCCESS Success Type Key
     */
    const TYPE_CODE_SUCCESS = "success";

    /**
     * @const TYPE_CODE_WARNING Warning Type Key
     */
    const TYPE_CODE_WARNING = "warning";

    /**
     * @const TYPE_CODE_ERROR Error Type Key
     */
    const TYPE_CODE_ERROR = "error";

    /**
     * @const TYPE_CODE_NOTICE Notice Type Key
     */
    const TYPE_CODE_NOTICE = "notice";

    /**
     * @const LOG_TASK_CAUSE_SYNC Get cause synchronisation task
     */
    const LOG_TASK_CAUSE_SYNC = 'Cause synchronisation';

    /**
     * @const LOG_TASK_CATEGORY_SYNC Get category synchronisation task
     */
    const LOG_TASK_CATEGORY_SYNC = 'Category synchronisation';

    /**
     * @const LOG_TASK_PRODUCT_SYNC Get product synchronisation task
     */
    const LOG_TASK_PRODUCT_SYNC = 'Product synchronisation';

    /**
     * @const LOG_TASK_ORDER_SYNC Get order synchronisation task
     */
    const LOG_TASK_ORDER_SYNC = 'Order synchronisation';

    /**
     * @const LOG_TASK_CREDITMEMO_SYNC Get creditmemo synchronisation task
     */
    const LOG_TASK_CREDITMEMO_SYNC = 'Creditmemo synchronisation';

    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('fraisrconnect/log');
        parent::_construct();
    }

    /**
     * Get Log Tasks
     * 
     * @return array
     */
    public function getTaskOptions()
    {
        $helper = Mage::helper('fraisrconnect/data');

        return array(
            self::LOG_TASK_CAUSE_SYNC => $helper->__(self::LOG_TASK_CAUSE_SYNC),
            self::LOG_TASK_CATEGORY_SYNC => $helper->__(self::LOG_TASK_CATEGORY_SYNC),
            self::LOG_TASK_PRODUCT_SYNC => $helper->__(self::LOG_TASK_PRODUCT_SYNC),
            self::LOG_TASK_ORDER_SYNC => $helper->__(self::LOG_TASK_ORDER_SYNC),
            self::LOG_TASK_CREDITMEMO_SYNC => $helper->__(self::LOG_TASK_CREDITMEMO_SYNC),
        );
    }

    /**
     * Get Log Types
     * 
     * @return array
     */
    public function getTypeOptions()
    {
        $helper = Mage::helper('fraisrconnect/data');

        return array(
            self::TYPE_CODE_SUCCESS   => $helper->__('Success'),
            self::TYPE_CODE_WARNING   => $helper->__('Warning'),
            self::TYPE_CODE_ERROR     => $helper->__('Error'),
            self::TYPE_CODE_NOTICE    => $helper->__('Notice'),
        );
    }

    /**
     * Log success entry
     * 
     * @return Fraisr_Connect_Model_Log
     */
    public function logSuccess()
    {
        $this
            ->setType(self::TYPE_CODE_SUCCESS)
            ->save();
        return $this;
    }

    /**
     * Log warning entry
     * 
     * @return Fraisr_Connect_Model_Log
     */
    public function logWarning()
    {
        $this
            ->setType(self::TYPE_CODE_WARNING)
            ->save();
        return $this;
    }

    /**
     * Log error entry
     * 
     * @return Fraisr_Connect_Model_Log
     */
    public function logError()
    {
        $this
            ->setType(self::TYPE_CODE_ERROR)
            ->save();
        return $this;
    }

    /**
     * Log notice entry
     * 
     * @return Fraisr_Connect_Model_Log
     */
    public function logNotice()
    {
        $this
            ->setType(self::TYPE_CODE_NOTICE)
            ->save();
        return $this;
    }

    /**
     * Rewrite save method to add the current GMT created_at date
     * 
     * @return Fraisr_Connect_Model_Log
     */
    public function save()
    {
        $this->setCreatedAt(Mage::getModel('core/date')->gmtDate());
        return parent::save();
    }
}