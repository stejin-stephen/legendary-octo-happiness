<?php

/**
 * @version    1.0
 * @package    Com_Tools
 * @author      <>
 * @copyright  2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
/**
 * item Table class
 *
 * @since  1.6
 */
class ToolsTableLog extends JTable
{
	/**
	 * Check if a field is unique
	 *
	 * @param   string  $field  Name of the field
	 *
	 * @return bool True if unique
	 */
	private function isUnique ($field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName($field))
			->from($db->quoteName($this->_tbl))
			->where($db->quoteName($field) . ' = ' . $db->quote($this->$field))
			->where($db->quoteName('id') . ' <> ' . (int) $this->{$this->_tbl_key});

		$db->setQuery($query);
		$db->execute();

		return ($db->getNumRows() == 0) ? true : false;
	}

	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tools_log', 'id', $db);
		$this->_observers = new JObserverUpdater($this);
		JObserverMapper::attachAllObservers($this);
		JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'ToolsTableLog', array('typeAlias' => 'com_tools.log'));
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  null|string  null is operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable:bind
	 * @since   1.5
	 */
	public function bind($array, $ignore = '')
	{
	    $date = JFactory::getDate();
		$task = JFactory::getApplication()->input->get('task');
	    

		// Support for multiple field: catid
		if (isset($array['catid']))
		{
			if (is_array($array['catid']))
			{
				$array['catid'] = implode(',',$array['catid']);
			}
			elseif (strpos($array['catid'], ',') != false)
			{
				$array['catid'] = explode(',',$array['catid']);
			}
			elseif (strlen($array['catid']) == 0)
			{
				$array['catid'] = '';
			}
		}
		else
		{
			$array['catid'] = '';
		}

		// Support for alias field: alias
		if (empty($array['alias']))
		{
			if (empty($array['title']))
			{
				$array['alias'] = JFilterOutput::stringURLSafe(date('Y-m-d H:i:s'));
			}
			else
			{
				if(JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$array['alias'] = JFilterOutput::stringURLUnicodeSlug(trim($array['title']));
				}
				else
				{
					$array['alias'] = JFilterOutput::stringURLSafe(trim($array['title']));
				}
			}
		}

		// Support for multi file field: document
		//if (!empty($array['document']))
		//{
		//	if (is_array($array['document']))
		//	{
		//		$array['document'] = implode(',', $array['document']);
		//	}
		//	elseif (strpos($array['document'], ',') != false)
		//	{
		//		$array['document'] = explode(',', $array['document']);
		//	}
		//}
		//else
		//{
		//	$array['document'] = '';
		//}


		// Support for multiple field: type
		if (isset($array['type']))
		{
			if (is_array($array['type']))
			{
				$array['type'] = implode(',',$array['type']);
			}
			elseif (strpos($array['type'], ',') != false)
			{
				$array['type'] = explode(',',$array['type']);
			}
			elseif (strlen($array['type']) == 0)
			{
				$array['type'] = '';
			}
		}
		else
		{
			$array['type'] = '';
		}
		$input = JFactory::getApplication()->input;
		$task = $input->getString('task', '');

		if ($array['id'] == 0 && empty($array['created_by']))
		{
			$array['created_by'] = JFactory::getUser()->id;
		}

		if ($array['id'] == 0)
		{
			$array['created'] = $date->toSql();
		}

		if ($array['id'] == 0 && empty($array['modified_by']))
		{
			$array['modified_by'] = JFactory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified_by'] = JFactory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$array['modified'] = $date->toSql();
		}

		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['imageinfo']) && is_array($array['imageinfo']))
		{
			// Convert the imageinfo array to a string.
			$parameter = new JRegistry;
			$parameter->loadArray($array['imageinfo']);
			$array['image'] = (string)$parameter;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		if (!JFactory::getUser()->authorise('core.admin', 'com_tools.log.' . $array['id']))
		{
			$actions         = JAccess::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_tools/access.xml',
				"/access/section[@name='log']/"
			);
			$default_actions = JAccess::getAssetRules('com_tools.log.' . $array['id'])->getData();
			$array_jaccess   = array();

			foreach ($actions as $action)
			{
                if (key_exists($action->name, $default_actions))
                {
                    $array_jaccess[$action->name] = $default_actions[$action->name];
                }
			}

			$array['rules'] = $this->JAccessRulestoArray($array_jaccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overload the store method.
	 *
	 * @param   boolean	Toggle whether null values should be updated.
	 * @return  boolean  True on success, false on failure.
	 * @since   1.6
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		if ($this->id)
		{
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		}
		else
		{
			// New weblink. A weblink created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!(int) $this->created)
			{
				$this->created = $date->toSql();
			}
			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}

		// Set publish_up to null date if not set
		//if (!$this->publish_up)
		//{
		//	$this->publish_up = $this->_db->getNullDate();
		//}

		// Set publish_down to null date if not set
		//if (!$this->publish_down)
		//{
		//	$this->publish_down = $this->_db->getNullDate();
		//}

		// Verify that the alias is unique
		$table = JTable::getInstance('Log', 'ToolsTable');

		if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_NEWS_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		// Convert IDN urls to punycode
		$this->url = JStringPunycode::urlToPunycode($this->url);

		//return parent::store($updateNulls);
		if(parent::store($updateNulls)){
			if($this->id)
			{
				$component = 'tools';
				$dispatcher =& JDispatcher::getInstance();
				JPluginHelper::importPlugin('attachments');

				$dispatcher->trigger('savedocs', array($component, $this->id, $this->catid, $this->access));
			}

			return true;
		}else{
			return false;
		}

	}

	/**
	 * This function convert an array of JAccessRule objects into an rules array.
	 *
	 * @param   array  $jaccessrules  An array of JAccessRule objects.
	 *
	 * @return  array
	 */
	private function JAccessRulestoArray($jaccessrules)
	{
		$rules = array();

		foreach ($jaccessrules as $action => $jaccess)
		{
			$actions = array();

			if ($jaccess)
			{
				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool)$allow);
				}
			}

			$rules[$action] = $actions;
		}

		return $rules;
	}

	/**
	 * Overloaded check function
	 *
	 * @return bool
	 */
	public function check()
	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}
		
		// Check if title is unique
		if (!$this->isUnique('title'))
		{
			throw new Exception('Your <b>title</b> item "<b>' . $this->title . '</b>" already exists');
		}
		// Check if alias is unique
		if (!$this->isUnique('alias'))
		{
			$this->alias .= '-' . JFilterOutput::stringURLSafe(date('Y-m-d-H:i:s'));
		}
		
		// Support multi file field: document
		//$app = JFactory::getApplication();
		//$files = $app->input->files->get('jform', array(), 'raw');
		//$array = $app->input->get('jform', array(), 'ARRAY');
		//
		//if ($files['document'][0]['size'] > 0)
		//{
		//	// Deleting existing files
		//	$oldFiles = ToolsHelper::getFiles($this->id, $this->_tbl, 'document');
		//
		//	foreach ($oldFiles as $f)
		//	{
		//		$oldFile = JPATH_ROOT . '/downloads/tools/' . $f;
		//
		//		if (file_exists($oldFile) && !is_dir($oldFile))
		//		{
		//			unlink($oldFile);
		//		}
		//	}
		//
		//	$this->document = "";
		//
		//	foreach ($files['document'] as $singleFile )
		//	{
		//		jimport('joomla.filesystem.file');
		//
		//		// Check if the server found any error.
		//		$fileError = $singleFile['error'];
		//		$message = '';
		//
		//		if ($fileError > 0 && $fileError != 4)
		//		{
		//			switch ($fileError)
		//			{
		//				case 1:
		//					$message = JText::_('File size exceeds allowed by the server');
		//					break;
		//				case 2:
		//					$message = JText::_('File size exceeds allowed by the html form');
		//					break;
		//				case 3:
		//					$message = JText::_('Partial upload error');
		//					break;
		//			}
		//
		//			if ($message != '')
		//			{
		//				$app->enqueueMessage($message, 'warning');
		//
		//				return false;
		//			}
		//		}
		//		elseif ($fileError == 4)
		//		{
		//			if (isset($array['document']))
		//			{
		//				$this->document = $array['document'];
		//			}
		//		}
		//		else
		//		{
		//			// Check for filesize
		//			$fileSize = $singleFile['size'];
		//
		//			if ($fileSize > 1048576)
		//			{
		//				$app->enqueueMessage('File bigger than 1MB', 'warning');
		//
		//				return false;
		//			}
		//
		//			// Check for filetype
		//			$okMIMETypes = 'application/pdf';
		//			$validMIMEArray = explode(',', $okMIMETypes);
		//			$fileMime = $singleFile['type'];
		//
		//			if (!in_array($fileMime, $validMIMEArray))
		//			{
		//				$app->enqueueMessage('This filetype is not allowed', 'warning');
		//
		//				return false;
		//			}
		//
		//			// Replace any special characters in the filename
		//			jimport('joomla.filesystem.file');
		//			$filename = JFile::stripExt($singleFile['name']);
		//			$extension = JFile::getExt($singleFile['name']);
		//			$filename = preg_replace("/[^A-Za-z0-9]/i", "-", $filename);
		//			$filename = $filename . '.' . $extension;
		//			$uploadPath = JPATH_ROOT . '/downloads/tools/' . $filename;
		//			$fileTemp = $singleFile['tmp_name'];
		//
		//			if (!JFile::exists($uploadPath))
		//			{
		//				if (!JFile::upload($fileTemp, $uploadPath))
		//				{
		//					$app->enqueueMessage('Error moving file', 'warning');
		//
		//					return false;
		//				}
		//			}
		//
		//			$this->document .= (!empty($this->document)) ? "," : "";
		//			$this->document .= $filename;
		//		}
		//	}
		//}
		//else
		//{
		//	$this->document .= $array['document_hidden'];
		//}

		return parent::check();
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not
	 *                            set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return   boolean  True on success.
	 *
	 * @since    1.0.4
	 *
	 * @throws Exception
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				throw new Exception(500, JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE `' . $this->_tbl . '`' .
			' SET `state` = ' . (int) $state .
			' WHERE (' . $where . ')' .
			$checkin
		);
		$this->_db->execute();

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin each row.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		return true;
	}

	/**
	 * Define a namespaced asset name for inclusion in the #__assets table
	 *
	 * @return string The asset name
	 *
	 * @see JTable::_getAssetName
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_tools.log.' . (int) $this->$k;
	}

	/**
	 * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
	 *
	 * @param   JTable   $table  Table name
	 * @param   integer  $id     Id
	 *
	 * @see JTable::_getAssetParentId
	 *
	 * @return mixed The id on success, false on failure.
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// The item has the component as asset-parent
		$assetParent->loadByName('com_tools');

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}

	/**
	 * Delete a record by id
	 *
	 * @param   mixed  $pk  Primary key value to delete. Optional
	 *
	 * @return bool
	 */
	public function delete($pk = null)
	{
		$this->load($pk);
		$result = parent::delete($pk);
		
		if ($result)
		{
			jimport('joomla.filesystem.file');

			foreach ($this->document as $documentFile)
			{
				JFile::delete(JPATH_ROOT . '/downloads/tools/' . $documentFile);
			}
		}

		return $result;
	}
}
