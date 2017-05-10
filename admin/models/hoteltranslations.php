<?php
/**
 * @copyright	Copyright (C) 2008-2012 CMSJunkie. All rights reserved.
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

jimport('joomla.application.component.model');

class jHotelReservationTranslations extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();
	}
	
	function getObjectTranslation($translationType,$objectId,$language)
	{
		if(isset($objectId) && $objectId!=""){
			$hotelTranslationsTable = $this->getTable('hoteltranslations');
			$translation = $hotelTranslationsTable->getObjectTranslation($translationType,$objectId,$language);
			return $translation;
		}
		else return null;
	}
	
	function getAllTranslations($translationType,$objectId)
	{
		$translationArray=array();
		if(isset($objectId) && $objectId!=""){
			$hotelTranslationsTable = $this->getTable('hoteltranslations');
			$translations = $hotelTranslationsTable->getAllTranslations($translationType,$objectId);
			if(count($translations)>0)
				foreach($translations as $translation){
					$translationArray[$translation->language_tag]=$translation->content;
				}
		}
		return $translationArray;
	}

    function getAllTranslationtByLanguage($translationType,$language){
        if(isset($language) && $language!=""){
            $hotelTranslationsTable = $this->getTable('hoteltranslations');
            $translation = $hotelTranslationsTable->getAllTranslationtByLanguage($translationType,$language);
            return $translation;
        }
        else return null;
    }
	
	function deleteTranslationsForObject($translationType,$objectId){
		$hotelTranslationsTable = $this->getTable('hoteltranslations');
		if(isset($objectId) && $objectId!=""){
			$hotelTranslationsTable->deleteTranslationsForObject($translationType,$objectId);
		}
	}
	
	function saveTranslation($translationType,$objectId,$language,$content){
		$hotelTranslationsTable = $this->getTable('hoteltranslations');
		$hotelTranslationsTable->type= $translationType; 
		$hotelTranslationsTable->object_id= $objectId;
		$hotelTranslationsTable->language_tag= $language;
		$hotelTranslationsTable->content= $content;
		
		if(!$hotelTranslationsTable->store()){
			JError::raiseWarning( 500,'Could not save translation');
		}
	}
	
	function getAllTranslationObjects($translationType,$objectId)
	{
		if(isset($objectId) && $objectId!=""){
			$hotelTranslationsTable = $this->getTable('hoteltranslations');
			$translations = $hotelTranslationsTable->getAllTranslations($translationType,$objectId);
		}
		return $translations;
	}
}
?>