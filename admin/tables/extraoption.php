<?php
/*------------------------------------------------------------------------
# JBusinessDirectory
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableExtraOption extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct($db){

		parent::__construct('#__hotelreservation_extra_options', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}


	function getDisplayExtras($hotelId, $offerId) {

		$db= JFactory::getDBO();
		$query = "select GROUP_CONCAT( DISTINCT eo.id) as ids
	    		  from #__hotelreservation_extra_options eo
	    		  where eo.status = 1 AND eo.hotel_id  = ".$hotelId." 
					and find_in_set(".$offerId.",eo.offer_ids) ";
		$db->setQuery( $query );
		$result = $db->loadObject();
		return $result->ids;
	}


	function getOldDisplayExtras($hotelId, $offerId) {

		$db= JFactory::getDBO();
		$query = "select eo.id,eo.offer_ids
	    		  from #__hotelreservation_extra_options eo
	    		  where eo.status = 1 AND eo.hotel_id  = ".$hotelId." 
					and find_in_set('".$offerId."',eo.offer_ids) 
					group by eo.id ";
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		return $result;
	}

	function getExtraOfferIds($hotelId, $extraId) {
		$db= JFactory::getDBO();
		$query = "select eo.offer_ids
	    		  from #__hotelreservation_extra_options eo
	    		  where eo.status = 1 AND eo.hotel_id  = ".$hotelId." 
					and eo.id = ".$extraId;
		$db->setQuery( $query );
		$result = $db->loadObject();
		return $result->offer_ids;
	}

}