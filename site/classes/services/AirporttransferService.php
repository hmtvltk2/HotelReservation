<?php
JTable::addIncludePath('administrator/components/com_jhotelreservation/tables');

class AirportTransferService {

    /**
     * @return mixed returns airlines object list
     */
    public static function getAllAirlines(){
    	$trasferTable = JTable::getInstance('Airlines','JTable', array());
    	return $trasferTable->getAirlines();
    }

    /**
     * @param $airportTransfers
     * Parse latest airport transfer from the session data
     * @return array
     */
    public static function parseAirportTransfers($airportTransfers){
    	
    	$airportTransfersArr = array();
        $countAirportArray = count($airportTransfers);
    	if(is_array($airportTransfers) && $countAirportArray>0){
    		foreach($airportTransfers as $key=>$value){
    			if(!empty($value)){
    				$airportTransfer = explode("|",$value);
    				$airportTransfersArr[$key] = $airportTransfer;
    			}
    		}
    	}
    	return $airportTransfersArr;
    }

    /**
     * @param $airportTransfersReservation
     * @param $index
     * @return array
     */
    static function getAirportTransferReservation($airportTransfersReservation, $index){

    	foreach($airportTransfersReservation as $airportTransferReservation){
    		if(!empty($airportTransferReservation) && $index == $airportTransferReservation->current){
    			return $airportTransferReservation;
    		}
    	}
    	return null;
    }


    /**
     * @param $airportTransfers selected airport transfer type
     * @param $airportTransfersReservation session airport transfer type
     * @param $nrRooms number of rooms booked
     * @param $currency currency that is beeing used
     * @return array returns the html that is used in the reservation view
     */
	public static function getReservationDetailsAirportTransfer($airportTransfers, $nrRooms, $currency){
		$result = array();
		if(!empty($airportTransfers)){
			for($i=1;$i<=$nrRooms;$i++){
				$airportTransfer = self::getAirportTransferReservation($airportTransfers,$i);
				if(empty($airportTransfer)){
					continue;
				}
			ob_start();
			?>
                <?php
                $vatPercent = 1+$airportTransfer->airport_transfer_type_vat / 100;
                $amount = (($airportTransfer->airport_transfer_type_price * $airportTransfer->airport_transfer_guest) * $vatPercent);
                $pricePerPerson = $airportTransfer->included_offer==1?JText::_('LNG_INCLUDED_IN_THE_OFFER',true).",":$currency->symbol."".JHotelUtil::fmt($airportTransfer->airport_transfer_type_price,2);
                if($airportTransfer->airport_transfer_type_id > 0){ ?>
				<tr class='rsv_dtls_arrival_options'>
					<td colspan=7 align=left style="padding: 3px 9px">
						<strong><?php echo JText::_('LNG_AIRPORT_TRANSFER',true)?></strong>
					</td>
				</tr>
				<?php

				?>
				<tr>
					<td align=left colspan=6 style="padding: 3px 9px 3px 20px;">
						<?php
							echo "<b>".$airportTransfer->airport_transfer_type_name."</b>, ".$pricePerPerson." ".strtolower(JText::_('LNG_PER_PERSON',true));
							if($airportTransfer->airport_transfer_guest > 0){
								echo "<br/>";
								$showDelimiter = false;
								if($airportTransfer->airport_transfer_guest > 0){
									echo JText::_('LNG_NUMBER_OF_PERSONS',true)." ".$airportTransfer->airport_transfer_guest;
									$showDelimiter = true;
								}
								echo "<br>";
								echo JText::_('LNG_AIRLINE_LABEL').":".$airportTransfer->airline_name;
								echo ", ".JText::_('LNG_FLIGHT_NR').":".strtoupper($airportTransfer->airport_transfer_flight_nr); 
								echo "<br/>";
								echo JText::_('LNG_DATE').' & '.JText::_('LNG_TIME').":".JHotelUtil::getDateGeneralFormat($airportTransfer->airport_transfer_date).'('.JHotelUtil::padNumber($airportTransfer->airport_transfer_time_hour,2).':'.JHotelUtil::padNumber($airportTransfer->airport_transfer_time_min).')';
							}
						?>
					</td>
					<td align=right style="padding: 3px 9px">
						&nbsp;
						<?php
							echo $airportTransfer->included_offer==1?JText::_('LNG_INCLUDED_IN_THE_OFFER',true):JHotelUtil::fmt($amount,2);
						?>
					</td>
	
				</tr>
				<tr class='rsv_dtls_room_price' bgcolor="#EFEDE9">
	
					<td colspan="6" style="padding: 3px 0px;"  align="right">
						<strong><?php echo JText::_('LNG_AIRPORT_TRANSFER_SUBTOTAL',true)?> (<?php echo $currency->name?>)</strong>
					</td>
					<td align="right" style="padding: 3px 9px" >
						<strong><?php echo $airportTransfer->included_offer==1?JText::_('LNG_INCLUDED_IN_THE_OFFER',true):JHotelUtil::fmt($amount,2)?></strong>
					</td>
	
				</tr>
                <?php  } ?>
				<?php
                $airportTransferInfo = new stdClass();
				$airportTransferInfo->description = ob_get_contents();
				ob_end_clean();
				$airportTransferInfo->airportTransferAmount = 0;
				if($airportTransfer->included_offer!=1)
					$airportTransferInfo->airportTransferAmount = $amount;
				$result[$i-1] = $airportTransferInfo;
				
			}
		}
		return $result;
	}


