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

class modJHotelReservationHelper
{
	static function getTitle( $params )
	{
		return '';
	}

	static function getHotelItems()
	{
		if(!isset($userData)){
			$userData =  isset($_SESSION['userData'])?$_SESSION['userData']:UserDataService::getUserData();
			$userData->searchParams["keyword"] =  isset($userData->keyword)?$userData->keyword:'';
			$userData->searchParams["orderBy"] = "noBookings desc";
		}
		
		$language = JFactory::getLanguage();
		$language_tag 	= $language->getTag();
		
		$db = JFactory::getDBO();
		$query = "  SELECT
				h.*,
				min(hp.hotel_picture_id),
				hotel_picture_path
				FROM #__hotelreservation_hotels h 
				left join #__hotelreservation_hotel_pictures hp on h.hotel_id=hp.hotel_id
				WHERE h.is_available=1 and LENGTH(h.hotel_latitude) > 3
				group by h.hotel_id";
		$db->setQuery($query);
		$list = $db->loadObjectList();


        $translations = new JHotelReservationLanguageTranslations();
        
        if(count($list))
        {
        	$translationsChildrenCategory = $translations->getAllTranslationtByLanguageArray(HOTEL_TRANSLATION,$language_tag);
        	
            foreach($list as $hotel){
            	if(!isset( $translationsChildrenCategory[$hotel->hotel_id])) 
            		continue;
                $translationChildrenCategory = $translationsChildrenCategory[$hotel->hotel_id];
                $hotel->hotelDescription = empty($translationChildrenCategory["content"])?"":$translationChildrenCategory["content"];
            }
        }
		
		return $list;

	}
	
	static function getHotelsLocation(){
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_jhotelreservation/tables');
		$hotelTable = JTable::getInstance('Hotels','Table');
		$list =  $hotelTable->getHotelsLocation();
		
		return $list; 
	}
	
	static function getStaticMarkers($hotels){
		$markers = "";
		foreach($hotels as $hotel){
			$markers .= "&markers=color:blue|$hotel->hotel_latitude,$hotel->hotel_longitude";
		}
		return $markers;
	}
	
}
?>
