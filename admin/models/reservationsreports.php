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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model'); 

class JHotelReservationModelReservationsReports extends JModelLegacy
{ 
	var $dataRooms 					= null;
	var $dataRoomsConfirmations		= null;
	var $paymentProcessorsResults	= null;
	var $hotels						= null;
	function __construct()
	{
		parent::__construct();
	}
	
	function getRoomTypes()
	{
        $languageTag = JRequest::getVar('_lang');

        $translationTable = new JHotelReservationLanguageTranslations();
		// Load the data
		$query = ' SELECT * FROM #__hotelreservation_rooms WHERE is_available = 1 AND hotel_id ="'.JRequest::getString('hotel_id') .'"';
		//echo $query;
		//$this->_db->setQuery( $query );
		$res = $this->_getList( $query );

        foreach($res as $room){
            $roomTranslations = $translationTable->getObjectTranslation(ROOM_NAME,$room->room_id,$languageTag);
            $room->room_name = empty($roomTranslations->content)?$room->room_name:$roomTranslations->content;
        }
		return $res;
	}
	
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}

	function getJsonIncomeData(){
		$row = $this->getTable('confirmations');
		$dateStart = JRequest::getVar('dateStart');
		$dateEnd = JRequest::getVar('dateEnd');
		$roomTypeId = JRequest::getVar('roomTypeId');
		$hotelId = JRequest::getVar('hotelId');
		$reportType = JRequest::getVar('reportType');
		
		if(isset($dateStart) && isset($dateEnd)){
			$reportData = $row->getReservationsIncome($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd);
			$processArray = array();
				
			foreach ($reportData as $data){
				if($data->groupUnit!=null)
					array_push($processArray,array($data->groupUnit, (int)$data->reservationTotal));
			}
			if(count($reportData)==0){
                $processArray=null;
            }
			$array = array($processArray);
			return json_encode($array);
		}
		return null;
	}
	function getJsonCountriesData(){
		$row = $this->getTable('confirmations');
		$dateStart = JRequest::getVar('dateStart');
		$dateEnd = JRequest::getVar('dateEnd');
		$roomTypeId = JRequest::getVar('roomTypeId');
		$hotelId = JRequest::getVar('hotelId');
		$reportType = JRequest::getVar('reportType');
	
		if(isset($dateStart) && isset($dateEnd)){
			$reportData = $row->getReservationsCountries($reportType,$hotelId,$roomTypeId,$dateStart,$dateEnd);
			$processArray = array();
			foreach ($reportData as $data){
				if($data->country!=null && $data->country!="")
					array_push($processArray,array($data->country, (int)$data->countryCount));
			}

			if(count($reportData)==0){
                $processArray=null;
            }
			$array = array($processArray);
			return json_encode($array);
		}
		return null;
	}

	/**
	 * @return null/offer report object list  return null if no result from the database is returned, returns object list based on filter data
	 * this method is used @exportToCsv method for the user to export the data to a csv file
	 * @throws Exception
	 */
	function getOffersReportData(){

		$row = $this->getTable('confirmations');

		// user application state input data from the filter
		$app        = JFactory::getApplication();
		$input      = $app->input;

		//date interval
		$dateStart  = JRequest::getVar('filter_datas');
		$dateEnd    = JRequest::getVar('filter_datae');

		//room type represented by room id
		$roomTypeId = $input->get('filter_room_types');

		//hotel type represented by hotel id
		$hotelId    = $input->get('hotel_id');

		//reserved offer with a code (optional filter)
		$voucherCode = $input->get('voucher_code');

		//generate submit button ,used only to check if the user pressed it or not to generate the report
		$generate   = JRequest::getVar('generate');


		//language translation class and loaded language
		$languageTag = JRequest::getVar('_lang');
		$translationTable = new JHotelReservationLanguageTranslations();

		// if the user hasn't type generate or selected a room type or if it has selected those but no dates selected or no hotel id is selected
		// no data will be processed but rather it will return null otherwise the processing and getting data from the table class will be in place
		if($generate != '' || $roomTypeId != '' && (isset($dateStart) || isset($dateEnd) || isset($hotelId) || $hotelId != '')) {
			$reportData = $row->getReservationsOffers($hotelId,$roomTypeId,$dateStart,$dateEnd,$voucherCode);

			if(isset($reportData))
			{
				foreach ( $reportData as $data )
				{
					// translate offer name
					// room name is  saved as translated in confirmation table ,  no need for translation
					$offerTranslations = $translationTable->getObjectTranslation( OFFER_NAME, $data->offer_id, $languageTag );
					$data->offer_name  = empty( $offerTranslations->content ) ? $data->offer_name : $offerTranslations->content;

				}
			}
			return $reportData;

		}
		return null;
	}
	
	function getCommissionReportData(){
		$row = $this->getTable('Invoices', 'JTable');

		// user application state input data from the filter
		$app        = JFactory::getApplication();
		$input      = $app->input;

		//hotel type represented by hotel id
		$hotelId    = $input->get('hotel_id');

		//date interval
		$dateStart  = JRequest::getVar('filter_datas');
		$dateEnd    = JRequest::getVar('filter_datae');

		//room type represented by room id
		$reservationCost = $input->get('reservation_cost');

		//filter by time unit 
		$reportType = JRequest::getVar('filter_report_type');

		//generate submit button ,used only to check if the user pressed it or not to generate the report
		$generate   = JRequest::getVar('generate');
		

		// if the user hasn't type generate or selected a room type or if it has selected those but no dates selected or no hotel id is selected
		// no data will be processed but rather it will return null otherwise the processing and getting data from the table class will be in place
		if($generate != '' || (isset($dateStart) || isset($dateEnd) || isset($hotelId) || $hotelId != '')) {
			$reportData = $row->getReservationCommissionData($hotelId,$reservationCost,$dateStart,$dateEnd,$reportType);

			return $reportData;

		}
		return null;
	}
	
	function getJsonReservationData(){
		$row = $this->getTable('confirmations');
		$dayLag = JRequest::getVar('daysLag');
		$reportType = JRequest::getVar('reportType');
		
		switch($dayLag){
			case 7:
			case 30:
				$reportType = "DAY";
				break;
			case 90:
			case 180:
			case 365:
				$reportType = "MONTH";
				break;
			case 730:
			case 1095:
				$reportType = "YEAR";
				break;
			default: 
				$reportType = "DAY";
		}
	
		if(isset($dayLag)){
			$reportData = $row->getReservationsReport($reportType,$dayLag);
			$processArray = array();
			foreach ($reportData as $data){
				array_push($processArray,array($data->groupUnit, (int)$data->reservationTotal));
			}
			if(count($reportData)==0){
				$processArray=null;
			}
			$array = array($processArray);
			return json_encode($array);
		}
		return null;
	}

	/**
	 * @throws Exception
	 */
	public function exportToCsv(){

		$offers = $this->getOffersReportData();
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$appDelimiter = $appSettings->delimiter;
		$csv_output = "id".$appDelimiter."offer name".$appDelimiter."room_type".$appDelimiter."hotel".$appDelimiter."bookings".$appDelimiter."persons".$appDelimiter."voucher".$appDelimiter."reservation amount".$appDelimiter."reservation amount paid".$appDelimiter."start date".$appDelimiter."end date"."\n";

		$app        = JFactory::getApplication();
		$input      = $app->input;
		//input field value that user searches for an offer with voucher code
		$voucherCode = $input->get('voucher_code');

		//get the hotels
		$hotels		= $this->getHotels();
		//check if the user doing the export has ownership over the hotels
		$hotels = checkHotels(JFactory::getUser()->id,$hotels);
		//object list used to loop through and check for the hotel id that one offer belongs to
		$offerHotels =  $hotels;

		//start date and end date selected by the user in the dates filter
		$startDate = JRequest::getVar('filter_datas');
		$EndDate = JRequest::getVar('filter_datae');

		foreach($offers as $item){
			$item->hotel_name = '';
			//getting the hotel name
			foreach ($offerHotels as $hotel){
				if($hotel->hotel_id == $item->hotel_id){
					$item->hotel_name = $hotel->hotel_name;
				}
			}


			$csv_output .= "\"$item->offer_id\"".$appDelimiter."\"$item->offer_name\"".$appDelimiter."\"$item->room_name\"".$appDelimiter."\"$item->hotel_name\"".$appDelimiter."\"$item->nrBookings\"".$appDelimiter."\"$item->persons\"".$appDelimiter."\"$voucherCode\"".$appDelimiter."\"$item->reservation_amount$item->currency_symbol\"".$appDelimiter."\"$item->amount_paid$item->currency_symbol\"".$appDelimiter."\"$startDate\"".$appDelimiter."\"$EndDate\"";
			$csv_output .= "\n";

		}
		ob_clean();
		//set the headers for export
		$fileName = "jhotel_reservations_offer_report";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$fileName.".csv");
		print $csv_output;
	}
	
	function exportCommissionIncomeToCsv(){
		$commissionData = $this->getCommissionReportData();

		$app        = JFactory::getApplication();
		$input      = $app->input;

		$hotelId    = $input->get('hotel_id');

		//dynamic label set value if one hotel is selected otherwise empty
		$hotelName = $hotelId > 0 ? 'hotel name':'';



		$reportType = JRequest::getVar('filter_report_type');

		//type of report monthly or yearly
		$groupUnitLabel = $reportType=="MONTH"?JText::_('LNG_REPORT_MONTH',true):JText::_('LNG_REPORT_YEAR',true);

		//get the hotels
		$hotels		= $this->getHotels();
		//check if the user doing the export has ownership over the hotels
		$hotels = checkHotels(JFactory::getUser()->id,$hotels);
		//object list used to loop through and check for the hotel id that one offer belongs to
		$invoicesHotel =  $hotels;

		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$appDelimiter = $appSettings->delimiter;

		$allHotels = $hotelId == -1 ?'Hotel names'.$appDelimiter:'';

		$csv_output = $allHotels."$groupUnitLabel".$appDelimiter."commission".$appDelimiter."reservation costs".$appDelimiter."total amount".$appDelimiter."start date".$appDelimiter."end date".$appDelimiter."$hotelName"."\n";

		//start date and end date selected by the user in the dates filter
		$startDate = JRequest::getVar('filter_datas');
		$EndDate = JRequest::getVar('filter_datae');

		foreach($commissionData as $item)
		{
			if(isset($item->reservationCost)) {
				$item->totalAmount +=$item->reservationCost;
				$item->reservationCost = number_format($item->reservationCost,2, '.', '');
			}
			$item->totalAmount      =  number_format((float)$item->totalAmount, 2, '.', '');
			$item->commissionAmount =  number_format((float)$item->commissionAmount, 2, '.', '');

			$AllHotelNames = isset($item->hotelNames)&&!empty($item->hotelNames) && $hotelId == -1? $item->hotelNames.$appDelimiter : '';

			$item->groupUnit  = $reportType=="MONTH"?date('F Y',strtotime($item->groupUnit)):$item->groupUnit;

			if($hotelId > 0 )
			{
				$item->hotel_name = '';
				//getting the hotel name
				foreach ( $invoicesHotel as $hotel )
				{
					if ( $hotel->hotel_id == $item->hotelId )
					{
						$item->hotel_name = $hotel->hotel_name;
					}
				}
			}
			$csv_output .= $AllHotelNames."\"$item->groupUnit\"".$appDelimiter."\"$item->commissionAmount\"".$appDelimiter."\"$item->reservationCost\"".$appDelimiter."\"$item->totalAmount\"".$appDelimiter."\"$startDate\"".$appDelimiter."\"$EndDate\"".$appDelimiter."\"$item->hotel_name\"";
			$csv_output .= "\n";

		}
		ob_clean();
		//set the headers for export
		$fileName = "jhotel_reservations_commission_report";
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header( "Content-disposition: filename=".$fileName.".csv");
		print $csv_output;
	}
	
}
