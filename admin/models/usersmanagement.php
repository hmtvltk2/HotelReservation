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
jimport('joomla.html.pagination');

class JHotelReservationModelUsersManagement extends JModelLegacy
{ 
	var $dataRooms 					= null;
	var $dataRoomsConfirmations		= null;
	var $paymentProcessorsResults	= null;
	var $hotels						= null;
	function __construct()
	{
		parent::__construct();
		
		$mainframe = JFactory::getApplication();
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->_total = 0;
	}
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	//users functions 
	
	function getHotelManagers(){
		$hotelTable = $this->getTable('users');
		$this->_total =$hotelTable->getHotelManagers();
		return $hotelTable->getHotelManagers();
	}
	function getAllHotels(){
		$hotelTable = $this->getTable('hotels');
		return $hotelTable->getAllHotels();
	}
	function assignUserHotels(){
		$post = JRequest::get('post');
		$table = $this->getTable('userhotelmapping');
		
		$cids = JRequest::getVar( 'hotels', array(0), 'post', 'array' );
		$userID = JRequest::getVar( 'user_id',null);
		$table->deleteHotelMappings($userID);
		
		if (count( $cids )) {
			foreach($cids as $cid) {
				$table->hotel_id = $cid;
				$table->user_id = $userID;
				if(!$table->store()){
					return $table->getErrorMsg();
				}
			}
		}
		return JText::_('LNG_USER_HOTEL_MAPPING_SAVED',true);
	}
	
	function getAssignedUserHotels(){
		$user_id = JRequest::getVar('user_id');
		$table = $this->getTable('userhotelmapping');
		return $table->getAssignedHotels($user_id);
	}
	function getAssignedHotelsName(){
		$user_id = JRequest::getVar('user_id');
		$table = $this->getTable('userhotelmapping');
		return $table->getAssignedHotelsName($user_id);
	}
	
	function assignUserGroups(){
		$post = JRequest::get('post');
		$table = $this->getTable('usergroupmapping');
	
		$cids = JRequest::getVar( 'groups', array(0), 'post', 'array' );
		$userID = JRequest::getVar( 'user_id',null);
		$table->deleteGroupMappings($userID);
	
		if (count( $cids )) {
			foreach($cids as $cid) {
				$table->group_id = $cid;
				$table->user_id = $userID;
				if(!$table->store()){
					return $this->_db->getErrorMsg();
				}
			}
		}
		return JText::_('LNG_USER_GROUP_MAPPING_SAVED',true);
	}
	
	function getAssignedUserGroups(){
		$user_id = JRequest::getVar('user_id');
		$table = $this->getTable('usergroupmapping');
		return $table->getAssignedUserGroups($user_id);
		
	}
	function getAssignedGroupsName(){
		$user_id = JRequest::getVar('user_id');
		$table = $this->getTable('usergroupmapping');
		return $table->getAssignedGroupsName($user_id);
	}
	
	
	//roles functions
	
