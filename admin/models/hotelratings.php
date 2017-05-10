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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model'); 

class JHotelReservationModelHotelRatings extends JModelList
{ 
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JHOTELRESERVATION';

	/**
	 * The type alias for this content type. Used for content version history.
	 *
	 * @var      string
	 * @since    3.2
	 */


	/**
	 * Override parent constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelLegacy
	 * @since   3.2
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'c.review_id');
		}

		parent::__construct($config);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object   $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		return true;
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param   object   $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		return true;
	}



	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */


    /**
     * Method to get All hotel datas from the Hotel Table
     * @return mixed
     */
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}

    /**
     * Method to get hotel data from the Hotel table
     * based on Hotel Id selection
     * @return mixed
     */
	function getHotel(){
		$hotelId = JRequest::getVar('hotel_id');
		$hotelTable = $this->getTable('hotels');
		$hotelTable->load($hotelId);
		return $hotelTable;
	}

    /**
     * Method to get the hotel review from the Table
     * based on hotel id selection
     * @return null
     */

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$context = $this->context;

		$hotelId = $this->getUserStateFromRequest($context . '.hotel_id', 'hotel_id');
		$this->setState('filter.hotelId', $hotelId);

		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);
		
		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		// List state information.
		parent::populateState('c.review_id', 'desc');
	}
	
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'concat(d.first_name," ",d.last_name) as clientsName,c.review_short_description,c.review_remarks,c.published,c.review_id'
			)
		);
		$query->from('#__hotelreservation_review_customers AS c');
		$query->join("inner","#__hotelreservation_confirmations d on c.confirmation_id = d.confirmation_id" );
		$query->where('d.hotel_id=' . (int) $this->getState('filter.hotelId', 'ASC'));
	
		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'c.review_id');
		$listDirn = $db->escape($this->getState('list.direction', 'DESC'));

		$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		
		return $query;
	}
	

    /**
     * Method to Store data
     * @param $data
     * @return bool
     */
	function store($data)
	{	
		$row = $this->getTable();

		// Bind the form fields to the table
		if (!$row->bind($data)) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		// Make sure the record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}


    /**
     * Method to change the state of one Review
     */
	function changeState()
	{
		$reviewId = JRequest::getVar('review_id');
		$hotelId = JRequest::getVar('hotel_id');
		$reviewCustomersTable = $this->getTable('reviewcustomers');
		$reviewCustomersTable->setPublished($reviewId);
		$reviewCustomersTable->calculateHotelRatingScore($hotelId);
	}

    /**
     * Method to delete a Hotel Rating
     */
	function deleteHotelRating(){
		$confirmationId = JRequest::getVar('confirmation_id',-1);
		
		$reviewCustomersTable = $this->getTable('reviewcustomers');
		$review = $reviewCustomersTable->getReviewByCofirmation($confirmationId);
	
		if($review->review_id == 0)
			return;
		$reviewAnswersTable = $this->getTable('reviewanswers');
		$reviewAnswersTable->deleteByReview($review->review_id);
		$this->recalculateReviewScore($review->hotel_id);
		$reviewCustomersTable->delete($review->review_id);
	}

    /**
     * Method to recalculate the Review Score
     * Calls the calculateHotelRatingScore function from the table
     * @param $hotelId the Id of the hotel that the calculations are done
     */
	function recalculateReviewScore($hotelId)
	{
		$reviewCustomersTable = $this->getTable('reviewcustomers');
		$reviewCustomersTable->calculateHotelRatingScore($hotelId);
	}
	
}
