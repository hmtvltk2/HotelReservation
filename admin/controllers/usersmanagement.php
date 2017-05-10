<?php
/**
 * @copyright	Copyright (C) 2009-2011 CMSJunkie - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

?>
<?php
class JHotelReservationControllerUsersManagement extends JControllerLegacy{
	function __construct($config = array())
	{
		JRequest::setVar('layout','listing');
		
		parent::__construct($config);
	}

	function listing(){
		JRequest::setVar('layout','listing');
		JRequest::setVar( 'view', 'usersmanagement' );
		return $this->display();
	}
	
	// user functions
	function back(){
		$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
	}
	function editHotelAssignments(){
		JRequest::setVar('layout','editHotelAssignments');
		JRequest::setVar( 'view', 'usersmanagement' );
		
		return $this->display();
	}
	
	function assignUserHotels(){
		$model = $this->getModel('usersmanagement');
		$msg = $model->assignUserHotels();
		$userID = JRequest::getVar('user_id',null);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.editHotelAssignments&user_id='.$userID, $msg );
	}
	function assignUserHotelsClose(){
		$model = $this->getModel('usersmanagement');
		$msg = $model->assignUserHotels();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=usersmanagement&view=usersmanagement', $msg );
	}
	function editGroupAssignments(){
		JRequest::setVar('layout','editGroupAssignments');
		JRequest::setVar( 'view', 'usersmanagement' );
		return $this->display();
	}
	
	function assignUserGroups(){
		$model = $this->getModel('usersmanagement');
		$msg = $model->assignUserGroups();
		$userID = JRequest::getVar('user_id',null);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.editGroupAssignments&user_id='.$userID, $msg );
	}
	
	function assignUserGroupsClose(){
		$model = $this->getModel('usersmanagement');
		$msg = $model->assignUserGroups();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=usersmanagement&view=usersmanagement', $msg );
	}
	
	
	//role functions
	function listRoles(){
		JRequest::setVar('layout','listRoles');
		JRequest::setVar( 'view', 'usersmanagement' );
		return $this->display();
	}
	function editRole(){
		JRequest::setVar('layout','editRole');
		JRequest::setVar( 'view', 'usersmanagement' );
		return $this->display();
	}
	function saveRole(){
		$model = $this->getModel('usersmanagement');
		$result = $model->saveRole();
		$msg = $result->msg;
		$roleID = $result->roleID;
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.editRole&id='.$roleID, $msg );
	}
	function saveRoleClose(){
		$model = $this->getModel('usersmanagement');
		$result = $model->saveRole();
		$msg = $result->msg;
		$roleID = $result->roleID;
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.listRoles', $msg );
	}
	function deleteRoles(){
		$model = $this->getModel('usersmanagement');
		$msg = $model->deleteRoles();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.listRoles', $msg );
	}
	
	//group functions 
	function listGroups(){
		JRequest::setVar('layout','listGroups');
		JRequest::setVar( 'view', 'usersmanagement' );
		return $this->display();
	}
	
	function editGroup(){
		JRequest::setVar('layout','editGroup');
		JRequest::setVar( 'view', 'usersmanagement' );
		return $this->display();
	}
	function saveGroup(){
		$model = $this->getModel('usersmanagement');
		$result = $model->saveGroup();
		$msg = $result->msg;
		$groupID = $result->groupID;
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.editGroup&id='.$groupID, $msg );
	}
	function saveGroupClose(){
		$model = $this->getModel('usersmanagement');
		$result = $model->saveGroup();
		$msg = $result->msg;
		$roleID = $result->roleID;
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.listGroups&id='.$roleID, $msg );
	}
	function deleteGroups(){
		$model = $this->getModel('usersmanagement');
		$msg = $model->deleteGroups();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=usersmanagement.listGroups', $msg );
	}
	
}