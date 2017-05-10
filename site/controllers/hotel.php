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

class JHotelReservationControllerHotel extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function showHotel(){
		//initialize search criteria
		$data = JRequest::get("get");
		$hotelId = JRequest::getVar("hotel_id");
		UserDataService::initializeUserData();
		
		if(isset($data["init_hotel"])){
			UserDataService::initializeReservationData();
		}
		$reservedItems = JRequest::getVar("reservedItems");
		if(!empty($reservedItems)){
			UserDataService::updateRooms($hotelId, $reservedItems);
		}		
		
		JRequest::setVar("view","hotel");
		parent::display();
	}
	
	function changeSearch(){
		$data = JRequest::get("post");
		JRequest::setVar("init_hotel","1");
		$hotel = HotelService::getHotel($data["hotel_id"]);
		$link = JHotelUtil::getHotelLink($hotel);
		$this->setRedirect($link);	
	}
	
	function reserveRoom(){
		$data = JRequest::get("post");
				
		$reservedItems = JRequest::getVar("reservedItems");
		$extraOptions = JRequest::getVar("extraOptions");

		$reservedItem = UserDataService::reserveRoom($data["hotel_id"], $data["reserved_item"], (int)$data["current"]);
		$reservedItems = empty($reservedItems)?$reservedItem:$reservedItems."||".$reservedItem;

		$extraParam ="";
		if(!empty( $extraOptions )){
			$extraParam = "&extraOptions=".$extraOptions ;
		}
		
		$current = count(explode('||',$reservedItems));
		
		
		
        $userData = UserDataService::getUserData();
        
        $roomReserved = $reservedItems[count($reservedItems)-1];
        $roomReservedInfo = explode("|",$reservedItem);

        $extraOptions = ExtraOptionsService::getHotelExtraOptions($data["hotel_id"], $userData->start_date, $userData->end_date, array(), $roomReservedInfo[1], $roomReservedInfo[0]);
        $airportTransferType = AirportTransferService::getHotelAirportTransferTypes($data["hotel_id"]);
        $appSetting = JHotelUtil::getApplicationSettings();

        if(PROFESSIONAL_VERSION==1 && (($appSetting->is_enable_extra_options && count($extraOptions)>0) || count($airportTransferType)>0  && $appSetting->is_enable_screen_airport_transfer)){
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=extraoptions&task=extraoptions.showExtras&hotelId=' . $data["hotel_id"] . '&current=' . $current . '&reservedItems=' . $reservedItems . $extraParam.JHotelUtil::getItemIdS(), false,-1));
        }
        else if(count($userData->reservedItems)< $userData->rooms ){
        	$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=hotel&task=hotel.showHotel&hotel_id='.$userData->hotelId."&reservedItems=".$reservedItems, false));
        }
        else {
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=guestDetails&task=guestDetails.showGuestDetails&reservedItems=' . $reservedItems.JHotelUtil::getItemIdS().'&hotel_id='.$userData->hotelId, false,-1));
        }
	}
	
	function getRoomCalendar(){
		//header('Content-type: XML');
	
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
	
	function getRoomCalendars(){
		
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
	
		$hotelId = $post["hotel_id"];
		$currentRoom = $post["current_room"];
		$adults = $userData->adults;
		$children = $userData->children;
		if(isset($userData->roomGuests)){
			$adults = $userData->roomGuests[$currentRoom-1];
		}
		
		$post["guest_adult"] = $adults;
		$post["guest_child"] = $userData->children;
		$post["rooms"] = $userData->rooms;
	
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
		
		$datasi			= date( "Y-m-d", mktime(0, 0, 0, $userData->month_start, $userData->day_start,$userData->year_start )	);
		$dataei			= date( "Y-m-d", mktime(0, 0, 0, $userData->month_end, $userData->day_end,$userData->year_end ));
		
		$diff = abs(strtotime($dataei) - strtotime($datasi));
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		
		$initialNrDays = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	
		$datas			= date( "Y-m-d", mktime(0, 0, 0, $month_start, $day_start,$year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $month_end, $day_end + 20,$year_end ));

		$offers =HotelService::getHotelOffers($hotelId,$datas, $datae,array(),$adults, $children);
		$rooms =HotelService::getHotelRooms($hotelId,$datas, $datae,array(),$adults, $children);


		$bookingsDays = BookingService::getNumberOfBookingsPerDay($hotelId,$datas, $datae);
		$hoteAvailability = HotelService::getHotelAvailabilyPerDay($hotelId,$datas, $datae);
		
		$temporaryReservedRooms= BookingService::getReservedRooms($userData->reservedItems);
		$temporaryReservedRooms["datas"]= $datasi;
		$temporaryReservedRooms["datae"]= $dataei;
			
		$roomsCalendar = HotelService::getRoomsCalendar($rooms,$initialNrDays,$adults,$children,$month_start,$year_start, $bookingsDays,$temporaryReservedRooms, $hoteAvailability);
		$offersCalendar = HotelService::getOffersCalendar($offers,$initialNrDays,$adults,$children,$month_start,$year_start, $bookingsDays,$temporaryReservedRooms, $hoteAvailability);
	
		//combining the calendars
		$calendar = array_combine(
				array_merge(array_keys($roomsCalendar),array_keys($offersCalendar)),
				array_merge(array_values($roomsCalendar),array_values($offersCalendar))
		);
		return $calendar;
	}
	
	public function checkReservationPendingPayments(){
		BookingService::checkReservationPendingPayments();
		JFactory::getApplication()->close();
	}
	//test method for availability. 
	public function checkAvailability(){
		$hotelId = 29;
		$startDate ="2015-12-17";
		$endDate ="2015-12-18";
	
		$isHotelAvailable = HotelService::checkAvailability($hotelId, $startDate, $endDate);

	
		if(!$isHotelAvailable){
			EmailService::sendNoAvailabilityEmail($hotelId, $startDate, $endDate);
		}
	}

    //api to save the comment
    public function saveComment(){
        //call model methods
        $model = $this->getModel('hotel');
        $post = JRequest::get( 'post' );
        $model->saveComment($post);
    }

    //api to save the comment
    public function editComment(){
        //call model methods
        $model = $this->getModel('hotel');
        $post = JRequest::get( 'post' );
        $model->editComment($post);
    }
    //api to delete a comment only by the user who made it
    public function deleteComment(){
        //call model methods
        $model = $this->getModel('hotel');
        $post = JRequest::get( 'post' );
        $model->deleteComment($post);
    }

    //login the user to comment
    public function loginUser(){
        $post = JRequest::get( 'post' );
        $where = '';
        if(isset($post['clickedLink']) && $post['clickedLink'] > 0){
           $where =  '&selected='.$post['clickedLink'];
        }
        $hotel = HotelService::getHotel($post['hotelIdReview']);
        $link = JHotelUtil::getHotelLink($hotel).'?'.strtolower(JText::_("LNG_REVIEWS").$where);

        UserService::isUserLoggedIn($link);
    }
}