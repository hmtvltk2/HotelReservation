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

$orderBy = isset($this->userData->orderBy)?$this->userData->orderBy: '';
$userData =  $_SESSION['userData'];
$appSettings = $this->appSettings;
?>

<div id="hotel_reservation">
<?php if (isset($this->userData->excursions) && is_array($this->userData->excursions) && count($this->userData->excursions)!=0)
{
?>
<form action="<?php echo JRoute::_('index.php?task=guestDetails.showGuestDetails') ?>" method="post"  name="skipAccomodations" id="skipAccomodations" >
	<div class="right clearfix" style="margin-right:5px;">
		<button value="checkRates" name="checkRates" type="submit" class="ui-hotel-button">
			<?php echo JText::_('LNG_SKIP_ACCOMODATION',true)?>
		</button>
	</div>
	<input type="hidden" name="tip_oper" 			id="tip_oper" 				value="-2" />
	<input type="hidden" name="view" 				id="view" 				value="guestDetails" />
	<input type="hidden" name="task" 				id="task" 				value="guestDetails.showGuestDetails" />
	<input type="hidden" name="option" 				id="option" 				value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="tmp" 				id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type='hidden'	name='jhotelreservation_datas' value='<?php echo $userData->start_date?>'>
	<input type='hidden'	name='jhotelreservation_datae' value='<?php echo $userData->end_date?>'>
	<input type='hidden'	name='rooms' 			value='<?php echo $userData->rooms?>'>
	<input type='hidden'	name='guest_adult' 		value='<?php echo $userData->adults?>'>
	<input type='hidden'	name='guest_child' 		value='<?php echo $userData->children?>'>
	<input type='hidden'	name='year_start' 		value='<?php echo $userData->year_start?>'>
	<input type='hidden'	name='month_start' 		value='<?php echo $userData->month_start ?>'>
	<input type='hidden'	name='day_start'		value='<?php echo $userData->day_start ?>'>
	<input type='hidden'	name='year_end' 		value='<?php echo $userData->year_end?>'>
	<input type='hidden'	name='month_end' 		value='<?php echo $userData->month_end?>'>
	<input type='hidden'	name='day_end' 			value='<?php echo $userData->day_end?>'>
	<input type='hidden'	name='filterParams'		id="filterParams" value='<?php  echo $this->searchFilter ?>'>
	<input type='hidden'	name='reserved_item' 	value='0|0'>
	<input type='hidden'	name='hotel_id'			id="hotel_id" value='0'>
</form>
<?php }?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&task=hotels.searchHotels', false) ?>" method="post"  name="adminForm" id="adminForm" >
	<div id="search-container">
		<h3>
			<?php echo JText::_('LNG_SEARCH_RESULTS');?>
		</h3>
		<div id="search-info">
			<span class="search-title"><?php echo $this->hotelTypes; ?></span>
			<strong class="search-available"></strong>

			<strong><?php  echo JText::_('LNG_YOU_SEARCHED_FOR')?>:</strong>
			<span class=""><?php echo JHotelUtil::getDateGeneralFormat($this->userData->start_date).' '.JText::_('LNG_TO',true).' '.JHotelUtil::getDateGeneralFormat($this->userData->end_date).' - '.JText::_('LNG_NUMBER_OF',true).' '.strtolower(JText::_('LNG_ADULTS',true)).': ', $this->userData->adults. ($this->appSettings->show_children!=0?(' ,'.strtolower(JText::_('LNG_CHILDREN',true)).': '.$this->userData->children):""). ', '.JText::_('LNG_NUMBER_OF',true).' '.JText::_('LNG_ROOMS',true).': '.$this->userData->rooms ?>  </span>
			<?php echo $this->pagination->getResultsCounter()?>
        </div>

        <div id="search-order">
	        <div class="sort-by row-fluid">
		        <div class="sortContainer border-none right">
			         <div class="left"> <label class="sortLabel" for="sort-by"><?php echo JText::_('LNG_SORT_BY');?>: </label></div>
				     <div class="styled-select small-medium">
				        <select onchange="changeOrder(this.value)" id="sort-by" name="orderBy" >
					        <?php $hotelCity = 'hotel_city '.$this->userData->ordering;  ?>
					        <option id="place" value="<?php echo $hotelCity?>" >
						        <?php echo JText::_('LNG_PLACE');?>
					        </option>
					        <?php $hotelName = 'hotel_name '.$this->userData->ordering; ?>
					        <option <?php echo $orderBy==$hotelName?'selected="selected"':''?> id="hotelName" value="<?php echo $hotelName?>" >
						        <?php echo JText::_('LNG_HOTEL_NAME');?>
					        </option>

					        <?php
					                $lowestPrice = 'lowest_hotel_price '.$this->userData->ordering;
					                $staringPrice = 'starting_price_offers '.$this->userData->ordering;
					        ?>
					        <option id="price" <?php echo $orderBy==$lowestPrice || $orderBy==$staringPrice?'selected="selected"':''?> value="<?php echo  isset($this->userData->voucher) & $this->userData->voucher!='' ? $staringPrice: $lowestPrice ?>"  >
						        <?php echo JText::_('LNG_PRICE');?>
					        </option>

					        <?php  $hotelStars = 'hotel_stars '.$this->userData->ordering; ?>
					        <option <?php echo $orderBy==$hotelStars?'selected="selected"':'' ?> value="<?php echo $hotelStars ?>" id="stars">
						        <?php echo JText::_('LNG_STARS');?>
					        </option>

					        <?php   $hotelratingsScore = 'hotel_rating_score '.$this->userData->ordering; ?>
					        <option <?php echo $orderBy==$hotelratingsScore?'selected="selected"':'' ?> value="<?php echo $hotelratingsScore?>" id="rating">
						        <?php echo JText::_('LNG_RATING');?>
					        </option>

					        <?php   $noBookings = 'noBookings '.$this->userData->ordering; ?>
					        <option <?php  echo $orderBy==$noBookings?'selected="selected"':'' ?> value="<?php echo $noBookings?>" id="mostBooked">
						        <?php echo JText::_('LNG_MOST_BOOKED');?>
					        </option>
				        </select>
				      </div>
		      
				      <div class="styled-select small-medium">
					        <select  onchange="changeOrdering('ordering');" class="jhotel-select" name="ordering" id="ordering">
					        <option <?php echo $this->userData->ordering=='asc'?'selected="selected"':'' ?> value="asc" id="asc" >
						        <?php echo JText::_('LNG_ASC');?>&nbsp;
					        </option>
					        <option <?php echo $this->userData->ordering=='desc'?'selected="selected"':'' ?> value="desc" id="desc" >
						        <?php echo JText::_('LNG_DESC');?>&nbsp;
					        </option>
				            </select>
				      </div>
				
			       
					<?php if($this->appSettings->enable_map == true) { ?>
			            <div class="map-link left">
			                <a href="javascript:void(0)" id="enable_map" onclick="displayMap(document.getElementById('enable_map'),document.getElementById('disable_map'));" class="right" > <i class="fa fa-globe" aria-hidden="true"></i>&nbsp;<?php echo JText::_('LNG_SHOW_HOTELS_MAP',true)?></a>
			                <a href="javascript:void(0)" id="disable_map" onClick="hideMap(document.getElementById('enable_map'),document.getElementById('disable_map'))" class="hidden right"> <i class="fa fa-globe" aria-hidden="true"></i>&nbsp;<?php echo JText::_('LNG_HIDE_HOTELS_MAP',true)?></a>
			            </div>
	           		 <?php } ?>
           		 </div>
			</div>
		 </div>
		<div class="clear"></div>
		 
        <?php if($this->appSettings->enable_map == true) { ?>

            <div id="map" style="display: none;" class="inlineMap">
                <?php  require JPATH_COMPONENT_SITE.'/include/hotels_map.php';?>
                <div id="hotelMap-2" style="position: relative;height:600px;"></div>
            </div>
        <?php }?>
	</div>


	<div id="hotel-search-list" class="hotel-search-list">
			<?php
			if(count($this->hotels)>0){
				$showNearby = true;
				foreach( $this->hotels as $hotel ){
					$currency = JHotelUtil::getCurrencyDisplay($userData->currency,$hotel->hotel_currency,$hotel->currency_symbol);
				?>
				<?php if(isset($hotel->nearBy) && $showNearby){
					$showNearby = false
				?>
					<div class="near-by-header"><?php echo JText::_("LNG_NEAR_BY_HOTELS")?></div>
				<?php } ?>
					<div class="hotel-info row-fluid">
						<div class="hotel-info-container">
						<div class="hotel-image-holder span3">
							<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>" alt="<?php echo stripslashes($hotel->hotel_name) ?>" title="<?php echo stripslashes($hotel->hotel_name) ?>">
								<img class="hotel-image"
									 src='<?php echo JURI::root().PATH_PICTURES.$hotel->hotel_picture_path?>'
									 alt="<?php echo JHotelUtil::setAltAttribute($hotel->hotel_picture_path); ?>"
								/>
							</a>
						</div>
						<div class="hotel-content span6">
							<div class="hotel-title">
								<h2>
									<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>" alt="<?php echo stripslashes($hotel->hotel_name) ?>" title="<?php echo stripslashes($hotel->hotel_name) ?>">
										<?php echo stripslashes($hotel->hotel_name) ?>
									</a>
								</h2>
								<span class="hotel-stars">
									<?php
									for ($i=1;$i<=$hotel->hotel_stars;$i++){ ?>
										<img  src='<?php echo JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />
									<?php } ?>
								</span>
							</div>

							<div class="hotel-address">
								<?php echo $hotel->hotel_address?>, <?php echo $hotel->hotel_city?>, <?php echo $hotel->hotel_county?>, <?php echo $hotel->country_name?>
							</div>
							<?php if(isset($hotel->nearBy)){?>
								<div class="location-distance">
									<?php echo JText::_("LNG_DISTANCE").": ".round($hotel->distance,1) ?> km
								</div>
							<?php }?>

							<div class="clear"></div>
							<div class="hotel-description">
								<div>
								<?php
								$hotelDescription = $hotel->hotel_description;
								if( strlen($hotelDescription) > MAX_LENGTH_HOTEL_DESCRIPTION ){
									 echo JHotelUtil::truncate($hotelDescription, MAX_LENGTH_HOTEL_DESCRIPTION, '&hellip;', true);
								?>
								<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>">  <?php  echo JText::_('LNG_READ_MORE',true);?></a>
								<?php }
								else{
									echo $hotelDescription;
								}
								?>
								</div>
								<div class="hotel-selling-points">
									<?php echo $hotel->hotel_selling_points ?>
								</div>
								<ul class="hotel_links">
									<li> <a href="<?php echo JHotelUtil::getHotelLink($hotel)."?".strtolower(JText::_("LNG_PHOTO")) ?>"> <?php  echo ($hotel->hotel_pictures_count)." ".JText::_('LNG_PHOTOS');?></a> </li>
									<li> <a href="<?php echo JHotelUtil::getHotelLink($hotel)."?".strtolower(JText::_("LNG_MAP")) ?>"> <?php  echo JText::_('LNG_VIEW_ON_MAP');?></a> </li>
								</ul>
								<div class="clear"></div>
							</div>

						</div>
						<div class="hotel-details span3">
							<?php if($hotel->noReviews >= MINIMUM_HOTEL_REVIEWS & $this->appSettings->enable_hotel_rating==1)
								{
									echo ReviewsService::getRatingSummaryHtml($hotel,$hotel->noReviews);
								}
							?>
					
 
							<?php if($hotel->recommended==1){?>
								<div class="hotel-recommanded">
									<span><?php  echo JText::_('LNG_RECOMMENDED',true);?></span>
								</div>
							<?php } ?>

							<?php if(!empty($hotel->roomsLeft) && !empty($appSettings->rooms_left) && $hotel->roomsLeft<=$appSettings->rooms_left){ ?>
							    <div class="roomsLeftInfo">
                                      <?php  echo $hotel->roomsLeft >= 1 ? $hotel->roomsLeft." ".JText::_("LNG_ROOMS_LEFT") : ""; ?>
								</div>
							<?php } ?>
						</div>
						
						<div class="row-fluid">
							<?php if(!empty($hotel->room_min_rate)  && (!isset($this->userData->voucher) || $this->userData->voucher=='')){ ?>
							 <div class="hotel-price span7 right textRight ">
								<div class="right clear span6 paddingr10">
									<span class=""> <?php  echo JText::_('LNG_ROOMS',true);?> </span>
									<span class=" "><?php  echo strtolower(JText::_('LNG_FROM',true));?> </span>
									<span class="price "><?php echo $currency ?> <?php echo JHotelUtil::fmt($hotel->room_min_rate,2) ?></span>
								</div>
								<br>
								<div class="span5 clear right">
									<a class="ui-hotel-button" href="<?php echo JHotelUtil::getHotelLink($hotel) ?>"><?php  echo JText::_('LNG_CHOOSE_YOUR_ROOM',true);?>&nbsp;<i class="fa fa-chevron-right font10"></i></a>
								</div>
							</div> 
							<?php } ?>
						</div> 
						
						<div class="clear"></div>

						<div class="hotel-packages">

								<?php if(!empty($hotel->offers) & $this->appSettings->is_enable_offers){ ?>
								<div class="hotel-offers clear">
								<!-- h3><?php echo JText::_("LNG_OFFERS")?></h3-->
										<?php foreach($hotel->offers as $i=>$offer){;?>
											<?php if ($i>=2) break;?>
												<div id="offer-<?php echo $offer[1]?>" class="offer-container span12" onclick="getRoomCalendar(<?php echo $hotel->hotel_id ?>, <?php echo $userData->year_start?>, <?php echo $userData->month_start?> ,'<?php echo ''.$offer[1].''.$offer[7]?>')">
													<div class="overview">
														<div class=" package-cell">
															<div class="toggle"><div></div></div>
															<div class="name span3"><strong><?php echo $offer[0]?></strong> <?php echo JText::_("LNG_OFFER")?></div>
																								
															
															 <div class="nights span2">
																<em><?php echo JText::_("LNG_NUMBER_OF_NIGHTS")?></em>
																<strong><?php echo $offer[3]?></strong>

															</div>
															<?php if(!empty($offer[9])){ ?>
																<div class="last-minute-offer span2">
																	<?php echo JText::_('LNG_LAST_MINUTE_OFFER');?>
																</div>
															<?php } ?>
															 <div class="right span2">
															 	<a class="ui-hotel-button prevent-click small" href="<?php echo JHotelUtil::getHotelLink($hotel)?>">
																	<?php echo JText::_('LNG_BOOK')?>
																</a>
															</div>
															<div class="price right span2">
																<?php
																$oldOfferPrice = '';
																$discount = JHotelUtil::discountPricePercentage($offer["price"],$offer[10]);
																if(!empty($offer[10]) && !empty($discount))
																{
																	$oldOfferPrice = '<span class="old-price">'.$currency. "&nbsp;" . number_format( $offer[10], 2 ).'</span>';
																}?>
																<?php 
																if(!empty($offer[10]) && !empty($discount)){?>
																	<span class="percentDiscount">
																	<?php ?>
																	<?php echo !empty($discount)? $discount."%":"";?>
																	</span>
																<?php }	?>
																<?php 
																echo $oldOfferPrice;
																?>
																<span class="price-small">
																	<span class="currency"><?php echo $currency ?></span><span class="amount"><?php echo JHotelUtil::fmt($offer["price"],2) ?></span>

																</span>

															 </div>
														</div>
														<div class="clear"></div>
													</div>
													<div id="offer-details-<?php echo $offer[1]?>" class="offer-details  row-fluid tr_with_dspl_none">
														<div  class="offer-description span6">
															<?php
																echo $offer[8];
															?>
														</div>
														<div class="span6 offer-calendar" id="calendar-holder-<?php echo ''.$offer[1].''.$offer[7]?>" class="room-calendar">
															<div class="room-loader right"></div>

														</div>
														<div class="clear"></div>
													</div>

												</div>
										<?php } ?>
									<?php if(count($hotel->offers)>2){ ?>
										<div class="span12">
											<a href="<?php echo JHotelUtil::getHotelLink($hotel) ?>">
												<?php echo JText::_("LNG_SHOW_ALL")." ".count($hotel->offers)." ".strtolower(JText::_("LNG_OFFERS"))." ".strtolower(JText::_("LNG_FROM_HOTEL"))." ".stripslashes($hotel->hotel_name) ?>
											</a>
										</div>
									<?php } ?>
								</div>
								<?php } ?>
						</div>
					</div>
				</div>
			<?php
					}
				}
			?>
			<div class="pagination">
				<?php echo $this->pagination->getListFooter(); ?>
				<div class="clear"></div>
			</div>
	</div>


	<input type="hidden" name="tip_oper" 			id="tip_oper" 				value="-2" />
	<input type="hidden" name="tmp" 				id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type='hidden' name='jhotelreservation_datas' value='<?php echo $userData->start_date?>'>
	<input type='hidden'	name='jhotelreservation_datae' value='<?php echo $userData->end_date?>'>
	<input type='hidden'	name='rooms' 			value='<?php echo $userData->rooms?>'>
	<input type='hidden'	name='guest_adult' 		value='<?php echo $userData->adults?>'>
	<input type='hidden'	name='guest_child' 		value='<?php echo $userData->children?>'>
	<input type='hidden'	name='year_start' 		value='<?php echo $userData->year_start?>'>
	<input type='hidden'	name='month_start' 		value='<?php echo $userData->month_start ?>'>
	<input type='hidden'	name='day_start'		value='<?php echo $userData->day_start ?>'>
	<input type='hidden'	name='year_end' 		value='<?php echo $userData->year_end?>'>
	<input type='hidden'	name='month_end' 		value='<?php echo $userData->month_end?>'>
	<input type='hidden'	name='day_end' 			value='<?php echo $userData->day_end?>'>
	<input type='hidden'	name='searchId'		id="searchId" value='<?php echo JRequest::getVar("searchId") ?>'>
	<input type='hidden'	name='searchType'		id="'searchType'" value='<?php echo JRequest::getVar("searchType") ?>'>
	<input type='hidden'	name='resetSearch'		id="'resetSearch'" value='true'>
	<input type='hidden'	name='filterParams'		id="filterParams" value='<?php  echo $this->searchFilter ?>'>
