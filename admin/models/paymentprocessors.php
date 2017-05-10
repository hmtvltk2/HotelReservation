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
class JHotelReservationModelPaymentProcessors extends JModelList
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
				'id', 'p.id',
				'name', 'p.name',
				'type','p.type',
				'ordering', 'p.ordering',
				'displayfront', 'p.displayfront',
				'status', 'p.status',
				'userGroup', 'p.userGroup'
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
		
		$table = JTable::getInstance("PaymentProcessorFields", "Table");
		foreach($items as $item){
			$item->processorFields= $table->getPaymentProcessorFields($item->id);
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
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'p.*'));
		$query->from($db->quoteName('#__hotelreservation_payment_processors').' AS p');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$query->where("p.name LIKE '%".$search."%'");
		}
		
		$statusId = $this->getState('filter.status_id');
		if (is_numeric($statusId)) {
			$query->where("p.status =".(int) $statusId);
		}
		
		$hotelId = $this->getState('filter.hotel_id');
		if (intval($hotelId)>0) {
			$query->where("p.hotel_id =".(int) $hotelId);
		}
		else 
		{
			$query->where("p.hotel_id =-1");
		}
		
		
		$query->group('p.id');

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'p.id')).' '.$db->escape($this->getState('list.direction', 'ASC')));

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

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$statusId = $app->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
		$this->setState('filter.status_id', $statusId);
	
		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);
	
		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);
		
		$hotel_id = JRequest::getVar('hotel_id', null);
		if(empty($hotel_id)){
			$hotel_id = $app->getUserState($this->context.'.filter.hotel_id');
			if (!$hotel_id) {
				$hotel_id = 0;
			}
		}
		$this->setState('filter.hotel_id', $hotel_id);
		$app->setUserState($this->context.'.filter.hotel_id', $hotel_id);
		
		// List state information.
		parent::populateState('p.id', 'desc');
	}
	
	function getStates(){
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JText::_('LNG_INACTIVE',true);
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JText::_('LNG_ACTIVE',true);
		$states[] = $state;
	
		return $states;
	}
	
	/**
	 * Method to adjust the ordering of a row.
	 *
	 * @param   integer  The ID of the primary key to move.
	 * @param   integer	Increment, usually +1 or -1
	 * @return  boolean  False on failure or error, true otherwise.
	 */
	public function reorder($pk, $direction = 0)
	{
		// Sanitize the id and adjustment.
		$pk	= (!empty($pk)) ? $pk : (int) $this->getState('paymentprocessor.id');
		$user = JFactory::getUser();
	
		// Get an instance of the record's table.
		$table = JTable::getInstance('PaymentProcessor');
	
		// Load the row.
		if (!$table->load($pk))
		{
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
	 * @param   array  An array of primary key ids.
	 * @param   integer  +/-1
	 */
	public function saveorder($pks, $order)
	{
		$table		= JTable::getInstance('PaymentProcessor');
		$user 		= JFactory::getUser();
		$conditions	= array();
	
		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_('COM_USERS_ERROR_LEVELS_NOLEVELS_SELECTED',true));
		}
	
		// update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);
	
			// Access checks.
			$allow = true; //$user->authorise('core.edit.state', 'com_users');
	
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
	function getHotels()
	{
		// get all hotels
		if (empty($this->_hotels ))
		{
			$table	= JTable::getInstance('hotels','Table');
			$hotels = $table->getAllHotels();
			$this->_hotels = $hotels;
		}
		return $this->_hotels;
	}
	
}
