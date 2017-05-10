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

class JHotelReservationControllerPaymentSettings extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'state', 'state');  
		$this->registerTask( 'add', 'edit'); 
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('paymentsettings');

		$post = JRequest::get( 'post' );
		if( JHotelUtil::checkIndexKey( '#__hotelreservation_paymentsettings', array('hotel_id' => $post['hotel_id'] , 'payment_type_id' => $post['payment_type_id'] ) , 'payment_id', $post['payment_id'] ) )
		{
			$msg = JText::_('LNG_PAYMENT_NAME_EXISTENT',true);
			JError::raiseWarning( 500, $msg );
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=paymentsettings&view=paymentsettings&task=add&hotel_id='.$post['hotel_id'] );
		}
		else if ($model->store($post)) 
		{
			$msg = JText::_('LNG_PAYMENT_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=paymentsettings&view=paymentsettings&hotel_id='.$post['hotel_id'], $msg );
		} 
		else 
		{
			$msg = JText::_('LNG_ERROR_SAVING_PAYMENT',true);
			//JError::raiseWarning( 500, $msg);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=paymentsettings&view=paymentsettings&hotel_id='.$post['hotel_id'] );	
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$post 		= JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0;
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=paymentsettings&view=paymentsettings&hotel_id='.$post['hotel_id'], $msg );
	}
	
	function delete()
	{
		$model = $this->getModel('paymentsettings');
		$post 		= JRequest::get( 'post' );
		if( !isset($post['hotel_id']) )
			$post['hotel_id'] = 0;
			
		if ($model->remove()) {
			$msg = JText::_('LNG_PAYMENT_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_PAYMENT',true);
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=paymentsettings&view=paymentsettings&hotel_id='.$post['hotel_id'], $msg );
	}
	
	function edit()
	{
		JRequest::setVar( 'view', 'paymentsettings' );
	
		parent::display(); 
		
	}
	
	function state()
	{
		$model = $this->getModel('paymentsettings');
		$get = JRequest::get( 'get' );
		if( !isset($get['hotel_id']) )
			$get['hotel_id'] = 0;
			
		if ($model->state()) {
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_PAYMENT_STATE',true);
		}

	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=paymentsettings&view=paymentsettings&hotel_id='.$get['hotel_id'], $msg );
	}
}