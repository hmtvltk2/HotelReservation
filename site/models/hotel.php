<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelHotel extends JModelItem{
	var $totalReviewsCount = 0;
	
	protected function populateState(){
		$app = JFactory::getApplication('site');
		
		// Load state from the request.
		$pk = JRequest::getInt('hotel_id');
		$this->setState('hotel.id', $pk);
		
		$tabId = JRequest::getInt('tabId',1);
		$this->setState('hotel.tabId', $tabId);
		$mainframe = JFactory::getApplication();
		
		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		UserDataService::updateUserData();
	}
	
	
	public function getItem($pk = null){
		// Initialise variables.
		$hotel = HotelService::getHotel($this->getState('hotel.id'));
		$hotel->reviews = $this->getHotelReviews($this->getState('hotel.id'));
	
		$hotel->overviewTranslation = JText::_("LNG_HOTEL_OVERVIEW");
		$hotel->faciltiesTranslation = JText::_("LNG_FACILITIES");
		$hotel->roomSpecialsTranslation = JText::_("LNG_SEARCH_ROOMS_SPECIALS");
		$hotel->roomSelectionTranslation = JText::_("LNG_CHOOSE_YOUR_ROOM");
		$hotel->reviewScoreTranslation = JText::_("LNG_REVIEW_TOTAL_SCORE");
		$hotel->infoTranslation = JText::_("LNG_HOTEL_IMPORTANT_INFORMATION");
		$hotel->nrRoomsTranslation = JText::_("LNG_NUMBER_OF_ROOMS");
		$hotel->suitableDisabledTranslation = JText::_("LNG_NO_SUITABLE_DISABLED");
		$hotel->suitableDisabledAvailableTranslation = JText::_("LNG_SUITABLE_DISABLED_AVAILABLE");
		$hotel->paymentOptionsTranslation = JText::_("LNG_HOTEL_PAYMENT_OPTIONS");
		$hotel->capacityTranslation = JText::_("LNG_CAPACITY_PERS");
		$hotel->specialOffersTranslation = JText::_("LNG_SPECIAL_OFFERS");
		
		if(!empty($hotel->types) && !empty($hotel->types[0]->name)){
			$overviewTranslation = JText::_("LNG_".$hotel->types[0]->name."_OVERVIEW");
			$hotel->overviewTranslation = !strpos($overviewTranslation,"LNG_")? JText::_("LNG_HOTEL_OVERVIEW") : $overviewTranslation;
				
			$faciltiesTranslation = JText::_("LNG_".$hotel->types[0]->name."_FACILITIES");
			$hotel->faciltiesTranslation = !strpos($faciltiesTranslation,"LNG_")? JText::_("LNG_FACILITIES") : $faciltiesTranslation;
				
			$roomSpecialsTranslation = JText::_("LNG_SEARCH_".$hotel->types[0]->name."_SPECIALS");
			$hotel->roomSpecialsTranslation = !strpos($roomSpecialsTranslation,"LNG_SEARCH_")? JText::_("LNG_SEARCH_ROOMS_SPECIALS") : $roomSpecialsTranslation;
				
			$roomSelectionTranslation = JText::_("LNG_CHOOSE_YOUR_".$hotel->types[0]->name);
			$hotel->roomSpecialsTranslation = !strpos($roomSelectionTranslation,"LNG_CHOOSE_YOUR_")? JText::_("LNG_CHOOSE_YOUR_ROOM") : $roomSelectionTranslation;
				
			$reviewScoreTranslation = JText::_("LNG_REVIEW_TOTAL_SCORE_".$hotel->types[0]->name);
			$hotel->reviewScoreTranslation = !strpos($reviewScoreTranslation,"LNG_REVIEW_TOTAL_SCORE_")? JText::_("LNG_REVIEW_TOTAL_SCORE") : $reviewScoreTranslation;
		
			$infoTranslation = JText::_("LNG_".$hotel->types[0]->name."_IMPORTANT_INFORMATION");
			$hotel->infoTranslation = !strpos($infoTranslation,"LNG_")? JText::_("LNG_HOTEL_IMPORTANT_INFORMATION") : $infoTranslation;
		
			$nrRoomsTranslation = JText::_("LNG_NUMBER_OF_ROOMS_".$hotel->types[0]->name."");
			$hotel->nrRoomsTranslation = !strpos($nrRoomsTranslation,"LNG_")? JText::_("LNG_NUMBER_OF_ROOMS") : $nrRoomsTranslation;
				
			$suitableDisabledTranslation = JText::_("LNG_NO_SUITABLE_DISABLED_".$hotel->types[0]->name."");
			$hotel->suitableDisabledTranslation = !strpos($suitableDisabledTranslation,"LNG_")? JText::_("LNG_NO_SUITABLE_DISABLED") : $suitableDisabledTranslation;
				
			$suitableDisabledAvailableTranslation = JText::_("LNG_SUITABLE_DISABLED_AVAILABLE_".$hotel->types[0]->name."");
			$hotel->suitableDisabledAvailableTranslation = !strpos($suitableDisabledAvailableTranslation,"LNG_")? JText::_("LNG_SUITABLE_DISABLED_AVAILABLE") : $suitableDisabledAvailableTranslation;
				
			$paymentOptionsTranslation = JText::_("LNG_".$hotel->types[0]->name."_PAYMENT_OPTIONS");
			$hotel->paymentOptionsTranslation = !strpos($paymentOptionsTranslation,"LNG_")? JText::_("LNG_HOTEL_PAYMENT_OPTIONS") : $paymentOptionsTranslation;
		
			$capacityTranslation = JText::_("LNG_CAPACITY_PERS_".$hotel->types[0]->name."");
			$hotel->capacityTranslation = !strpos($capacityTranslation,"LNG_")? JText::_("LNG_CAPACITY_PERS") : $capacityTranslation;
				
			$specialOffersTranslation = JText::_("LNG_SPECIAL_OFFERS_".$hotel->types[0]->name."");
			$hotel->specialOffersTranslation = !strpos($specialOffersTranslation,"LNG_")? JText::_("LNG_SPECIAL_OFFERS") : $specialOffersTranslation;
				
		}
		
		$userData = UserDataService::getUserData();
		if(empty($userData->currency->symbol)){
			UserDataService::setCurrency($hotel->hotel_currency, $hotel->currency_symbol);
		}
		
		return $hotel;
	}
	
	function getOffers(){
		$userData = UserDataService::getUserData();
		$appSettings = JHotelUtil::getApplicationSettings();
		
		$offers =  HotelService::getHotelOffers($this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		
		foreach ($offers as $idx=>$offer){
			
			//when searching with voucher only offer with searched voucher should be displayed
			if($userData->voucher!=''){
				$voucherFound = false;
				if(isset($offer->vouchers) && count($offer->vouchers)){
					foreach ($offer->vouchers as $voucher){
						//dmp($voucher);
						if( strcasecmp($voucher ,$userData->voucher)==0){
							$voucherFound = true;
						}
					}
				}
				if(!$voucherFound)
					unset($offers[$idx]);
			}
			
			//when searching without voucher, offers with voucher should not be visible
			if(($userData->voucher=='' && isset($offer->vouchers) && count($offer->vouchers)>0) && !$offer->public){
					unset($offers[$idx]);
			}

			$capacityExceeded = false;
			$capacityFullfilled= true;
			$overAvailability = false;
			$nrNights = UserDataService::getNrDays();
			

			if(isset($userData->roomGuests) & $userData->adults > $offer->max_adults){
				$capacityExceeded = true;
			}else if(!isset($userData->roomGuests) && $offer->max_adults < $userData->adults){
				$capacityExceeded = true;
			}				
			else if(($appSettings->show_children) && (!empty($userData->roomGuestsChildren) && isset($userData->roomGuestsChildren[count($userData->reservedItems)]) && $userData->roomGuestsChildren[count($userData->reservedItems)] > $offer->base_children)){
				$capacityExceeded = true;
			}
			else if(($appSettings->show_children) && !isset($userData->roomGuestsChildren) && ($offer->base_children < $userData->children)){
				$capacityExceeded = true;
			}
			else if($offer->offer_min_pers > ($userData->adults+$userData->children)){
				$capacityFullfilled = false;
			}
			else if($offer->offer_max_nights<$nrNights && $offer->apply_max_nights){
				$overAvailability = true;
			}
			
			//calculate capacity combined
		    if($appSettings->capacity_calculation==1){
		    	$nrPersons = $this->getReservationTotalPersons($userData);
		    	if($nrPersons>$offer->max_adults)
		    		$capacityExceeded = true;
		    }
			
			$offer->capacityExceeded = $capacityExceeded; 
			$offer->capacityFullfilled = $capacityFullfilled;
			$offer->overAvailability = $overAvailability;
			
		}
		return $offers;
	}
	
	function getRooms(){
		$userData = UserDataService::getUserData();
		$rooms = HotelService::getHotelRooms($this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		$appSettings = JHotelUtil::getApplicationSettings();
		
		if($userData->voucher!=''){
			$rooms = new stdClass();
		}

		foreach ($rooms as $idx=>$room){
			$capacityExceeded = false;
			if((isset($userData->roomGuests) && !empty($userData->roomGuests[count($userData->reservedItems)]) &&  $userData->roomGuests[count($userData->reservedItems)] > $room->max_adults)){
				$capacityExceeded = true;
			}
			else if(!isset($userData->roomGuests) && ($room->max_adults < $userData->adults)){
				$capacityExceeded = true;
			}else if(($appSettings->show_children) && (!empty($userData->roomGuestsChildren) && isset($userData->roomGuestsChildren[count($userData->reservedItems)]) && $userData->roomGuestsChildren[count($userData->reservedItems)] > $room->base_children)){
				$capacityExceeded = true;
			}
			else if(($appSettings->show_children) && !isset($userData->roomGuestsChildren) && ($room->base_children < $userData->children)) {
				$capacityExceeded = true;
			}
			
			//calculate capacity combined
			if($appSettings->capacity_calculation == 1){
				$nrPersons = $this->getReservationTotalPersons($userData);
				if($nrPersons>$room->max_adults)
					$capacityExceeded = true;
			}

			$room->capacityExceeded  = $capacityExceeded;
			
			if(!$room->front_display){
				unset($rooms[$idx]);
			}
		}
		
		return $rooms;
	}
	
	
	function getReservationTotalPersons($userData){
		$totalPers = 0;
		$appSettings = JHotelUtil::getApplicationSettings();
		
		if(isset($userData->roomGuests)){
			$totalPers += $userData->roomGuests[count($userData->reservedItems)];
		}else if(!isset($userData->roomGuests)){
			$totalPers += $userData->adults;
		}
		
		if(($appSettings->show_children) && (!empty($userData->roomGuestsChildren) && isset($userData->roomGuestsChildren[count($userData->reservedItems)]))){
			$totalPers += $userData->roomGuestsChildren[count($userData->reservedItems)];
		}
		else if(($appSettings->show_children) && !isset($userData->roomGuestsChildren)){
			$totalPers += $userData->children;
		}
		
		return $totalPers;
		
	} 
	
	function getExcursions(){
		$userData = UserDataService::getUserData();
		$excursions = ExcursionsService::getHotelExcursions(HOTEL_EXCURSIONS,$this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		return $excursions;
	}
	
	function getCourses(){
		$userData = UserDataService::getUserData();
		$excursions = ExcursionsService::getHotelExcursions(HOTEL_COURSES,$this->getState('hotel.id'), $userData->start_date, $userData->end_date, array(),$userData->adults,$userData->children);
		return $excursions;
	}

    function getHotelBreadCrumb(){

        $appSettings = JHotelUtil::getInstance()->getApplicationSettings();
        if($appSettings->enable_breadcrumb) {
            $hotelTable =$this->getTable('hotels');
            $hotel = $this->getItem();

            $hotelType = $hotelTable->getHotelType($this->getState('hotel.id'));
            $hotelRegion = $hotelTable->getHotelRegion($this->getState('hotel.id'));
            $cityCount = $hotelTable->getHotelsNumberByCity($hotel->hotel_city);
            
            $regionContent = "";
            if(!empty($hotelRegion->regionId)){
            $regionCount = $hotelTable->getRegionCount($hotelRegion->regionId);
            $regionTranslationValue = JText::_('LNG_' . strtoupper(str_replace(" ", "_", $hotelRegion->name)));
            $region = $regionTranslationValue . "(" . $regionCount->regionCount . ")";
	            $regionContent = '<a href="'.JRoute::_('index.php?option=com_jhotelreservation&view=hotels&filterParams=regionId=' . $hotelRegion->regionId).'">'.$region.'</a> <i class="fa fa-angle-double-right"> </i>';
            }

            $typeContent = "";
            if(!empty($hotelType->typeId)){
               $typeCount = $hotelTable->getTypeCount($hotelType->typeId);
            $typeTranslationValue = JText::_('LNG_' . strtoupper(str_replace(" ", "_", $hotelType->name)));
            $type = $typeTranslationValue . "(" . $typeCount->typeCount . ")";
         	   $typeContent = '<a href="'.JRoute::_('index.php?option=com_jhotelreservation&view=hotels&filterParams=typeId=' . $hotelType->typeId).'">'.$type.'</a>,';
            }
            
            $hotel_city = isset($cityCount) ? $hotel->hotel_city . "(" . $cityCount . ")" : $hotel->hotel_city;

            ob_start();
            ?>
            <div class="hotel-breadcrumb">
                <ul>
                    <li>
                       <?php echo JText::_('LNG_PROPERTIES'); ?> <i class="fa fa-angle-double-right"> </i>
                        <?php echo $regionContent. $typeContent;?>
                        <a href="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotels&city=' . $hotel->hotel_city) ?>">
                       	<?php echo $hotel_city ?> </a> <i class="fa fa-angle-double-right"> </i>
                       <?php echo $hotel->hotel_name ?>
                    </li>
                </ul>
            </div>
            <?php
            $buff = ob_get_contents();
            ob_end_clean();

            return $buff;
        }else{
            return '';
        }
    }



    private function saveCommentHTML($reviewComment,$reviewId,$userId){
        $user = JFactory::getUser();
        ob_start();
        ?>
        <div id="commentBlock_<?php echo $reviewComment->id ?>" class="commentBlock">
            <ul id="innerCommentBlock_<?php echo $reviewComment->id ?>" class="innerCommentBlock">
                <li >
                    <header>
                        <label class="author"> <?php echo $reviewComment->name ?> </label>
                        <p> <?php echo $reviewComment->name ?> </p>
                    </header>
                    <article>
                        <p class="comment"><?php echo $reviewComment->comment; ?></p>
                        <?php
                        if ($userId ==  $reviewComment->userId || $user->get('isRoot')) { ?>
                            <span class="editButtons">
                                <button
                                    class="cancel"
                                    onclick="deleteComment(<?php echo $reviewId ?>,<?php echo $reviewComment->id ?>)">
                                    <i class="fa fa-times"></i>
                                </button>
                                <button
                                    class="save"
                                    onclick="editCommentHTML(<?php echo $reviewId ?>,<?php echo $reviewComment->id ?>,'<?php echo (string)$reviewComment->comment; ?>','<?php echo (string)$reviewComment->name?>')">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            </span>
                        <?php } ?>
                    </article>
                </li>
            </ul>
        </div>
        <?php
        $buff = ob_get_contents();
        ob_end_clean();
        return $buff;
    }

    public function saveComment($post){

        if(isset($post['comment']) && isset($post['userId']) && isset($post['reviewId']))
        {
            $table = $this->getTable("ReviewComments","JTable");

            // Bind the data.
            if (!$table->bind($post))
            {
                $this->setError($table->getError());
            }

            // Check the data.
            if (!$table->check())
            {
                $this->setError($table->getError());
            }

            // Store the data.
            if (!$table->store())
            {
                $this->setError($table->getError());
            }
                $id = $this->_db->insertid();

                $userId   =  $table->userId;
                $comment  =  $table->comment;
                $reviewId =  $table->reviewId;

                $reviewComment = $table->getReviewComment($id,$reviewId,$userId);
                echo $this->saveCommentHTML($reviewComment,$reviewId,$post["userId"]);
            exit;
        }
    }

    private function editCommentHTML($reviewComment,$reviewId,$userId){
        $user = JFactory::getUser();
        ob_start();
        ?>
        <ul id="innerCommentBlock_<?php echo $reviewComment->id ?>" class="innerCommentBlock">
            <li>
                <header>
                    <label class="author"> <?php echo $reviewComment->name ?> </label>

                    <p> <?php echo $reviewComment->name ?> </p>
                </header>
                <article>
                    <p class="comment"><?php echo $reviewComment->comment; ?></p>
                    <?php
                    if ($userId ==  $reviewComment->userId  || $user->get('isRoot')) { ?>
                        <span class="editButtons">
                            <button
                                class="cancel"
                                onclick="deleteComment(<?php echo $reviewId ?>,<?php echo $reviewComment->id ?>)">
                                <i class="fa fa-times"></i>
                            </button>
                            <button
                                class="save"
                                onclick="editCommentHTML(<?php echo $reviewId ?>,<?php echo $reviewComment->id ?>,'<?php echo (string)$reviewComment->comment; ?>','<?php echo (string)$reviewComment->name?>')">
                                <i class="fa fa-pencil"></i>
                            </button>
                        </span>
                    <?php } ?>
                </article>
            </li>
        </ul>
        <?php
        $buff = ob_get_contents();
        ob_end_clean();
        return $buff;
    }

    public function editComment($post)
    {

        if (isset($post['comment']) && isset($post['userId']) && isset($post['reviewId']) && isset($post['id'])) {
            $table = $this->getTable("ReviewComments", "JTable");

            $comment = $post['comment'];
            $userId = $post['userId'];
            $id = $post['id'];
            $reviewId = $post['reviewId'];

            $reviewComment = $table->updateReviewComment($id, $reviewId, $comment, $userId);
            echo $this->editCommentHTML($reviewComment,$reviewId,$userId);
            exit;
        }
    }



    public function deleteComment($post){

        if(isset($post['id']) && isset($post['userId']) && isset($post['reviewId']))
        {
            $table = $this->getTable("ReviewComments","JTable");

            $commentId =  $post['id'];
            $reviewId  =  $post['reviewId'];

            $table->deleteCommentByReview($commentId,$reviewId);
            exit();
        }
    }
    
    public function getHotelReviews($hotelId){
    	$reviewAnswersTable	= JTable::getInstance('ReviewAnswers','Table', array());
    	$reviews = $reviewAnswersTable->getHotelReviews($hotelId, $this->getState('limitstart'), $this->getState('limit'));
    	
    	//get the comments for each review made
    	if(!empty($reviews)){
    		$this->totalReviewsCount = empty($reviews[0]->totalReviewsCount)?0:$reviews[0]->totalReviewsCount;
    		foreach($reviews as $review){
    			if(isset($review->review_id) && $review->review_id > 0 ) {
    				$review->comments = HotelService::getHotelCommentsReviews($review->review_id);
    			}
    		}
    	}
    	return $reviews;
    }
    
    function getReviewsPagination()
    {
    	// Load the content if it doesn't already exist
    	if (empty($this->_pagination)) {
    		jimport('joomla.html.pagination');
    		$total = $this->totalReviewsCount;
    		$this->_pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit') );
    		$this->_pagination->setAdditionalUrlParam('option','com_jhotelreservation');
    		$this->_pagination->setAdditionalUrlParam('view','hotel');
    		$showAll = JRequest::getVar("showAll");
    		if(!empty($showAll)){
    			$this->_pagination->setAdditionalUrlParam('showAll',$showAll);
    		}
    		$this->_pagination->setAdditionalUrlParam('Itemid','');
    	}
    	return $this->_pagination;
    }
	
}