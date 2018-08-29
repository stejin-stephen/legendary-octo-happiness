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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_tools', JPATH_SITE);


$user    = JFactory::getUser();
$canEdit = ToolsHelpersTools::canUserEdit($this->item, $user);


?>

<div class="item-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
			<?php throw new Exception(JText::_('COM_TOOLS_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo JText::sprintf('COM_TOOLS_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo JText::_('COM_TOOLS_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-item"
			  action="<?php echo JRoute::_('index.php?option=com_tools&task=item.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

	<?php echo $this->form->renderField('catid'); ?>

	<?php echo $this->form->renderField('title'); ?>

	<?php echo $this->form->renderField('alias'); ?>

	<?php echo $this->form->renderField('description'); ?>

	<input type="hidden" name="jform[image]" value="<?php echo $this->item->image; ?>" />

	<?php echo $this->form->renderField('document'); ?>

				<?php if (!empty($this->item->document)) : ?>
					<?php $documentFiles = array(); ?>
					<?php foreach ((array)$this->item->document as $fileSingle) : ?>
						<?php if (!is_array($fileSingle)) : ?>
							<a href="<?php echo JRoute::_(JUri::root() . 'downloads/tools' . DIRECTORY_SEPARATOR . $fileSingle, false);?>"><?php echo $fileSingle; ?></a> | 
							<?php $documentFiles[] = $fileSingle; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<input type="hidden" name="jform[document_hidden]" id="jform_document_hidden" value="<?php echo implode(',', $documentFiles); ?>" />
	<?php echo $this->form->renderField('type'); ?>

	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

	<?php echo $this->form->renderField('access'); ?>

	<?php echo $this->form->renderField('language'); ?>

	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
	<?php echo $this->form->renderField('created'); ?>

				<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo $this->form->renderField('modified'); ?>
				<div class="fltlft" <?php if (!JFactory::getUser()->authorise('core.admin','tools')): ?> style="display:none;" <?php endif; ?> >
                <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
                <?php echo JHtml::_('sliders.panel', JText::_('ACL Configuration'), 'access-rules'); ?>
                <fieldset class="panelform">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </fieldset>
                <?php echo JHtml::_('sliders.end'); ?>
            </div>
				<?php if (!JFactory::getUser()->authorise('core.admin','tools')): ?>
                <script type="text/javascript">
                    jQuery.noConflict();
                    jQuery('.tab-pane select').each(function(){
                       var option_selected = jQuery(this).find(':selected');
                       var input = document.createElement("input");
                       input.setAttribute("type", "hidden");
                       input.setAttribute("name", jQuery(this).attr('name'));
                       input.setAttribute("value", option_selected.val());
                       document.getElementById("form-item").appendChild(input);
                    });
                </script>
             <?php endif; ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<?php echo JText::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn"
					   href="<?php echo JRoute::_('index.php?option=com_tools&task=itemform.cancel'); ?>"
					   title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_tools"/>
			<input type="hidden" name="task"
				   value="itemform.save"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>
