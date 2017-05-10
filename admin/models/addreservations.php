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


//echo JPATH_COMPONENT_SITE.DS.'models'.DS.'confirmations.php';
class JHotelReservationModelAddReservations extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$h		= JRequest::getVar('hotel_id',  0, '');
		$this->setHotelId((int)$h);
	}
	function setHotelId($hotel_id)
	{
		// Set id and wipe data
		$this->_hotel_id	= $hotel_id;
		$this->_hotels		= null;
	}
	function &getRoomTypesId()
	{
		// Load the data
		$query = ' SELECT room_id FROM #__hotelreservation_rooms WHERE is_available = 1 ';
		//echo $query;
		//$this->_db->setQuery( $query );
		$arr = $this->_getList( $query );
		$ret = array();
		foreach( $arr as $v )
		{
			$ret[] = $v->room_id;
		}
		return $ret;
	}
	
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}
	
	function &getHotel()
	{
		$query = 	' SELECT 	
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels				h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						'.
					' WHERE 
						hotel_id = '.$this->_hotel_id;
		
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}

}
?>