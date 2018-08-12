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
		$item_id	= JRequest::getVar('id',0);

		//To get selected country item
		$query 		= $db->getQuery(true);
		$query->select('a.tool_catid')
			->from('#__tools a')
			->where('a.id='.$item_id);
		$db->setQuery($query);

		$result =$db->loadObject();

		//To get selected category list
		$query 		= $db->getQuery(true);
		$query->select('c.id, c.title')
			->from('#__tools_categories c')
			->order('id')
			->where('parent_id=0');

		$db->setQuery($query);

		$categories =$db->loadObjectList();


		$category_select='<select id="'.$this->id.'" name="'.$this->name.'" class="custom-select hasCustomSelect" >';
$category_select.='<option value="">- Select -</option>';
			if(count($categories)>0){
				foreach($categories as $category){
					if($category->id==$result->tool_catid){
						$category_select.='<option value="'.$category->id.'" selected="selected" >'.$category->title.'</option>';
					}else{
						$category_select.='<option value="'.$category->id.'">'.$category->title.'</option>';
              $subcat = self::getSubItems($category->id);
              foreach ($subcat as $itemcat) {
        					if($itemcat->id==$result->tool_catid){
        						$category_select.='<option value="'.$itemcat->id.'" selected="selected" >&emsp;'.$itemcat->title.'</option>';
                  } else {
                $category_select.='<option value="'.$itemcat->id.'">&emsp;'.$itemcat->title.'</option>';
                  }
              }
					}
				}
			}
		$category_select.='</select>';

		return $category_select;
	}

  public function getSubItems($id)
  {
  		//get database
  		$db 		= JFactory::getDbo();

  		$query 		= $db->getQuery(true);
  		$query->select('c.id, c.title')
  			->from('#__tools_categories c')
  			->order('id')
  			->where('parent_id = '.$id);

  		$db->setQuery($query);

      return $subcat =$db->loadObjectList();
  }
}
?>
