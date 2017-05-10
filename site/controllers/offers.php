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
jimport('joomla.application.component.controller');
JHTML::_('stylesheet','components/'.getBookingExtName().'/assets/style/specialoffers.css');
require_once( JPATH_COMPONENT_SITE.DS.'views'.DS.'offer'.DS.'view.html.php' );
require_once( JPATH_COMPONENT_SITE.DS.'views'.DS.'listoffers'.DS.'view.html.php' );
class JHotelReservationControllerOffers extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	
	}

	function searchOffers(){
		
		$model 	= $this->getModel('offers');
		$offers = $model->getOffers();

		$view = $this->getView("ListOffers");
		$view->offers = $offers;
		$mediaReferer = JRequest::getVar('mediaReferer');
		if(!isset($mediaReferer))
			$mediaReferer = '';
		$view->mediaReferer = $mediaReferer;

		$voucher = JRequest::getVar('voucher','');
		$view->voucher = $voucher;
	
		$view->setModel( $model, true );
		$view->display(); 
	} 
	
}