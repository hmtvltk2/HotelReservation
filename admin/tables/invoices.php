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

class JTableInvoices extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function JTableInvoices(& $db) {

		parent::__construct('#__hotelreservation_invoices', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	/**
	 * Method to build a sql query to get the Invoice datas based on its item Id:
	 * @param $invoiceId
	 * @return mixed Returns the object datas
	 */
	function getInvoice($invoiceId){
		$db =JFactory::getDBO();

		$query = $db->getQuery(true);
		//Get All fields from the table
		$query->select('hi.*');
		$query->from($db->quoteName('#__hotelreservation_invoices'). ' AS hi');

		if(is_numeric($invoiceId)) {
			$query->where('hi.id=' . (int)$invoiceId);
		}
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to build a sql query to get the List Object datas of all invoices that belond
	 * to one hotel based on its hotelId:
	 * @param $hotelId
	 * @return mixed returns the List Object of All Invoices
	 */
	function getHotelInvoices($hotelId){

		$db= JFactory::getDBO();
		$query = $db->getQuery(true);
		//Get All fields from the table
		$query->select('hi.*');
		$query->from($db->quoteName('#__hotelreservation_invoices'). ' AS hi');

		if(is_numeric($hotelId)) {
			$query->where('hi.hotelId=' . (int)$hotelId);
		}
		$db->setQuery((string)$query);
		$result = $db->loadObjectList();
		return $result;
	}

	/**
	 * Method to build a SQL query to Update an Invoice After its Approved and agreed based on its params:
	 * @param $invoiceId
	 * and
	 * @param $name
	 * and
	 * @param $agreed
	 *
	 * @return mixed
	 */
	function updateState($invoiceId, $name, $agreed){
		$db= JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__hotelreservation_invoices'));
		if(is_numeric($invoiceId) && is_numeric($agreed)) {
			$query->set('approvalName=' .$db->quote($db->escape($name), false).',agreed='.(int)$agreed);
			$query->where('id=' .(int)$invoiceId);
		}
		$db->setQuery((string)$query);

		return $db->query();
	}

	/**
	 * Method to build a SQL query to Get the List Objects
	 * of all invoices with the status 0
	 * @return mixed
	 */
	function getOpenInvoices(){
		$db =JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('hi.*');
		$query->from($db->quoteName('#__hotelreservation_invoices'). ' AS hi');
		$query->where('hi.status=0');
		$db->setQuery((string)$query);
		return $db->loadObjectList();
	}

	/**
	 * Method to build a sql query to get an Email template Object data based on its params:
	 * @param $hotelId
	 * and
	 * @param $template
	 * @return mixed return The Object of one Email Template
	 */
	function getEmailTemplate($hotelId, $template)
	{
		$db =JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		if(is_numeric($hotelId) && is_string($template)) {
			$query->from($db->quoteName('#__hotelreservation_emails').' where hotel_id= '.(int)$hotelId . '  AND is_default  = 1 AND email_type ='. ($db->quote($db->escape($template), false)));
		}
		$db->setQuery((string)$query);
		$templ = $db->loadObject();
		return $templ;
	}

	/**
	 * Method to build an SQL query to load the application settings
	 * @return mixed return Object of the app settings
	 */
    function getAppSettings()
    {
		$db =JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__hotelreservation_applicationsettings'));
		$db->setQuery((string)$query);
		$appSettings = $db->loadObject();
		return $appSettings;

    }

	/**
	 * Method to build an SQL query to load the hotel data with on param: .
	 * @param $hotelId
	 * @return mixed return Object of Hotel Data
	 */
    function getHotel($hotelId)
    {
        $db = JFactory::getDBO();
		$query = $db->getQuery(true);
		//Get All fields from the table
		$query->select('h.*,c.country_name,hp.*,hc.booking_list_email');
		$query->from($db->quoteName('#__hotelreservation_hotels'). ' AS h');
		//Join the currency table with the country table
		$query->join('LEFT', $db->quoteName('#__hotelreservation_countries'). ' AS c USING ( country_id)');
        $query->join('LEFT', $db->quoteName('#__hotelreservation_hotel_contacts'). ' AS hc USING ( hotel_id)');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_paymentsettings'). ' AS hp USING (hotel_id)');
		if(is_numeric($hotelId)) {
			$query->where('hotel_id='.(int)$hotelId);
		}
		$db->setQuery((string)$query);
		return  $db->loadObject();
    }


    function getInvoicesForExport($hotelIds,$startDate,$endDate,$invoiceId)
    {

        $where= '';
        $allHotels = '0';
        $checkAllHotels = explode(",",$hotelIds);

        if(!empty($hotelIds) && !in_array($allHotels,$checkAllHotels)){
            $where = " where hotel_id in (" . $hotelIds . ")  ";
        }


        $dateCondition = '';
        if(!empty($startDate) && isset($startDate) && !empty($endDate) && isset($endDate)){
            $dateCondition = " i.date>='".$startDate."' and i.date<='".$endDate."'";
        }


        $and='';
        if(!empty($dateCondition)) {
            $and = ' and';
        }

        $invoiceSingle = '';
        if(!empty($invoiceId) && isset($invoiceId)){
            $invoiceSingle = ' and i.id ='.$invoiceId;
        }


        $db = JFactory::getDBO();
        $query = "SELECT hotel_id,hotel_name FROM #__hotelreservation_hotels ".$where." group by hotel_id";
        $db->setQuery($query);

        $hotels = $db->loadObjectList();
        $hotels = checkHotels(JFactory::getUser()->id, $hotels);
        if (count($hotels)>0) {
            foreach ($hotels as $hotel) {
                    $db = JFactory::getDBO();
                    $query = "SELECT  i.id,i.hotelId,i.approvalName,id.reservationId,id.arrival,id.departure,id.voucher,id.commissionAmount,id.amount,id.status,CONCAT(hc.first_name,' ',hc.last_name) as guestName FROM #__hotelreservation_invoices i inner join #__hotelreservation_invoice_details id on i.id = id.invoiceid inner join #__hotelreservation_confirmations hc on hc.confirmation_id = id.reservationId where i.hotelId=".$hotel->hotel_id.$invoiceSingle.$and.$dateCondition;

                    $db->setQuery($query);
                    $hotel->hotelsInvoices = $db->loadObjectList();
                if($hotel->hotelsInvoices > 0 ) {
                    foreach ($hotel->hotelsInvoices as $hotelInvoice) {
                        // using the same variable $hotelInvoice->status to show the label
                        // because it is one way data binding
                        switch ($hotelInvoice->status) {
                            case 1:
                                $hotelInvoice->status = JText::_('LNG_AGREED', true);
                                break;
                            case 2:
                                $hotelInvoice->status = str_replace(",", "" , JText::_('LNG_NO_SHOW_NO_CHARGE', true));//removed the comma in the label
                                break;
                            case 3:
                                $hotelInvoice->status = str_replace(",", "" , JText::_('LNG_NO_SHOW_CANCELATION', true));
                                break;
                        }
                    }
                }
            }
            return $hotels;
        }
    }


	function getReservationCommissionData($hotelId,$reservationCost,$startDate,$endDate,$reportType){


		$hotelCondition = ' ';
		$selectedHotel = '';
		$selectHotelNames = 'h.hotel_name as hotelNames ,';
		$leftJoinHotels = 'left join #__hotelreservation_hotels as h on h.hotel_id = i.hotelId';
		$groupByHotelNames = ', hotelNames';
		if(isset($hotelId) && $hotelId > 0){
			$selectedHotel = ' i.hotelId ,';
			$hotelCondition = " i.hotelId=".$hotelId." and  ";

			$selectHotelNames='';
			$leftJoinHotels='';
			$groupByHotelNames = '';
		}

		$reservationCosts = "";
		if(isset($reservationCost) && $reservationCost == 1){
			$reservationCosts.="sum(i.reservationAmount) as reservationCost,";
		}


		$startDate =  date_format(new DateTime($startDate),'Y-m-d');
		$endDate =  date_format(new DateTime($endDate),'Y-m-d');

		$dateCondition = '';
		if(!empty($startDate) && isset($startDate) && !empty($endDate) && isset($endDate)){
			$dateCondition = " i.date>='".$startDate."' and i.date<='".$endDate."' ";
		}else if(!empty($startDate) && isset($startDate)){
			$dateCondition = " i.date>='".$startDate."' ";
		}else if(!empty($endDate) && isset($endDate)){
			$dateCondition = " i.date<='".$endDate."' ";
		}

		if((!empty($startDate) || !empty($dateEnd)) && !empty($hotelId))
		{
			$db = JFactory::getDBO();

			$query = "SELECT
						$selectHotelNames
						$selectedHotel
					    $reservationCosts
					    sum(i.amount) as totalAmount,
					    sum(i.commissionAmount) as commissionAmount,
					    (CASE '$reportType'
				                        WHEN 'MONTH' then concat('01','-',Month(i.date),'-',Year(i.date))
				                        WHEN 'YEAR' then  concat(Year(i.date))
				            END) as groupUnit
					  FROM
					    #__hotelreservation_invoices as i
					    $leftJoinHotels
					  where i.status = 1 and $hotelCondition.$dateCondition 
				 	  GROUP BY groupUnit $groupByHotelNames
					  ORDER BY groupUnit ASC ";

			// INNER JOIN
			// #__hotelreservation_invoice_details as id  ON id.invoiceId = i.id
			$db->setQuery( $query );
			$result = $db->loadObjectList();

			return $result;
		}
	}
}