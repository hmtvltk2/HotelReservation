<?php 
JTable::addIncludePath( JPATH_ROOT . '/administrator/components/com_jhotelreservation/tables' );
class HotelService{
	
	/**
	 * Check if a hotel is available for a period of time
	 * @param unknown_type $hotelId
	 * @param unknown_type $startDate
	 * @param unknown_type $endDate
	 * 
	 * @return true if available, false if not available
	 */
	public static function isHotelAvailable($hotelId, $startDate, $endDate){
		$hotel = JHotelUtil::getHotel($hotelId);
	
		if(strcmp($hotel->start_date,'0000-00-00')!=0 && strtotime($hotel->start_date)>strtotime($startDate) ){
			return false;
		}
	
		if(strcmp($hotel->end_date,'0000-00-00')!=0 && strtotime($hotel->end_date)<strtotime($endDate) ){
			return false;
		}

		$ignoredDays = explode(',',$hotel->ignored_dates);

		if(count($ignoredDays)>0){
			foreach($ignoredDays as $ignoredDay){
	
				if( strtotime($startDate) <= strtotime($ignoredDay) && strtotime($ignoredDay) < strtotime($endDate)){
					return false;
				}
			}
		}
	
		return true;
	}
	
	static function getHotelAvailabilyPerDay($hotelId, $startDate, $endDate){
	
		$hotelTable	= 	JTable::getInstance('hotels','Table', array());
		$hotel = $hotelTable->getHotel($hotelId);
		$availability = array();
	
		for( $d = strtotime($startDate);$d <= strtotime($endDate); ){
			$dayString = date("Y-m-d", $d);
			$available = true;
	
			if(strcmp($hotel->start_date,'0000-00-00')!=0 && strtotime($hotel->start_date)>$d ){
				$available = false;
			}
	
			if(strcmp($hotel->end_date,'0000-00-00')!=0 && strtotime($hotel->end_date)<$d ){
				$available = false;
			}
	
			$ignoredDays = explode(',',$hotel->ignored_dates);
			if(count($ignoredDays)>0){
				foreach($ignoredDays as $ignoredDay){
	
					if( $d == strtotime($ignoredDay)){
						$available = false;
					}
				}
			}
	
			$availability[$dayString]=$available;
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
	
		return $availability;
	}
	
	public static function getHotel($hotelId)
	{
		$db = JFactory::getDBO();
		// Load the data
		$languageTag 	= JRequest::getVar( '_lang' );
		
		if($hotelId<=0 || $hotelId==null){
			$hotel = new stdClass();
			$hotel->hotel_name = "";
            $hotel->hotel_alias="";
			$hotel->hotel_id= 0;
			$hotel->pictures	= array();
			
			$hotel->facilities = array();
			$hotel->chanelManager = null;
			$hotel->paymentOptions = "";
			$hotel->currency = "";
			$hotel->hotel_stars = "";
			$hotel->hotel_address = "";
			$hotel->hotel_city = "";
			$hotel->hotel_county = "";
			$hotel->country_name = "";
			$hotel->hotel_phone= "";
			$hotel->informations= new stdClass();
			$hotel->informations->check_in = "";
			$hotel->informations->check_out = "";
			$hotel->informations->city_tax = "";
			$hotel->informations->cancellation_days = "";
			$hotel->informations->id = -1;
				
			return $hotel;
		}
		$query = ' SELECT
					h.*,
					c1.country_name,
					c2.description	AS hotel_currency, c2.currency_symbol AS currency_symbol
					FROM #__hotelreservation_hotels 			h
					LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
					LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
					WHERE h.hotel_id = '.$hotelId.' AND h.is_available = 1
					';
		$db->setQuery( $query );
		$hotel = $db->loadObject();
		if(empty($hotel))
			$hotel = new stdClass();

		if(isset($hotel->hotel_name))
			$hotel->hotel_name = stripslashes($hotel->hotel_name);

		if(isset($hotel->hotel_alias))
			$hotel->hotel_alias = stripslashes($hotel->hotel_alias);

        $translations = new JHotelReservationLanguageTranslations();
        $hotelTranslations = $translations->getObjectTranslation(HOTEL_TRANSLATION,$hotelId,$languageTag);

		if (isset($hotel->hotel_description))
			$hotel->hotel_description = !empty($hotelTranslations->content)?$hotelTranslations->content:$hotel->hotel_description;

		$hotel->pictures	= array();
			
		$query = "  SELECT	*
				FROM #__hotelreservation_hotel_pictures
				WHERE hotel_id = ".$hotelId." AND hotel_picture_enable = 1
				ORDER BY hotel_picture_id
				";
		$db->setQuery( $query );
		$hotel->pictures =  $db->loadObjectList();

		$hotel->facilities = array();
		$query = "  SELECT	hf.*
					FROM #__hotelreservation_hotel_facilities hf
					inner join  #__hotelreservation_hotel_facility_relation hfc on hf.id = hfc.facilityId
					WHERE hfc.hotelId = ".$hotelId."
					ORDER BY hf.name";
		$db->setQuery( $query );
		$hotel->facilities = $db->loadObjectList();

		$hotel->types = array();
		$query = "  SELECT	hf.*
					FROM #__hotelreservation_hotel_types hf
					inner join  #__hotelreservation_hotel_type_relation hfc on hf.id = hfc.typeId
					WHERE hfc.hotelId = ".$hotelId."
					ORDER BY hf.name";
		$db->setQuery( $query );
		$hotel->types = $db->loadObjectList();

		
		$hotel->chanelManager = null;
		$query = "  SELECT	* from #__hotelreservation_hotel_channel_manager where hotel_id = $hotelId";			
		$db->setQuery( $query );
		$hotel->chanelManager = $db->loadObject();

        $hotel->contact = null;
        $query = "  SELECT	hc.booking_email from #__hotelreservation_hotel_contacts as hc where hotel_id = $hotelId";
        $db->setQuery( $query );
        $hotel->contact = $db->loadObject();

        $hotel->poi = null;
		$latitude = $hotel->hotel_latitude;
		$longitude = $hotel->hotel_longitude;

		$activeRadiusPois = self::getActivePOIs($latitude,$longitude,$hotelId,$hotel->poi);
		$hotel->poi = $activeRadiusPois;
		$enabledPicturesOnly = 1;
        if(isset( $hotel->poi ) && count( $hotel->poi )> 0){
            foreach($hotel->poi as $poi){
                $poiTranslation = $translations->getObjectTranslation(POI_TRANSLATION,$poi->id,$languageTag);
                $poi->description = !empty($poiTranslation->content)?$poiTranslation->content:$poi->description;
                $poi->distance  = JHotelUtil::distance($hotel->hotel_latitude,$hotel->hotel_longitude ,$poi->poi_latitude,$poi->poi_longitude);
                $poi->distance = JHotelUtil::fmt($poi->distance, 2);
                $poiPicturesTable = JTable::getInstance('PoiPictures','JTable',array());
                $poi->pictures = $poiPicturesTable->getPoiPictures($poi->id,$enabledPicturesOnly);
            }
        }

		$hotel->reviewAnwersScore = self::getHotelReviewScore($hotelId);

		if(isset($hotelId) && $hotelId > 0 && isset($hotel->hotel_rating_score))
			$hotel->ratingScores = ReviewsService::getHotelRatingClassifications($hotel->hotel_rating_score,$translations,$languageTag);

		

        //dmp($hotel->reviews);
        //die();

		$informationsTable = JTable::getInstance('HotelInformations','JTable',array());
		$hotel->informations =  $informationsTable->getHotelInformations($hotelId);
		$cancellationText ='';
		if(!empty($hotel->informations->uvh_agree) && $hotel->informations->uvh_agree==1){
			$cancellationText = JText::_('LNG_CANCELATION_UVH').' ';
		}

		if(count($hotel->types)==0){
			$type = new stdClass();
			$type->id=0;
			$hotel->types[0]=$type;
		}

		$cancellationText = (isset($hotel->types) && $hotel->types[0]->id == PARK_TYPE_ID) ? "": $cancellationText.str_replace("<<days>>", $hotel->informations->cancellation_days, JText::_('LNG_CANCELLATION_RULE')).' ';

        $cancellationCondition = $translations->getObjectTranslation(CANCELLATION_CONDITIONS, $hotel->informations->id,$languageTag);
        if(empty( $hotel->informations))
        	$hotel->informations= new stdClass();
        $hotel->informations->cancellation_conditions = !isset($cancellationCondition->content)?"":$cancellationCondition->content;
		$hotel->informations->cancellation_conditions = $cancellationText.$hotel->informations->cancellation_conditions;

        $childrenCategory = $translations->getObjectTranslation(CHILDREN_CATEGORY, $hotel->informations->id,$languageTag);
        $hotel->informations->children_category = empty($childrenCategory->content)?$hotel->informations->children_category:$childrenCategory->content;

		$informationsTable =JTable::getInstance('HotelInformations',"JTable",array());
		$hotel->paymentOptions =  $informationsTable->getHotelPaymentOptions($hotelId);

		return $hotel;
	}
	
	public static function getHotelReviewScore($hotelId){
		
		$reviewAnswersTable	= JTable::getInstance('ReviewAnswers','Table', array());
		$reviewAnswers = $reviewAnswersTable->getAverageReviewAnswersScoreByHotel($hotelId);
		
		$languageTag = JRequest::getVar('_lang');
		$translationClass = new JHotelReservationLanguageTranslations();
		$translatedReviewQuestions = $translationClass->getAllTranslationtByLanguage(REVIEW_QUESTIONS_TRANSLATION,$languageTag);
		foreach ($reviewAnswers as $reviewAnswer){
			$reviewAnswer->question = JHotelUtil::printTranslatedValues($translatedReviewQuestions,$reviewAnswer->review_question_id,$reviewAnswer->question);
		}

		return $reviewAnswers;
	
	}
	

	/**
	 * @param $latitude
	 * @param $longitude
	 * @param $pois
	 *
	 * the Hotel latitude and longtitude of the hotel to calculate the distance hotel to point of interest
	 * the result of the distances will compare with the activity radius to get the new result of the active points of interest Object list
	 * @return array
	 *
	 * @since version
	 */
	public static function getActivePOIs($latitude,$longitude){
		$pois = null;
		if(!empty($latitude) && !empty($longitude)){
			$poiTable	= JTable::getInstance('POI','JTable', array());
			$pois = $poiTable->getActivePOIs($latitude,$longitude);
		}
		
		return $pois;
	}




    public static function getSinglePOI($poid,$hotelId){
        $poiTable	= JTable::getInstance('POI','JTable', array());
        $poiPicturesTable = JTable::getInstance('PoiPictures','JTable',array());
	    $userData = UserDataService::getUserData();

	    // if poi is accessed from the module
	    $location = self::getCoordinates();

	    $hotelId = isset($hotelId)?$hotelId:$location["hotel_id"];
        $poi = $poiTable->getSinglePOI($poid,$hotelId);
        $countryTable	= JTable::getInstance('Country','JTable', array());


	    if(isset($poi)) {

		    $poi->location = isset($poi->hotel->hotel_name)?$poi->hotel->hotel_name:'';

		    if(isset($poi->hotel->hotel_name)) {
			    $poi->hotel_name = JText::_( 'LNG_DISTANCE_FROM_HOTEL' ) . "(" . $poi->hotel->hotel_name . ")";
		    }

            $poi->country_name = '';
            $poi->country  = $countryTable->getCountries();
			    if (isset( $location ) && !empty( $location ) ) {
				    $poi->hotel                  = new stdClass();
				    $poi->hotel->hotel_latitude  = (double) $location['latitude'];
				    $poi->hotel->hotel_longitude = (double) $location['longitude'];
				    $poi->hotel_name             = JText::_( 'LNG_DISTANCE_FROM' ) . "(" . $poi->location . ")";
				    $poi->location               = $poi->location;
				 }
            $poi->distance  = JHotelUtil::distance($poi->hotel->hotel_latitude,$poi->hotel->hotel_longitude  ,$poi->poi_latitude,$poi->poi_longitude);
	        $enabledPicturesOnly = 1;
	        $poi->pictures = $poiPicturesTable->getPoiPictures($poid,$enabledPicturesOnly);
            foreach ($poi->country as $country) {
                if ($poi->poi_country_id > 0 && $country->country_id == $poi->poi_country_id) {
                    $poi->country_name = $country->country_name;
                }
            }
        }
        return $poi;
    }

    public static function getHotelCommentsReviews($reviewId){
        $reviewAnswersTable	= JTable::getInstance('ReviewComments','JTable', array());
        return $reviewAnswersTable->getHotelCommentsReviews($reviewId);
    }
	
	/**
	 * Get all rooms from specified hotel. It calculates also the price for the room per day.
	 *
	 * @param $hotelId
	 * @param $startDate
	 * @param $endDate
	 * @param $roomIds
	 * @param $adults
	 * @param $children
	 * @return available room
	 */
	public static function getHotelRooms($hotelId, $startDate, $endDate, $roomIds=array(), $adults=2, $children=0, $discountCodes = null, $checkAvailability = true,$confirmationId=null){
		$adults = ($adults==0)?2:$adults;
		$db = JFactory::getDBO();
		$roomFilter ="";
        $startDate =  JHotelUtil::convertToMysqlFormat($startDate);
        $endDate = JHotelUtil::convertToMysqlFormat($endDate);
        $discountTable = JTable::getInstance('RoomDiscounts','JTable', array());
     
		if(count($roomIds)>0 && $roomIds[0]!=null){
			$roomFilter = " and r.room_id in (";
			foreach( $roomIds as $id )
			{
				$roomFilter .= $id.',';
			}
			$roomFilter = substr($roomFilter,0,-1);
			$roomFilter .= ")";
		}
		$appSettings = JHotelUtil::getApplicationSettings();
		
		$isHotelAvailable = true;
		if(!self::isHotelAvailable($hotelId, $startDate,$endDate) && $checkAvailability){
			$isHotelAvailable = false;
		}
		$languageTag = JRequest::getVar( '_lang');

		$availabilityFilter = "and r.is_available = 1";
		if(!$checkAvailability){
			$availabilityFilter="";
		}
		
		//get hotel rooms
		$query="select *, rr.id as rate_id ,h.reservation_cost_val AS reservation_cost_val, h.reservation_cost_proc AS reservation_cost_proc,hc.currency_symbol
				from #__hotelreservation_rooms r
				inner join #__hotelreservation_rooms_rates rr on r.room_id = rr.room_id
				inner join #__hotelreservation_hotels h	ON h.hotel_id = r.hotel_id
				left join #__hotelreservation_currencies hc on h.currency_id= hc.currency_id
				left join #__hotelreservation_countries hrc on h.country_id= hrc.country_id
				where 1  $availabilityFilter and
				#r.front_display=1 and
				r.hotel_id= $hotelId $roomFilter
		        order by r.ordering";
		//echo($query);
		$db->setQuery( $query );
		$rooms =  $db->loadObjectList();

		$number_days = JHotelUtil::getNumberOfDays($startDate,$endDate);
        $translationTable = new JHotelReservationLanguageTranslations();

		//get hotel rates
		$roomTranslations = $translationTable->getAllTranslationtByLanguageArray(ROOM_TRANSLATION,$languageTag);
		$roomNameTranslations = $translationTable->getAllTranslationtByLanguageArray(ROOM_NAME,$languageTag);
		
		foreach($rooms as $room){
			$query="select * from #__hotelreservation_rooms_rate_prices r
					where rate_id=$room->rate_id and '$startDate'<= date and date<='$endDate'"  ;
			$db->setQuery( $query );
			$roomRateDetails =  $db->loadObjectList();
			$room->roomRateDetails = $roomRateDetails;

            $roomTranslation = empty($roomTranslations[$room->room_id])?"":$roomTranslations[$room->room_id];
            $roomNameTranslation = empty($roomNameTranslations[$room->room_id])?"":$roomNameTranslations[$room->room_id];

            $room->room_main_description = empty($roomTranslation["content"])?$room->room_main_description:$roomTranslation["content"];
            $room->room_name = empty($roomNameTranslation["content"])?$room->room_name:$roomNameTranslation["content"];
			//calculate available number of room
			$room->nrRoomsAvailable = $room->availability;
			$room->lock_for_departure = false;
			$room->is_disabled = false;
			$daily = array();
			$totalPrice = 0;
			$currentDayNr =1; 


			if(!$isHotelAvailable){
				$room->is_disabled = true;
			}

			//check if arrival date is disabled
			if(count($roomRateDetails)){
				foreach($roomRateDetails as $roomRateDetail){
					if($roomRateDetail->date == $startDate){
						if($roomRateDetail->lock_arrival == 1){
							$room->is_disabled = true;
						}
						$room->max_days = $roomRateDetail->max_days;
						$room->min_days = $roomRateDetail->min_days;
					}

					if($roomRateDetail->date == $endDate){
						if($roomRateDetail->lock_departure == 1){
							$room->is_disabled = true;
							$room->lock_for_departure = true;
						}
					}
				}
			}

			//determine aspects for each day of booking period
			for( $d = strtotime($startDate);$d < strtotime($endDate); ){
				$dayString = date( 'Y-m-d', $d);

				//set default price from rate
				$weekDay = date("N",$d);
				$string_price = "price_".$weekDay;
				$dayPrice = $room->$string_price;
				$childPrice = $room->child_price;
				$extraPersonPrice = $room->extra_pers_price;

				//check if a custom price is set
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						if($roomRateDetail->date == $dayString){
							$dayPrice = $roomRateDetail->price;
							$extraPersonPrice = $roomRateDetail->extra_pers_price;
							$childPrice = $roomRateDetail->child_price;
						}
					}
				}
				
				//get category prices
				$childrenCategoryTotal = ChildrenCategoryService::getReservationCategoryPrices($room,$roomRateDetails,$childPrice,$hotelId,$dayString,date('Y-m-d',strtotime( $dayString.' + 1 day ')));
				if($room->price_type==1){
					$totalAdults = ($adults<=$room->base_adults)?$adults:$room->base_adults;
					//check children category prices
						if($appSettings->children_rates_type==0 && $children>0)
							$totalChildrenPrice  = $dayPrice - round(($dayPrice*$childrenCategoryTotal)/100,2); //for percentage
						else 
							$totalChildrenPrice  = $childrenCategoryTotal;//for value
					$dayPrice = $dayPrice * $totalAdults + $totalChildrenPrice;
				}
				//add extra person cost - if it is the case
				if($adults > $room->base_adults){
					$dayPrice += ($adults - $room->base_adults) *  $extraPersonPrice;
				}
				//for single use
				//if the price is per person apply single supplement , if is for room apply discount
				if($adults==1){
					if($room->price_type==1){//per person
						$dayPrice = $dayPrice + $room->single_balancing;
					}else{
						$dayPrice = $dayPrice - $room->single_balancing;
					}
				}

				//check if there is a custom price set
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						//get room availability - if rate details are set default settings are ignored
						if($roomRateDetail->date == $dayString){
							$room->nrRoomsAvailable = $roomRateDetail->availability;
						}

						//set single use price
						if($roomRateDetail->date == $dayString && $room->price_type==1 && $adults==1){
							$dayPrice = $roomRateDetail->single_use_price;
							if(!empty($totalChildrenPrice)){
								$dayPrice += $totalChildrenPrice;
							}
								
						}
					}
				}
				$totalReservationPrice =empty($totalPrice)?$dayPrice:$totalPrice;
				//apply current discounts
				// discount coupons for hotel rooms
				$date = date( 'Y-m-d', $d );
				$discounts = $discountTable->getHotelDiscountCoupons((int)$adults,$room->room_id,null,$date,(int)$number_days,(int)$currentDayNr,(float)$totalReservationPrice,false);
				if(empty($room->hasDiscounts))
					$room->hasDiscounts = count($discounts) > 0;

