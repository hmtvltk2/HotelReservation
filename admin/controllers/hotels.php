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

class JHotelReservationControllerHotels extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	

	function __construct()
	{
		parent::__construct();
	}

	function viewHotels(){
		JRequest::setVar( 'view', 'hotels' );
		$this->display();
	}

	function changeFeaturedState(){
		$model = $this->getModel('hotels');
        $hotel_id	= JRequest::getVar('cid', array(),'','array');
        $hotel_id = $hotel_id[0];

		if ($model->changeFeaturedState($hotel_id)) {
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_HOTEL_STATE',true);
		}
		JRequest::setVar( 'view', 'hotels' );
		$this->display();
	}


	function state()
	{
		$model = $this->getModel('hotels');

        $hotel_id	= JRequest::getVar('cid', array(),'','array');
        $hotel_id = $hotel_id[0];

		if ($model->state($hotel_id)) {
			$msg = JText::_( '' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_HOTEL_STATE',true);
		}


		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=hotels&view=hotels', $msg );
	}
	public function back(){
		$this->setRedirect('index.php?option='.getBookingExtName());
	}
	function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));
		
		// Get items to remove from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');
		
		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_('LNG_NO_HOTEL_SELECTED',true));
		}
		
		$model = $this->getModel('hotels');
		
		// Make sure the item ids are integers
		jimport('joomla.utilities.arrayhelper');
		JArrayHelper::toInteger($cid);
	
		if ($model->remove($cid)) {
			$msg = JText::_('LNG_HOTEL_HAS_BEEN_DELETED',true);
		} else {
			$msg = JText::_('LNG_ERROR_DELETE_HOTEL',true);
		}
	
		// Check the table in so it can be edited.... we are done with it anyway
	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=hotels.viewHotels', $msg );
	}
	
	function fixHotelsDates(){
		$model = $this->getModel('hotels');
		$model->fixHotelsDates();
	}
}