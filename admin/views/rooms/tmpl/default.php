<?php defined('_JEXEC') or die('Restricted access');
 
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('bootstrap.tooltip');
$canOrder	= true;
$user		= JFactory::getUser();
$saveOrder	= $listOrder == 'r.ordering';
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_jhotelreservation&task=rooms.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=rooms');?>" method="post" name="adminForm" id="adminForm">

	<div id="editcell">
		<fieldset>
			<div style='text-align:left'>
				<strong><?php echo JText::_('LNG_PLEASE_SELECT_THE_HOTEL_IN_ORDER_TO_VIEW_THE_EXISTING_SETTINGS',true)?> :</strong>
				
				 <select name="hotel_id" id="hotel_id" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('LNG_SELECT_DEFAULT',true)?></option>
						<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('filter.hotel_id'));?>
				</select>
				
				<hr>
			</div>
			<?php
			$rooms = count( $this->items );
			if( $this->state->get('filter.hotel_id') > 0 && $rooms > 0 )
			{
				?>

				<table class="table adminlist" id="itemList">
					<thead>
					<tr>
						<th width="1%" class="nowrap   hidden-phone">
							<?php echo JHtml::_( 'grid.sort', '<i class="icon-menu-2"></i>', 'r.ordering', $listDirn, $listOrder ); ?>
						</th>
						<th>#</th>
						<th width="1%" class=" ">
							<input type="checkbox" name="checkall-toggle" value=""
							       title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL', true ); ?>"
							       onclick="Joomla.checkAll(this)"/>
						</th>
						<Th width='1%' class="nowrap"><B><?php echo JText::_( 'LNG_AVAILABLE', true ) ?></B></Th>
						<th width="1%">&nbsp;</th>
						<Th width='15%' class="nowrap">
							<B>
								<?php echo JHtml::_( 'grid.sort', 'LNG_NAME', 'r.room_name', $listDirn, $listOrder ); ?>
							</B>
						</Th>
						<Th width='5%' class="nowrap  "><B><?php echo JText::_( 'LNG_CAPACITY', true ) ?></B></Th>
						<Th width='5%' class="nowrap   hidden-phone">
							<B><?php echo JText::_( 'LNG_DISPLAY_ON_FRONT', true ) ?></B></Th>
						<Th width='1%' class="nowrap   hidden-phone">
							<B>
								<?php echo JHtml::_( 'grid.sort', 'LNG_ORDER', 'r.ordering', $listDirn, $listOrder ); ?>
							</B>
						</Th>
						<Th width='1%' class="nowrap   hidden-phone"><B><?php
								echo JHtml::_( 'grid.sort', 'LNG_ID', 'r.room_id', $listDirn, $listOrder ); ?></B>
						</Th>
					</tr>
					</thead>
					<tbody>

					<?php
					$nrcrt = 1;
					for ( $i = 0; $i < $rooms; $i ++ )
					{
						$room      = $this->items[$i];
						$ordering  = ( $listOrder == 'r.ordering' );
						$canCreate = true;
						$canEdit   = true;
						$canChange = true;

						//dmp($room);

						?>
						<TR class="row<?php echo $i % 2 ?>"
						    sortable-group-id="<?php echo count( $this->items ); ?>">
							<td class="order nowrap   hidden-phone">
								<?php
								//$canChange = $user->authorise('core.edit.state', '.rooms.' . $room->room_id);
								$iconClass = '';
								if ( ! $canChange )
								{
									$iconClass = ' inactive';
								}
								elseif ( ! $saveOrder )
								{
									$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText( 'JORDERINGDISABLED' );
								}
								?>
								<span class="sortable-handler <?php echo $iconClass ?>">
									<i class="icon-menu"></i>
								</span>
								<?php if ( $canChange && $saveOrder ) : ?>
									<input type="text" style="display:none" name="order[]" size="5"
									       value="<?php echo $room->ordering; ?>"
									       class="width-20 text-area-order "/>
								<?php endif; ?>
							</td>
							<td><?php echo $nrcrt ++; ?></td>
							<td class=" ">
								<?php echo JHtml::_( 'grid.id', $i, $room->room_id ); ?>
							</td>
							<?php if ( $room->is_available == false )
							{ ?>
								<td class=" ">
									<a class="btn btn-micro hasTooltip" href="javascript:void(0);"
									   onclick="return listItemTask('cb<?php echo $i ?>','rooms.changeState')"
									   ><i class="icon-unpublish"></i></a>
								</td>
							<?php }
							else
							{ ?>
								<td class=" ">
									<a class="btn btn-micro hasTooltip" href="javascript:void(0);"
									   onclick="return listItemTask('cb<?php echo $i ?>','rooms.changeState')"
									  >
										<i class="icon-publish"></i>
									</a>
								</td>
							<?php } ?>
							<td>
								<?php
								if ( isset( $room->room_picture_path ) )
								{
									echo "<img alt='" . JHotelUtil::setAltAttribute( $room->room_picture_path ) . "' class='round-image preview' src='" . JURI::root() . PATH_PICTURES . $room->room_picture_path . "'/>";
								}
								else
								{
									echo "<img alt='default_" . $room->room_id . "' class='round-image preview' src='" . JURI::root() . PATH_PICTURES . "/no_image.jpg' />";
								}
								?>
							</td>
							<TD class="nowrap   has-context">

								<a href='<?php echo JRoute::_( 'index.php?option=' . getBookingExtName() . '&view=room&layout=edit&room_id=' . $room->room_id ) ?>'
								   title="<?php echo JText::_( 'LNG_CLICK_TO_EDIT', true ) ?>"
								>
									<B><?php
										$room_name = JHotelUtil::printTranslatedValues( $this->room_name_translation, $room->room_id, $room->room_name );
										echo $room_name;
										?></B>
								</a>

							</TD>
							<TD class="nowrap   has-context">
								<?php echo JText::_( 'LNG_ADULTS', true ) ?> :<?php echo $room->max_adults ?> <br/>
								<?php if ( $this->appSettings->show_children != 0 )
								{ ?>
									<?php echo JText::_( 'LNG_CHILDREN', true ) ?> :<?php echo $room->base_children ?>
								<?php } ?>
							</TD>
							<?php if ( $room->front_display == false )
							{ ?>
								<td class="  hidden-phone">
									<a class="btn btn-micro hasTooltip" href="javascript:void(0);"
									   onclick="return listItemTask('cb<?php echo $i ?>','rooms.changeFrontState')"
									   ><i class="icon-unpublish"></i></a>
								</td>
							<?php }
							else
							{ ?>
								<td class="  hidden-phone">
									<a class="btn btn-micro hasTooltip" href="javascript:void(0);"
									   onclick="return listItemTask('cb<?php echo $i ?>','rooms.changeFrontState')"
									   >
										<i class="icon-publish"></i>
									</a>
								</td>
							<?php } ?>
							<TD class="  hidden-phone"><?php echo $room->ordering ?></TD>
							<TD class="  hidden-phone"><?php echo $room->room_id ?></TD>
						</TR>
						<?php
					}
					?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="15">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</TABLE>
				<?php
			}else{
				?>
			<?php
				echo "<legend>".JText::_('LNG_NO_ROOMS_DEFINED') ."</legend>";
			}
			?>
		</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="refreshScreen" id="refreshScreen" value="<?php echo JRequest::getVar('refreshScreen',null)?>" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	
	
	<script language="javascript" type="text/javascript">

		Joomla.submitbutton = function(task) {
			if (task != 'rooms.delete' || confirm('<?php echo JText::_('LNG_ARE_YOU_SURE_YOU_WANT_TO_DELETE', true,true);?>')) {
				Joomla.submitform(task);
			}
		}

		jQuery(document).ready(function()
		{
			var hotelId=jQuery('#hotel_id').val();
			var refreshScreen=jQuery('#refreshScreen').val();
			var nrHotels = jQuery('#hotel_id option').length;
			if(refreshScreen=="" && parseInt(nrHotels)==2){
				jQuery('#hotel_id :nth-child(2)').prop('selected', true); 
				jQuery('#refreshScreen').val("true");
				jQuery("#hotel_id").trigger('change');	
			}
		});	
		</script>
</form>


