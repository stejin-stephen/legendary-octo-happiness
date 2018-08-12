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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_tools'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Tools', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('ToolsHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'tools.php');

$controller = JControllerLegacy::getInstance('Tools');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