</form>

<?php 
if (isset($this->userData->excursions) && is_array($this->userData->excursions) && count($this->userData->excursions)!=0)
{
?>
<form action="<?php echo JRoute::_('index.php?task=guestDetails.showGuestDetails'.JHotelUtil::getItemIdS()) ?>" method="post"  name="skipAccomodations" id="skipAccomodations" >
	<div class="right div_height">
		<span>
			<button class="ui-hotel-button" type='submit'><?php echo JText::_('LNG_SKIP_ACCOMODATION',true)?></button>
		</span>
	</div>
	<input type="hidden" name="tip_oper" 			id="tip_oper" 				value="-2" />
	<input type="hidden" name="view" 			id="view" 				value="guestDetails" />
	<input type="hidden" name="task" 			id="task" 				value="guestDetails.showGuestDetails" />
	<input type="hidden" name="option" 			id="option" 				value="<?php echo getBookingExtName()?>" />
	<input type="hidden" name="tmp" 				id="tmp" 					value="<?php echo JRequest::getVar('tmp') ?>" />
	<input type='hidden'	name='jhotelreservation_datas' value='<?php echo $userData->start_date?>'>
	<input type='hidden'	name='jhotelreservation_datae' value='<?php echo $userData->end_date?>'>
	<input type='hidden'	name='rooms' 			value='<?php echo $userData->rooms?>'>
	<input type='hidden'	name='guest_adult' 		value='<?php echo $userData->adults?>'>
	<input type='hidden'	name='guest_child' 		value='<?php echo $userData->children?>'>
	<input type='hidden'	name='year_start' 		value='<?php echo $userData->year_start?>'>
	<input type='hidden'	name='month_start' 		value='<?php echo $userData->month_start ?>'>
	<input type='hidden'	name='day_start'		value='<?php echo $userData->day_start ?>'>
	<input type='hidden'	name='year_end' 		value='<?php echo $userData->year_end?>'>
	<input type='hidden'	name='month_end' 		value='<?php echo $userData->month_end?>'>
	<input type='hidden'	name='day_end' 			value='<?php echo $userData->day_end?>'>
	<input type='hidden'	name='filterParams'		id="filterParams" value='<?php  echo $this->searchFilter ?>'>
	<input type='hidden'	name='reserved_item' 	value='0|0'>
	<input type='hidden'	name='hotel_id'		id="hotel_id" value='0'>
</form>
<?php }?>

