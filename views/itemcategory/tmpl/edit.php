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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	js('input:hidden.catid').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('catidhidden')){
			js('#jform_catid option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_catid").trigger("liszt:updated");
	});

	Joomla.submitbutton = function (task) {
		if (task == 'itemcategory.cancel') {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			
			if (task != 'itemcategory.cancel' && document.formvalidator.isValid(document.id('item-form'))) {
				
				Joomla.submitform(task, document.getElementById('item-form'));
			}
			else {
				//alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_tools&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="item-form" class="form-validate">

<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div class="form-horizontal">

		<div class="row-fluid">
			<div class="span9 main-container">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<?php echo $this->form->renderField('catid'); ?>

			<?php
				foreach((array)$this->item->catid as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="catid" name="jform[catidhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>
			<div class="form-vertical">	
				<?php echo $this->form->renderField('description'); ?>
			</div>
			<div id="bottomDiv">
            <div id="image-info">
                    <?php echo $this->form->renderFieldset('image-info');  ?>
            </div>
			
			<?php echo $this->form->renderField('password');  ?>

			<div class="form-vertical">	
			<?php echo $this->form->renderField('emails');  ?>
</div>
			</div>
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />

				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />


					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
			
			<div class="span3 main-filter">
			<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
<script>
	jQuery(document).ready(function ($){
		jQuery("#jform_parent_id").trigger('change');
	});
	jQuery("#jform_parent_id").change(function() {
		if(this.value != 0) 
		jQuery("#bottomDiv").hide();
		else
		jQuery("#bottomDiv").show();
	});
	</script>