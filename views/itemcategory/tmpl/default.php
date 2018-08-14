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

$my_page_title = $this->escape($this->item->title);
$page_title_parts = explode(" ", $my_page_title, 2);

if (count($page_title_parts)>0) {
	$new_page_title = $page_title_parts[0] . " " . '<span class="blue">' . $page_title_parts[1] . '</span>';
} else {
	$new_page_title = $page_title_parts;
}

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_tools.' . $this->item->id);

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_tools' . $this->item->id))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<div id="publication" class="column">
	<div class="column nomargin content rightpad">
		<div class="title nomargin column">
			<h3><?php echo $new_page_title; ?></h3>
		</div>
	<div class="intro"><?php echo $this->item->description; ?><br /></div>
	</div>
</div>

<?php echo $this->loadTemplate('items');

foreach($this->item->subitems as $sub): ?>

<div class="dynamic_content">
	<div class="title  nomargin">
		
		<h3 class="contentheading">
				<?php 
			$title = explode(" ",$this->escape($sub->title), 2);
			echo $title[0]." <span class='red'>".$title[1]."</span>";
		?></h3>
		
		<div itemprop="articleBody">
			<?= $sub->description; ?>
		</div>
	</div>
</div>

    <div class="listing column c0 nomargin">
        <div id="brochur_reports">
            <div id="listing_type1">
                <div class="reports_listing_clm c11 nomargin column">

                <?php $i = 0; foreach ($sub->tools as $i => $item) :?>
                    <ul class="column nomargin c6">
                        <li class="title c6 nomargin">
                            <span class="icon column"></span>
                            <h4 class="column"><?php echo $this->escape($item->title); ?></h4>
                        </li>
                        <li class="list c6 nomargin">
                            <dl>
                                <dt><img alt="Preview" name="mem_image" src="<?php echo $item->cat->image; ?>"></dt>
                                <dd>
                                    <p><?php echo $item->introtext; ?></p>
                                    <span class="dwn"><a class="pdf" href="<?php echo $item->link; ?>"><?php echo $item->showtext; ?></a></span>
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
	

<?php endforeach; ?>

<!-- <div class="item_fields">

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

</div> -->

<?php if (JFactory::getUser()->authorise('core.delete','com_tools.itemcategory.'.$this->item->id)) : ?>

	<!-- <a class="btn btn-danger" href="#deleteModal" role="button" data-toggle="modal">
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
			<a href="<?php echo JRoute::_('index.php?option=com_tools&task=itemcategory.remove&id=' . $this->item->id, false, 2); ?>" class="btn btn-danger">
				<?php echo JText::_('COM_TOOLS_DELETE_ITEM'); ?>
			</a>
		</div>
	</div> -->

<?php endif; ?>
