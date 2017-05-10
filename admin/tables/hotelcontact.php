<?php
/**
 * @copyright	Copyright (C) 2008-2012 CMSJunkie. All rights reserved.
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableHotelContact extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableHotelContact(& $db) {

		parent::__construct('#__hotelreservation_hotel_contacts', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getHotelContacts($hotelId){
		$query = "select * FROM #__hotelreservation_hotel_contacts where hotel_id= $hotelId";
		
		$this->_db->setQuery($query);
		$result =$this->_db->loadObject();
		
		if(empty($result)){
			$properties = $this->getProperties(1);
			$result = JArrayHelper::toObject($properties, 'JObject');
		}
	
		return $result;
	}
}
