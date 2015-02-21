<?php

/**
 * Campaign edit block
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */
class Aydus_Scheduledemails_Block_Adminhtml_Campaign_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'campaign_id';
        
        $this->_controller = 'adminhtml_campaign';
        $this->_blockGroup = 'aydus_scheduledemails';
        
        parent::__construct();
                
        if ($this->_isAllowedAction('save')) {
        	$this->_updateButton('save', 'label', $this->helper('aydus_scheduledemails')->__('Save Campaign'));
        	$this->_addButton('saveandcontinue', array(
        			'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
        			'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
        			'class'     => 'save',
        	), -100);
        } else {
        	$this->_removeButton('save');
        }
        
        if ($this->_isAllowedAction('delete')) {        	 
        	$this->_updateButton('delete', 'label', $this->helper('aydus_scheduledemails')->__('Delete Campaign'));
        } else {
        	$this->_removeButton('delete');
        }        
        
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_campaign')->getId()) {
        	$name = Mage::registry('current_campaign')->getName();
            return $this->helper('aydus_scheduledemails')->__("Edit Campaign '%s'", $this->htmlEscape($name));
        }
        else {
            return $this->helper('aydus_scheduledemails')->__('New Campaign');
        }
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
    
    /**
     * Set form action
     */
    public function getSaveUrl()
    {
    	return $this->getUrl('*/*/save');
    }
    
    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
    	return $this->getUrl('*/*/save', array(
    			'_current'   => true,
    			'back'       => 'edit',
    			'active_tab' => '{{tab_id}}'
    	));
    }    

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('campaign_edit_tabs');
        if ($tabsBlock) {
            $tabsBlockJsObject = $tabsBlock->getJsObjectName();
            $tabsBlockPrefix   = $tabsBlock->getId() . '_';
        } else {
            $tabsBlockJsObject = 'campaign_tabsJsTabs';
            $tabsBlockPrefix   = 'campaign_tabs_';
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }

            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }
                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                editForm.submit(url);
            }
        ";
        return parent::_prepareLayout();
    }

}
