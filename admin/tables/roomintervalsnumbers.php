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

class JTableRoomIntervalsNumbers extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function JTableRoomIntervalsNumbers(& $db) {

		parent::__construct('#__hotelreservation_rooms_intervals_numbers', 'room_interval_number_id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

}