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
 * Banderole Position Source Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_System_Config_Source_BanderolePosition
{
    /**
     * @const DONATION_LABEL_BANDEROLE_POSITION_TOPLEFT fraisr banderole position topleft
     */
    const DONATION_LABEL_BANDEROLE_POSITION_TOPLEFT = 'topleft';

    /**
     * @const DONATION_LABEL_BANDEROLE_POSITION_TOPRIGHT fraisr banderole position topright
     */
    const DONATION_LABEL_BANDEROLE_POSITION_TOPRIGHT = 'topright';

    /**
     * @const DONATION_LABEL_BANDEROLE_POSITION_BOTTOMLEFT fraisr banderole position bottomleft
     */
    const DONATION_LABEL_BANDEROLE_POSITION_BOTTOMLEFT = 'bottomleft';

    /**
     * @const DONATION_LABEL_BANDEROLE_POSITION_BOTTOMRIGHT fraisr banderole position bottomright
     */
    const DONATION_LABEL_BANDEROLE_POSITION_BOTTOMRIGHT = 'bottomright';

    /**
     * @const DONATION_LABEL_BANDEROLE_POSITION_CENTER fraisr banderole position center
     */
    const DONATION_LABEL_BANDEROLE_POSITION_CENTER = 'center';

    /**
     * Get fraisr icon position source options
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $banderolePositionOptions = array();

        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_TOPLEFT,
            'label' => Mage::helper('fraisrconnect/data')->__('Top left')
        );
        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_TOPRIGHT,
            'label' => Mage::helper('fraisrconnect/data')->__('Top right')
        );
        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_BOTTOMLEFT,
            'label' => Mage::helper('fraisrconnect/data')->__('Bottom left')
        );
        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_BOTTOMRIGHT,
            'label' => Mage::helper('fraisrconnect/data')->__('Bottom right')
        );
        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_CENTER,
            'label' => Mage::helper('fraisrconnect/data')->__('Center')
        );

        return $banderolePositionOptions;
    }
}