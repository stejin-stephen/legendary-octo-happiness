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

?>

    <div class="listing column c0 nomargin">
        <div id="brochur_reports">
            <div id="listing_type1">
                <div class="reports_listing_clm c11 nomargin column">

                <?php $i = 0; foreach ($this->item->tools as $i => $item) :?>
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
                                    <span class="dwn">
                                      <a class="pdf <?= $item->type !=3 ? 'blox bloxPopup' : '' ;?>" <?= $item->type !=3 ? 'data-src="'.$item->cat->url.'" data-id="'.$item->id.'"' : '' ;?> href="<?php echo $item->link; ?>"><?php echo $item->showtext; ?></a>
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
<style>
    .lightboxContain{position:fixed;top:0;bottom:0;left:0;right:0;z-index:2000;background:rgba(0,0,0,.5)}
    .lightboxContain .indLightbox{position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);
    transform:translate(-50%,-50%);width:56%;height:62%}.lightboxContain iframe{border:0;width:100%!important;height:100%!important}
    .lightboxContain img,img{height:auto;max-width:100%}.lightboxContain img{float:none;max-height:100%;margin:0 auto}img{display:block}
</style>

<script>

jQuery('.bloxPopup').lightboxController({
    appendRegion:   '.contentWrap'
});

</script>
