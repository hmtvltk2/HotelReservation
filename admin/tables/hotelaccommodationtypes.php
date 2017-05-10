<?php
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableHotelAccommodationTypes extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableHotelAccommodationTypes(& $db) {

		parent::__construct('#__hotelreservation_hotel_accommodation_types', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}
	
	function getAccommodationTypes(){
		$db =JFactory::getDBO();
		$query = "select id as value, name as text from  #__hotelreservation_hotel_accommodation_types order by 2";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}

?>