<form action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotel'.JHotelUtil::getItemIdS()) ?>" method="post" name="searchFormHotel" id="searchFormHotel">
	<input type="hidden" value="hotel.changeSearch" name="task">
	<input type='hidden' name='resetSearch' value='true'>
	<input id="hotel_id" type="hidden" value="" name="hotel_id">
	<input type="hidden" value="" id="jhotelreservation_datas2" name="jhotelreservation_datas" >
	<input type="hidden" value="" id="jhotelreservation_datae2" name="jhotelreservation_datae" >
	<input type='hidden'	name='rooms' 			value='<?php echo $userData->rooms?>'>
	<input type='hidden'	name='guest_adult' 		value='<?php echo $userData->adults?>'>
	<input type='hidden'	name='guest_child' 		value='<?php echo $userData->children?>'>
	<input type='hidden'	name='year_start' 		value='<?php echo $userData->year_start?>'>
	<input type='hidden'	name='month_start' 		value='<?php echo $userData->month_start ?>'>
	<input type='hidden'	name='day_start'		value='<?php echo $userData->day_start ?>'>
	<input type='hidden'	name='year_end' 		value='<?php echo $userData->year_end?>'>
	<input type='hidden'	name='month_end' 		value='<?php echo $userData->month_end?>'>
	<input type='hidden'	name='day_end' 			value='<?php echo $userData->day_end?>'>
