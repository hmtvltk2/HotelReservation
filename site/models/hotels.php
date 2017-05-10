<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JHotelReservationModelHotels extends JModelLegacy{
	
	var $searchFilter;
	var $itemHotels;
	var $hotelIds;
	var $searchParams;
	var $appSettings;
	
	function __construct()
	{
		$this->searchFilter = JRequest::getVar('searchkeyword');
		parent::__construct();
		$this->_total = 0;
		$this->_totalUnavailable = 0;
		
		$mainframe = JFactory::getApplication();
	
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
	
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->appSettings = JHotelUtil::getApplicationSettings();
	}
	
	function getHotels(){
		$userData =  $_SESSION['userData'];
		$this->createSeachFilter($userData->filterParams, $userData->orderBy);
		$this->searchParams = $this->createDBSearchParams($this->searchFilter["filterCategories"]);
		$this->searchParams["priceFilter"] = $this->createDBPriceFilter($userData->filterParams);
		$this->searchParams["keyword"] =  empty($userData->keyword)?"":$userData->keyword;
		$this->searchParams["orderBy"] = $userData->orderBy;
		$this->searchParams["adults"] = $userData->roomGuests;
		$this->searchParams["roomCount"] = $userData->rooms;
		$this->searchParams["voucher"] = $userData->voucher;
		$this->searchParams["startDate"] = $userData->start_date;
		$this->searchParams["endDate"] = $userData->end_date;
		$this->searchParams["languageTag"] = JRequest::getVar( '_lang');
		$this->searchParams["city"] = JRequest::getVar('city');
        $this->searchParams["showAll"] = JRequest::getVar('showAll');
		$this->searchParams["searchType"] = JRequest::getVar('searchType');
		$this->searchParams["searchId"] = JRequest::getVar('searchId');
		$this->searchParams["apply_search_params"] = $this->appSettings->apply_search_params;
		
		if($this->searchParams["searchType"]==JText::_("LNG_HOTELS")){
			$hotel = $hotel = HotelService::getHotel($this->searchParams["searchId"]);
			$link = JHotelUtil::getHotelLink($hotel);
			
			$app = JFactory::getApplication();
			$app->redirect($link);
			
		}

		if(!isset($userData->noDates))
			$userData->noDates = 0;
		$this->searchParams["no-dates"] = $userData->noDates;
		
		$hotelTable =$this->getTable('hotels');
		$hotels =  $hotelTable->getHotels($this->searchParams, $this->getState('limitstart'), $this->getState('limit'));

		$_SESSION['userData'] = $userData;
		
		$nearByHotels = array();
		if(count($hotels)<=3){ 
			$nearByHotels = $this->getNearByHotels($this->searchParams["keyword"], $this->searchParams, $hotels);
			if(!empty($nearByHotels)){
				foreach($nearByHotels as &$hotel){
					$hotel->nearBy =1;
				}
				$hotels = array_merge($hotels,$nearByHotels);
			}
		}
		
		$hotels = $this->prepareHotelsItems($hotels);
		
		$this->itemHotels = $hotels;
		//dmp($userData->orderBy);
		if(isset($userData->voucher) && $userData->voucher!='' && $userData->orderBy=='' && count($this->itemHotels)>0){
			//dmp("shuffle");
			shuffle($this->itemHotels);
		}
		
		return $this->itemHotels;
	}
	
	function getNearByHotels($locationName, $searchParams, $hotels){
		
		$location = JHotelUtil::getInstance()->getCoordinates($locationName);
		
		if(empty($location))
			return null;
		
		$excludedIds=array();
		foreach($hotels as $hotel){
			$excludedIds[]=$hotel->hotel_id;
		}
		
		$searchParams["nearByHotels"] = 1;
		$searchParams["latitude"]= $location["latitude"];
		$searchParams["longitude"]= $location["longitude"];
		$searchParams["distance"] = 100;
		if(!empty($excludedIds)){
			$searchParams["excludedIds"] = implode(",",$excludedIds);
		}
		$hotelTable =$this->getTable('hotels');
		$hotels =  $hotelTable->getHotels($searchParams, 0, 45);
		
		return $hotels;
		
	}
	
	
	function prepareHotelsItems($hotels){
        $translationTable = new JHotelReservationLanguageTranslations();
        $languageTag = JRequest::getVar( '_lang');
        $userData =  $_SESSION['userData'];
        
        $offerTranslations = $translationTable->getAllTranslationtByLanguageArray(OFFER_CONTENT_TRANSLATION,$languageTag);
        $offerNameTranslations = $translationTable->getAllTranslationtByLanguageArray(OFFER_NAME,$languageTag);
        //set number of rooms left
        
        $totalHotelsBookings = BookingService::getTotalNumberOfBookings($userData->start_date,$userData->end_date);
        $totalHotelsRoomsAvailable = RoomService::getTotalNrRoomsAvailable($userData->start_date, $userData->end_date );
        
		foreach($hotels as $idx=>$hotel){
			if(!empty($hotel->room_details)){
				$hotel->rooms = explode(",",$hotel->room_details);
				foreach($hotel->rooms as &$room){
					$offer[0] = explode("|",$room);
				}
			}
			//set currency
			if(!empty($userData->currency->name)){
				$hotel->room_min_rate = CurrencyService::convertCurrency($hotel->room_min_rate,$hotel->hotel_currency,$userData->currency->name);
			}
			

			//set offers 
			if(!empty($hotel->offer_details)){
				$hotel->offers = explode("##",$hotel->offer_details);
				//dmp($hotel->offers);
				foreach($hotel->offers as &$offer){		
					//dmp($offer);			
					$offer = explode("||",$offer);
                    $offer[0] = empty($offerNameTranslations[$offer[1]])?$offer[0]:$offerNameTranslations[$offer[1]]["content"];
                    $offer[8] = empty($offerTranslations[$offer[1]])?$offer[8]:$offerTranslations[$offer[1]]["content"];
					if(count($offer)<5)
						continue;
						
					//dmp($offer);
					$offer["price"] = $offer[2];
					if($offer[5] == 1){
						$offer["price"] = $offer[2];
					}else{
						if($offer[4]!=0)
							$offer["price"] = $offer[2] / $offer[4];
					}
					if($offer[6] == 1){
						$offer["price"] = $offer[2];
					}
				
					if(!empty($userData->currency->name)){
						$offer["price"] = CurrencyService::convertCurrency($offer["price"],$hotel->hotel_currency,$userData->currency->name);
					}
				}
			}
			

			
			if(isset($hotel->hotel_id) && $hotel->hotel_id > 0 && isset($hotel->hotel_rating_score))
				$hotel->ratingScores = ReviewsService::getHotelRatingClassifications($hotel->hotel_rating_score,$translationTable,$languageTag);
			
		}
		return $hotels;
	}
	
	function getSearchFilter(){
		return $this->searchFilter["searchParams"];
	}
	
	
	function getTotalHotels(){
		
		// Load the content if it doesn't already exist
			$categoryId= JRequest::getVar('categoryId');
			$hotelTable =$this->getTable('hotels');
			$hotels = $hotelTable->getTotalHotels($this->searchParams);
			$this->_total = count($hotels);
			$this->processSearchResults($hotels, $this->searchFilter["filterCategories"], true);
			$this->createPriceFilter($hotels);
				
			if(!isset($this->_total) || $this->_total<0){
				$this->_total = 0; 	
			}
		
		return $hotels;
	}
	
	function createDBPriceFilter($searchString){
		
		$priceFilter = array();
		if(isset($searchString)){
			$selectedParamsArray = explode("&", $searchString);
			foreach($selectedParamsArray as $selectedParam){
				if(isset($selectedParam) && strlen($selectedParam)>0){
					$selectedParamArray = explode("=", $selectedParam);
					if($selectedParamArray[0] == "priceFilter"){
						$filterVals = explode("_", $selectedParamArray[1]);
						$priceFilter[] = $filterVals;
					}
				}
			}
		}
		return $priceFilter; 
	}
	
	
	function createPriceFilter($searchResults){
		if(!is_array($searchResults) || count($searchResults)==0)
			return; 
		
		$userData =  $_SESSION['userData'];
		$currencySymbol = empty($userData->currency->symbol)?$searchResults[0]->currency_symbol:$userData->currency->symbol;
		//determine min/max per search
		if(empty($userData->maxPrice)){
			$minimum = $maximum = 0;
			
			foreach($searchResults as $searchResult){
				if (!empty($searchResult->min_room_price) && $minimum > $searchResult->min_room_price) {
					$minimum =  $searchResult->min_room_price;
				}
				
				if (!empty($searchResult->min_offer_price) && $minimum > $searchResult->min_offer_price) {
					$minimum =  $searchResult->min_offer_price;
				}
				
				if (!empty($searchResult->min_room_price) && ($maximum < $searchResult->min_room_price)) {
					$maximum =  $searchResult->min_room_price;
				}
					
				if (!empty($searchResult->min_offer_price) && $maximum < $searchResult->min_offer_price) {
					$maximum =  $searchResult->min_offer_price;
				}
	
			}
			$userData->minPrice = $minimum;
			$userData->maxPrice = $maximum ;
		}
		else{
			$minimum = $userData->minPrice;
			$maximum = $userData->maxPrice;
		} 

		//determine step for price filter
		$priceLag = $maximum - $minimum;
		$step = intVal($priceLag/JHP_PRICEFILTER_STEPS);
		$step = intVal($step/10)*10;
		
		//create filter 
		$priceCategories = array();
		for($idx=0;$idx<JHP_PRICEFILTER_STEPS;$idx++){
			$obj = new stdClass();
			if($idx<JHP_PRICEFILTER_STEPS-1){
				$obj->name = $currencySymbol.($idx*$step+1)." - ".$currencySymbol.(($idx+1)*$step);
				$obj->max = ($idx+1)*$step;
			}
			else{
				$obj->name = $currencySymbol.$idx*$step." + ";
				$obj->max = $maximum+1;
				
			}
			$obj->min = $idx*$step+1;
							
			$obj->id = ($idx*$step+1)."_".$obj->max;
			$obj->count = 0; 
			$obj->identifier = "priceFilter";
			$priceCategories[] = $obj;
		}	
		
		//update count 
		foreach($searchResults as $searchResult){
			$searchResult->lowest_hotel_price = 100;
				
			foreach($priceCategories as $priceCategory){
				if(($searchResult->lowest_hotel_price>= $priceCategory->min && $searchResult->lowest_hotel_price<=$priceCategory->max)){
					$priceCategory->count++;
				}
			}
		}
		//set selected items if any 
		$this->setSearchedParams("priceFilter",$priceCategories, $userData->filterParams);
		
		//update categories filter
		$filterCategories =  $this->searchFilter["filterCategories"];
		$filterCategories["priceFilter"] = array ("dbIdent"=>"priceFilter","name"=>JText::_('LNG_PRICE_FILTER'), "items"=>$priceCategories,"type"=>"value");
		$this->searchFilter["filterCategories"] = $filterCategories;
		$userData->searchFilter["filterCategories"] = $filterCategories;
		$_SESSION['userData'] = $userData;
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $searchResults
	 * @param unknown_type $params
	 * @return string
	 */
	function processSearchResults($searchResults, &$params, $countCategories = false){
		$hotelIds='';
		if(!is_array($searchResults) || count($searchResults)==0) 
			return null;
		
		foreach($searchResults as $searchResult){
			
			foreach($params as $key=>$param){
				if($countCategories){
					$results = explode(",",$searchResult->$param["dbIdent"]);
					$found = array();
					foreach($results as $result){
						if(!isset($found[$result])){
							foreach($param["items"] as $prm){
								if($prm->id == intval($result)){
									if(isset($prm->count)){
										$prm->count = $prm->count+1;
									}else{
										$prm->count = 1;
									}
									
									$found[$result] = true;
								}
							}
						}
					}
				}
			}
			$hotelIds = $hotelIds.$searchResult->hotel_id.',';
				
		}
		$hotelIds =substr($hotelIds, 0, -1);
		return $hotelIds;
	}
	
	/**
	 *
	 * Create the list of search ids for each filter category
	 * @param unknown_type $params
	 * @return string
	 */
	function createDBSearchParams($params){
		foreach($params as $key=>$value){
			$list =' ';
			foreach($value["items"] as $prm){
				if(isset($prm->selected)){
					$list = $list.$prm->id.",";
				}
			}
			$list =substr($list, 0, -1);
			$params[$key] = $list;
		}
		return $params;
	}
	
	/**
	 * Create search filter along with filter categories
	 *
	 * @param unknown_type $params
	 */
	function createSeachFilter($params, $orderBy){
		$this->searchFilter					= array();
		$this->searchFilter["searchParams"] = $params;
		
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_facilities ORDER BY name';
		$facilities = $this->_getList( $query );
		$this->setSearchedParams("facilityId",$facilities, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_types ORDER BY name';
		$types = $this->_getList( $query );
		$this->setSearchedParams("typeId",$types, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_accommodation_types ORDER BY name';
		$accommodationTypes = $this->_getList( $query );
		$this->setSearchedParams("accommodationTypeId",$accommodationTypes, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_environments ORDER BY name';
		$environments = $this->_getList( $query );
		$this->setSearchedParams("enviromentId",$environments, $params);
			
		$query = 	' 	SELECT * FROM #__hotelreservation_hotel_regions ORDER BY name';
		$regions = $this->_getList( $query );
		$this->setSearchedParams("regionId",$regions, $params);
	
		$query = 	' 	SELECT * FROM #__hotelreservation_offers_themes ORDER BY name';
		$themes = $this->_getList( $query );
		$this->setSearchedParams("themeId",$themes, $params);
		
		$stars = array();
		for($i = 0;$i<8;$i++){
			$obj = new stdClass();
			$obj->id=$i;
			$obj->name="$i stars";
			$stars[] = $obj;
		}
		$this->setSearchedParams("star",$stars, $params);
		
		$filterCategories =  array();
		$filterCategories["facilities"] = array ("dbIdent"=>"facilities","name"=>JText::_('LNG_FACILITIES'), "items"=>$facilities);
		$filterCategories["types"] = array ("dbIdent"=>"types","name"=>JText::_('LNG_TYPES'), "items"=>$types);
		$filterCategories["accommodationTypes"] = array ("dbIdent"=>"accommodationTypes","name"=>JText::_('LNG_ACCOMMODATION_TYPES'), "items"=>$accommodationTypes);
		$filterCategories["enviroments"] = array ("dbIdent"=>"enviroments","name"=>JText::_('LNG_ENVIROMENTS'), "items"=>$environments);
		$filterCategories["regions"] = array ("dbIdent"=>"regions","name"=>JText::_('LNG_REGIONS'), "items"=>$regions);
		$filterCategories["themes"] = array ("dbIdent"=>"themes","name"=>JText::_('LNG_THEMES'), "items"=>$themes);
		$filterCategories["stars"] = array ("dbIdent"=>"hstars","name"=>JText::_('LNG_STARS'), "items"=>$stars);
	
		$this->searchFilter["filterCategories"] = $filterCategories;
		$this->searchFilter["orderBy"] = $orderBy;
	}
	
	/**
	 *
	 * Select existing params
	 * @param unknown_type $params
	 * @param unknown_type $selectedParams
	 */
	function setSearchedParams($identifier, &$params, $selectedParams){
		foreach( $params as &$param ){
			$param->countResults = 0;
			$param->identifier = $identifier;
		}
		if(isset($selectedParams)){
			$selectedParamsArray = explode("&", $selectedParams);
			foreach($selectedParamsArray as $selectedParam){
				if(isset($selectedParam) && strlen($selectedParam)>0){
					$selectedParamArray = explode("=", $selectedParam);
					$paramId = $selectedParamArray[0];
					$paramValue = $selectedParamArray[1];
					foreach( $params as &$param ){
						$param->countResults = 0;
						$param->identifier = $identifier;
						if($identifier == $paramId && ($param->id == $paramValue)){
							$param->selected =1 ;	
							break;
						}
					}
				}
			}
		}
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
			$total = $this->_total;
			$this->_pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit') );
			$this->_pagination->setAdditionalUrlParam('option','com_jhotelreservation');
			$this->_pagination->setAdditionalUrlParam('view','hotels');
			$this->_pagination->setAdditionalUrlParam('Itemid','');
			$showAll = JRequest::getVar("showAll");
			if(!empty($showAll)){
				$this->_pagination->setAdditionalUrlParam('showAll',$showAll);
			}
			$this->_pagination->setAdditionalUrlParam('Itemid','');
			$this->_pagination->setAdditionalUrlParam('filterParams',rawurlencode($this->searchFilter["searchParams"]));
		}
		return $this->_pagination;
	}
	
	function getSuggestionsList($keyword){
		
		 $hotelTable =$this->getTable('hotels');
		 $limit = 5;
		 $cities = $hotelTable->getHotelCitiesSuggestions($keyword, $limit);
		 $provinces = $hotelTable->getHotelProvinceSuggestions($keyword, $limit);
		 $regions = $hotelTable->getHotelRegionSuggestions($keyword, $limit);
		 $hotels = $hotelTable->getHotelsSuggestions($keyword, $limit);
		 $accomodationTypes = $hotelTable->getHotelAccomodationTypeSuggestions($keyword, $limit);
		 $offerThemes = $hotelTable->getHotelOfferThemesSuggestions($keyword, $limit);
		 
		 //process regions
		 foreach ($regions as $region){
		 	$region->label = JText::_('LNG_'.strtoupper($region->label));
		 }
		 
		 $suggestionList = array();
		 if(!empty($cities))
			 $suggestionList = array_merge($suggestionList,$cities);
		 if(!empty($provinces))
			 $suggestionList = array_merge($suggestionList,$provinces);
		 if(!empty($regions))
		 	$suggestionList = array_merge($suggestionList,$regions);
		 if(!empty($hotels))
			$suggestionList = array_merge($suggestionList,$hotels);
		 if(!empty($accomodationTypes))
		 	$suggestionList = array_merge($suggestionList,$accomodationTypes);
		 if(!empty($offerThemes))
		 	$suggestionList = array_merge($suggestionList,$offerThemes);

		 foreach($suggestionList as &$item){
		 	$item->label = stripcslashes($item->label);
		 }

		$suggestionList = json_encode($suggestionList);
		
		return $suggestionList;
	}

    /**
     * Method to display the dynamic title on search result or filtered result
     * @return string
     */
    function getHotelTypes(){
        $pagination = $this->getPagination();
        $hotelType = HotelService::getHotelTypes($this->searchFilter["filterCategories"]["types"],$this->_total);
        return $hotelType;
    }
	
}

