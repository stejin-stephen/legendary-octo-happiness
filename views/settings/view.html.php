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
 * View class for a list of Tools.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_Tools
 * @since       1.5
 */
class ToolsViewSettings extends JViewLegacy
{
	protected $settings;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		//$this->state		= $this->get('State');
		$this->settings		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->language		=SELF::getLanguagelist();


		ToolsHelper::addSubmenu('settings');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/tools.php';

		$state	= $this->get('State');
		//$canDo	= JHelperContent::getActions($state->get('filter.category_id'), 0, 'com_Tools');
		$user	= JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('Tools Settings'), 'link Tools');

		JToolbarHelper::custom('settings.editSettings','apply.png','apply.png',JText::_('JTOOLBAR_APPLY'),false);
		JToolbarHelper::cancel('item.cancel');
		//JToolbarHelper::help('JHELP_COMPONENTS_TEAMLETTER_TEAMLETTER');

		JHtmlSidebar::setAction('index.php?option=com_tools&view=settings');


	}

	public function getLanguagelist(){

		$db = JFactory::getDbo();
					$query = $db->getQuery(true)
						->select('*')
						->from('#__languages')
						->where('published=1')
						->order('ordering ASC');
					$db->setQuery($query);

		$languages= $db->loadObjectList();
		return $languages;
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.hits' => JText::_('JGLOBAL_HITS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
