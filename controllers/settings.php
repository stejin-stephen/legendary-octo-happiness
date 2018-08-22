<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_Tools
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Tools list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_Tools
 * @since       1.6
 */
class ToolsControllerSettings extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since   1.6
	 */
	public function getModel($name = 'Settings', $prefix = 'ToolsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Save Tools settings
	 *
	 */
	public function editSettings()
	{
		$db =& JFactory::getDBO();

		//print_r($_REQUEST);exit;

		$id 		= JRequest::getVar('id', 0);
		$title 		= JRequest::getVar("title");
		$language 	= JRequest::getVar("filter");
		$lang_code	= $language['language'];

		$langsession = 	JFactory::getSession()->get('registry');
		$lang 		 = 	$langsession->get('application.lang');




		$description		= str_replace("'","\'", JRequest::getVar('introtext', '', 'post', 'string', JREQUEST_ALLOWRAW));

		if(empty($id)){
			$query = "INSERT INTO #__tools_settings (`id`, `title`, `introtext`) VALUES ( '',  {$db->quote($title)}, {$db->quote($description)})";

		}else{
			$query="UPDATE  #__tools_settings SET introtext = {$db->quote($description)}, `title` = {$db->quote($title)}".
			" WHERE id={$id}";
		}

		$db->setQuery( $query );


		if ($db->query()) {
			$msg = JText::_( 'Settings are saved' );
		}
		else {
			$msg = JText::_( 'Error. Unable to save data.' );
		}


		$link = 'index.php?option=com_tools&view=settings';
		$this->setRedirect($link, $msg);
	}

	/**
	 * Method to provide child classes the opportunity to process after the delete task.
	 *
	 * @param   JModelLegacy   $model   The model for the component
	 * @param   mixed          $ids     array of ids deleted.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}

}
