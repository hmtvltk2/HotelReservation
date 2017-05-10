
function checkRoomRates(formName) {
	var form = document.getElementById(formName);
	console.debug(form);
	var objDataS = form.elements['jhotelreservation_datas'];
	var objDataE = form.elements['jhotelreservation_datae'];
	var objR = form.elements['jhotelreservation_rooms'];
	var objGA = form.elements['jhotelreservation_guest_adult'];
	var objGC = form.elements['jhotelreservation_guest_child'];
	var message = '';
	var userCurrency = document.getElementById("user_currency");

	if (!checkDate(objDataS))
		return false;
	if (!checkDate(objDataE))
		return false;

	if (!compareDate(objDataS, objDataE, message))
		return false;

	var h = form.elements['hotel_id'];
	var yearObj_start = form.elements['year_start'];
	var monthObj_start = form.elements['month_start'];
	var dayObj_start = form.elements['day_start'];

	var yearObj_end = form.elements['year_end'];
	var monthObj_end = form.elements['month_end'];
	var dayObj_end = form.elements['day_end'];

	var r = form.elements['rooms'];
	var ga = form.elements['guest_adult'];
	var gc = form.elements['guest_child'];
	// form.elements['user_currency'].value =  userCurrency;

	r.value = objR.value;
	ga.value = objGA.value;
	
	if(typeof objGC != "undefined" )
	gc.value = objGC.value;

	re = /^(\d{1,4})\-(\d{1,2})\-(\d{2})$/;
	re2 = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
	re3 = /^\d{1,2}\/\d{1,2}\/\d{4}$/;

	if (regs1 = objDataS.value.match(re)) {
		regs1 = objDataS.value.split('-');
		yearObj_start.value = regs1[0];
		monthObj_start.value = regs1[1];
		dayObj_start.value = regs1[2];
	} else if (regs1 = objDataS.value.match(re2)) {
		regs1 = objDataS.value.split('-');
		yearObj_start.value = regs1[2];
		monthObj_start.value = regs1[1];
		dayObj_start.value = regs1[0];
	} else if (regs1 = objDataS.value.match(re3)) {
		regs1 = objDataS.value.split('/');
		yearObj_start.value = regs1[2];
		monthObj_start.value = regs1[0];
		dayObj_start.value = regs1[1];
	}

	if (regs2 = objDataE.value.match(re)) {
		regs2 = objDataE.value.split('-');
		yearObj_end.value = regs2[0];
		monthObj_end.value = regs2[1];
		dayObj_end.value = regs2[2];
	} else if (regs2 = objDataE.value.match(re2)) {
		regs2 = objDataE.value.split('-');
		yearObj_end.value = regs2[2];
		monthObj_end.value = regs2[1];
		dayObj_end.value = regs2[0];
	} else if (regs2 = objDataE.value.match(re3)) {
		regs2 = objDataE.value.split('/');
		yearObj_end.value = regs2[2];
		monthObj_end.value = regs2[0];
		dayObj_end.value = regs2[1];
	}

	form.submit();
}

function AddDays(date, amount) {
	var tzOff = date.getTimezoneOffset() * 60 * 1000;
	var t = date.getTime();
	t += (1000 * 60 * 60 * 24) * amount;
	var d = new Date();
	d.setTime(t);
	var tzOff2 = d.getTimezoneOffset() * 60 * 1000;
	if (tzOff != tzOff2) {
		var diff = tzOff2 - tzOff;
		t += diff;
		d.setTime(t);
	}
	return d;
}

function addFilterRule(filterRule) {
	if (jQuery("#userModuleForm #filterParams").val().length > 0)
		jQuery("#userModuleForm #filterParams").val(
				jQuery("#userModuleForm #filterParams").val() + "&" + filterRule);
	else
		jQuery("#userModuleForm #filterParams").val(filterRule);
	
	jQuery("#userModuleForm #resetSearch").val(0);
	checkRoomRates('userModuleForm');
}

