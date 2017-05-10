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

class JHotelReservationControllerHotelRatings extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}
	
	
	function display(){
		parent::display();
	}
	function submitReview(){
		
		$model = $this->getModel('hotelratings12');
		$model->submitReview();
		$msg = "Thank you for your review. Your feedback is appreciated.";
		$confirmationId = JRequest::getVar('confirmation_id');
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=hotelratings12&view=hotelratings12&submitreviewform=true&confirmation_id='.$confirmationId, $msg );
	}
	
	function printrating(){
		JRequest::setVar('layout','printrating');
		$reviewId= JRequest::getVar('review_id');
		if(isset($reviewId)){
			$model = $this->getModel('hotelratings12');
			$tableReview = $model->getReview($reviewId);
			$tableReview->load($reviewId);
			JRequest::setVar('confirmation_id',$tableReview->confirmation_id);
			$this->customerReview = $tableReview;
		}
		parent::display();
	}
	
	

}