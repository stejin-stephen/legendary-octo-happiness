<?php
	/**
	 * @package		Joomla.Administrator
	 * @subpackage	com_weblinks
	 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
	 * @license		GNU General Public License version 2 or later; see LICENSE.txt
	 */
	
	// No direct access.
	defined('_JEXEC') or die;
	
	$db	= JFactory::getDbo();

	$id 		= $this->item->id; //Item id
	$catid 		= $this->item->catid; // Category id
	
	if($id == ''){
		$id = 0;
	}
	
	if($catid == ''){
		$catid = 0;
	}
	
	$component 	= 'tools'; // Component directory name
	
	$query ="SELECT a.*, ad.id AS docid, ad.name, ad.title, ad.description, ad.extension, ad.size, ad.ordering, ad.language FROM #__attachments a, #__attachment_documents ad ".
			"WHERE ad.component = '{$component}' AND a.iid = {$id} AND a.cid = {$catid} AND a.did = ad.id ORDER BY ad.ordering ASC";
	$db->setQuery($query);
	$attachments = $db->loadObjectList();
	
	$document = JFactory::getDocument();
	//$document->addScript(JURI::root() . '/administrator/components/com_attachments/assets/scripts/jquery-1.7.2.min.js');

?>

	<script type="text/javascript">
		
		jQuery(document).ready(function(){
			
			
			jQuery('#saveDocItem').live('click', function(){
	
				var params = jQuery('.topost', jQuery(this).parent()).serializeArray();
				var currentSelection =jQuery(this).parent().parent();
				jQuery.post('index.php?option=com_attachments&task=upload.savedoc', params, function(data){		
					jQuery(currentSelection).attr('class','')			
					currentSelection.html(data);
				});
				downloadlimitCheck('save');
			jQuery( ".adminformlist").show();
				return false;
			});
			
			jQuery('input.saveDocItems').live('click', function(){
				
				var params = jQuery('#attachmentsContainer .topost').serializeArray();
				jQuery.post('index.php?option=com_attachments&task=upload.savedoc', params, function(data){
					//jQuery('#attachmentsContainer ul.adminformlist li').remove();
					jQuery('#attachmentsContainer ul.adminformlist').append(data);
					jQuery('.saveDocItems').hide();
				});
				
				return false;
			});
			
			jQuery('input.removeDocItem').live('click', function(){
				
				if(confirm('Are you sure you want to remove this item ?'))
				{
					jQuery(this).parent().parent().remove();
				}
				
				return false;
			});
			
			jQuery('a.editCover').live('click', function(){
				
				alert('edit cover');
				
				return false;
			});
			
			
			//Drag and Drop here ---------------------------->
			
			jQuery(function() {
				jQuery( ".drg_drp" ).sortable({
					items: "li:not(.ui-state-disabled)"
					//placeholder: "ui-state-highlight"
				});
				
			});
			
			jQuery(function() {
				jQuery("#attachmentsContainer ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
					var order = jQuery(this).sortable("serialize");
					jQuery.post("index.php?option=com_attachments&task=upload.updateorder", order, function(response){
						//respose
					});
					}
				});
			});	
					
			jQuery('.document_remove').live('click', function(){
	
				var dId = jQuery(this).attr('id');
				var selectedLi = jQuery(this).parent().parent();
				
				if(confirm('Are you sure you want to delete this document ?'))
				{
					jQuery.post('index.php?option=com_attachments&task=upload.deletedoc', { id : dId }, function(data){					
						selectedLi.remove();
						
						////Download limit
						var download_total=jQuery("#download_total").val();
						download_total--;
						jQuery("#download_total").val(download_total);
						////Download limit
					});	
				}
				downloadlimitCheck('remove');
				//jQuery( ".btn-info").show();
				return false;
			});			
			
			jQuery('.document_edit').live('click', function(){
	
				var dId = jQuery(this).attr('id');
				var selectedLi = jQuery(this).parent().parent();
				//alert(selectedLi.html());
				//alert(jQuery(selectedLi).attr('class','ui-state-disabled'));
				jQuery.post('index.php?option=com_attachments&task=upload.editdoc', { id : dId }, function(data){	
					jQuery(selectedLi).attr('class','ui-state-disabled')				
					selectedLi.html(data);
					jQuery( "#adate" ).datepicker();
					jQuery( "#adate"+dId ).datepicker();
				});	

				return false;
			});		

			jQuery('input.cancelDocItem').live('click', function(){
				
				var params = jQuery('.topost', jQuery(this).parent()).serializeArray();
				var currentSelection =jQuery(this).parent().parent();
				jQuery.post('index.php?option=com_attachments&task=upload.canceldoc', params, function(data){					
					currentSelection.html(data);
				});
				
				return false;
			});
			
			
			
			// Removing Default values in text and texarea
	 
		
							 jQuery('.topost').live('focus', function(){
							
								var current_value = jQuery(this).val();
								
								if (current_value == "Document title" || current_value == "A longer description can comes here"){
									
									jQuery(this).val('');
								}
							});
							
							 jQuery('.topost').live('blur', function(){
							
								var current_value = jQuery(this).val();								
								
								if (jQuery(this).is("input")){
									
									if (current_value == ""){									
										jQuery(this).val('Document title');
									}
								}
								
								if (jQuery(this).is("textarea")){
									
									if (current_value == ""){									
										jQuery(this).val('A longer description can comes here');
									}
								}
								
							});
			
			
					
		});
		function DatePickerReload(id){
			jQuery( "#"+id,  window.parent.document ).parent().hide();
			//jQuery( ".btn-info").hide();
		}
		
		function downloadlimitCheck(dval){
			var relativeUrl="index.php?option=com_attachments&view=documents&tmpl=component&directory=tools";
			<?php //To limit the download ?>
			if ( jQuery("#download_limit").val()==1 ) {				
				
				if (dval=='save') {
					jQuery('a[href="' + relativeUrl + '"]').parent().parent().parent().parent().hide();
				}else if (dval=='remove'){
					jQuery('a[href="' + relativeUrl + '"]').parent().parent().parent().parent().show();
				}
				
			}
			<?php //To limit the download ?>
				
		}
		jQuery('.adminformlist').bind('contentchanged', function() {
  // do something after the div content has changed
  alert('woo');
});
	</script>
	
		<div id="attachmentsContainer">
			<label id="docAttach" class="alert" style="<?php echo $this->item->type != 3 ? '' : 'display: none' ; ?>">
				Attachments can only be added for <em><strong>Document</strong></em> type.
			</label>
			<ul class="adminformlist drg_drp">
				<?php if($attachments) : ?>
				<?php foreach($attachments as $attachment) : ?>
				<li  id="ordering_<?php echo $attachment->docid; ?>">
                	<!--<span class="tooly">Click to Drag</span>-->
					<div class="prof_pic">
					  <img src="components/com_attachments/assets/images/icons/128/<?php echo $attachment->extension; ?>.png" width="128" height="128" />
					  <!--<a href="#" class="editCover">edit cover</a> -->
					</div>
                    <span class="edt"><a href="javascript:void(0);" id="<?php echo $attachment->docid ?>" class="document_edit">Edit</a> | <a href="javascript:void(0);" id="<?php echo $attachment->id ?>" class="document_remove">Remove</a></span>
					<div class="attach_list">
						<div class='download-row'>
                        	<div class='download-row-type'>Language: <span><?php echo $attachment->language; ?></span></div>
							<div class='download-row-tile'><?php echo $attachment->title ?></div>
							<div class='download-row-desc'><?php echo $attachment->description ?> </div>
							<div class='download-row-type'>Type: <span><?php echo $attachment->extension ?></span></div>
							<div class='download-row-type'>Size: <span><?php echo $attachment->size ?></span></div>
							<input type="hidden" class="topost" name="attachments[docid][]" value="<?php echo $attachment->docid ?>" />
							<input type="hidden" value="<?php echo $attachment->title ?>" name="attachments[title][]" class="topost">
							<input type="hidden" value="<?php echo $attachment->description ?>" name="attachments[description][]" class="topost">
							
						</div>			  
					</div>
				</li>
				<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
		<div class="marg_left" id="saveAll" style="display:none">
			  <input type="button" value="Save All" class="saveDocItems btn btn-success" />
		</div>
		
		<?php if( count($attachments)<1): ?>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('attachments') as $field): ?>
				<li><?php //echo $field->label; ?><?php echo $field->input; ?></li>
			<?php endforeach; ?>
		</ul>
		<?php else:?>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('attachments') as $field): ?>
				<li><?php //echo $field->label; ?><?php echo $field->input; ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<?php ////Download limit ?>
		<input type="hidden" value="<?php echo count($attachments); ?>" name="download_total" id="download_total" >
		<input type="hidden" value="<?php echo '1' ?>" name="download_limit" id="download_limit" >
		<?php ////Download limit ?>
