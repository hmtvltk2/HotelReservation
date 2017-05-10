<?php 
JTable::addIncludePath('administrator/components/com_jhotelreservation/tables');

class ReservationService{

	//create reservation from saved data in confirmation table
	function getReservation($reservationId=null, $hotelId = null, $checkAvailability = true){
		if(!isset($reservationId))
			$reservationId = JRequest::getInt("reservationId");
	
		if(empty($hotelId))
			$hotelId = -1;
		$confirmationTable = JTable::getInstance('Confirmations','Table', array());
		$reservation = $confirmationTable->getReservationData($reservationId);
		
		if(!$reservationId){
			$reservation = UserDataService::createUserData(array(),new stdClass());
			$reservation->hotelId = $hotelId;
		}else{
			$reservation->reservedItems = explode(",", $reservation->items_reserved);
			$reservation->extraOptionIds = explode(";", $reservation->extraOptionIds);
			$reservation->airportTransfers = empty($reservation->airportTransfers)?null:explode(",", $reservation->airportTransfers);
			$reservation->hotelId = $reservation->hotel_id;
			$reservation->guestDetails = $this->prepareGuestDetails($reservation->guestDetails);
			$reservation->roomGuests= explode(",", $reservation->total_adults);
			$reservation->total_adults = 0;
		
			
			if(isset($reservation->roomGuests) && count($reservation->roomGuests)>=1){
				foreach($reservation->roomGuests as $index=>$guestPerRoom){
					$values = explode("|",$guestPerRoom);
					$reservation->roomGuests[$index] = $values[0];
					$reservation->total_adults+= $values[0];
				}
			}
			
			$reservation->roomGuestsChildren= explode(",", $reservation->children);
			$reservation->total_children = 0;
			if(isset($reservation->roomGuestsChildren) && count($reservation->roomGuestsChildren)>=1){
				foreach($reservation->roomGuestsChildren as $guestPerRoom){
					$values = explode("|",$guestPerRoom);
					$reservation->total_children+= $values[0];
				}
			}
		}

		if(!isset($reservation->totalPaid))
			$reservation->totalPaid = 0;
		
		$hotel = HotelService::getHotel($reservation->hotelId);
		$reservation->currency = HotelService::getHotelCurrency($hotel);
		$_SESSION['userData'] = $reservation;
		
		$reservationData = new stdClass;
		$reservationData->userData = $reservation;
		$reservationData->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$reservationData->hotel = $hotel;
		
		$extraOptionIds = isset($reservationData->userData->extraOptionIds)?$reservationData->userData->extraOptionIds:null;
		$extraOptions = array();
		if(is_array($extraOptionIds) && count($extraOptionIds)>0){
			foreach($extraOptionIds as $key=>$value){
				if(strlen($value)>1){
					$extraOption = explode("|",$value);
					$extraOptions[$key] = $extraOption;
				}
			}
		}
		if(empty($reservationData->userData->hotelId))
			$reservationData->userData->hotelId =-1;


		$reservationDetails = new stdClass;
		if($reservationId){
			$reservationDetails = $this->generateReservationSummary($reservationData, $checkAvailability);
		}
		$reservationDetails->reservationData = $reservationData;

		$reservationDetails->billingInformation = $this->getBillingInformation($reservationData->userData, $reservationData->appSettings->hide_user_email);
		
		$reservationDetails->confirmation_id = $reservation->confirmation_id;
		$paymentDetails = PaymentService::getConfirmationPaymentDetails($reservation->confirmation_id);
		
		if(isset($paymentDetails) && $paymentDetails->confirmation_id!=0)
			$reservationDetails->paymentInformation = $this->getPaymentInformation($paymentDetails, $reservationDetails->total, $reservationDetails->cost);
		
		return $reservationDetails;
	}
	//used by both the confirmation data and in progress reservation 
	public function generateReservationSummary($reservationData, $checkAvailability = true){
		//generate data for rooms
		$startDate = $reservationData->userData->start_date;
		$endDate = $reservationData->userData->end_date;
		$hotelId = $reservationData->userData->hotelId;
        $currency = new stdClass();
		if(isset($reservationData->userData->currency))
			$currency = $reservationData->userData->currency;
		

		if(empty($currency->name)){//this should never happen
            //If currency not selected or empty from the backend
            //application settings currency is selected
            $appCurrency = JHotelUtil::getApplicationSettings();
            $result = CurrencyService::getCurrency($appCurrency->currency_id);
			$currency = new stdClass();
			$currency->name = $result->description;
			$currency->symbol = $result->currency_symbol;
		}
		$currency = JHotelUtil::getCurrencyDisplay($currency,null,null);
	
		if(empty($hotelId)) 	
			$hotelId = -1;
		$discountCode = $reservationData->userData->discount_code;
		
		$reservedItems = $reservationData->userData->reservedItems;
		
		//price override from edit reservation
		$roomsPrices = array();
		if(isset($reservationData->userData->room_prices))
			$roomsPrices = explode(",", $reservationData->userData->room_prices);
		
		if(isset($reservationData->userData->roomCustomPrices)){
			$roomsPrices = $reservationData->userData->roomCustomPrices;
		}

		$selectedRooms = $this->getSelectedRooms($reservedItems, $roomsPrices, $hotelId, $startDate, $endDate, $reservationData->userData->roomGuests,$reservationData->userData->roomGuestsChildren, $discountCode, $checkAvailability,$reservationData->userData->confirmation_id);
		$roomsInfo = $this->getReservationDetailsRooms($reservationData->userData, $selectedRooms, $currency);
		
		$nrRooms = count($selectedRooms);
		$roomNotAvailable = array();
		$showDiscounts = false;
		foreach($selectedRooms as $room){
			if($room->is_disabled){
				$roomNotAvailable[] = $room;
			}
			if(isset($room->hasDiscounts) && $room->hasDiscounts){
				$showDiscounts = true;
			}
		}
		
		//generate extra options
		$extraOptionsInfo = null;
		$extraOptionIds = isset($reservationData->userData->extraOptionIds)?$reservationData->userData->extraOptionIds:null;
		$extraOptions = array();
		if(is_array($extraOptionIds) && count($extraOptionIds)>0){
			foreach($extraOptionIds as $key=>$value){
				if(strlen($value)>1){
					$extraOptionsArray = explode(";",$value);//multiple extras
					foreach($extraOptionsArray as $idx=>$extraValue){
						$extraOption = explode("|",$extraValue);
						$extraOptions[$extraOption[3]."-".$extraOption[2]] = $extraOption;
					}
				}
			}
		}
	
		$selectedExtraOptions = array();
		if(isset($extraOptions) && count($extraOptions)>0){
			$selectedExtraOptions = ExtraOptionsService::getHotelExtraOptions($hotelId, $startDate, $endDate, $extraOptions, 0, 0);
			$extraOptionsInfo = ExtraOptionsService::getReservationDetailsExtraOptions($selectedExtraOptions,$extraOptions, $nrRooms,$currency);
				
		}
		//generated airport trasnfer	
		$airportTransfersInfo = null;
		$selectedAirportTransfers = array();
		if(!empty($reservationData->userData->airportTransfers)){
			$airportTransfers = AirportTransferService::getHotelAirportTransferTypes($hotelId, $reservationData->userData->airportTransfers);
			$selectedAirportTransfers = AirportTransferService::getSelectedTransfers($airportTransfers, $reservationData->userData->airportTransfers,$nrRooms,$reservedItems);
			$airportTransfersInfo = AirportTransferService::getReservationDetailsAirportTransfer($selectedAirportTransfers, $nrRooms,$currency);
		}
		
		//generate course/excursions
		$excursionsInfo = null;
		$selectedExcursions = array();
		if($reservationData->appSettings->enable_excursions && isset($reservationData->userData->excursions) && count($reservationData->userData->excursions)>0){
			$excursionsData= $reservationData->userData->excursions;
			if(!is_array($excursionsData))
				$excursionsData = array($excursionsData);
			foreach ($excursionsData as $excursionData){
				$excursionItems = explode("_",$excursionData);
				$startDate= JHotelUtil::convertToMysqlFormat($excursionItems[2]);
				$selectedExcursion =ExcursionsService::getSelectedExcursions($excursionData, $startDate, $endDate, $discountCode, $checkAvailability,$reservationData->userData->confirmation_id);
				$selectedExcursions = array_merge($selectedExcursions,$selectedExcursion);
			}
			$excursionsInfo = $this->getReservationDetailsExcursions($reservationData->userData, $selectedExcursions, $currency);
		}
		$costData = $this->getReservationCostData($selectedRooms);
		
		$guestDetails = array();
		if(isset( $reservationData->userData->guestDetails)){
			$guestDetails = $reservationData->userData->guestDetails;
		}
		
		$taxes = TaxService::getTaxes($hotelId);
		$reservationDetails = $this->getReservationDetails($reservationData, $roomsInfo, $extraOptionsInfo,$airportTransfersInfo,$excursionsInfo, $taxes, $guestDetails, $currency, $costData);
		$reservationDetails->rooms = $selectedRooms;
		$reservationDetails->roomsInfo = $roomsInfo;
		$reservationDetails->extraOptions = $selectedExtraOptions;
		$reservationDetails->extraOptionsInfo = $extraOptionsInfo;
		$reservationDetails->airportTransfers = $selectedAirportTransfers;
		$reservationDetails->airportTransfersInfo = $airportTransfersInfo;
		$reservationDetails->roomNotAvailable= $roomNotAvailable;
		$reservationDetails->showDiscounts = $showDiscounts;
		$reservationDetails->costData= $costData;
		$reservationDetails->excursions= $selectedExcursions;
		$reservationDetails->excursionsInfo = $excursionsInfo;
		$reservationDetails->taxes = $taxes;
		
		return $reservationDetails;
	}

