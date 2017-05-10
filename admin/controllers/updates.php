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

class JHotelReservationControllerUpdates extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function apply()
	{
		$msg = $this->saveSettings();
		$link = 'index.php?option='.getBookingExtName().'&controller=applicationsettings&view=applicationsettings';
		$this->setRedirect($link, $msg);
	}

	
	function saveOrder()
	{
		$model = $this->getModel('updates');
		$post = JRequest::get( 'post' ); 

		if ($model->store($post)) {
			$msg = JText::_('LNG_ORDER_SAVED',true);
		} else {
			$msg = JText::_('LNG_ERROR_SAVING_ORDER',true);
		}
		
		$link = 'index.php?option='.getBookingExtName().'&view=updates';
		$this->setRedirect($link, $msg);
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
	}
	public function find()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the caching duration
		$component = JComponentHelper::getComponent('com_installer');
		$params = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;
		
		$module = JComponentHelper::getComponent('com_jhotelreservation');
		
		// Find updates
		$model	= $this->getModel('updates');
		$model->findUpdates(array($module->id), $cache_timeout);
		$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=updates', false));
	}
	
	function update(){
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('updates');
		$uid   = JRequest::getVar('cid', array(), 'array');

		JArrayHelper::toInteger($uid, array());
		if ($model->update($uid))
		{
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
		}
	
		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_jhotelreservation.redirect_url');
		if (empty($redirect_url))
		{
			$redirect_url = JRoute::_('index.php?option=com_jhotelreservation&view=updates', false);
		}
		else
		{
			// Wipe out the user state when we're going to redirect
			$app->setUserState('com_jhotelreservation.redirect_url', '');
			$app->setUserState('com_jhotelreservation.message', '');
			$app->setUserState('com_jhotelreservation.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}
	
	function getVersionStatus(){
		$model = $this->getModel('updates');
		$result = $model->getVersionStatus();
		echo $result;
		exit;
	}

}