<?php 

class ExtraOptionsService{


    /**
     * @param int $hotelId
     * @param $startDate
     * @param $endDate
     * @param $extraOptionIds reserved items from session
     * @param $roomId related to the extra option
     * @param $offerId related to the extra option
     * @param bool|true $onlySelected
     * @param null $confirmationId
     * @return array returns the extra options selected for a hotel
     */
	public static function getHotelExtraOptions($hotelId=0, $startDate, $endDate, $extraOptionIds, $roomId, $offerId, $onlySelected = true,$confirmationId=null){
		$db = JFactory::getDBO();
		$filter="";
		if(isset($roomId) && $roomId > 0){
			$filter= " and FIND_IN_SET( ".$roomId.", room_ids  ) ";
		}

        if(isset($offerId) && $offerId > 0){
            $filter= "and FIND_IN_SET( ".$offerId.", offer_ids  ) ";
        }
		$extraFilter = "";
		if(isset($extraOptionIds) && count($extraOptionIds)>0 && empty($confirmationId)){
			$extraFilter = " eo.id in (";
			foreach( $extraOptionIds as $id )
			{
				$extraFilter .= $id[3].',';
			}
			$extraFilter = substr($extraFilter,0,-1);
			$extraFilter .= ")";
		}
		
		$whereFilter = " status = 1 ";
		if(!empty($extraFilter)){
			if($onlySelected ){
				$whereFilter = " $extraFilter ";
			}else{
				$whereFilter = "(status = 1 or $extraFilter) ";
			}
		}
		$languageTag = JRequest::getVar( '_lang');

		$query = "select eo.*
					from #__hotelreservation_extra_options eo
					WHERE
					$whereFilter
					$filter 
					and
					IF(
					eo.start_date <> '0000-00-00'
					AND
					eo.end_date <> '0000-00-00',
					('" . $startDate . "' BETWEEN eo.start_date  AND eo.end_date) and  ('" . $endDate . "' BETWEEN eo.start_date  AND eo.end_date),
					If(
						eo.start_date = '0000-00-00'
						AND
						eo.end_date <> '0000-00-00',
						'" . $endDate . "' < eo.end_date,
						if(
							eo.start_date <> '0000-00-00'
							AND
							eo.end_date = '0000-00-00',
							'" . $startDate . "' > eo.start_date ,
							1
							)
						)
					)
					and hotel_id = $hotelId
					order by ordering";
		$db->setQuery( $query );
		$extraOptions = $db->loadObjectList();
        $translations = new JHotelReservationLanguageTranslations();
        $hotel = HotelService::getHotel($hotelId);
        $userData =  $_SESSION['userData'];
        
		foreach($extraOptions as $extraOption){
			$extraOptionTranslations = $translations->getObjectTranslation(EXTRA_OPTIONS_TRANSLATION,$extraOption->id,$languageTag);
            $extraOption->description = empty($extraOptionTranslations->content)?$extraOption->description:$extraOptionTranslations->content;
            $extraOptionNameTranslations = $translations->getObjectTranslation(EXTRA_OPTION_NAME,$extraOption->id,$languageTag);
            $extraOption->name = empty($extraOptionNameTranslations->content)?$extraOption->name:$extraOptionNameTranslations->content;
			
			if(!empty($userData->currency)){
				$extraOption->price = CurrencyService::convertCurrency($extraOption->price,$hotel->hotel_currency,$userData->currency->name);
			}
		}
		return $extraOptions;
	}

