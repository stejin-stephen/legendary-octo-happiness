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
		if (task == 'item.cancel') {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			
			if (task != 'item.cancel' && document.formvalidator.isValid(document.id('item-form'))) {
				
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
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TOOLS_TITLE_ITEM', true)); ?>
		<div class="row-fluid">
			<div class="span9 main-container">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<?php //echo $this->form->renderField('catid'); ?>

			<?php
				foreach((array)$this->item->catid as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="catid" name="jform[catidhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>
				<?php echo $this->form->renderField('type'); ?>
			<!--<div id="url" style="<?php echo $this->item->type == 2 ? '' : 'display: none' ; ?>">
				<?php echo $this->form->renderField('url'); ?>
			</div>-->
			
			<!--<div id="docAttach" class="control-group" style="<?php echo $this->item->type == 3 ? '' : 'display: none' ; ?>">
				<div class="control-label">
					<label>Add File</label>
				</div>
				<div class="controls">
					<a class="modal btn" title="Upload" href="index.php?option=com_attachments&amp;view=documents&amp;tmpl=component&amp;directory=tools" rel="{handler: 'iframe', size: {x: 800, y: 600}}">
						Upload
					</a>
				</div>
			</div>-->
			
			<!--<?php echo $this->form->renderField('document'); ?>

				<?php if (!empty($this->item->document)) : ?>
					<?php $documentFiles = array(); ?>
					<?php foreach ((array)$this->item->document as $fileSingle) : ?>
						<?php if (!is_array($fileSingle)) : ?>
							<a href="<?php echo JRoute::_(JUri::root() . 'downloads/tools' . DIRECTORY_SEPARATOR . $fileSingle, false);?>"><?php echo $fileSingle; ?></a> | 
							<?php $documentFiles[] = $fileSingle; ?>
						<?php endif; ?>
					<?php endforeach; ?>
					<input type="hidden" name="jform[document_hidden]" id="jform_document_hidden" value="<?php echo implode(',', $documentFiles); ?>" />
				<?php endif; ?>				-->
			
            <div id="image-info">
                    <?php echo $this->form->renderFieldset('image-info');  ?>
            </div>
			<div class="form-vertical">	
				<?php echo $this->form->renderField('description'); ?>
			</div>
				<input type="hidden" name="jform[image]" value="<?php echo $this->item->image; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<?php //echo $this->form->renderField('access'); ?>
				<?php //echo $this->form->renderField('language'); ?>
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php //echo $this->form->renderField('created_by'); echo $this->form->renderField('created'); ?>

				<?php //echo $this->form->renderField('modified_by'); echo $this->form->renderField('modified'); ?>


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
				<div class="form-vertical">
					<div class="control-group">
					<?php echo $this->form->getControlGroup('tool_catid'); ?>
					<?php echo $this->form->getControlGroup('created'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php  echo JHtml::_('bootstrap.addTab', 'myTab', JText::_('Downloads'), 'Downloads'); ?>
			<div class="row-fluid form-horizontal-desktop">
				<div class="span9">
					<?php echo $this->loadTemplate('attachments'); ?>
				</div>

			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>	

		<?php /*if (JFactory::getUser()->authorise('core.admin','tools')) :
		echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true));
		echo $this->form->getInput('rules');
		echo JHtml::_('bootstrap.endTab');
		endif;*/ ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form><script>
	jQuery(document).ready(function ($){	
		$("#jform_catid_chzn").parent().parent().hide(); // Hide category
		jQuery("#jform_type").trigger('change');
		<?php
		echo ($this->item->type == 2) ?
		'jQuery("#jform_imageinfo_url").parent().parent().show();' :
		'jQuery("#jform_imageinfo_url").parent().parent().hide();';
		?>
	});
	
	jQuery("#jform_type").change(function() {
		if(this.value == 2) {
			jQuery("[href='#Downloads']").hide();
			jQuery("#jform_imageinfo_url").prop('required',true);
			jQuery("#jform_imageinfo_url").parent().parent().show();
		} else if(this.value == 3) {
			jQuery("[href='#Downloads']").show();
			jQuery("#jform_imageinfo_url").val('');
			jQuery("#jform_imageinfo_url").prop('required', false);
			jQuery("#jform_imageinfo_url").parent().parent().hide();
		} else {
			jQuery("[href='#Downloads']").hide();
			jQuery("#jform_imageinfo_url").val('');
			jQuery("#jform_imageinfo_url").prop('required', false);
			jQuery("#jform_imageinfo_url").parent().parent().hide();
		}
	});
	
</script>
