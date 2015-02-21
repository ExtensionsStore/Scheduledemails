<?php

/**
 * Campaign customer model
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Model_Campaign_Customer extends Mage_Core_Model_Abstract
{
		
	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
        parent::_construct();
        
		$this->_init('aydus_scheduledemails/campaign_customer');
	}	
	
	
}