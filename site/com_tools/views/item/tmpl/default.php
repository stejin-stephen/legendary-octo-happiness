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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_tools.' . $this->item->id);

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_tools' . $this->item->id))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_CATID'); ?></th>
			<td><?php echo $this->item->catid; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_TITLE'); ?></th>
			<td><?php echo $this->item->title; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_ALIAS'); ?></th>
			<td><?php echo $this->item->alias; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_DESCRIPTION'); ?></th>
			<td><?php echo nl2br($this->item->description); ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_DOCUMENT'); ?></th>
			<td>
			<?php
			foreach ((array) $this->item->document as $singleFile) : 
				if (!is_array($singleFile)) : 
					$uploadPath = 'downloads/tools' . DIRECTORY_SEPARATOR . $singleFile;
					 echo '<a href="' . JRoute::_(JUri::root() . $uploadPath, false) . '" target="_blank">' . $singleFile . '</a> ';
				endif;
			endforeach;
		?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_TYPE'); ?></th>
			<td><?php echo $this->item->type; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_ACCESS'); ?></th>
			<td><?php echo $this->item->access; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_LANGUAGE'); ?></th>
			<td><?php echo $this->item->language; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_CREATED'); ?></th>
			<td><?php echo $this->item->created; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_TOOLS_FORM_LBL_ITEM_MODIFIED'); ?></th>
			<td><?php echo $this->item->modified; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_tools&task=item.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_TOOLS_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_tools.item.'.$this->item->id)) : ?>

	<a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
		<?php echo JText::_("COM_TOOLS_DELETE_ITEM"); ?>
	</a>

	<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo JText::_('COM_TOOLS_DELETE_ITEM'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::sprintf('COM_TOOLS_DELETE_CONFIRM', $this->item->id); ?></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal">Close</button>
			<a href="<?php echo JRoute::_('index.php?option=com_tools&task=item.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_TOOLS_DELETE_ITEM'); ?>
			</a>
		</div>
	</div>

<?php endif; ?>