    /**
     * @param $sessionTransfer airport transfer type object from session
     * @param $airportTransferTypes airport transfer type object to populate the view
     * @param $userData Guest Data from the session object UserDaTa
     * @param $airport_transfer_id int the id of the airport transfer type that is included in the offer
     * @param $need_all_fields boolean for fields that are mandatory
     * @return string return the html form of the airport transfer types selected/included in the offer or not
     */
    static function getAirportTransferHTML($sessionTransfer,$airportTransferTypes,$userData,$offerTransferId,$current){
        $airportTransferId = !empty($sessionTransfer->airport_transfer_type_id) && (!empty($sessionTransfer->current)  && $sessionTransfer->current == $current)?$sessionTransfer->airport_transfer_type_id:0;
        $offerTransferId = empty($offerTransferId)?0:$offerTransferId;
        ob_start();

        $price = JText::_("LNG_PLEASE_SELECT_AIRPORT_TRANSFER_TYPE");
        $description = JText::_("LNG_PLEASE_SELECT_AIRPORT_TRANSFER_TYPE");
        $includedLabel = '';
        ?>
        <div class="aiportTransferContainer">
	        <div class="header_line">
	            <h3><?php echo JText::_('LNG_AIRPORT_TRANSFER');?></h3>
	            <div>
	                <?php echo JText::_('LNG_AIRPORT_TRANSFER_TITLE',true); ?>
	                <div class="styledCheckbox">
	                    <input
	                        type	='checkbox'
	                        name	='is_airport_transfers'
	                        id		='is_airport_transfers'
	                        <?php echo ($airportTransferId>0 || $offerTransferId>0)?'checked="checked"':'';?>
	                        onclick	= "showAirportTransfer('#div_airport_transfer',jQuery(this));"
	                        value="<?php echo ($airportTransferId>0 || $offerTransferId>0)?1:0;?>">
	                    <label for="is_airport_transfers"></label>
	                </div>
	            </div>
	        </div>
	        <div id='div_airport_transfer' class="panel-default"  <?php echo  ($airportTransferId>0 || $offerTransferId>0) > 0?"":"style='display:none'";?>>
	        	<div class="hotel_reservation">
	            <p>
	                <?php echo JText::_('LNG_FIELDS_MARKED_WITH');?>
	                <span class="mand">*</span>
	                <?php echo JText::_('LNG_ARE_MANDATORY');?>
	            </p>
	            <dl>
	                <dt><?php echo JText::_('LNG_TRANSFER_TYPE')?>: <span class='mand'>*</span> </dt>
	                <dd>
	                    <div class="styled-select medium">
	                        <select class="select_hotelreservation keyObserver inner-shadow validate[required]" name='airport_transfer_type_id' id = 'airport_transfer_type_id' onchange="populateTransfer(this.value)">
	                            <option	value=''><?php echo JText::_('LNG_SELECT_DEFAULT');?></option>
	                            <?php
	                            $includedInOffer = false;
	                            foreach( $airportTransferTypes as $valueAirportTransferType ) {
	                            
	                                ?>
	                                <option
	                                    <?php
	
	                                    if(isset($airportTransferId) && $valueAirportTransferType->airport_transfer_type_id == $airportTransferId && $valueAirportTransferType->airport_transfer_type_id != $offerTransferId ) {
	
	                                        $price =  isset($valueAirportTransferType->priceVat)?$valueAirportTransferType->priceVat:'';
	                                        $description = isset($sessionTransfer->airport_transfer_type_description)?$valueAirportTransferType->airport_transfer_type_description:'';
	                                        $includedLabel = '';
	                                        $offerTransferId = 0;
	                                        echo 'selected="selected"';
	
	                                    }elseif($valueAirportTransferType->airport_transfer_type_id == $offerTransferId){
	
	                                        $price =  JText::_('LNG_INCLUDED_IN_THE_OFFER', true);
	                                        $description = $valueAirportTransferType->airport_transfer_type_description;
	                                        $includedLabel = '</br>'.JText::_("LNG_AIRPORT_TRANSFER_TYPE_INCLUDED_IN_OFFER", true);
	                                        echo 'selected="selected"';
	                                        $includedInOffer = true;
	
	                                    }?>
	
	                                    value='<?php echo $valueAirportTransferType->airport_transfer_type_id;?>'>
	                                    <?php echo $valueAirportTransferType->airport_transfer_type_name ?>
	                                </option>
	                                <?php
	                            }
	                            ?>
	                        </select>
	                    </div>
	                </dd>
	                <dt>
	                    <?php echo JText::_('LNG_PRICE',true)?> :
	                </dt>
	
	                <dd>
	                    <span id='div_airport_transfer_type_price' name='div_airport_transfer_type_price'>
	
	                        <?php
	                            echo $price;
	                        ?>
	                    </span>
	                    &nbsp;
	
	                    <input type="hidden" name="airport_transfer_type_price" id="airport_transfer_type_price" value="">
	                    <input type="hidden" name="airport_transfer_type_vat" id="airport_transfer_type_vat" value="">
	                    <input type="hidden" name="hour" id="hour" value="">
	                    <input type="hidden" name="minute" id="minute" value="">
	                    <input type="hidden" name="included_offer" id="included_offer" value="<?php echo $includedInOffer?1:0?>">
	                    
	                    </dd>
	                <dt>
	                    <?php echo JText::_('LNG_DESCRIPTION')?> :
	                </dt>
	                <dd>
						<span id='div_airport_transfer_description' name='div_airport_transfer_description'>
	                    <?php
	                        echo $description;
	                    ?>
						</span>
	                    <span id="offer_airport_transfer_type"  name="offer_airport_transfer_type">
	                        <?php
	
	                        echo $includedLabel;
	                        ?>
	                    </span>
	                    &nbsp;
	                </dd>
	
	                <dt>
	                    <?php echo JText::_('LNG_GUEST',true)?> : <span class='mand'>*</span>
	                </dt>
	                <dd>
	                    <div class="styled-select medium">
	                        <select
	                            class="select_hotelreservation keyObserver inner-shadow validate[required]"
	                            type	='text'
	                            name	='airport_transfer_guest'
	                            id		='airport_transfer_guest'
	                            >
	                            <?php
	                            $guestDetails = isset($userData->guestDetails)?$userData->guestDetails:0;
	                            $total_children = isset($userData->total_children)?$userData->total_children:0;
	                            $number =(count((int)$guestDetails) + count((int)$total_children));
	                            for($i= 0;$i<=$number;$i++){ ?>
	                                <option
	                                    <?php
	                                   if(isset($sessionTransfer->airport_transfer_guest)){
	                                       echo $sessionTransfer->airport_transfer_guest == $i ? 'selected="selected"' : '';
	                                   }?>
	                                    value="<?php
	                                echo  $i;?>">
	                                    <?php  echo $i;?>
	                                </option>
	                            <?php } ?>
	                        </select>
	                    </div>
	                </dd>
	                <dt>
	                    <?php echo JText::_('LNG_DATE',true)?> : <span class='mand'>*</span>
	                </dt>
	                <dd>
	                    <div class="calendarHolder jqueryDatePickerDiv" id="mod_hotel_reservation">
	                        <div class="input-append" style="margin-bottom: 0 !important;">
	                            <input class="form-control customInpuT validate[required]"
	                                   data-provide="datepicker"
	                                   type="text"
	                                   value   ="<?php
	                                   echo isset($sessionTransfer->airport_transfer_date)?$sessionTransfer->airport_transfer_date:'';?>"
	                                   id="airport_transfer_date" name="airport_transfer_date">
	                            <button type="button" class="btn" onclick="jQuery('#airport_transfer_date').focus();" id="jhotelreservation_datas_img"><i class="icon-calendar"></i></button>
	                        </div>
							<span>
	                    		<label class="formInlinedisplay">
	                    			<input class="dateTime customInpuT validate[required]" id="time" data-format="HH:mm" data-template="HH : mm" name="datetime" type="text" onchange="setDateTime()" value="">
	                            </label>
	                        </span>
	                    </div>
	                </dd>
	                <dt>
	                    <?php echo JText::_('LNG_AIRLINE_LABEL',true)?> : <span class='mand' style='font-size: 10px !important;'>*</span>
	                </dt>
	                <dd>
	                    <input
	                        class="styled_input validate[required]"
	                        id		="airport_airline"
	                        name	= 'airport_airline'
	                        value   ="<?php
	                        echo isset($sessionTransfer->airline_name)?$sessionTransfer->airline_name:''; ?>"
	                        placeholder="<?php echo JText::_('LNG_TYPE_INSTRUCTIONS',true)?>"
	                        >
	                </dd>
	                <dt>
	                    <?php echo JText::_('LNG_FLIGHT_NR',true)?> : <span class='mand'>*</span>
	                </dt>
	                <dd>
	                    <input
	                        class="customInpuT validate[required]"
	                        placeholder="<?php echo JText::_('LNG_FLIGHT_NR_SAMPLE',true)?>"
	                        type	='text'
	                        name	='airport_transfer_flight_nr'
	                        id		='airport_transfer_flight_nr'
	                        value   ="<?php
	                        echo isset($sessionTransfer->airport_transfer_flight_nr)?$sessionTransfer->airport_transfer_flight_nr:'';?>">
	                </dd>
	            </dl>
	           </div>
	        </div>
	    </div>
	     
        <?php
        $buff = ob_get_contents();
        ob_end_clean();

        return $buff;
    }

