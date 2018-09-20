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

	public function userLogin()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		$session = JFactory::getSession();
		$email = JRequest::getVar('email');
		$tool = JRequest::getVar('tool_id');
		$paswd = JRequest::getVar('password');
		
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__tools_categories'))
			->where('id = ' . $tool);
		$db->setQuery($query);
		$row = $db->loadAssoc();
		
		$flag = false;
		
		if(!empty($row['emails']) &&
		   preg_match('/\b'.$email.'\b/',$row['emails']) === 1 &&
		   $paswd === $row['password']) {
			//echo 'mail & password';
			$flag = true;
		} else if(!empty($row['emails']) && $paswd === $row['password']) {
			//echo 'email another, password';
			$flag = false;
		} else if($paswd === $row['password']) {
			//echo 'only password';
			$flag = true;
		}
		
		if($flag) {
			$allowed_ids = $session->get('allowed_ids') ? $session->get('allowed_ids') : $tool;
			$session->set('logged_in', $email);
			$session->set('allowed_ids', $allowed_ids);
			echo $tool;
		}
		exit;
	}

	public function saveLog()
	{
		$session = JFactory::getSession();
		$tool = JRequest::getVar('toolId');
		$logged_in = $session->get('logged_in');
		$allowed_ids = $session->get('allowed_ids');

		if($logged_in) {
		//if($logged_in && in_array($tool, $allowed_ids)) {
		$db = JFactory::getDbo();
		//ordering
		$db->setQuery("SELECT max(ordering) as max_order FROM #__tools_log");
		$item = $db->loadObject();
		$max_ordering = $item->max_order;
		//ordering

		$obj = new stdClass();
		$obj->tool_catid = $tool;
		$obj->email = $logged_in;
		$obj->ordering = $max_ordering+1;
		$obj->state = 1;
		$obj->created = date("Y-m-d H:i:s");
		$obj->modified = date("Y-m-d H:i:s");

		$db->insertObject('#__tools_log', $obj);

		echo JRoute::_('index.php?option=com_tools&view=itemcategory&id='.$tool);
		}
		exit;
	}
}
