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

class JHotelReservationControllerExcursionsListing extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	
	}
	
	function searchExcursions(){	
		//initialize search criteria
		$post = JRequest::get('post');
		JRequest::setVar("view","excursionslisting");
		parent::display();
	}
	
	function searchCourses(){
		JRequest::setVar("view","excursionslisting");
		parent::display();
	}
	
	
	function reserveExcursions(){
		$data = JRequest::get("get");
		if(isset($data["excursion_id"])){
			$excursion = new stdClass;
			$excursion->id = $data["excursion_id"]; 
			$excursion->date = $data["date"];
			$excursion->quantity = $data["quantity"];
			$excursion->nrDays = $data["nrDays"];
			$excursions = ExcursionsService::addExcursion($excursion);
			print_r(json_encode($excursions)); 
			exit;
		}
		return null;
	}
	
	function getExcursionPrice(){
		$excursionData = new stdClass();
		$excursionId = JRequest::getVar('excursion_id');
		$excursionData->excursionIds = array($excursionId);
		$excursionData->nrBooked[$excursionId]=1;
		$excursionData->nrDays[$excursionId] = JRequest::getVar('nrDays');
		$datas =  JRequest::getVar('date');
		$datae = null;
		
		$excursions =ExcursionsService::getHotelExcursions(-1,-1,$datas, $datae,$excursionData,null,true,null,false);
		echo $excursions[0]->pers_total_price;
		exit;
	}
	
	function nextStepExcursions(){
		$data = JRequest::get("post");
		if($data["hotelRedirect"]==1 && $data["excursionRedirect"]==1)
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=hotels.searchHotels', false));
		else if($data["excursionRedirect"]==1)
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=hotels.searchHotels', false));
		else
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=excursionslisting.searchExcursions&excursion_type=courses&excursionRedirect=1&hotelRedirect='.$data["hotelRedirect"], false));
	}
	
	function nextStepCourses(){
		$data = JRequest::get("post");
		if($data["hotelRedirect"]==1 && $data["excursionRedirect"]==1){
			$userData = UserDataService::getUserData();
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=guestDetails&hotel_id='.$userData->hotelId."&reservedItems=".implode("||",$userData->reservedItems), false));
		}
		else if($data["excursionRedirect"]==1)
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=hotels.searchHotels', false));
		else
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=excursionslisting.searchExcursions&excursion_type=excursions&excursionRedirect=1', false));
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
	
	function getExcursionsCalendars(){
	
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
	
		$post = JRequest::get('post');
		$get = JRequest::get('get');
		if(!count($post))
			$post = $get;
		
		$excursionStartDate = JRequest::getVar("excursion_start_date",null);
		if(empty($excursionStartDate))
			$excursionStartDate = date('Y-m-d');

		$yearStart = date('Y',strtotime($excursionStartDate));
		$monthStart = date('m',strtotime($excursionStartDate));
		$dayStart = date('d',strtotime($excursionStartDate));
		
		$yearEnd = date('Y',strtotime($excursionStartDate));
		$monthEnd = date('m',strtotime($excursionStartDate));
		$dayEnd = date('d',strtotime($excursionStartDate));
		
		$year_start = $yearStart;
		$month_start = $monthStart;
		$day_start = 1;
		$year_end = $yearEnd;
		$month_end = $monthStart;
		$day_end =  date('t', mktime(0, 0, 0, $monthStart, 1, $yearStart));
	
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
		$datasi			= date( "Y-m-d", mktime(0, 0, 0, $monthStart, $dayStart,$yearStart )	);
		$dataei			= date( "Y-m-d", mktime(0, 0, 0, $monthEnd, $dayEnd,$yearEnd ));
	
		$diff = abs(strtotime($dataei) - strtotime($datasi));
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	
		$initialNrDays = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	
		$datas			= date( "Y-m-d", mktime(0, 0, 0, $month_start, $day_start,$year_start )	);
		$datae			= date( "Y-m-d", mktime(0, 0, 0, $month_end, $day_end + 7,$year_end ));
	
		$hotelId =-1;
		
		$excursionData = new stdClass();		
		$excursionData->excursionIds =null;
		$excursionData->nrBooked=null;
		$excursionData->nrDays=null;
		
		$excursions =ExcursionsService::getHotelExcursions(-1,$hotelId,$datas, $datae,$excursionData,0,100,null,true,null,true);

		$bookingsDays = BookingService::getExcursionBookingsPerDay($hotelId,$datas, $datae);
		$hoteAvailability = null;
		$temporaryReservedRooms= null;
		$excursionCalendar = ExcursionsService::getExcursionCalendar($excursions,$initialNrDays,$month_start,$year_start, $bookingsDays,$temporaryReservedRooms, $hoteAvailability);
	
		//dmp($excursionCalendar);
	

		return $excursionCalendar;
	}
	
	
	
}