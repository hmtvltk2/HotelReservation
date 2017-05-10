<?php

require_once JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/logger.php';

class UserDataService{

    /**
     * Get user data object created from session data
     * @return mixed|null|stdClass
     */
	public static function getUserData(){
		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:null;
		if(!isset($userData)){
			$userData = self::initializeUserData();
			$_SESSION['userData'] = $userData;
		}
		if(empty($userData->hotelId)){
			$userData->hotelId = 0;
			$_SESSION['userData'] = $userData;
		}
	
		return $userData;
	}

    /**
     * @param $hotelId
     * @param $reservedItem data of reserved items so far
     * @param $current identification for rooms that have the same current are considered as 1
     * @return string returns reserved Items by user on reservation steps
     */
	public static function reserveRoom($hotelId, $reservedItem, $current){
	
 		$log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
 		$log->LogDebug("reserveRoom hotelId= $hotelId, reservedItem = $reservedItem, current = $current");
		
		$userData =  $_SESSION['userData'];

		//remove all rooms that have same current
		$result = array();
		foreach($userData->reservedItems as $rsvItem){
			$values = explode("|",$rsvItem);
			if( $values[2]!= $current){
				$result[] = $rsvItem;
			}
		}
		$userData->reservedItems = $result;
		
		//add new room
		$reservedItem = $reservedItem."|".$current;
		$userData->reservedItems[] = $reservedItem;
		$userData->hotelId = $hotelId;
		$log->LogDebug("Reserved items: ".serialize($userData->reservedItems));
		
		$_SESSION['userData'] = $userData;
		return $reservedItem;
	}

    /**
     * @param $hotelId
     * @param $reservedItems
     */
	public static function updateRooms($hotelId, $reservedItems){
		$userData = $_SESSION['userData'];
		$reservedItems = explode('||',$reservedItems);
		$userData->reservedItems = $reservedItems;
		if(!empty($hotelId)){
			$userData->hotelId = $hotelId;
		}
		$_SESSION["userData"] = $userData;
	}

    /**
     * @param $adults
     */
	public static function updateGuests($totalAdults,$roomGuests){
		$userData = $_SESSION['userData'];
		
		$userData->adults = $totalAdults;
		$userData->total_adults = $totalAdults;
		$userData->roomGuests= $roomGuests;
		
		$_SESSION["userData"] = $userData;
	}

    /**
     *
     */
	public static function removeLastRoom(){
		$userData =  $_SESSION['userData'];
		$reservedItems = $userData->reservedItems;
		if(isset($reservedItems[count($reservedItems)-1])) {
			unset($reservedItems[count($reservedItems)-1]);
		}
		$current  = count($reservedItems)+1;
		$userData->reservedItems = $reservedItems;
		$extraOptions = $userData->extraOptionIds;
		$result = array();
		foreach($extraOptions as $extraOption){
			$values = explode("|",$extraOption);
			if( $values[2]!= $current){
				$result[] = $extraOption;
			}

		}
		$userData->extraOptionIds = $result;

		$_SESSION['userData'] = $userData;
	}


	public static function removeLastExtraOptions(){
		$userData =  $_SESSION['userData'];
		$reservedItems = $userData->reservedItems;
		$current  = count($reservedItems);
		
		$extraOptions = $userData->extraOptionIds;
		$result = array();
		foreach($extraOptions as $extraOption){
			$values = explode("|",$extraOption);
			if( $values[2]!= $current){
				$result[] = $extraOption;
			}
	
		}
		$userData->extraOptionIds = $result;
		$_SESSION['userData'] = $userData;
	}

    /**
     *
     */
    public static function removeLastRoomFromPaymentOptions(){
        $userData =  $_SESSION['userData'];
        
        $reservedItems = $userData->reservedItems;
        if(isset($reservedItems[count($reservedItems)-1])) {
            unset($reservedItems[count($reservedItems)-1]);
        }
        $userData->reservedItems = $reservedItems;

        $_SESSION['userData'] = $userData;
    }


