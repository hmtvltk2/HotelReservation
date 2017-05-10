<?php
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JHotelReservationControllerOfferListing extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	
	}
	function searchOffers(){
		//initialize search criteria
		JRequest::setVar('resetSearch',1);
		UserDataService::initializeUserData();
		//remove user data
		JRequest::setVar("view","offerlisting");
		parent::display();
	}
	
	function getOffersCalendars(){
	
		$calendars = $this->generateCalendarData();
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		foreach($calendars as $key=>$value){
			echo '<answer identifier="'.$key.'" calendar="'.htmlspecialchars($value).'" />';
		}
		echo '</room_statement>';
		echo '</xml>';
	
		JFactory::getApplication()->close();
	}
	
	function getRoomCalendar(){
	
		$year = JRequest::getVar("year");
		$month = JRequest::getVar("month");
		$identifier = JRequest::getVar("identifier");
	
		$calendars = $this->generateCalendarData($year, $month);
		$calendar = $calendars[$identifier];
	
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer identifier="'.$identifier.'" calendar="'.htmlspecialchars($calendar).'" />';
		echo '</room_statement>';
		echo '</xml>';
	
		JFactory::getApplication()->close();
	}
	
	function reserveRoom(){
		$data = JRequest::get("post");
		$currency = JRequest::getVar("currency");
		$roomGuests = array();
		$totalAdults =  $data["offer_adults"]; 
		
		UserDataService::setCurrency($currency,$currency);
		
		$adults = intval($data["offer_adults"]);
		$capacity= intval($data["room_capacity"]);
		$current = $data['current'];
		
		$nrRooms =1; 
		if($data["offer_adults"]>$data["room_capacity"]){
			$nrRooms =floor($adults/$capacity); 
		}
		/*$offerData  = explode("|",$data["reserved_item"]);
		$excursions = ExcursionsService::getOfferExcursions($offerData[0]);*/

		for($i=1;$i<=$nrRooms;$i++){
			$reservedItem = UserDataService::reserveRoom($data["hotel_id"], $data["reserved_item"], $i);
			$reservedItems = empty($reservedItems)?$reservedItem:$reservedItems."||".$reservedItem;
			
			//set number of guest per room 
			if($i==$nrRooms)
				$roomGuests[] = $totalAdults;
			else 
				$roomGuests[] = $data["room_capacity"];
			
			$totalAdults -= $data["room_capacity"];
				
			
			/*foreach($excursions as $excursionObj){
				$excursion = new stdClass;
				$excursion->id = intval($excursionObj->excursion_id);
				$excursion->date = $data["jhotelreservation_datas"];
				$excursion->quantity = 1;
				ExcursionsService::addExcursion($excursion);
			}  */
		}
		UserDataService::updateGuests($data["offer_adults"],$roomGuests);

		$userData = UserDataService::getUserData();
	
		$roomReservedInfo = explode("|",$reservedItem);
		$extraOptions = ExtraOptionsService::getHotelExtraOptions($data["hotel_id"], $userData->start_date, $userData->end_date, array(), $roomReservedInfo[1], $roomReservedInfo[0]);
		$airportTransferType = AirportTransferService::getHotelAirportTransferTypes($data["hotel_id"]);
		$appSetting = JHotelUtil::getApplicationSettings();
		
		
		if(PROFESSIONAL_VERSION==1 && (($appSetting->is_enable_extra_options && count($extraOptions)>0) || count($airportTransferType)>0  && $appSetting->is_enable_screen_airport_transfer)){
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=extraoptions.showExtras&hotelId=' . $data["hotel_id"] . '&current=' . $current . '&reservedItems=' . $reservedItems . $extraParam, false));
		}else {
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=guestDetails.showGuestDetails&view=guestDetails&reservedItems=' . $reservedItems, false));
		}
		
	}
	
	
	function generateCalendarData($year = 0, $month=0){
	
		$session = JFactory::getSession();
		$userData =  $_SESSION['userData'];
	
		$post = JRequest::get('post');
		$get = JRequest::get('get');
		if(!count($post))
			$post = $get;
	
		if(!isset($post["hotel_id"])){
			$post["hotel_id"] = JRequest::getInt( 'hotel_id');
		}
	
		$year_start = $userData->year_start;
		$month_start = $userData->month_start;
		$day_start = 1;
		$year_end = $userData->year_end;
		$month_end = $userData->month_start;
		$day_end =  date('t', mktime(0, 0, 0, $userData->month_start, 1, $userData->year_start));
	
		//dmp($currentRoom);
		$adults = $userData->adults;
		$children = $userData->children;
		//dmp($userData);
	
		$post["guest_adult"] = $adults;
		$post["guest_child"] = $userData->children;
	
		if($year!=0){
			$post["year_start"] = $year;
			$post["year_end"] = $year;
			$year_start = $year;
			$year_end = $year;
		}
		if($month != 0){
			$post["month_start"] = $month;
			$post["month_end"] = $month;
			$month_start = $month;
			$month_end = $month;
		}
			
		$number_persons = $post["guest_adult"];
	
		//dmp($post);
		$datasi			= date( "Y-m-d", mktime(0, 0, 0, $userData->month_start, $userData->day_start,$userData->year_start )	);
		$dataei			= date( "Y-m-d", mktime(0, 0, 0, $userData->month_end, $userData->day_end,$userData->year_end ));
	
		$diff = abs(strtotime($dataei) - strtotime($datasi));
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	
		$initialNrDays = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		//dmp($initialNrDays);

		$datas			= date( "Y-m-d", mktime(0, 0, 0, $month_start, $day_start,$year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $month_end, $day_end + 7,$year_end ));
	
		$hotelId = null;

		$offers =HotelService::getAllOffers($datas, $datae,array(),$adults, $children);
		$bookingsDays = BookingService::getNumberOfBookingsPerDay($hotelId,$datas, $datae);
		$hoteAvailability = null;
		$temporaryReservedRooms= null;
		$offersCalendar = HotelService::getOffersCalendar($offers,$initialNrDays,$adults,$children,$month_start,$year_start, $bookingsDays,$temporaryReservedRooms, $hoteAvailability);
		
		//dmp($offersCalendar);

		return $offersCalendar;
	}
}