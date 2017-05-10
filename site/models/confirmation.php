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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
jimport('joomla.user.helper');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JHotelReservationModelConfirmation extends JModelLegacy
{
	function __construct()
	{
		$this->log = Logger::getInstance();
		parent::__construct();
	}

	function getReservation($reservationId=null){
		if(!isset($reservationId))
			$reservationId = JRequest::getInt("reservationId");
		
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId);
		
		return $reservationDetails;
	}
	
	function sendConfirmedPaymentEmail(){
		
	}
	
	function sendGuestList(){
		$reservationService = new ReservationService();
		$startDate = date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 day'));
		$endDate = date('Y-m-d', strtotime($startDate . ' + 1 day'));
	 	$confirmationTable = $this->getTable('Confirmations');
		$reservations = $confirmationTable->getReservationList($startDate, $startDate);
		
		$guestDetailsList="<tr style='text-align: left;'><th>".JText::_("LNG_NAME")."</th><th>".JText::_("LNG_ARRIVAL")."</th><th>".JText::_("LNG_DEPARTURE")."</th><th>".JText::_("LNG_ADULTS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_ROOMS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_OFFERS")."</th></tr>";
		
		if(count($reservations)>0){
			$hotelId = $reservations[0]->hotel_id;
		
			$guestDetailsList.="<tr>";
			$guestDetailsList.="<td>".$reservations[0]->last_name.' '.$reservations[0]->first_name."&nbsp;&nbsp;&nbsp;</td><td nowrap='nowrap'>".JHotelUtil::convertToFormat($reservations[0]->start_date).
			" &nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservations[0]->end_date)."&nbsp;&nbsp;&nbsp;</td><td>".$reservations[0]->adults."&nbsp;&nbsp;&nbsp;</td><td>".$reservations[0]->number_rooms."&nbsp;&nbsp;&nbsp;</td><td>".$reservations[0]->offer_names."&nbsp;&nbsp;&nbsp;</td>";
			$guestDetailsList.="</tr>";
			foreach($reservations as $reservation){
				if($hotelId != $reservation->hotel_id || next($reservations)===false){
					$guestDetailsList.="<tr>";
					$guestDetailsList.="<td>".$reservation->last_name.' '.$reservation->first_name."&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->start_date).
					"&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->end_date)."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->adults."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->number_rooms."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->offer_names."&nbsp;&nbsp;&nbsp;</td>";
					$guestDetailsList.="</tr>";
					$guestDetailsList = "<table style='text-align:left'>".$guestDetailsList."</table>";
					EmailService::sendGuestListEmail($reservation->hotel_id, $reservation->hotel_name,$reservation->hotel_email, $guestDetailsList, $startDate);
					$guestDetailsList="<tr style='text-align: left;'><th>".JText::_("LNG_NAME")."</th><th>".JText::_("LNG_ARRIVAL")."</th><th>".JText::_("LNG_DEPARTURE")."</th><th>".JText::_("LNG_ADULTS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_ROOMS")."&nbsp;&nbsp;&nbsp;</th><th>".JText::_("LNG_OFFERS")."</th></tr>";
					$hotelId = $reservation->hotel_id;
					break;
				}
				
				$guestDetailsList.="<tr>";
				$guestDetailsList.="<td>".$reservation->last_name.' '.$reservation->first_name."&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->start_date).
									"&nbsp;&nbsp;&nbsp;</td><td  nowrap='nowrap'>".JHotelUtil::convertToFormat($reservation->end_date)."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->adults."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->number_rooms."&nbsp;&nbsp;&nbsp;</td><td>".$reservation->offer_names."&nbsp;&nbsp;&nbsp;</td>";
				$guestDetailsList.="</tr>";
				
			}
		}
	}

	function sendConfirmationEmail($reservationDetails){
		
		EmailService::sendConfirmationEmail($reservationDetails);
	}
	
	function saveConfirmation($reservationDetails){
		try{
			if(count($reservationDetails->roomNotAvailable)>0){
				foreach($reservationDetails->roomNotAvailable as $room){
					$this->setError($room->room_name." ".JText::_("LNG_ROOM_NOT_AVAILABLE")." ".$reservationDetails->reservationData->userData->start_date." ".JText::_("LNG_HOTEL_AND")." ".$reservationDetails->reservationData->userData->end_date);
				}
				return -1;
			}

			$startDate = $reservationDetails->reservationData->userData->start_date;
			$endDate =  $reservationDetails->reservationData->userData->end_date;
				
			$reservationId = $this->storeConfirmation($reservationDetails);
			$this->deleteReservaitonRooms($reservationId);
			
				
			
			foreach($reservationDetails->rooms as $room){
				$confirmationRoomId = $this->storeConfirmationRooms($reservationId, $room);
				$this->storeConfirmationRoomPrices($confirmationRoomId, $room, $startDate, $endDate);
			}
				
			$this->deleteReservationExtraOptions($reservationId);
			if(isset($reservationDetails->reservationData->userData->extraOptionIds) && is_array($reservationDetails->reservationData->userData->extraOptionIds) /*|| isset($reservationDetails->reservationData->userData->airport_transfer_type_id) && isset($reservationDetails->reservationData->userData->airline_id)*/){
				
				foreach($reservationDetails->reservationData->userData->extraOptionIds as $extraOptionId){
					$extraOption = ExtraOptionsService::getExtraOption($reservationDetails->extraOptions, $extraOptionId);
					$this->storeConfirmationExtraOptions($reservationId, $extraOption);
				}
			}
			if(isset( $reservationDetails->reservationData->userData->guestDetails)){
				$this->deleteGuestDetails($reservationId);
				$this->storeConfirmationGuestDetails($reservationId, $reservationDetails->reservationData->userData->guestDetails);
			}
			
		
			if(!empty($reservationDetails->excursions)){
				foreach($reservationDetails->excursions as $excursion){
					$confirmationExcursionId = $this->storeConfirmationExcursions($reservationId,$excursion);
					$this->storeConfirmationExcursionPrices($confirmationExcursionId,$excursion,$excursion->start_date, $excursion->end_date);
				}
			}

			if(!empty($reservationDetails->reservationData->userData->airportTransfers)) {
				$this->deleteConfirmationAirportTransfer($reservationId);
                //get the last session array of airport transfer type data
                foreach($reservationDetails->reservationData->userData->airportTransfers as $airportTransfer) {
                    //$airportTransfer = AirportTransferService::getLastIndex($reservationDetails->reservationData->userData->airportTransfers);
                    //save airport transfer confirmation
                    $this->storeConfirmationAirportTransfer($reservationId, $airportTransfer);
                }
            }

			if(!empty($reservationDetails->reservationData->userData->discount_code)){
				$discountCodes = $reservationDetails->reservationData->userData->discount_code;
				$selectedDiscounts = ReservationService::getReservationDiscounts( (int) $reservationDetails->reservationData->hotel->hotel_id, $reservationDetails->reservationData->userData->reservedItems, $discountCodes, $startDate, $endDate, null, true );
				$this->storeConfirmationDiscounts( $selectedDiscounts, $reservationId );
			}

            //do not create user if admin and user registration is not allowed 
			if(!isSuperUser(JFactory::getUser()->id) && JComponentHelper::getParams('com_users')->get('allowUserRegistration'))
				$this->addUser($reservationDetails,$reservationId);
			
			//exit;
		}catch( Exception $ex ){
			JError::raiseWarning( 500, $ex->getMessage() );
			return false;
		}
		//exit;
		return $reservationId;
	}
	

	function storeConfirmation($reservationDetails){


		$rowTable						= 	$this->getTable('Confirmations');
		$obj = new stdClass();
		$obj->confirmation_id			=	$reservationDetails->reservationData->userData->confirmation_id;
		$obj->hotel_id					=	$reservationDetails->reservationData->userData->hotelId;
		$obj->start_date				= 	$reservationDetails->reservationData->userData->start_date;
		$obj->end_date					= 	$reservationDetails->reservationData->userData->end_date;
		$obj->adults					= 	$reservationDetails->reservationData->userData->adults;
		$obj->children					= 	$reservationDetails->reservationData->userData->children;
		$obj->rooms						= 	$reservationDetails->reservationData->userData->rooms;
		if(isset($reservationDetails->reservationData->userData->coupon_code))
			$obj->coupon_code				=	$reservationDetails->reservationData->userData->coupon_code;
		$obj->guest_type				=	$reservationDetails->reservationData->userData->guest_type;
		$obj->first_name				=	$reservationDetails->reservationData->userData->first_name;
		$obj->last_name					=	$reservationDetails->reservationData->userData->last_name;
		$obj->remarks					=	$reservationDetails->reservationData->userData->remarks;
		$obj->remarks_admin				=	$reservationDetails->reservationData->userData->remarks_admin;
		$obj->arrival_time				=	$reservationDetails->reservationData->userData->arrival_time;
		$obj->address					=	$reservationDetails->reservationData->userData->address;
		$obj->postal_code				=	$reservationDetails->reservationData->userData->postal_code;
		$obj->city						=	$reservationDetails->reservationData->userData->city;
		$obj->state_name				=	$reservationDetails->reservationData->userData->state_name;
		$obj->country					=	$reservationDetails->reservationData->userData->country;
		$obj->phone						=	$reservationDetails->reservationData->userData->phone;
		$obj->email						=	$reservationDetails->reservationData->userData->email;
		if (!JFactory::getApplication()->isAdmin())
			$obj->user_id					=	JFactory::getUser()->id;
		
		if(isset($reservationDetails->reservationData->userData->conf_email))
			$obj->conf_email				=	$reservationDetails->reservationData->userData->conf_email;
		$obj->confirmation_details		= 	$reservationDetails->reservationInfo;
		if(empty($obj->confirmation_id))
			$obj->reservation_status		=	RESERVED_ID;
		$obj->total						= 	$reservationDetails->total;
		$obj->total_cost				= 	$reservationDetails->cost;
		if(isset($reservationDetails->reservationData->userData->media_referer))
			$obj->media_referer				= 	$reservationDetails->reservationData->userData->media_referer;
		if(isset($reservationDetails->reservationData->userData->voucher))
			$obj->voucher					= 	$reservationDetails->reservationData->userData->voucher;
		if(isset($reservationDetails->reservationData->userData->company_name))
			$obj->company_name				= 	$reservationDetails->reservationData->userData->company_name;
		if(isset($reservationDetails->reservationData->userData->discount_code))
			$obj->discount_code				= 	$reservationDetails->reservationData->userData->discount_code;

        $obj->language_tag                  =  JHotelUtil::getLanguageTag();

        if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		
		 if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		return $rowTable->confirmation_id;
	}

	function storeConfirmationRooms($confirmationId, $room){
		$rowTable						= 	 $this->getTable('ConfirmationsRooms');
		$obj = new stdClass();
		$obj->confirmation_id	=	$confirmationId;
		$obj->hotel_id			=	$room->hotel_id;
		$obj->offer_id			=	$room->offer_id;
		$obj->room_id			=	$room->room_id;
		$obj->current			=	$room->current;
		$obj->room_name			=	$room->room_name;
		$obj->adults			= 	$room->adults;
		$obj->children			= 	$room->children;
		//dmp($obj);
		//exit;
		if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		//dmp($obj);
		
		if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		
		return $rowTable->confirmation_room_id;
	}
	
	function deleteReservaitonRooms($reservationId){
		$rowTable = 	 $this->getTable('ConfirmationsRooms');
		$rowTable->deleteRooms($reservationId);
	}
	
	function deleteReservationExtraOptions($reservationId){
		$rowTable = $this->getTable('ConfirmationsExtraOptions');
		$rowTable->deleteExtraOptions($reservationId);
	}
	
	function storeConfirmationRoomPrices($confirmationRoomId, $room, $startDate, $endDate){
		
		//dmp($room);
		//dmp($startDate);
		//dmp($endDate);
		for( $d = strtotime($startDate);$d < strtotime($endDate); ){
			$dayString = date( 'Y-m-d', $d);
			
			$rowTable						= 	 $this->getTable('ConfirmationsRoomPrices');
			$obj = new stdClass();
			$obj->confirmation_room_id		=	$confirmationRoomId;
			$obj->current					=	$room->current;
			$obj->date						=	$dayString;
			$obj->price						= 	$room->daily[$dayString]['price_final'];
			
			if(isset ($room->customPrices) && isset($room->customPrices[$dayString])){
				$obj->price = $room->customPrices[$dayString];
			}
			
			//dmp($obj->price);
			
			if (!$rowTable->bind($obj)){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
			
			if (!$rowTable->check()){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
				
			if (!$rowTable->store()){
			 throw( new Exception($this->_db->getErrorMsg()) );
			} 
			
			//dmp($obj);
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
	}

	function storeConfirmationExtraOptions($confirmationId, $extraOptions){
		$rowTable =  $this->getTable('ConfirmationsExtraOptions');
		
		$obj = new stdClass();
		$obj->confirmation_id			=	$confirmationId;
		$obj->hotel_id					=	$extraOptions->hotel_id;
		$obj->offer_id					=	$extraOptions->offerId;
		$obj->room_id					=	$extraOptions->roomId;
		$obj->current					=	$extraOptions->current;
		$obj->extra_option_id			=	$extraOptions->id;
		$obj->extra_option_name			=	$extraOptions->name;
		$obj->extra_option_price		=	$extraOptions->price;
		$obj->extra_option_price_type	=	$extraOptions->price_type;
		$obj->extra_option_is_per_day	=	$extraOptions->is_per_day;
		$obj->extra_option_mandatory	=	$extraOptions->mandatory;
		$obj->extra_option_persons		=	$extraOptions->nrPersons;
		$obj->extra_option_days			=	$extraOptions->nrDays;
        $obj->extra_option_dates        =   $extraOptions->dates;
		$obj->extra_option_multiplier	=	$extraOptions->nrMultiplier;
		if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		
		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	}
	
	function storeConfirmationExcursions($confirmationId, $excursion){
		$rowTable =  $this->getTable('ConfirmationsExcursions');
		$obj = new stdClass();
		$obj->confirmation_id			=	$confirmationId;
		$obj->hotel_id					=	$excursion->hotel_id;
		$obj->excursion_id				=	$excursion->excursion_id;
		$obj->excursion_name			=	$excursion->excursion_name;
		$obj->nr_booked					=	$excursion->nrItemsBooked;
		
	
		if (!$rowTable->bind($obj)){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	
	
		if (!$rowTable->check()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	
		//dmp($obj);
		if (!$rowTable->store()){
			throw( new Exception($this->_db->getErrorMsg()) );
		}
		return $rowTable->confirmation_excursion_id; 
		//dmp("OK");
	}
	
	function storeConfirmationExcursionPrices($confirmationExcursionId, $excursion, $startDate, $endDate){
	
		for( $d = strtotime($startDate);$d < strtotime($endDate); ){
			$dayString = date( 'Y-m-d', $d);
			$rowTable						= 	 $this->getTable('ConfirmationsExcursionsPrices');
			$obj = new stdClass();
			$obj->confirmation_excursion_id	=	$confirmationExcursionId;
			$obj->date						=	$dayString;
			$obj->price						= 	$excursion->daily[$dayString]['price_final'];
				
			if(isset ($excursion->customPrices) && isset($excursion->customPrices[$dayString])){
				$obj->price = $excursion->customPrices[$dayString];
			}
				
			if (!$rowTable->bind($obj)){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
				
			if (!$rowTable->check()){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
	
			if (!$rowTable->store()){
				throw( new Exception($this->_db->getErrorMsg()) );
			}
				
			//dmp($obj);
			$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
		}
	}

	function storeConfirmationDiscounts($selectedDiscounts,$reservation_id){

		if(isset($selectedDiscounts->discounts)){
			foreach ($selectedDiscounts->discounts as $discount){
				$rowTable						= 	 $this->getTable('ConfirmationsDiscounts');
				$obj = new stdClass();
				$obj->reservation_id        	=	$reservation_id;
				$obj->discount_id				=	$discount->discount_id;
				$obj->discount_code				= 	$discount->code;
				$obj->name						= 	$discount->discount_name;
				$obj->value						= 	$discount->discount_value;
				$obj->is_percent				= 	$discount->percent;


				if (!$rowTable->bind($obj)){
					throw( new Exception($this->_db->getErrorMsg()) );
				}

				if (!$rowTable->check()){
					throw( new Exception($this->_db->getErrorMsg()) );
				}

				if (!$rowTable->store()){
					throw( new Exception($this->_db->getErrorMsg()) );
				}
			}
		}
	}

	
	function deleteGuestDetails($confirmationId){
		$db =JFactory::getDBO();
		$query="delete from #__hotelreservation_confirmations_guests where confirmation_id=".$confirmationId;
		$db->setQuery($query);

		return $db->query();
	}
	
	function storeConfirmationGuestDetails($confirmationId, $guestDetails){
		
		foreach($guestDetails as $guestDetail){
			$guestDetail->confirmation_id = $confirmationId;
			$rowTable	= 	 $this->getTable('ConfirmationsGuests');

			if (!$rowTable->bind($guestDetail))
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}

			if (!$rowTable->check())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}

			if (!$rowTable->store())
			{
				throw( new Exception($this->_db->getErrorMsg()) );
			}
		}
	}
		
	function storeConfirmationTaxes($confirmationId){
		$rowTable						= 	 $this->getTable('ConfirmationsTaxes');
		$rowTableManage					= 	 $this->getTable( "Taxes" );
			
		$rowTableManage->load( $obj_ids[$j]->tax_id );

		if( $rowTableManage->tax_id +0== 0 ){}

		$obj->confirmation_id			=	$confirmationId;
		$obj->tax_id					=	$rowTableManage->tax_id;
		$obj->tax_name					=	$rowTableManage->tax_name;
		$obj->hotel_id					=	$data->hotel_id;
		$obj->tax_type					=	$rowTableManage->tax_type;
		$obj->tax_value					=	$rowTableManage->tax_value;

		if (!$rowTable->bind($obj))
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->check())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}

		if (!$rowTable->store())
		{
			throw( new Exception($this->_db->getErrorMsg()) );
		}
	}


    function deleteConfirmationAirportTransfer($reservationId){
        $rowTable = $this->getTable('ConfirmationsRoomsAirportTransfer');
        $rowTable->deleteConfirmationAirportTransfer($reservationId);
    }



	function storeConfirmationAirportTransfer($confirmationId,$airportTransfer){
		try{
			$airportData = AirportTransferService::parseUserDataAirportTransfer($airportTransfer);
            //save the data only if airport transfer type is selected in its layout by the user
			if($airportData->airport_transfer_type_id > 0 && strlen($airportData->airline_name)>0) {
                $rowTable = $this->getTable('ConfirmationsRoomsAirportTransfer');
                $airportTransfer = AirportTransferService::getAirportTransferType($airportData->airport_transfer_type_id);

                $obj = new stdClass();
                $obj->confirmation_id = $confirmationId;
                $obj->room_id = $airportData->room_id;
                $obj->current = $airportData->current;
                $obj->airport_transfer_type_id = $airportData->airport_transfer_type_id;
                $obj->airport_transfer_type_name = $airportTransfer->airport_transfer_type_name;
                $obj->airport_transfer_type_price = (float)$airportTransfer->airport_transfer_type_price;
                $obj->airport_transfer_type_vat = (float)$airportTransfer->airport_transfer_type_vat;
                $obj->airline_id = $airportData->airline_id;
                $obj->airline_name = $airportData->airline_name;

                $obj->airport_transfer_flight_nr = $airportData->airport_transfer_flight_nr;
                $obj->airport_transfer_date = JHotelUtil::convertToMysqlFormat($airportData->airport_transfer_date);
                $obj->airport_transfer_time_hour = $airportData->airport_transfer_time_hour;
                $obj->airport_transfer_time_min = $airportData->airport_transfer_time_min;
                $obj->airport_transfer_guest = $airportData->airport_transfer_guest;
                $obj->included_offer = $airportData->included_offer;

                if (!$rowTable->bind($obj)) {
                    throw(new Exception($this->_db->getErrorMsg()));
                }

                if (!$rowTable->check()) {
                    throw(new Exception($this->_db->getErrorMsg()));
                }

                if (!$rowTable->store()) {
                    throw(new Exception($this->_db->getErrorMsg()));
                }

                return $rowTable->confirmation_airport_transfer_id;
            }

        }catch (Exception $ex){
           JError::raiseWarning( 500, $ex->getMessage() );
            return false;
        }
    }


	function addUser($reservationDetails,$reservationId){
		$user = JFactory::getUser();
		if(!$user->id || $user->guest==1){
			$userObj = UserService::getUserByEmail($reservationDetails->reservationData->userData->email);
			if(isset($userObj->id))
				$userId = $userObj->id;
			else
				$userId = $this->addJoomlaUser($reservationDetails);
		}
		else
			$userId = $user->id;
		
	}
	
	function addJoomlaUser($reservationDetails){

		//prepare user object
		$userdata = array(); // place user data in an array for storing.
		$userdata['name'] = $reservationDetails->reservationData->userData->last_name.' '.$reservationDetails->reservationData->userData->first_name; ;
		$userdata['email'] = $reservationDetails->reservationData->userData->email;
		$userdata['username'] = $reservationDetails->reservationData->userData->email;

		//set password
		$userdata['password'] = UserService::generatePassword( $reservationDetails->reservationData->userData->email, true );
		$userdata['password2'] = $userdata['password'];
		
		//create the user
		$userId = UserService::createJoomlaUser($userdata);
		
		if(!is_numeric($userId))
			JError::raiseWarning('', JText::_($userId)); // something went wrong!!
			
		return $userId;
	}

}