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

class TablePaymentProcessor extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db){

		parent::__construct('#__hotelreservation_payment_processors', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}


	function getPaymentProcessor($id){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_payment_processors where id=".$id;
		$db->setQuery($query);
		//dmp($query);
		return $db->loadObject();
	}
	
	
	function getPaymentProcessorFields($processorId){
		$query = " SELECT * FROM #__hotelreservation_payment_processor_fields where processor_id=$processorId";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	function getPaymentProcessors(){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_payment_processors where status=1 ";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	
	function changeState($id){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__hotelreservation_payment_processors SET status = IF(status, 0, 1) WHERE id = ".$id ;
		$db->setQuery( $query );

		if (!$db->query()){
			return false;
		}
		return true;
	}
	
	function changeFrontState($id){
		$db =JFactory::getDBO();
		$query = 	" UPDATE #__hotelreservation_payment_processors SET displayfront = IF(displayfront, 0, 1) WHERE id = ".$id ;
		$db->setQuery( $query );
	
		if (!$db->query()){
			return false;
		}
		return true;
	}
}