<?php

/**
 * Adminhtml customer edit form block
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Block_Adminhtml_Campaign_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
    protected function _prepareForm()
    {
    	$action = $this->getData('action');
    	$form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $action, 'method' => 'post'));
    	$form->setUseContainer(true);
    	$this->setForm($form);
    	return parent::_prepareForm();
    }
    
}
