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
 * Abstract Synchronisation Helper
 * 
 * @category   Fraisr
 * @package    Fraisr_Connect
 * @author     André Herrn <andre.herrn@das-medienkombinat.de>
 */
class Fraisr_Connect_Helper_Synchronisation_Abstract extends Fraisr_Connect_Helper_Data
{
    /**
     * @const CRON_TASK_ADDITIONAL_MINUTES Continue cron task will start in x minutes
     */
    const CRON_TASK_ADDITIONAL_MINUTES = 15;
    
    /**
     * Check if the runtime is already exceeded
     *
     * Compare the starttime with the current time and the maximum execution time
     * 
     * @param int $synchronisationStartTime
     * @return boolean
     */
    public function isRuntimeExceeded($synchronisationStartTime)
    {
        //Get maximum execution time
        $maxExecutionTime = $this->getMaximumExecutionTime();

        //Return true for is exceeded if we are 10sec before the execution time
        if (($maxExecutionTime - 10) < (time() - $synchronisationStartTime)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get maximum execution time
     * 
     * @return int
     */
    protected function getMaximumExecutionTime()
    {
        $maxExecutionTime = (int) ini_get('max_execution_time');
        if (false === is_int($maxExecutionTime) 
            || $maxExecutionTime < 1) {
            $maxExecutionTime = INF;
        }
        return $maxExecutionTime;
    }

    /**
     * Build syncronisation report details
     * 
     * @param array $report
     * @return string
     */
    public function buildSyncReportDetails($reports)
    {
        //Return empty string if report is empty
        if (0 == count($reports)) {
            return '';
        }

        //Build message
        $reportMessage = '';
        foreach ($reports as $report) {
            foreach ($report as $key => $value) {
                $reportMessage .= sprintf('["%s":"%s"]', $key, $value);
            }
            $reportMessage .= "\n";
        }
        return $reportMessage;
    }
}