//---start facility ----------
function showManageFacilities(){
	resetFacilities();
	jQuery("select#facilities").children().each(function(index) {
		if(jQuery(this).val())
			addNewFacility(jQuery(this).val(), jQuery(this).text());
	});
	jQuery('#frmFacilitiesFormSubmitWait').hide();
	jQuery.blockUI({ message: jQuery('#showFacilitiesNewFrm'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'} });
	jQuery('.blockUI.blockMsg').center();

}

function resetFacilities(){
	jQuery("#facility-container").empty();
}

function addNewFacility(id, value){
	
	var count = jQuery("#facility-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'facilityRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'facilityNames[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'facility_error_msg'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '15px');
	img_del.setAttribute('width', '15px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("facilityRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var facilityContainer =jQuery("#facility-container");
	facilityContainer.append(newRow);
}


function saveFacilities(formname){
	var error_flag=false;
	var postParameters='';
	jQuery("#facility-container :input").each(function(index) {
		$id = '#facility_error_msg'+(index+1);
		if(!jQuery(this).val()){
			jQuery($id).html('This is a required field.');
			jQuery($id).show();
			error_flag=true;
		}else{

			jQuery($id).html('');
			jQuery($id).hide();
		}
		postParameters +="&"+jQuery(this).attr('name')+'='+jQuery(this).val()+"&facilityIds[]="+jQuery(this).attr('id');
	});
	postParameters +="&hotelId="+jQuery("#hotel_id").val();
	//alert(postParameters);
	
	if(error_flag){
		return false;
	}
	else{
		var postData='&task=hotel.updateFacilities'+postParameters;
		//alert(baseUrl + postData);
		jQuery.post(baseUrl, postData, processSaveFacilitiesResult);
		jQuery('#frmFacilitiesFormSubmitWait').show();
		
	}
}		

function processSaveFacilitiesResult(responce){
	var xml = jQuery.trim(responce);
	jQuery('#frmFacilitiesFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if( jQuery(this).attr('error') == '1' )
		{
			jQuery('#frm_error_msg_facility').className='text_error';
			jQuery('#frm_error_msg_facility').html(jQuery(this).attr('errorMessage'));
			jQuery('#frm_error_msg_facility').show();

		}
		else if( jQuery(this).attr('error') == '0' )
		{
			jQuery.unblockUI();
			var success_msg= jQuery(this).attr('message');
			popUpMessage(jQuery(this).attr('mesage'));
			jQuery("#facility-holder").html(jQuery(this).attr('content_records'));
			jQuery("select#facilities").chosen();//call jquery chosen in joomla library
		}
	});
}


//-------end facility----------------------------

//---start type ----------
function showManageTypes(){
	resetTypes();
	jQuery(".types-select").children().each(function(index) {
		if(jQuery(this).val())
			addNewType(jQuery(this).val(), jQuery(this).text());
	});
	jQuery('#frmTypesFormSubmitWait').hide();
	jQuery.blockUI({ message: jQuery('#showTypesNewFrm'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'} });
	jQuery('.blockUI.blockMsg').center();
}

function resetTypes(){
	jQuery("#types-container").empty();
}

function addNewType(id, value){
	
	var count = jQuery("#types-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'typeRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'typeNames[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'type_error_msg'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '15px');
	img_del.setAttribute('width', '15px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("typeRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var typeContainer =jQuery("#types-container");
	typeContainer.append(newRow);
}


function saveTypes(formname){
	var error_flag=false;
	var postParameters='';
	jQuery("#types-container :input").each(function(index) {
		$id = '#type_error_msg'+(index+1);
		if(!jQuery(this).val()){
			jQuery($id).html('This is a required field.');
			jQuery($id).show();
			error_flag=true;
		}else{

			jQuery($id).html('');
			jQuery($id).hide();
		}
		postParameters +="&"+jQuery(this).attr('name')+'='+jQuery(this).val()+"&typeIds[]="+jQuery(this).attr('id');
	});
	postParameters +="&hotelId="+jQuery("#hotel_id").val();
	//alert(postParameters);
	
	if(error_flag){
		return false;
	}
	else{
		var postData='&task=hotel.updateTypes'+postParameters;
		//alert(baseUrl + postData);
		jQuery.post(baseUrl, postData, processSaveTypesResult);
		jQuery('#frmTypesFormSubmitWait').show();
		
	}
}		

function processSaveTypesResult(responce){
	
	var xml = jQuery.trim(responce);
	//alert(xml);
	jQuery('#frmTypesFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if( jQuery(this).attr('error') == '1' )
		{
			jQuery('#frm_error_msg_types').html(jQuery(this).attr('errorMessage'));
			jQuery('#frm_error_msg_types').show();

		}
		else if( jQuery(this).attr('error') == '0' )
		{
			jQuery.unblockUI();
			var success_msg= jQuery(this).attr('message');
			popUpMessage(jQuery(this).attr('mesage'));
			jQuery("#types-holder").html(jQuery(this).attr('content_records'));
			jQuery("select#types").chosen();//call jquery chosen in joomla library
		}
	});
}


//-------end type----------------------------

//---start accommodation  types----------
function showManageAccommodationTypes(){
	resetAccommodationTypes();
	jQuery("select#accommodationtypes").children().each(function(index) {
		if(jQuery(this).val())
			addNewAccommodationType(jQuery(this).val(), jQuery(this).text());
	});
	jQuery('#frmAccommodationTypesFormSubmitWait').hide();
	jQuery.blockUI({ message: jQuery('#showAccommodationTypesNewFrm'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'} });
	jQuery('.blockUI.blockMsg').center();
}

function resetAccommodationTypes(){
	jQuery("#accommodationtypes-container").empty();
}

function addNewAccommodationType(id, value){
	
	var count = jQuery("#accommodationtypes-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'typeRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'accommodationtypeNames[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'accommodationtype_error_msg'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '15px');
	img_del.setAttribute('width', '15px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("typeRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var typeContainer =jQuery("#accommodationtypes-container");
	typeContainer.append(newRow);
}


function saveAccommodationTypes(formname){
	var error_flag=false;
	var postParameters='';
	jQuery("#accommodationtypes-container :input").each(function(index) {
		$id = '#accommodationtype_error_msg'+(index+1);
		if(!jQuery(this).val()){
			jQuery($id).html('This is a required field.');
			jQuery($id).show();
			error_flag=true;
		}else{

			jQuery($id).html('');
			jQuery($id).hide();
		}
		postParameters +="&"+jQuery(this).attr('name')+'='+jQuery(this).val()+"&accommodationtypeIds[]="+jQuery(this).attr('id');
	});
	postParameters +="&hotelId="+jQuery("#hotel_id").val();
	//alert(postParameters);
	
	if(error_flag){
		return false;
	}
	else{
		var postData='&task=hotel.updateAccommodationTypes'+postParameters;
		//alert(baseUrl + postData);
		jQuery.post(baseUrl, postData, processSaveAccommodationTypesResult);
		jQuery('#frmAccommodationTypesFormSubmitWait').show();
		
	}
}		

function processSaveAccommodationTypesResult(responce){
	
	var xml = jQuery.trim(responce);
	jQuery('#frmAccommodationTypesFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if( jQuery(this).attr('error') == '1' )
		{
			jQuery('#frm_error_msg_accommodationtypes').html(jQuery(this).attr('errorMessage'));
			jQuery('#frm_error_msg_accommodationtypes').show();

		}
		else if( jQuery(this).attr('error') == '0' )
		{
			jQuery.unblockUI();
			var success_msg= jQuery(this).attr('message');
			popUpMessage(jQuery(this).attr('mesage'));
			jQuery("#accommodationtypes-holder").html(jQuery(this).attr('content_records'));
			jQuery("select#accommodationtypes").chosen();//call jquery chosen in joomla library
		}
	});
}


//-------end type----------------------------


//---start environment ----------
function showManageEnvironments(){
	resetEnvironments();
	jQuery("select#environments").children().each(function(index) {
		if(jQuery(this).val())
			addNewEnvironment(jQuery(this).val(), jQuery(this).text());
	});
	jQuery('#frmEnvironmentsFormSubmitWait').hide();
	jQuery.blockUI({ message: jQuery('#showEnvironmentsNewFrm'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'} });
	jQuery('.blockUI.blockMsg').center();
}

function resetEnvironments(){
	jQuery("#environments-container").empty();
}

function addNewEnvironment(id, value){

	var count = jQuery("#environments-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'environmentRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'environmentNames[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'environment_error_msg'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '15px');
	img_del.setAttribute('width', '15px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("environmentRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var environmentContainer =jQuery("#environments-container");
	environmentContainer.append(newRow);
}


function saveEnvironments(formname){
	var error_flag=false;
	var postParameters='';
	jQuery("#environments-container :input").each(function(index) {
		$id = '#environment_error_msg'+(index+1);
		if(!jQuery(this).val()){
			jQuery($id).html('This is a required field.');
			jQuery($id).show();
			error_flag=true;
		}else{

			jQuery($id).html('');
			jQuery($id).hide();
		}
		postParameters +="&"+jQuery(this).attr('name')+'='+jQuery(this).val()+"&environmentIds[]="+jQuery(this).attr('id');
	});
	postParameters +="&hotelId="+jQuery("#hotel_id").val();
	//alert(postParameters);
	
	if(error_flag){
		return false;
	}
	else{
		var postData='&task=hotel.updateEnvironments'+postParameters;
		//alert(baseUrl + postData);
		jQuery.post(baseUrl, postData, processSaveEnvironmentsResult);
		jQuery('#frmEnvironmentsFormSubmitWait').show();
		
	}
}		

function processSaveEnvironmentsResult(responce){
	
	var xml = jQuery.trim(responce);
	jQuery('#frmEnvironmentsFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if( jQuery(this).attr('error') == '1' )
		{
			jQuery('#frm_error_msg_environments').className='text_error';
			jQuery('#frm_error_msg_environments').html(jQuery(this).attr('errorMessage'));
			jQuery('#frm_error_msg_environments').show();

		}
		else if( jQuery(this).attr('error') == '0' )
		{
			jQuery.unblockUI();
			var success_msg= jQuery(this).attr('message');
			popUpMessage(jQuery(this).attr('mesage'));
			jQuery("#environments-holder").html(jQuery(this).attr('content_records'));
			//jQuery("select#environments").selectList({
			//	 sort: true,
			//	 classPrefix: 'environments'
	 		//});
			jQuery("select#environments").chosen();//call jquery chosen in joomla library
		}
	});
}


//-------end environment----------------------------


//---start regions ----------
function showManageRegions(){
	resetRegions();
	jQuery("select#regions").children().each(function(index) {
		if(jQuery(this).val())
			addNewRegion(jQuery(this).val(), jQuery(this).text());
	});
	jQuery('#frmRegionsFormSubmitWait').hide();
	jQuery.blockUI({ message: jQuery('#showRegionsNewFrm'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'} });
	jQuery('.blockUI.blockMsg').center();
}

function resetRegions(){
	jQuery("#regions-container").empty();
}

function addNewRegion(id, value){
	
	var count = jQuery("#regions-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'regionRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'regionNames[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'region_error_msg'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '15px');
	img_del.setAttribute('width', '15px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("regionRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var regionContainer =jQuery("#regions-container");
	regionContainer.append(newRow);
}


function saveRegions(formname){
	var error_flag=false;
	var postParameters='';
	jQuery("#regions-container :input").each(function(index) {
		$id = '#region_error_msg'+(index+1);
		if(!jQuery(this).val()){
			jQuery($id).html('This is a required field.');
			jQuery($id).show();
			error_flag=true;
		}else{

			jQuery($id).html('');
			jQuery($id).hide();
		}
		postParameters +="&"+jQuery(this).attr('name')+'='+jQuery(this).val()+"&regionIds[]="+jQuery(this).attr('id');
	});
	postParameters +="&hotelId="+jQuery("#hotel_id").val();
	//alert(postParameters);
	
	if(error_flag){
		return false;
	}
	else{
		var postData='&task=hotel.updateRegions'+postParameters;
		//alert(baseUrl + postData);
		jQuery.post(baseUrl, postData, processSaveRegionsResult);
		jQuery('#frmRegionsFormSubmitWait').show();
		
	}
}		

function processSaveRegionsResult(responce){
	
	var xml = jQuery.trim(responce);
	jQuery('#frmRegionsFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if( jQuery(this).attr('error') == '1' )
		{
			jQuery('#frm_error_msg_region').className='text_error';
			jQuery('#frm_error_msg_region').html(jQuery(this).attr('errorMessage'));
			jQuery('#frm_error_msg_region').show();

		}
		else if( jQuery(this).attr('error') == '0' )
		{
			jQuery.unblockUI();
			var success_msg= jQuery(this).attr('message');
			popUpMessage(jQuery(this).attr('mesage'));
			jQuery("#regions-holder").html(jQuery(this).attr('content_records'));
			jQuery("select#regions").chosen();//call jquery chosen in joomla library
		}
	});
}


//-------end region----------------------------


//---start paymentOption ----------
function showManagePaymentOptions(){
	resetPaymentOptions();
	jQuery("select#paymentOptions").children().each(function(index) {
		if(jQuery(this).val())
			addNewPaymentOption(jQuery(this).val(), jQuery(this).text());
	});
	jQuery('#frmPaymentOptionsFormSubmitWait').hide();
	jQuery.blockUI({ message: jQuery('#showPaymentOptionsNewFrm'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'} });
	jQuery('.blockUI.blockMsg').center();
}

function resetPaymentOptions(){
	jQuery("#paymentOption-container").empty();
}

function addNewPaymentOption(id, value){
	
	var count = jQuery("#paymentOption-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'paymentOptionRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'paymentOptionNames[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'paymentOption_error_msg'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '15px');
	img_del.setAttribute('width', '15px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("paymentOptionRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var paymentOptionContainer =jQuery("#paymentOption-container");
	paymentOptionContainer.append(newRow);
}


function savePaymentOptions(formname){
	var error_flag=false;
	var postParameters='';
	jQuery("#paymentOption-container :input").each(function(index) {
		$id = '#paymentOption_error_msg'+(index+1);
		if(!jQuery(this).val()){
			jQuery($id).html('This is a required field.');
			jQuery($id).show();
			error_flag=true;
		}else{

			jQuery($id).html('');
			jQuery($id).hide();
		}
		postParameters +="&"+jQuery(this).attr('name')+'='+jQuery(this).val()+"&paymentOptionIds[]="+jQuery(this).attr('id');
	});
	postParameters +="&offerId="+jQuery("#offer_id").val();
	postParameters +="&hotelId="+jQuery("#hotel_id").val();
	//alert(postParameters);
	
	if(error_flag){
		return false;
	}
	else{
		var postData='&task=hotel.updatePaymentOptions'+postParameters;
		//alert(baseUrl + postData);
		jQuery.post(baseUrl, postData, processSavePaymentOptionsResult);
		jQuery('#frmPaymentOptionsFormSubmitWait').show();
		
	}
}		

function processSavePaymentOptionsResult(responce){
	
	var xml = jQuery.trim(responce);
	jQuery('#frmPaymentOptionsFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if( jQuery(this).attr('error') == '1' )
		{
			jQuery('#frm_error_msg_paymentOption').className='text_error';
			jQuery('#frm_error_msg_paymentOption').html(jQuery(this).attr('errorMessage'));
			jQuery('#frm_error_msg_paymentOption').show();

		}
		else if( jQuery(this).attr('error') == '0' )
		{
			jQuery.unblockUI();
			var success_msg= jQuery(this).attr('message');
			popUpMessage(jQuery(this).attr('mesage'));
			jQuery("#paymentOption-holder").html(jQuery(this).attr('content_records'));
			jQuery("select#paymentOptions").chosen();//call jquery chosen in joomla library
		}
	});
}


//-------end paymentOption----------------------------