<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.application.component.modelitem');
class JHotelReservationModelHotelRatings extends JModelLegacy{

	protected $item;



	/**

	* Get the message

	* @return string The message to be displayed to the user

	*/

	public function getReviewQuestions(){
			// Get all review questions
		$query = 	' SELECT * FROM #__hotelreservation_review_questions order by review_question_nr';
		$this->_db->setQuery( $query );
		return  $this->_db->loadObjectList();
	}

    public function getTranslatedReviewquestions($reservationLanguage)
    {
        $hoteltranslationsModel = new JHotelReservationLanguageTranslations();
        $reviewQuestions = $this->getReviewQuestions();
        foreach($reviewQuestions as $r){
            if($r->review_question_id > 0 ){
              return  $translatedReviewQuestion = $hoteltranslationsModel->getObjectTranslation(REVIEW_QUESTIONS_TRANSLATION, $r->review_question_id,$reservationLanguage);
            }
        }
    }
	
	public function getReviewRatingScale(){
		// Get all rating scales
		
		$query = 	' SELECT * FROM #__hotelreservation_review_rating_scale';
		$this->_db->setQuery( $query );
		$this->_data = $this->_db->loadObjectList();
		
		return $this->_data;
	}
	
	public function getReservationDetails(){
		$reservationDetails = new stdClass();
		$id= JRequest::getVar('confirmation_id');
		$reservationDetails->confirmation_id = $id;
		$table = $this->getTable('Confirmations', 'Table');
		$table->load($id);

		$hotelService = new HotelService();
		$hotel	=  $hotelService->getHotel($table->hotel_id);

		$reservationDetails->hotelDetails = $hotel->hotel_name.", ".$hotel->hotel_address.", ".$hotel->hotel_city.", ".$hotel->country_name;
		$reservationDetails->arrivalDate = JHotelUtil::getDateGeneralFormat($table->start_date);
		$reservationDetails->returnDate =  JHotelUtil::getDateGeneralFormat($table->end_date);
		$reservationDetails->bookingPerson = $table->last_name;
		$reservationDetails->hotelId = $table->hotel_id;
		return $reservationDetails;
	}
	
	public function getCustomerReview(){
		$reservationDetails = null;
		$id= JRequest::getVar('confirmation_id');
		if(isset($id)){
			$query = 'SELECT * FROM #__hotelreservation_review_customers where confirmation_id='.$id;
			$this->_db->setQuery( $query );
			return $this->_db->loadObject();
		}
		else 
			return null;
		
	}
	public function getCustomerReviewById(){
		$reservationDetails = null;
		$id= JRequest::getVar('review_id');
		$query = 'SELECT * FROM #__hotelreservation_review_customers where review_id='.$id;
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	
	}

    public function getReviewComments(){
        $id = JRequest::getVar('review_id');
        $comments = HotelService::getHotelCommentsReviews($id);
        return $comments;
    }
	
	function &getReservationInfo($id)
	{
		// Load the data
		$hotel = null;
		if($id==0)
		return $hotel;
		$query = ' SELECT
							h.*,
							c1.country_name,
							c2.description	AS hotel_currency
						FROM #__hotelreservation_hotels 			h
						LEFT JOIN #__hotelreservation_countries 	c1 USING (country_id)
						LEFT JOIN #__hotelreservation_currencies 	c2 USING (currency_id)
						
						WHERE h.hotel_id = '.$id.' AND h.is_available = 1 
						ORDER BY h.hotel_name';
		$this->_db->setQuery( $query );
		$hotel = $this->_db->loadObject();
	
		return $hotel;
	}

	function getReview($reviewID){
		$table = $this->getTable('ReviewCustomers', 'Table');
		$table->load($reviewID);
		return $table;
	}
	function getReviewAnswers(){
		$reviewId= JRequest::getVar('review_id');
		$reviewAnswersTbl = $this->getTable('ReviewAnswers', 'Table');
		$answers = $reviewAnswersTbl->getReviewAnswers($reviewId);
		return $answers;
	}
	
	function submitReview(){
		$post = JRequest::get('post');
        $contactReviewer = isset($post['contact'])?(bool)$post['contact']:false;
	 	$table = $this->getTable('ReviewCustomers', 'Table');
		$table->bind($post);
		$table->review_date = date("Y-m-d");
		if (!$table->store()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		$reviewId = $table->review_id;
		$reviewAnswersTbl = $this->getTable('ReviewAnswers', 'Table');
        $reservationService = new ReservationService();
        $reservationDetails	=  $reservationService->getReservation($table->confirmation_id);
        $reviewQuestions = $this->getReviewQuestions();

		foreach($reviewQuestions as $reviewQuestion ){
			if(isset($post["question_".$reviewQuestion->review_question_id])){
				$reviewAnswersTbl->review_question_id = $reviewQuestion->review_question_id;
				$reviewAnswersTbl->review_id = $reviewId;
				$reviewAnswersTbl->rating_scale_id = $post["question_".$reviewQuestion->review_question_id];
				if (!$reviewAnswersTbl->store()) {
					$this->setError( $this->_db->getErrorMsg() );
					throw new Exception('Error saving answers'.$this->_db->getErrorMsg());
					return false;
				}
			}
		}
		
		$properties = $table->getProperties(1);
		$review = JArrayHelper::toObject($properties, 'JObject');
        $review->contact = '';
        if(isset($contactReviewer) && $contactReviewer == true ) {
            $review->contact = JText::_('LNG_REQUEST_CONTACT',true);
        }
		$hotelID = $this->getHotelId($table->confirmation_id);
		$this->calculateHotelReview($hotelID);
		$emailService = new EmailService();
		$emailService->sendReviewSubmitedEmail($reservationDetails, $review);
	}

	function calculateHotelReview($hotelId){
		$hotelratings = $this->getTable('reviewcustomers');
		$hotelratings->calculateHotelRatingScore($hotelId);
	}	
	
	function getHotelId(){
		$id= JRequest::getVar('confirmation_id');
		$reservationDetails->confirmation_id = $id;
		$table = $this->getTable('Confirmations', 'Table');
		$table->load($id);
		return $table->hotel_id;
	}
	
	function sendReviews(){
		$table 	= $this->getTable('Confirmations', 'Table');
		$reviews = $table->getReviewsToSend();
		foreach($reviews as $review){
			$reservationService = new ReservationService();
			$reservationDetails	=  $reservationService->getReservation($review->confirmation_id);
			$emailService = new EmailService();
			$emailService->sendReviewEmail($reservationDetails);
			$table->load($review->confirmation_id);
			$table->review_email_date = date('Y-m-d H:i:s');
			$table->store();
		}
			exit;	
	}

    public function getReviewHotel(){
        $id= JRequest::getVar('review_id');
        if($id > 0 ) {
            $hotelratings = $this->getTable('reviewcustomers');
            $hotel = $hotelratings->getReviewHotel($id);
            return $hotel;
        }
    }
}

