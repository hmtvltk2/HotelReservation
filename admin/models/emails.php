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


jimport('joomla.application.component.modellist');

/**
 * List Model.
 *
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation
 */
class JHotelReservationModelEmails extends JModelList
{
	function __construct()
	{
		parent::__construct();
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
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //Get All fields from the table
        $query->select($this->getState('list.select', 'he.*'));
        $query->from($db->quoteName('#__hotelreservation_emails') . 'AS he');

        //Join the currency table with the country table
        $query->select('hh.hotel_id');
        $query->join('LEFT', $db->quoteName('#__hotelreservation_hotels') . 'AS hh ON hh.hotel_id = he.hotel_id ');

        $hotelId = $this->getState('filter.hotel_id');
        if (is_numeric($hotelId)) {
            $query->where('he.hotel_id ='.(int) $hotelId);
        }

        $query->group('he.email_id');

        return $query;
    }

    /**Method to get all Hotels from Hotels Table
     * @return mixed
     */
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}

    /**
     * Method to get the Hotel Ids
     * @return mixed
     */
    function getHotelId()
    {
        $hotel_id = JRequest::getVar('hotel_id',  0, '');
        $this->setHotelId($hotel_id);
        return $hotel_id;
    }


    /**
     * Method to set the hotel Ids
     * @param $hotel_id
     */
    function setHotelId($hotel_id)
    {
        // Set id and wipe data
        $this->_hotel_id = $hotel_id;
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
    protected function populateState($ordering = null , $direction = null) {
        $app = JFactory::getApplication('administrator');

        $select = $this->getUserStateFromRequest($this->context.'.filter.select', 'filter_select');
        $this->setState('filter.select', $select);

        $statusId = $app->getUserStateFromRequest($this->context.'.filter.email_id', 'filter_email_id');
        $this->setState('filter.email_id', $statusId);

        $stateId = $app->getUserStateFromRequest($this->context.'.filter.hotel_id', 'hotel_id');
        $this->setState('filter.hotel_id', $stateId);

        // Check if the ordering field is in the white list, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
        $this->setState('list.ordering', $value);

        // Check if the ordering direction is valid, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
        $this->setState('list.direction', $value);

        // List state information.
        parent::populateState('he.email_id', 'desc');
    }
}
