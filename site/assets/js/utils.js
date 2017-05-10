function validateField( objField, type, accept_empty, msg )
{
	if( accept_empty == null )
		accept_empty = true;
	if( msg == null )
		msg = '';
	if( objField == null )
		return false;
	if( accept_empty == true && objField.value == '')
		return true;
	
	var ret = true;

	if( type == 'numeric' )
	{
		ret = isNumeric(objField.value);
	}
	else if( type == 'string' )
	{
		ret = objField.value == ''? false : true ;
	}
	else if( type =='email')
	{
		var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
		ret = filter.test(objField.value); 
	}
	else if( type =='date')
	{
		return checkDate(objField);
	}
	else if( type == 'radio' || type == 'checkbox' )
	{
		if( objField.length == null )
		{
			
			ret = objField.checked ? true : false  ;
		}
		else
		{
			
			var nLen  	= objField.length;
			var nSel	= false;
			ret 		= false;
			
			for( i = 0; i < nLen; i ++ )
			{
				if( objField[i].checked)
				{
					ret = true;
					break;
				}
			}
		}
		
	}
	
	if( ret == false && msg != '' )
	{
		alert(msg);
		if( objField.focus )
			objField.focus();
	}
	//myRegExpPhoneNumber = /(\d\d\d) \d\d\d-\d\d\d\d/
	return ret;
	
}



function calendarFormat(dateFormat) {
    switch(dateFormat){
        case 'Y-m-d':
            return 'yyyy-mm-dd';
            break;
        case 'm/d/Y':
            return 'mm/dd/yyyy';
            break;
        case 'd-m-Y':
            return 'dd-mm-yyyy';
            break;
    }
}




function isNumeric(str)
{
	return parseFloat(str)==str;

	//mystring = str;
	//alert(str);
	//if (mystring.match(/^\d+$|^\d+\.\d{2}$/ ) ) 
//	{
	//	return true;
	//}
	//return false;

}

function classOf(o) 
{
	if (undefined === o) 
		return "Undefined";
	if (null === o) 
		return "Null";
	return {}.toString.call(o).slice(8, -1);
}

function isArray(obj) 
{
	//alert(obj.constructor);
	//returns true is it is an array
	if (obj.constructor = Array )
		return false;
	else
		return true;
}


Date.prototype.getMonthName = function() 
{
	var m = ['January','February','March','April','May','June','July','August','September','October','November','December'];
	return m[this.getMonth()];
} 
Date.prototype.getDayName = function() 
{
	var d = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	return d[this.getDay()];
}
function daysInMonth(year, month) 
{
	//alert(month + " " + year);
	var dd = new Date(year, month, 0);
	return dd.getDate();

} 

function checkDate(field) 
{ 
	var allowBlank 	= !true; 
	var minYear 	= 1902; 
	var maxYear 	= (new Date()).getFullYear(); 
	var errorMsg 	= ""; 
	
	// regular expression to match required date format 
	re = /^(\d{1,4})\-(\d{1,2})\-(\d{2})$/; 
	re2 = /^\d{1,2}\-\d{1,2}\-\d{4}$/; 
	re3 = /^\d{1,2}\/\d{1,2}\/\d{4}$/; 
	
	if(field.value != '') 
	{ 
		
		if(regs = field.value.match(re)) 
		{ 
			if(regs[3] < 1 || regs[3] > 31) 
			{ 
				errorMsg = "Invalid value for day: " + regs[3]; 
			} 
			else if(regs[2] < 1 || regs[2] > 12) 
			{ 
				errorMsg = "Invalid value for month: " + regs[2]; 
			} 
			else if(regs[1] < minYear /*|| regs[1] > maxYear*/) 
			{ 
				errorMsg = "Invalid value for year: " + regs[1];//+ " - must be between " + minYear + " and " + maxYear; 
			} 
		}
		else if (regs= field.value.match(re2))
		{ 
			if(regs[1] < 1 || regs[1] > 31) 
			{ 
				errorMsg = "Invalid value for day: " + regs[3]; 
			} 
			else if(regs[2] < 1 || regs[2] > 12) 
			{ 
				errorMsg = "Invalid value for month: " + regs[2]; 
			} 
			else if(regs[3] < minYear /*|| regs[1] > maxYear*/) 
			{ 
				errorMsg = "Invalid value for year: " + regs[1];//+ " - must be between " + minYear + " and " + maxYear; 
			} 
		}
		else if (regs= field.value.match(re3))
		{ 
			if(regs[1] < 1 || regs[1] > 31) 
			{ 
				errorMsg = "Invalid value for day: " + regs[3]; 
			} 
			else if(regs[0] < 1 || regs[0] > 12) 
			{ 
				errorMsg = "Invalid value for month: " + regs[0]; 
			} 
			else if(regs[2] < minYear /*|| regs[1] > maxYear*/) 
			{ 
				errorMsg = "Invalid value for year: " + regs[1];//+ " - must be between " + minYear + " and " + maxYear; 
			} 
		}	
		else{
			errorMsg = "Invalid date format: " + field.value; 
		} 
	} 
	else if(!allowBlank) 
	{ 
		errorMsg = "Please select a date for your reservation!"; 
	} 
	
	if(errorMsg != "") 
	{ 
		alert(errorMsg);
		//exit(0);
		return false; 
	} 
	return true; 
}