	function prepareGuestDetails($guestDetails){
		$result = array();
		$guestDetails = explode(",", $guestDetails);
		foreach($guestDetails as $guestDetail){
			$guest = new stdClass();
			$value = explode("|",$guestDetail);
			if(isset($value[0]) && isset($value[1]) && isset($value[2])){
				$guest->first_name = $value[0];
				$guest->last_name = $value[1];
				$guest->identification_number = $value[2];
				$result[] = $guest;
			}
			
		}
		return $result;
	}
	
	function getSelectedRooms($reservedItems, $customPrices, $hotelId, $startDate, $endDate, $roomGuests, $roomGuestsChildren, $discountCode, $checkAvailability = true,$confirmationId=null){
		$selectedRooms = array();
		
		foreach($reservedItems as $reservedItem){
			$values = explode("|",$reservedItem);
			if(count($values)<2) continue;
			$nr_guests= 0;
			$selectedRoom = null;
			if(isset($roomGuests[$values[2]-1]))
				$adults = $roomGuests[$values[2]-1];
			else 
				$adults = 2;
			if(isset($roomGuestsChildren[$values[2]-1]))
				$children = $roomGuestsChildren[$values[2]-1];
			else
				$children = 0;
			
			if($values[0]==0){
				$selectedRoom = HotelService::getHotelRooms($hotelId, $startDate, $endDate,array($values[1]), $adults, $children, $discountCode, $checkAvailability,$confirmationId);
			}else{
				$selectedRoom = HotelService::getHotelOffers($hotelId, $startDate, $endDate,array($reservedItem), $adults, $children, $discountCode, $checkAvailability,$confirmationId);
			}
			if(count($selectedRoom)==0){
				$selectedRoom = new stdClass();
				$selectedRoom->current =$values[2];
				$selectedRoom->is_disabled=false;
				$selectedRoom->hasDiscounts=false;
				$selectedRoom->offer_id = 0;
				$selectedRoom->reservation_cost_val  = 0;
				$selectedRoom->reservation_cost_proc= 0;
				$selectedRoom->customPrices =array();
				$selectedRoom->daily =array();
				$selectedRooms[$values[2]-1] = $selectedRoom;
			}
			else{
				$selectedRoom = $selectedRoom[0];
				$selectedRoom->current = $values[2];
				$selectedRoom->customPrices =  $this->getCustomPrices($selectedRoom, $customPrices);
				$selectedRooms[$values[2]-1] = $selectedRoom;
			}
			
		}
		ksort($selectedRooms);
		
		return $selectedRooms;
	}

	
	function getCustomPrices(&$room, $customPrices){
		$result = array();
		foreach($customPrices as $customPrice){
			if(!empty($customPrice)){
				$values = explode("|",$customPrice);
				if(!empty($values) &&  $values[0] == $room->offer_id && $values[1] == $room->room_id && $values[2] == $room->current){
					$result[$values[3]] = $values[4];
				}
			}
		}
	
		return $result;
	}

