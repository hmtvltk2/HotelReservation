<?php


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Menu List Model for Rooms.
 *
 */
class JHotelReservationModelRooms extends JModelList
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
				'room_id', 'r.room_id',
				'ordering', 'r.ordering',
				'room_name', 'r.room_name',
			);
		}

		parent::__construct($config);
	}

	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
	
		$published = $this->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);
	
		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', 0, 'int');
		$this->setState('filter.type', $type);
		
		$hotel_id = JRequest::getVar('hotel_id', null);
		if ($hotel_id) {
			if ($hotel_id != $app->getUserState($this->context.'.filter.hotel_id')) {
				$app->setUserState($this->context.'.filter.hotel_id', $hotel_id);
				JRequest::setVar('limitstart', 0);
			}
		}
		else {
			$hotel_id = $app->getUserState($this->context.'.filter.hotel_id');
	
			if (!$hotel_id) {
				$hotel_id = 0;
			}
		}
		$app->setUserState('com_jhotelreservation.rooms.filter.hotel_id', $hotel_id);
		$this->setState('filter.hotel_id', $hotel_id);
		
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);
		
		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);
		
		JRequest::setVar("limit",0);
		
		//dmp($hotel_id);
		// List state information.
		parent::populateState('r.ordering', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.

		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.hotel_id');
	
		return parent::getStoreId($id);
	}
	
	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();
		
		// Getting the following metric by joins is WAY TOO SLOW.
		// Faster to do three queries for very large menu trees.

		// If emtpy or an error, just return.
		if (empty($items))
		{
			return array();
		}
		

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	public function getModel($name = 'RoomRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select all fields from the table.
		$query->select($this->getState('list.select', 'r.*'));
		$query->from($db->quoteName('#__hotelreservation_rooms').' AS r');

		$query->select('rp.room_picture_path');
		$query->join( 'LEFT', $db->quoteName('#__hotelreservation_rooms_pictures').' as rp on rp.room_id = r.room_id');

		// Join over currency
		$query->select('hrr.id as rate_id,hrr.base_children');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_rooms_rates').' AS hrr ON hrr.room_id=r.room_id');
		
		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('r.is_available = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(r.is_available IN (0, 1))');
		}
		
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('r.hotel_id = '.$db->quote($hotelId));
		}
		
		$query->group('r.room_id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'r.ordering')).' '.$db->escape($this->getState('list.direction', 'DESC')));
		return $query;
	}
	
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}

    /**
     * Method to adjust the ordering of a row.
     *
     * @param	int		The ID of the primary key to move.
     * @param	integer	Increment, usually +1 or -1
     * @return	boolean	False on failure or error, true otherwise.
     */
    public function reorder($pk, $direction = 0)
    {
        // Sanitize the id and adjustment.
        $pk	= (!empty($pk)) ? $pk : (int) $this->getState('room.room_id');
        $user = JFactory::getUser();

        // Get an instance of the record's table.
        $table = JTable::getInstance('Room','JTable');

        // Load the row.
        if (!$table->load($pk)) {
            $this->setError($table->getError());
            return false;
        }

        // Access checks.
        $allow = true; //$user->authorise('core.edit.state', 'com_users');

        if (!$allow)
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED',true));
            return false;
        }

        // Move the row.
        // TODO: Where clause to restrict category.
        $table->move($pk);

        return true;
    }

    /**
     * Saves the manually set order of records.
     *
     * @param	array	An array of primary key ids.
     * @param	int		+/-1
     */
    function saveorder($pks, $order)
    {
        // Initialise variables.
        $table		= JTable::getInstance('Room','JTable');
        $user 		= JFactory::getUser();
        $conditions	= array();

        if (empty($pks)) {
            return JError::raiseWarning(500, JText::_('COM_USERS_ERROR_LEVELS_NOLEVELS_SELECTED',true));
        }

        // update ordering values
        foreach ($pks as $i => $pk)
        {
            $table->load((int) $pk);

            // Access checks.
            $allow = true;//$user->authorise('core.edit.state', 'com_users');

            if (!$allow)
            {
                // Prune items that you can't change.
                unset($pks[$i]);
                JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED',true));
            }
            elseif ($table->ordering != $order[$i])
            {
                $table->ordering = $order[$i];
                if (!$table->store())
                {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond)
        {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        return true;
    }
}
