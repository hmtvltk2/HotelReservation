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

class JTableOffersViews extends JTable
{

	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function JTableOffersViews(& $db) {

		parent::__construct('#__hotelreservation_offers_views', 'id', $db);
	}
	
	function increaseViewCount($offer_id, $media_referer, $voucher){
		$db =JFactory::getDBO();
		
		$query = "insert into #__hotelreservation_offers_views(offer_id, media_referer, voucher, view_count) values ($offer_id, '$media_referer','$voucher',1)
					ON DUPLICATE KEY UPDATE	view_count = view_count+1
				 " ;
		//dmp($query);
		$db->setQuery($query);
		$db->query();
	}
	
	
}