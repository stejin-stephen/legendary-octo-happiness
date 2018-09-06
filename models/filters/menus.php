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
 
class JFormFieldMenus extends JFormField {
 
	protected $type = 'Menus';
 
	public function getInput() {
		//get database
		$db 		= JFactory::getDbo();
		//$item_id 	= isset($_REQUEST['filter']['menus']) ? $_REQUEST['filter']['menus'] : '';
		$item_id 	= $this->form->getData()->get('filter',array())->menus;
		$langsession 	= JFactory::getSession()->get('registry');
		
		$query 		= $db->getQuery(true);

		$query->select('c.id, c.title')
			->from('#__tools_categories c')
			->order('id')
			->where('parent_id=0')
			->where('state=1');

		$db->setQuery($query);
		//echo nl2br(str_replace('#__','dmo3c_',$query));	
		$categories =$db->loadObjectList();
		
		
		$menu_select='<select id="filter_menus" name="filter[menus]" onchange="this.form.submit();" >';
		$menu_select.='<option value="">'.JText::_('- select category -').'</option>';
			if(count($categories)>0){
				foreach($categories as $category){
					
					$subcat = self::getSubItems($category->id);
					$disable = $subcat ? 'disabled="disabled"' : '';
					if($category->id==$item_id){
						$menu_select.='<option value="'.$category->id.'" selected="selected" >'.$category->title.'</option>';
					}else{
						$menu_select.='<option value="'.$category->id.'" '.$disable.'>'.$category->title.'</option>';
              foreach ($subcat as $itemcat) {
        					if($itemcat->id==$item_id){
        						$menu_select.='<option value="'.$itemcat->id.'" selected="selected" >&emsp;'.$itemcat->title.'</option>';
                  } else {
                $menu_select.='<option value="'.$itemcat->id.'">&emsp;'.$itemcat->title.'</option>';
                  }
              }
					}
				}
			}
		$menu_select.='</select>';
		
		return $menu_select;
	}
	
	  public function getSubItems($id)
  {
  		//get database
  		$db 		= JFactory::getDbo();

  		$query 		= $db->getQuery(true);
  		$query->select('c.id, c.title')
  			->from('#__tools_categories c')
  			->order('id')
  			->where('parent_id = '.$id)
			->where('state=1');

  		$db->setQuery($query);

      return $subcat =$db->loadObjectList();
  }
}
?>