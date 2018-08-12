<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_Tools
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$editor		=& JFactory::getEditor();
$item = $this->settings[0];
?>
<style>
.com_governance .editor #description_ifr {
    height:auto!important;
    min-height:300px !important;
}
.com_governance .editor #description {
    width:100% !important;
    min-height:400px !important;
}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_tools&view=settings'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>

	<div id="j-sidebar-container" class="span12 horizontal-navigation-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span9">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<?php /*if (empty($this->settings)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else :*/ ?>
		<div class="">

			<table class="table table-striped component-settings" id="ItemList">
				<tbody>
					<tr class="row1" sortable-group-id="<?php echo $item->catid?>">
						 <td colspan="2" style="text-align: left" class="order nowrap center hidden-phone">
							<div class="control-label"><label><?php echo JText::_("Intro Text");?></label></div>
							<?php echo $editor->display( 'introtext',  $item->introtext , '200', '20', '30', '15' ,array('pagebreak','readmore','jcommentson', 'image', 'menu', 'article','video','module','contact', 'attachments')) ;?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php //endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $item->id?>" />
		<input type="hidden" name="filter_language" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

	<div class="span3">
		<?php
			// Search tools bar

		?>
	</div>
</form>