function removeFilterRule(filterRule) {
	var str = jQuery("#userModuleForm #filterParams").val();
	jQuery("#userModuleForm #filterParams").val((str.replace(filterRule, "")));
	checkRoomRates('userModuleForm');
}

function setSearchType(searchType) {
	document.getElementById('searchType').value = searchType;
	checkRoomRates('userModuleForm');
}

function setDepartureDate(elementId, date,dateFormat) {
	var strDate = document.getElementById(elementId);
	strDate.value = getNextDay(date,dateFormat);
}

function setDepartureDateHotel(date) {
	var strDate = document.getElementById('jhotelreservation_datae2');
	strDate.value = getNextDay(date);
}

function getNextDay(date,dateFormat) {
	dateA = date.split('-');
	if (dateA.length != 3)
		dateA = date.split('/');
	if (dateA.length == 3) {
		re = /^(\d{1,4})\-(\d{1,2})\-(\d{2})$/;
		re2 = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
		re3 = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
		var dateR = '';
		if (regs1 = date.match(re)) {
			dateR = new Date(dateA[0], dateA[1] - 1, dateA[2]);
		} else if (regs1 = date.match(re2)) {
			dateR = new Date(dateA[2], dateA[1] - 1, dateA[0]);
		} else if (regs1 = date.match(re3)) {
			dateR = new Date(dateA[2],dateA[0]-1,dateA[1]);
		 }
		
		var d2 = AddDays(dateR, 1);

		var y = d2.getFullYear();
		var m = d2.getMonth() + 1;
		var d = d2.getDate();
		
	

		if (m < 10)
			m = '0' + m;
		if (d < 10)
			d = '0' + d;

		var strD2 = y + '-' + m + '-' + d;

		if('d-m-Y'== dateFormat)
			strD2 = d + '-' + m + '-' + y;
		if('m/d/Y'== dateFormat)
			strD2 = m + '/' + d + '/' + y;
        if('m-d-Y' == dateFormat)
            strD2 = m + '-' + d + '-' + y;
		

		return strD2;
	}
	return '';
}

function compareDateValue(field1, field2, msg) {
	var ret = false;
	re = /^(\d{1,4})\-(\d{1,2})\-(\d{2})$/;
	re2 = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
	if (field1 != '' && field2 != '') {
		if (regs1 = field1.match(re)) {
			regs1 = field1.split('-');
			regs2 = field2.split('-');
			if (regs1 && regs2) {
				date1 = new Date(regs1[0], regs1[1] - 1, regs1[2]);
				date2 = new Date(regs2[0], regs2[1] - 1, regs2[2]);
				ret = date1.getTime() < date2.getTime();
			}
		} else if (regs1 = field1.match(re2)) {
			regs1 = field1.split('-');
			regs2 = field2.split('-');
			if (regs1 && regs2) {
				date1 = new Date(regs1[2], regs1[1] - 1, regs1[0]);
				date2 = new Date(regs2[2], regs2[1] - 1, regs2[0]);
				ret = date1.getTime() <= date2.getTime();
			}
		}
	}
	if (ret == false && msg != '') {
		alert(msg);
	}
	return ret;
}

function checkStartDate(startDate, defaultStartDate, defaultEndDate) {

	if (!defaultStartDate || !defaultEndDate)
		return true;
	var result = compareDateValue(
			defaultStartDate,
			startDate,
			'Rooms Available Between '
					+ defaultStartDate
					+ ' and '
					+ defaultEndDate
					+ ' only. You have selected a date outside this range. Please select new dates.');
	if (result)
		result = compareDateValue(
				startDate,
				defaultEndDate,
				'Rooms Available Between '
						+ defaultStartDate
						+ ' and '
						+ defaultEndDate
						+ ' only. You have selected a date outside this range. Please select new dates.');

	return result;
}

