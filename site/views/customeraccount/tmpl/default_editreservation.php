<?php 
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
?>
<div class="reservationButtons">
	<button class="ui-hotel-button grey right" value="checkRates" name="checkRates" type="button"	onclick="cancelEdit()">
		<icon class="fa fa-times-circle-o red"></icon>
		<span class="ui-button-text">
			<?php echo JText::_('LNG_CLOSE',true);?>
		</span>
	</button>

	<button class="ui-hotel-button grey right" value="checkRates" name="checkRates" type="button" onclick="saveClose()">
	    <icon class="fa fa-check green"></icon>
		<span class="ui-button-text">
			<?php echo JText::_('LNG_SAVE_CLOSE',true);?>
		</span>			
	</button>
	
	<button class="ui-hotel-button ui-hotel-button right"  value="checkRates" name="checkRates" type="button"	onclick="save()">
	    <icon class="fa fa-pencil-square-o"></icon>
		<span class="ui-button-text">
			<?php echo JText::_('LNG_SAVE',true);?>
		</span>
	</button>
</div>

<div class="frontEndEditReservation">
	<?php include(JPATH_COMPONENT_ADMINISTRATOR.'/views/reservation/tmpl/edit.php');?>
</div>

<div class="reservationButtons">
	<button class="ui-hotel-button grey right" value="checkRates" name="checkRates" type="button"	onclick="cancelEdit()">
		<icon class="fa fa-times-circle-o red"></icon>
		<span class="ui-button-text">
			<?php echo JText::_('LNG_CLOSE',true);?>
		</span>
	</button>

	<button class="ui-hotel-button grey right" value="checkRates" name="checkRates" type="button" onclick="saveClose()">
	    <icon class="fa fa-check green"></icon>
		<span class="ui-button-text">
			<?php echo JText::_('LNG_SAVE_CLOSE',true);?>
		</span>			
	</button>
	
	<button class="ui-hotel-button ui-hotel-button right"  value="checkRates" name="checkRates" type="button"	onclick="save()">
	    <icon class="fa fa-pencil-square-o"></icon>
		<span class="ui-button-text">
			<?php echo JText::_('LNG_SAVE',true);?>
		</span>
	</button>
</div>


<script type="text/javascript">
	var dateFormat = "<?php echo $this->appSettings->dateFormat; ?>";
	var language = "<?php echo JHotelUtil::getJoomlaLanguage();?>";
	var formatToDisplay = calendarFormat(dateFormat);

function changeDates(){
	if(jQuery("#start_date_i").val()=="" || jQuery("#end_date_i").val()==""){
		alert("Please select dates");
		return false;
	}
	jQuery("#start_date").val(jQuery("#start_date_i").val());
	jQuery("#end_date").val(jQuery("#end_date_i").val());
	jQuery("#update_price_type").val(jQuery("#change-dates input[type='radio']:checked").val());
	Joomla.submitbutton('customeraccount.saveReservation');
}
function cancelEdit(){
	jQuery("input[name='task']").val("customeraccount.managereservations");
	jQuery("input[name='view']").val("customeraccount");
	jQuery("form[name='adminForm']").action ="index.php?option=<?php getBookingExtName();?>"
	jQuery("form[name='adminForm']").submit();
}

function save(){
	jQuery("input[name='task']").val("customeraccount.saveReservation");
	jQuery("form[name='adminForm']").submit();
}

function saveClose(){
	jQuery("input[name='task']").val("customeraccount.saveCloseReservation");
	jQuery("form[name='adminForm']").submit();
}
function addRoom(){
	
	var postParameters ="&roomId="+jQuery("#rooms").val()
					+"&startDate="+jQuery("#start_date").val() 
					+"&endDate="+jQuery("#end_date").val()
					+"&current="+jQuery("#current").val()
					+"&adults="+jQuery("#adults").val()
					+"&children="+jQuery("#children").val()
					+"&discountCode="+jQuery("#discount_code").val()
					+"&hotelId="+jQuery("#hotelId").val(); 
	var postData='&task=reservation.addHotelRoom'+postParameters;
	baseUrl="<?php echo JURI::root().'administrator/index.php?option=com_jhotelreservation'?>";
	jQuery.post(baseUrl, postData, processAddRoomResult);
}

    jQuery(document).ready(function () {
        jQuery.fn.datepicker.defaults.language = language;
        jQuery.fn.datepicker.defaults.format = formatToDisplay;
    });
</script>
