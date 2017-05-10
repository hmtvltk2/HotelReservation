<?php 
$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
$jhotelreservation_datas = isset($jhotelreservation_datas)?$jhotelreservation_datas:$startDate;
$jhotelreservation_datae = isset($jhotelreservation_datae)?$jhotelreservation_datae:$endDate;
$userData =  empty($userData)?UserDataService::getUserData():$userData;
?>
<form method="post" name="justTobe" id="justTobe"></form>

	<div id="advanced-search" class="row-fluid" style="display: none">
		<span  title="Cancel" style="display:;" class="dialogCloseButton" onClick="revertValue();jQuery.unblockUI();">
			<span title="Cancel" class="closeText">x</span>
		</span>
		<div class="mod_hotel_reservation_popup" id="mod_hotel_reservation">
			<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation') ?>" method="post" name="userModuleAdvancedForm" id="userModuleAdvancedForm" >
				<input id="controller3" type='hidden' name='controller' value='hotels'/>
				<input id="task3" type='hidden' name='task' value='hotels.searchHotels'/>
			    <input name="jhotelreservation_datas" id="jhotelreservation_datas3" type="hidden" value="<?php echo $jhotelreservation_datas; ?>">
			    <input name="jhotelreservation_datae" id="jhotelreservation_datae3" type="hidden" value="<?php echo $jhotelreservation_datae; ?>">
			    <input id='jhotelreservation_rooms3' name='jhotelreservation_rooms' type="hidden">
			    <input id='jhotelreservation_guest_adult3' name='jhotelreservation_guest_adult' type="hidden">
			    <input id='jhotelreservation_guest_child3' name='jhotelreservation_guest_child' type="hidden">
				<input type="hidden" name="hotel_id" id="hotel_id3" value="" />
				<input type='hidden' name='year_start' value=''/>
				<input type='hidden' name='month_start' value=''/>
				<input type='hidden' name='day_start' value=''/>
				<input type='hidden' name='year_end' value=''/>
				<input type='hidden' name='month_end' value=''/>
				<input type='hidden' name='day_end' value=''/>
				<input type='hidden' name='rooms' value='' />
				<input type='hidden' name='guest_adult' value=''/>
				<input type='hidden' name='guest_child' value=''/>
				<input type='hidden' name='filterParams' id="filterParams" value='<?php echo isset($userData->filterParams) ? $userData->filterParams :''?>' />
				<input type='hidden' name='resetSearch' value='true'>
				<input type='hidden' name='keyword' id="keywordAdvanced" value=''>
				<input type='hidden' name='voucher' id='voucher' value='<?php echo isset($userData->voucher) ? $userData->voucher :''?>' />
				
			
			<div class="span11"> 
				<div id="roomsContainer">
				<?php 
					$index = 0;
					if(isset($userData->roomGuests)){				
						foreach($userData->roomGuests as $nrGuests){
							$index++;
							?>
							<fieldset id="roomHolder<?php echo $index;?>">
								<legend><?php echo JText::_('LNG_ROOM',true)." ".$index ?></legend>
							<div class="reservation-detail">
								<label><?php echo JText::_('LNG_GUEST',true)?></label>
							 	<div class="styled-select span3 fixedWidth">
									<select name="room-guests[]">
										<?php
											$i_min = 1;
											$i_max = 5;//$params->get("max-room-guests");
											
											for($i=$i_min; $i<=$i_max; $i++)
											{
											?>
											<option value='<?php echo $i?>'  <?php echo $nrGuests==$i ? " selected " : ""?>><?php echo $i?></option>
											<?php
											}
											?>
									</select>
								 </div>	
							</div>										
										
							<?php if($appSettings->show_children!=0){ ?>
								<div class="reservation-detail">
									<label><?php echo JText::_('LNG_CHILDREN');?></label>
								 	<div class="styled-select span3 fixedWidth">
										<select name="room-guests-children[]" onchange="showChildrenAges('<?php echo $index;?>',this.value)">
												<?php
													$i_min = 0;
													$i_max = 5;
													
													for($i=$i_min; $i<=$i_max; $i++)
													{
													?>
													<option value='<?php echo $i?>'  <?php echo (isset($userData->roomGuestsChildren[$index-1]) &&  $userData->roomGuestsChildren[$index-1]==$i) ? " selected " : ""?>><?php echo $i?></option>
													<?php
													}
													?>
											</select>
									</div>
								</div>
							<?php }?>
							<?php if($appSettings->enable_children_categories!=0){?>		
								<div class="reservation-detail">
									<div class="row-fluid">
										<label>
										<div id="introText_<?php echo $index;?>" style="display:<?php echo (isset($userData->roomChildrenAges[$index])|| !empty($userData->jhotelreservation_child_ages))?'block':'none'?>" class="">
											<?php echo JText::_('LNG_CHILDREN_AGE_SELECT',true)?>
										</div>
										</label>
										<div id="childrenAges_<?php echo $index;?>" class="row-fluid" >
										<?php if(isset($userData->roomChildrenAges[$index]) || !empty($userData->jhotelreservation_child_ages)){
											if(!isset($userData->roomChildrenAges[$index]))
												$childrenAgesArray = $userData->jhotelreservation_child_ages;
											else
												$childrenAgesArray = $userData->roomChildrenAges[$index];
											foreach($childrenAgesArray as $childAge){
										?>
											 <div class="styled-select span3 fixedWidth">
												 <select id="room_children_ages_<?php echo $index;?>[]"  name="room_children_ages_<?php echo $index;?>[]">
												 <?php for ($j = 0; $j <= 16; $j++){?>
											        <option value="<?php echo $j?>"  <?php echo intVal($childAge)==$j ? "selected" : ""?>><?php echo $j?></option>
											     <?php }?>   
												</select>
											 </div>
										<?php } 
										
										}
										?>	
										
										</div>
									</div>
								</div>
							<?php }?>
							<div class="reservation-detail">
								<?php if($index!=1){?>
									<label id="deleteRoomLabel"><a id='close-"+count+"' class='red' onclick='deleteRoom("<?php echo $index?>")'><?php echo JText::_('LNG_DELETE_ROOM',true)?></a></label>
								<?php }?>
							</div>	
						</fieldset>
					<?php 
						}
					}
					?>
					</div>
					
					<div class="reservation-detail clearfix">
						<a id="add-new-room" href="javascript:void(0)" onclick="generateRoomContent(1)"><?php echo JText::_('LNG_ADD_ROOM',true);?></a>
					</div>
					
					<div class="reservation-detail right">
						<button class="ui-hotel-button" id ="search-btn" onClick="checkRoomRates('userModuleAdvancedForm');"
						type="button" name="checkRates" value="checkRates"><?php echo JText::_('LNG_SEARCH',true)?></button>
						<a id ="cancel-btn" onClick="jQuery.unblockUI();"><?php echo JText::_('LNG_CANCEL',true)?></a>
					</div>
			</div>
			<div class="clearfix"></div> 
			<div class="multiple-rooms-contact"> 
				<?php echo JText::_("LNG_MULTIPLE_ROOM_CONTACT_INFO")?>
			</div>
		</form>
	</div>
