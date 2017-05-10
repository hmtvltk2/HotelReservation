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

class TableUserHotelMapping extends JTable
{

	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableUserHotelMapping(& $db) {
	
		parent::__construct('#__hotelreservation_user_hotel_mapping', 'hotel_id,user_id', $db);
	}
	
	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getAssignedHotels($userId){
		
		$query = "select a.hotel_id
					from #__hotelreservation_user_hotel_mapping  a
					where a.user_id=".$userId;
		$this->_db->setQuery( $query );
		return $this->_db->loadColumn();
	}
	function getAssignedHotelsName($userId){
	
		$query = "select b.hotel_name
						from #__hotelreservation_user_hotel_mapping  a, 
							 #__hotelreservation_hotels b
						where a.hotel_id=b.hotel_id
						and a.user_id=".$userId
						;
		$this->_db->setQuery( $query );
		return $this->_db->loadColumn();
	}
	function deleteHotelMappings($userId){
		$query = "delete from #__hotelreservation_user_hotel_mapping where user_id=".$userId;
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
	
	function createHotelMapping($userId,$hotelId){
		$query = "insert into #__hotelreservation_user_hotel_mapping(`hotel_id`,`user_id`) values('$hotelId','$userId')";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
}