    /**
     * @param $airportTransfer
     * @return stdClass
     */
 	public static function parseUserDataAirportTransfer($airportTransfer){
 		$id =explode("|",$airportTransfer);
 		$airportTransferObj = new stdClass();
 		$airportTransferObj->airport_transfer_type_id = $id[0];
 		$airportTransferObj->room_id = $id[1];
 		$airportTransferObj->airline_id = $id[2];
 		$airportTransferObj->airline_name = $id[3];
 		$airportTransferObj->current = $id[4];
 		$airportTransferObj->airport_transfer_flight_nr = $id[5];
 		$airportTransferObj->airport_transfer_date = $id[6];
 		$airportTransferObj->airport_transfer_time_hour = $id[7];
 		$airportTransferObj->airport_transfer_time_min= $id[8];
 		$airportTransferObj->airport_transfer_guest = $id[9];
        $airportTransferObj->included_offer = $id[10];
 		
 		return $airportTransferObj;
 	}


    /**
     * @param $airportTransferTypeId
     * @return mixed
     */
 	public static function getAirportTransferType($airportTransferTypeId){
 		$trasferTable = JTable::getInstance('AirportTransferTypes','JTable', array());
 		$airportTransferObj = $trasferTable->load($airportTransferTypeId);
 		return $airportTransferObj;
 	}


