<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JHotelReservationModelOffers extends JModelLegacy{
	
	function __construct()
	{
		$this->voucher = JRequest::getVar('voucher');
		$this->hotelId = JRequest::getVar('hotelId');
		parent::__construct();
	
		$mainframe = JFactory::getApplication();
	
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
	
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->_data = null;
	}
	
	
	function getOffers()
	{
		// Load the data
		if (empty( $this->_data ))
		{
			if(empty($this->voucher)){
				$this->voucher ='';
			}

			$orderBy = JRequest::getVar('orderBy');
			$city = JRequest::getVar('city');
			
			$offersTable =$this->getTable('Offers','JTable');
			$offers = $offersTable->getOffers($this->voucher, $this->hotelId, $city, $orderBy, $this->getState('limitstart'), $this->getState('limit'));
            $languageTag = JRequest::getVar( '_lang');
            $translationTable = new JHotelReservationLanguageTranslations();
            
			if(count($offers))
				foreach($offers as $offer){
					$offer->pictures = $offersTable->getOffersPictures($offer->offer_id);
					$offerTranslations = $translationTable->getAllObjectTranslationsArray($offer->offer_id,$languageTag);
						
					$offer->hotel_name = stripslashes($offer->hotel_name);
					if($offer->price_type == 0 &&  $offer->base_adults>0){
						$offer->starting_price = $offer->starting_price/ $offer->base_adults;
					}
						
                    $offerName = $translationTable->getObjectTranslation(OFFER_NAME,$offer->offer_id,$languageTag);
                    $offer->offer_name = empty($offerName->content)?$offer->offer_name:$offerName->content;
                    $offer->offer_content = empty($offerTranslations[OFFER_CONTENT_TRANSLATION])?$offer->offer_content:$offerTranslations[OFFER_CONTENT_TRANSLATION]["content"];
           	    	$offer->offer_description = empty($offerTranslations[OFFER_TRANSLATION])?$offer->offer_description:$offerTranslations[OFFER_TRANSLATION]["content"];
				}	

			//mixing the result when no order is selected
			$result = array();
			if(empty($orderBy)){	
				shuffle($offers);
				
				
				foreach($offers as $offer){
					if($offer->featuredOffer == 1){
						array_unshift($result,$offer);
					}else{
						array_push($result,$offer);
					}
				}
			}else{
				$result = $offers;
			}
		}
		return $result;
	}
		
	function getOffer($offerId){
		$offersTable =$this->getTable('Offers','JTable');
		$offer = $offersTable->getOffer($offerId);
		$offer->pictures = $offersTable->getOffersPictures($offer->offer_id);
		$offer->hotel_name = stripslashes($offer->hotel_name);
		
		if($offer->price_type == 0){
			$offer->starting_price = $offer->starting_price/ $offer->base_adults;
		}
		
		$offer->hotel = HotelService::getHotel($offer->hotel_id);
        $languageTag = JRequest::getVar( '_lang');
        $translationTable = new JHotelReservationLanguageTranslations();
        $offerTranslations = $translationTable->getAllObjectTranslationsArray($offer->offer_id,$languageTag);
        
         
        $offer->offer_name = empty($offerTranslations[OFFER_NAME])?$offer->offer_name:$offerTranslations[OFFER_NAME]["content"];
        $offer->offer_content = empty($offerTranslations[OFFER_CONTENT_TRANSLATION])?$offer->offer_content:$offerTranslations[OFFER_CONTENT_TRANSLATION]["content"];
        $offer->offer_description = empty($offerTranslations[OFFER_TRANSLATION])?$offer->offer_description:$offerTranslations[OFFER_TRANSLATION]["content"];
        
        $mediaReferer = JRequest::getVar('mediaReferer');
		$voucher = JRequest::getVar('voucher');
		$offersViewsTable =$this->getTable('OffersViews','JTable');
		$offersViewsTable->increaseViewCount($offer->offer_id, $mediaReferer, $voucher);
		
		return $offer;
	}
	
	function getTotalOffers(){
	
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$offersTable =$this->getTable('Offers','JTable');
			$city = JRequest::getVar('city');
			$this->_total = $offersTable->getTotalOffers($this->voucher, $city);
		}
		return $this->_total;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return JPagination
	 */
	function getPagination()
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
		
			$this->_pagination = new JPagination($this->getTotalOffers(), $this->getState('limitstart'), $this->getState('limit') );
			$this->_pagination->setAdditionalUrlParam('option','com_jhotelreservation');
			$this->_pagination->setAdditionalUrlParam('task','offers.searchOffers');
			$orderBy = JRequest::getVar('orderBy');
			$voucher = JRequest::getVar('voucher');
			
			if(isset($orderBy))
				$this->_pagination->setAdditionalUrlParam('orderBy',$orderBy);
			
			if(isset($voucher))
				$this->_pagination->setAdditionalUrlParam('voucher',$voucher);
		}
		return $this->_pagination;
	}
}

