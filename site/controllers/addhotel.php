<?php
/**
 * @copyright	Copyright (C) 2008-2016 CMSJunkie. All rights reserved.
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

defined('_JEXEC') or die('Restricted access');

class JHotelReservationControllerAddHotel extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	function __construct()
	{
		JRequest::setVar('view','addhotel');
		parent::__construct();
	}

	//override controller methods

	function edit()
	{
		JRequest::setVar('view','addhotel');
		return $this->display();
	}
	
	function save(){
		$msg = $this->saveHotel();
		//method to send the email to the hotel admin
		$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
		//set the visibility to 0 by default
	}

	function saveHotel()
	{
		$model = $this->getModel('addhotel');
		$post = JRequest::get( 'post' );
		$post['hotel_description'] 			= JRequest::getVar('hotel_description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['hotel_selling_points'] 		= JRequest::getVar('hotel_selling_points', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$addHotelAdmin 						= JRequest::getVar('addHotelAdmin');
		
		if(strlen($post['hotel_website'])>1){
			$post['hotel_website']= str_replace("http://", "", $post['hotel_website'] );
			$post['hotel_website'] = "http://".$post['hotel_website'];
		}
		$post['hotel_name']= (string)($post['hotel_name']);

		$post['is_available'] = 0;
		$post['hotel_state'] = 0;
		$post['hotel_name'] = str_replace('"', "'", $post['hotel_name']);
		$captchaAnswer = !empty($post['recaptcha_response_field'])?$post['recaptcha_response_field']:$post['g-recaptcha-response'];

		$namespace="jhotelreservation.addhotel";
		$captcha = JCaptcha::getInstance("recaptcha", array('namespace' => $namespace));

		// check if captcha response is empty or not used at all
		// error shown if it is not set otherwise the saving will take place
		if(!$captcha->checkAnswer($captchaAnswer))
		{
			$msg = $this->setMessage( "Captcha error!", 'warning' );
			$this->setRedirect( 'index.php?option= '.getBookingExtName().'&view=addhotel', $msg);

		} else {

			if ( $model->store( $post ) )
			{
				$post["hotel_id"] = $model->_hotel_id;
				JRequest::setVar( 'hotel_id', $model->_hotel_id );
				$post["informationId"] = $model->_informationId;
				$model->saveHotelDescriptions( $post );
				$msg = JText::_( 'LNG_HOTEL_SAVED_AND_REQUEST_SENT', true );
				$model->sendEmailAddHotelRequest( $post['email'], $post['hotel_name'] );
				if($addHotelAdmin)
					$model->addHotelAdmin( $post['email'], $post['hotel_name'],$post["hotel_id"]);
			}
			else
			{
				$msg = "";
				JError::raiseWarning( 500, JText::_( 'LNG_ERROR_SAVING_HOTEL', true ) );
				$this->setRedirect( 'index.php?option=' . getBookingExtName(), '' );
			}
		}

		return $msg;
	}

	function cancel(){
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&view=hotels&task=hotels.searchHotels' );
	}
}