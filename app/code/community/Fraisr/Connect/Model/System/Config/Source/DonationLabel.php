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
 * Donation Label Source Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_System_Config_Source_DonationLabel
{
    /**
     * @const DONATION_LABEL_ICON fraisr label in product list as icon
     */
    const DONATION_LABEL_ICON = 'icon';

    /**
     * @const DONATION_LABEL_BANDEROLE fraisr label in product list as a banderole
     */
    const DONATION_LABEL_BANDEROLE = 'banderole';

    /**
     * Get donation label source options
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $donationLabelOptions = array();

        $donationLabelOptions[] = array(
            'value' => self::DONATION_LABEL_ICON,
            'label' => Mage::helper('fraisrconnect/data')->__('Icon')
        );
        $donationLabelOptions[] = array(
            'value' => self::DONATION_LABEL_BANDEROLE,
            'label' => Mage::helper('fraisrconnect/data')->__('Banderole')
        );

        return $donationLabelOptions;
    }
}
