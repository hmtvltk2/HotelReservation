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

class JTableViewedProperties extends JTable
{

    /**
     * @var
     */
    private $db;

    /**
     * @var
     */
    private $pk;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) {

        parent::__construct('#__hotelreservation_viewed_properties', 'id',$db);
    }
    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    function getAllViewedProperties($userId){

	    $language = JFactory::getLanguage();
	    $language_tag 	= $language->getTag();

	    $translations = new JHotelReservationLanguageTranslations();

	    $db = JFactory::getDBO();

	    $query = $db->getQuery( true );

	    $query->select( 'h.hotel_id,h.hotel_name,h.hotel_alias,h.hotel_stars,h.hotel_short_description as hotel_description,h.hotel_latitude,h.hotel_longitude,hp.hotel_picture_path,h.hotel_address,h.hotel_rating_score,h.hotel_phone,vp.user_id' );
	    $query->from( $db->quoteName( '#__hotelreservation_viewed_properties' ) . 'as vp' );
	    $query->join('LEFT', $db->quoteName( '#__hotelreservation_hotels' ) . 'as h on h.hotel_id = vp.hotel_id' );
	    $query->join('LEFT', $db->quoteName('#__hotelreservation_hotel_pictures'). ' as hp on hp.hotel_id = h.hotel_id');
		$query->where( "vp.user_id  = " . $userId );
	    $query->where( 'h.is_available = 1' );
	    $query->group('vp.hotel_id');
	    $db->setQuery( (string) $query );

	    $items = $db->loadObjectList();

	    foreach ($items as $item){
		    $poiTranslation          = $translations->getObjectTranslation( HOTEL_TRANSLATION, $item->hotel_id, $language_tag );
		    $item->hotel_description = ! empty( $poiTranslation->content ) ? $poiTranslation->content : $item->hotel_description;

	    }
	    return $items;
    }


    function getUserProperty($propertyId,$userId){

	    $db = JFactory::getDBO();

	    $query = $db->getQuery( true );

	    $query->select( 'vp.*' );
	    $query->from( $db->quoteName( '#__hotelreservation_viewed_properties' ) . 'as vp' );
	    $query->where( "vp.user_id  = " . $userId );
	    $query->where( 'vp.hotel_id = '.$propertyId);
	    $db->setQuery( (string) $query );
	    $item = $db->loadObject();
	    if(!isset($item)){
		    $item = new stdClass();
	    	$item->hotel_id = null;
		    $item->user_id = null;
	    }
	    return $item;
    }


	function deleteProperty($propertyId,$userId){
		$db = JFactory::getDBO();
		$query = $db->getQuery( true );
		$query->delete();
		$query->from("#__hotelreservation_viewed_properties");
		$query->where('hotel_id = ' . (int)$propertyId);
		$query->where('user_id = ' . (int)$userId);
		$db->setQuery($query);
		$db->execute();
	}
}
