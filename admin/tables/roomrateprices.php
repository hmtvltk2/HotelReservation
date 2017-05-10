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

class JTableRoomRatePrices extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct($db)
	{
		parent::__construct('#__hotelreservation_rooms_rate_prices', 'id', $db);
	}
	
	function getRateDetails($rateId, $date){
		$query = " SELECT *  FROM #__hotelreservation_rooms_rate_prices where rate_id = $rateId and date='$date'";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

}