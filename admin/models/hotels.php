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
class JHotelReservationModelHotels extends JModelList
{
	function __construct()
	{
		if (empty($config['filter_fields']))
			{
				$config['filter_fields'] = array(
					'id', 'h.hotel_id',
					'featured', 'h.featured',
					'country', 'hc.country_name',
					'city', 'h.hotel_city',
					'available', 'h.is_available',
                    'hotel_phone','h.hotel_phone',
                    'email','h.email'
				);
			}

		parent::__construct($config);		
	}

	public function getTable($type = 'Hotels', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
	
		// Select all fields from the table.
		$query->select($this->getState('list.select', 'h.*'));
		$query->from($db->quoteName('#__hotelreservation_hotels').' AS h');

		$query->select('hp.hotel_picture_path');
		$query->join( 'LEFT',$db->quoteName( '#__hotelreservation_hotel_pictures' ).' as hp on hp.hotel_id = h.hotel_id');

		// Join over countries
		$query->select('hc.country_name');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_countries').' AS hc ON h.country_id=hc.country_id');
	
		// Join over currency
		$query->select('hcr.description as hotel_currency');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_currencies').' AS hcr ON h.currency_id=hcr.currency_id');
		
		// Join over accomadation types
		$query->select('hatr.accommodationtypeId ');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_hotel_accommodation_type_relation').' AS hatr ON h.hotel_id=hatr.hotelid');
		$query->select('hat.name as accommodation_type');
		$query->join('LEFT', $db->quoteName('#__hotelreservation_hotel_accommodation_types').' AS hat ON hat.id=hatr.accommodationtypeId');


        $typeId = $this->getState('filter.accommodationtypeId');
        if (is_numeric($typeId)) {
            $query->where('hatr.accommodationtypeId ='.(int) $typeId);
        }
        $statusId = $this->getState('filter.status_id');
        if (is_numeric($statusId)) {
            $query->where('h.is_available ='.(int) $statusId);
        }
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
            $query->where(" (h.hotel_name LIKE lower('%" . $db->escape($search) . "%') or hc.country_name LIKE lower('%" . $db->escape($search) . "%') or h.hotel_city LIKE lower('%" . $db->escape($search) . "%'))");
        }

	
		$query->group('h.hotel_id');
	
		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'hotel_name')).' '.$db->escape($this->getState('list.direction', 'ASC')));
	
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
	
		$typeId = $app->getUserStateFromRequest($this->context.'.filter.accommodationtypeId', 'filter_accommodationtypeId');
		$this->setState('filter.accommodationtypeId', $typeId);
	
		$statusId = $app->getUserStateFromRequest($this->context.'.filter.status_id', 'filter_status_id');
		$this->setState('filter.status_id', $statusId);
			
		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);
	
		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);

		$limit = JRequest::setVar("limit",0, '', 'int');
        $this->setState("limit",$limit);
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
		
		// List state information.
		parent::populateState('h.hotel_name', 'asc');
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	
	function getAccommodationTypes(){
		$typesTable = $this->getTable("HotelAccommodationTypes");
		return $typesTable->getAccommodationTypes();
	}

	function remove($cids)
	{
		try
		{
			$this->_db->BeginTrans();
			$row = $this->getTable();

			if (count( $cids )) {
				foreach($cids as $cid)
				{
					if (!$row->delete( $cid ))
					{
						$this->setError( $row->getErrorMsg() );
						throw( new Exception($this->_db->getErrorMsg()) );
					}
						
					$query = 	" DELETE FROM #__hotelreservation_rooms WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
						
					$query = 	" DELETE FROM #__hotelreservation_taxes WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
						
					$query = 	" DELETE FROM #__hotelreservation_paymentsettings WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
						
					$query = 	" DELETE FROM #__hotelreservation_discounts WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					$query = 	" DELETE FROM #__hotelreservation_emails WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
						throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_airport_transfer_types WHERE hotel_id = ".$cid ;
										$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_extra_options WHERE hotel_id = ".$cid ;
										$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_accommodation_type_relation WHERE hotelId = ".$cid ;
										$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_channel_manager WHERE hotel_id = ".$cid ;
										$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_environment_relation WHERE hotelId = ".$cid ;
										$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_facility_relation WHERE hotelId = ".$cid ;
										$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_informations WHERE hotel_id = ".$cid ;
										$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_payment_option_relation WHERE hotelId = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_pictures WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_region_relation WHERE hotelId = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_hotel_type_relation WHERE hotelId = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_language_translations WHERE type =1 and object_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					
					$query = 	" DELETE FROM #__hotelreservation_user_hotel_mapping WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
					$query = 	" DELETE FROM #__hotelreservation_review_customers WHERE hotel_id = ".$cid ;
					$this->_db->setQuery( $query );
					if (!$this->_db->query())
					{
					throw( new Exception($this->_db->getErrorMsg()) );
					}
				}
			}
				
			$this->_db->CommitTrans();
		}
		catch( Exception $ex )
		{
			dmp($ex);
			exit ;
			$this->_db->RollbackTrans();
			return false;
		}

		return true;


	}

	function state($hotelId)
	{
		//$hotelId = JRequest::getVar('hotel_id');
		$query = 	" UPDATE #__hotelreservation_hotels SET is_available = IF(is_available, 0, 1) WHERE hotel_id = ".$hotelId;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;
				
		}
		return true;
	}

	function changeFeaturedState($hotelId)
	{
		//$hotelId = JRequest::getVar('hotel_id');
		$query = 	" UPDATE #__hotelreservation_hotels SET featured = IF(featured, 0, 1) WHERE hotel_id = ".$hotelId;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}

	function updateHotelShortDescription(){
	
		$query = "SELECT * FROM #__hotelreservation_hotels h";
		
		$this->_db->setQuery( $query );
		$hotels =  $this->_db->loadObjectList();
		
		foreach($hotels as $hotel){
			$row = $this->getTable();
			
			$description = $hotel->hotel_short_description; 
			$description = strip_tags($description);
			$pos = strrchr ($description , "." );
			$description = substr($description, 0,$pos);
			
			$hotel->hotel_short_description = $description;
		}
		
	}
	
	function fixHotelsDates(){
		$query = "SELECT * FROM #__hotelreservation_hotels h";
		
		$this->_db->setQuery( $query );
		$hotels =  $this->_db->loadObjectList();
		$row = $this->getTable();
		
		foreach($hotels as $hotel){
			$hotel->ignored_dates = JHotelUtil::formatToDefaultDate($hotel->ignored_dates);
			dmp($hotel->hotel_id." ".$hotel->ignored_dates);
			$query = "update #__hotelreservation_hotels set ignored_dates = '".$hotel->ignored_dates."' where hotel_id = $hotel->hotel_id";
			$this->_db->setQuery( $query );
			$this->_db->query();
		}
	}


}
?>