<?php
/**
 * @copyright	Copyright (C) 2009-2012 CMSJunkie - All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHtml::_('behavior.combobox');
?>

<script>
    jQuery(document).ready(function () {
        <?php if(!checkUserAccess(JFactory::getUser()->id,"hotel_extra_info")){?>
        jQuery("#start_date, #end_date").attr('readonly','readonly');
        <?php } ?>
    });
</script>
<div id="rightprslide page-characteristics">
	<br style="font-size: 1px;" />
    <fieldset>
		<h4><?php echo JText::_( 'LNG_GENERAL_INFORMATION' ,true); ?></h4>
			<div style='display: none'>
				<div id='div_calendar' class='div_calendar'>
					<p>
					</p>
				</div>
			</div>
				<div class="admintable">
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_NAME'); ?>:</div>
						<div class="col column_8_of_12" ><input type="text" name="hotel_name"
							id="hotel_name" class="validate[required] text-input" value="<?php echo isset($this->item->hotel_name)?htmlspecialchars($this->item->hotel_name):'';?>"
							size=50 maxlength=255  />
						</div>
					</div>
                    <div class="section group">
                        <div   class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_ALIAS'); ?>:</div>
                        <div class="col column_8_of_12" ><input type="text" name="hotel_alias"
                              placeholder="Auto Generate"
                              id="hotel_alias" class=""
                              value="<?php echo isset($this->item->hotel_alias)?$this->item->hotel_alias:'';?>"
                              size=50 maxlength=255  />
                        </div>
                    </div>
					<div class="section group">
						<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_EMAIL'); ?>:</div>
						<div class="col column_8_of_12" ><input type="text" name="email" id="email"
							value='<?php echo isset($this->item->email)?$this->item->email:''?>' size=50 maxlength=80
							 class='validate[required,custom[email]]' />
						</div>
					</div>
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_TELEPHONE_NUMBER'); ?>:</div>
						<div class="col column_8_of_12"  ><input type="text" class="validate[required]" name="hotel_phone" id="hotel_phone"
							value='<?php echo isset($this->item->hotel_phone)?$this->item->hotel_phone:''?>' size=50 maxlength=80
							 />
						</div>
					</div>
					<?php
					if(JRequest::getVar('addhotel') == 0)
					{
						if ( checkUserAccess( JFactory::getUser()->id, "hotel_extra_info" ) )
						{ ?>

							<div class="section group">
								<div
									class="key labelFields col column_2_of_12"><?php echo JText::_( 'LNG_AVAILABLE', true ); ?></div>
								<div id="is_available" class="radio btn-group btn-group-yesno col column_9_of_12">
									<input
										style='float:none'
										type="radio"
										name="is_available"
										id="is_available0"
										value='1'
										<?php echo $this->item->is_available == true ? " checked " : "" ?>
									/>
									<label
										class="labelPerPerson"
										id="label_is_available0"
										for="is_available0"><?php echo JText::_( 'LNG_STR_YES', true ); ?></label>
									&nbsp;
									<input
										style='float:none'
										type="radio"
										name="is_available"
										id="is_available1"
										value='0'
										<?php echo $this->item->is_available == false ? " checked " : "" ?>
									/>
									<label
										class="labelNo"
										id="label_is_available1"
										for="is_available1"><?php echo JText::_( 'LNG_STR_NO', true ); ?></label>
								</div>
								<div>
									&nbsp;
								</div>
							</div>
							<div class="section group">
								<div
									class="key labelFields col column_2_of_12"><?php echo JText::_( 'LNG_STATUS', true ); ?></div>
								<div id="hotel_state" class="radio btn-group btn-group-yesno col column_9_of_12">
									<input
										style='float:none'
										type="radio"
										name="hotel_state"
										id="hotel_state0"
										value='1'
										<?php echo $this->item->hotel_state == true ? " checked " : "" ?>
										accesskey="Y"

									/>
									<label
										class="labelPerPerson"
										id="label_hotel_state0"
										for="hotel_state0"><?php echo JText::_( 'LNG_LIVE', true ); ?></label>
									&nbsp;
									<input
										style='float:none'
										type="radio"
										name="hotel_state"
										id="hotel_state1"
										value='0'
										<?php echo $this->item->hotel_state == false ? " checked " : "" ?>
										accesskey="N"
									/>
									<label
										class="labelNo"
										id="label_hotel_state1"
										for="hotel_state1"><?php echo JText::_( 'LNG_EDIT', true ); ?></label>
								</div>
							</div>
						<?php }
					}?>
					<div class="section group">
						<div
							class="key labelFields col column_2_of_12"><?php echo JText::_( 'LNG_SOCIAL_SHARING', true ); ?></div>
						<div id="social_sharing" class="radio btn-group btn-group-yesno col column_9_of_12">
							<input
								style='float:none'
								type="radio"
								name="social_sharing"
								id="social_sharing0"
								value='1'
								<?php echo $this->item->social_sharing == true ? " checked " : "" ?>
								accesskey="Y"

							/>
							<label
								class="labelPerPerson"
								id="label_social_sharing0"
								for="social_sharing0"><?php echo JText::_( 'LNG_YES', true ); ?></label>
							&nbsp;
							<input
								style='float:none'
								type="radio"
								name="social_sharing"
								id="social_sharing1"
								value='0'
								<?php echo $this->item->social_sharing == false ? " checked " : "" ?>
								accesskey="N"
							/>
							<label
								class="labelNo"
								id="label_social_sharing1"
								for="social_sharing1"><?php echo JText::_( 'LNG_NO', true ); ?></label>
						</div>
					</div>
					<div class="section group">
						<div class="key labelFields col column_2_of_12">
							&nbsp;
						</div>
						<div class="col column_8_of_12">
							<?php echo JText::_('LNG_SELECT_A_CURRENCY_FOR_THE_PRICES_DISPLAYED_IN_THE_RESERVATION_PROCESS')?>
						</div>
					</div>
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_CURRENCY')?>
							:</div>
						<div class="col column_4_of_12" >
							<select id='currency_id' name='currency_id'  class="validate[required]">
									<option value='' selected><?php echo JText::_('LNG_SELECT_DEFAULT')?></option>
								<?php
								for($i = 0; $i <  count( $this->item->currencies ); $i++)
								{
									$currency = $this->item->currencies[$i];
								?>
								<option value = '<?php echo $currency->currency_id?>' <?php echo $currency->currency_id==$this->item->currency_id? "selected" : ""?>> <?php echo $currency->description?></option>
								<?php
								}
								?>
							</select>

						</div>
					</div>
					<div class="section group">
						<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_STARS',true); ?>:</div>
						<div class="col column_10_of_12"  ><select name="hotel_stars" id="hotel_stars" class="chosenAttribute">
						<?php
							for($i=0;$i<=7;$i++)
							{
						?>
							<option
								<?php echo $this->item->hotel_stars==$i? "selected" : ""?>
									value='<?php echo $i;?>'>
								<?php echo $i ?>
							</option>
						<?php
						}
						?>
					</select>
						</div>
					</div>
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_AVAILABILITY'); ?>:</div>
						<div class="col column_8_of_12 reservation-details" >
                            <label for="start_date" class="tdLabel"><?php echo JText::_('LNG_START_DATE',true)?>:</label>
                            <div class="calendarDisplay">
                            <input class="form-control"
                                   id="start_date"
                                   data-provide="datepicker"
                                   name="start_date"
                                   value ="<?php echo empty($this->item->start_date) ||  $this->item->start_date=="0000-00-00"? '' : $this->item->start_date;?>"
                                   type="text">

                            <button type="button" class="btn" id="start_date_img"><i class="icon-calendar"></i></button>
                            </div>
                            <label class="tdLabel" for="end_date"><?php echo JText::_('LNG_END_DATE',true)?>:</label>
                            <div class="calendarDisplay">
                            <input class="form-control"
                                   id="end_date"
                                   data-provide="datepicker"
                                   name="end_date"
                                   value ="<?php echo empty($this->item->end_date) ||  $this->item->end_date=="0000-00-00"? '' : $this->item->end_date;?>"
                                   type="text">


                            <button type="button" class="btn" id="end_date_img"><i class="icon-calendar"></i></button>
                            </div>

							<label class="tdLabel" style="font-size: 12px"> <?php echo JText::_('LNG_AVAILABILITY_INFO',true); ?> </label>
						</div>
                    </div>
                    
                    <div class="section group">
						<div
							class="key labelFields col column_2_of_12"><?php echo JText::_( 'LNG_DISPLAY_UNAVAILABILITY_MESSAGE' ); ?></div>
						<div id="display_unavailability_message" class="radio btn-group btn-group-yesno col column_9_of_12">
							<input
								style='float:none'
								type="radio"
								name="display_unavailability_message"
								id="display_unavailability_message0"
								value='1'
								<?php echo $this->item->display_unavailability_message == true ? " checked " : "" ?>
								accesskey="Y"

							/>
							<label
								class="labelPerPerson"
								id="label_display_unavailability_message0"
								for="display_unavailability_message0"><?php echo JText::_( 'LNG_YES', true ); ?></label>
							&nbsp;
							<input
								style='float:none'
								type="radio"
								name="display_unavailability_message"
								id="display_unavailability_message1"
								value='0'
								<?php echo $this->item->display_unavailability_message == false ? " checked " : "" ?>
								accesskey="N"
							/>
							<label
								class="labelNo"
								id="label_display_unavailability_message1"
								for="display_unavailability_message1"><?php echo JText::_( 'LNG_NO', true ); ?></label>
						</div>
					</div>
                    
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_UNAVAILABILITY',true); ?>:</div>
						<div class="col column_10_of_12" id="inlineCalendar">
							<span> <?php echo JText::_('LNG_UNAVAILABILITY_INFO',true); ?> </span>
							<div class="dates_hotel_calendar" id="dates_hotel_calendar" data-date="<?php echo $this->item->ignored_dates; ?>"></div>
                                <input
                                    type='hidden'
                                    name='ignored_dates'
                                    id='ignored_dates'
                                    value='<?php echo $this->item->ignored_dates;?>'
                                        />
						</div>
					</div>
					<!--
					<div>
						<div    class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_SHORT_DESCRIPTION',true); ?>:</div>
						<td>
							<textarea id='hotel_short_description' name='hotel_short_description' rows='10' cols="90" style='width:80%'><?php echo $this->item->hotel_short_description;?></textarea>
						</td>
					</div> -->
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_DESCRIPTION',true); ?>
							:</div>
						<div  class="col column_8_of_12 hotel-editor">
							<?php
								$appSettings = JHotelUtil::getApplicationSettings();
								$dirs = JHotelUtil::languageTabs();
								$j=0;
							$options = array(
								'onActive' => 'function(title, description){
						            description.setStyle("display", "block");
						            title.addClass("open").removeClass("closed");
						        }',
								'onBackground' => 'function(title, description){
						            description.setStyle("display", "none");
						            title.addClass("closed").removeClass("open");
						        }',
								'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
								'useCookie' => true, // this must not be a string. Don't use quotes.
								);

							    echo JHtml::_('tabs.start', 'tab_language_id', $options);

								foreach( $dirs  as $_lng ) {

                                    $langName= JHotelUtil::languageNameTabs($_lng);

                                    echo JHtml::_('tabs.panel',  $langName, 'tab'.$j);
                                    $langContent = isset($this->translations[$_lng]) ? $this->translations[$_lng] : "";
                                    if (checkUserAccess(JFactory::getUser()->id, "hotel_extra_info") || JRequest::getVar('addhotel') === 1) {
                                        $editor = JFactory::getEditor();
                                        echo $editor->display('hotel_description_' . $_lng, $langContent, '800', '400', '70', '15', false);
                                    } else {
                                        echo "<textarea id='hotel_description_'.$_lng name='hotel_description_$_lng' rows='10' style='width: 96%;'>$langContent</textarea>";
                                    }
                                }
								echo JHtml::_('tabs.end');
							?>
						</div>
					</div>
					<div class="section group">
						<div    class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_SELLING_POINTS',true); ?>	:</div>
						<div  class="col column_8_of_12 hotel-editor" >
							<?php

								$sellingPoints = isset($this->item->hotel_selling_points)?$this->item->hotel_selling_points:'';
								if (checkUserAccess(JFactory::getUser()->id,"hotel_extra_info") || JRequest::getVar('addhotel') === 1){
									$editor =JFactory::getEditor();
									echo $editor->display('hotel_selling_points',$sellingPoints , '800', '400', '70', '15', false);
								} else {
									echo "<textarea id='hotel_selling_points' name='hotel_selling_points' rows='10' style='width: 96%;'>".$sellingPoints."</textarea>";
								}
							?>
						</div>
					</div>
					<div class="section group">
						<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_AUTOCOMPLETE_ADDRESS')?></div>
						<div class="col column_8_of_12">
							<input size="40" type="text" id="autocomplete" class="input_txt" value="" placeholder="Enter your address" onFocus="" />
						</div>
					</div>
                    <div class="section group">
                        <div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_COUNTRY',true); ?>
                            :
                        </div>
                        <div  class="col column_10_of_12" >
                            <select id="country" name="country_id" class="validate[required]">
                                <?php if(!isset($this->item->country_id)){ ?>
                                    <option <?php echo $this->item->country_id=='0'? "selected" : ""?> value='0'></option>
	                            <?php } ?>
                                <?php
                                foreach( $this->item->countries as $country )
                                {

                                    $options[] = JHTML::_('select.option',$country->country_id, $country->country_name);

                                }
                                $options1 = array_slice($options, 4);
                                echo JHtml::_('select.options', $options1, 'value', 'text',$this->item->country_id);
                                ?>
                            </select>
                        </div>
                    </div>
					<div class="section group">
						<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_COUNTY',true); ?>:</div>
						<div  class="col column_8_of_12" ><input type="text" name="hotel_county" class="validate[required] text-input"
							id="administrative_area_level_1" value='<?php echo $this->item->hotel_county?>'
							size=40 maxlength=255  />
						</div>

					</div>
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_CITY',true); ?>:</div>
						<div class="col column_8_of_12" ><input type="text" name="hotel_city" class="validate[required] text-input"
							id="locality" value='<?php echo $this->item->hotel_city?>'
							size=40 maxlength=255  />
						</div>
					</div>
					<div class="section group">
						<div class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_ADDRESS',true); ?>:</div>
						<div class="col column_8_of_12" ><input type="text" name="hotel_address"
							id="route" class="validate[required] text-input"
							value='<?php echo $this->item->hotel_address?>' size=40
							maxlength=255  />
						</div>
					</div>
					<div class="section group">
						<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_POSTAL_CODE',true); ?>:</div>
						<div  class="col column_8_of_12" >
							<input type="text" name="hotel_zipcode" id="postal_code" class="text-input"
								value='<?php echo $this->item->hotel_zipcode?>' size=40 maxlength=255  />
						</div>
					</div>
					<div class="section group">
						<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_WEBSITE',true); ?>:</div>
						<div class="col column_8_of_12" ><input type="text" name="hotel_website"
							id="hotel_website"
							value='<?php echo $this->item->hotel_website?>' size=40
							maxlength=255  />
						</div>
					</div>
					<div class="section group" >
						<div  class="key labelFields col column_2_of_12"><?php echo JText::_('LNG_LOCATION',true); ?>:</div>
						<div class="col column_8_of_12"  >
							<?php echo JText::_('LNG_LATITUDE',true); ?> <input type="text"
								name="hotel_latitude" id="latitude"
								value='<?php echo $this->item->hotel_latitude?>' size=30
								maxlength=255  />
								</br>
							<?php echo JText::_('LNG_LONGITUDE',true); ?>
							<input
								type		= "text"
								name		= "hotel_longitude"
								id			= "longitude"
								value		= '<?php echo $this->item->hotel_longitude?>'
								size		= 30
								maxlength	= 255

							/>
						</div>
					</div>
					<div class="section group">
						<div class="col column_12_of_12">
							<div class="section group">
								<div>
									<div id="map-container">
										<div id="company_map">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
    </fieldset>
</div>


<script>
var placeSearch, autocomplete;
var autocomplete_address = document.getElementById('autocomplete');
var component_form = {
    'street_number': 'short_name',
    'route': 'long_name',
	'locality': 'long_name',
	'administrative_area_level_1': 'long_name',
	'country': 'long_name',
	'postal_code': 'short_name'
};


var dateFormat = '<?php echo  $this->appSettings->dateFormat; ?>';
var language = '<?php echo JHotelUtil::getJoomlaLanguage();?>';
var formatToDisplay = calendarFormat(dateFormat);

jQuery(document).ready(function(){
    jQuery.fn.datepicker.defaults.language = language;
    jQuery.fn.datepicker.defaults.format = formatToDisplay;
});

jQuery("#start_date #end_date").datepicker({
    autoclose: true,
    format: formatToDisplay,
    orientation: "bottom left",
    toggleActive: true,
    language: language
});

jQuery("#start_date_img").click(function(){
    jQuery('#start_date').focus();
});

jQuery("#end_date_img").click(function(){
    jQuery('#end_date').focus();
});



jQuery("#inlineCalendar div#dates_hotel_calendar").datepicker({
    multidate: true,
    toggleActive: true,
    language: language,
    format: formatToDisplay
});

jQuery("#inlineCalendar div#dates_hotel_calendar").on("changeDate", function(event) {
    jQuery("#ignored_dates").val(
        jQuery("#inlineCalendar div#dates_hotel_calendar").datepicker('getFormattedDate')
    );
});

function initializeAutocomplete() {
	if (typeof(autocomplete_address) != 'undefined' && autocomplete_address != null) {
		autocomplete = new google.maps.places.Autocomplete(document.getElementById('autocomplete'), { types: [ 'geocode' ] });
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			fillInAddress();
		});
	}
}

function fillInAddress() {
  var place = autocomplete.getPlace();

  for (var component in component_form) {
      var field =document.getElementById(component);
          if(field) {
              field.value = "";
              field.disabled = false;
          }
  }
    for (var j = 0; j < place.address_components.length; j++) {
        var att = place.address_components[j].types[0];

        if (component_form[att]) {
            var val = place.address_components[j][component_form[att]];

            var attribute = document.getElementById(att);
            var street_numberValue;
            switch (att) {
                case 'street_number':
                    if(val) {
                        street_numberValue = ", " + val;
                    }
                    break;
                case 'route':
                    street_numberValue = street_numberValue?street_numberValue:'';
                    attribute.value = val + street_numberValue;
                    break;
                case 'country':
                    var country = attribute;
                    if (typeof (country) != 'undefined' && country != null) {
                        for (var c = 0; c < country.length; c++) {
                            if (country.options[c].textContent === val) {
                                country.options[c].selected = true;
                                country.options[c].setAttribute('selected', "selected");
                            }
                        }
                    }
                    break;
                default:
                    attribute.value = val;
                    break;
            }
        }
    }

if (place.geometry.viewport) {
  hotelMap.fitBounds(place.geometry.viewport);
}
if( place.geometry.location) {
  hotelMap.setCenter(place.geometry.location);
  hotelMap.setZoom(17);
    addMarker(place.geometry.location);
  }

}

function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
      autocomplete.setBounds(new google.maps.LatLngBounds(geolocation, geolocation));
    });
  }
}


var hotelMap;
var markers = [];


function initialize() {
	<?php
		$latitude = isset($this->item->hotel_latitude) && strlen($this->item->hotel_latitude)>0?$this->item->hotel_latitude:"0";
		$longitude = isset($this->item->hotel_longitude) && strlen($this->item->hotel_longitude)>0?$this->item->hotel_longitude:"0";
	 ?>
	var companyLocation = new google.maps.LatLng(<?php echo $latitude ?>, <?php echo $longitude ?>);

	var mapOptions = {
	  zoom: <?php echo !(isset($this->item->hotel_latitude) && strlen($this->item->hotel_latitude))?1:15?>,
	  center: companyLocation,
	  scrollwheel: false,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	var mapdiv = document.getElementById("company_map");
	mapdiv.style.width = '99%';
	mapdiv.style.height = '400px';
	hotelMap = new google.maps.Map(mapdiv,  mapOptions);

	var latitude = '<?php echo  $this->item->hotel_latitude ?>';
	var longitude = '<?php echo  $this->item->hotel_longitude ?>';

	if(latitude && longitude)
	    addMarker(new google.maps.LatLng(latitude, longitude ));

	google.maps.event.addListener(hotelMap, 'click', function(event) {
		 deleteOverlays();
	   addMarker(event.latLng);
	});

}

//Add a marker to the map and push to the array.
function addMarker(location) {
	document.getElementById("latitude").value = location.lat();
	document.getElementById("longitude").value = location.lng();

	var marker = new google.maps.Marker({
	  position: location,
	  map: hotelMap
	});
	markers.push(marker);
}

//Sets the map on all markers in the array.
function setAllMap(map) {
	for (var i = 0; i < markers.length; i++) {
	  markers[i].setMap(map);
	}
}

//Removes the overlays from the map, but keeps them in the array.
function clearOverlays() {
	setAllMap(null);
}

//Shows any overlays currently in the array.
function showOverlays() {
	setAllMap(map);
}

//Deletes all markers in the array by removing references to them.
function deleteOverlays() {
	clearOverlays();
	markers = [];
}

function loadHotelMapScript() {
	initialize();
}

jQuery(document).ready(function(){

	initializeAutocomplete();
	loadHotelMapScript();


	jQuery(window).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });
});

</script>
