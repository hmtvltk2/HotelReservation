<?php 
class ChildrenCategoryService{

	public static function getChildrenCategories($hotelId=-1,$active=1){

		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
	
		$db = JFactory::getDBO();
		$query = " SELECT h.*
				   FROM #__hotelreservation_children_categories h
					where h.status =  $active
					and h.hotel_id = $hotelId
					ORDER BY h.id asc";
		$db->setQuery( $query );
		$childrenCategories = $db->loadObjectList();
		
		if(empty($childrenCategories)){//get default categories
			$query = " SELECT h.*
						FROM #__hotelreservation_children_categories h
						where h.status =  $active
						and h.hotel_id = -1
						ORDER BY h.id asc";
			$db->setQuery( $query );
			$childrenCategories = $db->loadObjectList();
		}
        $translationTable = new JHotelReservationLanguageTranslations();
        if(count($childrenCategories))
        {
            foreach($childrenCategories as $childrenCategory){
                $translationChildrenCategory = $translationTable->getObjectTranslation(CHILDREN_CATEGORY_TRANSLATION,$childrenCategory->id,$languageTag);
                $childrenCategory->category_name = !empty($translationChildrenCategory->content)?$translationChildrenCategory->content:"";
            }
        }
        return $childrenCategories;
	}
	
	public static function  getChildrenCategoryPrices($rateID=0){
		
		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
		
		$rateID = empty($rateID)?0:$rateID;
		
		$db = JFactory::getDBO();
		$query = " SELECT cg.id as category_id, rcm.price
				   FROM #__hotelreservation_children_categories cg
				   inner join #__hotelreservation_children_categories_prices rcm on cg.id= rcm.category_id 
				   WHERE rcm.rate_id = $rateID
				   ORDER BY cg.id";
		$db->setQuery( $query );
		return $db->loadObjectList('category_id');
	}
	
	public static function getChildrenCategoryCustomPrices($rateID=0,$startDate,$endDate){
	
		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
	
		$db = JFactory::getDBO();
		$query = " SELECT cg.id as category_id, rcm.price,rcm.date, cg.*
		FROM #__hotelreservation_children_categories cg
		inner join #__hotelreservation_children_categories_rate_prices rcm on cg.id= rcm.category_id
		WHERE rcm.rate_id = $rateID
			  and rcm.date>='$startDate'
			  and rcm.date<='$endDate'
		ORDER BY cg.id";
		$db->setQuery( $query );

		return $db->loadObjectList('category_id');
	}
	
	public static function getChildrenCustomPrices($rateID=0,$startDate,$endDate,$categoryId){
	
		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
	
		$db = JFactory::getDBO();
		$query = " SELECT cg.id as category_id, rcm.price,rcm.date, cg.*
		FROM #__hotelreservation_children_categories cg
		inner join #__hotelreservation_children_categories_rate_prices rcm on cg.id= rcm.category_id
		WHERE rcm.rate_id = $rateID
		and rcm.date>='$startDate'
		and rcm.date<='$endDate'
		and cg.id = $categoryId
		ORDER BY cg.id";
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
	
	public static function  getRoomChildrenCategoryPrices($roomId=0,$categoryId){
	
		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
	
		$db = JFactory::getDBO();
		$query = "  SELECT cg.id as category_id, rcm.price, cg.*
					FROM #__hotelreservation_children_categories cg
					inner join #__hotelreservation_children_categories_prices rcm on cg.id= rcm.category_id
					inner join #__hotelreservation_rooms_rates rr on rcm.rate_id = rr.id
					WHERE rr.room_id = $roomId
					and cg.id = $categoryId
					ORDER BY cg.id";
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
	
	public static function  clearChildrenPrices($rateID=0){
	
		$db = JFactory::getDBO();
		$rateID = empty($rateID)?0:$rateID;
		
		$query = " delete from #__hotelreservation_children_categories_prices WHERE rate_id = $rateID";
		$db->setQuery( $query );
		return $db->query();
	}
	
	public static function getHotelChildrenCategories($active=1){
	
		$language = JFactory::getLanguage();
		$languageTag = $language->getTag();
	
		$db = JFactory::getDBO();
		$query = " SELECT h.*
				   FROM #__hotelreservation_children_categories h
							where h.status =  $active
							ORDER BY h.id asc";
		$db->setQuery( $query );
        $childrenCategories =  $db->loadObjectList();

        $translationTable = new JHotelReservationLanguageTranslations();
        if(count($childrenCategories))
        {
            foreach($childrenCategories as $childrenCategory){
                $translationChildrenCategory = $translationTable->getObjectTranslation(CHILDREN_CATEGORY_TRANSLATION,$childrenCategory->id,$languageTag);
                $childrenCategory->category_name = $translationChildrenCategory->content;
            }
        }
        return $childrenCategories;
	}
	
	
	static function getReservationCategoryPrices($room,$roomRateDetails,$defaultChildPrice,$hotelId,$startDate,$endDate){
		
		//check if children category prices are set
		
		$childrenCategoryTotal = 0;
		
		$childrenCategories = ChildrenCategoryService::getChildrenCategories($hotelId);
		$userData = UserDataService::getUserData();
		$selectedAges = array();
		$current = -1;
		if(!empty($userData->reservedItems)){//multiple rooms
			foreach($userData->reservedItems as $reservedItem){
				$items = explode("|",$reservedItem);
				if($items[1]==$room->room_id){
					$current = $items[2];
				}
			}
		}
		if(!empty($userData->roomChildrenAges[$current])){ //multiple rooms booked
			$selectedAges = $userData->roomChildrenAges[$current];
		}
		else if(isset($userData->jhotelreservation_child_ages)) //single room booked
			$selectedAges = $userData->jhotelreservation_child_ages;
		
		if(count($selectedAges)){
			foreach($selectedAges as $childAge){ //loop through selected ages to retrieve rates
		
				$found = false;
				foreach ($childrenCategories as $childCategory){
					if ($childAge>=$childCategory->min_age &&  $childAge<= $childCategory->max_age){ //find category of age
		
						if(count($roomRateDetails)){
							//retrive custom prices for children categories
							$categoryRates = ChildrenCategoryService::getChildrenCustomPrices($room->rate_id,$startDate,$endDate,$childCategory->id);
						}
						else{
							//retrive rate prices for children categories
							$categoryRates = ChildrenCategoryService::getRoomChildrenCategoryPrices($room->room_id,$childCategory->id);
						}
						foreach($categoryRates as $categoryRate){
								
							if(!empty($categoryRate->date)){ //check if custom rate is found
								for( $d = strtotime($startDate);$d < strtotime($endDate); ){
									if($categoryRate->date ==  date('Y-m-d', $d)){ //check if rate is set for the reservation day
										$childrenCategoryTotal +=  $categoryRate->price; //set custom price
										$found = true;
										$foundRate = true;
										
										break; //no need to loop through other rates
									}
									$d = strtotime( date('Y-m-d', $d).' + 1 day ');
								}
							}
							else{
								if(!empty($categoryRate->price)){
									$childrenCategoryTotal +=  $categoryRate->price; //set default price
									$foundRate = true;
									$found = true;
									break;
								}
							}
						}
					}
					if($found) //no need to loop through other categories
						break;
				}
				//apply default child rate
				if(!$found)
					$childrenCategoryTotal += $defaultChildPrice;
			}
		}
		//return rate total
		return $childrenCategoryTotal;
		
	}
	
	
	
	
	
}

?>