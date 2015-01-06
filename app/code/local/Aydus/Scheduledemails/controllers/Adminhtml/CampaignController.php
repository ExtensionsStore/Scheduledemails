<?php

/**
 * Campaign controller
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Adminhtml_CampaignController extends Mage_Adminhtml_Controller_Action 
{
	
	/**
	 * Init selected campaign
	 *
	 * @param string $idFieldName
	 * @return Aydus_Scheduledemails_Adminhtml_CampaignController
	 */
	protected function _initCampaign($idFieldName = 'campaign_id')
	{
        $this->_title($this->__('System'))
             ->_title($this->__('Scheduled Emails'))
             ->_title($this->__('Campaign Info'));
		$campaignId = (int) $this->getRequest()->getParam($idFieldName);
			
		$campaign = Mage::getModel('aydus_scheduledemails/campaign');
	
		if ($campaignId) {
			$campaign->load($campaignId);
		}
	
		Mage::register('current_campaign',$campaign);
		return $this;
	}
			
	/**
	 *	Campaigns admin
	 */
	public function indexAction() 
	{
        $this->loadLayout()->renderLayout();
	}
	
	/**
	 * Campaigns grid
	 */
	public function gridAction()
	{
        $this->loadLayout()->renderLayout();
	}
	
	/**
	 * Add/edit campaign
	 */
	public function editAction()
	{
		$this->_initCampaign();
		$this->loadLayout()->renderLayout();
	}
	
	/**
	 * Create new campaign
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}	
	
	/**
	 * Save action
	 */
	public function saveAction()
	{

		if ($data = $this->getRequest()->getPost()) {
			
			$adminUserId = (int)$data['admin_user_id'];
			$adminUserId = ($adminUserId) ? $adminUserId : Mage::getSingleton('admin/session')->getUser()->getId();
			$name = $data['name'];
			$description = $data['description'];
			$frequency = $data['frequency'];
			$monthDay = $data['month_day'];
			$weekDay = $data['week_day'];
			$startTime = (is_array($data['start_time']) && count($data['start_time'])>0) ? implode(',',$data['start_time']) : '00,00,00';
			$attributeSetIds = array();
			if (is_array($data['attribute_set_ids']) && count($data['attribute_set_ids'])>0){
				$attributeSetIds = $data['attribute_set_ids'];
			}
			$attributeSetIds = serialize($attributeSetIds);
			$skus = $data['skus'];
				
			$scheduledEmails = array();
			$data['scheduled_emails'] = array_values(array_filter($data['scheduled_emails']));
			if (is_array($data['scheduled_emails']) && count($data['scheduled_emails'])>0){
				$scheduledEmails = $data['scheduled_emails'];
			}
			$scheduledEmails = serialize($scheduledEmails);
				
			$campaign = Mage::getModel('aydus_scheduledemails/campaign');
				
			if ($campaignId = $this->getRequest()->getParam('campaign_id')) {
				
				$campaign->load($campaignId);
				$campaign->setDateCreated(date('Y-m-d H:i:s'));
				
			} else {
				
				$campaign->setDateCreated(date('Y-m-d H:i:s'));
				$campaign->setDateUpdated(date('Y-m-d H:i:s'));
			}

			$campaign->setAdminUserId($adminUserId);
			$campaign->setName($name);
			$campaign->setDescription($description);
			$campaign->setAttributeSetIds($attributeSetIds);
			$campaign->setSkus($skus);
			$campaign->setScheduledEmails($scheduledEmails);
			$campaign->setFrequency($frequency);
			$campaign->setMonthDay($monthDay);
			$campaign->setWeekDay($weekDay);
			$campaign->setStartTime($startTime);
				
			try {
				
				$campaign->save();
	
				// display success message
				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('aydus_scheduledemails')->__('The Campaign has been saved.'));

				Mage::getSingleton('adminhtml/session')->setFormData(false);
				// check if 'Save and Continue'
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('campaign_id' => $campaign->getId(), '_current'=>true));
					return;
				}
				// go to grid
				$this->_redirect('*/*/');
				return;
	
			} catch (Mage_Core_Exception $e) {
				
				$this->_getSession()->addError($e->getMessage());
				
			} catch (Exception $e) {
				
				$this->_getSession()->addException($e,
						Mage::helper('aydus_scheduledemails')->__('An error occurred while saving the campaign.'));
			}
	
			$this->_getSession()->setFormData($data);
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			return;
		}
		
		$this->_redirect('*/*/');
	}	
	
	/**
	 * Delete Campaign
	 */
	public function deleteAction()
	{
		if ($campaignId = $this->getRequest()->getParam('campaign_id')) {
			try {
				$campaign = Mage::getModel('aydus_scheduledemails/campaign');
				$campaign->load($campaignId);
				$campaign->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(
				Mage::helper('aydus_scheduledemails')->__('The campaign has been deleted.'));

				$this->_redirect('*/*/');
				return;
		
			} catch (Exception $e) {
				// display error message
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				// go back to edit form
				$this->_redirect('*/*/edit', array('campaign_id' => $id));
				return;
			}
		}

		Mage::getSingleton('adminhtml/session')->addError(
		Mage::helper('aydus_scheduledemails')->__('Unable to find a campaign to delete.'));

		$this->_redirect('*/*/');		
	}
	
	/**
	 * Export campaign grid to CSV format
	 */
	public function exportCsvAction()
	{
		$fileName   = 'campaigns.csv';
		$content    = $this->getLayout()->createBlock('aydus_scheduledemails/adminhtml_campaign_grid')
		->getCsvFile();
	
		$this->_prepareDownloadResponse($fileName, $content);
	}
	
	/**
	 * Export campaign grid to XML format
	 */
	public function exportXmlAction()
	{
		$fileName   = 'campaigns.xml';
		$content    = $this->getLayout()->createBlock('aydus_scheduledemails/adminhtml_campaign_grid')
		->getExcelFile();
	
		$this->_prepareDownloadResponse($fileName, $content);
	}	
	
	/**
	 *  Export campaign grid to Excel XML format
	 */
	public function exportExcelAction()
	{
		$fileName   = 'campaigns.xml';
		$grid       = $this->getLayout()->createBlock('aydus_scheduledemails/adminhtml_campaign_grid');
		$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
	}
	

}