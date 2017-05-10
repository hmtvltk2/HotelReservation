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
?>
<h3><?php echo JText::_('LNG_SELECT_DEFAULT_EXTRA_OPTION',true)?></h3>
<table class="table table-striped adminlist"  id="itemList">
	<thead>
			<tr>
			
				<th width="15%" >
					<?php echo JText::_('LNG_NAME',true); ?>
				</th>
				<th width="20%" >
					<?php echo JText::_('LNG_DESCRIPTION',true); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('LNG_PRICE',true); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JText::_('LNG_END_DATE',true); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JText::_('LNG_END_DATE',true); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('LNG_IMAGE',true); ?>
				</th>
				<th width="3%" class="nowrap">
					<?php echo JText::_('LNG_PER_DAY',true); ?>
				</th>
				<th width="3%">
					<?php echo JText::_('LNG_MANDATORY',true); ?>
				</th>
				
			</tr>
		</thead>
		<tbody>
		<?php $count = count($this->items); ?>
		<?php foreach ($this->items as $i => $item) :

			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a href="javascript:void(0);" onclick= "if (window.parent) window.parent.addNewItem(<?php echo $item->id ?>);">
						<?php echo $this->escape($item->name); ?>
					</a>
				</td>
				<td class="center">
					<?php echo $item->description; ?>
				</td>
				<td class="center">
					<?php echo $item->price; ?>
				</td>
				<td class="center">
					<?php echo $item->start_date!='0000-00-00' ? JHotelUtil::getDateGeneralFormat($item->start_date):"" ?>
				</td>
				<td class="center">
					<?php echo $item->end_date!='0000-00-00' ? JHotelUtil::getDateGeneralFormat($item->end_date):"" ?>
				</td>
				<td class="center">
					<?php
						if(isset($item->image_path)){
							echo "<img class='preview' src='".JURI::base() ."components/".getBookingExtName().EXTRA_OPTISON_PICTURE_PATH.$item->image_path."'/>";
						}
					?>
				</td>
				<td class="center">
					<?php echo $item->is_per_day; ?>
				</td>
				<td class="center">
					<?php echo $item->mandatory; ?>
				</td>

			</tr>
		<?php endforeach; ?>
		
		</tbody>
	</table>
	