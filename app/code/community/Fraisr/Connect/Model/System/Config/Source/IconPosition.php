
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
 * Icon Position Source Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_System_Config_Source_IconPosition
{
    /**
     * @const DONATION_LABEL_ICON_POSITION_TOP fraisr icon position top
     */
    const DONATION_LABEL_ICON_POSITION_TOP = 'top';

    /**
     * @const DONATION_LABEL_ICON_POSITION_BOTTOM fraisr icon position bottom
     */
    const DONATION_LABEL_ICON_POSITION_BOTTOM = 'bottom';

    /**
     * @const DONATION_LABEL_ICON_POSITION_CENTER fraisr icon position center
     */
    const DONATION_LABEL_ICON_POSITION_CENTER = 'center';

    /**
     * Get fraisr icon position source options
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $iconPositionOptions = array();

        $iconPositionOptions[] = array(
            'value' => self::DONATION_LABEL_ICON_POSITION_TOP,
            'label' => Mage::helper('fraisrconnect/data')->__('Top')
        );
        $iconPositionOptions[] = array(
            'value' => self::DONATION_LABEL_ICON_POSITION_BOTTOM,
            'label' => Mage::helper('fraisrconnect/data')->__('Bottom')
        );
        $iconPositionOptions[] = array(
            'value' => self::DONATION_LABEL_ICON_POSITION_CENTER,
            'label' => Mage::helper('fraisrconnect/data')->__('Center')
        );

        return $iconPositionOptions;
    }
}
