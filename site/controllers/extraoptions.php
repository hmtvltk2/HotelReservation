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

class JHotelReservationControllerExtraOptions extends JControllerLegacy
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function showExtras(){
	
		//add room if does not exist
		$reservedItems = JRequest::getVar("reservedItems");
		$hotelId = JRequest::getVar("hotelId");
		
		UserDataService::updateRooms($hotelId, $reservedItems);
		
		$userData = UserDataService::getUserData();
		$userData->hotelId = $hotelId;

        $data = JRequest::get("post");
        $extraOptions = array();
        if(isset($data["extraOptionIds"])){
            $extraOptions = ExtraOptionsService::parseExtraOptions($data);
        }
        if(count($extraOptions)>0){
            UserDataService::addExtraOptions($extraOptions,JRequest::getVar("current"));
        }

        $appSetting = JHotelUtil::getApplicationSettings();
    	$model = $this->getModel("ExtraOptions");
		$extraOptions = $model->getExtraOptions();

		if(PROFESSIONAL_VERSION==1 && (($appSetting->is_enable_extra_options && count($extraOptions)>0)  || $appSetting->is_enable_screen_airport_transfer)){
			JRequest::setVar("view","extraoptions");
			parent::display();
		}else{
			if(count($userData->reservedItems) < $userData->rooms ){
				$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=hotel&task=hotel.showHotel&hotel_id='.$userData->hotelId."&reservedItems=".$reservedItems, false));
			}
			else{
				$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=guestDetails&task=guestDetails.showGuestDetails&reservedItems='.$reservedItems, false));
			}
		}
	}
	
	function addExtraOptions(){
		$reservedItems = JRequest::getVar("reservedItems");
		$hotelId = JRequest::getVar("hotel_id");
		
		if(!empty($reservedItems)){
			UserDataService::updateRooms($hotelId, $reservedItems);
		}
		
		$data = JRequest::get("post");

		$current = $data["current"];
		$extraOptions = array();
		if(isset($data["extraOptionIds"])){
			$extraOptions = ExtraOptionsService::parseExtraOptions($data);
		}
		UserDataService::addExtraOptions($extraOptions,$data["current"]);
            UserDataService::addAirportTrasnferTypes($data);

		$userData = UserDataService::getUserData();
		
		if(count($userData->reservedItems) < $userData->rooms ){
		    $extra = implode("#",$extraOptions);
			$extraParam ="";
			if(!empty( $extra)){
				$extraParam = "&extraOptions=".$extra;
			}
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=hotel.showHotel&reservedItems='.$reservedItems.'&hotel_id='.$hotelId.$extraParam, false));
		}
		else{
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&task=guestDetails.showGuestDetails&view=guestDetails&reservedItems='.$reservedItems, false));
		}
	}
	
	function back(){
		UserDataService::removeLastRoom();
		$userData = UserDataService::getUserData();
		$hotel = HotelService::getHotel($userData->hotelId);
		$link = JHotelUtil::getHotelLink($hotel);
		$this->setRedirect($link);
	}


    function back1(){
        $data = JRequest::get("post");
        $reservedItems = JRequest::getVar("reservedItems");
        $hotelId = JRequest::getVar("hotel_id");
        
        $model = $this->getModel("ExtraOptions");
        $extraOptions = $model->getExtraOptions();
        $airportTrasnferType= $model->getAirportTransferTypes();
        $appSetting = JHotelUtil::getApplicationSettings();
		
		if(PROFESSIONAL_VERSION==1 && (($appSetting->is_enable_extra_options && count($extraOptions)>0) || count($airportTrasnferType)>0  && $appSetting->is_enable_screen_airport_transfer)){
        	JRequest::setVar("view","extraoptions");
        	JRequest::setVar("reservedItems",$reservedItems);
        	$reservedArray= explode("||",$reservedItems);
        	JRequest::setVar("current",count($reservedArray));
        	parent::display();
        }
        else{
        	$hotel = HotelService::getHotel($hotelId);
        	$hotelLink = JHotelUtil::getHotelLink($hotel);
        	$app =JFactory::getApplication();
        	$app->redirect($hotelLink);
        }
    }
}