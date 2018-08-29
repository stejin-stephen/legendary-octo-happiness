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
		$paswd = $db->quote(JRequest::getVar('password'));

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('alias')->from($db->quoteName('#__tools_categories'))
			  ->where('password = ' . $paswd)
			  ->where('id = ' . $tool);
		
		if($email) {
			$query->where('emails LIKE ' . $db->quote('%' . $db->escape($email, true) . '%'));
		}
		
		$db->setQuery($query);
		$row = $db->loadRow();

		if($row) {
			//$allowed_ids = $session->get('allowed_ids') ? $session->get('allowed_ids') : array();
			$allowed_ids = $session->get('allowed_ids') ? $session->get('allowed_ids') : $tool;
			$session->set('logged_in', $email);
			//array_push($allowed_ids, $tool);
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
