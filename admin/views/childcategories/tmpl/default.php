<?php 
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHTML::_('behavior.modal');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= true;
$saveOrder	= $listOrder == 'eo.ordering';

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task =='childcategories.addDefault'){
			 SqueezeBox.initialize({
			        size: {x: 1200, y: 500}
			    }); 

			
			 SqueezeBox.open('<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=childcategories&hotel_id=-1&tmpl=component&layout=defaults',false,-1);?>',{handler: 'iframe',size:{x:840,y:550}});
		}
		else if (task != 'companies.delete' || confirm('<?php echo JText::_('LNG_CHILDREN_CATEGORIES_CONFIRM_DELETE');?>'))
		{
			Joomla.submitform(task);
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=childcategories');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left fltlft">
				<label class="filter-search-lbl element-invisible" for="hotel_id"><?php echo JText::_('LNG_SELECT_HOTEL',true); ?></label>
					 <select name="hotel_id" id="hotel_id" class="inputbox" onchange="this.form.submit()">
							<option value=""><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
							<option value="-1" <?php echo $this->state->get('filter.hotel_id') == -1?'selected="selected"':''?>><?php echo JText::_('LNG_DEFAULT_CHILD_CATEGORIES',true)?></option>
							<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('filter.hotel_id'));?>
					</select>
			</div>
			
			<div class="filter-select pull-right fltrt btn-group">
				<select name="status_id" class="inputbox input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('LNG_JOPTION_SELECT_STATUS',true);?></option>
					<?php echo JHtml::_('select.options', $this->statuses, 'value', 'text', $this->state->get('filter.status_id'));?>
				</select>
			</div>
		</div>
	</div>

	<div class="clr clearfix"> </div>
	
	<table class="table  adminlist"  id="itemList">
		<thead>
				<tr>
					<th width="1%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL',true); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="10%" >
						<?php echo JText::_('LNG_NAME');?>
					</th>
					<th width="25%" class="center">
						<?php echo JText::_('LNG_CHILDREN_AGE')."(".JText::_('LNG_MIN').":".JText::_('LNG_MAX').")"; ?>
					</th>
					<th width="25%" class="center">
						<?php echo JHtml::_('grid.sort', 'LNG_STATUS', 'eo.status', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'eo.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="15">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php $count = count($this->items); ?>
			<?php foreach ($this->items as $i => $item) :
				$ordering  = ($listOrder == 'eo.ordering');
				$canCreate = true;
				$canEdit   = true;
				$canChange = true;
				$object = $this->hoteltranslationsModel->getObjectTranslation(CHILDREN_CATEGORY_TRANSLATION,$item->id,JRequest::getVar( '_lang'));
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&task=childcategory.edit&id='.$item->id);?>">
							<?php echo isset($object->content)?$object->content:$item->name; ?></a>
						<?php else : ?>
							<?php echo  isset($object->content)?$object->content:$item->name; ?>
						<?php endif; ?>
					</td>
					<td class="center">
						<?php echo $item->min_age.":".$item->max_age; ?>
					</td>
					
					<td class="center">
						<?php echo (int) $item->status==1?JText::_("LNG_ACTIVE"):JText::_("LNG_INACTIVE"); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			
			</tbody>
		</table>
	 
	 <input type="hidden" name="option"	value="<?php echo getBookingExtName()?>" />
	 <input type="hidden" name="task" id="task" value="" /> 
	 <input type="hidden" name="id" value="" />
	 <input type="hidden" name="sourceId" id="sourceId" value="" />
	 <input type="hidden" name="boxchecked" value="0" />
	 <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	 <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	 <?php echo JHTML::_( 'form.token' ); ?> 
</form>

<script type="text/javascript">
	function addNewItem(sourceId){
		SqueezeBox.close();
		jQuery("#sourceId").val(sourceId);
		jQuery("#task").val("childcategory.add");
		jQuery("#adminForm").submit();
	}
	
</script>