    /**
     * @param $offerId the offer that includes the extras
     * @param $startDate starting date of an extra option
     * @param $endDate the date that extra option expires backend
     * @return string comma separated extra option names included in an offer
     */
	public static function getOfferExtraOptions($offerId,$startDate,$endDate){
		$row =JTable::getInstance('OffersExtraOptions',"JTable");

        $startDate =  JHotelUtil::convertToMysqlFormat($startDate);
        $endDate = JHotelUtil::convertToMysqlFormat($endDate);
        //returns a list of objects with the id and name of the extra option included in an offer
		$extraOptions = $row->getOfferExtraOptionsIncluded($offerId,$startDate,$endDate);

        //translation class to translate the extra option name
        $translationTable = new JHotelReservationLanguageTranslations();
        $languageTag = JRequest::getVar('_lang');
        //array that will hold the extra option names
        $extra = array();
        foreach($extraOptions as $extraOption){
            $extra_option = $translationTable->getObjectTranslation(EXTRA_OPTION_NAME,$extraOption->id,$languageTag);
            $extraOption->name = !empty($extraOption->name)?$extraOption->name:'';
            $extra_name = empty($extra_option->content)?$extraOption->name:$extra_option->content;
            $extra []= $extra_name;
        }
        // all extras names included in an offer separated by comma
        // to be displayed in the rooms layouts in front end
        // and in 'What's included' section in payment&confirmation screen
        $ext = implode(',',$extra);
        return $ext;
	}
	
	// set parameters from the session for a selected extra
	public static function setSelectedExtra($extraOption,$roomId,$offerId,$confirmationId,$current){
		$extraOption->checked = false;
		
		$userData =  $_SESSION['userData'];
		$extraOptionIds = self::parseRervationExtraOptions($userData->extraOptionIds);
		foreach( $extraOptionIds as $id )
		{
			if($extraOption->id == $id[3] && $current == $id[2] && (!empty($confirmationId)?$roomId==$id[1]:true)  && (!empty($confirmationId) && !empty($offerId)?$offerId==$id[0]:true)){//if reservation check room to correspond
				$extraOption->checked = true;
				$extraOption->persons = $id[5];
				$extraOption->days = $id[6];
				$extraOption->multiplier = !empty($id[8])?$id[8]:'';;
				$extraOption->current = $id[2];
				$extraOption->dates   = !empty($id[7])?$id[7]:'';
			}
		}
		return $extraOption;
	}

    /**
     * @param $data
     * @return array
     */
	public static function parseExtraOptions($data){
		$extraOptions = array();
		foreach($data["extraOptionIds"] as $key=>$value){
			$extraOption = explode("|",$value);
			if($extraOption[5]>0 || $extraOption[6]>0)
				continue;
			if(isset($data["extra-option-persons-".$extraOption[3]."-".$extraOption[2]])){
				$extraOption[5] = $data["extra-option-persons-".$extraOption[3]."-".$extraOption[2]];
			}
			if(isset($data["extra-option-days-".$extraOption[3]."-".$extraOption[2]])){
				$extraOption[6] = $data["extra-option-days-".$extraOption[3]."-".$extraOption[2]];
			}
			$extraOption[7] = "";
            if(isset($data['extraOptionDates-' . $extraOption[3] . '-' .$extraOption[2]])) {
                $extraOption[7] = implode(', ', $data['extraOptionDates-' . $extraOption[3] . '-' .$extraOption[2]]);
            }
            if(isset($data["extra-option-multiplier-".$extraOption[3]."-".$extraOption[2]])){
            	$extraOption[8] = $data["extra-option-multiplier-".$extraOption[3]."-".$extraOption[2]];
            }
            $extraOptions[$key] = implode("|",$extraOption);
		}
		return $extraOptions;
	}

    /**
     * @param $extraOptionsArray
     * @return array
     */
	public static function parseRervationExtraOptions($extraOptionsArray){
		$extraOptions = array();
		foreach($extraOptionsArray as $key=>$value){
			if(strlen($value)>0){
				$extraOption = explode("|",$value);
				$extraOptions[$key] = $extraOption;
			}
		}
		return $extraOptions;
	}

