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

class TableUserGroupMapping extends JTable
{

	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableUserGroupMapping(& $db) {
	
		parent::__construct('#__hotelreservation_user_group_mapping', 'group_id,user_id', $db);
	}
	
	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getAssignedUserGroups($userId){
		
		$query = "select a.group_id
					from #__hotelreservation_user_group_mapping  a
					where a.user_id=".$userId;
		$this->_db->setQuery( $query );
		return $this->_db->loadColumn();
	}
	function getAssignedGroupsName($userId){
	
		$query = "select b.group_name
						from #__hotelreservation_user_group_mapping  a, 
							 #__hotelreservation_groups b
						where a.group_id_id=b.id
						and a.user_id=".$userId
						;
		$this->_db->setQuery( $query );
		return $this->_db->loadColumn();
	}
	function deleteGroupMappings($userId){
		$query = "delete from #__hotelreservation_user_group_mapping where user_id=".$userId;
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
	
	function assignToDefaultAccessGroup($userId){
		$query = "insert into #__hotelreservation_user_group_mapping(`group_id`,`user_id`) values('1','$userId')";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
}