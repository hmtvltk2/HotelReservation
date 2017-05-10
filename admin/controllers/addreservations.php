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
require_once JPATH_COMPONENT_SITE.DS.'models'.DS.'variables.php'; 
require_once JPATH_COMPONENT_SITE.DS.'models'.DS.'confirmations.php'; 
require_once( JPATH_COMPONENT_SITE.DS.'include'.DS.'search'.DS.'searchcriteria.php' );

JHTML::_('script', 						'components/'.getBookingExtName().'/assets/datepicker/js/datepicker.js');
JHTML::_('script', 								'components/'.getBookingExtName().'/assets/datepicker/js/eye.js');
JHTML::_('script', 							'components/'.getBookingExtName().'/assets/datepicker/js/utils.js');
JHTML::_('script', 							'components/'.getBookingExtName().'/assets/datepicker/js/layout.js');



class JHotelReservationControllerAddReservations extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'next_step', 'next_step');  
	}
	
	function next_step()
	{
		$post = JRequest::get( 'post' );
		// dmp($post);

		/*$session = JFactory::getSession();
		$searchCriteria =  $session->get('searchCriteria');
		
		if(!isset($searchCriteria)){
			$searchCriteria = new stdClass();
			$searchCriteria = initializeSearchParams($searchCriteria, null);
			$session->set('searchCriteria',$searchCriteria);
		}*/
		
		
		JRequest::setVar( 'tip_oper', $post['tip_oper'] ); 
		JRequest::setVar( 'view', 	$post['view'] ); 
		JRequest::setVar( 'task', 	$post['view'] ); 
		// exit;

	
		parent::display();  
	}
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	 
}


