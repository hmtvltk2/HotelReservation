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

class TableOfferRate extends JTable
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
		parent::__construct('#__hotelreservation_offers_rates', 'id', $db);
	}
	
	//get offer rate plan for cubilis
	function getRoomOfferRatePlans($roomId){
		$db= JFactory::getDBO();
		$query="select rr.id offer_rate_id, concat(r.room_name , '-' , of.offer_name) offer_plan_name
				from #__hotelreservation_rooms r
				inner join #__hotelreservation_offers_rates rr on r.room_id = rr.room_id
				inner join #__hotelreservation_offers of on of.offer_id = rr.offer_id
				where r.room_id =  $roomId and of.is_available=1";
		$db->setQuery( $query );
		$roomsOfferPlans =  $db->loadObjectList();

		return $roomsOfferPlans;
	}
	
}