function compareDate(field1, field2,msg) 
{ 
	var ret = false;
	// regular expression to match required date format 
	re = /^(\d{1,4})\-(\d{1,2})\-(\d{2})$/; 
	re2 = /^\d{1,2}\-\d{1,2}\-\d{4}$/;
	re3 = /^\d{1,2}\/\d{1,2}\/\d{4}$/; 

	if(field1.value != '' && field2.value != '') 
	{ 
		if(regs1 = field1.value.match(re)){
			regs1 = field1.value.split('-');
			regs2 = field2.value.split('-');
			if(regs1 &&	regs2) 
			{ 
				date1 = new Date(regs1[0],regs1[1]-1,regs1[2]);
				date2 = new Date(regs2[0],regs2[1]-1,regs2[2]);
				ret = date1.getTime() < date2.getTime();
			}
		}else if(regs1 = field1.value.match(re2)){
			regs1 = field1.value.split('-');
			regs2 = field2.value.split('-');
			if(regs1 &&	regs2) 
			{ 
				date1 = new Date(regs1[2],regs1[1]-1,regs1[0]);
				date2 = new Date(regs2[2],regs2[1]-1,regs2[0]);
				ret = date1.getTime() < date2.getTime();
			}
		} 
		else if(regs1 = field1.value.match(re3)){
			regs1 = field1.value.split('/');
			regs2 = field2.value.split('/');
			if(regs1 &&	regs2) 
			{ 
				date1 = new Date(regs1[2],regs1[0]-1,regs1[1]);
				date2 = new Date(regs2[2],regs2[0]-1,regs2[1]);
				ret = date1.getTime() < date2.getTime();
			}
		} 
	} 
	
	if( ret == false && msg != '' )
	{
		alert(msg);
		if( field1.focus )
			field1.focus();
	}
	
	return ret;

}


function deleteReservedItems()
{
	//remove from all elements
	var arrFields 		= new Array();
	arrFields[ 0] 		= 'items_reserved';
	arrFields[ 1] 		= 'package_ids';
	arrFields[ 2] 		= 'package_day';
	arrFields[ 3] 		= 'itemPackageNumbers';
	arrFields[ 4] 		= 'arrival_option_ids';
	arrFields[ 5] 		= 'airport_airline_ids';
	arrFields[ 6] 		= 'airport_transfer_type_ids';
	arrFields[ 7] 		= 'airport_transfer_dates';
	arrFields[ 8] 		= 'airport_transfer_time_hours';
	arrFields[ 9] 		= 'airport_transfer_time_mins';
	arrFields[10] 		= 'airport_transfer_flight_nrs';
	arrFields[11] 		= 'airport_transfer_guests';

	
	for( i = 0; i < arrFields.length; i ++ )
	{
		var crt = 1;
		jQuery("input[name=\""+arrFields[i]+"[]\"]").each(function()
		{
		
			jQuery(this).remove();
		
		});
	}
}

function showOfferDesc(descClass,linkOnClick,less,more,offerlink) {

    if(jQuery(descClass).is(':hidden')){
        linkOnClick.parent().parent().parent().next('offer_tr_cnt').children('.offer_td_cnt').children('.offer_cnt').slideToggle('slow');

        jQuery(offerlink).html('<icon class="fa fa-chevron-right">'+ less+'</icon>');

        jQuery('.triggerOffer a.linkmore').html('<icon class="fa fa-chevron-right">'+more+'</icon>');
        jQuery('.trigger a.linkmore').html('<icon class="fa fa-chevron-right">'+more+'</icon>');

        jQuery(".offer_cnt").not(linkOnClick.next('offer_tr_cnt').children('.offer_td_cnt').children('.offer_cnt')).slideUp('fast');
        jQuery(".cnt").not(linkOnClick.next('offer_tr_cnt').children('.offer_td_cnt').children('.offer_cnt')).slideUp('fast');

        jQuery(descClass).show('slow', function () {
            jQuery(this).focus();
        });
    }else{
        jQuery(descClass).hide('fast');
    }
}

