function addSelection(id){
	var output = jQuery("#room_ids").val();
	if(output){
		output = output.join(', ');
		output = output + ","+id;
	}else{
		output = id;
	}
	//console.debug(output);
	changeSelection(output);
}		

function removeSelection(id){
	
	var output = jQuery("#room_ids").val();
	if(output)
		output = output.join(', ');
	//console.debug(output);
	changeSelection(output);
}	


function changeSelection(roomIds){
	
	var offerIds = jQuery("#offer_ids").val();
	if(offerIds){
		offerIds = offerIds.join(', ');
	}
	var postParameters='';
	postParameters +="&roomIDs[]="+roomIds; 
	postParameters +="&offerIDs[]="+offerIds; 
//	console.debug(postParameters);
	
	var postData='&task=extraoption.updateOffers'+postParameters;
	jQuery.post(baseUrl, postData, processChangeSelection);
}

function processChangeSelection(responce){
	
	var xml =  jQuery.trim(responce);
	//alert(xml);
	jQuery(xml).find('answer').each(function()
	{
		jQuery("#offers-holder").html(jQuery(this).attr('content_records'));
		jQuery("select#offer_ids").selectList({ 
			 sort: true,
			 classPrefix: 'offer_ids'
 		});
	});
}