    /**
     * @param $offerId
     * Get the airport transfer id for an offer
     * @return mixed
     */
    public static function getOfferAirportTransfersId($offerId){
        $row = JTable::getInstance('Offers',"JTable");
        if(isset($offerId)) {
            $airport_transfer_type_id = $row->getOfferAirportTransferId($offerId);
            if (count($airport_transfer_type_id))
                return $airport_transfer_type_id;
        }
    }

    /**
     * @param $offerId
     * Get the name of the airport transfer type included in an offer
     * @return null
     */
    public static function getAirportNamesByOffer($offerId){
        $row = JTable::getInstance('Offers',"JTable");
        $offerAirportType = $row->getAirportNamesByOffer($offerId);
        if(count($offerAirportType))
            return $offerAirportType;
        else
            return null;
    }


    /**
     * @param $array
     * Get latest index of an array with its key=>value
     * @return mixed
     */
    public static function getLastIndex($array){
        $last = count($array)-1;
        if($last!=-1) {
            $load = $array[$last];
            return $load;
        }
    }

    /**
     * @param $hotelId
     * @return mixed
     */
    public static function getHotelAirportTransferTypes($hotelId,$sessionTransfers=null){

        //connect to the airport table
        $trasferTable = JTable::getInstance('AirportTransferTypes', 'JTable', array());
        $sessionTransfersIds = null;
        if(is_array($sessionTransfers)){
        	$sessionTransfersTmp = array();
        	foreach($sessionTransfers as $selectedTransfer){
        		if(!empty($selectedTransfer)){
	        		$selectedTransfer = self::parseUserDataAirportTransfer($selectedTransfer);
	        		$sessionTransfersTmp[] = $selectedTransfer->airport_transfer_type_id; 
        		}
        	}
        	$sessionTransfersIds = implode(",",$sessionTransfersTmp);
        }
        $airportTransfers = $trasferTable->getHotelAirportTransfer($hotelId,$sessionTransfersIds);

        //language loaded from the user
        $languageTag = JRequest::getVar('_lang');
        //begin translation
        $translationObj = new JHotelReservationLanguageTranslations();

        foreach ($airportTransfers as $airportTransfer) {

            //get the translation object data for airport transfer name & description
            $transferTranslations = $translationObj->getObjectTranslation(AIRPORT_TRANSFER_TRANSLATION, $airportTransfer->airport_transfer_type_id, $languageTag);
            $transferNameTranslations = $translationObj->getObjectTranslation(AIRPORT_TRANSFER_TRANSLATION_NAME, $airportTransfer->airport_transfer_type_id, $languageTag);
            $airportTransfer->airport_transfer_type_description = !empty($transferTranslations->content) ? $transferTranslations->content : $airportTransfer->airport_transfer_type_description;

            //formating the description for display in airport transfer form
            $airportTransfer->airport_transfer_type_description = strip_tags($airportTransfer->airport_transfer_type_description);
            $airportTransfer->airport_transfer_type_description = preg_replace('/([\r\n\t])/',' ', $airportTransfer->airport_transfer_type_description);

            $airportTransfer->airport_transfer_type_name = !empty($transferNameTranslations->content) ? $transferNameTranslations->content : $airportTransfer->airport_transfer_type_name;
            //end translations

            //format the price and Vat for Display only
            $airportTransfer->priceVat = $airportTransfer->airport_transfer_type_price.($airportTransfer->airport_transfer_type_vat != 0 ? (" + " . $airportTransfer->airport_transfer_type_vat . " % " . JText::_('LNG_VAT', true)) : "");
        }
        return $airportTransfers;
    }
    