function respondCanvas(c){
    var container = jQuery(c).parent();

    jQuery(window).resize( respondCanvas );

    function respondCanvas(){
        c.attr('width', jQuery(container).width() ); //max width
        c.attr('height', jQuery(container).height() ); //max height
        generateChart();
    }

    respondCanvas();
}

function respondCanvasDashboard(c,value){
    var container = jQuery(c).parent();

    //jQuery(window).resize( respondCanvas );

    function respondCanvas(){
        c.attr('width', jQuery(container).width() ); //max width
        c.attr('height', jQuery(container).height() ); //max height
        generateChart(value);
    }

    respondCanvas();
}

function fillInAddress(place,component_form,selectedOption) {
    for (var component in component_form) {
        switch (component){
            case 'route':
                component = 'address';
                var address  = document.getElementById(component);
                if(typeof (address) != 'undefinded' && address != null) {
                    address.value = "";
                    address.disabled = false;
                }
                break;
            case 'administrative_area_level_1':
                component = 'state_name';
                var state_name  = document.getElementById(component);
                if(typeof (state_name) != 'undefinded' && state_name != null) {
                    state_name.value = "";
                    state_name.disabled = false;
                }
                break;
            case 'locality':
                component = 'city';
                var empty_city  = document.getElementById(component);
                if(typeof (empty_city) != 'undefinded' && empty_city != null) {
                    empty_city.value = "";
                    empty_city.disabled = false;
                }
                break;
            default:
                var defaultItem = document.getElementById(component);
                if(typeof (defaultItem) != 'undefinded' && defaultItem != null) {
                    defaultItem.value = "";
                    defaultItem.disabled = false;
                }
                break;
        }
    }

    for (var j = 0; j < place.address_components.length; j++) {
        var att = place.address_components[j].types[0];

        if (component_form[att] &&  typeof component_form[att] != 'undefined' ) {
            var val = place.address_components[j][component_form[att]];
            var street_numberValue;
            switch (att){
                case 'street_number':
                    if(val) {
                        street_numberValue = ", " + val;
                    }
                    break;
                case 'route':
                    var address = document.getElementById('address');
                    street_numberValue = street_numberValue?street_numberValue:'';
                    address.value = val + street_numberValue;
                    break;
                case 'administrative_area_level_1':
                    var state = document.getElementById('state_name');
                    if(typeof (state) != 'undefined' && state != null) {
                        state.value = val;
                    }
                    break;
                case 'locality':
                    var city = document.getElementById('city');
                    if(typeof(city) != 'undefined' && city != null) {
                        city.value = val;
                    }
                    break;
                case selectedOption:
                    var country = document.getElementById(selectedOption);
                    if(typeof (country) != 'undefined' && country != null) {
                        for (var c = 0; c < country.length; c++) {
                            if (country.options[c].value === val) {
                                country.options[c].selected = true;
                                country.options[c].setAttribute('selected', "selected");
                            }
                        }
                    }
                    break;
                case 'postal_code':
                    var postal_code = document.getElementById(att);
                    if(typeof (postal_code) != 'undefinded' && postal_code != null) {
                        postal_code.value = val;
                    }
                    break;
            }
        }
    }
}

function initializeAutocomplete() {
    var autocomplete_address = document.getElementById('autocomplete_address');
    var autocomplete;
    var component_form = {
        'street_number': 'short_name',
        'route': 'long_name',
        'locality': 'long_name',
        'administrative_area_level_1': 'long_name',
        'country': 'long_name',
        'postal_code': 'short_name'
    };

    if (typeof(autocomplete_address) != 'undefined' && autocomplete_address != null) {
        autocomplete = new google.maps.places.Autocomplete(autocomplete_address, {types: ['geocode']});
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            fillInAddress(place, component_form, 'country');
        });
    }
}

function checkGoogleObj() {
    var autocomplete_address = document.getElementById('autocomplete_address');
    if (typeof(autocomplete_address) != 'undefined' && autocomplete_address != null) {
        if (typeof google === 'object' && typeof google.maps === 'object') {
            //library already called
            google.maps.event.addDomListener(window, 'load', initializeAutocomplete);
        } else {
            var head = document.getElementsByTagName('head')[0];
            var script = document.createElement("script");
            script.type = 'text/javascript';
            script.charset = 'utf-8';
            script.src = "https://maps.google.com/maps/api/js?sensor=true&libraries=places";
            script.defer = true;
            script.async = true;
            script.onload = function () {
                google.maps.event.addDomListener(window, 'load', initializeAutocomplete);
            };
            head.appendChild(script);
        }
    }
}