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
		
		// get logged in item id from session
		$session = JFactory::getSession();
		$allowed_ids = $session->get('allowed_ids');

		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		
		if($this->item->id != $allowed_ids) {
			$app->enqueueMessage('Please login.', 'error');
			$app->redirect(JRoute::_('index.php?Itemid=217')); // redirect to menu item id
		}
		
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
			->where('a.state = 1')
			->order('a.ordering');

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
			->where('a.state = 1')
				->order('a.ordering');

			$db->setQuery($query);

			$result = $db->loadObjectList();

			foreach ($result as $item) {
				$item->cat = json_decode($item->image);
				//$item->introtext = truncateHelper::truncate($item->description,100,array('html' => true,'exact' => false, 'ending' => '...'));
				$item->introtext = $item->description;
				$item->showtext = $item->type == 2 ? 'View' : 'Download';
				
				if($item->type == 3):
					$item->link = self::getDownload($item);
				elseif($item->type == 2):
					$item->link = $item->cat->url;
				else:
					$item->link = $item->cat->image;
				endif;
			}
			//var_dump($result); exit;
			return $result;
	}

	public function getDownload($item)
	{
		$db	= JFactory::getDbo();

		$id 		= $item->id; //Item id
		$catid 		= $item->catid; // Category id
		$component 	= 'tools'; // Component directory name

		$query ="SELECT a.*, ad.id AS docid, ad.name, ad.title, ad.description, ad.extension, ad.size, ad.ordering, ad.language FROM #__attachments a, #__attachment_documents ad ".
"WHERE ad.component = '{$component}' AND a.iid = {$id} AND a.cid = {$catid} AND a.did = ad.id ORDER BY ad.ordering ASC";

$db->setQuery($query);
$attachments = $db->loadObjectList();

		if (count($attachments)){
			foreach ($attachments as $attachment){
			 $downloadURL = JRoute::_("index.php?option=com_attachments&view=attachments&id=" . $attachment->did);
			 				$documentTitle = (!empty($attachment->title)) ? $attachment->title : $attachment->name;
				
				$downloads_title = $documentTitle;
				if($attachment->did) {
				$downloads[] = "<p><a class='pdf' href='{$downloadURL}' >{$downloads_title}</a></p>"; }
			}
		}//var_dump($downloads);

return $downloads;
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
