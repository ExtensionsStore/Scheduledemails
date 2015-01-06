<?php

/**
 * Campaign model
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Model_Campaign extends Mage_Core_Model_Abstract
{
	const IMMEDIATELY = 0;
	const ONE_DAY_AFTER = '1 DAY';
	const ONE_WEEK_AFTER = '1 WEEK';
	const ONE_MONTH_AFTER = '1 MONTH';
	const THREE_MONTHS_AFTER = '3 MONTH';
	const SIX_MONTHS_AFTER = '6 MONTH';
	const NINE_MONTHS_AFTER = '9 MONTH';
	const ONE_YEAR_AFTER = '1 YEAR';
	
	protected $_emails;
		
	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
        parent::_construct();
        
		$this->_init('aydus_scheduledemails/campaign');
	}	
	
    /**
     * Processing object after delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDelete()
    {
    	try {
    		
    		//delete customers
			$campaignCustomers = Mage::getModel('aydus_scheduledemails/campaign_customer')->getCollection();
			$campaignCustomers->addFieldToFilter('campaign_id', $this->getId());
			if ($campaignCustomers->getSize()){
				foreach ($campaignCustomers as $campaignCustomer){
					$campaignCustomer->delete();
				}
			}
			//delete schedules
			$campaignSchedules = Mage::getModel('aydus_scheduledemails/campaign_schedule')->getCollection();
			$campaignSchedules->addFieldToFilter('campaign_id', $this->getId());
			if ($campaignSchedules->getSize()){
				foreach ($campaignSchedules as $campaignSchedule){
					$campaignSchedule->delete();
				}
			}
				
    	} catch(Exception $e){
    		
    		Mage::log($e->getMessage(), null, 'aydus_scheduledemails.log');
    		
    	}
    	
        return parent::_afterDelete();
    }
	
	/**
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 */
	public function scheduleEmails($schedule)
	{	
		$campaigns = $this->getCollection();
	
		$size = $campaigns->getSize();
	
		if ($size > 0){
				
			$campaignsScheduled = array();
				
			foreach ($campaigns as $campaign){
	
				$campaignId = $campaign->getId();
				$jobCode = 'aydus_scheduledemails_runcampaign';
				$frequency = $campaign->getFrequency();
				$monthDay = $campaign->getMonthDay();
				$weekDay = $campaign->getWeekday();
				$startTime = $campaign->getStartTime();
	
				$scheduledAt = Mage::getModel('aydus_scheduledemails/cron')->generateSchedules($campaignId, $jobCode, $frequency, $monthDay, $weekDay, $startTime);
				$campaignsScheduled[] = 'Campaign '.$campaignId .' at '.$scheduledAt;
			}
				
			return implode(',', $campaignsScheduled);
		}
	
		return 'Nothing to run';
	}	
	
	/**
	 * Run campaign
	 * 
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @param string $result;
	 */
	public function runCampaign($schedule)
	{
		$scheduleId = $schedule->getId();

		Mage::log('Scheduled '.$scheduleId, null, 'aydus_scheduledemails.log');
		$campaignSchedule = Mage::getModel('aydus_scheduledemails/campaign_schedule')->load($scheduleId);
		
		try {
			
			if ($campaignSchedule->getId() && $campaignSchedule->getCampaignId()){
					
				$campaignId = $campaignSchedule->getCampaignId();
				Mage::log('Running '.$campaignId, null, 'aydus_scheduledemails.log');
					
				$this->load($campaignId);
					
				if ($this->getId()){
			
					$scheduledEmails = $this->getScheduledEmails();
					$scheduledEmails = unserialize($scheduledEmails);
						
					if(is_array($scheduledEmails) && count($scheduledEmails)>0){
							
						$numberStages = count($scheduledEmails);
			
						foreach ($scheduledEmails as $stage => $scheduledEmail){
								
							$emailTemplateId = $scheduledEmail['email'];
							$interval = $scheduledEmail['schedule'];
			
							$campaignCustomers = Mage::getModel('aydus_scheduledemails/campaign_customer')->getCollection();
							
							$select = $campaignCustomers->getSelect();
							
							if ($interval){
								
								$now = date('Y-m-d H:i:s');
								$select->where("campaign_id = ? AND stage < ? AND DATE_SUB('$now', INTERVAL $interval) >= date_created", $campaignId, $numberStages);
								
							} else {
								
								$select->where("campaign_id = ? AND stage = 0", $campaignId);
							}
							
							$selectStr = (string)$select;
															
							if ($campaignCustomers->getSize()){
								
								foreach ($campaignCustomers as $campaignCustomer){
										
									//order item and product for email template
									$incrementId = $campaignCustomer->getIncrementId();
									$order = Mage::getModel('sales/order')->load($incrementId, 'increment_id');
									$storeId = $order->getStore()->getId();
									$itemId = $campaignCustomer->getItemId();
									$item = Mage::getModel('sales/order_item')->load($itemId);
									$productId = $campaignCustomer->getProductId();
									$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
																			
									$mailer = Mage::getModel('core/email_template_mailer');
									$mailer->setSender(array('email' => Mage::getStoreConfig('trans_email/ident_sales/email'), 'name' => Mage::getStoreConfig('trans_email/ident_sales/name')));
									$mailer->setStoreId($storeId);
									$mailer->setTemplateId($emailTemplateId);
									$mailer->setTemplateParams(array(
											'order'        => $order,
											'item'		   => $item,
											'product'      => $product,
										)
									);
									
									//customer name email
									if ($order->getCustomerIsGuest()) {
										$customerName = $order->getBillingAddress()->getName();
									} else {
										$customerName = $order->getCustomerName();
									}							
									$email = $campaignCustomer->getEmail();		
									$emailInfo = Mage::getModel('core/email_info');
									$emailInfo->addTo($email, $customerName);
									$mailer->addEmailInfo($emailInfo);
										
									$sent = $mailer->send();
									
									if ($sent){
										
										$campaignCustomer->setStage($stage + 1)->setDateUpdated(date('Y-m-d H:i:s'))->save();
										
									} else {
										
										$log = 'Could not email '.$email;
										Mage::log($log, null, 'aydus_scheduledemails.log');
									}
									
								}
									
							} else {
								$log = 'No customers for '.$campaignId.' stage '.$stage + 1;
								Mage::log($log, null, 'aydus_scheduledemails.log');
							}
						}
					}
					
				} else {
					$result = 'No campaign id '.$campaignId;
					Mage::log($result, null, 'aydus_scheduledemails.log');
				}
				
				//delete the schedule, no longer needed
				$campaignSchedule->delete();
			
			} else {
					
				$result = 'No campaign schedule '.$scheduleId;
				Mage::log($result, null, 'aydus_scheduledemails.log');
			}
			
		} catch(Exception $e){
			
			$result = $e->getMessage();
			Mage::log($result, null, 'aydus_scheduledemails.log');
		}
		
		if (!$result){
			$result = "Schedule $scheduleId campaign $campaignId ran successfully";
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @param Male_Sales_Model_Order_Item $item
	 * @param string $customerEmail
	 * @return boolean|Aydus_Scheduledemails_Model_Campaign_Customer
	 */
	public function registerItem($item, $order, $customerEmail)
	{
		$incrementId = $order->getIncrementId();
		$itemId = $item->getId();
		$product = $item->getProduct();
		$productId = $item->getProductId();
		$attributeSetId = $product->getAttributeSetId();
		$sku = $product->getSku();
		
		try {
			
			$campaigns = $this->getCollection();
			$size = $campaigns->getSize();
			
			if ($size > 0){
					
				foreach ($campaigns as $campaign){
			
					$campaignId = $campaign->getId();
					$attributeSetIds = $campaign->getAttributeSetIds();
					$attributeSetIds = unserialize($attributeSetIds);
					$skus = explode('\n',$campaign->getSkus());
			
					if ((is_array($attributeSetIds) && count($attributeSetIds)> 0) || (is_array($skus) && count($skus)>0)){
							
						if (in_array($attributeSetId, $attributeSetIds) || in_array($sku, $skus)){
			
							$currentDate = date('Y-m-d H:i:s');
							$campaignCustomer = Mage::getModel('aydus_scheduledemails/campaign_customer');
			
							$campaignCustomer->setEmail($customerEmail)
							->setIncrementId($incrementId)
							->setItemId($itemId)
							->setProductId($productId)
							->setCampaignId($campaignId)
							->setStage(0)
							->setDateCreated($currentDate)
							->setDateUpdated($currentDate)
							->save();
							
							return $campaignCustomer;
						}
							
					} else {
							
						Mage::log('No products for campaign '.$campaignId, null, 'aydus_scheduledemails.log');
			
					}
			
				}
					
			}
				
		} catch(Exception $e){
			
			Mage::log($e->getMessage(), null, 'aydus_scheduledemails.log');
				
		}
		
		return false;
	}
	
}