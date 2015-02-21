<?php

/**
 * Array form element from System Config
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */
class Aydus_Scheduledemails_Block_Adminhtml_System_Config_Form_Field_Array
	extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract 
{
    
	/**
	 * Pass in widget element attributes
	 * 
	 * @param unknown $config
	 */
    public function __construct($attributes)
    {
    	if (is_array($attributes['columns']) && count($attributes['columns'])>0){
    		foreach ($attributes['columns'] as $columnName => $column){
    			$this->addColumn($columnName, $column);
    		}
    	}
    
        parent::__construct($attributes);
    }
    
    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array $params
     */
    public function addColumn($name, $params)
    {
    	$this->_columns[$name] = array(
    			'label'     => empty($params['label']) ? 'Column' : $params['label'],
    			'size'      => empty($params['size'])  ? false    : $params['size'],
    			'style'     => empty($params['style'])  ? null    : $params['style'],
    			'class'     => empty($params['class'])  ? null    : $params['class'],
    			'type'     	=> empty($params['type'])  ? 'text'    : $params['type'],
    			'values'    => empty($params['values'])  ? null    : $params['values'],
    			'renderer'  => false,
    	);
    	if ((!empty($params['renderer'])) && ($params['renderer'] instanceof Mage_Core_Block_Abstract)) {
    		$this->_columns[$name]['renderer'] = $params['renderer'];
    	}
    }    
    

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
    	if (empty($this->_columns[$columnName])) {
    		throw new Exception('Wrong column name specified.');
    	}
    	$column     = $this->_columns[$columnName];
    	$type		= $column['type'];
    	$optionArray= $column['values'];
    	$value 		= $this->getValue();
    	$inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
    
    	if ($column['renderer']) {
    		return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
    		->toHtml();
    	}
    	    	
    	if ($type=='select' && is_array($optionArray) && count($optionArray)>0){
    		
    		$cell = '<select name="'.$inputName.'" class="'.(isset($column['class']) ? $column['class'] : 'select').'" '.(isset($column['style']) ? ' style="'.$column['style'] . '"' : '').'  >';
    		
    		foreach ($optionArray as $i=>$option){
    			    			
    			$cell .= '<option value="'.$option['value'].'">'.$option['label'].'</option>';
    		}
    		
    		$cell .= '</select>';
    		
    	} else {
    		
    		$cell = '<input type="'.$type.'" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
    				($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
    				(isset($column['class']) ? $column['class'] : 'input-text') . '"'.
    				(isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '/>';
    	}
    	
    	return $cell;
    }    
    
}

