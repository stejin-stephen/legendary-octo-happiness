<?php

/**
 * @version    CVS: 1.0
 * @package    Com_Tools
 * @author      <>
 * @copyright  2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
/**
 * Content Component Category Tree
 *
 * @since  1.6
 */
class ToolsCategories extends JCategories
{
    /**
     * Class constructor
     *
     * @param   array  $options  Array of options
     *
     * @since   11.1
     */
    public function __construct($options = array())
    {
        $options['table'] = '#__tools';
        $options['extension'] = 'com_tools';

        parent::__construct($options);
    }
}
