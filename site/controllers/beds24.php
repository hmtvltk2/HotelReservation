<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 CMSJunkie,  All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport( 'joomla.application.component.controller' );

require_once JPATH_COMPONENT.DS.'classes/beds24/beds24xml.php';
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'roomrateprices.php');
/**
 * The Beds24 Controller
 *
 */
class JHotelReservationControllerBeds24 extends JControllerLegacy
{
	
	function __construct()
	{
		$this->log = Logger::getInstance(JPATH_COMPONENT."/logs/site-log-".date("d-m-Y").'.log',1);
		
		parent::__construct();
	}
	/**
	 * Display the view
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.

	 */
	public function display($cachable = false, $urlparams = false)
	{
	}

	public function getRoomsAvailability(){
		$this->log->LogDebug("Beds24 :: call function getRoomsAvailability()");
		$beds24Xml = new Beds24Xml();
		$beds24Xml->getRoomsAvailability();
	}
	
	public function sendReservations(){
		$this->log->LogDebug("Beds24 :: call function sendReservations()");
		$beds24Xml = new Beds24Xml();
		$beds24Xml->sendReservations();
	}
}
