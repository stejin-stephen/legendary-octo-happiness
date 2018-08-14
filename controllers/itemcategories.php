<?php
/**
 * @version    1.0
 * @package    Com_Tools
 * @author      <https://development.karakas.be/issues/5184>
 * @copyright  2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Items list controller class.
 *
 * @since  1.6
 */
class ToolsControllerItemCategories extends ToolsController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'ItemCategories', $prefix = 'ToolsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	public function saveLog()
	{
		$session = JFactory::getSession();
		$tool = JRequest::getVar('toolId');
		$logged_in = $session->get('logged_in');
		
		if(!$loggedIn) echo NULL; exit;
		
		//ordering
		$db->setQuery("SELECT max(ordering) as max_order FROM #__dmo3c_tools_log");
		$item = $db->loadObject();
		$max_ordering = $item->max_order;
		//ordering
		
		$obj = new stdClass();
		$obj->tool_catid = $tool;
		$obj->email = $logged_in;
		$obj->ordering = $max_ordering+1;
		$obj->state = 1;
		$obj->created = time();
		$obj->modified = time();
		
		$db->insertObject('#__tools_log', $obj);
		
		echo Route::_('index.php?option=com_tools&view=itemcategory&id='.$id);
		exit;
	}
}
