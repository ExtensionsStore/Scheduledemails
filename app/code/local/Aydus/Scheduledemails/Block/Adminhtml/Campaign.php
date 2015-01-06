<?php

/**
 * Campaign grid container
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */
class Aydus_Scheduledemails_Block_Adminhtml_Campaign extends Mage_Adminhtml_Block_Widget_Grid_Container 
{

    public function __construct()
    {
        $this->_controller         = 'adminhtml_campaign';
        $this->_blockGroup         = 'aydus_scheduledemails';
        parent::__construct();
        $this->_headerText         = Mage::helper('aydus_scheduledemails')->__('Campaigns');
    }
}
