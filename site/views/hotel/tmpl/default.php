<?php // no direct access
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

defined('_JEXEC') or die('Restricted access'); 
$hotel = $this->hotel;
$hotelUrl = JURI::current();

if( $this->state->get("hotel.tabId") != 4){
	HotelService::generateHotelMetaData($hotel);
}
$this->currency = JHotelUtil::getCurrencyDisplay($this->userData->currency,$hotel->hotel_currency,$hotel->currency_symbol);
?>
	<div class="hotel_reservation" id="hotel_reservation">
		<div class="hoteInnerContainer">
			<div class="hotel-content row-fluid">
				<div class="span6">
					<div class="hotel-title">
						<h1>
							<?php echo stripslashes($this->hotel->hotel_name) ?> 
						</h1>
						
						<span class="hotel-stars">
							<?php
							for ($i=1;$i<= $this->hotel->hotel_stars;$i++){ ?>
								<img  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
							<?php } ?>
						</span>
					</div>
					<div class="hotel-address">
						<?php echo $this->hotel->hotel_address?>, <?php echo $this->hotel->hotel_zipcode?$this->hotel->hotel_zipcode.", ":""?> <?php echo $this->hotel->hotel_city?>,
						 <?php echo $this->hotel->hotel_county?$this->hotel->hotel_county.", ":""?><?php echo $this->hotel->country_name?><?php //echo $this->hotel->hotel_phone?>
					</div>
					<div class="clear"></div>
				</div>
				<div class="span6" style="padding-right:10px">
					<div class="hotel-details">
						<?php if($hotel->recommended==1){?>
						<div class="hotel-recommanded">
							<span><?php  echo JText::_('LNG_RECOMMENDED');?></span>
						</div>
						<?php } ?>
					</div>	
					<div class="clear"></div>
					<?php if(count($hotel->reviews) >= MINIMUM_HOTEL_REVIEWS & $this->appSettings->enable_hotel_rating==1)
					{ 
						echo ReviewsService::getRatingSummaryHtml($hotel,$hotel->reviews[0]->totalReviewsCount);
					}  
					?>
					<div class="clear"></div>
					<?php if($hotel->social_sharing ){ ?>
					<div class="hotel-actions right">
						<?php echo $this->loadTemplate("social_share"); ?>
					</div>
					<?php } ?>
				</div>
                <div class="clear"></div>
			</div>
            <?php echo $this->hotelBreadCrumb; ?>

            <?php
			
			$map = JRequest::getVar(strtolower(JText::_("LNG_MAP")));
			$fotoGallery = JRequest::getVar(strtolower(JText::_("LNG_PHOTO")));
			$reviews = JRequest::getVar( strtolower(JText::_("LNG_REVIEWS")));
			$facilities = JRequest::getVar(strtolower(JText::_("LNG_FACILITIES")));
            $poi = JRequest::getVar( strtolower(JText::_("LNG_POI")));

            $overview = !(isset($map)|| isset($fotoGallery) || isset($reviews) || isset($facilities) || (isset($poi)));
			
			?>
			<?php if ($this->appSettings->enable_hotel_tabs==1) {?>	
			<div class="rel">
				<div class="tabs tabsHolder">
					
					<ul>
						<li class="<?php echo $overview?'selected':''?> ">
							<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel) ?>"><span><?php echo $hotel->overviewTranslation?></span></a>
												</li>
						<li class="<?php echo isset($map)?'selected':''?> ">
							<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_MAP")) ?>"><span><?php echo JText::_('LNG_MAP')?></span></a>
						</li>
						<li class="<?php echo isset($fotoGallery)?'selected':''?>">
							<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_PHOTO")) ?>"><span><?php echo JText::_('LNG_PHOTO_GALLERY')?></span></a>
						</li>
						
						<?php if(count($hotel->reviews) >= MINIMUM_HOTEL_REVIEWS && $this->appSettings->enable_hotel_rating==1){ ?>
							<li class="<?php echo isset($reviews)?'selected':''?>">
								<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_REVIEWS")) ?>"><span><?php echo JText::_('LNG_REVIEWS')?></span></a>
							</li>
						<?php }?>
						<?php if($this->appSettings->enable_hotel_facilities==1){?>
							<li class="<?php echo isset($facilities)?'selected':''?>">
								<a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_FACILITIES")) ?>"><span><?php echo $hotel->faciltiesTranslation?></span></a>
							</li>
						<?php }?>
                        <li class="<?php echo isset($poi)?'selected':''?>">
                            <a  href="<?php echo JHotelUtil::getHotelLink($this->hotel).'?'.strtolower(JText::_("LNG_POI")) ?>"><span><?php echo JText::_('LNG_POI')?></span></a>
                        </li>
					</ul>
					<span class="right spacingStyle"> <?php echo JText::_("LNG_HOTEL_DETAILS")?>:</span>
				</div>
				<div class="clear"></div>
			</div>
			<?php }?>
			<div class="hotel_details_container">
				<?php 
					
					if(isset($map)){
						echo $this->loadTemplate('hotelmap');
					} else if(isset($fotoGallery)){
						echo $this->loadTemplate('hotelgallery');
					} else if(isset($reviews)){
						echo $this->loadTemplate('hotelreviews');
					}else if(isset($facilities)){
						echo $this->loadTemplate('hotelfacilities');
					}else if(isset($poi)){
                        echo $this->loadTemplate('poi');
                    }else{
						echo $this->loadTemplate('hoteloverview');
					}
				?>
			</div>
		</div> 
	</div>
	
	<script>
	<?php if(JRequest::getVar('rm_id',0)>0){?>
		var roomId = "#room_<?php echo JRequest::getVar('rm_id',0)?> div";
		jQuery(document).ready(function(){
				setTimeout(openSelectedRoom, 500);
			});
	<?php }?>	

		jQuery(document).ready(function(){
			jQuery('body').removeClass("homepage");
			jQuery('body').addClass("subpage");
		});
		
		function openSelectedRoom(){
			jQuery(roomId).removeClass('open');
			jQuery(roomId).addClass('close');
			jQuery(roomId).parent().parent('tr').next().children('.td_cnt').children('.cnt').slideDown(100);
			jQuery(roomId).children('.room_expand').addClass('expanded');
			jQuery(roomId).children('.link_more').html('&nbsp;<?php echo JText::_('LNG_LESS',true)?> »');
			jQuery(roomId).focus();
			jQuery('html, body').animate({ scrollTop: jQuery(roomId).offset().top-40 }, 'slow');
			
			return false;
			}	
	
		function showEmailDialog(){
			jQuery.blockUI({ message: jQuery('#share-hotel-email'), css: {width: '600px'} }); 
			var form = document.emailForm;
			form.elements["email_to_address"].value='';
			form.elements["email_from_name"].value='';
			form.elements["email_from_address"].value='';
			form.elements["email_note"].value='';
			form.elements["copy_yourself"][1].checked=false;
			
		}

		function goBack(){
			var form 	= document.forms['userForm'];
			form.task.value	="hotels.searchHotels";
			form.submit();
		}
	
		function showTab(tabId){
			location = "<?php echo $hotelUrl ?>"+"?tabId="+tabId;
		}

		function sendMail(){
			
			jQuery("#emailError").hide();
			var form = document.emailForm;
			var postParameters='';
			postParameters +="&email_to_address=" + form.elements["email_to_address"].value;
			postParameters +="&email_from_name=" + form.elements["email_from_name"].value;
			postParameters +="&email_from_address=" + form.elements["email_from_address"].value;
			postParameters +="&email_note=" + form.elements["email_note"].value;
			postParameters +="&copy_yourself=" + form.elements["copy_yourself"][1].checked;
			var postData='&controller=email&task=sendEmail'+postParameters;

			jQuery.post(baseUrl, postData, sendMailResult);
		}


		function sendMailResult(responce){
			var xml = responce;
			alert(xml);
			//jQuery('#frmFacilitiesFormSubmitWait').hide();
			jQuery(xml).find('answer').each(function()
			{
				if(jQuery(this).attr('result')==true){
					jQuery("#email-message").html("<p><?php echo JText::_('LNG_EMAIL_SUCCESSFULLY_SENT') ?></p>");
					jQuery.unblockUI();
					jQuery.blockUI({ message: jQuery('#share-hotel-email-message'), css: {width: '600px'} }); 
					setTimeout(jQuery.unblockUI, 2500);
				}else{
					jQuery("#emailError").html(jQuery(this).attr('result'));
					jQuery("#emailError").show();
				}
			});
		}

		
		function bookItem(offerId, roomId){
			jQuery("#reserved_item").val(offerId+"|"+roomId);
			jQuery("#userForm").submit();
		}

		function selectCalendarDate(hoteId,startDate, endDate){
			jQuery('#jhotelreservation_datas2').val(startDate);
			jQuery('#jhotelreservation_datae2').val(endDate);
			if(typeof checkRoomRates === 'function')
				checkRoomRates('searchForm');
		}

		function showRoomCalendars(){
			var postParameters='';
			postParameters +="&hotel_id="+<?php echo $this->state->get("hotel.id") ?>;
			postParameters +="&current_room="+<?php echo count($this->userData->reservedItems) +1 ?>;
			postParameters +="&tip_oper=-1";
			<?php 
					foreach($this->userData->reservedItems as $itemReserved){
						echo 'postParameters +="&items_reserved[]='.$itemReserved.'";';
					}
				?>

			var postData='&task=hotel.getRoomCalendars'+postParameters;

			jQuery.post(baseUrl, postData, processShowRoomCalendarResults);
		}

		function processShowRoomCalendarResults(responce){
			var xml = responce;
			jQuery("<div>" + xml + "</div>").find('answer').each(function()
			{
				var identifier = jQuery(this).attr('identifier');
				jQuery("#calendar-holder-"+identifier).html(jQuery(this).attr('calendar'));
			});
		}

		function checkReservationPendingPayments(){
			var postParameters='';
			var postData='&task=hotel.checkReservationPendingPayments';
			jQuery.post(baseUrl, postData, processShowRoomCalendarResult);
		}
		
		function showRoomCalendar(hotelId, year, month, identifier){

			jQuery("#calendar-holder-"+identifier).html('<div class="room-loader right"></div>');
			console.debug("show ca");
			//alert("show");
			var postParameters='';
			postParameters +="&month="+month;
			postParameters +="&year="+year;
			postParameters +="&identifier="+identifier;
			postParameters +="&hotel_id="+<?php echo $this->state->get("hotel.id") ?>;
			postParameters +="&tip_oper=-1";
			postParameters +="&current_room="+<?php echo count($this->userData->reservedItems) +1 ?>;

			<?php 
				foreach($this->userData->reservedItems as $itemReserved){
					echo 'postParameters +="&items_reserved[]='.$itemReserved.'";';
				}
			?>
			
			jQuery("#loader-"+identifier).show();
			jQuery("#room-calendar-"+identifier).hide();
			var postData='&task=hotel.getRoomCalendar'+postParameters;
			jQuery.post(baseUrl, postData, processShowRoomCalendarResult);
		}

		function processShowRoomCalendarResult(responce){
			var xml = responce;
			jQuery("<div>" + xml + "</div>").find('answer').each(function()
			{
				var identifier = jQuery(this).attr('identifier');
				jQuery("#calendar-holder-"+identifier).html(jQuery(this).attr('calendar'));
			});
		}

		function parseXml(xml) {
		     if (jQuery.browser.msie) {
		        var xmlDoc = new ActiveXObject("Microsoft.XMLDOM"); 
		        xmlDoc.loadXML(xml);
		        xml = xmlDoc;
		    }   
		    return xml;
		}

		</script> 
	
	

