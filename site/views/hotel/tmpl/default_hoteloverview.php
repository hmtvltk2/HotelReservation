<?php
$hotel =  $this->hotel;

//create dates & default values
$startDate = $this->userData->start_date;
$endDate = $this->userData->end_date;
$startDate = JHotelUtil::convertToFormat($startDate);
$endDate = JHotelUtil::convertToFormat($endDate);

$lang = JFactory::getLanguage();
$locales = $lang->getLocale();

?>

<script type="text/javascript">

    var  defaultStartDate = "<?php echo isset($module)?$module->params["start-date"]: ''?>";
    var defaultEndDate = "<?php echo isset($module)?$module->params["end-date"]: ''?>";

    var dateFormat = '<?php echo  $this->appSettings->dateFormat; ?>';
    var language = '<?php echo JHotelUtil::getLanguageTag();?>';
    var formatToDisplay = calendarFormat(dateFormat);

</script>

<?php if ($this->appSettings->enable_hotel_tabs==1) {?>	
	<div class="hotel-image-gallery">
		<div class="image-preview-cnt">
			<img id="image-preview"
				 alt="<?php echo JHotelUtil::setAltAttribute(isset($hotel->pictures[0]->hotel_picture_info)?$hotel->pictures[0]->hotel_picture_info:null); ?>"
				 src='<?php if(isset($hotel->pictures[0])) echo JURI::root().PATH_PICTURES.$hotel->pictures[0]->hotel_picture_path?>'
			/>
		</div>
		<div class="small-images">
		<?php
			foreach( $this->hotel->pictures as $index=>$picture ){
				if($index>=32) break;
		?>
			<div class="image-prv-cnt">
				<img class="image-prv"
					 alt="<?php echo JHotelUtil::setAltAttribute($picture->hotel_picture_info); ?>"
					src='<?php echo JURI::root() .PATH_PICTURES.$picture->hotel_picture_path?>' />
			</div>	
			
		<?php } ?>
		</div>
		
		<div class="clear"> </div>
		<div class="right">
			<a href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_PHOTO")) ?>" ><?php echo JText::_('LNG_VIEW_ALL_PHOTOS')?></a>
		</div>
	</div>
<?php }?>
<div class="clear"> </div>
<div class="reservation-details-holder row-fluid">
	<h3><?php echo $hotel->roomSpecialsTranslation?>:</h3>
	<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotel'.JHotelUtil::getItemIdS()) ?>" method="post" name="searchForm" id="searchForm">
		<input type='hidden' name='resetSearch' id='resetSearch' value='true'>
		<input type='hidden' name='option' value='com_jhotelreservation'>
		<input type='hidden' name='task' id="task" value='hotel.changeSearch'>
		<input type="hidden" name="hotel_id" id="hotel_id" value="<?php echo $this->hotel->hotel_id ?>" />
		<input type="hidden" name="user_property" id="user_property" value="<?php echo $this->hotel->hotel_id ?>" />
		<input type='hidden' name='year_start' value=''>
		<input type='hidden' name='month_start' value=''>
		<input type='hidden' name='day_start' value=''>
		<input type='hidden' name='year_end' value=''>
		<input type='hidden' name='month_end' value=''>
		<input type='hidden' name='day_end' value=''>
		<input type='hidden' name='rooms' value=''>
		<input type='hidden' name='guest_adult' value=''>
		<input type='hidden' name='guest_child' value=''>
		<input type='hidden' name='user_currency' value=''>
		
		<?php 
			if(isset($this->userData->roomGuests )){
				foreach($this->userData->roomGuests as $guestPerRoom){?>
					<input class="room-search" type="hidden" name='room-guests[]' value='<?php echo $guestPerRoom?>'/>
				<?php }
			}
			if(isset($this->userData->roomGuestsChildren )){
				foreach($this->userData->roomGuestsChildren as $guestPerRoomC){?>
						<input class="room-search" type="hidden" name='room-guests-children[]' value='<?php echo $guestPerRoomC?>'/>
					<?php }
				}
			if(isset($this->userData->excursions ) && is_array($this->userData->excursions) && count($this->userData->excursions)>0){
				foreach($this->userData->excursions as $excursion){?>
					<input class="excursions" type="hidden" name='excursions[]' value='<?php echo $excursion;?>' />
				<?php }
				}
			if(isset($this->userData->roomChildrenAges ) && is_array($this->userData->roomChildrenAges) && count($this->userData->roomChildrenAges)>0){
				foreach($this->userData->roomChildrenAges as $childAges){ 
					foreach($childAges as $childAge){
					?>
					<input class="jhotelreservation_child_age" type="hidden" name='roomChildrenAges[]' value='<?php echo $childAge;?>' />
				<?php
					 }
				}
			}				

		?>
		<div class="reservation-details span12" >
			<div class="reservation-detail">
				<div class="input-append">
					<label for="jhotelreservation_datas2"><?php echo JText::_('LNG_ARIVAL')?></label>
                            <input class="form-control" data-provide="datepicker"
                               id="jhotelreservation_datas2"
                               name="jhotelreservation_datas"
                               type="text"
                               value="<?php echo $startDate; ?>"
                               readonly
                               style = "cursor:pointer" 
                               onchange="if(!checkStartDate(this.value, defaultStartDate,defaultEndDate))return false;setDepartureDate('jhotelreservation_datae2',this.value,dateFormat);">
                            <button type="button" class="btn" id="jhotelreservation_datas2_img"><i class="icon-calendar"></i></button>
				</div>
			</div>	
			<div class="reservation-detail ">
				<div class="calendarHolder">	
					<div class="input-append">
						<label for="jhotelreservation_datae2"><?php echo JText::_('LNG_DEPARTURE')?></label>
                            <input class="form-control"
                                   data-provide="datepicker"
                                   type="text"
                                   name="jhotelreservation_datae"
                                   value="<?php echo $endDate; ?>"
                                   readonly
                               	   style = "cursor:pointer" 
                                   onchange="checkEndDate(this.value,defaultStartDate,defaultEndDate)"
                                   id="jhotelreservation_datae2">
						   <button type="button" class="btn" id="jhotelreservation_datae2_img"><i class="icon-calendar"></i></button>
					</div>
				</div>
			</div>
			<div class="reservation-detail">
				<label for=""><a id="" href="javascript:void(0);" onclick="showExpandedSearch()"><?php echo JText::_('LNG_ROOMS')?></a></label>
				<div class="styled-select">
					<select id='jhotelreservation_rooms2' name='jhotelreservation_rooms' class = 'select_hotelreservation'>
						<?php
						$jhotelreservation_rooms = $this->userData->rooms;
						
						$i_min = 1;
						$i_max = 5;
						
						for($i=$i_min; $i<=$i_max; $i++)
						{
						?>
						<option 
							value='<?php echo $i?>'
							<?php echo $jhotelreservation_rooms==$i ? " selected " : ""?>
						>
							<?php echo $i?>
						</option>
						<?php
						}
						?>
					</select>
				</div>
			</div>
			<div class="reservation-detail">
				<label for=""><?php echo JText::_('LNG_ADULTS_19')?></label>
				<div class="styled-select">
					<select name='jhotelreservation_guest_adult' id='jhotelreservation_guest_adult'	class = 'select_hotelreservation'>
						<?php
						$i_min = 1;
						$i_max = 12;
						
						$jhotelreservation_adults = $this->userData->total_adults;
						
						for($i=$i_min; $i<=$i_max; $i++)
						{
						?>
						<option value='<?php echo $i?>'  <?php echo $jhotelreservation_adults==$i ? " selected " : ""?>><?php echo $i?></option>
						<?php
						}
						?>
					</select>
				</div>
			</div>
			
			<div class="reservation-detail " style="<?php echo $this->appSettings->show_children!=0 ? "":"display:none" ?>">
				<label for=""><?php echo JText::_('LNG_CHILDREN_0_18')?></label>
				<div class="styled-select">
					<select name='jhotelreservation_guest_child' id='jhotelreservation_guest_child'
						class		= 'select_hotelreservation' onchange='showChildrenAgesHotel(this.value)'
					>
						<?php
						$i_min = 0;
						$i_max = 10;
						$jhotelreservation_children = $this->userData->total_children;
							
						for($i=$i_min; $i<=$i_max; $i++)
						{
						?>
						<option <?php echo $jhotelreservation_children==$i ? " selected " : ""?> value='<?php echo $i?>'  ><?php echo $i?></option>
						<?php
						}
						?>
					</select>
				</div>
			</div>
			
			<?php if($this->appSettings->enable_children_categories!=0){?>
			<div class="reservation-detail">
				<label id="labelText" style="display:<?php echo !empty($this->userData->jhotelreservation_child_ages)?'block':'none';?>;"><?php echo JText::_('LNG_CHILDREN_AGE_SHORT',true)?></label>
				<div id="childrenAgesHotel">
					<?php
					if(!empty($this->userData->jhotelreservation_child_ages)){				
						$childrenAgesArray = $this->userData->jhotelreservation_child_ages;
						
						foreach($childrenAgesArray as $childAge){
					?>
						 <div class="styled-select span3 spacer2 fixedWidth">
							 <select id="jhotelreservation_child_ages[]"  name="jhotelreservation_child_ages[]">
							 <?php for ($j = 0; $j <= HOTEL_MAX_CHILDREN_AGE; $j++){?>
						        <option value="<?php echo $j?>"  <?php echo intVal($childAge)==$j ? "selected" : ""?>><?php echo $j?></option>
						     <?php }?>   
							</select>
						 </div>
					<?php } 
					
					}
					?>	
				</div>
			</div>
			<?php } ?>	

			<?php if ($this->appSettings->is_enable_offers){?>
			<div class="reservation-detail voucher">
				<label for=""><?php echo JText::_('LNG_VOUCHER')?></label>
				<input type="text" value="<?php echo $this->userData->voucher ?>" name="voucher" id="voucher"/>
			</div>
			<?php }?>
			<div class="reservation-detail">
				<label for="">&nbsp;</label>
				<button class="ui-hotel-button small"	onClick	="checkRoomRates('searchForm');"
					type="button" name="checkRates" value="checkRates"><i class="fa fa-search" alt=""></i> <?php echo JText::_('LNG_SEARCH')?></button>
			</div>
			<div class="clear"></div>
		</div>
	</form>