				$selectedDiscounts = self::getSelectedDiscounts($discountCodes,$discounts);

				$dayPriceWD = $totalReservationPrice;
				//apply percent
				$dayPrice  = round($dayPrice - $dayPrice * ($selectedDiscounts->discountPercent/100),2);
				//apply value
				$dayPrice = $dayPrice - $selectedDiscounts->discountValue;
				
				//do not allow rates lower than 0 
				$dayPrice = $dayPrice<0?0:$dayPrice;
				
				if($room->nrRoomsAvailable ==0){
					$room->is_disabled = true;
				}
				//dmp($selectedDiscounts);

				$day = array(
						'date'				 => $dayString,
						'price'				 => $dayPrice,
						'priceWD'			 => $dayPriceWD,
						'price_final'		 => $dayPrice,
						'display_price_final'=> $dayPrice,
						'discounts'			 => $selectedDiscounts->discounts,
						'nrRoomsAvailable'   => $room->nrRoomsAvailable
				);

				$totalPrice += $dayPrice;
				$currentDayNr += 1;
				$daily[$dayString]=$day;
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}


			$room->daily = $daily;

			//average price per room
			$room->room_average_price = JHotelUtil::fmt($totalPrice/$number_days,2);
			$room->pers_total_price = JHotelUtil::fmt($totalPrice/($adults+$children),2);
			$room->total_price = $totalPrice;
			
			//set pictures
			$query = "  SELECT *
						FROM #__hotelreservation_rooms_pictures
						WHERE room_id = ".$room->room_id." AND room_picture_enable = 1
						ORDER BY room_picture_id
						";
			$db->setQuery( $query );
			$room->pictures =  $db->loadObjectList();
			$room->offer_id = 0;
			$room->adults = $adults;
			$room->children = $children;
		}
		self::setRoomDisplayPrice($rooms);
		self::checkRoomAvailability($rooms,array(),$hotelId, $startDate, $endDate,$confirmationId);
		return $rooms;

	}
	
	/**
	* Get all rooms available. It calculates also the price for the room per day.
	*
	* @param $hotelId
	* @param $startDate
	* @param $endDate
	* @param $roomIds
	* @param $adults
	* @param $children
	* @return available room
	*/
	
	function roomSorter($a, $b){
		$a = $a->room_average_display_price;
		$b = $b->room_average_display_price;
	
		if ($a == $b) {
			return 0;
		}
		 
		return ($a < $b) ? -1 : 1;
	}

	public static function getHotelOffers($hotelId, $startDate, $endDate, $offersIds=array(), $adults=2, $children=0, $discountCodes = null, $checkAvailability = true,$confirmationId=null){
        $startDate = JHotelUtil::convertToMysqlFormat($startDate);
        $endDate = JHotelUtil::convertToMysqlFormat($endDate);
		$adults = ($adults==0)?2:$adults;
		$db = JFactory::getDBO();
		$offerFilter ="";
		if(count($offersIds)>0){
			$offerFilter = " and ";

			foreach( $offersIds as $id )
			{
				$values = explode("|",$id);
				$offerFilter .= "(hor.offer_id =". $values[0].' and ';
				$offerFilter .= "hor.room_id =".$values[1].' )';
			}
		}

		$isHotelAvailable = true;
		if(!self::isHotelAvailable($hotelId, $startDate,$endDate)  && $checkAvailability){
			$isHotelAvailable = false;
		}
		
		$availabilityFilter = "and	o.is_available = 1 and r.is_available = 1 ";
		if(!$checkAvailability){
			$availabilityFilter="";
		}
		$languageTag = JRequest::getVar( '_lang');
		
		
		//get hotel rooms
		$query="select r.room_id,r.room_name,r.is_available,r.max_adults ,o.* ,hrc.country_id,hc.currency_id,hc.currency_symbol,
                    ot.*, ot.id as rate_id,
					rr.availability as availability, rr.id as room_rate_id,GROUP_CONCAT(DISTINCT exc.name) as excursions,
					GROUP_CONCAT(hov.voucher) as vouchers
				from #__hotelreservation_rooms r
				inner join #__hotelreservation_rooms_rates rr 			on r.room_id = rr.room_id
				inner join #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
				inner join #__hotelreservation_offers		 			o 		ON hor.offer_id 	= o.offer_id
				inner join #__hotelreservation_offers_rates 			ot 		ON ot.offer_id	= hor.offer_id and ot.room_id = hor.room_id
				left join #__hotelreservation_offers_vouchers hov on hov.offerId = o.offer_id
                left join #__hotelreservation_offers_excursions ofex on ofex.offer_id = o.offer_id
				left join #__hotelreservation_excursions exc on ofex.excursion_id = exc.id and exc.is_available = 1 and exc.data_start <='".$startDate."'  AND exc.data_end >= '".$endDate."'
				left join #__hotelreservation_hotels h	ON h.hotel_id = r.hotel_id
				left join #__hotelreservation_currencies hc on h.currency_id= hc.currency_id
				left join #__hotelreservation_countries hrc on h.country_id= hrc.country_id
				where o.hotel_id= $hotelId $offerFilter
				$availabilityFilter
				and	IF(
					o.offer_datasf <> '0000-00-00' AND o.offer_dataef <> '0000-00-00',
					DATE(now()) BETWEEN o.offer_datasf  AND o.offer_dataef,
					IF(
						o.offer_datasf <> '0000-00-00',
						DATE(now()) >= o.offer_datasf,
						DATE(now()) <=o.offer_dataef
					)
				)
				group by hor.offer_room_id
				order by o.ordering
				";

		$db->setQuery( $query );
		$offers =  $db->loadObjectList();

		$number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;
		//get hotel rates
        $translationModel = new JHotelReservationLanguageTranslations();
		$discountTable = JTable::getInstance('RoomDiscounts','JTable', array());
		$roomTranslations = $translationModel->getAllTranslationtByLanguageArray(ROOM_NAME,$languageTag);
		
		
		if(count($offers)){
			foreach($offers as $offer){
				//get offer custom rate settings
				$query="select * from #__hotelreservation_offers_rate_prices r
					where rate_id=$offer->rate_id and '$startDate'<= date and date<='$endDate'" ;
				$db->setQuery( $query );
				$offerRateDetails =  $db->loadObjectList();
				$offer->offerRateDetails = $offerRateDetails;
				//get room custom rate settings
				$query="select * from #__hotelreservation_rooms_rate_prices r
					where rate_id=$offer->room_rate_id and '$startDate'<= date and date<='$endDate'" ;
				$db->setQuery( $query );
				$roomRateDetails =  $db->loadObjectList();

				$offerTranslations = $translationModel->getAllObjectTranslationsArray($offer->offer_id,$languageTag);

				$offerRoomName = empty($roomTranslations[$offer->room_id])?"":$roomTranslations[$offer->room_id];
                $offer->room_name = empty($offerRoomName['content'])?$offer->room_name:$offerRoomName['content'];
                $offer->offer_name = empty($offerTranslations[OFFER_NAME])?$offer->offer_name:$offerTranslations[OFFER_NAME]["content"];
                $offer->offer_content = empty($offerTranslations[OFFER_CONTENT_TRANSLATION])?$offer->offer_content:$offerTranslations[OFFER_CONTENT_TRANSLATION]["content"];
                $offer->offer_other_info = empty($offerTranslations[OFFER_INFO_TRANSLATION])?$offer->offer_other_info:$offerTranslations[OFFER_INFO_TRANSLATION]["content"];
                $offer->offer_short_description = empty($offerTranslations[OFFER_SHORT_TRANSLATION])?$offer->offer_short_description:$offerTranslations[OFFER_SHORT_TRANSLATION]["content"];
                $offer->offer_description = empty($offerTranslations[OFFER_TRANSLATION])?$offer->offer_description:$offerTranslations[OFFER_TRANSLATION]["content"];

				$offer->roomRateDetails = $roomRateDetails;

				//calculate available number of room
				$offer->nrRoomsAvailable = $offer->availability;

				$offer->is_disabled = false;
				$offer->lock_for_departure = false;
				//dmp($offer->vouchers);

				//set voucher as array
				if(isset($offer->vouchers))
					$offer->vouchers = explode(',', $offer->vouchers);
				//check if offer can start on arrival date
				$d = strtotime($startDate);
				$nr_d =  'offer_day_'.date("N", $d);
				if( $offer->{ $nr_d } == 0 ){
					$offer->is_disabled = true;
				}

				$daily = array();
				$totalPrice = 0;
				$currentDayNr =1;
				$offer_max_nights	= $offer->offer_max_nights;


				if(!$isHotelAvailable){
					$offer->is_disabled = true;
				}
				//check if arrival date is disabled on arrival date
				if(count($roomRateDetails)){
					foreach($roomRateDetails as $roomRateDetail){
						if($roomRateDetail->date == $startDate && $roomRateDetail->lock_arrival == 1){
							$offer->is_disabled = true;
						}

						if($roomRateDetail->date == $endDate){
							if($roomRateDetail->lock_departure == 1){
								$offer->is_disabled = true;
								$offer->lock_for_departure = true;
							}
						}
					}
				}
				$dayCounter = 0;
				for( $d = strtotime($startDate);$d < strtotime($endDate); ){
					$dayString = date( 'Y-m-d', $d);
					$dayCounter++;
					//set default price from rate
					$weekDay = date("N",$d);
					$string_price = "price_".$weekDay;
					$dayPrice = $offer->$string_price;
					$childPrice = $offer->child_price;


					$extraPerson = "extra_pers_price_".$weekDay;
					$extraPersonPrice = $offer->$extraPerson;

					//check if there is a custom price set
					if(count($offerRateDetails)){
						foreach($offerRateDetails as $offerRateDetail){
							if($offerRateDetail->date == $dayString){
								$dayPrice = $offerRateDetail->price;
								$extraPersonPrice = $offerRateDetail->extra_pers_price;

								$childPrice = $offerRateDetail->child_price;
								//	dmp($dayString . ": ". $dayPrice);
							}
						}
					}

					//check if we have an extra night
					$extra_night_price = "extra_night_price_".$weekDay;
					$isExtraNight = false;
					if( $offer_max_nights <= 0  ){
						$dayPrice = $offer->$extra_night_price;
						$isExtraNight = true;
					}
					
					if($offer->price_type==1){
						$totalAdults = ($adults<=$offer->base_adults)?$adults:$offer->base_adults;
						$dayPrice = $dayPrice * $totalAdults+$childPrice * $children;
					}
					//add extra person cost - if it is the case
					if($adults > $offer->base_adults){
						$dayPrice += ($adults - $offer->base_adults) *  $extraPersonPrice;
					}
						
						
					$nrDays = JHotelUtil::getNumberOfDays($startDate, $endDate);
					if( $offer->offer_min_nights > $nrDays ){
						$offer->is_disabled = true;
					}

					$offer->offer_price_type = $offer->price_type;
					$offer->offer_single_balancing = $offer->single_balancing;
					//for single use
					//if the price is per person apply single supplement , if is for room apply discount
					if($adults==1){
						if(!$isExtraNight){
							if($offer->price_type==1){
								$dayPrice = $dayPrice + $offer->single_balancing;
							}else{
								$dayPrice = $dayPrice - $offer->single_balancing;
							}
						}else if($offer->price_type_day==1){
							if($offer->price_type==1){
								$dayPrice = $dayPrice + $offer->single_balancing/$offer->offer_min_nights;
							}else{
								$dayPrice = $dayPrice - $offer->single_balancing/$offer->offer_min_nights;
							}
						}else if($offer->price_type_day==0){ 
							
							if($offer->price_type==1){
								$dayPrice = $dayPrice + $offer->single_balancing;
							}else{
								$dayPrice = $dayPrice - $offer->single_balancing;
							}
						}
					}
						
					//check if offer is available on stay period
					if(!(strtotime($offer->offer_datas) <= $d && $d<=strtotime($offer->offer_datae) )){
						$offer->is_disabled = true;
					}

					//get the minimum availability in the selected period
					if(count($roomRateDetails)){
						foreach($roomRateDetails as $roomRateDetail){
							//get room availability - if rate details are set default settings are ignored
							if($roomRateDetail->date == $dayString){
								$offer->nrRoomsAvailable = $roomRateDetail->availability;
							}
						}
					}
						
					if( $offer_max_nights > 0  ){
						if(count($offerRateDetails)){
							foreach($offerRateDetails as $offerRateDetail){
								//set single use price
								if($offerRateDetail->date == $dayString && $offer->price_type==1 && $adults==1){
									$dayPrice = $offerRateDetail->single_use_price;
								}
							}
						}
					}
					$totalReservationPrice =empty($totalPrice)?$dayPrice:$totalPrice;
					//apply current discounts
					//discount coupons for hotel offers
					$discounts = $discountTable->getHotelDiscountCoupons($adults,null,$offer->offer_id,$dayString,$number_days,$currentDayNr,$totalReservationPrice,false);
					if(empty($offer->hasDiscounts))
						$offer->hasDiscounts = count($discounts) > 0;

					// set to true only if function
					// is being used to find discounts for offers
					// default value is FALSE
					$offersDiscounts = true;
					$selectedDiscounts = self::getSelectedDiscounts($discountCodes,$discounts,$offersDiscounts,$dayCounter);
					
					if($offer->nrRoomsAvailable ==0){
						$offer->is_disabled = true;
					}
						
					// price without discount
					$dayPriceWD = $totalReservationPrice;
					//apply percent
					$dayPrice  = round($dayPrice - $dayPrice * ($selectedDiscounts->discountPercent/100),2);
						
					//apply value
					$dayPrice = $dayPrice - $selectedDiscounts->discountValue;
					
					//do not allow rates lower than 0
					$dayPrice = $dayPrice<0?0:$dayPrice;

					$day = array(
							'date'				  => $dayString,
							'price'				  => $dayPrice,
							'priceWD'			  => $dayPriceWD,
							'price_final'		  => $dayPrice,
							'display_price_final' => $dayPrice,
							'discounts' 		  => $selectedDiscounts->discounts,
							'nrRoomsAvailable'    => $offer->nrRoomsAvailable,
							'isExtraNight'		  => $isExtraNight,
			                'extra_night_price'   => $offer->$extra_night_price

					);
						
					$daily[$dayString]=$day;
					$totalPrice += $dayPrice;
					$offer_max_nights--;
					$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
					$currentDayNr++;
				}


				$number_days = (strtotime($endDate) - strtotime($startDate) ) / ( 60 * 60 * 24) ;

				//average price per offer
				$offer->offer_average_price = JHotelUtil::fmt($totalPrice/$number_days,2);
				$offer->pers_total_price = $totalPrice/($adults+$children);
				$offer->total_price = $totalPrice;
				
				if($offer->price_type_day == 1){//per offer price
					$offer->offer_average_price = $daily[$startDate]["price"];
					$offer->pers_total_price = $daily[$startDate]["price"]/($adults+$children);
					$offer->total_price = JHotelUtil::fmt($totalPrice/$number_days,2);
						
					foreach($daily as $day){
						if($day["isExtraNight"]){
							$daily[$startDate]["price"] += $day["price"];
							$daily[$startDate]["price_final"] += $day["price"];
							$daily[$startDate]["isExtraNight"] = $day["isExtraNight"]; 
							$daily[$startDate]["extra_night_price"] = $day["extra_night_price"];
							$offer->pers_total_price += $day["price"]/($adults+$children);
							$offer->offer_average_price += $day["price"];
								
						}
					}
					
				}
				
				$offer->daily = $daily;
				$offer->pers_total_price = JHotelUtil::fmt($offer->pers_total_price,2);
				
				//load offers pictures
				$query = "  SELECT *
							FROM #__hotelreservation_offers_pictures
							WHERE offer_id = ".$offer->offer_id." AND offer_picture_enable = 1
							ORDER BY offer_picture_id";
				
				$db->setQuery( $query );
				$offer->pictures =  $db->loadObjectList();
			
				$offer->adults = $adults;
				$offer->children = $children;
			}
		}
		
		self::setOfferDisplayPrice($offers);
		self::checkRoomAvailability($offers,array(),$hotelId, $startDate, $endDate,$confirmationId);
		
		return $offers;
	}

	public static function getAllOffers($startDate, $endDate, $offersIds=array(), $adults=2, $children=0,$limitstart= 0 ,$limit = 0,$lastMinuteOffers, $discountCodes = null, $checkAvailability = true,$confirmationId=null)
    {
        $startDate = JHotelUtil::convertToMysqlFormat($startDate);
        $endDate = JHotelUtil::convertToMysqlFormat($endDate);
        $adults = ($adults == 0) ? 2 : $adults;
        $db = JFactory::getDBO();
        $offerFilter = "";
        if (count($offersIds) > 0) {
            $offerFilter = " and ";

            foreach ($offersIds as $id) {
                $values = explode("|", $id);
                $offerFilter .= "(hor.offer_id =" . $values[0] . ' and ';
                $offerFilter .= "hor.room_id =" . $values[1] . ' )';
            }
        }
        $isHotelAvailable = true;
        $availabilityFilter = "and	o.is_available = 1 and r.is_available = 1 ";
        if (!$checkAvailability) {
            $availabilityFilter = "";
        }


	    $lastMinuteCondition = '';
	    if((int)$lastMinuteOffers == 1){
		    $lastMinuteCondition = " and o.last_minute = 1 ";
	    }

        $languageTag = JRequest::getVar('_lang');


        //get hotel rooms
        $query = "select r.*,o.*,hc.*,ot.*, ot.id as rate_id, group_concat( h.hotel_name,', ' ,h.hotel_city, ', ' ,c.country_name) as offer_hotel_info,
				rr.availability as availability, rr.id as room_rate_id,GROUP_CONCAT(exc.name) as excursions,
				GROUP_CONCAT(hov.voucher) as vouchers, GROUP_CONCAT(exc.id) as excursionIds
	
				from #__hotelreservation_rooms r
				inner join #__hotelreservation_rooms_rates rr 			on r.room_id = rr.room_id
				inner join #__hotelreservation_offers_rooms 			hor 	ON hor.room_id	 	= r.room_id
				inner join #__hotelreservation_offers		 			o 		ON hor.offer_id 	= o.offer_id and o.public = 1
				inner join #__hotelreservation_offers_rates 			ot 		ON ot.offer_id	= hor.offer_id and ot.room_id = hor.room_id
				left join #__hotelreservation_offers_vouchers hov on hov.offerId = o.offer_id
				left join #__hotelreservation_offers_excursions ofex on ofex.offer_id = o.offer_id
				left join #__hotelreservation_excursions exc on ofex.excursion_id = exc.id
				inner join #__hotelreservation_hotels h	ON h.hotel_id = r.hotel_id
				left join #__hotelreservation_currencies hc on h.currency_id= hc.currency_id
				left join #__hotelreservation_countries	c on c.country_id = h.country_id
						 where 1 $offerFilter
						 $availabilityFilter
						 $lastMinuteCondition
						 and o.hotel_id > 0 and	IF(
						 o.offer_datasf <> '0000-00-00'
						 AND
						 o.offer_dataef <> '0000-00-00',
						 DATE(now()) BETWEEN o.offer_datasf  AND o.offer_dataef,
						 IF(
						 o.offer_datasf <> '0000-00-00',
						 DATE(now()) >= o.offer_datasf,
						 DATE(now()) <=o.offer_dataef
						 )
						 )
						 group by hor.offer_room_id
						 order by o.hotel_id,o.ordering
						 ";
        $db->setQuery($query,$limitstart,$limit);
        $offers = $db->loadObjectList();
        
        //check if offers not empty
    	if(!empty($offers)){
        $db->setQuery($query);
        $totalOffers = $db->loadObjectList();
        $offers[0]->total= count($totalOffers);
		}
        
        $number_days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
        //get hotel rates
        $translationTable = new JHotelReservationLanguageTranslations();
	    $discountTable = JTable::getInstance('RoomDiscounts','JTable', array());

        if (count($offers)) {
            foreach ($offers as $offer) {
                $isHotelAvailable = true;
                if(isset($offer->hotel_id) && $offer->hotel_id > 0) {
                    if (!self::isHotelAvailable($offer->hotel_id, $startDate, $endDate) && $checkAvailability) {
                        $isHotelAvailable = false;
                    }
                }
                //get offer custom rate settings
                $query = "select * from #__hotelreservation_offers_rate_prices r
						 where rate_id=$offer->rate_id and '$startDate'<= date and date<='$endDate'";
                $db->setQuery($query);
                $offerRateDetails = $db->loadObjectList();

                $offer->offerRateDetails = $offerRateDetails;
                //get room custom rate settings
                $query = "select * from #__hotelreservation_rooms_rate_prices r
						 where rate_id=$offer->room_rate_id and '$startDate'<= date and date<='$endDate'";
                $db->setQuery($query);
                $roomRateDetails = $db->loadObjectList();

                $offer->roomRateDetails = $roomRateDetails;
				
                $offerRoomName = $translationTable->getObjectTranslation(ROOM_NAME,$offer->room_id,$languageTag);
                $offer->room_name = empty($offerRoomName->content)?$offer->room_name:$offerRoomName->content;

                $offerName = $translationTable->getObjectTranslation(OFFER_NAME,$offer->offer_id,$languageTag);
                $offer->offer_name = empty($offerName->content)?$offer->offer_name:$offerName->content;


                $offerTranslations = $translationTable->getObjectTranslation(OFFER_CONTENT_TRANSLATION,$offer->offer_id,$languageTag);
                $offer->offer_content = empty($offerTranslations->content)?$offer->offer_content:$offerTranslations->content;

                $offerOtherInfoTranslations = $translationTable->getObjectTranslation(OFFER_INFO_TRANSLATION,$offer->offer_id,$languageTag);
                $offer->offer_other_info = empty($offerOtherInfoTranslations->content)?$offer->offer_other_info:$offerOtherInfoTranslations->content;

                $offerShortDescInfoTranslations = $translationTable->getObjectTranslation(OFFER_SHORT_TRANSLATION,$offer->offer_id,$languageTag);
                $offer->offer_short_description = empty($offerShortDescInfoTranslations->content)?$offer->offer_short_description:$offerShortDescInfoTranslations->content;

                $offerDescInfoTranslations = $translationTable->getObjectTranslation(OFFER_TRANSLATION,$offer->offer_id,$languageTag);

                $offer->offer_description = empty($offerDescInfoTranslations->content)?$offer->offer_description:$offerDescInfoTranslations->content;
				

                //calculate available number of room
                $offer->nrRoomsAvailable = $offer->availability;

                $offer->is_disabled = false;
                $offer->lock_for_departure = false;
                $offer->overAvailability = false;
                
                if($offer->offer_max_nights<$number_days && $offer->apply_max_nights){
                	$offer->overAvailability = true;
                }

                //set voucher as array
                if (isset($offer->vouchers))
                    $offer->vouchers = explode(',', $offer->vouchers);
                    if($offer->vouchers !=null)
                        $offer->vouchers = array_unique($offer->vouchers);
                //check if offer can start on arrival date
                $d = strtotime($startDate);
                $nr_d = 'offer_day_' . date("N", $d);
                if ($offer->{$nr_d} == 0) {
                    $offer->is_disabled = true;
                }

                $daily = array();
                $totalPrice = 0;
                $offer_max_nights = $offer->offer_max_nights;

                if (!empty($offer->excursionIds)) {
                	$bookableExcArray = array();
                    $excursionsArr = explode(",", $offer->excursionIds);
                   /* foreach ($excursionsArr as $excursionId) {
                    	if(!isset($bookableExcArray[$excursionId])){
	                        $bookable = ExcursionsService::excursionsBookable($excursionId, $startDate, $endDate, $adults);
	                        $bookableExcArray[$excursionId] = $bookable; 
                    	}
                        if (!$bookableExcArray[$excursionId]) {
                            $offer->is_disabled = true;
                            break;
                        }
                    }*/
                }

                if (!$isHotelAvailable) {
                    $offer->is_disabled = true;
                }


                //check if arrival date is disabled on arrival date
                if (count($roomRateDetails)) {
                    foreach ($roomRateDetails as $roomRateDetail) {
                        if ($roomRateDetail->date == $startDate && $roomRateDetail->lock_arrival == 1) {
                            $offer->is_disabled = true;
                            //dmp("disable");
                        }
                        if ($roomRateDetail->date == $endDate) {
                            if ($roomRateDetail->lock_departure == 1) {
                                // 								dmp("disable");
                                $offer->is_disabled = true;
                                $offer->lock_for_departure = true;
                            }
                        }
                    }
                }
                $dayCounter = 0;
	            for ($d = strtotime($startDate); $d < strtotime($endDate);) {
                    $dayString = date('Y-m-d', $d);
                    $dayCounter++;
                    //set default price from rate
                    $weekDay = date("N", $d);
                    $string_price = "price_" . $weekDay;
                    $dayPrice = $offer->$string_price;
                    $childPrice = $offer->child_price;

		            $extraPerson = "extra_pers_price_".$weekDay;
		            $extraPersonPrice = $offer->$extraPerson;

		            //check if there is a custom price set
                    if (count($offerRateDetails)) {
                        foreach ($offerRateDetails as $offerRateDetail) {
                            if ($offerRateDetail->date == $dayString) {
                                $dayPrice = $offerRateDetail->price;
                                $extraPersonPrice = $offerRateDetail->extra_pers_price;
                                $childPrice = $offerRateDetail->child_price;
                                //	dmp($dayString . ": ". $dayPrice);
                            }
                        }
                    }

	                $extra_night_price = "extra_night_price_".$weekDay;
	                //check if we have an extra night
                    $isExtraNight = false;
                    if ($offer_max_nights <= 0) {
	                    $dayPrice = $offer->$extra_night_price;
                        $isExtraNight = true;
                        //dmp("extra price: ".$offer->extra_night_price);
                    }

                    if ($offer->price_type == 1) {
                        $totalAdults = ($adults <= $offer->base_adults) ? $adults : $offer->base_adults;
                        $dayPrice = $dayPrice * $totalAdults + $childPrice * $children;
                    }
                    //add extra person cost - if it is the case
                    if ($adults > $offer->base_adults) {
                        $dayPrice += ($adults - $offer->base_adults) * $extraPersonPrice;
                    }
                    $nrDays = JHotelUtil::getNumberOfDays($startDate, $endDate);
                    if ($offer->offer_min_nights > $nrDays) {
                        $offer->is_disabled = true;
                    }

                    //for single use
                    //if the price is per person apply single supplement , if is for room apply discount
                    if ($adults == 1 && $children == 0) {
                        if (!$isExtraNight) {
                            if ($offer->price_type == 1) {
                                $dayPrice = $dayPrice + $offer->single_balancing;
                            } else {
                                $dayPrice = $dayPrice - $offer->single_balancing;
                            }
                        } else if ($offer->price_type_day == 1) {
                            if ($offer->price_type == 1) {
                                $dayPrice = $dayPrice + $offer->single_balancing / $offer->offer_min_nights;
                            } else {
                                $dayPrice = $dayPrice - $offer->single_balancing / $offer->offer_min_nights;
                            }
                        } else if ($offer->price_type_day == 0) {
                            if ($offer->price_type == 1) {
                                $dayPrice = $dayPrice + $offer->single_balancing;
                            } else {
                                $dayPrice = $dayPrice - $offer->single_balancing;
                            }
                        }
                    }

                    //check if offer is available on stay period
                    if (!(strtotime($offer->offer_datas) <= $d && $d <= strtotime($offer->offer_datae))) {
                        $offer->is_disabled = true;
                    }

                    //get the minimum availability in the selected period
                    if (count($roomRateDetails)) {
                        foreach ($roomRateDetails as $roomRateDetail) {
                            //get room availability - if rate details are set default settings are ignored
                            if ($roomRateDetail->date == $dayString) {
                                $offer->nrRoomsAvailable = $roomRateDetail->availability;
                            }
                        }
                    }

                    if ($offer_max_nights > 0) {
                        if (count($offerRateDetails)) {
                            foreach ($offerRateDetails as $offerRateDetail) {
                                //set single use price
                                if ($offerRateDetail->date == $dayString && $offer->price_type == 1 && $adults == 1 && $children == 0) {
                                    $dayPrice = $offerRateDetail->single_use_price;
                                }
                            }
                        }
                    }

                    //apply current discounts
	                // discount coupons all offers
	                $discounts = $discountTable->getHotelDiscountCoupons($adults,null,$offer->offer_id,$dayString,$number_days,null,null,true);
	                $offer->hasDiscounts = count($discounts) > 0;
                    //dmp($discounts);

	                // set to true only if function
	                // is being used to find discounts for offers
	                // default value is FALSE
	                $offersDiscounts = true;
                    $selectedDiscounts =self::getSelectedDiscounts($discountCodes,$discounts,$offersDiscounts,$dayCounter);

                    if ($offer->nrRoomsAvailable == 0) {
                        $offer->is_disabled = true;
                    }

                    //apply percent
                    $dayPrice = round($dayPrice - $dayPrice * ($selectedDiscounts->discountPercent / 100), 2);
                    //apply value
                    $dayPrice = $dayPrice - $selectedDiscounts->discountValue;

                    $day = array(
                        'date' => $dayString,
                        'price' => $dayPrice,
                        'price_final' => $dayPrice,
                        'display_price_final' => $dayPrice,
                        'discounts' => $selectedDiscounts->discounts,
                        'nrRoomsAvailable' => $offer->nrRoomsAvailable,
                        'isExtraNight' => $isExtraNight,
		                'extra_night_price'=>$offer->$extra_night_price
                    );

                    $daily[$dayString] = $day;
                    $totalPrice += $dayPrice;
                    $offer_max_nights--;
                    $d = strtotime(date('Y-m-d', $d) . ' + 1 day ');
                }

                //dmp($offer->offer_name);
                //dmp($offer->nrRoomsAvailable);

                //$this->itemRoomsCapacity[$offer->room_id ] = array($offer->nrRoomsAvailable, 1);

                $number_days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);
                $offer->daily = $daily;

                //average price per offer
                $offer->offer_average_price = JHotelUtil::fmt($totalPrice / $number_days, 2);
                $offer->pers_total_price = JHotelUtil::fmt($totalPrice / ($adults + $children), 2);
                $offer->total_price = $totalPrice;

                if ($offer->price_type_day == 1) {
                    $offer->offer_average_price = $daily[$startDate]["price"];
                    $offer->pers_total_price = $daily[$startDate]["price"] / ($adults + $children);

                    foreach ($daily as $day) {
                        if ($day["isExtraNight"]) {
                            $offer->pers_total_price += $day["price"] / ($adults + $children);
                            $offer->offer_average_price += $day["price"];
                        }
                    }

                }

                //load offers pictures
                $query = "  SELECT *
							FROM #__hotelreservation_offers_pictures
							WHERE offer_id = " . $offer->offer_id . " AND offer_picture_enable = 1
							ORDER BY offer_picture_id";

                $db->setQuery($query);
                $offer->pictures = $db->loadObjectList();

                $offer->adults = $adults;
                $offer->children = $children;
            }
        }

        self::setOfferDisplayPrice($offers);
        self::checkRoomListingAvailability($offers, array(), $startDate, $endDate, $confirmationId);
		
        return $offers;
    }
	
	

	public static function checkRoomAvailability($rooms,$items_reserved, $hotel_id, $datas ,$datae,$confirmationId=null){
		//dmp($rooms);dmp($items_reserved);exit;
		$app = JFactory::getApplication();
		$rooms_reserved = BookingService::getNumberOfBookingsPerDay($hotel_id, $datas ,$datae,$confirmationId);
		$temporaryReservedRooms = BookingService::getReservedRooms($items_reserved);
		if(isset($rooms) && count($rooms)>0){
			foreach($rooms as $room){
				foreach($room->daily as $day){

					$totalNumberRoomsReserved = 0;
					if(isset($rooms_reserved[$room->room_id][$day["date"]]))
						$totalNumberRoomsReserved = $rooms_reserved[$room->room_id][$day["date"]];

					if(isset($temporaryReservedRooms[$room->room_id])){
						$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
					}
					$curentStep = JRequest::getVar("view");
					switch($curentStep){
						case 'hotel':
							if($day["nrRoomsAvailable"] <= $totalNumberRoomsReserved)//on the hotel view, no rooms booked, check if availability is greater or equal
							{
								$room->is_disabled = true;
					}
							break;
						default:
							if($day["nrRoomsAvailable"] < $totalNumberRoomsReserved)
							{
								$room->is_disabled = true;
							}
							break;
					}
					//set number of rooms left
					$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
					$appRoomsLeftNumber = $appSettings->rooms_left;

					if(isset($appRoomsLeftNumber) && $appRoomsLeftNumber > 0)
					{
						$nrRoomsLeft = $day["nrRoomsAvailable"] - $totalNumberRoomsReserved;
						if ( $appRoomsLeftNumber >= $nrRoomsLeft )
						{
							$room->nrRoomsLeft = $nrRoomsLeft;
						}
					}
				}
			}
		}
	}
	
	public static function checkAvailability($hotelId, $startDate, $endDate){
		$rooms = self::getHotelRooms($hotelId, $startDate, $endDate);
		$isAvailable = false;
		if(isset($rooms) && count($rooms)>0){
			foreach($rooms as $room){
				if(!$room->is_disabled){
					$isAvailable = true;
					break;
				}
			}
		}
		
		return $isAvailable;
	}
	

	public static function checkRoomListingAvailability(&$rooms,$items_reserved, $datas ,$datae,$confirmationId=null){
		//number of reserved rooms for each room type
		$temporaryReservedRooms = BookingService::getReservedRooms($items_reserved);
		$ingoreNrRooms = !empty($confirmationId)?1:0;
		if(isset($rooms) && count($rooms)>0){
			foreach($rooms as $room){
				$rooms_reserved = BookingService::getNumberOfBookingsPerDay($room->hotel_id, $datas ,$datae,$confirmationId);
				foreach($room->daily as $day){
	
					$totalNumberRoomsReserved = 0;
					if(isset($rooms_reserved[$room->room_id][$day["date"]]))
						$totalNumberRoomsReserved = $rooms_reserved[$room->room_id][$day["date"]];
	
					if(isset($temporaryReservedRooms[$room->room_id])){
						$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
					}
	
					if($day["nrRoomsAvailable"] <= ($totalNumberRoomsReserved - $ingoreNrRooms ))
					{
						$room->is_disabled = true;
					}
				}
			}
		}
	}
	
	
	static function setRoomDisplayPrice(&$rooms){
		$app = JFactory::getApplication();
		//curency converter not present in admin
		if($app->isAdmin())
			return $rooms;
		
		if(!empty($rooms)){
			$hotel = JHotelUtil::getHotel($rooms[0]->hotel_id); 
			$userData = UserDataService::getUserData();
			if(empty($userData->currency) || empty($userData->currency->name)){
				$userData->currency =self::getHotelCurrency($hotel);
			}
		}
		foreach( $rooms as &$room ){
			$room->room_average_display_price = CurrencyService::convertCurrency($room->room_average_price, $hotel->hotel_currency,$userData->currency->name);
			$room->pers_total_price = CurrencyService::convertCurrency($room->pers_total_price, $hotel->hotel_currency,$userData->currency->name);
			$room->total_price= CurrencyService::convertCurrency($room->total_price, $hotel->hotel_currency,$userData->currency->name);
				
			foreach( $room->daily as &$daily )
			{
				$daily['display_price_final'] = CurrencyService::convertCurrency($daily['price_final'],$hotel->hotel_currency,$userData->currency->name);
				$daily['price'] = CurrencyService::convertCurrency($daily['price'],$hotel->hotel_currency,$userData->currency->name);
				
			}
		}
	}
	
	static function setOfferDisplayPrice(&$offers){
		$app = JFactory::getApplication();
		
		if($app->isAdmin())
			return $offers;
		
		if(!empty($offers)){
			$hotel = JHotelUtil::getHotel($offers[0]->hotel_id);
			$userData = UserDataService::getUserData();
			if(empty($userData->currency) || empty($userData->currency->name)){
				$userData->currency = new stdClass();
				$userData->currency->name ="";
			}
		}
		foreach( $offers as &$offer ){				
			$offer->offer_average_display_price = CurrencyService::convertCurrency($offer->offer_average_price,$hotel->hotel_currency,$userData->currency->name);
			$offer->pers_total_price = CurrencyService::convertCurrency($offer->pers_total_price,$hotel->hotel_currency,$userData->currency->name);
			foreach( $offer->daily as &$daily )
			{
				$daily['display_price_final'] = CurrencyService::convertCurrency($daily['price_final'],$hotel->hotel_currency,$userData->currency->name);
				$daily['price'] = CurrencyService::convertCurrency($daily['price'],$hotel->hotel_currency,$userData->currency->name);
				
			}
		}
	}
	
	static function getRoomsCalendar($rooms, $nrOfDays, $adults,$children,$month, $year, $bookings,$temporaryReservedRooms, $hotelAvailability){
		$roomsCalendar = array();
		$appSettings = JHotelUtil::getApplicationSettings();
		$endDay =  date('t', mktime(0, 0, 0,$month, 1, $year));
		foreach ($rooms as $room){
			$roomsInfo = array();
			$index = 1;
				
			$roomRateDetails = $room->roomRateDetails;
			$nrDays = 0;
			foreach($room->daily as &$daily )
			{
				$price = $daily["price"];
				$available = true;
				$totalPrice =0;
				$nrDays = $nrOfDays;
				$lockArrival= false;
				$lockDeparture = false;
	
				if($index<=$endDay){
					$startDate = date('Y-m-d', mktime(0, 0, 0, $month, $index, $year));
						
					if(count($roomRateDetails)){
						foreach($roomRateDetails as $roomRateDetail){
							if($roomRateDetail->date == $startDate){
								if($roomRateDetail->lock_arrival == 1){
									$available = false;
									$lockArrival = true;
								}
								if($roomRateDetail->lock_departure == 1){
									$available = false;
									$lockDeparture = true;
								}
								$room->max_days = $roomRateDetail->max_days;
								$room->min_days = $roomRateDetail->min_days;
							}
						}
					}
						
					$nrDays = $nrDays<$room->min_days ? $room->min_days: $nrDays;
						
					$endDate = date('Y-m-d', mktime(0, 0, 0, $month, $index+$nrDays, $year));
						
					$room->nrRoomsAvailable = $room->availability;
					$room->bookings = 0;
					
					if($appSettings->calendar_availability_type==1)
						$nrDays = 1;
						
					for( $i = $index; $i<($index+$nrDays);$i++ )
					{
						$day= date('Y-m-d', mktime(0, 0, 0, $month, $i, $year));
	
						//check if hotel is available
						if(!$hotelAvailability[$day]){
							$available = false;
						}
	
						foreach($roomRateDetails as $roomRateDetail){
							if($roomRateDetail->date == $day){
								$room->nrRoomsAvailable = $roomRateDetail->availability;
							}
						}
	
						$totalNumberRoomsReserved = 0;
	
						if(isset($bookings[$room->room_id][$day])){
							$totalNumberRoomsReserved = $bookings[$room->room_id][$day];
						}
						if(isset($temporaryReservedRooms[$room->room_id]) && (strtotime($temporaryReservedRooms["datas"])<= strtotime($day) &&  strtotime($day)<strtotime($temporaryReservedRooms["datae"]) )){
							$totalNumberRoomsReserved += $temporaryReservedRooms[$room->room_id];
						}
	
						//calculate maximum number of bookings per stay interval
						if($room->nrRoomsAvailable<=$totalNumberRoomsReserved){
							$available = false;
						}
	
						if(isset($room->daily[$i-1])){
							$price 	= $room->daily[$i-1]['price'];
						}
	
						$totalPrice += $price;
					}
						
				}
				$room->room_average_price = round($totalPrice/$nrDays,2);
				$room->pers_total_price = round($totalPrice/($adults+$children),2);
				if(JRequest::getVar( 'show_price_per_person')==1){
					$price = $room->pers_total_price;
				}else{
					$price = $room->room_average_price;
				}
	
				$roomsInfo[] = array("price" => JHotelUtil::fmt($price,2), "isAvailable" => $available, "lockArrival" => $lockArrival, "lockDeparture" => $lockDeparture);
				$id= $room->room_id;
				$index++;
			}
			$hotelId = $room->hotel_id;
			$roomsCalendar[$id]= JHotelUtil::getAvailabilityCalendar($hotelId, $month, $year, $roomsInfo, $nrDays, $id);
		}
	
		return $roomsCalendar;
	}
	
	static function getOffersCalendar($offers, $initialNrDays, $adults,$children ,  $month, $year, $bookings, $temporaryReservedRooms, $hotelAvailability){
		$offersCalendar = array();
		$endDay =  date('t', mktime(0, 0, 0,$month, 1, $year));
		if($adults==" " || $adults=="")
		$adults=2;
		$userData = UserDataService::getUserData();
		
		if(count($offers)){
			foreach($offers as $offer){
	
				$roomsInfo = array();
				$index = 1;
	
				$daily = array();
	
				$nrDays = $initialNrDays;
				if($nrDays<$offer->offer_min_nights){
					$nrDays = $offer->offer_min_nights;
				}
				$offerRateDetails = $offer->offerRateDetails;
				$roomRateDetails = $offer->roomRateDetails;
				$firstDayPrice = 0;	
				
				
				foreach($offer->daily as &$daily ){
					$available = true;
					$totalPrice = 0;
					$offer_max_nights	= $offer->offer_max_nights;
					$extraNightPrice = 0;
					$lockArrival= false;
					$lockDeparture = false;
						
					if($index<=$endDay){
						$startDate = date('Y-m-d', mktime(0, 0, 0, $month, $index, $year));
						$endDate = date('Y-m-d', mktime(0, 0, 0, $month, $index+$nrDays, $year));
	
						$d = strtotime($startDate);
						$nr_d =  'offer_day_'.date("N", $d);
						if( $offer->{ $nr_d } == 0 ){
							$available = false;
						}
	
						//check if arrival date is disabled
						if(count($roomRateDetails)){
							foreach($roomRateDetails as $roomRateDetail){
								if($roomRateDetail->date == $startDate){
									
									if($roomRateDetail->lock_arrival == 1){
										$lockArrival = true;
										$available = false;
									}
									if($roomRateDetail->lock_departure == 1){
										$lockDeparture = true;
										$available = false;
									}
								}
							}
						}
						/*if(!empty($offer->excursionIds)){
							$excursionsArr = explode(",",$offer->excursionIds);
							foreach($excursionsArr as $excursionId){
								$bookable = ExcursionsService::excursionsBookable($excursionId,$startDate,$endDate,$adults);
								if(!$bookable){
									$available = false;
									break;
								}
							}
						}*/
						$offer->nrRoomsAvailable = $offer->availability;
						$offer->bookings = 0;

						for( $d = strtotime($startDate);$d < strtotime($endDate); ){
							$dayString = date( 'Y-m-d', $d);
								
							//set default price from rate
							$weekDay = date("N",$d);
							$string_price = "price_".$weekDay;
							$dayPrice = $offer->$string_price;
							$childPrice = $offer->child_price;

							$extraPerson = "extra_pers_price_".$weekDay;
							$extraPersonPrice = $offer->$extraPerson;
								
							//check if there is a custom price set
							if(count($offerRateDetails)){
								foreach($offerRateDetails as $offerRateDetail){
									if($offerRateDetail->date == $dayString){
										$dayPrice = $offerRateDetail->price;
										$extraPersonPrice = $offerRateDetail->extra_pers_price;
										$childPrice = $offerRateDetail->child_price;
										
									}
								}
							}
							
							$isExtraNight = false;	
							//check if we have an extra night
							if( $offer_max_nights <= 0  ){
								$extra_night_price = "extra_night_price_".$weekDay;
								$dayPrice = $offer->$extra_night_price;
								$isExtraNight = true;
							}
								
							if($offer->price_type==1){
								$totalAdults = ($adults<=$offer->base_adults)?$adults:$offer->base_adults;
								$dayPrice = $dayPrice * $totalAdults+$childPrice * $children;
							}
								
							//add extra person cost - if it is the case
							if($adults > $offer->base_adults){
								$dayPrice += ($adults - $offer->base_adults) *  $extraPersonPrice;
							}
								
								
							//for single use
							//if the price is per person apply single supplement , if is for room apply discount
							if($adults==1){
								if(!$isExtraNight){
									if($offer->price_type==1){
										$dayPrice = $dayPrice + $offer->single_balancing;
										//dmp("add balancing: ".$offer->single_balancing." -> ".$dayPrice);
									}else{
										$dayPrice = $dayPrice - $offer->single_balancing;
									}
								}else if($offer->price_type_day==1){
									if($offer->price_type==1){
										$dayPrice = $dayPrice + $offer->single_balancing/$offer->offer_min_nights;
									}else{
										$dayPrice = $dayPrice - $offer->single_balancing/$offer->offer_min_nights;
									}
								}
							}
								
							//check if offer is available on stay period
							if(!(strtotime($offer->offer_datas) <= $d && $d<=strtotime($offer->offer_datae) )){
								$available = false;
							}
								
							//get the minimum availability in the selected period
							if(count($roomRateDetails)){
								foreach($roomRateDetails as $roomRateDetail){
									//get room availability - if rate details are set default settings are ignored
									if($roomRateDetail->date == $dayString){
										$offer->nrRoomsAvailable = $roomRateDetail->availability;
									}
								}
							}
								
								
							$totalNumberRoomsReserved = 0;
								
							if(isset($bookings[$offer->room_id][$dayString])){
								$totalNumberRoomsReserved = $bookings[$offer->room_id][$dayString];
							}
							if(isset($temporaryReservedRooms[$offer->room_id]) && (strtotime($temporaryReservedRooms["datas"])<= $d &&  $d<strtotime($temporaryReservedRooms["datae"]) )){
								$totalNumberRoomsReserved += $temporaryReservedRooms[$offer->room_id];
							}
								
							//calculate maximum number of bookings per stay interval
							if($offer->nrRoomsAvailable <= $totalNumberRoomsReserved ){
								$available = false;
							}
								
							if( $offer_max_nights > 0  ){
								if(count($offerRateDetails)){
									foreach($offerRateDetails as $offerRateDetail){
										//set single use price
										if($offerRateDetail->date == $dayString && $adults==1){
											$dayPrice = $offerRateDetail->single_use_price;
										}
									}
								}
							}
	
							//check if hotel is available
							if(isset($hotelAvailability) && !$hotelAvailability[$dayString]){
								$available = false;
							}

							if(strtotime($startDate)==$d){
								$firstDayPrice = $dayPrice;
							}
							
							if($isExtraNight){
								$extraNightPrice += $dayPrice;
							}
							
							$totalPrice += $dayPrice;
							$offer_max_nights--;
							$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						}
					}
						
					//average price per offer
					$offer->offer_average_price = round($totalPrice/$nrDays,2);
					$offer->pers_total_price = round($totalPrice/($adults+$children),2);
						
					$price = $offer->offer_average_price;
					if(JRequest::getVar( 'show_price_per_person')==1){
						$price = $offer->pers_total_price;
					}
					
					if($offer->price_type_day == 1){
						$price = $firstDayPrice;
						if($offer->price_type==1){
							$price = $firstDayPrice/($adults+$children);
							$price += $extraNightPrice/($adults+$children);
						}
					}
					$hotel = JHotelUtil::getHotel($offer->hotel_id);
					$price = CurrencyService::convertCurrency($price,$hotel->hotel_currency,$userData->currency->name);
					$roomsInfo[] = array("price" => JHotelUtil::fmt($price,2), "isAvailable" => $available, "lockArrival" => $lockArrival, "lockDeparture" => $lockDeparture);
					$index++;
				}
				$id= $offer->offer_id.$offer->room_id;
				$hotelId = $offer->hotel_id;
				$offersCalendar[$id]= JHotelUtil::getAvailabilityCalendar($hotelId, $month, $year, $roomsInfo, $nrDays, $id);
			}
		}
	
		
		
		return $offersCalendar;
	}
	
	public static function getCredential($user){
		$db = JFactory::getDBO();
		$query = "  SELECT	* 	FROM #__hotelreservation_hotel_channel_manager where user='$user'";
		$db->setQuery( $query );
		$result = $db->loadObject();
		return $result;
	}
	
	public static function getHotelCurrency($hotel){
		$currency = new stdClass();
		if(isset($hotel->hotel_currency)){
			$currency->name = $hotel->hotel_currency;
			$currency->symbol = empty($hotel->currency_symbol)?"":$hotel->currency_symbol;
		}
		else{ 
			$currency->name = "";
			$currency->symbol = "";
		}
		
		return $currency;
	}

    /**
     * @param $roomId
     * @param $offers
     * @return array
     */
    public static function getRoomOffers($roomId,$offers,$startDate,$endDate,$searchOfferVoucher)
    {
            $result = array();
            $translationTable = new JHotelReservationLanguageTranslations();
            $languageTag = JRequest::getVar('_lang');
            foreach ($offers as $k=>$offer) {
                    if($offer->room_id == $roomId) {
                        $airport  = AirportTransferService::getAirportNamesByOffer($offer->offer_id);
                        $extraOptions = ExtraOptionsService::getOfferExtraOptions($offer->offer_id,$startDate,$endDate);
                        if(!empty($airport)){
                            $airport_transfer_type = $translationTable->getObjectTranslation(AIRPORT_TRANSFER_TRANSLATION_NAME,$offer->airport_transfer_type_id,$languageTag);
                            $airport->airport_transfer_type_name = !empty($airport->airport_transfer_type_name)?$airport->airport_transfer_type_name:'';

                            $offer->airport = empty($airport_transfer_type->content)?$airport->airport_transfer_type_name:$airport_transfer_type->content;
                        }
                        $offer->extraOption = $extraOptions;

                        //check if offer do not have a voucher and it is not searched with a voucher code
                        // the result will be only offers without a voucher code when searching without one
                        if(empty($searchOfferVoucher) && $offer->public){
                            array_push($result,$offer);

                            //if the user searches with a voucher code and there are offers with a voucher code
                            // the result will be populated with that result and not with offers without a voucher code
                        }elseif (!empty($searchOfferVoucher) && $offer->vouchers!=null) {
                            if (isset($offer->vouchers) && count($offer->vouchers) > 0) {
                                $offer->vouchers = array_unique($offer->vouchers);
                                foreach ($offer->vouchers as $voucher) {
                                    if (strcasecmp($voucher, $searchOfferVoucher) == 0) {
                                        array_push($result,$offer);
                                    }
                                }
                            }
                        }

                    }
                }
        return $result;
    }

    /**
     * @param $filterCategories
     * @return string
     */
    public static function getHotelTypes($filterCategory,$total)
    {
        $types = 0;
        $itemNames = "";
        $comma = "";
        $text = "";
        $itemFound= 0;
        $found = false;

            if (isset($filterCategory['name']) && isset($filterCategory['items'])) {
                foreach ($filterCategory['items'] as $item) {
                    if (isset($item->count) && (isset($item->selected) && $item->selected == 1) ) {
                        $itemFound++;
                        $found = true;
                        //Translate the name of the type
                        $translationValue = JText::_('LNG_'.strtoupper(str_replace(" ", "_", $item->name)));
                        if ($item->name == $translationValue) {
                            $itemName = $translationValue;
                        } else {
                            $itemName = $translationValue;
                        }

                        $types += $item->count;
                        $itemNames .= $comma . $itemName;
                        $comma = $itemFound >= 1 ? ", " : "";
                    }
                }
            }
        if($found){
            $text .= $types > 1 ? JText::_('LNG_PROPERTIES', true) . "(" . $itemNames . ")" . JText::_('LNG_FOUND') : JText::_('LNG_PROPERTY', true) . "(" . $itemNames . ")" . JText::_('LNG_FOUND');
            return $types . " " . $text;
        }
        return $total." ".JText::_('LNG_HOTELS_FOUND', true);
    }

	/**
	 * @param      $discountCodes
	 * @param      $discounts
	 * @param bool $offerDiscount
	 * @param null $dayCounter
	 *
	 * $discountCodes concatenated with comma ',' each code typed by the user
	 * $discounts defined in the backend
	 * $offerDiscount is true when used only inside getHotelOffers() and getAllOffers()
	 * $dayCounter the value for this parameter is present only when used inside getHotelOffers() and getAllOffers()
	 *
	 * @return mixed|stdClass
	 * returns the discount stdClass structure with the sum of the discounted value or percentage value
	 * the boolean value foundDiscountCode true|false and the discounts array containing all the discounts object
	 * that are found by discount code
	 * @throws Exception
	 */
	public static function getSelectedDiscounts($discountCodes,$discounts,$offerDiscount = false,$dayCounter = null){

		// using stdClass for the discount helper structure instead of an array
		// default value to 0
		$selectedDiscounts = new stdClass();
		$selectedDiscounts->discountValue = 0;
		$selectedDiscounts->discountPercent = 0;
		$selectedDiscounts->discounts = array();

		if(!empty($discountCodes))
		{
			$discountCodes = explode( ',', $discountCodes );
			foreach ( $discountCodes as $discountCode )
			{
				if ( count( $discounts ) > 0 )
				{
					foreach ( $discounts as $discount )
					{
						// find discount for hotel offers and AllOffers
						// finding discounts for offers requires $offerDiscount to true and the $daycounter parameter
						if($offerDiscount)
						{
							if ( $dayCounter <= $discount->maximum_number_days )
							{
								$selectedDiscounts = self::findDiscount( $selectedDiscounts, $discount, $discountCode);
							}
						}
						//find discount for hotel rooms
						else {
							$selectedDiscounts = self::findDiscount($selectedDiscounts, $discount, $discountCode);
						}
					}
				}
			}

		}else{
			//if discounts are defined and ready to be applied without discount codes
			foreach ( $discounts as $discount )
			{
				if(empty($discount->code))
					$selectedDiscounts->discounts[$discount->discount_id] = $discount;
			}
		}
		
		if(!empty($selectedDiscounts->discounts))
		{
			// do calculations for the discounted price only for the selected or founded discounts
			foreach ( $selectedDiscounts->discounts as $discount )
			{
				if ( isset( $discount->percent ) && $discount->percent )
				{
					$selectedDiscounts->discountPercent += $discount->discount_value;
				}
				else
				{
					$selectedDiscounts->discountValue += $discount->discount_value;
				}
			}
		}

		return $selectedDiscounts;
	}

	/**
	 * @param $selectedDiscounts
	 * @param $discount
	 * @param $discountCode
	 *
	 * $selectedDiscounts is passed here to store the value for $selectedDiscounts->discountPercent|discountValue ,
	 * $selectedDiscounts->foundDiscountCode and the $selectedDiscounts->discounts in case the conditions are meet
	 * @return mixed $selectedDiscounts stdClass helper structure to store discounts values
	 */
	public static function findDiscount($selectedDiscounts,$discount,$discountCode){
		$match = false;

		if ( isset( $discount->code ) && isset( $discountCode ) )
		{
			if ( $discount->check_full_code == 1 )
			{
				$match = strcasecmp( $discountCode, $discount->code ) == 0;
			}
			else
			{
				$match = stripos( $discountCode, $discount->code ) === 0;
			}
		}

		if ( $match || !isset( $discount->code ) || strlen( $discount->code ) == 0 )
		{
			$selectedDiscounts->discounts[$discount->discount_id] = $discount;
		}


		return $selectedDiscounts;
	}
	
	public static function generateHotelMetaData($hotel){
		$hotelUrl = JURI::current();
		$document = JFactory::getDocument();
		
		$title = JText::_('LNG_METAINFO_HOTEL_TITLE');
		$description = JText::_('LNG_METAINFO_HOTEL_DESCRIPTION');
		$keywords = JText::_('LNG_METAINFO_HOTEL_KEYWORDS');
		
		$title =  str_replace("<<hotel>>", $hotel->hotel_name, $title);
		$title =  str_replace("<<city>>", $hotel->hotel_city, $title);
		$title =  str_replace("<<province>>", $hotel->hotel_county, $title);
		if(isset($hotel->types[0]->name))
			$title =  str_replace("<<type>>", $hotel->types[0]->name, $title);
		
		$description =  str_replace("<<hotel>>", $hotel->hotel_name, $description);
		$description =  str_replace("<<city>>", $hotel->hotel_city, $description);
		$description =  str_replace("<<province>>", $hotel->hotel_county, $description);
		$description =  str_replace("<<hotel-stars>>", $hotel->hotel_stars, $description);
		
		$keywords =  str_replace("<<hotel>>", $hotel->hotel_name, $keywords);
		$keywords =  str_replace("<<city>>", $hotel->hotel_city, $keywords);
		$keywords =  str_replace("<<province>>", $hotel->hotel_county, $keywords);
		
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		
		$document->setTitle($title);
		$document->setDescription($description);
		$document->setMetaData('keywords', $keywords);
		$document->addCustomTag('<meta property="og:title" content="'.$title.'"/>');
		$document->addCustomTag('<meta property="og:description" content="'.$description.'"/>');
		$document->addCustomTag('<meta property="og:image" content="'.(isset($hotel->pictures[0])?JURI::root().PATH_PICTURES.$hotel->pictures[0]->hotel_picture_path:'').'"/>');
		$document->addCustomTag('<meta property="og:type" content="website"/>');
		$document->addCustomTag('<meta property="og:url" content="'.$hotelUrl.'"/>');
		$document->addCustomTag('<meta property="og:site_name" content="'.$hotelUrl.'"/>');
	}


	public static function getHotelParkingInfoStatus($hotel,$currency,$forEmail=false) {

		$parkingTax = JText::_("LNG_PARKING_NOT_AVAILABLE",true);
		
		if(!empty($hotel->informations->parking) && (int)$hotel->informations->parking == 1 && empty($hotel->informations->price_parking) || (isset($hotel->informations->price_parking) &&(int)$hotel->informations->price_parking == 0 )) {
			$parkingTax = JText::_("LNG_PARKING_FREE",true);
		}

		if(!empty($hotel->informations->parking) && (int)$hotel->informations->parking == 1 && !empty($hotel->informations->price_parking) && (int)$hotel->informations->price_parking > 0 ) {

			$parkingLabel = JText::_("LNG_PARKING_PRICE",true);
			$parkingPrice =$currency." ".JHotelUtil::fmt( $hotel->informations->price_parking, 2 )." ".JText::_("LNG_PER_DAY",true);

			$parkingTax = $parkingLabel.$parkingPrice;
		}

		if(isset($hotel->informations->parking) && (int)$hotel->informations->parking == 0) {
			$parkingTax = JText::_("LNG_PARKING_NOT_AVAILABLE",true);
		}

		ob_start();
		?>
		<?php if($forEmail){
			echo "<p>".$parkingTax.".</p>";
		} else {?>
		<tr class='rsv_dtls_subtotal' bgcolor="#FEFEFE">
			<td colspan=1 align="left">
				<?php echo $parkingTax; ?>.
			</td>
			<td align=right style="padding: 3px 9px;">
			</td>
		</tr>
		<?php
		}
		$buff = ob_get_contents();
		ob_end_clean();

		return $buff;
	}

	/**
	 * @return array|null
	 */
	static function getCoordinates(){

		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:UserDataService::getUserData();
		$userData->keyword = isset($userData->keyword)?$userData->keyword:'';

		$location = JHotelUtil::getInstance()->getCoordinates($userData->keyword);

		$hotelId = JRequest::getVar("hotelId");

		if(isset($hotelId) && !empty($hotelId) && $hotelId != "" || (isset( $userData->keyword ) && $userData->keyword != '' && !isset($location["latitude"]))) {
			$db = JFactory::getDBO();
			//$query1     = $db->getQuery( true );

			$hotel = '';
			if(isset($hotelId) && !empty($hotelId) && $hotelId != "") {
				$hotel = 'h.hotel_id=' . $hotelId;
			}

			$search = '';

			if(isset($userData->keyword) && $userData->keyword != '' && empty($hotelId) ) {
				$search = " (h.hotel_name like '%$userData->keyword%') or (h.hotel_city like '%$userData->keyword%') or (h.hotel_county like '%$userData->keyword%') ";
			}
			$condition = '';
			if ( strlen( $hotel ) > 0 && strlen( $search ) > 0 ) {
				$condition = ' or ';
			}

			if ( isset( $hotelId ) && ! empty( $hotelId ) && $hotelId != "" || isset( $userData->keyword ) && $userData->keyword != '' ) {
				//select the hotel
				$query1 = " SELECT h.hotel_id,h.hotel_name, h.hotel_city, h.hotel_county, h.hotel_latitude, h.hotel_longitude
 								FROM #__hotelreservation_hotels h
							    where $hotel $condition $search ";
				$db->setQuery( $query1 );
				$result = $db->loadObject();
				$location = array();
				$location["latitude"]   = $result->hotel_latitude;
				$location["longitude"] =  $result->hotel_longitude;
				$location["hotel_id"]   = $result->hotel_id;
			}
		}
		return $location;
	}
	
	static function getHotelsBeds24(){
		
		$hotelTable	= 	JTable::getInstance('hotels','Table', array());
		$hotels = $hotelTable->getHotelsBeds24(); 
		
		return $hotels;
	}
		
}

?>