//---start theme ----------
function showManageThemes(){
	resetThemes();
	jQuery("#themes").children().each(function(index) {
		if(jQuery(this).val())
			addNewTheme(jQuery(this).val(), jQuery(this).text());
	});
	jQuery('#frmThemesFormSubmitWait').hide();
	jQuery.blockUI({ message: jQuery('#showThemesNewFrm'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'} });
	jQuery('.blockUI.blockMsg').center();
}

function resetThemes(){
	jQuery("#theme-container").empty();
}

function addNewTheme(id, value){
	
	var count = jQuery("#theme-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'themeRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'themeNames[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('size',		'32');
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'theme_error_msg_test'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '12px');
	img_del.setAttribute('width', '12px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("themeRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var themeContainer =jQuery("#theme-container");
	themeContainer.append(newRow);
}



function saveThemes(formname){
	var error_flag=false;
	var postParameters='';
	jQuery("#theme-container :input").each(function(index) {
		var $id = '#theme_error_msg_test'+(index+1);
		//console.debug(jQuery(this).val());
		//error_flag=true;
		if(!jQuery(this).val()){
			jQuery($id).html('This is a required field.');
			jQuery($id).show();
			error_flag=true;
		}else{
			jQuery($id).html('');
			jQuery($id).hide();
		}
		postParameters +="&"+jQuery(this).attr('name')+'='+jQuery(this).val()+"&themeIds[]="+jQuery(this).attr('id'); 
	});
	postParameters +="&offerId="+jQuery("#offer_id").val();
	//alert(postParameters);
	
	if(error_flag){
		return false;
	}
	else{
		var postData='&task=offer.updateThemes'+postParameters;
		//alert(baseUrl + postData);
		jQuery.post(baseUrl, postData, processSaveThemesResult);
		jQuery('#frmThemesFormSubmitWait').show();
		
	}
}		

function processSaveThemesResult(responce){
	
	var xml = responce;
	jQuery('#frmThemesFormSubmitWait').hide();
	jQuery("<div>" + xml + "</div>").find('answer').each(function()
	{
		if( jQuery(this).attr('error') == '1' )
		{
			jQuery('#frm_error_msg_theme').className='text_error';
			jQuery('#frm_error_msg_theme').html(jQuery(this).attr('errorMessage'));
			jQuery('#frm_error_msg_theme').show();

		}
		else if( jQuery(this).attr('error') == '0' )
		{
			jQuery.unblockUI();
			var success_msg= jQuery(this).attr('message');
            popUpMessage(success_msg);
			jQuery("#theme-holder").html(jQuery(this).attr('content_records'));
			jQuery("select#themes").chosen();//call jquery chosen in joomla library
		}
	});
}


//-------end theme----------------------------

//------- offer vouchers------
function addNewVoucher(id, value){
	
	var count = jQuery("#voucher-container").children().length+1;
	var newRow 	= document.createElement('div');
	newRow.setAttribute('class',		'form_row');
	newRow.setAttribute('id',		'voucherRow'+count);
	
	var outerDiv = document.createElement('div');
	outerDiv.setAttribute('class',		'outer_input');
	
	
	var newInput = document.createElement('input');
	newInput.setAttribute('type',		'text');
	newInput.setAttribute('name',		'vouchers[]');
	newInput.setAttribute('id',			id);
	newInput.setAttribute('size',		'32');
	newInput.setAttribute('maxlength',	'128');
	newInput.setAttribute('value', value);
	
	var newSpan 		= document.createElement('span');
	newSpan.setAttribute('id',		'voucher_error_msg'+count);
	newSpan.setAttribute('class',		'error_msg errormsg');
	newSpan.setAttribute('style',		'display:none');
	
	var img_del		 	= document.createElement('img');
	img_del.setAttribute('src', deleteImagePath);
	img_del.setAttribute('alt', 'Delete option');
	img_del.setAttribute('height', '12px');
	img_del.setAttribute('width', '12px');
	img_del.setAttribute('align', 'left');
	img_del.setAttribute('onclick', 'removeRow("voucherRow'+count+'")');
	img_del.setAttribute('style', "cursor: pointer; margin:3px;");
	
	outerDiv.appendChild(newInput);
	outerDiv.appendChild(newSpan);
	newRow.appendChild(outerDiv);
	newRow.appendChild(img_del);
	
	var clearDiv 	= document.createElement('div');
	clearDiv.setAttribute('class',		'clear');
	
	var voucherContainer =jQuery("#voucher-container");
	voucherContainer.append(newRow);
	voucherContainer.append(clearDiv);
	
}
//---------end vouchers-------
