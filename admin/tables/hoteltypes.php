<?php
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableHotelTypes extends JTable
{

		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableHotelTypes(& $db) {

		parent::__construct('#__hotelreservation_hotel_types', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

}

?>