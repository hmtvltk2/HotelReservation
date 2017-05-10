<?php
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableHotelPaymentOptions extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function JTableHotelPaymentOptions(& $db) {

		parent::__construct('#__hotelreservation_hotel_payment_options', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getHotelPaymentOptions($hotelId){
		$query = "select * FROM #__hotelreservation_hotel_payment_options po
					  inner join  #__hotelreservation_hotel_payment_option_relation pr on po.id=pr.paymentOptionId
					  where pr.hotelId=$hotelId";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}


}