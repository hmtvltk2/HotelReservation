function showChangeDates(){
	jQuery.blockUI({ message: jQuery('#change-dates'), css: {
		top:  50 + 'px', 
		width:'80%',
        left: (jQuery(window).width() - 600) /2 + 'px',
		'max-width': '600px', 
		backgroundColor: '#fff'}}	);
	jQuery('.blockOverlay').attr('title','Click to unblock').click(function(){jQuery.unblockUI( {css: {overflow:'scroll'}});jQuery('html, body').css('overflow','scroll');});
	jQuery('.blockUI.blockMsg').center();
	jQuery('html, body').css('overflow','hidden');
}

function changeDates(){
	if(jQuery("#start_date_i").val()=="" || jQuery("#end_date_i").val()==""){
		alert("Please select dates");
		return false;
	}
		
	jQuery("#start_date").val(jQuery("#start_date_i").val());
	jQuery("#end_date").val(jQuery("#end_date_i").val());
	jQuery("#update_price_type").val(jQuery("#change-dates input[type='radio']:checked").val());
	
	Joomla.submitbutton('reservation.apply');
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
	jQuery.post(baseUrl, postData, processAddRoomResult);
}

function processAddRoomResult(responce){
	var xml = responce;
	jQuery("<div>" + xml + "</div>").find('answer').each(function()
	{
		
		jQuery("#reservation-rooms").append(jQuery(this).attr('content_records'));
		jQuery("#current").val(parseInt(jQuery("#current").val())+1);
	});
}

function addOffer(){
	
}

function removeRoom(id){
	jQuery("#"+id).remove();
	var current =  jQuery("#current").val();
	if(current>1)
		jQuery("#current").val(jQuery("#current").val()-1);
	shiftCurrentItems(id,current-1);
	
}

function shiftCurrentItems(deletedId,current){
	console.debug(current);
	var res = deletedId.split("-");
	console.debug(deletedId);
	if(res[2]<current){//shift current values in all items
		jQuery("input[name = 'reservedItem[]']").each(function(){//rooms
			valArr = jQuery(this).val();
			console.debug(valArr);
			tmp = valArr.split("|");
			if(tmp[2]>res[2]){
				tmp[2] = tmp[2]-1;
				tmpVal = tmp.join("|");
				jQuery(this).val(tmpVal);
				console.debug("room final "+tmpVal);
			}
		});
		
		jQuery("input[name = 'extraOptionIds[]']").each(function(){//extraoptions
			valArr = jQuery(this).val();
			tmp = valArr.split("|");
			if(tmp[2]>res[2]){
				tmp[2] = tmp[2]-1;
				tmpVal = tmp.join("|");
				jQuery(this).val(tmpVal);
				jQuery("#persons-"+ tmp[3]).attr("name","extra-option-persons-"+ tmp[3]+"-"+tmp[2]);
				jQuery("#days-"+ tmp[3]).attr("name","extra-option-days-"+ tmp[3]+"-"+tmp[2]);
			}
		});
	}
}

function validateForm(){
	if(jQuery(".roomrate").length==0){
		alert("Please add at least one room")
		return false;
	}
	return true;
}

function setCustomPrice(){
	jQuery("#update_price_type").val('2'); //set custom price to be considered
}