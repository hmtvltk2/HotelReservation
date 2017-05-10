<?php
/**
 * @copyright	Copyright (C) 2008-2015 CMSJunkie. All rights reserved.
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


require JPATH_SITE."/administrator/components/com_jhotelreservation/tables/hoteltranslations.php";

class JHotelReservationLanguageTranslations{


    protected $hotelTranslationsTable;

    /**
     *
     */
    function __construct()
    {
        $this->hotelTranslationsTable = JTable::getInstance('hoteltranslations','Table',array());
    }


    /**
     * @param $translationType
     * @param $objectId
     * @param $language
     * @return null
     */
    function getObjectTranslation($translationType,$objectId,$language)
    {
    	if(isset($objectId) && $objectId!=""){

            $translation =  $this->hotelTranslationsTable->getObjectTranslation($translationType,$objectId,$language);
            return $translation;
        }
        else return null;
    }

    /**
     * @param $translationType
     * @param $objectId
     * @return array
     */
    function getAllTranslations($translationType,$objectId)
    {
        $translationArray=array();
        if(isset($objectId) && $objectId!=""){
            $translations = $this->hotelTranslationsTable->getAllTranslations($translationType,$objectId);
            if(count($translations)>0)
                foreach($translations as $translation){
                    $translationArray[$translation->language_tag]=$translation->content;
                }
        }
        return $translationArray;
    }

    /**
     * @param $translationType
     * @param $language
     * @return null
     */
    function getAllTranslationtByLanguage($translationType,$language){
        if(isset($language) && $language!=""){
            $translation = $this->hotelTranslationsTable->getAllTranslationtByLanguage($translationType,$language);
            return $translation;
        }
        else return null;
    }

    function getAllTranslationtByLanguageArray($translationType,$language){
    	if(isset($language) && $language!=""){
    		$translation = $this->hotelTranslationsTable->getAllTranslationtByLanguageArray($translationType,$language);
    		return $translation;
    	}
    	else return null;
    }
    
    function getAllObjectTranslationsArray($objectId,$language){
    	if(isset($language) && $language!=""){
    		$translation = $this->hotelTranslationsTable->getAllObjectTranslationsArray($objectId,$language);
    		return $translation;
    	}
    	else return null;
    }

    function deleteTranslationsForObject($translationType,$objectId){
        if(isset($objectId) && $objectId!=""){
            $this->hotelTranslationsTable->deleteTranslationsForObject($translationType,$objectId);
        }
    }

    /**
     * @param $translationType
     * @param $objectId
     * @param $language
     * @param $content
     */
    function saveTranslation($translationType,$objectId,$language,$content){
        $hotelTranslationsTable = JTable::getInstance('hoteltranslations','Table',array());
        $hotelTranslationsTable->type= $translationType;
        $hotelTranslationsTable->object_id= $objectId;
        $hotelTranslationsTable->language_tag= $language;
        $hotelTranslationsTable->content= $content;

        if(!$hotelTranslationsTable->store()){
            JError::raiseWarning( 500,'Could not save translation');
        }
    }

    /**
     * @param $translationType
     * @param $objectId
     * @return mixed
     */
    function getAllTranslationObjects($translationType,$objectId)
    {
        if(isset($objectId) && $objectId!=""){
            $translations = $this->hotelTranslationsTable->getAllTranslations($translationType,$objectId);
        }
        return $translations;
    }

    function saveCopiedContentTranslations($translations,$Id){
        if(count($translations)>0){
            foreach($translations as $translation){
                $translationTable	= JTable::getInstance('hoteltranslations','Table',array());
                $translationTable->bind($translation);
                $translationTable->object_id = $Id;
                $translationTable->id = null;
                if(!$translationTable->store()){
                    throw( new Exception("Error saving email templates translation") );
                }
            }
        }
    }

}