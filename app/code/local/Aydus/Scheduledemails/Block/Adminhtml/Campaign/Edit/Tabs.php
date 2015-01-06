<?php

/**
 * Campaign edit tabs
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Block_Adminhtml_Campaign_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs 
{
    /**
     * Initialize Tabs
     */
    public function __construct() 
    {
        parent::__construct();
        $this->setId('campaign_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('aydus_scheduledemails')->__('Campaign Details'));
    }

}
