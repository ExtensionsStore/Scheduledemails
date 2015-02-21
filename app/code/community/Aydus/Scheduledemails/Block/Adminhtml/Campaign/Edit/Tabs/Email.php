<?php

/**
 * Emails for campaign
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_Scheduledemails_Block_Adminhtml_Campaign_Edit_Tabs_Email
	extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('campaign_email_grid');
        $this->setDefaultSort('visit_time', 'desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
	
    protected function _prepareCollection() 
    {

        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare the grid columns
     * 
     * @return Preserve_Artisan_Block_Adminhtml_Artisan_Edit_Tab_Product
     */
    protected function _prepareColumns()
    {
        $this->addColumn('email_id', array(
            'header'=> $this->helper('aydus_scheduledemails')->__('ID'),
            'align' => 'left',
            'index' => 'email_id',
        ));
    }

    /**
     * get row url
     * 
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }
    
    /**
     * get grid url
     * 
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/customers', array(
            'campaign_id'=>$this->getCampaign()->getId()
        ));
    }
    
    /**
     * get the current visitor
     * 
     * @return Mage_Log_Model_Visitor
     */
    public function getCampaign()
    {
        return Mage::registry('current_campaign');
    }

}