</form>
</div>
<script>
	// fix for hotel container on some templates
	jQuery(document).ready(function(){
		jQuery('body').removeClass("homepage");
		jQuery('body').addClass("subpage");
	});
    /**
     * Data for the markers consisting of a name, a LatLng and a zIndex for
     * the order in which these markers should display on top of each
     * other.
     */
    var hotels = [
        <?php
       		echo (JHotelUtil::generateMapInfoContent($this->hotels));
         ?>
    ];
    function displayMap(enable,disable) {
        document.getElementById('map').style.display = "block";
        disable.removeAttribute('class', 'hidden');
        enable.setAttribute('class', 'hidden');
        disable.setAttribute('class', 'visible');
        loadMapScript(2,hotels);
    }

    function hideMap(enable,disable){
        document.getElementById('map').style.display="none";
        disable.setAttribute('class', 'hidden');
        enable.setAttribute('class', 'visible');
    }
		//not used anymore
		function setCheckedValue(radioObj, newValue) {
			if(!radioObj)
				return;
			var radioLength = radioObj.length;
			if(radioLength == undefined) {
				radioObj.checked = (radioObj.value == newValue.toString());
				return;
			}
			for(var i = 0; i < radioLength; i++) {
				radioObj[i].checked = false;
				if(radioObj[i].value == newValue.toString()) {
					radioObj[i].checked = true;
				}
			}
		}

		function showHotel(hotelId, selectedTab){
			jQuery("#tabId").val(selectedTab);
			jQuery("#tip_oper").val('-1');
			jQuery("#controller").val('');
			jQuery("#task").val('checkAvalability');
			jQuery("#hotel_id").val(hotelId);
			jQuery("#adminForm").submit();
		}

		function changeOrder(orderField){
			//jQuery("#orderBy").val(orderField);

//			console.log(document.getElementsByName('orderBy'));

//			return false;
			jQuery("#adminForm").submit();
		}

	function changeOrdering(element) {
		var orderBy = document.getElementsByName('orderBy');

		var options = orderBy[0];

		for (var key = 0 ; key <= 5; key++) {
				if (options[key].value) {
					var str = options[key].value;
					var orderString = options[key].value.split(' ');
					options[key].value = str.replace(orderString[1], document.getElementById(element).value);
				}
		}
		var form = document.getElementById('adminForm');
		form.submit();
	}


		jQuery(document).ready(function(){
			jQuery('.offer-container').click(function(){
				jQuery(this).toggleClass("open");
				offerId = jQuery(this).attr("id");
				offerId = offerId.replace("offer-","");

				if(jQuery(this).hasClass("open")){
					jQuery("#offer-details-"+offerId).slideDown(100);
				}else{
					jQuery("#offer-details-"+offerId).slideUp(100);
				}
			});

			jQuery(".offer-description").click( function(event) {
			    event.stopPropagation();
			} );

			jQuery(".room-calendar").click( function(event) {
			    event.stopPropagation();
			} );

			jQuery(".room-calendar").click( function(event) {
			    event.stopPropagation();
			} );

			jQuery(".prevent-click").click( function(event) {
			    event.stopPropagation();
			} );

			jQuery(".offer-details").click( function(event) {
			    event.stopPropagation();
			} );


			showRoomCalendars(<?php echo $this->hotels[0]->hotel_id ?>);


		});

		function showRoomCalendars(hotelId){
			var postParameters='';
			postParameters +="&hotel_id="+hotelId;
			postParameters +="&current_room=1";
			postParameters +="&tip_oper=-1";

			var postData='&task=hotel.getRoomCalendars'+postParameters;
			jQuery.post(baseUrl, postData, processShowRoomCalendarResults);
		}

		function processShowRoomCalendarResults(responce){
			var xml = responce;
			jQuery("<div>" + xml + "</div>").find('answer').each(function()
			{
				var identifier = jQuery(this).attr('identifier');
				//console.debug(identifier);
				jQuery("#calendar-holder-"+identifier).html(jQuery(this).attr('calendar'));
			});
		}

		function checkReservationPendingPayments(){
			var postParameters='';
			var postData='&task=hotel.checkReservationPendingPayments';
			jQuery.post(baseUrl, postData, processShowRoomCalendarResult);
		}

		function getRoomCalendar(hotelId, year,month, identifier){
			var htmlContent = jQuery("#calendar-holder-"+identifier).html();

			if(htmlContent.search("room-calendar")==-1){
				showRoomCalendar(hotelId, year,month, identifier);
			}
		}

		function showRoomCalendar(hotelId,year,month, identifier){
			var postParameters='';
			postParameters +="&month="+month;
			postParameters +="&year="+year;
			postParameters +="&identifier="+identifier;
			postParameters +="&hotel_id="+hotelId;
			postParameters +="&tip_oper=-1";
			postParameters +="&current_room=1";

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
				//console.debug(identifier);
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

		function selectCalendarDate(hotelId, startDate, endDate){
			jQuery('#jhotelreservation_datas2').val(startDate);
			jQuery('#jhotelreservation_datae2').val(endDate);
			jQuery('#hotel_id').val(hotelId);
			jQuery("#searchFormHotel").submit();
		}
	</script>
