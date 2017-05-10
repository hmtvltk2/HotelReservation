<?php

/**
 * @copyright	Copyright (C) 2008-2016 CMSJunkie. All rights reserved.
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


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once( JPATH_COMPONENT_ADMINISTRATOR.'/models/hotel.php' );


class JHotelReservationModelAddHotel extends JHotelReservationModelHotel {



	/**
	 * @param $email
	 * @param $hotel_name
	 *
	 * method to send the notification email to the hotel admin
	 * after the hotel request from the front end is saved in the database
	 *
	 * @return mixed
	 */
	function sendEmailAddHotelRequest($email,$hotel_name){

		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

		//content of the email
		$content = JText::_('LNG_REQUEST_ADD_HOTEL_MESSAGE',true);
		// the hotel name and email saved in the request
		$content = str_replace("<email>",$email,$content);
		$content = str_replace("<hotel_name>",$hotel_name,$content);
		$from = $appSettings->company_email;
		$fromName = $appSettings->company_name;
		$isHtml = true;
		$subject = JText::_("LNG_REQUEST_TO_ADD_HOTEL");
		// notifications sent only to hotel administrator
		$bcc = null;
		$toEmail = $from;

		return EmailService::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml);
	}
	
	
	function addHotelAdmin($email,$hotelName,$hotelId){
		$userObj = UserService::getUserByEmail($email);
		if(!isset($userObj->id)){
			//prepare user object
			$userdata = array(); // place user data in an array for storing.
			$userdata['name'] = $hotelName;
			$userdata['email'] = $email;
			$userdata['username'] = $email;
			
			//set password
			$userdata['password'] = UserService::generatePassword( $reservationDetails->reservationData->userData->email, true );
			$userdata['password2'] = $userdata['password'];
			
			//create the user
			$userId = UserService::createJoomlaUser($userdata);
			
			$groupId = UserService::getJoomlaGroupIdByName("Hotel Manager");
			if(!empty($groupId))
				JUserHelper::addUserToGroup($userId, $groupId);
			
			if(!is_numeric($userId))
				JError::raiseWarning('', JText::_($userId)); // something went wrong!!
			else{ 
				$row = $this->getTable('UserHotelMapping');
				$groupTable = $this->getTable('UserGroupMapping');
				
				//assign created hotel 
				$row->createHotelMapping($userId,$hotelId);
				$groupTable->assignToDefaultAccessGroup($userId); //assign to the default access group 
			}
		}
		
	}
	
}