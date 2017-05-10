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

class modJHotelSimilarInterestHotels
{
	/**
	 * @param $params
	 * Return hotels based on the user session data of viewed hotels
	 * @return array
	 */
    static function getItems($params) {
	    $numberOfProperties = $params->get( 'number_of_properties' );

	    $language     = JFactory::getLanguage();
	    $language_tag = $language->getTag();

	    $translations = new JHotelReservationLanguageTranslations();

	    $db = JFactory::getDBO();

	    $user = JFactory::getUser();
	    $userData = isset( $_SESSION['userData'] ) ? $_SESSION['userData'] : UserDataService::getUserData();

	    JTable::addIncludePath( JPATH_ROOT . '/administrator/components/com_jhotelreservation/tables' );
	    $propertiesTable = JTable::getInstance( 'ViewedProperties', 'JTable' );

	    $otherUsersProperties = $propertiesTable->getOtherUsersViewedProperties( $userData->user_properties,$user->id );

	    if ( isset( $otherUsersProperties ) && ! empty( $otherUsersProperties ) )
	    {
			    $query = $db->getQuery( true );
			    $query->select( 'h.hotel_id,h.hotel_name,h.hotel_alias,h.hotel_stars,h.hotel_short_description as hotel_description,h.hotel_latitude,h.hotel_longitude,hp.hotel_picture_path,h.hotel_address,h.hotel_rating_score,h.hotel_phone,h.min_room_price,count(hrt.review_id) as noReviews,h.recommended' );
			    $query->from( $db->quoteName( '#__hotelreservation_hotels' ) . 'as h' );
			    $query->join( 'LEFT', $db->quoteName( '#__hotelreservation_hotel_pictures' ) . ' as hp on hp.hotel_id = h.hotel_id' );
		        $query->join('LEFT', $db->quoteName('#__hotelreservation_review_customers'). 'as hrt on h.hotel_id=hrt.hotel_id');
		        $query->where( "h.hotel_id  in (" . $otherUsersProperties.")" );
			    $query->where( 'h.is_available = 1' );
			    $query->order( 'h.hotel_id limit ' . $numberOfProperties );
		        $query->group('h.hotel_id');
			    $db->setQuery( (string) $query );

			    $items = $db->loadObjectList();


		        foreach ($items as $item ){

			        if(isset($item->hotel_id) && $item->hotel_id > 0 && isset($item->hotel_rating_score))
				        $item->ratingScores = ReviewsService::getHotelRatingClassifications($item->hotel_rating_score,$translations,$language_tag);

			        $propTranslation          = $translations->getObjectTranslation( HOTEL_TRANSLATION, $item->hotel_id, $language_tag );
			        $item->hotel_description = ! empty( $propTranslation->content ) ? $propTranslation->content : $item->hotel_description;
		        }
		    return $items;
	    }
    }
}