</div>
<script>
  			var dateFormat = "<?php echo $appSettings->dateFormat; ?>";
			var before_change1;
			var before_change2;
			jQuery(document).ready(function(){
								
				jQuery("#jhotelreservation_rooms").change( function(e){
					jQuery("#search-box").show();
					//jQuery("#booking-details").show();
					jQuery("#search-btn").html("<?php echo JText::_('LNG_SEARCH',true)?>");
					
					showExpandDialog(this.value,1);
					jQuery("#jhotelreservation_rooms3").val(jQuery("#jhotelreservation_rooms").val());
					jQuery("#jhotelreservation_datas3").val(jQuery("#jhotelreservation_datas").val());
					jQuery("#jhotelreservation_datae3").val(jQuery("#jhotelreservation_datae").val());
					jQuery("#keywordAdvanced").val(jQuery("#keyword").val());
					jQuery("#searchTypeAdvanced").val(jQuery("#searchType").val());
					jQuery("#searchIdAdvanced").val(jQuery("#searchId").val());
					
					if(this.value!=1){
						lastSel.attr("selected", true);
						lastSel2.attr("selected", true);
					}

					jQuery("#task3").val("searchHotels");
					jQuery("#hotel_id3").val("");
					jQuery("#controller3").val('hotels');

					jQuery('#jhotelreservation_rooms').change(function(e){
					     before_change1 = jQuery(this).data('pre');//get the pre data
					    //Do your work here
					    jQuery(this).data('pre', $(this).val());//update the pre data
					});

					jQuery('#jhotelreservation_rooms2').change(function(e){
					     before_change2 = jQuery(this).data('pre');//get the pre data
					    //Do your work here
					    jQuery(this).data('pre', $(this).val());//update the pre data
					});

					
				});

				var lastSel =  jQuery("#jhotelreservation_rooms option:selected");
				var lastSel2 = jQuery("#jhotelreservation_rooms2 option:selected");

				jQuery("#jhotelreservation_rooms2").click(function(){
				    lastSel2 = jQuery("#jhotelreservation_rooms2 option:selected");
				});

				jQuery("#jhotelreservation_rooms").click(function(){
				    lastSel = jQuery("#jhotelreservation_rooms option:selected");
				});
						
								
				jQuery("#jhotelreservation_rooms2").change( function(e){
					jQuery("#search-box").hide();
					jQuery("#booking-details").hide();
					jQuery("#search-btn").html("<?php echo JText::_('LNG_UPDATE_SEARCH',true)?>");
					showExpandDialog(this.value,0);
					jQuery("#jhotelreservation_rooms3").val(jQuery("#jhotelreservation_rooms2").val());
					jQuery("#jhotelreservation_datas3").val(jQuery("#jhotelreservation_datas2").val());
					jQuery("#jhotelreservation_datae3").val(jQuery("#jhotelreservation_datae2").val());
					if(this.value!=1){
						lastSel.attr("selected", true);
						lastSel2.attr("selected", true);
					}

					jQuery("#task3").val("hotel.showHotel");
					jQuery("#hotel_id3").val(jQuery("#hotel_id").val());
					jQuery("#controller3").val('hotel');
				});

				if(jQuery("#jhotelreservation_rooms").val()==1){// || jQuery("#rooms-container tbody").children().length < 2)
				   //alert("hide");
				   jQuery("#show-expanded").hide();
				   jQuery("#show-expanded2").hide();
				}

				//select children ages	
				
		    });

		    function showChildrenAges(index,limit){
		    	jQuery("#childrenAges_"+index).empty();
				if(limit==0)
					jQuery("#introText_"+index).hide();

				
				for (i = 0; i < limit; i++) {
					 var container = jQuery("<div></div>").attr("class","styled-select span3 fixedWidth");
					 var combo = jQuery("<select></select>").attr("id", "room_children_ages_"+index+"[]").attr("name", "room_children_ages_"+index+"[]");
					 for (j = 0; j <= 16; j++)
				        combo.append("<option value='"+j+"'>" + j + "</option>");

					 container.append(combo);
					 jQuery("#childrenAges_"+index).append(container);
					 jQuery("#introText_"+index).show(); 
				}
			}

			
			function showExpandDialog(rooms, type){
				if(rooms>1){
					jQuery("#add-new-room").show();
					jQuery('#roomsContainer').children().remove();
					generateRoomContent(rooms);
					jQuery.blockUI({ message: jQuery('#advanced-search'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'},overlayCSS: { backgroundColor: '#000',opacity: 0.7  }});
					jQuery('.blockUI.blockMsg').center();

					jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
					jQuery("#show-expanded").show();
					jQuery("#show-expanded2").show();
				}else{
					jQuery("#show-expanded").hide();
					jQuery("#show-expanded2").hide();
					jQuery(".room-search").remove();
				}
			}

			

            function displayRoom(){
                var display = jQuery('#advanced-search');

                if(!display.is(':visible'))
                {
                    display.css('display','inline-block');
                }else{
                    display.hide('slow');
                }
            }
            function displayRoomBlock(){
                var display = jQuery('#advanced-search');

                if(!display.is(':visible'))
                {
                    display.css('display','block');
                }else{
                    display.hide('slow');
                }
            }

            function showExpandedSearch(){
				jQuery.blockUI({ message: jQuery('#advanced-search'), centerX: true, css: {width: '80%','max-width':'700px',top: '15%',cursor:'pointer'},overlayCSS: { backgroundColor: '#000',opacity: 0.7  }});
				jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
				jQuery('.blockUI.blockMsg').center();
				jQuery("#keywordAdvanced").val(jQuery("#keyword").val());
				jQuery("#searchTypeAdvanced").val(jQuery("#searchType").val());
				jQuery("#searchIdAdvanced").val(jQuery("#searchId").val());
			}
			
			function generateRoomContent(nrRooms){
				var selectContent="";
				var selectContentChildren="";
				<?php
				$i_min = 0;
				$i_max = 5;//$params->get("max-room-guests");
				
				for($i=$i_min; $i<=$i_max; $i++)
				{
				?>
					selectContent+="<option <?php echo $i==2?'selected':''?> value='<?php echo $i?>'><?php echo $i?></option>";
					selectContentChildren+="<option <?php echo $i==0?'selected':''?> value='<?php echo $i?>'><?php echo $i?></option>";
				<?php
				}
				?>
				
				for(i=1;i<=nrRooms;i++){
					var count = jQuery("#roomsContainer").children().length+1;
					if(jQuery("#roomHolder"+count).length)
						count = count +1;
					if(count>4)
						jQuery("#add-new-room").hide();

					var guestTd = "<div class='reservation-detail'><label><?php echo JText::_('LNG_GUEST',true)?></label><div class='styled-select span3 fixedWidth'><select name='room-guests[]'>"+selectContent+"</select></div></div>";
					var childrenTd = "<div class='reservation-detail'><label><?php echo JText::_('LNG_CHILDREN',true)?></label><div class='styled-select span3 fixedWidth'><select name='room-guests-children[]' onchange='showChildrenAges("+count+",this.value)'>"+selectContentChildren+"</select></div></div>";
					var ageDiv = '<div class="reservation-detail"><div class="row-fluid"><label><div id="introText_'+count+'" style="display:none" class=""><?php echo JText::_('LNG_CHILDREN_AGE_SELECT',true)?></div></label><div id="childrenAges_'+count+'" class="row-fluid" ></div></div></div>';
					
					<?php if($appSettings->show_children==0){ ?>
						childrenTd = "";
						ageDiv = "";
					<?php }?>
					var elem = jQuery("<fieldset id='roomHolder"+count+"'><legend><?php echo JText::_('LNG_ROOM',true)?> "+count+"</legend>"+guestTd+childrenTd+ageDiv+"<div class='reservation-detail'><label id='deleteRoomLabel'><a id='close-"+count+"' class='red' onclick='deleteRoom("+count+")'><?php echo JText::_('LNG_DELETE_ROOM',true)?></a></label></div><fieldset>");
					jQuery("#roomsContainer").append(elem);
				}
			}

			function deleteRoom(id){
				jQuery("#roomHolder"+id).remove();
				jQuery("#roomsContainer fieldset:first-child(1) label").each(function(index) {
				    jQuery(this).text("<?php echo JText::_('LNG_ROOM',true)?> "+(index+1));
				});
			}

			function revertValue(){
				jQuery('#jhotelreservation_rooms').val(before_change1);
				jQuery('#jhotelreservation_rooms2').val(before_change2);
			}
		</script>
