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

class TableUsers extends JTable
{

	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableUsers(& $db) {
	
		parent::__construct('#__users', 'id', $db);
	}
	
	function setKey($k)
	{
		$this->_tbl_key = $k;
	}
	
	function getHotelManagers(){
		$query = "	select c.*, d.hotels,e.groups
					from  #__users c
                        left join
								(SELECT user_id,
									GROUP_CONCAT(DISTINCT hotel_name ORDER BY hotel_name DESC SEPARATOR ',')  hotels
									FROM
									(
										select a.user_id,b.hotel_name
										from #__hotelreservation_user_hotel_mapping  a,
										     #__hotelreservation_hotels b
										where a.hotel_id=b.hotel_id) a
									GROUP BY user_id
							   ) as d 
					 	on c.id=d.user_id
						inner join #__user_usergroup_map  b on b.user_id = c.id
						inner join #__usergroups as a on a.id = b.group_id
                        left join 
								(SELECT user_id,
								GROUP_CONCAT(DISTINCT name ORDER BY name DESC SEPARATOR ',')  groups
								FROM
								(
									select a.user_id,b.name
									from #__hotelreservation_user_group_mapping  a,
									     #__hotelreservation_groups b
									where a.group_id=b.id) a
								GROUP BY user_id
							) as e 
						on c.id=e.user_id
					where a.title like 'Hotel Manager'";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	function getUsersPermissions($userId){
		$query = "SELECT e.code 
				  FROM  #__users a,
						#__hotelreservation_user_group_mapping b,
						#__hotelreservation_group_role_mapping c,
						#__hotelreservation_role_permission_mapping d,
						#__hotelreservation_permissions e
					WHERE a.id=b.user_id
						and b.group_id = c.group_id
						and c.role_id = d.role_id
						and d.permission_id = e.id
						and a.id=".$userId;
		$this->_db->setQuery( $query );
		return $this->_db->loadColumn();
	}

	function getUserByEmail($email){
		$query = " SELECT *
							FROM #__users 
								WHERE 
									username = 	'".trim($email)."' 
									OR
									email 		= '".trim($email)."' 
								";
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}
}