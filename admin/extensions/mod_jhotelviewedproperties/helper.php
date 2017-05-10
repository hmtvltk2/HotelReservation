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

class modJHotelViewedProperties
{
    static function getItems($params)
    {

        $language = JFactory::getLanguage();
        $language_tag 	= $language->getTag();

        $translations = new JHotelReservationLanguageTranslations();

	    $db = JFactory::getDBO();
	    $userData =  isset($_SESSION['userData'])?$_SESSION['userData']:UserDataService::getUserData();

	    $items = array();

		if (!empty( $userData->user_properties))
		{
			foreach ( $userData->user_properties as $user_property )
			{
				if(!isset($items[$user_property]))
				{
					$query = $db->getQuery( true );
					$query->select( 'h.hotel_id,h.hotel_name,h.hotel_alias,h.hotel_stars,h.hotel_short_description as hotel_description,h.hotel_latitude,h.hotel_longitude,hp.hotel_picture_path,h.hotel_address,h.hotel_rating_score,h.hotel_phone' );
					$query->from( $db->quoteName( '#__hotelreservation_hotels' ) . 'as h' );
					$query->join('LEFT', $db->quoteName('#__hotelreservation_hotel_pictures'). ' as hp on hp.hotel_id = h.hotel_id');


					if ( isset( $user_property ) )
					{
						$query->where( "h.hotel_id  = " . $user_property );
					}
					$query->where( 'h.is_available = 1' );
					$db->setQuery( (string) $query );

					$item = $db->loadObject();

					$poiTranslation           = $translations->getObjectTranslation( HOTEL_TRANSLATION, $item->hotel_id, $language_tag );
					$item->hotel_description = ! empty( $poiTranslation->content ) ? $poiTranslation->content : $item->hotel_description;

					$items[$item->hotel_id] = $item;
				}
			}
			return $items;
		}
    }
}
