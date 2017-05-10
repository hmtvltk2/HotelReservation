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

class JHotelReservationControllerGuestDetails extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
	}
	
	function showGuestDetails(){
		JRequest::setVar("view","guestDetails");
		parent::display();
	}
	
	function addGuestDetails(){
		$data = JRequest::get("post");
		UserDataService::addGuestDetails($data);
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		if($appSettings->save_all_guests_data){
			UserDataService::storeGuestDetails($data);
		}
		
		
		$reservedItems = JRequest::getVar("reservedItems");
		$hotelId = JRequest::getVar("hotel_id");
		if(!empty($reservedItems)){
			UserDataService::updateRooms($hotelId, $reservedItems);
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=paymentoptions&task=paymentoptions.showPaymentOptions', false));
	}
	
	
	function back(){
		
		UserDataService::removeLastRoom();
		$userData = UserDataService::getUserData();

		if($userData->hotelId>0){
			$hotel = HotelService::getHotel($userData->hotelId);
			$link = JHotelUtil::getHotelLink($hotel);
		}
		else{
			$link = (JRoute::_('index.php?option=com_jhotelreservation&view=excursionslisting&task=excursionslisting.searchExcursions', false));
			UserDataService::initializeExcursions();
		}
		$this->setRedirect($link);
	}

    function getPastReservationData(){
        //$hotelId= JRequest::getVar("hotel_id");
        $userData = UserDataService::getUserData();
        $reservedItems = JRequest::getVar("reservedItems");
        UserService::isUserLoggedIn('index.php?option=com_jhotelreservation&view=guestDetails&task=guestDetails.showGuestDetails&reservedItems='.$reservedItems.'&hotel_id='.$userData->hotelId);
        UserDataService::prepareLoggedInGuestDetails();
    }
}