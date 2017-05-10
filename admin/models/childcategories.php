<?php
/**
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

/**
 * List Model.
 *
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation
 */
class JHotelReservationModelChildCategories extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'eo.id',
				'title', 'eo.name',
				'oredering', 'eo.ordering'
			);
		}

		parent::__construct($config);
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

		// If emtpy or an error, just return.
		if (empty($items))
		{
			return array();
		}
		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
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
		// Create a new query objeeo.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'eo.*'));
		
		$query->from($db->quoteName('#__hotelreservation_children_categories').' AS eo');
		
		
		// Filter on the published state.
		$published = $this->getState('filter.status_id');
		if (is_numeric($published)) {
			$query->where('eo.status = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(eo.status IN (0, 1))');
		}
		
		// Filter the items over the menu id if set.
		$hotelId = $this->getState('filter.hotel_id');
		if (!empty($hotelId)) {
			$query->where('eo.hotel_id = '.$db->quote($hotelId));
		}else{
			$query->where('eo.hotel_id = -2');
		}
		
		$query->group('eo.id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'eo.id')).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
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
		$app = JFactory::getApplication('administrator');

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);	

		//dmp($this->context);
		$hotel_id = JRequest::getVar('hotel_id', null);
		if($hotel_id == 0 ){
			$hotel_id = $app->getUserState($this->context.'.filter.hotel_id');
		
			if (!$hotel_id) {
				$hotel_id = 0;
			}
		}
		
		$layout = JRequest::getVar('layout', null);
		if(!isset($layout)){
			$app->setUserState($this->context.'.filter.hotel_id', $hotel_id);
		}
		$this->setState('filter.hotel_id', $hotel_id);
		
		
		$status_id = JRequest::getVar('status_id', null);
		$this->setState('filter.status_id', $status_id);
		
		
		// List state information.
		parent::populateState('eo.id', 'desc');
	}

	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param	int		The ID of the primary key to move.
	 * @param	integer	Increment, usually +1 or -1
	 * @return	boolean	False on failure or error, true otherwise.
	 */
	
	
	function getHotels()
	{
		// get all hotels
		if (empty( $this->_hotels ))
		{
			$table		= JTable::getInstance('hotels','Table');
			$hotels = $table->getAllHotels();
			$this->_hotels = $hotels;
		}
		return $this->_hotels;
	}
}