    //populate data for selected transfers 
    public static function getSelectedAirportTransfer($selectedTransfers,$displayedTransfers,$current)
    {
    	$tmpTransfer = new stdClass;
    	foreach ($displayedTransfers as $displayedTransfer) {
    		foreach($selectedTransfers as $selectedTransfer){
    			$selectedTransfer = self::parseUserDataAirportTransfer($selectedTransfer);
    			if ($displayedTransfer->airport_transfer_type_id == $selectedTransfer->airport_transfer_type_id && $current == $selectedTransfer->current){//there is a match for current room and id 
    				$displayedTransfer->checked = true;
    				$displayedTransfer->airline_name = $selectedTransfer->airline_name;
    				$displayedTransfer->current = $selectedTransfer->current;
    				$displayedTransfer->airport_transfer_flight_nr = $selectedTransfer->airport_transfer_flight_nr;
    				$displayedTransfer->airport_transfer_date = $selectedTransfer->airport_transfer_date;
    				$displayedTransfer->airport_transfer_time_hour = $selectedTransfer->airport_transfer_time_hour;
    				$displayedTransfer->airport_transfer_time_min= $selectedTransfer->airport_transfer_time_min;
    				$displayedTransfer->airport_transfer_guest = $selectedTransfer->airport_transfer_guest;
    				$displayedTransfer->included_offer = $selectedTransfer->included_offer;
    				$tmpTransfer = $displayedTransfer;
    				break;
    			}
    		}
    	}
       	return $tmpTransfer; // one transfer per room
    }
    public static function getSelectedTransfers($displayedTransfers,$selectedTransfers,$nrRooms,$reservedItems)
    {
    	$transfers = array();
    	
    	for($i=1;$i<=$nrRooms;$i++){//retrieve transfer for each room 
    		$transfer = null;
    		$transfer =  self::getSelectedAirportTransfer($selectedTransfers,$displayedTransfers,$i); 
    		if(!empty($transfer->current))
    			$transfers[] = clone $transfer;
    	} 
    	return $transfers;
    }
    
}