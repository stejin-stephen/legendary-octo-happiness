<?php
/**
 * @version    1.0
 * @package    Com_Tools
 * @author      <>
 * @copyright  2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Items list controller class.
 *
 * @since  1.6
 */
class ToolsControllerLogs extends JControllerAdmin
{
	/**
	 * Method to clone existing Items
	 *
	 * @return void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		Jsession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('COM_TOOLS_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Jtext::_('COM_TOOLS_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_tools&view=logs');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'log', $prefix = 'ToolsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	
	public function Export2Excel()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.email', 'b.title as category', 'a.created'));
		$query->from($db->quoteName('#__tools_log', 'a'));
		$query->join('INNER', $db->quoteName('#__tools_categories', 'b') .
				' ON (' . $db->quoteName('a.tool_catid') . ' = ' . $db->quoteName('b.id') . ')');
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		$letters = array("A", "B", "C", "D"); $i = 0;
		
		foreach($results[0] as $key => $row){
			$keys[] = $key;
		}
		$filename = 'Tools-Logs-'.date('Y_m_d_H_i_s').'.xlsx' ;
		
		//load PHPExcel library
		require_once(JPATH_ROOT. '/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$i].'1', "Sl. No.");
		
		foreach($keys as $k) {
			$i++;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$i].'1', ucfirst($keys[$i-1]));
		}
		$k = 0;
		//foreach($results as $row){
		//	
		//	for($j = 0;$j<count($keys);$j++) { $num = $k+2;
		//		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letters[$j+1].$num, $results[$k]->$keys[$j]);
		//	}
		//	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$num, $num-1);
		//	$k++;
		//}
		$num = 2;$k = 0;
		foreach($results as $row){
			
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$num, $num-1);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$num, $results[$k]->email);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$num, $results[$k]->category);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$num, $results[$k]->created);
			$num++;$k++;
		}
		
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header("Cache-Control: max-age=0");
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter->save("php://output");
		exit;
	}
}
