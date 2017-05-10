<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelPoi extends JModelItem{

    protected function populateState(){
        $app = JFactory::getApplication('site');

        // Load state from the request.
        $pk = JRequest::getVar('poid');
        $this->setState('poi.id', $pk);

	    $hotelId = JRequest::getVar('hotelId');
	    $this->setState('poi.hotelId', $hotelId);

        $offset = JRequest::getUInt('limitstart');
        $this->setState('list.offset', $offset);

        UserDataService::updateUserData();
    }


    public function getSinglePOI($pk = null){

        $singlePOI = HotelService::getSinglePOI($this->getState('poi.id'),$this->getState('poi.hotelId'));
        if(!empty($singlePOI)) {
            $languageTag = JRequest::getVar('_lang');
            $translations = new JHotelReservationLanguageTranslations();
            $poiTranslation = $translations->getObjectTranslation(POI_TRANSLATION, $singlePOI->id, $languageTag);
            $singlePOI->description = !empty($poiTranslation->content) ? $poiTranslation->content : $singlePOI->description;
        }
        return $singlePOI;
    }

}