    /**
     * @param $extraOptions object list
     * @param $extraOptionIds session $extraOptionIds selected in the reservation steps
     * @param $nrRooms number of rooms booked
     * @param $currency currency that is beeing used
     * @return array returns the html that is used in the reservation view to show the extra options selected in the reservation steps
     *
     */
	public static function getReservationDetailsExtraOptions($extraOptions,$extraOptionIds, $nrRooms, $currency){
		$result = array();
		if( isset($extraOptions) && count($extraOptions) > 0 && count($extraOptionIds)>0 ){
			for($i=1;$i<=$nrRooms;$i++){
	
				$extraOptionsDetails = array();
				$extraOptionInfo = new stdClass();
				$extraOptionsArray = self::getExtraOptionIds($extraOptionIds,$i);
				$extraOptionsAmount	= 0;
				$extrasCostAmount = 0;
				$extraOptionsValue = 0;
				$excludedExtrasAmount = 0;
	
				if(is_array($extraOptionsArray) && count($extraOptionsArray)>0){
					ob_start();
					?>
						<tr class='rsv_dtls_arrival_options'>
							<td colspan=7 align=left style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
								<strong><?php echo JText::_('LNG_EXTRAS',true)?></strong>
							</td>
						</tr>
						<?php
						foreach( $extraOptions as $extraOption){
							$extraOption = self::setSelectedExtra($extraOption,null,null,null,$i);
								
							if((isset($extraOption->current) && $extraOption->current!=$i)){
								continue;
							}

                            $amount = $extraOption->price;

							$extra_option_cost = $extraOption->extra_option_cost;
							
							if($extraOption->price_type == 1){
								$amount = $amount * $extraOption->persons;
							}
							if($extraOption->is_per_day == 1 ){
								$amount = $amount * ($extraOption->days);
							}
							else if($extraOption->is_per_day == 2){
								$amount = $amount * $extraOption->days;
							}
							if($extraOption->multiplier)
								$amount = $amount * $extraOption->multiplier;
							$extraOptionCostAmount = 0;
							if((int)$extra_option_cost >= 0 ) {
								$extraOptionCostAmount = $extra_option_cost * $amount/100;
							}
							?>
							<tr>
								<td align=left colspan=6 style="padding: 3px 9px 3px 20px;font-family:Tahoma,sans-serif;font-size:13px;">
									<?php
										echo $extraOption->name.", ".$currency." ". JHotelUtil::fmt($extraOption->price,2)." ".($extraOption->price_type == 1?strtolower(JText::_('LNG_PER_PERSON',true))." ":"" )."".($extraOption->is_per_day == 1 ?strtolower(JText::_('LNG_PER_DAY',true)):"" )."".($extraOption->is_per_day == 2 ?strtolower(JText::_('LNG_PER_NIGHT',true)):"" );
										
										if($extraOption->persons > 0 || $extraOption->days > 0){
											echo "<br/><i>(";
											$showDelimiter = false;
											if($extraOption->persons > 0){
												echo strtolower(JText::_('LNG_NUMBER_OF_PERSONS',true))." ".$extraOption->persons;
												$showDelimiter = true;
											}
											
											if($extraOption->days > 0){
												if($showDelimiter){
													echo ", ";
												}
												echo strtolower(($extraOption->is_per_day == 1 ?JText::_('LNG_NUMBER_OF_DAYS',true):JText::_('LNG_NUMBER_OF_NIGHTS',true)))." ".$extraOption->days;
											}
											if($extraOption->multiplier > 0){
												if($showDelimiter){
													echo ", ";
												}
												echo " ".strtolower(JText::_('LNG_MULTIPLIER',true))." ".$extraOption->multiplier;
											}
											echo ")</i>";
										}
									?>
								</td>
								<td align=right style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;">
									&nbsp;
									<?php
										echo JHotelUtil::fmt($amount,2);
									?>
								</td>
			
							</tr>
                            <?php if(!empty($extraOption->dates)){?>
                            <tr>
                                <td align=left colspan=6 style="padding: 3px 9px 3px 20px;font-family:Tahoma,sans-serif;font-size:13px;">
                                    <?php echo JText::_('LNG_CHOSEN_EXTRA_OPTIONS_DATES') ." ". $extraOption->dates; ?>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php
							$extraOptionsDetail = new stdClass();
							$extraOptionsDetail->name = $extraOption->name;
							$extraOptionsDetail->amount = $amount;
							$extraOptionsDetail->rowId = $extraOption->id."-".$extraOption->current;
								
							$extraOptionsDetails[] = $extraOptionsDetail;
							$extraOptionsAmount += $amount;
							// if this extra option has commission save its amount for later use in the reservation service class
							if((int)$extra_option_cost >= 0 ){
								$excludedExtrasAmount += $amount;
								$extrasCostAmount += $extraOptionCostAmount;
							}
						}?>
						<tr class='rsv_dtls_room_price' bgcolor="#EFEDE9">

							<td colspan="6" style="padding: 3px 0px;font-family:Tahoma,sans-serif;font-size:13px;"  align="right">
								<strong><?php echo JText::_('LNG_EXTRA_OPTIONS_SUBTOTAL',true)?> (<?php echo $currency?>)</strong>
							</td>
							<td align="right" style="padding: 3px 9px;font-family:Tahoma,sans-serif;font-size:13px;" >
								<strong><?php echo JHotelUtil::fmt($extraOptionsAmount,2)?></strong>
							</td>
			
						</tr>
						<?php
					
						$extraOptionInfo->details  = $extraOptionsDetails;//used for the reservation info module
						$extraOptionInfo->description = ob_get_contents();
						ob_end_clean();
						$extraOptionInfo->extraOptionsAmount = $extraOptionsAmount;
						$extraOptionInfo->excludedExtrasAmount = $excludedExtrasAmount;
					    $extraOptionInfo->extrasCost = $extrasCostAmount;
						$result[$i-1] = $extraOptionInfo;
					}
				}
				
			}
			
			return $result;
		}

    /**
     * @param $extraOptionIds
     * @param $index
     * @return array
     */
		static function getExtraOptionIds($extraOptionIds, $index){
			$result = array();
			foreach($extraOptionIds as $extraOptionId){
				if($index == $extraOptionId[2]){
					$result[]=$extraOptionId[3];
				}
			}
			return $result;
		}

    /**
     * @param $extraOptionIds
     * @param $index
     * @return array
     */
		static function getExtraOptionInfo($extraOptionIds, $index){
			$result = array();
			foreach($extraOptionIds as $extraOptionId){
				if($index == $extraOptionId[2]){
					$extrInfo = new stdClass();
					$extrInfo->id = $extraOptionId[3];
					$extrInfo->persons = $extraOptionId[5];
					$extrInfo->days = $extraOptionId[6];
					$extrInfo->multiplier = empty($extraOptionId[8])?"":$extraOptionId[8];
					$extrInfo->offerId = $extraOptionId[0];
					$extrInfo->roomId = $extraOptionId[1];
					$extrInfo->current = $extraOptionId[2];
                    $extrInfo->dates   = isset($extraOptionId[7])?$extraOptionId[7]:'';
                    $result[$extrInfo->id] = $extrInfo;
				}
			}
			return $result;
		}

    /**
     * @param $extraOptions
     * @param $extraOptionId
     * @return null
     */
		static function getExtraOption($extraOptions, $extraOptionId){
			$extraOptionValues = explode("|",$extraOptionId);
			foreach($extraOptions as $extraOption){
				if($extraOption->id == $extraOptionValues[3]){
					$extraOption->nrPersons = $extraOptionValues[5];
					$extraOption->nrDays = $extraOptionValues[6];
					$extraOption->nrMultiplier = $extraOptionValues[8];
					$extraOption->offerId = $extraOptionValues[0];
					$extraOption->roomId = $extraOptionValues[1];
					$extraOption->current = $extraOptionValues[2];
                    $extraOption->dates   = isset($extraOptionValues[7])?$extraOptionValues[7]:'';
					return $extraOption;
				}
			}
			return null;
		}

    /**
     * @param $extraOptions extra option object
     * @param $reservedItems  reserved extra option session data
     * @param $currencySymbol currency that is being used to buy the extra option
     * @param int $current
     * @param null $confirmationId
     * @return string the html form and its elements of extra options selection/selected
     */
		static function getExtraOptionsHTML($extraOptions,$reservedItems,$currency,$current=1,$confirmationId=null,$start_date = null ,$end_date = null){

			$idx = count($reservedItems)-1;
			if(!empty($confirmationId))//data from reservation
				$idx = $current-1;
			ob_start();
		?>
			<div id="extra-options-container" class="">
				<?php
				foreach($extraOptions as $item ) {
					$checked        = false;
					$extraOptionKey = $reservedItems[ $idx ] . '|' . $item->id;
					$reserved       = explode( "|", $extraOptionKey );
					$offerId        = $reserved[0];
					$roomId         = $reserved[1];
					$item = self::setSelectedExtra($item,$roomId,$offerId,$confirmationId,$current);
					$checked = $item->checked;

					?>
					<div class="extra-option">
						<div class='extra-option-image'>
							<?php if ( isset( $item->image_path ) && strlen( $item->image_path ) > 0 ) {
								echo "<img 
								alt='".JHotelUtil::setAltAttribute($item->image_path)."'
								src='" . JURI::root() . PATH_PICTURES . EXTRA_OPTIONS_PICTURE_PATH . "/" . $item->image_path . "'/>";;
							} else {
								echo "<img 
								alt='".JHotelUtil::setAltAttribute('components/com_jhotelreservation/assets/img/no_image.jpg')."'
								src='" . JURI::root() . "components/com_jhotelreservation/assets/img/no_image.jpg'/>";
							}
							?>
						</div>
						<div class="styledCheckbox">
							<input 
									type	='checkbox'
									name	='extraOptionIds[<?php echo $item->id ?>-<?php echo $current; ?>]'
									id		='extraOptionIds<?php echo $item->id ?>-<?php echo $current; ?>'
									value	= '<?php echo $extraOptionKey?>|1|0|0'
									class="extraCheckbox <?php echo !empty($item->mandatory)?"validate[required]":"";?>"
									onchange="changeOptionState('options-<?php echo $item->id ?>-<?php echo $current; ?>','extraOptionDivDates_<?php echo $item->map_per_length_of_stay !='1'?$item->id:"" ?>');upateExtraOption('<?php echo $item->id."-".$current?>')"
									<?php echo $checked ? 'checked="checked"':''?> >
							<label for="extraOptionIds<?php echo $item->id ?>-<?php echo $current; ?>"></label>
						</div>
						<div class="extra-option-box">
							<strong><?php echo $item->name ?></strong>, <?php echo $currency; ?> <?php echo JHotelUtil::fmt( $item->price, 2 ) ?><?php echo $item->price_type == 1 ? ",&nbsp;" . strtolower( JText::_( 'LNG_PER_PERSON', true ) ) : "" ?><?php echo $item->is_per_day == 1 ? ",&nbsp;" . JText::_( 'LNG_PER_DAY', true ) : "" ?><?php echo $item->is_per_day == 2 ? ",&nbsp;" . JText::_( 'LNG_PER_NIGHT', true ) : "" ?>
							<p><i><?php echo $item->description ?></i></p>

							<div class="extras-options" id="options-<?php echo $item->id ?>-<?php echo $current; ?>"
							     style="<?php echo $checked? "" : "display:none"; ?>">
								<?php if ( $item->price_type == 1 ) { ?>
									<dt>
										<?php echo JText::_( 'LNG_NUMBER_OF_PERSONS', true ); ?>
									</dt>
									<dd>
										<div class="styled-select small">
											<select id="persons-<?php echo $item->id?>-<?php echo $current; ?>"
											        name="extra-option-persons-<?php echo $item->id ?>-<?php echo $current; ?>"
											        onchange="upateExtraOption('<?php echo $item->id."-".$current?>')" class="validate[required]">
												<?php for ( $i = 1; $i < 21; $i ++ ) { ?>
													<option
														value="<?php echo $i ?>" <?php echo ( isset( $item->persons ) && intVal( $item->persons ) == $i ) ? 'selected="selected"' : ''; ?> ><?php echo $i ?></option>
												<?php } ?>
											</select>
										</div>
									</dd>
								<?php } ?>
								<?php if ( $item->is_per_day == 1 || $item->is_per_day == 2 ) { ?>
									<dt>
										<?php echo $item->is_per_day == 1 ? JText::_( 'LNG_NUMBER_OF_DAYS', true ) : JText::_( 'LNG_NUMBER_OF_NIGHTS', true ) ?>
									</dt>
									<?php
									$nrDays = UserDataService::getNrDays();
										if($item->is_per_day == 1)
											$nrDays++;
									?>
									<?php if($item->map_per_length_of_stay=='1'){
											 echo $nrDays;
								    ?>
										     <input type="hidden" id="days-<?php echo $item->id?>-<?php echo $current; ?>" name="extra-option-days-<?php echo $item->id?>-<?php echo $current; ?>" value="<?php echo $nrDays ?>">
									<?php } 
									   else  {
									   ?>
									
									<div class="styled-select small">
										<select class="validate[required]" id="days-<?php echo $item->id?>-<?php echo $current; ?>" name="extra-option-days-<?php echo $item->id?>-<?php echo $current; ?>" onchange="upateExtraOption('<?php echo $item->id."-".$current?>');" <?php echo $item->map_per_length_of_stay=='1'?' onblur="this.value ='.$nrDays.'"':''?>>
											<?php for($i=1;$i<21;$i++){ ?>
												<option value="<?php echo $i ?>" <?php echo (isset($item->days) && intVal($item->days)==$i) ? 'selected="selected"':''; ?> ><?php ?><?php echo $i ?></option>
											<?php } ?>
										</select>
									</div>
									
									<?php } ?>
								<?php } ?>
								<?php if(!empty($item->multiplier)){?>
									<dt>
										<?php echo JText::_('LNG_MULTIPLIER')?>
									</dt>
									<div class="styled-select small">
										<select class="validate[required]" id="multiplier-<?php echo $item->id?>-<?php echo $current; ?>" name="extra-option-multiplier-<?php echo $item->id?>-<?php echo $current; ?>" onchange="upateExtraOption('<?php echo $item->id."-".$current?>');">
											<?php for($i=1;$i<21;$i++){ ?>
												<option value="<?php echo $i ?>" <?php echo (isset($item->multiplier) && intVal($item->multiplier)==$i) ? 'selected="selected"':''; ?> ><?php ?><?php echo $i ?></option>
											<?php } ?>
										</select>
									</div>
							<?php } ?>
							
							</div>
						</div>

						<div class="clear"></div>
					
						<div class="extra-option extras-dates" id="extraOptionDivDates_<?php echo $item->id ?>"
						     style="<?php echo ($item->map_per_length_of_stay !='1') && ( $item->checked ) ? "" : "display:none"; ?>"
						>
							<?php if ( $start_date != null || !empty($item->dates)) { ?>
								<label><?php echo JText::_( 'LNG_EXTRA_OPTIONS_DATES' ) ?></label>
							<?php 
							}
							
							if ( $start_date != null && $end_date != null ) {
								$item->dates = isset( $item->dates ) ? $item->dates : array();
								echo self::getExtraOptionIntervalDates( $start_date, $end_date, $current, $item->id, $item->dates );
							}
							elseif (!empty($item->dates) ){
								$selectedIntervalDates = explode( ", ", $item->dates );
								$i = 0;
								//selected interval dates
								?>
								<div class="extra-option">	
									<?php
									foreach ( $selectedIntervalDates as $dateI ) {
										$i ++;
										?>
										<div class="datesBlock">
											<div class="styledCheckbox">
												<input
													type='checkbox'
													name='extraOptionDates-<?php echo $item->id ?>-<?php echo $current ?>[]'
													id='extraOptionDates<?php echo $item->id ?>-<?php echo $current ?>-<?php echo $i ?>'
													value='<?php echo $dateI ?>'
													<?php echo in_array( $dateI, $selectedIntervalDates ) ? 'checked="checked"' : ''; ?>
													/>
												<label for="extraOptionDates-<?php echo $item->id ?>-<?php echo $current ?>-<?php echo $i ?>"></label>
												<strong class="datesLabel"><?php echo $dateI ?></strong>
											</div>
										</div>
										<?php
									}
									?>
								</div>
								<?php 
									}
								?>
						</div>
					</div>
					<?php
				}
				?>
		</div>
		<?php
		$buff = ob_get_contents();
		ob_end_clean();
		
		return $buff;
	}

    /**
     * @param $hotelId
     * @param $extraOptionIds
     * @param $extraOptionObject
     * @param $start_date
     * @param $end_date
     * @param $room_id
     * @param $confirmation_id
     * @return array returns the array of extra option objects from the session or not
     */
    public static function getSelectedExtras($selectedExtras,$displayedExtras,$current)
    {
        $selectedExtras = self::parseRervationExtraOptions($selectedExtras);
        foreach ($displayedExtras as $displayedExtra) {
        	foreach($selectedExtras as $selectedExtra)
                if ($displayedExtra->id == $selectedExtra[3] && $current == $selectedExtra[2]){//there is a match
        			$displayedExtra->checked = true;
                    if(!empty($selectedExtra[4]) && !empty($selectedExtra[6])) {
                        $displayedExtra->persons = $selectedExtra[5];
                        $displayedExtra->days = $selectedExtra[6];
                        $displayedExtra->multiplier = !empty($selectedExtra[8])?$selectedExtra[8]:"";
                        $displayedExtra->current = $selectedExtra[2];
                        $displayedExtra->dates   = explode(', ',!empty($selectedExtra[7])?$selectedExtra[7]:'');
                    }
                }
        }
        return $displayedExtras;
    }
    
    /**
     * @param $fromdate
     * @param $todate
     * @return string
     */
    public static function getExtraOptionIntervalDates($fromdate,$todate,$current,$extraOptionId,$dates){
    
    	$intervalDates = JHotelUtil::generateIntervalDates($fromdate,$todate);
    	if(!is_array($dates))
   			$dates = array_map('trim', explode(',', $dates));
    	$extraOptionIntervalDatesHTML = self::getIntervalDatesHTML($intervalDates,$current,$extraOptionId,$dates);
    
    	return $extraOptionIntervalDatesHTML;
    }
    
    /**
     * @param $dateIntervals
     * @return string
     */
    public static function getIntervalDatesHTML($dateIntervals,$current,$extraOptionId,$dates){
    	//generate a select option with the interval dates
    	ob_start();
    	$i = 0;
    	
    	    	?>
            <div class="extra-option">
            <?php
            foreach( $dateIntervals as $dateI ) {
                $i++;
                    ?>
                <div class="datesBlock">
                    <div class="styledCheckbox">
                        <input
                            type    ='checkbox'
                            name='extraOptionDates-<?php echo $extraOptionId ?>-<?php echo $current ?>[]'
                            id      ='extraOptionDates<?php echo $extraOptionId ?>-<?php echo $current ?>-<?php echo $i?>'
                            value   ='<?php echo strftime("%A %e %B",$dateI->getTimestamp()) ?>'
                           <?php echo in_array(strftime("%A %e %B",$dateI->getTimestamp()), $dates) ? 'checked="checked"' : ''; ?>
                         />
                        <label for="extraOptionDates<?php echo $extraOptionId ?>-<?php echo $current ?>-<?php echo $i?>"></label>
                        <strong class="datesLabel"><?php echo strftime("%A %e %B",$dateI->getTimestamp()) ?></strong>
                    </div>
                </div>
                    <?php
                }
            ?>
            </div>
                <?php
            $buff = ob_get_contents();
            ob_end_clean();
            return $buff;
        }

}


?>