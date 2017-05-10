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

class JTableRoom extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__hotelreservation_rooms', 'room_id', $db);
	}
	
	public function delete($pk = null, $children = false)
	{
		
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_rooms");
		$query->where('room_id = ' . (int)$pk);
		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_rooms_rates");
		$query->where('room_id = ' . (int)$pk);
		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_rooms_pictures");
		$query->where('room_id = ' . (int)$pk);
		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		
		
		return parent::delete($pk, $children);
	}

	/**
	 * @param $hotelId Hotel that rooms belong to
	 * @param $itemIds room id or ids to be checked if these room id(s) exist in an active reservation(s)
	 *
	 * @return mixed object list of the result
	 */
	public function checkRoomRecords($hotelId,$itemIds){
		$query = " 	SELECT  
						*  
					FROM #__hotelreservation_confirmations					c
					INNER JOIN #__hotelreservation_confirmations_rooms		r USING( confirmation_id )
					WHERE 
						r.room_id IN (".implode(',', $itemIds).")
						AND
						c.hotel_id IN (".$hotelId.")
						AND 
						c.reservation_status NOT IN (".CANCELED_ID.", ".CHECKEDOUT_ID." )
					";
		$this->_db->setQuery( $query );
		$checked_records = $this->_db->loadObjectList();
		return $checked_records;
	}
	
	/**
	 * Method to run an update query and check for a database error
	 *
	 * @param   string  $query         The query.
	 * @param   string  $errorMessage  Unused.
	 *
	 * @return  boolean  False on exception
	 *
	 * @since   11.1
	 */
	protected function _runQuery($query, $errorMessage)
	{
		$this->_db->setQuery($query);
	
		// Check for a database error.
		if (!$this->_db->execute())
		{
			$e = new JException($this->_db->getErrorMsg());
			$this->setError($e);
			$this->_unlock();
			return false;
		}
		if ($this->_debug)
		{
			$this->_logtable();
		}
	}
	//get last order id +1
	function getRoomOrder(){
			$query = " SELECT (max(ordering)+1) as roomOrder  FROM #__hotelreservation_rooms";
			//dmp($query);
			$this->_db->setQuery( $query );
			return $this->_db->loadObject()->roomOrder;
	}

}