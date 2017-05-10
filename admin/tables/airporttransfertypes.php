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

class JTableAirportTransferTypes extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct($db) {

		parent::__construct('#__hotelreservation_airport_transfer_types', 'airport_transfer_type_id', $db);
	}
	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

    /**
     * Method to build an SQL query to change the state of one item based on its:
     * @param $airporttransfertypeid
     * @return bool True if the query is executed and the state is changed
     */
    function state($airporttransfertypeid)
    {

        $db= JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->update($db->quoteName('#__hotelreservation_airport_transfer_types'));
        $query->set('is_available = IF(is_available, 0, 1)');
        if(is_numeric($airporttransfertypeid)) {
            $query->where('airport_transfer_type_id=' . (int)$airporttransfertypeid);
        }
        $db->setQuery((string)$query);

        if (!$db->query())
        {
            return false;
        }
        return true;
    }

    function getHotelAirportTransfer($hotelId,$airportTransfers=null)
    {

        $filter="";

        if(isset($hotelId) && $hotelId > 0){
            $filter= "and hotel_id = $hotelId ";
        }

        $tranferFilter = "";

        if(!empty($airportTransfers)){
            $tranferFilter = "and at.airport_transfer_type_id in (".$airportTransfers.")";
        }

        $db= JFactory::getDBO();
        $query = "select at.*
	    		  from #__hotelreservation_airport_transfer_types at
	    		  where at.is_available = 1
	    			$tranferFilter
	    			$filter";
        $db->setQuery( $query );
        return $db->loadObjectList();
    }

    function getAirportTransferTypes($hotel_id){
        $db= JFactory::getDBO();
        $query = "select at.airport_transfer_type_id ,at.airport_transfer_type_name
	    		  from #__hotelreservation_airport_transfer_types at
	    		  where at.is_available = 1 and at.hotel_id = $hotel_id";
        $db->setQuery( $query );
        return $db->loadObjectList();
    }

}