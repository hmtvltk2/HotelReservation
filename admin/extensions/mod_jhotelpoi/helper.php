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

class modJHotelPoiHelper
{
    static function getItems($params)
    {

        $poi_number = $params->get('poi_number');
        $poi_order  = $params->get('poi_order');


        $language = JFactory::getLanguage();
        $language_tag 	= $language->getTag();

        $translations = new JHotelReservationLanguageTranslations();

	    $result = self::getCoordinates();
	    $db = JFactory::getDBO();

	    //set the limit result based on module param @poi_number
	    $limit = '';
	    if ( isset( $poi_number ) && (int) $poi_number > 0 ) {
		    $limit = ' limit ' . $poi_number;
	    }

		if ( ! empty( $result)) {
			$latitude  = (double)$result['latitude'];
			$longitude = (double)$result['longitude'];
			$query     = $db->getQuery( true );
			$query->select( 'poi.id,poi.hotel_id,poi.name,poi.description,poi.poi_latitude,poi.poi_longitude,h.hotel_name,
                         111.1111 *
                                DEGREES(ACOS(COS(RADIANS(' . $latitude . '))
                                     * COS(RADIANS(poi.poi_latitude))
                                     * COS(RADIANS(' . $longitude . ' - poi.poi_longitude))
                                     + SIN(RADIANS(' . $latitude . '))
                                     * SIN(RADIANS(poi.poi_latitude)))) AS distance_in_km' );
			$query->from( $db->quoteName( '#__hotelreservation_points_of_interest' ) . ' as poi' );
			$query->join('LEFT', $db->quoteName( '#__hotelreservation_hotels' ) . 'as h on poi.hotel_id = h.hotel_id ' );
			if(isset($result["hotel_id"])) {
				$query->where("poi.hotel_id = ". $result["hotel_id"]);
			}
			$query->where( 'poi.publish = 1' );
			$query->group( 'poi.id' );
			if ( $poi_order == 1 ) {
				$query->order( 'RAND() ' . $limit );
			}
			if ( $poi_order == 0 ) {
				$query->order( 'distance_in_km ' . $limit );
			}
			$db->setQuery( (string) $query );

			$items = $db->loadObjectList();

			if ( isset( $items ) && count( $items ) > 0 ) {
				foreach ( $items as $poi ) {
					$poiTranslation         = $translations->getObjectTranslation( POI_TRANSLATION, $poi->id, $language_tag );
					$poi->description       = ! empty( $poiTranslation->content ) ? $poiTranslation->content : $poi->description;
					$poi->distance          = JHotelUtil::distance( $latitude, $longitude, $poi->poi_latitude, $poi->poi_longitude );
					$poi->formattedDistance = JHotelUtil::fmt( $poi->distance, 2 );
					$poi->distance_in_km    = (double) $poi->distance_in_km;

					$query = $db->getQuery( true );
					$query->select( 'poi_picture_path' );
					$query->from( $db->quoteName( '#__hotelreservation_points_of_interest_pictures' ) );
					$query->where( 'poi_picture_enable = 1' );
					$query->where( 'poid=' . (int) $poi->id );
					$query->order( 'id asc' );
					$db->setQuery( (string) $query );
					$files                 = $db->loadObject();
					$poi->poi_picture_path = isset( $files->poi_picture_path ) ? $files->poi_picture_path : '';
				}
			}

			return $items;
		}

    }

	/**
	 * @return array|null
	 */
	static function getCoordinates(){

		$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:UserDataService::getUserData();
		$userData->keyword = isset($userData->keyword)?$userData->keyword:'';

		$location = JHotelUtil::getInstance()->getCoordinates($userData->keyword);

		$hotelId = JRequest::getVar("hotel_id");

		if(isset($hotelId) && !empty($hotelId) && $hotelId != "" || (isset( $userData->keyword ) && $userData->keyword != '' && !isset($location["latitude"]))) {
			$db = JFactory::getDBO();
			//$query1     = $db->getQuery( true );

			$hotel = '';
			if(isset($hotelId) && !empty($hotelId) && $hotelId != "") {
				$hotel = 'h.hotel_id=' . $hotelId;
			}

			$search = '';

			if(isset($userData->keyword) && $userData->keyword != '' && empty($hotelId) ) {
				$search = " (h.hotel_name like '%$userData->keyword%') or (h.hotel_city like '%$userData->keyword%') or (h.hotel_county like '%$userData->keyword%') ";
			}
			$condition = '';
			if ( strlen( $hotel ) > 0 && strlen( $search ) > 0 ) {
				$condition = ' or ';
			}

			if ( isset( $hotelId ) && ! empty( $hotelId ) && $hotelId != "" || isset( $userData->keyword ) && $userData->keyword != '' ) {
				//select the hotel
				$query1 = " SELECT h.hotel_id,h.hotel_name, h.hotel_city, h.hotel_county, h.hotel_latitude, h.hotel_longitude
 								FROM #__hotelreservation_hotels h
							    where $hotel $condition $search ";
				$db->setQuery( $query1 );
				$result = $db->loadObject();
				$location = array();
				$location["latitude"] = $result->hotel_latitude;
				$location["longitude"] =  $result->hotel_longitude;
				$location["hotel_id"]   = $result->hotel_id;
			}
		}
		return $location;
	}

}
