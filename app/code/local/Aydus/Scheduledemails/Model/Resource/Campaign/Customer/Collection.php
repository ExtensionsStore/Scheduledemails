<?php

/**
 * Campaign customer resource collection model
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */
	
class Aydus_Scheduledemails_Model_Resource_Campaign_Customer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract 
{

	protected function _construct()
	{
        parent::_construct();
		$this->_init('aydus_scheduledemails/campaign_customer');
	}
	
}