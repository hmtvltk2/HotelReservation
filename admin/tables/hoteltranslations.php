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

class TableHotelTranslations extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableHotelTranslations(& $db) {

		parent::__construct('#__hotelreservation_language_translations', 'id', $db);
	}

	function getAllTranslations($type,$objectId){
		$db =JFactory::getDBO();
		$query = "select * from #__hotelreservation_language_translations where type=$type and object_id=$objectId order by language_tag";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function deleteTranslationsForObject($type,$objectId){
		$db =JFactory::getDBO();
		$query = "delete from #__hotelreservation_language_translations where type=$type and object_id=$objectId";
		$db->setQuery($query);
		$db->query();
	}
	
	function saveTranslation($type,$objectId,$language,$content){
		$db =JFactory::getDBO();
		$query = "insert into #__hotelreservation_language_translations values($type,$objectId,'$language','$content')";
		$db->setQuery($query);
		$db->query();
	}
	function getObjectTranslation($translationType,$objectId,$language)
	{
		$db =JFactory::getDBO();
		$query = "select * from  #__hotelreservation_language_translations where type=$translationType and object_id=$objectId and language_tag='$language'";
        $db->setQuery($query);
		return $db->loadObject();
	}

    function getAllTranslationtByLanguage($translationType,$language){

        $db =JFactory::getDBO();
        $query = "select * from #__hotelreservation_language_translations where type=$translationType and language_tag = '$language'";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getAllTranslationtByLanguageArray($translationType,$language){
    
    	$db =JFactory::getDBO();
    	$query = "select * from #__hotelreservation_language_translations where type=$translationType and language_tag = '$language'";
    	$db->setQuery($query);
    	return $db->loadAssocList('object_id');
    }
    
    function getAllObjectTranslationsArray($objectId,$language){
    
    	$db =JFactory::getDBO();
    	$query = "select * from #__hotelreservation_language_translations where object_id=$objectId and language_tag = '$language'";
    	$db->setQuery($query);
    	
    	return $db->loadAssocList('type');
    }
}