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
                                    <span class="dwn"><a class="pdf" href="<?php echo $item->link; ?>"><?php echo $item->showtext; ?>sdsd</a></span>
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
