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
 * Backend Config Support Area
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Block_Adminhtml_System_Config_Support
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Support Template
     * @var string
     */
    protected $_template = 'fraisrconnect/system/config/support.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $fieldset
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $fieldset)
    {
        $originalData = $fieldset->getOriginalData();
        $this->addData(array(
            'fieldset_label' => $fieldset->getLegend(),
        ));
        return $this->toHtml();
    }

    /**
     * Get extension version
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return(string) Mage::getConfig()
            ->getNode('modules')
            ->children()
            ->Fraisr_Connect
            ->version;
    }

    /**
     * Get Magento version
     *
     * @return string
     */
    public function getMageVersion()
    {
        $mageVersion = Mage::getVersion();
        if (true === is_callable('Mage::getEdition')) {
            $mageVersion = sprintf(
                '%s %s',
                Mage::getEdition(),
                $mageVersion
            );
        }
        return $mageVersion;
    }

    /**
     * Get support email address
     *
     * @return string
     */
    public function getSupportEmail()
    {
        return $this->getConfig()->getSupportEmail();
    }

    /**
     * Get Config Model
     * 
     * @return Fraisr_Connect_Model_Config
     */
    protected function getConfig()
    {
        return Mage::getModel('fraisrconnect/config');
    }
}
