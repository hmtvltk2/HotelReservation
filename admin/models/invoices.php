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

jimport('joomla.application.component.modellist');

class JHotelReservationModelInvoices extends JModelList
{

    function __construct()
    {
        parent::__construct();
    }



    /**
     * Method to get the Hotel Ids
     * @return mixed
     */
    function getHotelId()
    {
        $hotel_id = JRequest::getVar('hotel_id');
        if(is_numeric($hotel_id)) {
            $this->setHotelId($hotel_id);
        }
        return $hotel_id;
    }

    /**
     * @return mixed
     */
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}

    /**
     * Method to set the hotel Ids
     * @param $hotel_id
     */
    function setHotelId($hotel_id)
    {
        // Set id and wipe data
        $this->_hotel_id = $hotel_id;
    }


    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  string  An SQL query
     *
     * @since   1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //Get All fields from the table
        $query->select($this->getState('list.select', 'hi.*'));
        $query->from($db->quoteName('#__hotelreservation_invoices') . 'AS hi');

        //Join the currency table with the country table
        $query->select('hh.hotel_id');
        $query->join('LEFT', $db->quoteName('#__hotelreservation_hotels') . 'AS hh ON hh.hotel_id = hi.hotelId ');

        $hotelId = $this->getState('filter.hotel_id');
        if (is_numeric($hotelId)) {
            $query->where('hi.hotelId ='.(int) $hotelId);
        }

        $query->group('hi.id');
        $query->order('hi.id desc');
        
        return $query;
    }


    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = null , $direction = null) {
        $app = JFactory::getApplication('administrator');

        $select = $this->getUserStateFromRequest($this->context.'.filter.select', 'filter_select');
        $this->setState('filter.select', $select);

        $statusId = $app->getUserStateFromRequest($this->context.'.filter.airline_id', 'filter_invoiceId');
        $this->setState('filter.invoiceId', $statusId);

        $stateId = $app->getUserStateFromRequest($this->context.'.filter.hotel_id', 'filter_hotel_id');
        $this->setState('filter.hotel_id', $stateId);

        // Check if the ordering field is in the white list, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
        $this->setState('list.ordering', $value);

        // Check if the ordering direction is valid, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
        $this->setState('list.direction', $value);


        $hotel_id = JRequest::getVar('hotel_id', null);
        if ($hotel_id) {
            if ($hotel_id != $app->getUserState($this->context.'.filter.hotel_id')) {
                $app->setUserState($this->context.'.filter.hotel_id', $hotel_id);
                JRequest::setVar('limitstart', 0);
            }
        }
        else {
            $hotel_id = $app->getUserState($this->context.'.filter.hotel_id');

            if (!$hotel_id) {
                $hotel_id = 0;
            }
        }
        $app->setUserState('com_jhotelreservation.invoices.filter.hotel_id', $hotel_id);
        $this->setState('filter.hotel_id', $hotel_id);


        // List state information.
        parent::populateState('hi.Id', 'desc');
    }


    /**
     * @return mixed
     */
    function getAppSettings()
    {
        $appSettingsQuery = $this->getTable("Invoices","JTable");
        $this->appSettings = $appSettingsQuery->getAppSettings();

        return $this->appSettings;
    }

    function exportInvoicesCsv(){
        $csv_output = $this->getHotelsInvoicesCSV();
        ob_clean();
        $fileName = "jhotelreservation_hotels_invoices";
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header( "Content-disposition: filename=".$fileName.".csv");
        print $csv_output;
    }

    /**
     * @return string generate a csv file with all the invoices for hotels ,
     * based on the start date and end date of the hotel
     * @throws Exception
     */
    public function getHotelsInvoicesCSV(){
        //user selected values
	    $appSettings = JHotelUtil::getInstance()->getApplicationSettings();
	    $delimiter = $appSettings->delimiter;

	    $startDate = JRequest::getVar("start_date");
        $endDate = JRequest::getVar("end_date");
        $hotelsSelected = JRequest::getVar("hotels"); //multiple hotels selected , the hotels are checked here by their ID's

        //convert dates
        $startDate = JHotelUtil::convertToMysqlFormat($startDate);//the start date format for mysql
        $endDate = JHotelUtil::convertToMysqlFormat($endDate); //the end date format for mysql

        $invoiceTable = $this->getTable('Invoices','JTable');

        $hotelIds="";
        //get the hotel ids selected by the user ,value 0 or empty is used to get all the invoices for hotels
        if(!empty($hotelsSelected)){
                if(isset($hotelsSelected) && count($hotelsSelected) > 0 ){
                    $hotelIds = $hotelsSelected;
                }
            $hotelIds = implode(",", $hotelIds);
        }
        //jtable method to get the data for csv export
        $hotels =  $invoiceTable->getInvoicesForExport($hotelIds,$startDate,$endDate,$invoiceId=null);

        $csv_output ="Hotel Name".$delimiter."id".$delimiter."reservation id".$delimiter."Invoice Approval Name".$delimiter."arrival".$delimiter."departure".$delimiter."voucher".$delimiter."status".$delimiter."amount".$delimiter."commissionAmount";

        $csv_output = $csv_output."\n";

        //loop for the hotels to be used in the csv
        foreach($hotels as $hotel){
            //will use only one hotel object to get the invoices for that hotel
            foreach($hotel->hotelsInvoices as $hotelInvoices) {
                $csv_output .= $hotel->hotel_name.$delimiter.$hotelInvoices->id . $delimiter . "\"$hotelInvoices->reservationId\"" . $delimiter . "\"$hotelInvoices->approvalName\"" . $delimiter . "\"$hotelInvoices->arrival\"" . $delimiter . "\"$hotelInvoices->departure\"" . $delimiter. "$hotelInvoices->voucher" . $delimiter . "\"$hotelInvoices->status\"" . $delimiter . "\"$hotelInvoices->amount\"" . $delimiter."\"$hotelInvoices->commissionAmount\"" . $delimiter;

                $csv_output .= "\n";
            }
        }

        return $csv_output;
    }

}