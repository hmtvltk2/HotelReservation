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

//jimport('joomla.application.component.model');
jimport('joomla.application.component.modellist');

class JHotelReservationModelOffers extends JModelList
{ 
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'offer_id', 'ho.offer_id',
					'name', 'ho.offer_name',
					'title', 'a.title',
					'menutype', 'a.menutype',
                    'ordering','ho.ordering'
			);
		}
		parent::__construct($config);
	}
	
	function &getHotelId()
	{
		return $this->_hotel_id;
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
	
		$published = $this->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);
	
		$type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', 0, 'int');
		$this->setState('filter.type', $type);

        // Check if the ordering field is in the white list, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
        $this->setState('list.ordering', $value);

        // Check if the ordering direction is valid, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
        $this->setState('list.direction', $value);

        $hotel_id = JRequest::getVar('hotel_id', null);

        if($hotel_id == 0 ){
            $hotel_id = $app->getUserState($this->context.'.filter.hotel_id');

            if (!$hotel_id) {
                $hotel_id = 0;
            }
        }
		$app->setUserState('com_jhotelreservation.offers.filter.hotel_id', $hotel_id);
        $this->setState('filter.hotel_id', $hotel_id);


		// List state information.
		parent::populateState('ho.offer_id', 'desc');
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
		if(isManager( JFactory::getUser()->id ) || isSuperUser( JFactory::getUser()->id )){
			$this->cache[$store] = $items;
			return $this->cache[$store];
		}else {
			$availableItems = array();
			foreach ($items as $item) {
				if($item->is_available == 1 ) {
					$availableItems[] = $item;
				}
			}

			$this->cache[$store] = $availableItems;

			return $this->cache[$store];
		}
	
	
		// Add the items to the internal cache.

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
		$hotelId = $this->getState('filter.hotel_id');
		
		if (!empty($hotelId)) {
			// Select all fields from the table.
			$query->select($this->getState('list.select', 'ho.*'));
			$query->from($db->quoteName('#__hotelreservation_offers').' AS ho');
	
	
			$query->select('op.offer_picture_path');
			$query->join( 'LEFT', $db->quoteName('#__hotelreservation_offers_pictures'). 'as op ON op.offer_id = ho.offer_id' );
	
			// Join over currency
			$query->select('GROUP_CONCAT(DISTINCT hov.voucher SEPARATOR ", ") as vouchers');
			$query->join('LEFT', $db->quoteName('#__hotelreservation_offers_vouchers').' AS hov ON hov.offerId=ho.offer_id');
		
			// Filter on the published state.
			$published = $this->getState('filter.published');
			if (is_numeric($published)) {
				$query->where('ho.is_available = '.(int) $published);
			} elseif ($published === '') {
				$query->where('(ho.is_available IN (0, 1))');
			}
		
			// Filter the items over the menu id if set.
			if (!empty($hotelId)) {
				$query->where('ho.hotel_id = '.$db->quote($hotelId));
			}
	        $query->group('ho.offer_id');
	
			// Add the list ordering clause.
			$query->order($db->escape($this->getState('list.ordering', 'ho.offer_id')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		}
		return $query;
	}

	/**
	 * Method to get applicationsettings
	 * @return object with data
	 */
	function getOfferContent(){
		
		$offerId = JRequest::getVar('offer_id');
		$value = $this->getTable("Offers","JTable");
		$value->load($offerId);
		
		$content_info = "<TABLE WIDTH='100%' cellpadding=0 border=0 cellspacing=0>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_CODE',true)." :</B></TD><TD><B>".$value->offer_code."</B></TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_NAME',true)." :</B></TD><TD><B>".$value->offer_name."</B></TD></TR>";
		if( $value->offer_reservation_cost_val !=0 )
			$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_COST_VALUE',true)." :</B></TD><TD><B>".$value->offer_reservation_cost_val."</B></TD></TR>";
		if( $value->offer_reservation_cost_proc !=0 )
			$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_COST_PERCENT',true)." :</B></TD><TD><B>".$value->offer_reservation_cost_proc." %</B></TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_DESCRIPTION',true)." :</B></TD><TD>".$value->offer_description."</TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_OFFER_PERIOD',true)." :</B></TD><TD>".$value->offer_datas." <> ".$value->offer_datae."</TD></TR>";
		$content_info .= "<TR><TD nowrap width=10%><B>".JText::_('LNG_DISPLAY_ON_FRONT',true)." :</B></TD><TD>".($value->offer_datasf !='0000-00-00'?$value->offer_datasf:'&nbsp;')." <> ".($value->offer_dataef !='0000-00-00'?$value->offer_dataef:'&nbsp;')."</TD></TR>";
		
		$query = "
							SELECT
								r.room_id,
								r.room_name
		
							FROM #__hotelreservation_rooms	r
							INNER JOIN #__hotelreservation_offers_rooms_price 		ord		USING(room_id)
							WHERE ord.offer_id = ".$value->offer_id."
							GROUP BY r.room_id
							ORDER BY room_name
				";
		
		$room = $this->_getList( $query );
		if( isset($room) )
		{
			$old_room_id 	= 0;
			foreach( $room as $value_room )
			{
				$content_info 	.= "<TR><TD nowrap align=center valign=middle width=10%><B>".$value_room->room_name."</B></TD><TD>";
					
				$query = "
									SELECT
										ord.*
									FROM #__hotelreservation_rooms	r
									INNER JOIN #__hotelreservation_offers_rooms_price 		ord		USING(room_id)
									WHERE
										ord.offer_id = ".$value->offer_id."
										AND
										r.room_id = ".$value_room->room_id."
									ORDER BY room_name
						";
				$room_discount = $this->_getList( $query );
				$old_room_id =$value_room->room_id;
		
				// $content_info .= "	</TABLE>";
				$content_info .= "<HR></TD></TR>";
			}
		}
		
		$content_info .= "</TABLE>";
		
 		return $content_info;
		//warning info
		
	}
	
	function getWarningContent(){
		
		$offerId = JRequest::getVar('offer_id');
		$warning_info = "";
		$query = "
							SELECT
								r.room_id,
								r.room_name,
								o.offer_datas,
								o.offer_datae
							FROM #__hotelreservation_rooms				 				r
							INNER JOIN #__hotelreservation_offers_rooms_price 		ord		ON r.room_id = ord.room_id
							INNER JOIN #__hotelreservation_offers						o		ON o.offer_id = ord.offer_id
							WHERE ord.offer_id = ".$offerId."
							GROUP BY r.room_id
							ORDER BY room_name
				";
		// dmp($query);
		$room = $this->_getList( $query );
		if( isset($room) )
		{
			$old_room_id 	= 0;
			foreach( $room as $value_room )
			{
				$answ = array();
				$is_error_period 		= false;
				$is_error_ignored_days 	= false;
				if(
				$value_room->offer_datas 	!= '0000-00-00'
						)
				{
					$answ[]		 = JText::_('LNG_ERROR_DEFAULT_ROOM_PERIOD',true)."<BR>".$value_room->offer_datas." > ( ".JText::_('LNG_DEFAULT_DATE_ROOM_START',true)." )";
				}
		
				if(
				$value_room->offer_datae 	!= '0000-00-00'
						)
		
				{
					$answ[]		 = JText::_('LNG_ERROR_DEFAULT_ROOM_PERIOD',true)."<BR>".$value_room->offer_datae." > ( ".JText::_('LNG_DEFAULT_DATE_ROOM_END',true)." )";
				}
		
				$nr_a = 1;
				foreach( $answ as $a )
				{
                    $warning_info 	.= "<TR>";
                    if( $nr_a == 1 )
                        $warning_info 	.= "<TD rowspan = ".(count($answ)+1)."  align=center valign=middle width=10%><B>".$value_room->room_name."</B></TD>";
                    $warning_info 	.= "	<TD width=90%>$a</TD>";
                    $warning_info 	.= "</TR>";
					$nr_a ++;
				}
				if( count($answ) > 0 )
				{
					$warning_info .= "<TR> <TD rowspan = ".count($answ)."  align=center valign=middle width=30%>".
                        "<a href='index.php?option=com_jhotelreservation&view=rooms'>".JText::_('LNG_MORE_DETAILS_ROOM_SETTINGS')."</a>".
                        "</TD><td colspan=3><HR></TD></TR>";
				}
				$old_room_id =$value_room->room_id;
			}
		}
		if( strlen($warning_info) > 0 )
		{
			$warning_info = "	<TABLE WIDTH='100%' cellpadding=0 border=0 cellspacing=0>".
					$warning_info.
					"</TABLE>";
		}
		return $warning_info;
	}
	
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}
	
	function &getHotel()
	{
		$query = 	' SELECT 	
							h.*,
							c.country_name
						FROM #__hotelreservation_hotels				h
						LEFT JOIN #__hotelreservation_countries		c USING ( country_id)
						'.
					' WHERE 
						hotel_id = '.$this->_hotel_id;
		
		$this->_db->setQuery( $query );
		$h = $this->_db->loadObject();
		return  $h;
	}

	
	

	
	function remove($hotel_id,&$ItemsId)
	{
		$query = " 	SELECT  
						*  
					FROM #__hotelreservation_confirmations					c
					INNER JOIN #__hotelreservation_confirmations_rooms		r USING( confirmation_id )
					WHERE 
						r.offer_id IN (".implode(',', $ItemsId).")
						AND
						c.hotel_id IN (".$hotel_id.")
						AND 
						c.reservation_status NOT IN (".CANCELED_ID.", ".CHECKEDOUT_ID." )
					";
						
		$checked_records = $this->_getList( $query );
		if ( count($checked_records) > 0 ) 
		{
			JError::raiseWarning( 500, JText::_('LNG_SKIP_OFFER_REMOVE',true) );
			return false;
		}

		$row = $this->getTable('Offers','JTable');

		if (count( $ItemsId )) {
			foreach($ItemsId as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					$msg = JText::_( 'LNG_OFFER_ERROR_DELETE' ,true);
					return false;
				}
			}
		}
		return true;

	}
	
	
	
	function state()
	{
		$cids = JRequest::getVar( 'cid', array(0));
        $app		= JFactory::getApplication();
        $hotelId = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');
		if (count( $cids )) {
			foreach($cids as $cid) {
				$query = 	" UPDATE #__hotelreservation_offers SET is_available = IF(is_available, 0, 1) WHERE offer_id = ".$cid ." AND hotel_id = ".$hotelId;
				$this->_db->setQuery( $query );
				if (!$this->_db->query())
				{
					//JError::raiseWarning( 500, "Error change Room state !" );
					return false;

				}
				return true;
			}
		}
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
        $pk	= (!empty($pk)) ? $pk : (int) $this->getState('offer.offer_id');
        $user = JFactory::getUser();

        // Get an instance of the record's table.
        $table = JTable::getInstance("Offers","JTable");

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
        $table		= JTable::getInstance("Offers","JTable");
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

	function changeFeaturedState()
    {
        $cids = JRequest::getVar('cid', array(0));
        if (count($cids)) {
            foreach ($cids as $offerId) {
                $query = " UPDATE #__hotelreservation_offers SET featured = IF(featured, 0, 1) WHERE offer_id = " . $offerId;

                $this->_db->setQuery($query);
                if (!$this->_db->query()) {
                    return false;
                }
                return true;
            }
        }
    }
	
	function changeTopState()
    {
        $cids = JRequest::getVar('cid', array(0));
        if (count($cids)) {
            foreach ($cids as $offerId) {
                $query = " UPDATE #__hotelreservation_offers SET top = IF(top, 0, 1) WHERE offer_id = " . $offerId;

                $this->_db->setQuery($query);
                if (!$this->_db->query()) {
                    return false;
                }
                return true;
            }
        }
    }
	

	function getLastOrderNumber($hotelId){
		$query = "select from max(ordering) as ordering from  #__hotelreservation_offers WHERE  hotel_id = ".$hotelId;
		$this->_db->setQuery( $query );
		$offer = $this->_db->loadObject();
		return $offer->ordering;
	}
	
	function getLastOrder($offerId)
	{
		$offer_id = 0;
		if( isset($offerId) )
			$offer_id = $offerId;
		$increment = 0;
		if( $offer_id > 0 ){
			$query = 	" SELECT * FROM #__hotelreservation_offers  WHERE offer_id = ".$offer_id;
		} else {
			$query = 	" SELECT * FROM #__hotelreservation_offers  ORDER BY ordering DESC LIMIT 1 ";
			$increment++;
		}
		
		$db 	= JFactory::getDBO();
		$this->_db->setQuery( $query );
		$row = $this->_db->loadObject();
	
		if(!isset($row ))
			return 1;
	
		return ($row->ordering+$increment);
	}

	function getExtraOptions(){
		$query = "select * from  #__hotelreservation_extra_options WHERE  hotel_id = ". $this->_hotel_id;
		$this->_db->setQuery( $query );
		$extraOptions = $this->_db->loadObjectList();
		return $extraOptions;
	}
}
?>