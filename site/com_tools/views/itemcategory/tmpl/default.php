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
	<?php //echo $this->loadTemplate('items');

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
                                <dt><img alt="Preview" name="mem_image" src="<?php echo $item->cat->image_thumb; ?>"></dt>
                                <dd>
                                    <p><?php echo $item->introtext; ?></p>
                                    <span class="dwn gallery"><a class="pdf" <?= $item->type !=3 ? "rel='prettyPhoto'" : '' ;?> href="<?php echo $item->link; ?>"><?php echo $item->showtext; ?></a></span>
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
</div>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function(){
            $(".gallery a[rel^='prettyPhoto']").prettyPhoto();
        });
    </script>
    <style>
        div.pp_overlay{background:#000;display:none;left:0;position:absolute;top:0;width:100%;z-index:9500}.pp_details{display: none;}
        div.pp_pic_holder{display:none;position:absolute;width:100px;z-index:10000}.pp_fade,.pp_gallery li.default a img{display:none}
    </style>

