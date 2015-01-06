<?php

/**
 * Campaign grid
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */
class Aydus_Scheduledemails_Block_Adminhtml_Campaign_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('campaignGrid');
        $this->setDefaultSort('date_created');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    /**
     * prepare collection
     * @return Athena_Visitorlog_Block_Adminhtml_Visitorlog_Grid
     */
    protected function _prepareCollection()
    {
    	$collection = Mage::getResourceModel('aydus_scheduledemails/campaign_collection');
    	$this->setCollection($collection);
		parent::_prepareCollection();
    	
        return $this;
    }
    
    /**
     * prepare grid collection
     * @return Athena_Visitorlog_Block_Adminhtml_Visitorlog_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('campaign_id', array(
            'header'    => Mage::helper('aydus_scheduledemails')->__('ID'),
            'index'        => 'campaign_id',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('aydus_scheduledemails')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('date_created', array(
        		'header'    => Mage::helper('aydus_scheduledemails')->__('Date Created'),
        		'align'     => 'left',
        		'index'     => 'date_created',
        ));
                
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('aydus_scheduledemails')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('aydus_scheduledemails')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'campaign_id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('aydus_scheduledemails')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('aydus_scheduledemails')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('aydus_scheduledemails')->__('XML'));
        
        return parent::_prepareColumns();
    }
    
    /**
     * get the row url
     * 
     * @param Mage_Log_Model_Visitor
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('campaign_id' => $row->getId()));
    }
    
    /**
     * get the grid url
     * 
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    /**
     * after collection load
     * 
     * @return Athena_Visitorlog_Block_Adminhtml_Visitorlog_Grid
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    
    /**
     * filter store column
     * 
     * @param Mage_Log_Model_Resource_Visitor_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Athena_Visitorlog_Block_Adminhtml_Visitorlog_Grid
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        
        $collection->addStoreFilter($value);
        return $this;
    }
}
