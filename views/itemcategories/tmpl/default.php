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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder  = $user->authorise('core.edit.state', 'com_tools');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tools&task=itemcategories.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>
<?php
	$data = @$displayData;;

	// Receive overridable options
	$data['options'] = !empty($data['options']) ? $data['options'] : array();

	// Set some basic options
	$customOptions = array(
		'filtersHidden'       => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
		'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : JFactory::getApplication()->get('list_limit', 20),
		'searchFieldSelector' => '#filter_search',
		'orderFieldSelector'  => '#list_fullordering',
		'filterButton' 	      => 0
	);

	$data['options'] = array_merge($customOptions, $data['options']);

	$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

	// Load search tools
	JHtml::_('searchtools.form', $formSelector, $data['options']);

	$filtersClass = @$data['view']->activeFilters ? ' js-stools-container-filters-visible' : '';
?>
<?php
	// Set some basic options for pagination
	$list = array(
		'showLimitBox' => 0
	);
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<div class="content-area-wrap">
<form action="<?php echo JRoute::_('index.php?option=com_tools&view=itemcategories'); ?>" method="post"
	  name="adminForm" id="adminForm">
<div class="listing-sub-menu">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span12 horizontal-navigation-container">
		<?php echo $this->sidebar; ?>
	</div>
	<?php endif; ?>
		<div class="module_filter_search search-wrap">
            <?php echo JLayoutHelper::render('joomla.searchtools.default.bar', array('view' => $this , 'options' => $customOptions)); ?>
			</div>
			</div>
	<div class="span9 main-container">
		<div id="j-main-container">
			<div class="clearfix"></div>
			<?php if (empty($this->items)) : ?>
					<div class="alert alert-no-items">
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
			<?php else: ?>
			<table class="table table-striped" id="itemList">
				<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value=""
							   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
							   <label for="checkall" class="blanklabel">&nbsp;</label>
					</th>
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_TOOLS_ITEMS_TITLE', 'a.`title`', $listDirn, $listOrder); ?>
				</th>
				<!--<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_TOOLS_ITEMS_DESCRIPTION', 'a.`description`', $listDirn, $listOrder); ?>
				</th>-->
				<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'E-mail(s)', 'a.`emails`', $listDirn, $listOrder); ?>
				</th>

					<?php if (isset($this->items[0]->state)): ?>
						<th class='left'>
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
</th>
					<?php endif; ?>
									<th class='left'>
				<?php echo JHtml::_('searchtools.sort',  'COM_TOOLS_ITEMS_ID', 'a.`id`', $listDirn, $listOrder); ?>
				</th>
					<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
					<?php endif; ?>


				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php //echo $this->pagination->getListFooter(); ?>
						<?php echo $this->pagination->getPaginationLinks('joomla.pagination.links',$list); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_tools');
					$canEdit    = $user->authorise('core.edit', 'com_tools');
					$canCheckin = $user->authorise('core.manage', 'com_tools');
					$canChange  = $user->authorise('core.edit.state', 'com_tools');
					?>
					<tr class="row<?php echo $i % 2; ?>">


						<td class="hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							<label for="cb<?php echo $i; ?>" class="blanklabel">&nbsp;</label>
						</td>

														<td>
				<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'itemcategories.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_tools&task=itemcategory.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->title); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->title); ?>
				<?php endif; ?>

				</td>		<td>

					<?php echo $item->emails; ?>
				</td>			
						<?php if (isset($this->items[0]->state)): ?>
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $item->state, $i, 'itemcategories.', $canChange, 'cb'); ?>
</td>
						<?php endif; ?>	<td>

					<?php echo $item->id; ?>
				</td>					<?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';

									if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
										  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5"
										   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						<?php endif; ?>

					</tr>
					<?php // Child categories STARTS HERE
					if($item->subItems):
						foreach ($item->subItems as $j => $item) :
							$ordering   = ($listOrder == 'a.ordering');
							$canCreate  = $user->authorise('core.create', 'com_tools');
							$canEdit    = $user->authorise('core.edit', 'com_tools');
							$canCheckin = $user->authorise('core.manage', 'com_tools');
							$canChange  = $user->authorise('core.edit.state', 'com_tools');
					?>
						<tr>
							<td class="hidden-phone">
								<?php echo JHtml::_('grid.id', '0'.$j, $item->id); ?>
								<label for="cb0<?php echo $j; ?>" class="blanklabel">&nbsp;</label>
							</td>
							<td>
				<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
					<?php echo JHtml::_('jgrid.checkedout', '0'.$j, $item->uEditor, $item->checked_out_time, 'itemcategories.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_tools&task=itemcategory.edit&id='.(int) $item->id); ?>">
					<?php echo '— '.$this->escape($item->title); ?></a>
				<?php else : ?>
					<?php echo $this->escape($item->title); ?>
				<?php endif; ?>

				</td><td>

					<?php echo $item->emails; ?>
				</td>				<!--<td>

					<?php echo $item->description; ?>
				</td>--><?php if (isset($this->items[0]->state)): ?>
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $item->state, '0'.$j, 'itemcategories.', $canChange, 'cb'); ?>
</td><td>

					<?php echo $item->id; ?>
				</td><?php if (isset($this->items[0]->ordering)) : ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';

									if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
										  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5"
										   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						<?php endif; ?>
						</tr>
					<?php endforeach; endif; ?>
				<?php endforeach; ?>
				</tbody>
			</table>
<?php endif; ?>
				</div>
	</div>
	<div class="span3 main-filter">
	    <div id="filter-bar" class="btn-toolbar">
		<?php echo JLayoutHelper::render('joomla.searchtools.default.filters', array('view' => $this)); ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default.list', array('view' => $this)); ?>
	    </div>
	</div>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        window.submitform(task);

        return false;
    };
</script>