</div>

	<?php if(  $this->userData->rooms > 1){ ?>
	
		<div class="reservation-info-container span12">
			<div class="reservation-info-container-outer">
				<div class="reservation-info-container-inner">
					<div class="choose-room">
						<?php 
							echo $hotel->roomSpecialsTranslation ."&nbsp;(";
							echo count($this->userData->reservedItems) +1 ;
							echo "&nbsp;".JText::_('LNG_OF',true) ." ";
							echo $this->userData->rooms.") ";
							echo JText::_('LNG_ADULTS').":".$this->userData->roomGuests[count($this->userData->reservedItems)];
							if($this->appSettings->show_children){
								echo " ".JText::_('LNG_CHILDREN').":".$this->userData->roomGuestsChildren[count($this->userData->reservedItems)];
							}
				?>
			</div>
		
				</div>
			</div>
		</div>
	<?php }
		if($hotel->display_unavailability_message==1){
			$nextAvailableDate = BookingService::getNextAvailableDate($this->hotel->hotel_id,$startDate);
			if(date('m',strtotime($nextAvailableDate))>date('m')){
	?>
			<div class="alert_message div_alert_roomrates">	
				<?php echo JText::_('LNG_UNAVAILABILITY_MESSAGE');
					  echo sprintf(JText::_('LNG_NEXT_AVAILABILITY_MESSAGE'),$nextAvailableDate);
				?>
			</div>
	<?php
			}
		}
	if( JRequest::getVar( 'infoCheckAvalability') != '' )	
	{
	?>
	<div class="alert_message div_alert_roomrates">
		<?php echo JRequest::getVar('infoCheckAvalability') ?>
	</div>	
	<?php
	}
	else{
		JText::script('LNG_LESS_DETAILS');
		JText::script('LNG_MORE_DETAILS');
	?>
		<form autocomplete='off' action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userForm" id="userForm" >
			<div id="boxes" class="hotel_reservation">
				<div id='div_room'>
					<?php
						try {   
                            if(count($this->offers)>0 && $this->appSettings->is_enable_offers && $this->loadTemplate("offers_".$this->appSettings->room_view)) {
                                 echo $this->loadTemplate("offers_".$this->appSettings->room_view);
                            }
                        } catch(exception $ex) {
                           	//do nothing, generated for combined views
                        }
                            if(count($this->rooms)) {
                                echo $this->loadTemplate("rooms_".$this->appSettings->room_view);
                            }
                        
					?>		
					</div>
			</div> 
			
			<input type="hidden" name="task" 	 						id="task"	 				value="hotel.reserveRoom"  />
			<input type="hidden" name="hotel_id"						id="hotel_id"	 			value="<?php echo $this->state->get("hotel.id") ?>"	/>
			<input type="hidden" name="tmp"								id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
			<input type="hidden" name="tip_oper" 						id="tip_oper" 				value="<?php echo JRequest::getVar( 'tip_oper') ?>" />
			<input type="hidden" name="reserved_item"					id="reserved_item" 			value="" />
			<input type="hidden" name="current"   						id="current"				value="<?php echo count($this->userData->reservedItems) +1 ?>" />
			<input type="hidden" name="reservedItems" 					id="reservedItems"          value="<?php echo JRequest::getVar("reservedItems") ?>" />
			<input type="hidden" name="extraOptions"  					id="extraOptionss"          value="<?php echo JRequest::getVar("extraOptions") ?>" />
			
		</form>
	<?php 	
	}			
	?>


