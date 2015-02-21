<?php

/**
 * Campaign resource model
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Model_Resource_Campaign_Schedule extends Mage_Core_Model_Resource_Db_Abstract
{
	
	protected function _construct()
	{
		$this->_init('aydus_scheduledemails/campaign_schedule', 'schedule_id');
		$this->_isPkAutoIncrement = false;
	}
	
}

