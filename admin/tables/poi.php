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

class JTablePOI extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) {

        parent::__construct('#__hotelreservation_points_of_interest', 'id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    /**
     * Method to build an SQL query to change the state of one item based on its:
     * @param $discountId
     * @return bool True if the query is executed and the state is changed
     */
    function state($id)
    {
        $db= JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->update($db->quoteName('#__hotelreservation_points_of_interest'));
        $query->set('publish = IF(publish, 0, 1)');
        if(is_numeric($id)) {
            $query->where('id=' . (int)$id);
        }
        $db->setQuery((string)$query);

        if (!$db->query())
        {
            return false;
        }
        return true;
    }

    function getSinglePOI($poid,$hotelId){
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__hotelreservation_points_of_interest'));
        $query->where('publish = 1');
        $query->where('id=' . (int)$poid);
        $db->setQuery((string)$query);
        $poi = $db->loadObject();

        if(isset($poi) ) {
            $query1 = $db->getQuery(true);
            $query1->select('hotel_name,hotel_city,hotel_county,hotel_latitude,hotel_longitude');
            $query1->from($db->quoteName('#__hotelreservation_hotels'));
            $query1->where('is_available = 1');
            $query1->where('hotel_id =' . $hotelId );
            $db->setQuery((string)$query1);
            $poi->hotel = $db->loadObject();
        }
        if (!$db->query())
        {
            return new stdClass();
        }
        return $poi;
    }

    function getActivePOIs($latitude,$longitude){
	    $db = JFactory::getDBO();
	    
	    $query = "
	    		select * from( 
		    		select id,name,description,poi_latitude,poi_longitude,activity_radius,poi_address,poi_city,poi_zipcode,poi_county,
		    			 111.1111 * DEGREES(ACOS(COS(RADIANS(". $latitude ."))
			             * COS(RADIANS(poi_latitude))
			             * COS(RADIANS(". $longitude . " - poi_longitude))
			             + SIN(RADIANS(" . $latitude . "))
			               * SIN(RADIANS(poi_latitude)))) AS distance
		    		  from #__hotelreservation_points_of_interest  
		    		  where publish = 1 ) a 
			      where a.distance <= a.activity_radius
		          order by name asc";
	    $db->setQuery( $query );
	    $pois = $db->loadObjectList();
   
	    return $pois;
    }

}