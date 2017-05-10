<?php
/**
 * @copyright	Copyright (C) 2008-2012 CMSJunkie. All rights reserved.
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableHotelInformations extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function JTableHotelInformations(& $db) {

		parent::__construct('#__hotelreservation_hotel_informations', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getHotelInformations($hotelId){
		$query = "select * FROM #__hotelreservation_hotel_informations where hotel_id=$hotelId";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}
	
	function getHotelPaymentOptions($hotelId){
		$query = "select * FROM #__hotelreservation_hotel_payment_options hpo
		inner join #__hotelreservation_hotel_payment_option_relation hpor on hpo.id=hpor.paymentOptionId 
		where hotelId=$hotelId";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
