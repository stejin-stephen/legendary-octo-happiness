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

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

//use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * Tools model.
 *
 * @since  1.6
 */
class ToolsModelItemForm extends JModelForm
{
    private $item = null;

    

    

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return void
     *
     * @since  1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('com_tools');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit')
        {
                $id = JFactory::getApplication()->getUserState('com_tools.edit.item.id');
        }
        else
        {
                $id = JFactory::getApplication()->input->get('id');
                JFactory::getApplication()->setUserState('com_tools.edit.item.id', $id);
        }

        $this->setState('item.id', $id);

        // Load the parameters.
        $params       = $app->getParams();
        $params_array = $params->toArray();

        if (isset($params_array['item_id']))
        {
                $this->setState('item.id', $params_array['item_id']);
        }

        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param   integer $id The id of the object to get.
     *
     * @return Object|boolean Object on success, false on failure.
     *
     * @throws Exception
     */
    public function getItem($id = null)
    {
        if ($this->item === null)
        {
            $this->item = false;

            if (empty($id))
            {
                    $id = $this->getState('item.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            if ($table !== false && $table->load($id))
            {
                $user = JFactory::getUser();
                $id   = $table->id;
                

                
				if ($id)
				{
					$canEdit = $user->authorise('core.edit', 'com_tools.item.' . $id) || $user->authorise('core.create', 'com_tools.item.' . $id);
				}
				else
				{
					$canEdit = $user->authorise('core.edit', 'com_tools') || $user->authorise('core.create', 'com_tools');
				}

                if (!$canEdit && $user->authorise('core.edit.own', 'com_tools.item.' . $id))
                {
                        $canEdit = $user->id == $table->created_by;
                }

                if (!$canEdit)
                {
                        throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
                }

                // Check published state.
                if ($published = $this->getState('filter.published'))
                {
                        if (isset($table->state) && $table->state != $published)
                        {
                                return $this->item;
                        }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->item = ArrayHelper::toObject($properties, 'JObject');
                
		if (is_object($this->item->catid))
		{
			$this->item->catid = ArrayHelper::fromObject($this->item->catid);
		}

                
            }
        }

        return $this->item;
    }

    /**
     * Method to get the table
     *
     * @param   string $type   Name of the JTable class
     * @param   string $prefix Optional prefix for the table class name
     * @param   array  $config Optional configuration array for JTable object
     *
     * @return  JTable|boolean JTable if found, boolean false on failure
     */
    public function getTable($type = 'Item', $prefix = 'ToolsTable', $config = array())
    {
        $this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_tools/tables');

        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Get an item by alias
     *
     * @param   string $alias Alias string
     *
     * @return int Element id
     */
    public function getItemIdByAlias($alias)
    {
        $table      = $this->getTable();
        $properties = $table->getProperties();

        if (!in_array('alias', $properties))
        {
                return null;
        }

        $table->load(array('alias' => $alias));


        
            return $table->id;
        
    }

    /**
     * Method to check in an item.
     *
     * @param   integer $id The id of the row to check out.
     *
     * @return  boolean True on success, false on failure.
     *
     * @since    1.6
     */
    public function checkin($id = null)
    {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('item.id');
        
        if ($id)
        {
            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin'))
            {
                if (!$table->checkin($id))
                {
                    return false;
                }
            }
        }

        return true;
        
    }

    /**
     * Method to check out an item for editing.
     *
     * @param   integer $id The id of the row to check out.
     *
     * @return  boolean True on success, false on failure.
     *
     * @since    1.6
     */
    public function checkout($id = null)
    {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('item.id');
        
        if ($id)
        {
            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout'))
            {
                if (!$table->checkout($user->get('id'), $id))
                {
                    return false;
                }
            }
        }

        return true;
        
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm    A JForm object on success, false on failure
     *
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_tools.item', 'itemform', array(
                        'control'   => 'jform',
                        'load_data' => $loadData
                )
        );

        if (empty($form))
        {
                return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     *
     * @since    1.6
     */
    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState('com_tools.edit.item.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }
        
		// Support for multiple or not foreign key field: type
		$array = array();

		foreach ((array) $data->type as $value)
		{
			if (!is_array($value))
			{
				$array[] = $value;
			}
		}

		$data->type = $array;

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param   array $data The form data
     *
     * @return bool
     *
     * @throws Exception
     * @since 1.6
     */
    public function save($data)
    {
        $id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('item.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user  = JFactory::getUser();

        
        if ($id)
        {
            // Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'com_tools.item.' . $id) || $authorised = $user->authorise('core.edit.own', 'com_tools.item.' . $id);
        }
        else
        {
            // Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_tools');
        }

        if ($authorised !== true)
        {
            throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $table = $this->getTable();

        if ($table->save($data) === true)
        {
            return $table->id;
        }
        else
        {
            return false;
        }
        
    }

    /**
     * Method to delete data
     *
     * @param   int $pk Item primary key
     *
     * @return  int  The id of the deleted item
     *
     * @throws Exception
     *
     * @since 1.6
     */
    public function delete($pk)
    {
        $user = JFactory::getUser();

        
            if (empty($pk))
            {
                    $pk = (int) $this->getState('item.id');
            }

            if ($pk == 0 || $this->getItem($pk) == null)
            {
                    throw new Exception(JText::_('COM_TOOLS_ITEM_DOESNT_EXIST'), 404);
            }

            if ($user->authorise('core.delete', 'com_tools.item.' . $id) !== true)
            {
                    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
            }

            $table = $this->getTable();

            if ($table->delete($pk) !== true)
            {
                    throw new Exception(JText::_('JERROR_FAILED'), 501);
            }

            return $pk;
        
    }

    /**
     * Check if data can be saved
     *
     * @return bool
     */
    public function getCanSave()
    {
        $table = $this->getTable();

        return $table !== false;
    }
    public function getAliasFieldNameByView($view)
	{
		switch ($view)
		{
			case 'item':
			case 'itemform':
				return 'alias';
			break;
		}
	}
}
