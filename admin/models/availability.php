<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once 'rooms.php';
/**
 * Menu List Model for Rooms.
 *
 */
class JHotelReservationModelAvailability extends JHotelReservationModelRooms
{
	/**
	 * Constructor.
	 *
	 * @param   array	An optional associative array of configuration settings.
	 *
	 * @see		JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				
			);
		}

		parent::__construct($config);
	}
	
	
	public function saveHotelAvailability($data){
		$row = $this->getTable('hotels');
			
		if (!$row->bind($data))
		{
			throw( new Exception($this->_db->getErrorMsg()) );
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			throw( new Exception($this->_db->getErrorMsg()) );
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			throw( new Exception($this->_db->getErrorMsg()) );
			$this->setError( $this->_db->getErrorMsg() );
			 return false;
		}
		
		return true;
	}
}
