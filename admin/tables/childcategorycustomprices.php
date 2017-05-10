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

class JTableChildCategoryCustomPrices extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct($db){

		parent::__construct('#__hotelreservation_children_categories_rate_prices', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}
	function getRateDetails($rateId, $date,$categoryId){
		$query = " SELECT *  FROM #__hotelreservation_children_categories_rate_prices where rate_id = $rateId and date='$date' and category_id='$categoryId'";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}
	
}