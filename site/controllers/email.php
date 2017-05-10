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

class JHotelReservationControllerEmail extends JController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		//$this->registerTask( 'cancel'  , 	'cancel' );
		//$this->registerTask( 'save'  , 	'save' );
	}
	
	
	function display(){
		
	}
	
	function sendEmail(){
		
		//$post = JRequest::get('post');
		//$result = $model->sendHotelEmail($post);
		
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<email_statement>';
		//echo '<answer result="'.$result.'"/>';
		echo '</email_statement>';
		echo '</xml>';
		exit;

		
	}
}