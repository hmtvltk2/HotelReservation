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


$appSetings = JHotelUtil::getApplicationSettings();
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task != 'reservations.delete' || confirm('<?php echo JText::_('COM_JHOTELRESERVATION_RESERVATIONS_CONFIRM_DELETE', true,true);?>'))
		{
			Joomla.submitform(task);
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservations');?>" method="post" name="adminForm" id="adminForm">
<div id="boxes">
	<fieldset>
        <div class="reservation-details" style="float: left;">
            <label class="filter-search-lbl" for="filter_start_date"><?php echo JText::_('LNG_FROM',true); ?></label>
                <input class="form-control"
                       id="filter_start_date"
                       data-provide="datepicker"
                       name="filter_start_date"
                       value ="<?php echo $this->state->get('filter.start_date');?>"
                       type="text">
                <button type="button" class="btn" id="filter_start_date_img"><i class="icon-calendar"></i></button>

            <label class="filter-search-lbl" for="filter_end_date"><?php echo JText::_('LNG_TO',true); ?></label>
                <input class="form-control"
                       id="filter_end_date"
                       data-provide="datepicker"
                       name="filter_end_date"
                       value ="<?php echo $this->state->get('filter.end_date');?>"
                       type="text">
                <button type="button" class="btn" id="filter_end_date_img"><i class="icon-calendar"></i></button>
        </div>
        <div class="reservation-search" style="float: left;">
            <div class="filter-search fltlft" id="filter-search">
                <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL',true); ?></label>
                <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC',true); ?>" />

                <label class="filter-search-lbl" for="filter_voucher"><?php echo JText::_('LNG_VOUCHER',true); ?>:</label>
                <input type="text" name="filter_voucher" id="filter_voucher" value="<?php echo $this->escape($this->state->get('filter.voucher')); ?>" title="<?php echo JText::_('COM_JHOTELRESRVATION_FILTER_VOUCHER_DESC',true); ?>" />
                <button type="submit" class="btn btn-primary "><?php echo JText::_('JSEARCH_FILTER_SUBMIT',true); ?></button>
                <button type="button" class="btn btn-danger" onclick="clearSearch();this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR',true); ?></button>
            </div>
		</div>
		<div class="filter-select fltrt" id="reservationFilter">
			<select name="filter_hotel_id" class="inputbox" onchange="jQuery('#filter_room_type').attr('selectedIndex',0);this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_HOTEL',true)?></option>
				<?php echo JHtml::_('select.options', $this->hotels, 'hotel_id', 'hotel_name', $this->state->get('filter.hotel_id'));?>
			</select>

			<select id="filter_room_type" name="filter_room_type" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ROOM_TYPES',true);?></option>
				<?php echo JHtml::_('select.options', $this->roomTypes, 'value', 'text', $this->state->get('filter.room_type'));?>
			</select>

			<select name="filter_status" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_STATUS',true);?></option>
				<?php echo JHtml::_('select.options', $this->reservationStatuses, 'value', 'text', $this->state->get('filter.status'));?>
			</select>

			<select name="filter_payment_status" class="inputbox" onchange="this.form.submit()">
				<option value="-1"><?php echo JText::_('JOPTION_SELECT_PAYMENT_STATUS',true);?></option>
				<?php echo JHtml::_('select.options', $this->paymentStatuses, 'value', 'text', $this->state->get('filter.payment_status'));?>
			</select>
            <select name="filter_payment_methods" class="inputbox" onchange="this.form.submit()">
                <option value="-1"><?php echo JText::_('JOPTION_SELECT_PAYMENT_METHODS',true);?></option>
                <?php echo JHtml::_('select.options', $this->paymentMethods, 'value', 'text', $this->state->get('filter.payment_methods'));?>
            </select>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>
	</fieldset>
	<div class="clr"> </div>
    <div class="responsive_table-responsive-vertical">
	<table class="responsive_table responsive_table-hover responsive_table-mc-light-blue"  id="itemList">
		<thead>
			<tr>
				<th class="center rowNumber" width="1%">#</th>
				<th class="center rowNumber" width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL',true); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'LNG_ID', 'c.confirmation_id', $listDirn, $listOrder); ?>
				</th>
				<th width="3%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'LNG_GUEST_NAME', 'c.first_name', $listDirn, $listOrder); ?>
				</th>
				<th width="3%" class="center">
					<?php echo JHtml::_('grid.sort', 'LNG_HOTEL', 'h.hotel_name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center">
					<?php echo JHtml::_('grid.sort', 'LNG_VOUCHER', 'c.voucher', $listDirn, $listOrder); ?>
				</th>
				<th width="4%" class="center">
					<?php echo JHtml::_('grid.sort', 'LNG_CHECK_IN', 'c.start_date', $listDirn, $listOrder); ?>
				</th>
				<th width="4%"  class="center">
					<?php echo JHtml::_('grid.sort', 'LNG_CHECK_OUT', 'c.end_date', $listDirn, $listOrder); ?>
				</th>
				<th width="5%"  class="center">
					<?php echo JHtml::_('grid.sort', 'LNG_CREATED', 'c.created', $listDirn, $listOrder); ?>
				</th>
				<th width="7%"  class="center" >
					<?php echo JText::_('LNG_DESCRIPTION',true) ?>
				</th>
				<th width="4%"  class="center">
					<?php echo JText::_('LNG_AMOUNT',true) ?>
				</th>
				<th width="4%"  class="center">
					<?php echo JText::_('LNG_STATUS',true) ?>
				</th>
				<th width="4%"  class="center">
					<?php echo JText::_('LNG_PAYMENT',true) ?>
				</th>
				<th width="1%"  class="center">
					<?php echo JText::_('LNG_PAYMENT_PROCESSOR',true) ?>
				</th>
				<th width="1%"  class="center">
					<?php echo JText::_('LNG_ACTIONS',true) ?>
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
			<?php foreach ($this->items as $i => $item) {?>
			
			<tr class="row<?php echo $i % 2; ?>">
				<td  class="center rowNumber" width="1%"  data-title="#"><?php echo $i+1; ?></td>
				<td class="center rowNumber" width="1%">
					<?php echo JHtml::_('grid.id', $i, $item->confirmation_id); ?>
				</td>
				<td class="center has-context" data-title="<?php echo JText::_('LNG_ID',true)?>">
					<a
						href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&task=reservation.edit&reservationId='. $item->confirmation_id )?>'
						title="<?php echo JText::_('LNG_CLICK_TO_EDIT',true); ?>"> 
						<?php echo JHotelUtil::getStringIDConfirmation($item->confirmation_id);?>
					</a>	
					
				</td>
				<td class="center" data-title="<?php echo JText::_('LNG_GUEST_NAME',true)?>" >
					<?php echo $item->first_name.' '.$item->last_name?>
				</td>
				<td  class="center" data-title="<?php echo JText::_('LNG_HOTEL',true)?>">
					<?php echo stripslashes($item->hotel_name)?>
				</td>
				<td  class="center small" data-title="<?php echo JText::_('LNG_VOUCHER',true)?>">
					<?php echo stripslashes($item->voucher)?>
				</td>
				<td  class="center small" data-title="<?php echo JText::_('LNG_CHECK_IN',true)?>">
					<?php echo JHotelUtil::getDateGeneralFormat($item->start_date)?>
				</td>
				<td  class="center small" data-title="<?php echo JText::_('LNG_CHECK_OUT',true)?>">
					<?php echo JHotelUtil::getDateGeneralFormat($item->end_date)?>
				</td>
				<td  class="center small" data-title="<?php echo JText::_('LNG_CREATED',true)?>">
					<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC2',true)); ?>
				</td>
				<td  class="center small " data-title="<?php echo JText::_('LNG_DESCRIPTION',true)?>">
					<?php echo JText::_('LNG_ADULTS',true)?>: <?php echo $item->total_adults?>
					 &nbsp;&nbsp;&nbsp;
					 <?php if($appSetings->show_children){?>
						<?php echo JText::_('LNG_CHILDREN',true)?>: <?php echo $item->total_children?>
						&nbsp;&nbsp;&nbsp;
					<?php } ?>
					<?php echo JText::_('LNG_ROOMS',true)?>: <?php echo $item->rooms;?> 
				</td>
				<td  class="center small" >
					<?php echo $item->total;?> 
				</td>
				<td class="center" data-title="<?php echo JText::_('LNG_STATUS',true)?>">
					<div class="reservation-status-<?php echo $item->reservation_status?> reservation-status">
						<select name="reservation_status" class="inputbox" onchange="changeStatus(this.value,<?php echo $item->confirmation_id ?>)">
							<?php echo JHtml::_('select.options', $this->reservationStatuses, 'value', 'text', $item->reservation_status);?>
						</select>
					</div>
				</td>
				<td class="center" data-title="<?php echo JText::_('LNG_PAYMENT',true)?>">
					<div class="payment-status-<?php echo $item->payment_status?> payment-status <?php echo $item->amount_paid == $item->total && ($item->payment_status == JHP_PAYMENT_STATUS_PAID || $item->payment_status == JHP_PAYMENT_STATUS_WAITING )? "full-payment":"" ?>">
						<select name="reservation_status" class="inputbox" onchange="changePaymentStatus(this.value,<?php echo $item->confirmation_id ?>)">
							<?php
                            echo JHtml::_('select.options', $this->paymentStatuses,'value', 'text', $item->payment_status);?>
						</select>
					</div>
				</td>
				<td  class="center small" >
					<?php echo $item->processor_type; echo empty($item->payment_method)?"":" - ".$item->payment_method?> 
				</td>
				<td  class="actions" data-title="<?php echo JText::_('LNG_ACTIONS',true)?>">
					<a class="quick-action" href="javascript:showConfirmation(<?php echo $item->confirmation_id ?>)" title="<?php echo JText::_('LNG_PREVIEW',true); ?>">
						<img   src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/preview.png" ?>' />
					</a>
					&nbsp;
					<a class="quick-action" href='<?php echo JRoute::_( 'index.php?option=com_jhotelreservation&task=reservation.edit&reservationId='. $item->confirmation_id )?>'
						title="<?php echo JText::_('LNG_EDIT',true); ?>"> 
						<img  src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/edit.png" ?>' />
					</a>
					&nbsp;
					<a class="quick-action" href="javascript:showConfirmation(<?php echo $item->confirmation_id ?>)" title="<?php echo JText::_('LNG_PRINT',true); ?>">
						<img  src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/print.png" ?>' />
					</a>
					&nbsp;
					<a  class="quick-action" href="javascript:sendEmail(<?php echo $item->confirmation_id ?>)" title="<?php echo JText::_('LNG_SEND_EMAIL',true); ?>">
						<img src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/email.png" ?>' />
					</a>
					&nbsp;
					<a  class="quick-action" href="javascript:sendClientInvoiceEmail(<?php echo $item->confirmation_id ?>)" title="<?php echo JText::_('LNG_SEND_INVOICE_EMAIL',true); ?>">
						<img src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/invoice_email.png" ?>' />
					</a>
					&nbsp;
					<?php if($item->reservation_status==CANCELED_ID && $item->cancellation_notes!=""){?>
					<a class="quick-action" href="javascript:void;" title="<?php echo $item->cancellation_notes?$item->cancellation_notes:"n/a"; ?>"> 
						<img  src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/cancel_notes.png" ?>' />
					</a>
					<?php }?>
						<a class="quick-action" href="javascript:showChangeLog(<?php echo $item->confirmation_id ?>);" title="<?php echo "Change Log"; ?>">
							<img  src='<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/about.png" ?>' />
						</a>
				</td>
			</tr>
			<?php 
			}?>
		</tbody>	
	</table>
    </div>
	</div>

	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" id="reservationId" name="reservationId" value="" />
	<input type="hidden" id="statusId" name="statusId" value="" />
	<input type="hidden" id="paymentStatusId" name="paymentStatusId" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>

</form>

<div id="reservation-view"  style="display:none">
	<div id="dialog-container">
		<div class="titleBar">
			<span class="dialogTitle" id="dialogTitle"></span>
			<span  title="Cancel"  class="dialogCloseButton" onClick="jQuery.unblockUI();">
				<span title="Cancel" class="closeText">x</span>
			</span>
		</div>
		
		<div class="dialogContent">
			
			<iframe name="confirmationIfr" id="confirmationIfr" src="">
			
			</iframe>
		</div>
	</div>
</div>

<script>
    var dateFormat = '<?php echo  $this->appSettings->dateFormat; ?>';
    var language = '<?php echo JHotelUtil::getJoomlaLanguage();?>';
    var formatToDisplay = calendarFormat(dateFormat);

    jQuery(document).ready(function(){
        jQuery.fn.datepicker.defaults.language = language;
        jQuery.fn.datepicker.defaults.format = formatToDisplay;
    });


    jQuery("#filter_start_date,#filter_end_date").datepicker({
        autoclose: true,
        orientation: "top left",
        startDate: -Infinity,
        toggleActive: true,
        format:formatToDisplay,
        language: language
    });

    jQuery("#filter_start_date_img").click(function(){
        jQuery('#filter_start_date').focus();
    });

    jQuery("#filter_end_date_img").click(function(){
        jQuery('#filter_end_date').focus();
    });

	function showConfirmation(reservationId){
		var baseUrl = "<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservation&tmpl=component&layout=single',false,0); ?>";
		baseUrl = baseUrl + "&reservationId="+reservationId;
		jQuery("#confirmationIfr").attr("src",baseUrl);
        jQuery.blockUI({ message: jQuery('#reservation-view'), css: {width: '85%', top: '7%',left: '8%'} });
        jQuery('.blockOverlay').click(jQuery.unblockUI);
	}


    function showChangeLog(reservationId){
	    var baseUrl = "<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=reservation&tmpl=component&layout=changelog',false,0); ?>";
	    baseUrl = baseUrl + "&reservationId="+reservationId;
	    jQuery("#confirmationIfr").attr("src",baseUrl);
	    jQuery.blockUI({ message: jQuery('#reservation-view'), css: {width: '85%', top: '7%',left: '8%'} });
	    jQuery('.blockOverlay').click(jQuery.unblockUI);
    }

	function sendEmail(reservationId){
		if(confirm('<?php echo JText::_('COM_JHOTELRESERVATION_SEND_EMAIL', true,true);?>')){
			jQuery("#reservationId").val(reservationId);
			jQuery("#task").val("reservations.sendEmail");
			jQuery("#adminForm").submit();
		}
	}

	function sendClientInvoiceEmail(reservationId){
		if(confirm('<?php echo JText::_('COM_JHOTELRESERVATION_SEND_EMAIL', true,true);?>')){
			jQuery("#reservationId").val(reservationId);
			jQuery("#task").val("reservations.sendClientInvoiceEmail");
			jQuery("#adminForm").submit();
		}
	}

	function clearSearch(){
		document.getElementById('filter_search').value='';
		document.getElementById('filter_voucher').value='';
		document.getElementById('filter_start_date').value='';
		document.getElementById('filter_end_date').value='';
		jQuery("#task").val("");
	}
	
	function changeStatus(status, reservationId){
		jQuery("#statusId").val(status);
		jQuery("#reservationId").val(reservationId);
		jQuery("#task").val("reservations.changeStatus");
		jQuery("#adminForm").submit();
	}

	function changePaymentStatus(status, reservationId){
		jQuery("#paymentStatusId").val(status);
		jQuery("#reservationId").val(reservationId);
		jQuery("#task").val("reservations.changePaymentStatus");
		jQuery("#adminForm").submit();
	}
</script>

