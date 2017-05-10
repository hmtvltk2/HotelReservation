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

class JHotelReservationModelPaymentSettings extends JModelLegacy
{ 
	function __construct()
	{
		parent::__construct();
		$array 	= JRequest::getVar('payment_id',  0, '', 'array');
		$h		= JRequest::getVar('hotel_id',  0, '');
		//var_dump($array);
		if(isset($array[0])) $this->setId((int)$array[0]);
		$this->setHotelId((int)$h);
	}
	function setId($payment_id)
	{
		// Set id and wipe data
		$this->_payment_id	= $payment_id;
		$this->_data		= null;
		$this->_hotels		= null;
	}
	
	function setHotelId($hotel_id)
	{
		// Set id and wipe data
		$this->_hotel_id	= $hotel_id;
		$this->_data		= null;
		$this->_hotels		= null;
	}
	function &getHotelId()
	{
		return $this->_hotel_id;
	}
	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	 
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
	
	function &getDatas()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = ' 	SELECT 
							p.*,
							t.payment_type_name
						FROM #__hotelreservation_paymentsettings  p
						LEFT JOIN #__hotelreservation_payment_types t USING(payment_type_id)
						WHERE hotel_id='.$this->_hotel_id." 
						ORDER BY payment_order ";
			//$this->_db->setQuery( $query );
			$this->_data = $this->_getList( $query );
		}
		
		return $this->_data;
	}
	
	
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) 
		{
			$query = 	' SELECT 
								* 
							FROM #__hotelreservation_paymentsettings'.
						' WHERE payment_id = '.$this->_payment_id;
			
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
					
					
			
		}
		if (!$this->_data) 
		{
			$this->_data = new stdClass();
			$this->_data->payment_id				= null;
			$this->_data->payment_type_id			= null;
			$this->_data->hotel_id 					= null;
			$this->_data->payment_name				= null;	
			$this->_data->payment_percent			= null;
			$this->_data->payment_value				= null;			
			$this->_data->payment_days				= null;
			$this->_data->payment_order				= null;
			$this->_data->is_check_days				= null;
			$this->_data->is_available				= null;
			
		}
		
		$query = ' 	SELECT 
						*
					FROM #__hotelreservation_payment_types  
					ORDER BY payment_type_name ';
		//$this->_db->setQuery( $query );
		$this->_data->payments = $this->_getList( $query );
		return $this->_data;
	}

	function store($data)
	{	
		$row = $this->getTable();

		// Bind the form fields to the table
		if (!$row->bind($data)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	function remove()
	{
		$cids = JRequest::getVar( 'payment_id', array(0), 'post', 'array' );
		
		

		$row = $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;

	}
	
	function state()
	{
		$query = 	" UPDATE #__hotelreservation_paymentsettings SET is_available = IF(is_available, 0, 1) WHERE payment_id = ".$this->_payment_id;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) 
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;
			
		}
		return true;
	}




}
?>