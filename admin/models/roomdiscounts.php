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
class JHotelReservationModelRoomDiscounts extends JModelList
{
    function __construct()
    {
        parent::__construct();
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
        foreach($items as $i=>$item){
            $item->discount_datas = JHotelUtil::convertToFormat($item->discount_datas);
            $item->discount_datae = JHotelUtil::convertToFormat($item->discount_datae);
            $translations = new	JHotelReservationLanguageTranslations();
            if(!empty($item->discount_room_ids)){
	            $roomIds = explode(",",$item->discount_room_ids);
	            if(!empty($roomIds)){
	            	$roomNames = array();
		            foreach($roomIds as $roomId){
		            	$translation = $translations->getObjectTranslation(ROOM_NAME,$roomId,JHotelUtil::getLanguageTag());
		            	if(!empty($translation))
		            		$roomNames[] = $translation->content;
		            }
		            if(!empty($roomNames))
		            	$item->discount_rooms = implode(",",$roomNames);
	            }
            }
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
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //Get All fields from the table
        $query->select($this->getState('list.select', 'hd.*'));
        $query->from($db->quoteName('#__hotelreservation_discounts') . 'AS hd');

        $query->select('hr.room_id,GROUP_CONCAT( hr.room_name ORDER BY hr.room_name ) AS discount_rooms');
        $query->join('LEFT',$db->quoteName('#__hotelreservation_rooms').'AS hr ON FIND_IN_SET(hr.room_id, hd.discount_room_ids)');

        //Join the currency table with the country table
        $query->select('hh.hotel_id');
        $query->join('LEFT', $db->quoteName('#__hotelreservation_hotels') . 'AS hh ON hh.hotel_id = hd.hotel_id ');


        $hotelId = $this->getState('filter.hotel_id');
        if (is_numeric($hotelId)) {
            $query->where('hd.hotel_id ='.(int) $hotelId);
        }

        $query->group('hd.discount_id');

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
    protected function populateState($ordering = null , $direction = null) {
        $app = JFactory::getApplication('administrator');

        $select = $this->getUserStateFromRequest($this->context.'.filter.select', 'filter_select');
        $this->setState('filter.select', $select);

        $statusId = $app->getUserStateFromRequest($this->context.'.filter.discount_id', 'filter_discount_id');
        $this->setState('filter.discount_id', $statusId);

        $statusId = $app->getUserStateFromRequest($this->context.'.filter.room_id', 'filter_room_id');
        $this->setState('filter.room_id', $statusId);

        $hotel_id = JRequest::getVar('hotel_id', null);

        if($hotel_id == 0 ){
            $hotel_id = $app->getUserState($this->context.'.filter.hotel_id');

            if (!$hotel_id) {
                $hotel_id = 0;
            }
        }
        $app->setUserState('com_jhotelreservation.roomdiscounts.filter.hotel_id', $hotel_id);
        $this->setState('filter.hotel_id', $hotel_id);

        // Check if the ordering field is in the white list, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
        $this->setState('list.ordering', $value);

        // Check if the ordering direction is valid, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
        $this->setState('list.direction', $value);

        // List state information.
        parent::populateState('hd.discount_id', 'desc');
    }
}