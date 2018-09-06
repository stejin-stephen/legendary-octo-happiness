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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');

?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">

    <div class="listing column c0 nomargin">
        <div id="brochur_reports">
            <div id="listing_type1">
                <div class="reports_listing_clm c11 nomargin column">

                <?php $i = 0; foreach ($this->items as $i => $item) : ?>
                    <ul class="column nomargin c6">
                        <li class="title c6 nomargin">
                            <span class="icon column"></span>
                            <h4 class="column"><?php echo $this->escape($item->title); ?></h4>
                        </li>
                        <li class="list c6 nomargin">
                            <dl>
                                <dt><img alt="Preview" name="mem_image" src="<?= $item->cat->image_thumb; ?>"></dt>
                                <dd>
                                    <?php echo $item->introtext; ?>
                                    <span class="dwn">
                    <!--<a class="pdf" href="<?php echo $item->link; ?>">See more</a>-->
                  <?php echo $item->link; ?>
                  </span>
                                </dd>
                            </dl>
                        </li>
                    </ul>
                <?php echo $i%2 !=0 ? '</div><div class="reports_listing_clm c11 nomargin column">' : '';
                $i++; endforeach; ?>

                </div>
            </div>
        </div>
    </div>

				<?php echo $this->pagination->getPagesLinks(); ?>


	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

<div id="loginModal" class="modal">

  <form class="modal-content" id="loginForm">
    <div class="imgcontainer">
      <span onclick="document.getElementById('loginModal').style.display='none'" class="close" title="Close Modal">&times;</span>
    </div>

    <div class="container">
      <label><strong>E-mail</strong></label>
      <input type="text" placeholder="Enter your e-mail" name="email" >

      <label><strong>Password</strong></label>
      <input type="password" placeholder="Enter password" name="password" required>
      <input type="hidden" name="tool_id" id="tool_id" value="" required>
 		<label>
        <input type="checkbox" required><label>I agree to <a href="disclaimer.html" target="_blank">privacy policy</a></label>
      </label>
      <button name="submit_bt" src="templates/gbd_inner/images/green_by_design_send_bt.gif" id="send-bt" class="atag_btn_sprites submit_btn button push"></button>
     
    </div>

  </form>
</div>

<script>
var modal = document.getElementById('loginModal');

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

$(document).keyup(function(e) {
     if (e.keyCode == 27) { // escape key maps to keycode `27`
        modal.style.display = "none";
    }
});

$("#loginForm").on('submit', function(e){
	e.preventDefault();
	$.ajax({
		type : 'POST',
		url : 'index.php?option=com_tools&task=itemcategories.userLogin',
		data : $('#loginForm').serialize(),
		success: function(resp) {
			if(resp) {//$('#tool_'+resp).trigger("click");
				var ajaxcall = "index.php?option=com_tools&task=itemcategories.saveLog&toolId="+resp;
				jQuery.post(ajaxcall,function(res){
					window.location.href = res;
				});
			}
			else alert('Sorry, Invalid E-mail / Password');
		}
	});
});

$('[id^=tool]').click(function(){
	var tool = this.id.split("_");
//	var ajaxcall = "index.php?option=com_tools&task=itemcategories.saveLog&toolId="+tool[1];
//	jQuery.post(ajaxcall,function(resp){
//  if(resp) window.location.href = resp;
//  else
  modal.style.display='block';
		$('#tool_id').val(tool[1]);
	//});
});
</script>
