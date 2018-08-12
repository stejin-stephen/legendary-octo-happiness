<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_event
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * https://docs.joomla.org/Creating_a_custom_form_field_type
 */

defined('_JEXEC') or die;

jimport('joomla.form.formfield');
 
class JFormFieldCategorytype extends JFormField {
 
	protected $type = 'Categorytype';
 
	public function getInput() {
		//get database
		$db 		= JFactory::getDbo();
		//$item_id	= JRequest::getVar('id',0);
		$item_id 	= isset($_REQUEST['filter']['categorytype_id']) ? $_REQUEST['filter']['categorytype_id'] : '';
		
		//To get selected category list
		$query 		= $db->getQuery(true);
		$query->select('c.*')			
			->from('#__tools_categorytypes c')
			->order('id');
			
		$db->setQuery($query);
		//echo nl2br(str_replace('#__','dmo3c_',$query));	
		$categorytypes =$db->loadObjectList();
		
		
		$category_select='<select id="filter_categorytype_id" name="filter[categorytype_id]" onchange="this.form.submit();" >';
		$category_select.='<option value="">'.JText::_('Select Category Type').'</option>';
			if(count($categorytypes)>0){
				foreach($categorytypes as $category){
					if($category->id==$item_id){
						$category_select.='<option value="'.$category->id.'" selected="selected" >'.$category->title.'</option>';
					}else{
						$category_select.='<option value="'.$category->id.'">'.$category->title.'</option>';
					}
				}
			}
		$category_select.='</select>';
		
		return $category_select;
	}
}
?>