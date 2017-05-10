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

class JTableExcursion extends JTable
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
		parent::__construct('#__hotelreservation_excursions', 'id', $db);
	}
	
	public function delete($pk = null, $children = false)
	{
		
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_excursions");
		$query->where('id = ' . (int)$pk);
		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_excursion_rates");
		$query->where('excursion_id = ' . (int)$pk);
		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from("#__hotelreservation_excursion_pictures");
		$query->where('excursion_id = ' . (int)$pk);
		$this->_runQuery($query, 'JLIB_DATABASE_ERROR_DELETE_FAILED');
		
		
		return parent::delete($pk, $children);
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
	
	function getExcursionOrder(){
		$query = " SELECT max(ordering)+1 as excursionOrder  FROM #__hotelreservation_excursions";
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->loadObject()->excursionOrder;
	}

}