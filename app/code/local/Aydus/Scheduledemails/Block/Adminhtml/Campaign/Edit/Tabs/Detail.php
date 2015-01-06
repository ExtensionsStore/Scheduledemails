<?php

/**
 * Campaign details tab
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Block_Adminhtml_Campaign_Edit_Tabs_Detail 
	extends Mage_Adminhtml_Block_Widget_Form 
		implements Mage_Adminhtml_Block_Widget_Tab_Interface
{	
    /**
     * Initialize form
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Account
     */
    public function _prepareForm()
    {
        $campaign = Mage::registry('current_campaign');
        
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('campaign_');
        
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->helper('aydus_scheduledemails')->__('Campaign Information')
        ));
        
        if ($campaign->getId()) {
        	$fieldset->addField('campaign_id', 'hidden', array(
        		'name' => 'campaign_id',
        	));
        }     

        $fieldset->addField('admin_user_id', 'hidden', array(
        		'name' => 'admin_user_id',
        		'value' => Mage::getSingleton('admin/session')->getUser()->getId(),
        ));
        
        $fieldset->addField('name', 'text', array(
        	'label' => $this->helper('aydus_scheduledemails')->__('Campaign Name'),
        	'name'  => 'name',
        	'required'  => true,
        ));

        $fieldset->addField('description', 'textarea', array(
        	'label' => $this->helper('aydus_scheduledemails')->__('Description'),
        	'name'  => 'description',
        	'required'  => true,
        ));        
                
        $fieldset->addField('frequency', 'select', array(
        		'label' => $this->helper('aydus_scheduledemails')->__('Frequency'),
        		'name'  => 'frequency',
        		'required'  => true,
        		'values' => Mage::getModel('adminhtml/system_config_source_cron_frequency')->toOptionArray(),
        		'note' => 'How often to run (once a month, week or day).'
        ));
        
        $fieldset->addField('month_day', 'text', array(
        		'label' => $this->helper('aydus_scheduledemails')->__('Day of the Month'),
        		'name'  => 'month_day',
        		'type'	=> 'number',
        		'required'  => false,
        		'note'	=> 'For month frequency, specify day of the month (1-31).',
        ));        

        $fieldset->addField('week_day', 'select', array(
        		'label' => $this->helper('aydus_scheduledemails')->__('Day of the Week'),
        		'name'  => 'week_day',
        		'required'  => false,
        		'values' => Mage::getModel('adminhtml/system_config_source_locale_weekdays')->toOptionArray(),
        		'note'	=> 'For week frequency, specify day of the week.',
        ));
        
        $fieldset->addField('start_time', 'time', array(
        		'label' => $this->helper('aydus_scheduledemails')->__('Start Time'),
        		'name'  => 'start_time',
        		'required'  => true,
        		'note'	=> 'Specify time of the day.',
        ));        
        
        $attributeSetOptions = Mage::getResourceModel('eav/entity_attribute_set_collection')->addFieldToFilter('entity_type_id', 4)->toOptionArray();
        $attributeSetIdsElement = $fieldset->addField('attribute_set_ids', 'multiselect', array(
        		'label' => $this->helper('aydus_scheduledemails')->__('Attribute Sets'),
        		'name'  => 'attribute_set_ids',
        		'values' => $attributeSetOptions,
        		'note' => 'Select product attribute sets to send for this campaign'
        ));
        
        $fieldset->addField('skus', 'textarea', array(
        		'label' => $this->helper('aydus_scheduledemails')->__('Skus'),
        		'name'  => 'skus',
        		'note' => 'Enter skus, one per line'
        ));
                  
        $fieldset->addType('array','Aydus_Scheduledemails_Block_Adminhtml_Widget_Form_Element_Array');
        $emailTemplateCollection = Mage::getResourceModel('core/email_template_collection');
        $emailTemplates = $emailTemplateCollection->toOptionArray();
        $schedules = array(
        	array('value' => Aydus_Scheduledemails_Model_Campaign::IMMEDIATELY,  'label' =>$this->helper('aydus_scheduledemails')->__('Immediately (next schedule)')),
        	array('value' => Aydus_Scheduledemails_Model_Campaign::ONE_DAY_AFTER ,  'label' =>$this->helper('aydus_scheduledemails')->__('1 Day After')),
        	array('value' => Aydus_Scheduledemails_Model_Campaign::ONE_WEEK_AFTER,  'label' =>$this->helper('aydus_scheduledemails')->__('1 Week After')),
        	array('value' => Aydus_Scheduledemails_Model_Campaign::ONE_MONTH_AFTER,  'label' => $this->helper('aydus_scheduledemails')->__('1 Month After')),
        	array('value' => Aydus_Scheduledemails_Model_Campaign::THREE_MONTHS_AFTER,  'label' => $this->helper('aydus_scheduledemails')->__('3 Months After')),
        	array('value' => Aydus_Scheduledemails_Model_Campaign::SIX_MONTHS_AFTER,  'label'  => $this->helper('aydus_scheduledemails')->__('6 Months After')),
        	array('value' => Aydus_Scheduledemails_Model_Campaign::NINE_MONTHS_AFTER,  'label'=> $this->helper('aydus_scheduledemails')->__('9 Months After')),
        	array('value' => Aydus_Scheduledemails_Model_Campaign::ONE_YEAR_AFTER, 'label'=> $this->helper('aydus_scheduledemails')->__('1 Year After')),
        );
                
        $scheduledEmailsElement = $fieldset->addField('scheduled_emails', 'array', array(
        		'label' => $this->helper('aydus_scheduledemails')->__('Scheduled Emails'),
        		'name'  => 'scheduled_emails',
        		'required'  => true,
        		'columns' => array(
        			'email' => array('label'=>Mage::helper('aydus_scheduledemails')->__('Email'), 'style'=> 'width:120px', 'type'=>'select', 'values' => $emailTemplates),
        			'schedule' => array('label' => Mage::helper('aydus_scheduledemails')->__('Schedule'), 'style' => 'width:120px', 'type'=>'select', 'values' => $schedules),
        		),
        		'note' => 'Assign email templates and schedules for this campaign'
        ));
        
        if (is_array($campaign->getData()) && count($campaign->getData())>0){
        	
        	$form->setValues($campaign->getData());
        	$attributeSetIds = unserialize($campaign->getAttributeSetIds());
        	$attributeSetIdsElement->setValue($attributeSetIds);
        	
        	if ($campaign->getScheduledEmails()){
        	
        		$scheduledEmails = unserialize($campaign->getScheduledEmails());
        		$scheduledEmailsElement->setValue($scheduledEmails);
        	}
        	 
        }
        
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
    	return Mage::helper('aydus_scheduledemails')->__('Details');
    }
    
    public function getTabTitle()
    {
    	return Mage::helper('aydus_scheduledemails')->__('Campaign Info');
    }
    
    public function canShowTab()
    {    
    	return true;
    }
    
    public function isHidden()
    {    
    	return false;
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
    	$allowed = Mage::getSingleton('admin/session')->isAllowed('system/scheduledemails/' . $action);
    	return $allowed;
    }
    
}
