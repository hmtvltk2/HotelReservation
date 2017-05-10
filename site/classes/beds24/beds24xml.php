<?php
/**
 * 
 * Description:
 * Copyright: Copyright (c) 2005 - 2012
 * Company: CMSJunkie
 * @author
 * @version 1.0
 */
JModelLegacy::addIncludePath(JPATH_SITE.'/administrator/components/com_jhotelreservation/models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'roomrateprices.php');
require_once(JPATH_COMPONENT.DS.'models'.DS.'beds24.php');


class Beds24Xml{
	
	protected $availRequestURL; 
	protected $resRequestURL;
	
	protected $log;
	
	/**
	 * Constructs and initalize an CublisXml object
	 */
	public function __construct() {
		$this->log = Logger::getInstance();
		$this->availRequestURL = "https://api.beds24.com/ota/OTA_HotelAvail";
		$this->resRequestURL = "https://api.beds24.com/ota/OTA_HotelRes";
		
	}
	
	
	public function getRoomsAvailability(){
		$startDate = JRequest::getVar("startDate",date('Y-m-d'));
		$endDate = JRequest::getVar("endDate",JHotelUtil::shiftDate($startDate,1));
		$this->log->LogDebug("Beds24 retrieve availability:".$startDate."-".$endDate);
		
		$hotels = HotelService::getHotelsBeds24();
		foreach($hotels as $hotel){
			$hotelId = $hotel->hotel_id;
			$rooms = HotelService::getHotelRooms($hotelId, $startDate, $endDate, array(), 2);
			foreach ($rooms as $room){
				if(!empty($room->beds24_room_id)){
					$this->log->LogDebug("Beds24 retrieve room:".$room->beds24_room_id);
					$otaRequest = $this->generateOTARequest($room->beds24_room_id,$startDate, $endDate);
					$result = $this->sendRequest($otaRequest,$this->availRequestURL,$hotel->user, $hotel->password);
					$this->processOTA_HotelAvailRS($result);
				}
			}
		}
	}
	
	function generateOTARequest($roomid,$start, $end){

		$echotoken = time();
		
		$xml = '';
		$xml .= '<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="'.$echotoken.'" Version="1.0">
				  <AvailRequestSegments>
				    <AvailRequestSegment>
				      <StayDateRange Start="'.$start.'" End="'.$end.'" />
				      <RoomStayCandidates>
				        <RoomStayCandidate RoomTypeCode="'.$roomid.'" />
				      </RoomStayCandidates>
				    </AvailRequestSegment>
				  </AvailRequestSegments>
				</OTA_HotelAvailRQ>';
		return $xml; 
	}
	
	function sendRequest($xml,$url,$username, $password){
		
		//send request to beds24 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$return = curl_exec($ch);
		curl_close($ch);
		
		return $return;
	}
	
	function processOTA_HotelAvailRS($result){
		$roomRates = $this->parseOTA_HotelAvailRS($result);
		$model = JModelLegacy::getInstance( 'RoomRatePrices', 'JHotelReservationModel' );
		$this->log->LogDebug("Beds24 rates to save :".serialize($roomRates));
		
		$messages = $model->checkRateData($roomRates);
		if(count($messages)==0)
			$messages = $model->saveCustomDates($roomRates);
		if(empty($messages))
			echo "Rates retrieved and saved successfully";
		else dmp($messages);
		
		exit;
	}
	
	function parseOTA_HotelAvailRS($request){
	
		$xml = simplexml_load_string($request);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
	
		$allRates = array();
		$ratesMapping = array();
		$roomStaysArray = $array["RoomStays"]["RoomStay"];
		foreach ($roomStaysArray as $roomStay){
			$rateInfo =  new stdClass();
			$ratePlanId = "";
			$nrRoomsAvailable = "";

			$startDate = $roomStay["TimeSpan"]["@attributes"]["Start"];
			$endDate = $roomStay["TimeSpan"]["@attributes"]["End"];
			
			if(!empty($roomStay["RoomTypes"])){
				$nrRoomsAvailable = $roomStay["RoomTypes"][0]["@attributes"]["NumberOfUnits"];
				$roomId = $roomStay["RoomTypes"][0]["@attributes"]["RoomTypeCode"];
				$ratesMapping[$startDate][$roomId] = $nrRoomsAvailable; 
			}
			
			if(!empty($roomStay["RoomRates"])){
				foreach($roomStay["RoomRates"] as $roomRate){
					$roomId = $roomRate["@attributes"]["RoomTypeCode"];
					$ratePlanId = $roomRate["@attributes"]["RatePlanCode"];
						
					foreach ($roomRate["Rates"] as $rate){
						$rateInfo->maxGuests = $rate["@attributes"]["MaxGuestApplicable"];
						$rateInfo->minDays = $rate["@attributes"]["MinLOS"];
						$rateInfo->rate = $rate["Base"]["@attributes"]["AmountAfterTax"];
						$rateInfo->currency = $rate["Base"]["@attributes"]["CurrencyCode"];
						
					}
				}
				$rateInfo->rateId = $ratePlanId;
				$rateInfo->roomId = RoomService::getBeds24Room($roomId);
				$rateInfo->startDate = $startDate;
				$rateInfo->single_use ="";
				$rateInfo->maxDays = "";
				$rateInfo->lockForArrival = "";
				$rateInfo->lockForDeparture  = "";
				$rateInfo->endDate = $endDate;
				$rateInfo->nrRoomsAvailable = isset($ratesMapping[$startDate][$roomId])?$ratesMapping[$startDate][$roomId]:"";
					
				$allRates[] = $rateInfo;
			}
			

		}
	
		return $allRates;
	}
	
	public function sendReservations(){
		$this->log->LogDebug("OTA_HotelResRQ");
		$model = JModelLegacy::getInstance( 'Beds24', 'JHotelReservationModel' );

		$hotels = HotelService::getHotelsBeds24();
		foreach($hotels as $hotel){
			$hotelId = $hotel->hotel_id;
			$reservations = $model->getNewReservations($hotelId);
			$otaRequest = $this->generateOTA_HotelResRQ($reservations,$hotel->user);
			dmp($otaRequest);
			$result = $this->sendRequest($otaRequest,$this->resRequestURL,$hotel->user, $hotel->password);
			//$model->setReservationBeds24Status($reservations);
			dmp($result);
		}
		exit;
	
	}
	
	
	
	function generateOTA_HotelResRQ($reservations,$hotelCode){
		$echotoken = time();		
		$xml = '<OTA_HotelResRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="'.$echotoken.'" ResStatus="Commit" Version="1.0"></OTA_HotelResRQ>';
		$sxe = new SimpleXMLElement($xml);

			if(!empty($reservations)){
				$hotelReservations = $sxe->addChild('HotelReservations');
				foreach($reservations as $reservation){
					$hotelReservation = $hotelReservations->addChild('HotelReservation');
					$hotelReservation ->addAttribute("CreatorID",  $reservation->confirmation_id);
					
					if(!empty($reservation->rooms)){
					$roomStays = $hotelReservation->addChild('RoomStays');
						foreach($reservation->rooms as $index=>$room){
							$roomStay = $roomStays->addChild('RoomStay');
							$roomStay->addAttribute("IndexNumber", $index+1);
							
							$roomTypes = $roomStay->addChild('RoomTypes');
							$roomType = $roomTypes->addChild('RoomType');
							$roomType->addAttribute("RoomTypeCode", $room->beds24_room_id);
							$roomType->addAttribute("NumberOfUnits",1);
										
							$roomRates = $roomStay->addChild('RoomRates');
							$roomRate = $roomRates->addChild('RoomRate');
							//$roomRate->addAttribute("BookingCode", "abx");
							
							$rates = $roomRate->addChild('Rates');
							$rate = $rates->addChild('Rate');
							$total = $rate->addChild('Total');
							$total->addAttribute("AmountAfterTax", $reservation->total);
							$total->addAttribute("AmountBeforeTax", $reservation->total);
							
							$guestCounts = $roomStay->addChild('GuestCounts');
							$guestCount = $guestCounts->addChild('GuestCount');
							$guestCount ->addAttribute("AgeQualifyingCode", "1");
							$guestCount ->addAttribute("Count", $room->adults);
							
							$timeSpan = $roomStay->addChild('TimeSpan');
							$timeSpan ->addAttribute("Start", $reservation->start_date);
							$timeSpan ->addAttribute("End", $reservation->end_date);
							
							$guarantee = $roomStay->addChild('Guarantee');
							$guaranteesAccepted = $guarantee->addChild('GuaranteesAccepted');
							$guaranteeAccepted = $guaranteesAccepted->addChild('GuaranteeAccepted');
							switch ($reservation->processor_type){
								case "authorize":
								case "offlinecreditcard":
									$paymentCard = $guaranteeAccepted->addChild('PaymentCard');
									$paymentCard->addAttribute("CardCode", "VI");
									$paymentCard->addAttribute("ExpireDate", $reservation->card_expiration_month.$reservation->card_expiration_year);
									$paymentCard->addChild('CardHolderName',$reservation->card_name);
									$paymentCard->addChild('SeriesCode',$reservation->card_security_code);
									$paymentCard->addChild('CardNumber',"$reservation->card_number");
								default: 
									$cash = $guaranteeAccepted->addChild('Cash');
									$cash->addAttribute("CashIndicator", 1);
							}
							$resGuestRPHs = $roomStay->addChild('ResGuestRPHs',1);
	
							if(!empty($reservation->extra_info)){
								$specialRequests = $roomStay->addChild('SpecialRequests');
								$specialRequest = $specialRequests->addChild('SpecialRequest');
								$text = $specialRequest->addChild('Text',$reservation->extra_info);
							}
						}
					}
					$resGuests = $hotelReservation->addChild('ResGuests');
					$resGuest = $resGuests->addChild('ResGuest');
					$resGuest->addAttribute("ResGuestRPH",1);
					$resGuest->addAttribute("ArrivalTime","15:00:00");

					
					$profiles = $resGuest->addChild('Profiles');
					$profileInfo = $profiles->addChild('ProfileInfo');
					$profile = $profileInfo->addChild('Profile');
						
					$customer = $profile->addChild('Customer');
					$personName = $customer->addChild('PersonName');
					$personName->addChild('GivenName',$reservation->first_name);
					$personName->addChild('Surname',$reservation->last_name);
						
					$telephone = $customer->addChild('Telephone');
					$telephone->addAttribute("PhoneNumber", $reservation->phone);
					$email = $customer->addChild('Email', $reservation->email);
					$address = $customer->addChild("Address");
					$address->addChild("AddressLine",$reservation->address);
					$address->addChild("CityName",$reservation->city);
					$address->addChild("PostalCode",$reservation->postal_code);
					$address->addChild("CountryName",$reservation->country);
					
					$resGlobalInfo = $hotelReservation->addChild('ResGlobalInfo');
					$hotelReservationIDs = $resGlobalInfo->addChild('HotelReservationIDs');
					$hotelReservationID = $hotelReservationIDs->addChild('HotelReservationID');
					
					$hotelReservationID->addAttribute("ResID_Value", $reservation->confirmation_id);
					
					$basicPropertyInfo = $resGlobalInfo->addChild('BasicPropertyInfo');
					$basicPropertyInfo->addAttribute("HotelCode", $hotelCode);

				}
			}
				
		$result = $sxe->asXML();
		
		
		
		return $result;
	}

	
	function getHotelId($request){
		$xml = simplexml_load_string($request);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		
		$hotelId = $array["POS"]["Source"]["RequestorID"]["@attributes"]["ID"];
		return $hotelId;
		
	}

	
	
	function buildXmlErrorResponse($message){
		$xmlContent = '<?xml version="1.0" encoding="UTF-8"?><Errors></Errors>';
		$sxe = new SimpleXMLElement($xmlContent);
		
		$errors = array();
		array_push($errors, $message);
		
		foreach($errors as $error){
			$errorXML = $sxe->addChild("Error");
			$errorXML ->addAttribute("Code", "1");
			$errorXML ->addAttribute("ShortText", $error);
			$errorXML ->addAttribute("Type", "General");
		}
		
		$result = $sxe->asXML();
		
		return $result;
		
	}
	
	
}

