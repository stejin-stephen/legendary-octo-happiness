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

//$my_page_title = $this->escape($this->params->get('page_title'));

$my_page_title = $this->escape($this->settings->title);
$page_title_parts = explode(" ", $my_page_title, 2);

if (count($page_title_parts)>0) {
	$new_page_title = $page_title_parts[0] . " " . '<span class="blue">' . $page_title_parts[1] . '</span>';
} else {
	$new_page_title = $page_title_parts;
}

?>


<!--<div id="publication" class="column">
	<div class="column nomargin content">
		<div class="title nomargin column">
			<h3><?php echo $new_page_title; ?></h3>
		</div>
	<div class="intro"><?php echo str_replace("\'","'", $this->settings->introtext); ?><br /></div>
	</div>
</div>-->

<?php echo $this->loadTemplate('categories'); ?>
