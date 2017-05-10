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

class TableManageHotelUsers extends JTable
{
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableManageHotelUsers(& $db) {

		parent::__construct('#__hotelreservation_users', 'user_id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}
	
	function getUserById($userId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_users where user_id=$userId";
		//dmp($query);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	function getUsers($limitstart=0, $limit=0){
		$db =JFactory::getDBO();
		$query = "select hu.* from #__hotelreservation_users hu
		inner join #__hotelreservation_confirmations hc on hu.user_id = hc.user_id" ;
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}
	function getHotelUsers($hotelId, $limitstart=0, $limit=0){
		$db =JFactory::getDBO();
		$query = "select hu.* from #__hotelreservation_users hu 
					inner join #__hotelreservation_confirmations hc on hu.user_id = hc.user_id where hc.hotel_id=$hotelId" ;
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadObjectList();
	}
	
	function getTotalUsers($hotelId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_users hu 
					inner join #__hotelreservation_confirmations hc on hu.user_id = hc.user_id where hc.hotel_id=$hotelId" ;
		//dmp($query);
		$db->query();
		return $db->getNumRows();
	}
	function deleteUser($userId,$hotelId){
		$db =JFactory::getDBO();
		$query = "delete from #__hotelreservation_users where user_id=$userId";
		$db->setQuery($query);
		$db->query();
	}
}