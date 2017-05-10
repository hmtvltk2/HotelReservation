<?php

class ReviewsService{

	static function getSelectedRatingScale($ratingAnswers,$questionId){
		foreach($ratingAnswers as $ratingAnswer){
			if ($ratingAnswer->review_question_id==$questionId){
				return $ratingAnswer->rating_scale_id;
			}
		}
	}
	
	//generate meta data review print
	public static function generateReviewMetaData($hotel,$customerReview){
		$document = JFactory::getDocument();
		$hotelUrl = JURI::root().'index.php?option='.getBookingExtName().'&task=hotelratings.printrating&view=hotelratings&review_id='.$customerReview->review_id;
		
		
		$reviewContet = $customerReview->review_short_description.' '.$customerReview->review_remarks;
		
		$title = JText::_('LNG_HOTEL', true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city . ", " . $hotel->hotel_country . ", " . JText::_("LNG_READ_WHAT_PEOPLE_SAID", true) ." : ";
		$description = JText::_('LNG_READ_REVIEWS_OF_HOTEL', true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city . ", " . $hotel->hotel_country . ". " . JText::_("LNG_READ_WHAT_PEOPLE_SAID", true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city . ", " . $hotel->hotel_country;
		
		$document->setBase($hotelUrl);
		$document->setTitle($title);
		$document->setDescription($description);
		$document->setMetaData('keywords', JText::_('LNG_HOTEL_REVIEW', true) . " " . $hotel->hotel_name . " " . $hotel->hotel_city . ", " . $hotel->hotel_country . " / " . JText::_('LNG_EXPERIENCES', true) . " " . $hotel->hotel_name . " " . JText::_("LNG_IN", true) . " " . $hotel->hotel_city . " / " . JText::_("LNG_WHAT_PEOPLE_FIND_OF", true) . " " . $hotel->hotel_name . " " . JText::_("LNG_IN", true) . " " . $hotel->hotel_city . "/ " . JText::_('LNG_CUSTOMER_REMARKS', true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city);
		
		$document->addCustomTag('<meta property="og:title" content="' . $title.PHP_EOL.$reviewContet. '"/>');
		$document->addCustomTag('<meta property="og:description" content="' . $description . '"/>');
		$document->addCustomTag('<meta property="og:image" content="' . JURI::base() . 'images' . DS . 'icon-facebook.jpg" /> ');
		$document->addCustomTag('<meta property="og:type" content="website"/>');
		$document->addCustomTag('<meta property="og:url" content="' . $hotelUrl . '"/>');
		$document->addCustomTag('<meta property="og:site_name" content="' . $hotelUrl . '"/>');
		$document->addCustomTag('<meta property="fb:admins" content=""/>');
	}
	
	
	//generate meta data reviews
	public static function generateReviewsMetaData($hotel){
		$document = JFactory::getDocument();
		$config = JFactory::getConfig();
		$document = JFactory::getDocument();
		
		$hotelUrl = JURI::root().'index.php?option='.getBookingExtName().'&task=hotelratings.printrating&view=hotelratings&review_id='.$customerReview->review_id;
		
		
		$title = JText::_('LNG_HOTEL', true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city . ", " . $hotel->hotel_county . ", " . JText::_('LNG_READ_REVIEWS_AT', true) . " " . $config->get('config.sitename');
		$description = JText::_('LNG_READ_REVIEWS_OF_HOTEL', true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city . ", " . $hotel->hotel_county . ". " . JText::_("LNG_READ_WHAT_PEOPLE_SAID", true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city . ", " . $hotel->hotel_county;
		
		$document->setTitle($title);
		$document->setDescription($description);
		$document->setMetaData('keywords', JText::_('LNG_HOTEL_REVIEW', true) . " " . $hotel->hotel_name . " " . $hotel->hotel_city . ", " . $hotel->hotel_county . " / " . JText::_('LNG_EXPERIENCES', true) . " " . $hotel->hotel_name . " " . JText::_("LNG_IN", true) . " " . $hotel->hotel_city . " / " . JText::_("LNG_WHAT_PEOPLE_FIND_OF", true) . " " . $hotel->hotel_name . " " . JText::_("LNG_IN", true) . " " . $hotel->hotel_city . "/ " . JText::_('LNG_CUSTOMER_REMARKS', true) . " " . $hotel->hotel_name . " " . JText::_('LNG_IN', true) . " " . $hotel->hotel_city);
		
		$reviewContet = $title.PHP_EOL.JText::_('LNG_REVIEW').' @ ';
		
		$document->addCustomTag('<meta property="og:title" content="' . $title . '"/>');
		$document->addCustomTag('<meta property="og:description" content="' . $description . '"/>');
		$document->addCustomTag('<meta property="og:image" content="' . JURI::base() . 'images' . DS . 'icon-facebook.jpg" /> ');
		$document->addCustomTag('<meta property="og:type" content="website"/>');
		$document->addCustomTag('<meta property="og:url" content="' . $hotelUrl . '"/>');
		$document->addCustomTag('<meta property="og:site_name" content="' . $config->get('config.sitename') . '"/>');
		$document->addCustomTag('<meta property="fb:admins" content="george.bara"/>');
	}

	/**
	 * @param $hotelId
	 * @param $translations
	 * @param $languageTag
	 * @param $hotel_rating_score
	 *
	 * @return mixed
	 */
	public static function getHotelRatingClassifications($hotelRatingScore,$translations,$languageTag){
		$hotelRatingScore = self::getHotelRatingClassification($hotelRatingScore);

		if(isset($hotelRatingScore->ratingScores) && count($hotelRatingScore->ratingScores)>0){
			foreach($hotelRatingScore->ratingScores as $ratingScore){
				if(isset($ratingScore->id) && $ratingScore->id > 0){
					$hotelRatingScoreTranslations = $translations->getObjectTranslation(RATE_CLASSIFICATION_TRANSLATION,$ratingScore->id,$languageTag);
					$ratingScore->name = !empty($hotelRatingScoreTranslations->content)?$hotelRatingScoreTranslations->content:$ratingScore->name;
				}
			}
		}
		return $hotelRatingScore;
	}

	/**
	 * @param $hotelId
	 * @param $hotel_rating_score
	 *
	 * @return mixed
	 */
	public static function getHotelRatingClassification($hotel_rating_score){
		$ratingScoresTable	= JTable::getInstance('RatingClassifications','JTable', array());
		return $ratingScoresTable->getHotelRatingClassification($hotel_rating_score);
	}
	public static function getRatingSummaryHtml($hotel,$nrReviews){
		ob_start();
	?>
		<div class="hotel-rating right">
			<div class="info">
				<?php if ( isset( $hotel->ratingScores ) && count( $hotel->ratingScores ) > 0 ){ ?>
				<div>
					<div class="rating-classification">
						<?php foreach ( $hotel->ratingScores as $ratingScore ) { ?>
							<div class="right">
								<?php echo $ratingScore->name; ?>
							</div><br>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
				
				<div class="ratingCount right">
					<a href="<?php echo JHotelUtil::getHotelLink( $hotel ) . '?' . strtolower( JText::_( "LNG_REVIEWS" ) ) ?>"><?php echo $nrReviews; ?> <?php echo JText::_( 'LNG_REVIEWS' ) ?></a>
				</div>
			</div>
			<div class="rating">
				<?php echo JHotelUtil::fmt( $hotel->hotel_rating_score, 1 ) ?>
			</div>
			<div class="clear"></div>
		</div>
	<?php						
		$buff = ob_get_contents();
		ob_end_clean();
			
		return $buff;
	 }
}