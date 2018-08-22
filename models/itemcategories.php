<?php

/**
 * @version    1.0
 * @package    Com_Tools
 * @author      <https://development.karakas.be/issues/5184>
 * @copyright  2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Tools records.
 *
 * @since  1.6
 */
class ToolsModelItemCategories extends JModelList
{


/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				//'catid', 'a.`catid`',
				'title', 'a.`title`',
				'alias', 'a.`alias`',
				'description', 'a.`description`',
				'image', 'a.`image`',
				'document', 'a.`document`',
				'emails', 'a.`emails`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'access', 'a.`access`',
				'language', 'a.`language`',
				'created_by', 'a.`created_by`',
				'created', 'a.`created`',
				'modified_by', 'a.`modified_by`',
				'modified', 'a.`modified`',
			);
		}

		parent::__construct($config);
	}





	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		// Filtering type
		//$this->setState('filter.type', $app->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_tools');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.language');
		$id .= ':' . $this->getState('filter.created');


                    return parent::getStoreId($id);

	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__tools_categories` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the access level field 'access'
		$query->select('`access`.title AS `access`');
		$query->join('LEFT', '#__viewlevels AS access ON `access`.id = a.`access`');

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');

		// Join over the language
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the categories.
		//$query->select('c.title AS category_title')
		//	->join('LEFT', '#__categories AS c ON c.id = a.catid');

		//only fetch parent categories
		$query->where('a.parent_id = 0');

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}


		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by category.
		//$categoryId = $this->getState('filter.category_id');
		//if (is_numeric($categoryId))
		//{
		//	$query->where('a.catid = ' . (int) $categoryId);
		//}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.title LIKE ' . $search . '  OR  a.description LIKE ' . $search . ' )');
			}
		}


		// Filtering type
		//$filter_type = $this->state->get("filter.type");
		//
		//if ($filter_type !== null && (is_numeric($filter_type) || !empty($filter_type)))
		//{
		//	$query->where("a.`type` = '".$db->escape($filter_type)."'");
		//}

		if(JRequest::getVar('tmpl')!='component'){
			// Filter on the language.
			$langsession 	= 	JFactory::getSession()->get('registry');
			$lang 		= 	$langsession->get('application.lang');

			if($lang){
				$query->where('a.language = "' . $lang .'" ');
			}else if ($language = $this->getState('filter.language')){
				$query->where('a.language = ' . $db->quote($language));
			}

		}else{
			$query->where('a.language = "' . JRequest::getVar('forcedLanguage') .'" ');
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "ASC");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $oneItem)
		{
			$oneItem->subItems = self::getSubItems($oneItem->id);;
		}

		return $items;
	}

	public function getSubItems($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('a.*');
		$query->from('#__tools_categories as a');
		$query->where("parent_id = ".$id);

    // Add the list ordering clause.
    $orderCol  = $this->state->get('list.ordering', "a.id");
    $orderDirn = $this->state->get('list.direction', "ASC");

    if ($orderCol && $orderDirn)
    {
      $query->order($db->escape($orderCol . ' ' . $orderDirn));
    }

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results
		$results = $db->loadObjectList();

		return $results;
	}
}
