<?php

/**
 * Scheduled Emails observer
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Model_Observer 
{
	
	/**
	 *
	 * @param Varien_Object $observer
	 * 
	 * @return Aydus_Scheduledemails_Model_Observer
	 */
	public function registerOrder($observer)
	{
		$order = $observer->getOrder();
		$customerEmail = $order->getCustomerEmail();
		
		$items = $order->getAllItems();
		
		$campaign = Mage::getModel('aydus_scheduledemails/campaign');
		
		foreach ($items as $item){
			
			$campaignCustomer = $campaign->registerItem($item, $order, $customerEmail);
			
			if ($campaignCustomer && $campaignCustomer->getId()){
				Mage::log($customerEmail.' registered for campaign '.$campaignCustomer->getCampaignId(), null, 'aydus_scheduledemails.log');
				
			}
				
		}
		
		return $this;
	}	
	
}


