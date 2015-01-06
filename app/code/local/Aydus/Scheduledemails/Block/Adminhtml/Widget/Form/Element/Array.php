<?php

/**
 * Adminhtml widget form array element
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */
class Aydus_Scheduledemails_Block_Adminhtml_Widget_Form_Element_Array 
	extends Varien_Data_Form_Element_Abstract 
{
    
	/**
	 * Render out the system config form element as a widget form element
	 * 
	 * @return string
	 */
    public function getElementHtml()
    {
    	$form = $this->getForm();
    	$parent = $form->getParent();
    	$layout = $parent->getLayout();
    	$arrayBlock = $layout->createBlock('aydus_scheduledemails/adminhtml_system_config_form_field_array', $this->getName(), $this->getData());
    	
    	$arrayBlock->setElement($this);
    	
    	$html = $arrayBlock->toHtml();
    	$html.= $this->getAfterElementHtml();
    	
    	$htmlId = $arrayBlock->getHtmlId();
    	$rows = $arrayBlock->getArrayRows();
    	$columns = $this->getColumns();
    	$selectedValues = $this->getValue();
    	
    	if (is_array($rows) && count($rows)>0 && is_array($columns) && count($columns)>0){
    		
    		$html.= '<script type="application/javascript">
    			';
    		foreach ($rows as $i=>$row){
    			foreach ($columns as $columnName=>$column){
    				$html.= '$$("select[name=\"'.$htmlId.'['.$i.']['.$columnName.']\"]").each(function(select, selectIndex){
	    			$(select).select("option").each(function(option, optionIndex){
	    				if ("'.$selectedValues[$i][$columnName].'" == option.value){
	    					option.selected = true;
	    				}
					});
				});
	    		';
    			}
    		}
    		
    		$html.= '</script>
    		';
    	}
    	 
    	return $html;
    }
}

