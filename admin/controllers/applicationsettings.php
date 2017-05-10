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

class JHotelReservationControllerApplicationSettings extends JControllerLegacy
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
	
	function save()
	{
		$msg = $this->saveSettings();
		$link = 'index.php?option='.getBookingExtName();
		$this->setRedirect($link, $msg);
	}
	
	function saveSettings()
	{
		$model = $this->getModel('applicationsettings');
		$post = JRequest::get( 'post' ); 
		
		$post['terms_and_conditions'] 			= JRequest::getVar('terms_and_conditions', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $post['special_notes'] 					= JRequest::getVar('special_notes', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['policy'] 						= JRequest::getVar('policy', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['rooms_left']                     = $post['rooms_left']==''?-1:$post['rooms_left'];
		
		$config =JFactory::getConfig();
		$post['sendmail_from'] = $config->get( 'config.mailfrom' );
		$post['sendmail_name'] = $config->get( 'config.fromname' );
		if ($model->store($post)) {
            $model->saveTermsAndConditions($post);
            $msg = JText::_('LNG_SETTINGS_APPLICATION_SAVED',true);
		} else {
			$msg = JText::_('LNG_ERROR_SAVING_SETTINGS_APPLICATION',true);
		}
		return $msg;
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

}