	function getAllRoles(){
		$table = $this->getTable('usersroles');
		return $table->getAllRoles();
	}
	function getRole(){
		$roleId = JRequest::getVar('id',null);
		$table = $this->getTable('usersroles');
		$table->load($roleId);
		return $table;
	}
	function getRolePermissions(){
		$roleId = JRequest::getVar('id',null);
		$table = $this->getTable('rolepermissionsmapping');
		return $table->getRolePermissions($roleId);
	}
	function saveRole(){
		//save role 
		$result = null; 
		$table = $this->getTable('usersRoles');
		
		$data = JRequest::get( 'post' );
		
		// Bind the form fields to the table
		if (!$table->bind($data))
		{
			$result->msg = $this->_db->getErrorMsg();
			return $result;
		}
		
		// Make sure the record is valid
		if (!$table->check()) {
			$result->msg = $this->_db->getErrorMsg();
			return $result;
		}
		// Store the web link table to the database
		if (!$table->store()) {
			$result->msg = $this->_db->getErrorMsg();
			return $result;
		}
		if(!isset($roleID)){
			$roleID= $table->id; 
		}
			
		//save role permissions
		if(isset($roleID)){
			$result->roleID= $roleID;
			$this->saveRolePermissions($roleID);		
		}
		else
			$result->msg= JText::_('LNG_ROLE_PERMISSION_ERROR_SAVING',true);
		
		return $result;
	}
	function saveRolePermissions($roleID){
		$cids = JRequest::getVar( 'permissions', array(0), 'post', 'array' );
		$table = $this->getTable('rolepermissionsmapping');
		
		$table->deleteRoleMappings($roleID);
		if (count( $cids )) {
			foreach($cids as $cid) {
				$table->permission_id = $cid;
				$table->role_id = $roleID;
				if(!$table->store()){
					return $table->getErrorMsg();
				}
			}
		}
	}
	function deleteRoles(){
		$cids = JRequest::getVar( 'id', array(0), 'post', 'array' );
		$post = JRequest::get('post');
	
		$row = $this->getTable('usersRoles');
		$table = $this->getTable('rolepermissionsmapping');

		if (count( $cids )) {
			foreach($cids as $cid) {
				$table->deleteRoleMappings($cid);
				if (!$row->delete( $cid )) {
					return $row->getErrorMsg();
				}
			}
		}
		return JText::_('LNG_ROLES_DELETED',true);
	}
	
	function getAllPermissions(){
		$table = $this->getTable('userspermissions');
		return $table->getAllPermissions();
	}
	
	
	//groups functions 
	
	function getAllGroups(){
		$table = $this->getTable('usersgroups');
		return $table->getAllGroups();
	}
	function getGroup(){
		$groupId = JRequest::getVar('id',null);
		$table = $this->getTable('usersgroups');
		$table->load($groupId);
		return $table;
	}
	function getGroupRoles(){
		$groupId = JRequest::getVar('id',null);
		$table = $this->getTable('grouprolesmapping');
		return $table->getGroupRoles($groupId);
	}
	function saveGroup(){
		//save role
		$result = null;
		$table = $this->getTable('usersgroups');
	
		$data = JRequest::get( 'post' );
	
		// Bind the form fields to the table
		if (!$table->bind($data))
		{
			$result->msg = $this->_db->getErrorMsg();
			return $result;
		}
	
		// Make sure the record is valid
		if (!$table->check()) {
			$result->msg = $this->_db->getErrorMsg();
			return $result;
		}
		// Store the web link table to the database
		if (!$table->store()) {
			$result->msg = $this->_db->getErrorMsg();
			return $result;
		}
		if(!isset($groupID)){
			$groupID= $table->id;
		}
			
		//save group roles
		if(isset($groupID)){
			$result->groupID= $groupID;
			$this->saveGroupRoles($groupID);
		}
		else
		$result->msg= JText::_('LNG_GROUP_ROLE_ERROR_SAVING',true);
	
		return $result;
	}
	function saveGroupRoles($groupID){
		$cids = JRequest::getVar( 'roles', array(0), 'post', 'array' );
		$table = $this->getTable('grouprolesmapping');
	
		$table->deleteGroupMappings($groupID);
		if (count( $cids )) {
			foreach($cids as $cid) {
				$table->role_id = $cid;
				$table->group_id = $groupID;
				if(!$table->store()){
					return $table->getErrorMsg();
				}
			}
		}
		
	}
	function deleteGroups(){
		$cids = JRequest::getVar( 'id', array(0), 'post', 'array' );
		$post = JRequest::get('post');
	
		$row = $this->getTable('usersgroups');
		$table = $this->getTable('grouprolesmapping');
	
		if (count( $cids )) {
			foreach($cids as $cid) {
				$table->deleteGroupMappings($cid);
				if (!$row->delete( $cid )) {
					return $row->getErrorMsg();
				}
			}
		}
		return JText::_('LNG_GROUPS_DELETED',true);
	}
	

}
?>