    /**
     * @param $hotelId
     * @param $reservedItem
     */
	public static function reserveOffer($hotelId, $reservedItem){
		$userData =  $_SESSION['userData'];
		
		$reservedItem = $reservedItem."|".(count($userData->reservedItems)+1);
		$userData->reservedItems[] = $reservedItem;
		$userData->hotelId = $hotelId;
	
		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $extraOptionsIds
     */
	public static function addExtraOptions($extraOptionsIds,$current){
		$userData =  $_SESSION['userData'];
				
		//remove current extras
		foreach($userData->extraOptionIds as $key=>$value){
			$extraOption = explode("|",$value);
			if($extraOption[2]==$current)
				unset($userData->extraOptionIds[$key]);
			
		}
	
		if(!empty($extraOptionsIds)){ //add extra options to session data
			$userData->extraOptionIds = array_merge($userData->extraOptionIds,$extraOptionsIds);
		}
		
		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $airportTrasnferType
     */
	public static function addAirportTrasnferTypes($data){
		$userData = $_SESSION['userData'];
		$current = $data["current"];
		
		//remove current extras
		foreach($userData->airportTransfers as $key=>$value){
			$transfer = explode("|",$value);
			if(intVal($transfer[4])==$current)
				unset($userData->airportTransfers[$key]);
		}
		
		if(!empty($data["airport_transfer_type_id"])){//add airport transfer id any
			$airportObj = new stdClass();
			$airportTransferTmp = array();
	        $airportObj->airport_transfer_type_id = $data["airport_transfer_type_id"];
	        $airportObj->room_id = $data['room_id'];
	        $airportObj->airline_id            = -1;
	        $airportObj->airline_name          = ucfirst($data["airport_airline"]);
	        $airportObj->current = $current;
	        $airportObj->airport_transfer_flight_nr = strtoupper($data["airport_transfer_flight_nr"]);
	        $airportObj->airport_transfer_date = $data["airport_transfer_date"];
	        $airportObj->airport_transfer_time_hour            = $data["hour"];
	        $airportObj->airport_transfer_time_min          = $data["minute"];
	        $airportObj->airport_transfer_guest     = $data["airport_transfer_guest"];
	        $airportObj->included_offer     = $data["included_offer"];
	        $airportTransferArr = get_object_vars($airportObj);
	        $airportTransferTmp[] = implode("|",$airportTransferArr);
	        if(empty($userData->airportTransfers))
	        	$userData->airportTransfers = array();
	        $userData->airportTransfers= array_merge($userData->airportTransfers, $airportTransferTmp);
		}
		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $excursions
     */
	public static function addExcursions($excursions){
		$userData =  $_SESSION['userData'];

        $userData->excursions = $excursions;
		if($userData->hotelId=="")
       		  $userData->hotelId = 0;
		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $guestDetails
     */
	public static function addGuestDetails($guestDetails){
		$userData =  $_SESSION['userData'];
		
		$userData->first_name = ucfirst($guestDetails["first_name"]);
		$userData->remarks = $guestDetails["remarks"];
		$userData->last_name = ucfirst($guestDetails["last_name"]);
		$userData->address	= ucfirst($guestDetails["address"]);
		$userData->city	= $guestDetails["city"];
		$userData->state_name	= $guestDetails["state_name"];
		$userData->country	= $guestDetails["country"];
		$userData->postal_code= strtoupper($guestDetails["postal_code"]);
		$userData->phone = $guestDetails["phone"];
		$userData->email= $guestDetails["email"];
		$userData->conf_email = $guestDetails["conf_email"];
		$userData->company_name=$guestDetails["company_name"];
		$userData->guest_type = $guestDetails["guest_type"]; 
		
		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $loggedInguestDetails
     */
    public static function prepareLoggedInGuestDetails()
    {

        if(JFactory::getUser()->id > 0) {
            $loggedInGuestDetails = ReservationService::getLogInUserData(JFactory::getUser()->id);
            if (!empty($loggedInGuestDetails) && empty($userData->first_name) && empty($userData->last_name) && empty($userData->email)) {
            	$userData = $_SESSION['userData'];
            	             	
            	$userData->first_name = $loggedInGuestDetails->first_name;
                $userData->remarks = $loggedInGuestDetails->remarks;
                $userData->last_name = $loggedInGuestDetails->last_name;
                $userData->address = $loggedInGuestDetails->address;
                $userData->city = $loggedInGuestDetails->city;
                $userData->state_name = $loggedInGuestDetails->state_name;
                $userData->country = $loggedInGuestDetails->country;
                $userData->postal_code = $loggedInGuestDetails->postal_code;
                $userData->phone = $loggedInGuestDetails->phone;
                $userData->email = $loggedInGuestDetails->email;
                $userData->conf_email = $loggedInGuestDetails->conf_email;
                $userData->company_name = $loggedInGuestDetails->company_name;
                $userData->guest_type = $loggedInGuestDetails->guest_type;
                $userData->guestDetails = $loggedInGuestDetails->guestDetails;

                $_SESSION['userData'] = $userData;
            }
        }
    }


    /**
     * @param $reservationDetails
     */
	public static function setReservationDetails($reservationDetails){
		$userData =  $_SESSION['userData'];

		$userData->total = $reservationDetails->total;
		$userData->cost = $reservationDetails->cost;
		
		$_SESSION['userData']= $userData;
	}
	
	public static function setCurrency($currencyName, $currencySymbol){
		$userData =  $_SESSION['userData'];
		
		$currency = new stdClass();
		$currency->name = $currencyName;
		$currency->symbol = $currencySymbol;
		
		$userData->currency = $currency;
		if($userData->user_currency=="")
			$userData->user_currency = $currency->name;
		
		$_SESSION['userData'] = $userData;
	}

    /**
     *
     */
	public static function prepareGuestDetails(){
		$userData =  $_SESSION['userData'];
		
		if(isset($userData->guestDetails) && count($userData->guestDetails)== $userData->total_adults)
			return;
	
		$guestDetails = array();
		for($i=0;$i<$userData->total_adults;$i++){
			$guestDetail = new stdClass();
			$guestDetail->first_name="";
			$guestDetail->last_name="";
			$guestDetail->identification_number="";
			$guestDetails[] = $guestDetail;
		}
		$userData->guestDetails = $guestDetails;

		$_SESSION['userData'] = $userData;
	}

	/**
	 * Function to prepare and parse newly viewed hotels by their hotel id
	 * adding the hotel id at the end of the array that represent the viewed properties
	 */
	public static function prepareUserViewedProperties(){
		$userData =  $_SESSION['userData'];

		if(JRequest::getVar("hotel_id") != null)
		{
			$userData->user_properties[] = JRequest::getVar( "hotel_id" );
			$userData->user_properties   = array_unique( $userData->user_properties );
		}

		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $data
     */
	public static function storeGuestDetails($data){
		$userData =  $_SESSION['userData'];
		
		$guestDetails = array();
		for($i=0;$i<count($data["guest_first_name"]);$i++){
			$guestDetail = new stdClass();
			$guestDetail->first_name = $data["guest_first_name"][$i];
			$guestDetail->last_name = $data["guest_last_name"][$i];
			$guestDetail->identification_number= $data["guest_identification_number"][$i];
			$guestDetails[] = $guestDetail;
		}
		
		$userData->guestDetails = $guestDetails;
		
		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $discountCode
     */
	public static function setDiscountCode($discountCode){
		$userData =  $_SESSION['userData'];

		$userData->discount_code = $discountCode;
		 
		$_SESSION['userData'] = $userData;
	}
	public static function updateUserData(){
		$userData =  $_SESSION['userData'];

		$currentRoom = count($userData->reservedItems);
		if(isset($userData->roomGuests[$currentRoom]))
			$userData->adults = $userData->roomGuests[$currentRoom];
		else
			$userData->adults = 2;
		if(isset($userData->roomGuestsChildren[$currentRoom]))
			$userData->children = $userData->roomGuestsChildren[$currentRoom];
		else
			$userData->children = 0;
		
		$_SESSION['userData'] = $userData;
	}
	  
	/**
	 * Initialiaze search criteria
	 */
	public static function initializeUserData($resetUserData = false){

		$get = JRequest::get( 'get' );
		$post = JRequest::get( 'post' );
		if(count($post)==0)
			$post = $get;
		
		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:null;
		if(!isset($userData) || !empty($post["resetSearch"]) || $resetUserData){
			//dmp($post);exit;
			$userData = self::createUserData($post,$userData);
			$_SESSION['userData'] = $userData;
		}
		
		if(JRequest::getVar( 'minNights')!='')
			$userData = self::changeDepatureDate($userData,$userData->start_date ,JRequest::getVar( 'minNights'));
		
		if(isset($get['filterParams'])){
			$userData->voucher='';
			$userData->keyword='';
			$_SESSION['userData'] = $userData;
		}
		$userData = self::initializeFilter($userData,$post);

		$_SESSION['userData'] = $userData;
		
		return $userData;
	}

    /**
     * @return mixed
     */
	public static function initializeReservationData(){
		$userData =  $_SESSION['userData'];
		
		$userData->reservedItems = array();
		$userData->confirmation_id = 0;
		$userData->totalPaid= 0;

        $userData->airportTransfers=array();

		$_SESSION['userData'] = $userData;

		return $userData;
	}

	public static function initializeExcursions(){
		$userData =  $_SESSION['userData'];
		$userData->excursions = array();
		$_SESSION['userData'] = $userData;
	}

    /**
     * @param $data
     * @param $userData
     * @return stdClass
     */
	public static function createUserData($data,$userData){
		if(isset($data) && count($data)>0 && isset($data["jhotelreservation_datas"])){
			if(!empty($data["keyword"]))
				$userData->keyword = $data["keyword"];
			else
				$userData->keyword = "";
			$data["jhotelreservation_datas"] = empty($data["jhotelreservation_datas"])?date('Y-m-d'):$data["jhotelreservation_datas"];
			$data["jhotelreservation_datae"] = empty($data["jhotelreservation_datae"])?date( "Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"))):$data["jhotelreservation_datae"];
			$userData->start_date = JHotelUtil::convertToMysqlFormat($data["jhotelreservation_datas"]);
			$userData->end_date = JHotelUtil::convertToMysqlFormat($data["jhotelreservation_datae"]);
			$userData->rooms = $data["rooms"];
			$userData->adults = $data["guest_adult"];
			$userData->total_adults = $userData->adults;
			$userData->children = $data["guest_child"];
			$userData->total_children= $userData->children;
			$userData->year_start =$data["year_start"];
			$userData->month_start =$data["month_start"];
			$userData->day_start =$data["day_start"];
			$userData->year_end = $data["year_end"];
			$userData->month_end =$data["month_end"];
			$userData->day_end =$data["day_end"];
			$userData->discount_code = "";
			$userData->room_prices = "";
			$userData->currency = new stdClass();
				
			
			if(isset($data["user_currency"]))
				$userData->user_currency =$data["user_currency"];
			else 
				$userData->user_currency = "";
			if(isset($data["excursions"]))
				$userData->excursions =$data["excursions"];
			else
				$userData->excursions = array();
			
			if($userData->children==0)
				$data["jhotelreservation_child_ages"] = null;
			if(isset($data["jhotelreservation_child_ages"]))
				$userData->jhotelreservation_child_ages =$data["jhotelreservation_child_ages"];
			else
				$userData->jhotelreservation_child_ages = array();
		
			$userData->voucher = isset($data["voucher"])?$data["voucher"]:"";
			$userData->filterParams ='';
			$userData->searchFilter = array("filterCategories"=>array());
			
			if(isset($data["room-guests"]) && count($data["room-guests"])>1){
				$userData->roomGuests = $data["room-guests"];
				$userData->rooms = count($userData->roomGuests);
				$userData->total_adults = 0;
				foreach($userData->roomGuests as $guestPerRoom){
					$userData->total_adults+= $guestPerRoom;
				}
				JRequest::setVar('jhotelreservation_rooms',$userData->rooms);
				JRequest::setVar('jhotelreservation_guest_adult',$userData->total_adults);
			}else{
				$userData->roomGuests=array($data["guest_adult"]); 
			}
				
			if(isset($data["room-guests-children"]) && count($data["room-guests-children"])>1){
				$userData->roomGuestsChildren = $data["room-guests-children"];
				$userData->total_children = 0;
				$idx = 1; 
				foreach($userData->roomGuestsChildren as $guestPerRoom){
					$userData->total_children+= $guestPerRoom;
					
					if(isset($data["room_children_ages_".$idx]) && count($data["room_children_ages_".$idx])>0){
						$userData->roomChildrenAges[$idx] = $data["room_children_ages_".$idx];
					}else{
						$userData->roomChildrenAges[$idx] = array(0);
					}
					$idx++;	
				}
				JRequest::setVar('jhotelreservation_guest_child',$userData->total_children);
			}else{
				$userData->roomGuestsChildren=array($data["guest_child"]);
			}
			//dmp($userData->total_children);
				
			$userData->noDates = JRequest::getVar('no-dates', null);
		}else{
			if(empty($userData))
				$userData = new stdClass();
			$userData->searchType = '';
			$userData->keyword = '';
			$userData->start_date = date('Y-m-d');
			$userData->end_date = date( "Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));;
			$userData->rooms = '1';
			$userData->roomGuests = array(2);
			$userData->roomGuestsChildren = array(0);
			$userData->adults = isset($data["guest_adult"])?$data["guest_adult"]:'2';
			$userData->children = isset($data["guest_children"])?$data["guest_children"]:'0';
			$userData->total_adults = $userData->adults;
			$userData->total_children = $userData->children;
			$userData->nights = '1';
            $userData->year_start =date('Y');
			$userData->month_start =date('m');
			$userData->day_start = date('d');
			$userData->year_end = date( "Y",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
			$userData->month_end = date( "m",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
			$userData->day_end = date( "d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
			$userData->voucher = isset($data["voucher"])?$data["voucher"]:"";
			$userData->filterParams ='';
			$userData->searchFilter = array("filterCategories"=>array());
			$userData->excursions=array();
			$userData->discount_code = "";
			$userData->currency = new stdClass();
				
			
			if(isset($data["keyword"]))
				$userData->keyword = $data["keyword"];
			else
				$userData->keyword = "";

			$userData->user_currency = "";

		}

		$userData->reservedItems = array();
		$userData->confirmation_id = 0;
		if(!isset($userData->first_name))
			$userData->first_name = '';
		if(!isset($userData->last_name))
			$userData->last_name = '';
		if(!isset($userData->address))
			$userData->address	= '';
		if(!isset($userData->city))
			$userData->city	= '';
        if(!isset($userData->state_name))
			$userData->state_name	= '';
		if(!isset($userData->country))
			$userData->country	= '';
		if(!isset($userData->postal_code))
			$userData->postal_code= '';
		if(!isset($userData->phone))
			$userData->phone = '';
		if(!isset($userData->email))
			$userData->email= '';
		if(!isset($userData->conf_email))
			$userData->conf_email = '';
		if(!isset($userData->company_name))
			$userData->company_name='';
		if(!isset($userData->coupon_code))
			$userData->coupon_code='';
		if(!isset($userData->guest_type))
			$userData->guest_type = 0;
		if(!isset($userData->discount_code))
			$userData->discount_code='';
		if(!isset($userData->remarks))
			$userData->remarks='';
		$userData->remarks_admin='';
		$userData->media_referer='';
		$userData->arrival_time='';
		$userData->totalPaid= 0;
			$userData->extraOptionIds=array();
			$userData->airportTransfers=array();
		$userData->user_properties = array();

		return $userData;
		
	}

    /**
     * @return float
     */
 	public static function getNrDays(){
 		$userData =  $_SESSION['userData'];
 		
 		$nrDays = JHotelUtil::getNumberOfDays($userData->start_date,$userData->end_date);
 		
 		return $nrDays;
 	}


    /**
     * @param $userData
     * @param $currentDate
     * @param $days
     * @return mixed
     */
	public static function changeDepatureDate($userData, $currentDate, $days){
		
		$currentDate = strtotime($currentDate);
		$date = date( "Y-m-d",mktime(0, 0, 0, date("m"), date("d")+$days, date("Y")));
		$userData->end_date = date( "Y-m-d",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		$userData->year_end = date( "Y",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		$userData->month_end = date( "m",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		$userData->day_end = date( "d",mktime(0, 0, 0, date("m",$currentDate), date("d",$currentDate)+$days, date("Y",$currentDate)));
		
		return $userData;
	}

    /**
     * @param $userData
     * @param $post
     * @return mixed
     */
	 public static function initializeFilter($userData, $post){
         $userData->filterParams = JRequest::getVar('filterParams');

         $filterRegion = JRequest::getVar('filterRegion',null);
         $filterType = JRequest::getVar('filterType',null);

         if(!empty($filterRegion)) {
             $userData->filterParams .= "&regionId=".$filterRegion;
         }

         if(!empty($filterType)) {
             $userData->filterParams .= "&typeId=".$filterType;
         }

		if(JRequest::getInt("resetSearch")==1 && JRequest::getInt("searchId",0)==0){
			$userData->filterParams= "";
			$userData->minPrice=0;
			$userData->maxPrice=0;
		}
		$userData->orderBy = JRequest::getVar('orderBy');
		$ordering = JRequest::getVar('ordering');
		 if(!isset($ordering)) {
			 $ordering = 'desc';
		 }
		$userData->ordering = $ordering;
		
		if($userData->orderBy=='' && $userData->voucher == '')
			$userData->orderBy ="noBookings ".$ordering;
		if(isset($post["voucher"])){
			$userData->voucher = $post["voucher"];
		}
		return $userData;
	}

    /**
     * @param $reservedItems
     */
	public static function setReservedItems($reservedItems){
		$userData =  $_SESSION['userData'];
		$userData->reservedItems = $reservedItems;
		$_SESSION['userData']= $userData;
	}
	
	private static function getJoomlaSession(){
		$session = JFactory::getSession();
		return $session;
	}
	
	public static function checkValidReservationData() {
		if(!empty( $_SESSION['userData'])){
			$userData =  $_SESSION['userData'];

			//redirect if reserved items or excursions are not set
			if((empty($userData->reservedItems) && empty($userData->excursions)) || (!empty($userData->reservedItems) && empty($userData->hotelId)) ){
				$app = JFactory::getApplication();
				$msg = "Your session has expired";
				$app->redirect( 'index.php?option='.getBookingExtName().'&task=hotels.searchHotels', $msg );
			}
		}
	}
	
}

?>