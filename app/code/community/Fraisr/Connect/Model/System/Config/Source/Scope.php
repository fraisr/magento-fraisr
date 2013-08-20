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
 * Scope Source Model
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Model_System_Config_Source_Scope
{
    /**
     * @const SCOPE_GLOBAL Select value for global scope
     */
    const SCOPE_GLOBAL = '#global#';

    /**
     * @const KEY_WEBSITE Value prefix to build optgroups in scope select box
     */
    const KEY_WEBSITE = '#website#_';

    /**
     * Get scope options for extension configuration
     * 
     * @return array
     */
    public function toOptionArray()
    {
        //Build option for global scope
        $scopes[] = array(
            'label' => Mage::helper('fraisrconnect/data')->__('Global'),
            'value' => self::SCOPE_GLOBAL
        );

        //For every website
        foreach (Mage::app()->getWebsites() as $website) {
            //Website optgroup option
            $scopes[self::KEY_WEBSITE.$website->getId()] = array(
                'value' => array(),
                'label' => $website->getName()
            );
            
            //For every store
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    //Store option
                    $scopes[self::KEY_WEBSITE.$website->getId()]['value'][] = array(
                        'value' => $store->getId(),
                        'label' => $store->getName()
                    );
                }
            }
        }
        return $scopes;
    }
}