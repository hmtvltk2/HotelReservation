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


class JHotelReservationControllerHotelRatings extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);

	}


    /**
     * Method to display the Hotel Ratings Menu
     */
	function menuhotelratings(){
		JRequest::setVar('view','hotelratings');
		JRequest::setVar('layout','ratingsmenu');
		parent::display();
	}

    /**
     * Method to display the Hotel Ratings Layout
     */
	function hotelratings(){
		parent::display();
	}

    /**
     * Method to handle the delete button
     */
	function deletehotelrating(){
        $deleteHotelRating = $this->getModel("HotelRatings");
        $msg = $deleteHotelRating->deleteHotelRating();
        $this->setRedirect('index.php?option='.getBookingExtName().'&view=hotelratings12', $msg);
	}

    /**
     * Method to handle the back button
     */
	function back(){
		$msg = JText::_( '' ,true);
		$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
	}

    /**
     *
     */
	function changeState(){
		$hotelratings = $this->getModel('hotelratings');
		$hotelratings->changeState();
		$hotelId = JRequest::getVar('hotel_id');
		$msg = JText::_('LNG_REVIEW_PUBLISH_STATE',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=hotelratings&view=hotelratings&hotel_id='.$hotelId, $msg );
		parent::display();
	}

}


		