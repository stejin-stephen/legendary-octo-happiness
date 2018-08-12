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
 
class JFormFieldCat extends JFormField {
 
	protected $type = 'Cat';
 
	public function getInput() {
		//get database
		$db 		= JFactory::getDbo();
		//$item_id	= JRequest::getVar('id',0);
		$item_id 	= isset($_REQUEST['filter']['category_id']) ? $_REQUEST['filter']['category_id'] : '';
		$langsession 	= JFactory::getSession()->get('registry');
		
		//To get selected category list
		$query 		= $db->getQuery(true);
		$query->select('c.*')			
			->from('#__categories c')
			->where('c.extension="com_tools"')
			->order('id');
		
		if($langsession->get('application.lang')){
		    $query->where("c.language IN ('". $langsession->get('application.lang') ."')");
		}
			
		$db->setQuery($query);
		//echo nl2br(str_replace('#__','dmo3c_',$query));	
		$categories =$db->loadObjectList();
		
		
		$category_select='<select id="filter_category_id" name="filter[category_id]" onchange="this.form.submit();" >';
		$category_select.='<option value="">'.JText::_('JOPTION_SELECT_CATEGORY').'</option>';
			if(count($categories)>0){
				foreach($categories as $category){
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