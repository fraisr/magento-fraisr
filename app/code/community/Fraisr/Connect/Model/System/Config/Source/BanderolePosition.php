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
     * @const DONATION_LABEL_BANDEROLE_POSITION_TOP fraisr banderole position top
     */
    const DONATION_LABEL_BANDEROLE_POSITION_TOP = 'top';

    /**
     * @const DONATION_LABEL_BANDEROLE_POSITION_BOTTOM fraisr banderole position bottom
     */
    const DONATION_LABEL_BANDEROLE_POSITION_BOTTOM = 'bottom';

    /**
     * @const DONATION_LABEL_BANDEROLE_POSITION_CENTER fraisr banderole position center
     */
    const DONATION_LABEL_BANDEROLE_POSITION_CENTER = 'center';

    /**
     * Get fraisr banderole position source options
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $banderolePositionOptions = array();

        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_TOP,
            'label' => Mage::helper('fraisrconnect/data')->__('Top')
        );
        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_BOTTOM,
            'label' => Mage::helper('fraisrconnect/data')->__('Bottom')
        );
        $banderolePositionOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE_POSITION_CENTER,
            'label' => Mage::helper('fraisrconnect/data')->__('Center')
        );

        return $banderolePositionOptions;
    }
}