function checkEndDate(endDate, defaultStartDate, defaultEndDate) {
	// alert("check "+defaultStartDate+" "+defaultEndDate);
	if (!defaultStartDate || !defaultEndDate)
		return;
	// alert("check "+defaultStartDate+" "+defaultEndDate);
	var result = compareDateValue(
			endDate,
			defaultEndDate,
			'Rooms Available Between '
					+ defaultStartDate
					+ ' and '
					+ defaultEndDate
					+ ' only. You have selected a date outside this range. Please select new dates.');
	if (result)
		result = compareDateValue(
				defaultStartDate,
				endDate,
				'Rooms Available Between '
						+ defaultStartDate
						+ ' and '
						+ defaultEndDate
						+ ' only. You have selected a date outside this range. Please select new dates.');
	return result;
}


function disableDate(d)
{
	//console.debug("disable date");
	tdy = new Date();
	diff = d - tdy;
	dys = Math.round(diff/(1000*60*60*24));
	if (dys > -1)
		return false;
	return true;
}


jQuery(document).ready(function(){
	if(jQuery('.trigger').length > 0) 
	{
		jQuery('.trigger a').click(function()
		{

            jQuery(this).parent().parent().parent().parent().next('offer_tr_cnt').children('.offer_td_cnt').children('.offer_cnt').slideToggle('slow');

            //close other opened items
            //jQuery(".cnt").not(jQuery(this).parent().next('tr_cnt').children('.td_cnt').children('.cnt')).slideUp('fast');
           // jQuery(".offer_cnt").not(jQuery(this).parent().next('tr_cnt').children('.td_cnt').children('.cnt')).slideUp('fast');

            if (jQuery(this).parent().hasClass('open'))
			{
				jQuery(this).parent().removeClass('open');
				jQuery(this).parent().addClass('closeh');
                jQuery(this).parent().parent().parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideDown(500, function(){
                    jQuery(this).focus()
                });
                if(jQuery(this).hasClass('linkmore')) {
                	jQuery(this).html('<icon class="fa fa-chevron-right smallfont"></icon>'+Joomla.JText._('LNG_LESS_DETAILS'));
                }
				return false;
			} else {
				jQuery(this).parent().removeClass('closeh');
				jQuery(this).parent().addClass('open');
                jQuery(this).parent().parent().parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideUp(250);
				jQuery(this).children('room_expand').removeClass('expanded');

				if(jQuery(this).hasClass('linkmore')) {
					jQuery(this).html('<icon class="fa fa-chevron-right smallfont"></icon>'+Joomla.JText._('LNG_MORE_DETAILS'));
				}
                return false;
			}
        });
        jQuery('.triggerOffer a').click(function()
        {
            if (jQuery(this).parent().hasClass('open'))
            {
                jQuery(this).parent().removeClass('open');
                jQuery(this).parent().addClass('closeh');
                jQuery(this).html('<icon class="fa fa-chevron-right smallfont"></icon>'+Joomla.JText._('LNG_LESS_DETAILS'));
                return false;
            } else {
                jQuery(this).parent().removeClass('closeh');
                jQuery(this).parent().addClass('open');
                jQuery(this).html('<icon class="fa fa-chevron-right smallfont"></icon>'+Joomla.JText._('LNG_MORE_DETAILS'));
                return false;
            }
        });
        jQuery('button.trigger').click(function()
        {
            jQuery(".cnt").not(jQuery(this).parent().parent().parent().next('tr_cnt').children('.td_cnt').children('.cnt')).slideUp('fast');
            if (jQuery(this).hasClass('open'))
            {
                jQuery(this).removeClass('open');
                jQuery(this).addClass('closeh');
                jQuery(this).parent().parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideDown(500);
                return false;
            } else {
                jQuery(this).removeClass('closeh');
                jQuery(this).addClass('open');
                jQuery(this).parent().parent().parent().next('.tr_cnt').children('.td_cnt').children('.cnt').slideUp(250);
                return false;
            }
        });

	showRoomCalendars();
	checkReservationPendingPayments();
	}
});