<?php if($this->appSettings->enable_hotel_description==1){?>
<div class="hotel-description hotel-item">
	<h2><?php echo $hotel->overviewTranslation?></h2>
	<?php  
		$hotelDescription = $this->hotel->hotel_description;
		echo html_entity_decode($hotelDescription)
    ?>
</div>
<?php }?>
<?php if($this->appSettings->enable_hotel_facilities==1){?>
<div class="hotel-facilities hotel-item">
	
	<h2><?php echo $hotel->faciltiesTranslation?></h2>
	<ul class="blue">
		<?php
        foreach($this->hotel->facilities as $facility) {
            if ($facility->name == JText::_('LNG_'.strtoupper(str_replace(" ","_",$facility->name)))) {
                ?>
                <li><?php echo JText::_('LNG_'.strtoupper(str_replace(" ","_",$facility->name))); ?></li>
            <?php
            } else {
                ?>
                <li><?php echo JText::_('LNG_'.strtoupper(str_replace(" ","_",$facility->name))); ?></li>
            <?php
            }
        } ?>
	</ul>
</div>
<?php }?>

<?php
 if(count($hotel->reviews) >= MINIMUM_HOTEL_REVIEWS & $this->appSettings->enable_hotel_rating==1){ 
	 echo $this->loadTemplate('hotelreviews'); 
 }
 
 ?>
 
 
