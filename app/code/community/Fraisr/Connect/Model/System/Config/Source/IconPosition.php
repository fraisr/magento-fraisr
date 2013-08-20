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
     * @const DONATION_LABEL_ICON_POSITION_TOPLEFT fraisr icon position topleft
     */
    const DONATION_LABEL_ICON_POSITION_TOPLEFT = 'topleft';

    /**
     * @const DONATION_LABEL_ICON_POSITION_TOPRIGHT fraisr icon position topright
     */
    const DONATION_LABEL_ICON_POSITION_TOPRIGHT = 'topright';

    /**
     * @const DONATION_LABEL_ICON_POSITION_BOTTOMLEFT fraisr icon position bottomleft
     */
    const DONATION_LABEL_ICON_POSITION_BOTTOMLEFT = 'bottomleft';

    /**
     * @const DONATION_LABEL_ICON_POSITION_BOTTOMRIGHT fraisr icon position bottomright
     */
    const DONATION_LABEL_ICON_POSITION_BOTTOMRIGHT = 'bottomright';

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
            'value' => self::DONATION_LABEL_ICON_POSITION_TOPLEFT,
            'label' => Mage::helper('fraisrconnect/data')->__('Top left')
        );
        $iconPositionOptions[] = array(
            'value' => self::DONATION_LABEL_ICON_POSITION_TOPRIGHT,
            'label' => Mage::helper('fraisrconnect/data')->__('Top right')
        );
        $iconPositionOptions[] = array(
            'value' => self::DONATION_LABEL_ICON_POSITION_BOTTOMLEFT,
            'label' => Mage::helper('fraisrconnect/data')->__('Bottom left')
        );
        $iconPositionOptions[] = array(
            'value' => self::DONATION_LABEL_ICON_POSITION_BOTTOMRIGHT,
            'label' => Mage::helper('fraisrconnect/data')->__('Bottom right')
        );
        $iconPositionOptions[] = array(
            'value' => self::DONATION_LABEL_ICON_POSITION_CENTER,
            'label' => Mage::helper('fraisrconnect/data')->__('Center')
        );

        return $iconPositionOptions;
    }
}