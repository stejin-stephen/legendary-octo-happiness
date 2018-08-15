<?php

/**
 * @version    1.0
 * @package    Com_Tools
 * @author      <https://development.karakas.be/issues/5184>
 * @copyright  2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once(JPATH_ROOT.'/includes/truncatetext.php');

/**
 * View to edit
 *
 * @since  1.6
 */
class ToolsViewItemCategory extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		$this->params = $app->getParams('com_tools');

		$this->item->tools = self::getTools($this->item->id);
		$this->item->subitems = self::getSubItems($this->item->id);

		if (!empty($this->item))
		{
			$this->form = $this->get('Form');
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		if(!in_array($this->item->access, $user->getAuthorisedViewLevels())){
                return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            }


		if ($this->_layout == 'edit')
		{
			$authorised = $user->authorise('core.create', 'com_tools');

			if ($authorised !== true)
			{
				throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}public function getSubItems($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('a.*')
			->from($db->quoteName('#__tools_categories', 'a'))
			->where('a.parent_id = '.$id)
			->order('a.id DESC');

		$db->setQuery($query);

		$result = $db->loadObjectList();

		foreach ($result as $item) {
			$item->tools = self::getTools($item->id);
		}

		return $result;
	}

	public function getTools($id)
	{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query
				->select('a.*')
				->from($db->quoteName('#__tools', 'a'))
				->where('a.tool_catid = '.$id)
				->order('a.id DESC');

			$db->setQuery($query);

			$result = $db->loadObjectList();

			foreach ($result as $item) {
				$item->cat = json_decode($item->image);
				$item->introtext = truncateHelper::truncate($item->description,100,array('html' => true,'exact' => false, 'ending' => '...'));
				$item->showtext = $item->type == 3 ? 'Download' : 'View';
			}
			//var_dump($result); exit;
			return $result;
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// We need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_TOOLS_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