<?php if($this->appSettings->enable_hotel_information==1) {
		echo $this->loadTemplate('informations'); 
}	
?>

<script>
    jQuery(document).ready(function(){
    	jQuery("img.image-prv").hover(function(e){
			jQuery("#image-preview").attr('src', this.src);	
		});
        jQuery.fn.datepicker.defaults.language = language;
        jQuery.fn.datepicker.defaults.format = formatToDisplay;
    });



	var message = "<?php echo JText::_('LNG_ERROR_PERIOD',true)?>";
	var localeLang = "<?php echo $locales[4] ?>";

    jQuery("#jhotelreservation_datae2 #jhotelreservation_datas2").datepicker({
        language: language,
        format: formatToDisplay
    });

    jQuery("#jhotelreservation_datas2_img").click(function(){
        jQuery("#jhotelreservation_datas2").focus();
    });

    jQuery("#jhotelreservation_datae2_img").click(function(){
        jQuery("#jhotelreservation_datae2").focus();
    });

	function showChildrenAgesHotel(limit){
		jQuery("#childrenAgesHotel").empty();
		if(limit==0)
			jQuery("#labelText").hide();
		for (i = 0; i < limit; i++) {
			 var container = jQuery("<div></div>").attr("class","styled-select span3 spacer2 fixedWidth");
			 var combo = jQuery("<select></select>").attr("id", "jhotelreservation_child_ages[]").attr("name", "jhotelreservation_child_ages[]");
			 for (j = 0; j <= 16; j++)
		        combo.append("<option value='"+j+"'>" + j + "</option>");

			 container.append(combo);
			 jQuery("#childrenAgesHotel").append(container);
			 jQuery("#labelText").show();
		}
	}	
</script>

<?php
	require_once JPATH_SITE.'/components/com_jhotelreservation/include/multipleroomselection.php';
?> 
			 