  	function getReservationDetails($reservationData, $roomsInfo, $extraOptionsInfo,$airportTransferInfo,$excursionsInfo, $taxes, $guestDetails, $currency, $costData){
		ob_start();
		?>
			<table class="reservation_details" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 1px solid #eaeaea; background: #FFF;border-collapse: separate;">
			  <thead>
				<tr bgcolor="#c7d9e7" class='rsv_dtls_main_header'>
					<th  colspan="7" align="left" style="line-height:24px;font-family:Tahoma,sans-serif;font-size:13px;padding:0px 0px 0px 9px">
						<strong><?php echo JText::_('LNG_RESERVATION_DETAILS',true); ?></strong>
					</th>
				</tr>
			 </thead>
				<tbody class='rsv_dtls_container'>
				<?php if( isset($reservationData->hotel->hotel_id) && $reservationData->hotel->hotel_id >0 ) { ?>
					<tr bgcolor="#FFF" style="background:#FFF;" class='rsv_dtls_hotel_container'>
                        <td style="padding: 3px 0px 3px 9px;" colspan="7">
                           <div style="font-family:Tahoma,sans-serif;font-size:13px;">
                           		<table style="width:100%">
                           			<tr>
                           				<td style="font-family:Tahoma,sans-serif;font-size:13px;width: 150px;vertical-align: top;">
			                                <div style=" -moz-box-shadow: 0 2px 5px #969696;-webkit-box-shadow: 0px 2px 5px #969696; box-shadow: 0px 2px 5px #969696; float: left;display: inline-block;padding: 2px;background-color: #FFFFFF;margin-right: 5px;">
			                                    <img height="100" style="height: 100px !important;border: medium none; float: left;"
			                                         src="<?php echo isset($reservationData->hotel->pictures[0])?JURI::root() .PATH_PICTURES.$reservationData->hotel->pictures[0]->hotel_picture_path:"" ?>" alt="Hotel Image" />
			                                </div>
			                             </td >
			                             <td style="font-family:Tahoma,sans-serif;font-size:13px;">
			                                <div style="display: inline-block;  margin-bottom: 5px;float:left;margin-right: 5px;">
			                                    <span style="line-height: 24px;margin: 0;"><?php echo $reservationData->hotel->hotel_name?>
			                                        <span>
			                                            <?php
			                                                for ($i=1; $i<=$reservationData->hotel->hotel_stars; $i++){ ?>
			                                                <img  style="display:inline;" src='<?php echo JURI::root() ."administrator/components/".getBookingExtName()."/assets/img/star.png" ?>' />         <?php }?>
			                                        </span>
			                                    </span>
			                                    <br>
			
			                                    <span class="hotel-address">
			                                        <?php echo $reservationData->hotel->hotel_address?><br> <?php echo isset($reservationData->hotel->hotel_zipcode)?$reservationData->hotel->hotel_zipcode.", ":""?> <?php echo $reservationData->hotel->hotel_city?> <br> <?php echo $reservationData->hotel->hotel_county?>, <?php echo $reservationData->hotel->country_name?>
			                                    </span>
			                                    <br>
			                                    <span class="hotel-address" style="display: block;"><?php echo JText::_('LNG_TELEPHONE_NUMBER',true).' '.$reservationData->hotel->hotel_phone  ?></span>
			                                </div>
			                            </td>
		                             </tr>
                               </table>
                               <div class="clear"></div>
                           </div>
                        </td>
					</tr>
					<?php } ?>

					<tr bgcolor="#FFF" style="background:#FFF;" class='rsv_dtls_hotel_container'>
						<td style="padding: 3px 9px 3px 0;" colspan="10">
							<table>
								<?php
									if( isset($reservationData->userData->confirmation_id) && $reservationData->userData->confirmation_id>0 )
									{
									?>
									<tr>
										<td  align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<strong><?php echo JText::_('LNG_ID_RESERVATION',true); ?></strong>
										</td>
										<td style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;" colspan="4" align="left">
											<span class='title_ID'><?php echo JHotelUtil::getStringIDConfirmation($reservationData->userData->confirmation_id)?></span>
										</td>
									</tr>
									<?php
									}
									?>
									<?php if($reservationData->userData->rooms && count($roomsInfo)>0){?>
									<tr>	
										<td align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<strong><?php echo JText::_('LNG_ARIVAL'); ?></strong>
										</td>
										<td  align="left" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<?php echo JHotelUtil::getDateGeneralFormat($reservationData->userData->start_date) ?> (<?php echo strtolower(JText::_('LNG_CHECK_IN')); ?> <?php echo $reservationData->hotel->informations->check_in  ?> <?php echo strtolower(JText::_('LNG_HOURS')); ?>)
										</td>
									</tr>	
									<tr>	
										<td  align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<strong><?php echo JText::_('LNG_DEPARTURE'); ?></strong>
										</td>
										<td  align="left" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<?php echo JHotelUtil::getDateGeneralFormat($reservationData->userData->end_date) ?> (<?php echo strtolower(JText::_('LNG_CHECK_OUT')); ?> <?php echo  $reservationData->hotel->informations->check_out ?> <?php echo strtolower(JText::_('LNG_HOURS')); ?>)
										</td>
									</tr>	

									<tr>
										<td  align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<strong><?php echo isset($reservationData->hotel->types) && $reservationData->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_NUMBER_OF_PARKS',true) : JText::_('LNG_NUMBER_OF_ROOMS',true); ?></strong>
										</td>
										<td  align="left" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<?php echo $this->getRoomNames($roomsInfo);?>
											 
										</td>
									</tr>
								
									<tr>	
										<td align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<strong><?php echo JText::_('LNG_GUESTS'); ?></strong>
										</td>
										<td   align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
												<?php echo $reservationData->userData->total_adults > 0? $reservationData->userData->total_adults.'&nbsp;' : ""?>
												&nbsp;&nbsp;&nbsp;<?php if(isset($reservationData->userData->total_children))  echo $reservationData->userData->total_children > 0? $reservationData->userData->total_children.'&nbsp;'.JText::_('LNG_CHILD_S',true) : ""?>
										</td>					
									</tr>
									<tr>	
										<td align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<strong><?php echo JText::_('LNG_NIGHTS'); ?></strong>
										</td>
										<td   align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<?php echo JHotelUtil::getNumberOfDays($reservationData->userData->start_date, $reservationData->userData->end_date);?>
										</td>					
									</tr>
									<?php }?>
									<?php if(!empty($reservationData->userData->remarks) || !empty($reservationData->userData->discount_code)){?>
									<tr>	
										<td align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
											<strong><?php echo JText::_('LNG_REMARKS'); ?></strong>
										</td>
										<td  align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
												<?php echo $reservationData->userData->remarks ?>
												<br/>
												<?php
												$totalRooms = 0;
												$appliedDiscountCodes  = '';
												foreach($roomsInfo as $key=>$roomInfo){
													// calc the room price without discount
													//$totalRooms += $roomInfo->roomPriceWD;
													$discountCodes         = $reservationData->userData->discount_code;
													$appliedDiscountCodes  = '';
													$comma                 = '';

													$selectedDiscounts = ReservationService::getReservationDiscounts((int) $reservationData->hotel->hotel_id, $reservationData->userData->reservedItems, $discountCodes, $reservationData->userData->start_date, $reservationData->userData->end_date, $roomInfo->roomPriceWD, true );
													foreach ( $selectedDiscounts->discounts as $discount )
													{
														if(!empty($discount->code))
														{
															$appliedDiscountCodes .= $comma . $discount->code;
															$comma = ',';
														}
													}
												}

												if(isset($reservationData->userData->discount_code))
												{
													$reservationData->userData->discount_code = $appliedDiscountCodes;
													echo !empty($appliedDiscountCodes)?JText::_( 'LNG_DISCOUNT_CODE' ) . " " . $appliedDiscountCodes:'';
												}
												?>
										</td>
									</tr>
									<?php }?>
									<?php if(isset($guestDetails) && count($guestDetails)>0){?>
										<tr>
											<td align="left" valign="top" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
												<strong><?php echo JText::_('LNG_GUEST_DETAILS'); ?></strong>
											</td>
											<td>
												<table>
													<tr>
														<td style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
															<?php echo JText::_('LNG_FIRST_NAME');?>
														</td>
														<td style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
															<?php echo JText::_('LNG_LAST_NAME');?>
														</td>
														<td style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
															<?php echo JText::_('LNG_PASSPORT_NATIONAL_ID',true);?>
														</td>
													</tr>
													<?php foreach($guestDetails as $guestDetail){?>	
														<tr>
															<td style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
																<?php echo $guestDetail->first_name?>
															</td>
															<td style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
																<?php echo $guestDetail->last_name?>
															</td>
															<td style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
																<?php echo $guestDetail->identification_number?>
															</td>
														</tr>
													<?php } ?>
												</table>
											</td>
										</tr>
									<?php } ?>
							</table>
						</td>
					</tr>
				
					
				
					<tr bgcolor="#c7d9e7" class='rsv_dtls_header'>
						<td colspan="6" align="left" style="line-height:24px;font-family:Tahoma,sans-serif;font-size:13px;padding-left:9px;"><strong><?php echo JText::_('LNG_ITEM')?></strong>&nbsp;</td>
						<td align="right" style="line-height:24px;font-family:Tahoma,sans-serif;font-size:13px;"><?php echo JText::_('LNG_SUBTOTAL')?>&nbsp;</td>
					</tr>
					<!--room details  -->
					<?php
						$reservationPrice = 0; 
						foreach($roomsInfo as $key=>$roomInfo){
							$subtotalRooms = 0;
							
							//display room details
							if($roomInfo->roomPrice!=0)
								echo $roomInfo->roomDescription;
							$subtotalRooms += $roomInfo->roomPrice;
							
							//display extra options for rooms
							if(isset($extraOptionsInfo[$key])){
								echo $extraOptionsInfo[$key]->description;
								$subtotalRooms += $extraOptionsInfo[$key]->extraOptionsAmount;
							}
							//display airport transfers for rooms
							if(isset($airportTransferInfo[$key])){
								echo $airportTransferInfo[$key]->description;
								$subtotalRooms += $airportTransferInfo[$key]->airportTransferAmount;
							}
							
							//total price per room
							$reservationPrice += $subtotalRooms;
						?>
						
						<?php if($subtotalRooms!=0){?>
							<tr class='rsv_dtls_subtotal'  bgcolor="#FEFEFE" >
								<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;">
									<strong><?php echo JText::_('LNG_ESTIMATED_SUBTOTAL' )?> (<?php echo $currency?>)</strong>
								</td>
								<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
									<strong><?php echo JHotelUtil::fmt($subtotalRooms,2)?></strong>
								</td>
							</tr>
						<?php }?>
						<?php 
						}
						//display courses/excursions
						if(is_array(($excursionsInfo))){
                            if(empty($currency)){

                                $currency->name = $excursionsInfo[0]->currency_name;
                                $currency->symbol = $excursionsInfo[0]->currency_symbol;
                            }
	  						foreach($excursionsInfo as $key=>$excursionInfo){
								$subtotalExcursions= 0;
								echo $excursionInfo->name;
								if(!$roomsInfo[0]->isOffer){
									$subtotalExcursions += $excursionInfo->excursionPrice;
									$reservationPrice += $subtotalExcursions;
								}
							?>
                         	<?php if($subtotalExcursions>0){?>
								<tr class='rsv_dtls_subtotal' bgcolor="#FEFEFE">
                           			<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo $excursionInfo->excursionDescription;?></strong>	</td>
                                 	<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">&nbsp;</td>
                                </tr>
                                <tr class='rsv_dtls_subtotal' bgcolor="#FEFEFE">
                                	<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php if($subtotalExcursions>0) echo JText::_('LNG_ESTIMATED_SUBTOTAL' )?> (<?php echo $currency?>)</strong>
                                	</td>
                                    <td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo JHotelUtil::fmt($subtotalExcursions,2)?></strong>
                                   	</td>
                                 </tr>
							<?php 
								}
							}
						}
					?>
					<tr class='rsv_dtls_total_room_price' bgcolor="#FEFEFE">
						<td align="right" colspan="6" style="border-top:solid 1px #eaeaea;padding: 3px 0px;font-family:Tahoma,sans-serif;font-size:13px;"  >
							<strong><?php echo JText::_('LNG_TOTAL_ROOMS_RATES')?> (<?php echo $currency?>)</strong>
						</td>
						<td align="right" style="border-top:solid 1px #eaeaea;padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;" >
							<strong><?php echo JHotelUtil::fmt($reservationPrice)?></strong>
						</td>
	
					</tr>
				
					<?php 
						$val_taxes = 0;
						$taxesTotal = 0; 
						foreach( $taxes as $tax)
						{
							if( $tax->tax_type =='Fixed'){
								$val_taxes = $tax->tax_value;
							}else{
								$extrasTotal = 0; 
								if($tax->apply_to_extras==0)
								foreach($roomsInfo as $key=>$roomInfo){
									//display extra options for rooms
									if(isset($extraOptionsInfo[$key])){
										$extrasTotal += $extraOptionsInfo[$key]->extraOptionsAmount;
									}
								}
								$reservationPrice -=$extrasTotal;
								$val_taxes = ($tax->tax_value * $reservationPrice / 100);
							}
							
							if( $val_taxes == 0 )
								continue;
							?>
							<tr>
								<td colspan=6 align="right" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
									<?php echo $tax->tax_name?>
									(<?php echo (($tax->tax_value).' '.($tax->tax_type=='Fixed'? ($currency) : ' % ') )?>)
								</td>

								<td align="right" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
									<?php echo JHotelUtil::fmt($val_taxes)?>
								</td>

							</tr>
							<?php
							$taxesTotal += $val_taxes;
						}
						$reservationPrice += $taxesTotal;

					if($reservationData->appSettings->charge_only_reservation_cost)
						$costData->bIsCostV = true;

					if( $costData->bIsCostV )
					{
                        //echo $info_discount;
                        ?>
						<tr class='rsv_dtls_subtotal' bgcolor="#FEFEFE">
							<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo JText::_('LNG_COST_VALUE',true)?>
									(<?php echo $currency?>)</strong>
							</td>
							<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo JHotelUtil::fmt( $costData->costV,2)?>
							</strong>
							</td>
						</tr>
						<?php
					 }
					$extrasCost = 0;
					$extras=0;
					$extrasAvailable = false;
					$excludedExtrasAmount = 0;

					 if( $costData->bIsCostV )
					 {
						//extras commission amount to pay from extras
						foreach($roomsInfo as $key=>$roomInfo){
							//display extra options for rooms
							if(isset($extraOptionsInfo[$key]) && $extraOptionsInfo[$key]->extrasCost>0){
								$extrasAvailable = true;
								$extrasCost += $extraOptionsInfo[$key]->extrasCost;
								$extras += $extraOptionsInfo[$key]->extraOptionsAmount;
								$excludedExtrasAmount += $extraOptionsInfo[$key]->excludedExtrasAmount;
							}
						}
						 // applied only if the extras has commission equal or greater than 0
						 // the extras amount with this type of commission will be added here in this statement to the reservation total otherwise
						 // if the extra has not a commission set (empty) the value will be added in the room estimation total
						 // NOT here
						?>
						<tr class='rsv_dtls_subtotal' bgcolor="#FEFEFE">
							<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo JText::_('LNG_ESTIMATED_TOTAL')?>
									(<?php echo $currency?>)</strong>
							</td>
							<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo JHotelUtil::fmt( ($reservationPrice) + $costData->costV,2)?>
							</strong>
							</td>
						</tr>
						<?php
					 }

					 
					$total_cost	= 0;
                    $reservationDiscount = false;
					$total_cost_without_discount = 0;

					if( $reservationData->userData->totalPaid == 0 )
					{

                        $total_cost  += $extrasCost + $costData->costV + $costData->percent * ($reservationPrice-$excludedExtrasAmount) /100;
						$total_cost_without_discount  += $extrasCost + $costData->costV + $costData->percent * $reservationPrice /100;
                        $info_discount	= '';
                        if(isset($reservationData->userData->discount_code) && (!empty($costData->costV) || !empty($costData->percent))) {
	                            $discountCodes = $reservationData->userData->discount_code;
		                        $selectedDiscounts = self::getReservationDiscounts($reservationData->hotel->hotel_id, $reservationData->userData->reservedItems, $discountCodes, $reservationData->userData->start_date, $reservationData->userData->end_date, $total_cost );
		                        foreach ( $selectedDiscounts->discounts as $discount )
		                        {
			                        $total_cost = isset( $discount->discountReservationValue ) ? $discount->discountReservationValue : $total_cost;
			                        if ( isset( $discount->discountReservationValue ) )
			                        {
				                        $reservationDiscount = true;
			                        }

			                        if ( isset( $discount->reservation_cost_discount ) && $discount->reservation_cost_discount == true && isset( $discount->discountReservationValue ) )
			                        {
				                        $info_discount .= '<div class="discount_info">' . $discount->discount_name . ' ' . JHotelUtil::fmt( - 1 * $discount->discount_value ) . '' . ( $discount->percent == 1 ? "%" : " " . $currency ) . '</div>';
			                        }
		                        }


	                        if( strlen($info_discount)>0){
                                $info_discount = "<tr class='rsv_dtls_subtotal'><td align=right colspan='6'><strong>".$info_discount."</strong></td><td align=right style='padding: 3px 9px;'></td></tr>";
                            }
                        }

                        ?>
					<?php



					$extrasCostLabel = "<tr class='rsv_dtls_subtotal'  bgcolor='#FEFEFE'><td colspan=6 align='right'><strong>".JText::_('LNG_EXTRAS',true)." ".JText::_('LNG_COST',true)." (".$currency.")</strong></td><td align=right style='padding: 3px 9px;'><strong>".JHotelUtil::fmt($extrasCost)."</strong></td></tr>";

				    if($extrasAvailable && $extrasCost > 0 && $excludedExtrasAmount > 0 ) {
					    echo $extrasCostLabel;
				    }
				    ?>

					<tr class='rsv_dtls_subtotal'  bgcolor="#FEFEFE">
						<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;">
							<strong>
								
								<?php echo JText::_('LNG_AMOUNT_PAY',true)?> 
								
								<?php
								if($costData->costV > 0 )
									echo "(".JText::_('LNG_COST_VALUE',true);
								if($costData->costV  > 0 && $costData->percent  > 0  )
									echo ' + ';
								if($costData->percent  > 0 )
									echo $costData->percent.'% '.JText::_('LNG_ESTIMATED_SUBTOTAL',true);
								if($costData->costV > 0 )
									echo ")";
								?>
								
								(<?php echo $currency?>)
							</strong>
						</td>

						<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
							<?php if($costData->bIsCostV || $costData->bIsCostP){ ?>
								<strong><?php
                                    echo JHotelUtil::fmt(( $extrasCost + $costData->costV + $costData->percent * ($reservationPrice-$excludedExtrasAmount) /100),2);
                                    ?> </strong>
							<?php }else{?>
								<strong><?php echo JHotelUtil::fmt($reservationPrice, 2)?></strong>
							<?php } ?>
						</td>
					</tr>
                    <?php echo $reservationDiscount?$info_discount:''; ?>
					<?php
					}
					$reservationPrice = $reservationPrice + $costData->costV;

                    if($reservationDiscount){?>
                    <tr class='rsv_dtls_subtotal'  bgcolor="#FEFEFE">
                        <td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;">
                            <strong>
                                <?php echo JText::_('LNG_AMOUNT_PAY',true)?>
                                (<?php echo $currency?>)
                            </strong>
                        </td>

                        <td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
                            <?php if($costData->bIsCostV || $costData->bIsCostP){ ?>
                                <strong><?php
                                    echo JHotelUtil::fmt($total_cost,2);
                                    ?> </strong>
                            <?php }else{ ?>
                                <strong><?php echo JHotelUtil::fmt($reservationPrice, 2)?></strong>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }?>

				<?php
					if( $reservationData->userData->totalPaid >0 )
					{
						?>
							<tr class='rsv_dtls_subtotal' bgcolor="#FEFEFE">
								<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo JText::_('LNG_TOTAL_PAID',true)?>
										(<?php echo $currency?>) <?php echo isset($reservationData->userData->payment_method)?' - '.strtoupper($reservationData->userData->payment_method):'' ?></strong>
								</td>
								<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo JHotelUtil::fmt( $reservationData->userData->totalPaid,2)?>
								</strong>
								</td>
							</tr>
							<?php
						 }
				
				if( $total_cost >= 0 || $reservationData->userData->totalPaid > 0)
				{
					?>
					<tr class='rsv_dtls_subtotal'  bgcolor="#FEFEFE">
	                                      <td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;"><strong><?php echo isset($reservationData->hotel->types) && $reservationData->hotel->types[0]->id == PARK_TYPE_ID ?JText::_('LNG_REMAINING_PARK_PAY',true) : JText::_('LNG_REMAINING_PAY',true)?> 
							(<?php echo $currency?>)
							</strong>
                                               </td>
						<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
							<?php if ($reservationData->userData->totalPaid == 0 ){?>
								<strong><?php
									$cost = $reservationDiscount?$total_cost_without_discount:$total_cost;
									echo JHotelUtil::fmt($reservationPrice - $cost,2)?></strong>
							<?php }else{?>
								<strong><?php echo JHotelUtil::fmt($reservationPrice - $reservationData->userData->totalPaid,2)?></strong>
							<?php }?>
						</td>
					</tr>
					<?php
				} ?>
				</tbody>
			</table>
			<?php
		
			$reservationInfo = ob_get_contents();
			ob_end_clean(); 
		
			$reservationDetails = new stdClass();
			$reservationDetails->total = $reservationPrice;
			$reservationDetails->cost = $total_cost;
			$reservationDetails->reservationInfo = $reservationInfo;

			return $reservationDetails;
	}

	public function getReservationDetailsRooms($resevation, $rooms, $currency){
		$result = array();
		$nr_days_except_offers	= 0;
		$index = 0;
		$offerItems = "";
		$extraNightItems = "";
		$hotel = HotelService::getHotel($resevation->hotelId);
		$userData =  $_SESSION['userData'];
		
		foreach( $rooms as $room )
		{
			$index++;
			$showRoomDescription = true;
			$roomInfo = new stdClass();
			$totalRoomPrice 	= 0;
			$totalRoomPriceWithoutdiscount = 0;
			$dayCounter = 0;
			$showPricePerDay = true;
			if(isset($room->price_type_day) && $room->price_type_day == 1) {
				$showPricePerDay = false;
			}
			ob_start();

			foreach( $room->daily as $day)
			{
				$price_day = $day['price_final'];
				$priceWD   = $day['priceWD'];
				if(isset($room->customPrices) && isset($room->customPrices[$day["date"]])){
					$price_day = $room->customPrices[$day["date"]]; 
				}
				
				$info_discount	= '';
				$dayCounter ++;

				foreach( $day['discounts'] as $d )
				{
                    if($d->reservation_cost_discount == false) {
                        if (strlen($info_discount) > 0)
                            $info_discount .= '<BR>';
                        $info_discount .= $d->discount_name . ' ' . JHotelUtil::fmt(-1 * $d->discount_value) . '' . ($d->percent == 1 ? "%" : " " . $currency);
                    }
				}
	
				if( strlen($info_discount)>0){
					$info_discount = "<div class='discount_info'>".$info_discount.'</div>';
				}
			
				?>
				
				<tr class='rsv_dtls_room_info'>
					<?php
					if( $showRoomDescription == true)
					{
						?>
						<td colspan=5 align="left" valign="top" style="width:50%;border-top:solid 1px #EAEAEA;padding: 3px 9px;text-align:left;font-family:Tahoma,sans-serif;font-size:13px;"	rowspan='<?php echo !$showPricePerDay ? 1:count($room->daily)*2?>'>
							
							<?php if(count($rooms)>1){ ?>
								<strong>#<?php echo $index?></strong>
							<?php }?>
							<?php
									$offerItems = "";
									if((int)$room->offer_id  > 0){
										echo "<strong>".$room->offer_name."</strong> <br/>";
										echo $room->offer_content;

										if($room->included_info)
										{
											$offerItems .= "<br><b>" . JText::_( 'LNG_INCLUDED_ITEMS' ) . "</b>";
											$offerItems .= "<br><b>" . JText::_( 'LNG_ROOM' ) . "</b>:" . $room->room_name;
											if ( strlen( $room->excursions ) > 0 )
											{
												$offerItems .= "<br><b>" . JText::_( 'LNG_EXCURSION' ) . "</b>:" . $room->excursions;
											}
											$extraOptions = ExtraOptionsService::getOfferExtraOptions( $room->offer_id, $resevation->start_date, $resevation->end_date );
											if ( strlen( $extraOptions ) > 0 )
											{
												$offerItems .= "<br><b>" . JText::_( 'LNG_EXTRA_OPTIONS' ) . "</b>:" . $extraOptions;
											}

											echo $offerItems;
										}
									} 
									else
									{
										echo '<strong>'.$room->room_name.'</strong>'.' (<i>'.JText::_('LNG_CAPACITY',true).' '.$room->max_adults.' '.strtolower(JText::_('LNG_ADULTS',true)).($room->max_children > 0 ?' | '.$room->max_children.' '.JText::_('LNG_CHILDREN',true):'').'</i>)';
									}
								?>

								<?php
									if($room->offer_id  > 0 && $room->offer_max_nights <count($room->daily)){
										echo "<br/>";
										echo JText::_('LNG_EXTRA_NIGHT_BREAKFAST_INCLUDED',true);
										
									}
								?>
						</td>
						<?php
						$showRoomDescription = false;
					}
					?>
					
				<?php
				$totalRoomPrice += $price_day;
				$totalRoomPriceWithoutdiscount += $priceWD;
				if( isset($room->offer_id) && $room->offer_id > 0)
				{
					$nr_days_except_offers++;
				}
			
				if(isset($room->price_type_day) && $room->price_type_day == 1) {
						if($day["isExtraNight"]){
							$totalRoomPriceWithoutdiscount +=$day["priceWD"];
						}
				}
				?>	
					<td align="center" valign="top" style="border-top:solid 1px #eaeaea;padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
						<?php 
							if(isset($room->price_type_day) && $room->price_type_day == 0) {
								echo JHotelUtil::getDateGeneralFormat($day['date']);
							}
							echo $info_discount;
						?>
					</td>
					<td align="right" valign="top" style="border-top:solid  1px #eaeaea;padding: 3px;font-family:Tahoma,sans-serif;font-size:13px;">
						&nbsp;
						<?php
						if(!empty($userData->currency)){
							$totalRoomPrice = CurrencyService::convertCurrency($totalRoomPrice,$hotel->hotel_currency,$userData->currency->name);
							$price_day = CurrencyService::convertCurrency($price_day,$hotel->hotel_currency,$userData->currency->name);
						}
						echo JHotelUtil::fmt($showPricePerDay?$price_day:$totalRoomPrice,2);
						?>
					</td>
					
					
				</tr>
				
				<tr >
					<td  colspan="5"></td>
					<td align="left" valign="top" style="font-family:Tahoma,sans-serif;font-size:13px;">
						<?php
							if (isset($day["isExtraNight"] ) && $day["isExtraNight"] && isset($day["extra_night_price"])) {
								$extraNightPrice = JHotelUtil::fmt($day["extra_night_price"],2);
								if(isset($room->price_type_day) && $room->price_type_day == 0)
									$extraNightPrice = JHotelUtil::fmt($extraNightPrice/(count($room->daily) - $room->offer_max_nights),2);
								echo "<br><b>" . JText::_( 'LNG_EXTRA_NIGHT_CHARGE' )."</b> : ".$currency." ".$extraNightPrice." ".strtolower(JText::_("LNG_PER_PERSON")." ".JText::_("LNG_PER")." ".JText::_("LNG_NIGHT"));
							}
							if($room->adults == 1)
								if(!empty($room->offer_price_type) && isset($room->offer_single_balancing) && !empty($room->offer_single_balancing)){
									$price = $room->offer_single_balancing;
									if($room->price_type_day==1){
										 $price = JHotelUtil::fmt((((count($room->daily) - $room->offer_max_nights)*($room->offer_single_balancing/$room->offer_min_nights))+ $room->offer_single_balancing)/count($room->daily),2);
									}
									if(!empty($price))
										echo "<br><strong>".JText::_('LNG_SINGLE_SUPPLEMENT')."</strong>: ".$currency.$price." ".strtolower(JText::_("LNG_PER")." ".JText::_("LNG_NIGHT"));
								}else if($room->price_type == 1){
									if(!empty($room->single_balancing))
										echo '<br><strong>'.JText::_('LNG_SINGLE_SUPPLEMENT').'</strong>: '.$currency.$room->single_balancing;
								}
											
							 echo $info_discount;
						?>
					</td>
				</tr> 
				
				<?php 
				if(isset($room->price_type_day) && $room->price_type_day == 1) {
					break;
				}
			}
			?>
			
				<tr class='rsv_dtls_subtotal'  bgcolor="#EEE">

					<td colspan=6 align="right" style="font-family:Tahoma,sans-serif;font-size:13px;">
						<strong><?php echo JText::_('LNG_ROOM_SUBTOTAL',true)?> (<?php echo $currency?>)</strong>
					</td>
					<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
						<strong><?php echo JHotelUtil::fmt($totalRoomPrice,2)?></strong>
					</td>
		
				</tr>
				<?php
			
			$roomInfo->name = $room->offer_id >0? $room->offer_name:$room->room_name;
			$roomInfo->isOffer = $room->offer_id >0?true:false;
			$roomInfo->roomName = $room->room_name;
			$roomInfo->offerItems =  $offerItems;
			$roomInfo->roomDescription = ob_get_contents();
			ob_end_clean();
			$roomInfo->roomPrice = $totalRoomPrice;
			$roomInfo->roomPriceWD = $totalRoomPriceWithoutdiscount;
			$result[] = $roomInfo;
		}
		return $result;
	}
	
	//display room names for offers in summary 
	function getRoomNames($roomsInfo){
		
		$content = "";
		if(count($roomsInfo)==1)
			$content = "1 x ".$roomsInfo[0]->roomName;
		else
			foreach ($roomsInfo as $idx=>$roomInfo){
				if($roomInfo->isOffer){
					$content .= "#".($idx+1).": ".$roomInfo->roomName;
					$content .=count($roomsInfo)-1 != $idx?", ":"";
				}
			}

		return $content; 
	}
	
	public function getReservationDetailsExcursions($resevation, $excursions, $currency){
		$result = array();
		$nr_days_except_offers	= 0;
		$index = 0;
		
		foreach( $excursions as $excursion )
		{
			$index++;
			$currency = CurrencyService::getCurrency($excursion->currency_id);
			$showExcursionDescription = true;
			$excursionInfo = new stdClass();
			$totalExcursionPrice 	= 0;
			$dayCounter = 0;
			if(isset($resevation->reservedItems[0]))
				$reserveParts = explode("|",$resevation->reservedItems[0]);
			else 
				$reserveParts[0] = 0;
			$isOffer = $reserveParts[0]>0?true:false;
			$showPricePerDay = true;
			if(isset($excursion->price_type_day) && $excursion->price_type_day == 1) {
				$showPricePerDay = false;
			}
			ob_start();
				
			foreach( $excursion->daily as $day)
			{
				$price_day = $day['price_final'];
				if(isset($excursion->customPrices) && isset($excursion->customPrices[$day["date"]])){
					$price_day = $excursion->customPrices[$day["date"]];
				}
				
				$price_day *= $excursion->nrItemsBooked;
	
				$info_discount	= '';
				$dayCounter ++;
				foreach( $day['discounts'] as $d )
				{
                    if($d->reservation_cost_discount == false) {
                        if (strlen($info_discount) > 0)
                            $info_discount .= '<BR>';
                        $info_discount .= $d->discount_name . ' ' . JHotelUtil::fmt(-1 * $d->discount_value) . '' . ($d->percent == 1 ? "%" : " " . $currency->currency_symbol);
                    }
				}
	
				if( strlen($info_discount)>0){
					$info_discount = "<div class='discount_info'>".$info_discount.'</div>';
				}
					
				?>
					
					<tr class='rsv_dtls_excursion_info'>
						<?php
						if( $showExcursionDescription)
						{
							?>
							<td colspan=5 align="left" valign="top" style="border-top:solid 1px grey;padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;"	rowspan='<?php echo !$showPricePerDay ? 1:count($excursion->daily)?>'>
								
								<?php if(count($excursions)>1){ ?>
									<strong>#<?php echo $index?></strong>
								<?php }?>
								<?php
											echo '<strong>'.$excursion->excursion_name.'</strong>'.' ('.JText::_('LNG_FOR',true).' '.$excursion->nrItemsBooked.')';
									?>
							</td>
							<?php
							$showExcursionDescription = false;
						}
						?>
						
					<?php
					$totalExcursionPrice += $price_day;
					?>
					
					<td align="left" valign="top" style="border-top:solid  1px grey;padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
							
							<?php
								 if(isset($excursion->price_type_day) && $excursion->price_type_day == 1) {
									$nrDays = JHotelUtil::getNumberOfDays($resevation->start_date, $resevation->end_date); 
									//TODO - get nr Days
									echo $nrDays." ".strtolower(JText::_("LNG_NIGHTS"));
								 }else{
									echo JHotelUtil::getDateGeneralFormat($day['date']);
								 }
						
								 echo $info_discount;
							?>
						</td>
						<td align="right" valign="top" style="border-top:solid  1px grey;padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
							&nbsp;
							<?php
							echo $isOffer?JText::_("LNG_INCLUDED"):JHotelUtil::fmt($showPricePerDay?$price_day:$totalExcursionPrice,2);
							?>
						</td>
						
						
					</tr>
					
					<?php 
					if(isset($excursion->price_type_day) && $excursion->price_type_day == 1) {
						break;
					}
				}
				?>
				
					<?php
				
				$excursionInfo->name = $excursion->excursion_name;
				$excursionInfo->excursionDescription = ob_get_contents();
				$excursionInfo->currency_name = $currency->description;
				$excursionInfo->currency_symbol = $currency->currency_symbol;
				ob_end_clean();
				$excursionInfo->excursionPrice = $totalExcursionPrice;
				$result[] = $excursionInfo;
			}
			return $result;
	}
	
	public function getReservationCostData($rooms){
		$costData = new stdClass();
		$bIsCostV 	= false;
		$costV		= 0;
		$bIsCostP 	= false;
		$costP		= 0;
		$percent 	= 0;
		foreach($rooms as $room){
			if(	(( $room->offer_id  > 0 && ( $room->offer_reservation_cost_val > 0 || $room->offer_reservation_cost_proc > 0 ) )
				||( $room->offer_id == 0 && ( $room->reservation_cost_val > 0 || $room->reservation_cost_proc > 0 ) )))	{
				
				$bIsCostVi 	= ($room->offer_id  > 0 && $room->offer_reservation_cost_val > 0 ) || ($room->offer_id  == 0 && $room->reservation_cost_val > 0 );
				$costVi		=  $room->offer_id  > 0 ? $room->offer_reservation_cost_val : $room->reservation_cost_val;
				$bIsCostPi 	= ($room->offer_id  > 0 && $room->offer_reservation_cost_proc > 0) || ($room->offer_id  == 0 && $room->reservation_cost_proc > 0 );
				$costPi		= ($room->offer_id  > 0 ? $room->offer_reservation_cost_proc : $room->reservation_cost_proc) ;
				$percent	= ($room->offer_id  > 0 ? $room->offer_reservation_cost_proc : $room->reservation_cost_proc);
					
				if($bIsCostVi && ($costV < $costVi)){
					$bIsCostV = $bIsCostVi;
					$costV = $costVi;
				}
					
				if($bIsCostPi && ($costP < $costPi)){
					$bIsCostP = $bIsCostPi;
					$costP = $costPi;
				}
			}
		}
		$costData->bIsCostV 	= $bIsCostV;
		$costData->costV		= $costV;
		$costData->bIsCostP 	= $bIsCostP;
		$costData->costP		= $costP;
		$costData->percent		= $costP;
		
		return $costData;
	}

	function getBillingInformation($data, $hideEmail = false)
	{
		$gender = !empty($data->guest_type)?JText::_("LNG_ADDRESS_GUEST_TYPE_".$data->guest_type):"";
		ob_start();
		?>
			<?php //echo !empty($data->company_name)? $data->company_name."<br/>":"" ?>
			<?php echo  $gender.' '.$data->first_name.' '.$data->last_name?> <br/>
			<?php echo $data->address?><br/>							
			<?php echo $data->postal_code ." " ?>	<?php echo $data->city?><br/>
			<?php echo $data->country?><br/>
			T: <?php echo $data->phone?><br/>
			<?php if(!$hideEmail){ ?><a href='mailto:<?php echo $data->email?>'><?php echo $data->email?></a><br/><br/>	<?php } ?>
			<?php
			$buff = ob_get_contents();
			ob_end_clean(); 
	
			return $buff;
	}
	
	function getPaymentInformation($paymentDetails, $amount, $cost){
		$paymentDetails->processor_id = isset($paymentDetails->processor_id)?$paymentDetails->processor_id:0; // 0 is for Cash
		$processor = PaymentService::createReservationPaymentProcessor($paymentDetails->processor_type,$paymentDetails->processor_id); 
		ob_start();
	
		echo "<ul style='margin:0px;padding-left: 0;list-style:none'>";
		echo "<li style='margin-left: 0px'>";
		echo $processor->getPaymentDetails($paymentDetails, $amount, $cost);
		echo "</li>";
		echo "</ul>";
		
		$buff = ob_get_contents();
		ob_end_clean(); 
		
		return $buff;
	}
	
	static function getClientReservations($userId = null){
		if(!isset($userId))
			return null;
	
		$confirmationTable = JTable::getInstance('Confirmations','Table', array());
		$reservations = $confirmationTable->getClientReservations($userId);
		
		return $reservations;
	}

    public static function getLogInUserData($id){

        $user = JFactory::getUser();
        if(!isset($id)){
            $app = JFactory::getApplication();
            $msg =  JText::_('LNG_UNAUTHORIZED_ACCESS',true);
            //returns empty object
            return new stdClass();
        }elseif(isset($id) && $id > 0){
            $confirmationTable = JTable::getInstance('Confirmations','Table', array());
            $storedGuestUserData = $confirmationTable->getLoginUserData($id);
            if(!empty($storedGuestUserData)) {
                return $storedGuestUserData;
            }
        }

    }


    /**
     * Params used to get the object
     * @param $hotel_id
     * @param $reservedItems
     * @param $discountCodes
     * @param $start_date
     * @param $end_date
     * @param $reservationCost
     * @param $allDiscounts
     *
     * $allDiscounts is used to select only reservation costs discounts or all of the discounts applied in a reservation
     * returns discount Object
     * @return mixed
     * @throws Exception
     *
     * Method to get the discount object
     * for a reservation costs only
     * if the option for reservation cost is enabled
     */
    public static function getReservationDiscounts($hotel_id,$reservedItems,$discountCodes,$start_date,$end_date,$reservationCost,$allDiscounts = false){

	    // helper stdClass structure with an array inside
	    // to store and access easily the selected discounts
	    $selectedDiscounts    = new stdClass();
	    $selectedDiscounts->discounts = array();


	    $discountTable = JTable::getInstance('RoomDiscounts','JTable', array());

        $load = array();
        $last = count($reservedItems)-1;
        if($last!=-1) {
            $load = $reservedItems[$last];
        }
        
        $reservedItem = explode("|",$load);
        $offerId = (int)$reservedItem[0];
        $roomId  = (int)$reservedItem[1];
        $discounts = $discountTable->getReservationDiscountCoupons((int)$hotel_id,$roomId,$offerId,$start_date,$end_date,$reservationCost,$allDiscounts);
	    $discountValue   = 0;
	    $discountPercent = 0;
	    //apply discounts with a code	  
        if(!empty($discountCodes))
        {
	        $discountCodes = explode( ',', $discountCodes );
	        foreach ( $discountCodes as $idx=>$discountCode )
	        {
	        	$match           = false;
		        foreach ( $discounts as $discount )
		        {
			        if (!empty( $discountCode ) && !empty( $discount->code ))
			        {
				        if ( $discount->check_full_code == 1 )
				        {
					        $match = $discountCode == $discount->code;
				        }
				        else
				        {
					        $match = strpos( $discountCode, $discount->code ) === 0;
				        }
			        }
			        if ( $match )
			        {
			        	$selectedDiscounts->discounts[$discount->discount_id] = $discount;
			        	break; //found a code, break loop
			        }
		        }
		        if (!$match){
		        	//raise warning, code not found. 
		        	$discountCodes[$idx] = null;
		        	$app = JFactory::getApplication();
		        	$app->enqueueMessage( JText::_( "LNG_DISCOUNT_CODE_NOT_FOUND" ), 'warning' );
		        }
	        	
	        }
	        UserDataService::setDiscountCode(implode(",",$discountCodes));
        }

        //apply discounts without a code 
	   	foreach ( $discounts as $discount ){
	        if(empty($discount->code))
		        $selectedDiscounts->discounts[$discount->discount_id] = $discount;
	    }
        
	    // do calculation only for reservation costs to apply in the reservation amount to pay
	    if(!$allDiscounts && isset($selectedDiscounts->discounts))
	    {
		    foreach ( $selectedDiscounts->discounts as $discount )
		    {
			    if ( $discount->reservation_cost_discount == true )
			    {
				    if ( $discount->percent )
				    {
					    $discountValue += round( $reservationCost * $discount->discount_value / 100, 2 );
				    }
				    else
				    {
					    $discountValue += $discount->discount_value;
				    }
				    if ( isset( $reservationCost ) || !empty( $reservationCost ) )
				    {
					    //do not allow negative discount values on reservation cost
					    if ( $reservationCost >= $discountValue )
					    {
						    $reservationCost = round( $reservationCost - $reservationCost * ( $discountPercent / 100 ), 2 );
						    //apply value
						    $reservationCost                    = $reservationCost - $discountValue;
						    $discount->discountReservationValue = $reservationCost;
					    }
					    else if ( $reservationCost <= $discountValue )
					    {
						    $discount->discountReservationValue = 0;
					    }
				    }
			    }
		    }
	    }

        return $selectedDiscounts;
    }
}

?>