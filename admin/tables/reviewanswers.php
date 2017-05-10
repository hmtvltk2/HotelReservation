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

class TableReviewAnswers extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db) {
		parent::__construct('#__hotelreservation_review_answers', 'review_id,rating_scale_id,review_question_id', $db);

	}

	function setKey($k){
		$this->_tbl_key = $k;
	}
	function getReviewAnswers($reviewId){
		$query = ' SELECT *  FROM #__hotelreservation_review_answers where review_id = '.$reviewId.' ORDER BY review_question_id';
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getAverageReviewAnswersScoreByHotel($hotelId){
		$query = ' select rq.review_question_id,rq.review_question_desc as question, avg(rs.weight) as average
					from #__hotelreservation_review_answers ra 
					inner join #__hotelreservation_review_customers rc on ra.review_id = rc.review_id
					inner join #__hotelreservation_review_questions rq on ra.review_question_id = rq.review_question_id 
					inner join #__hotelreservation_review_rating_scale rs on rs.rating_scale_id = ra.rating_scale_id
					where rc.hotel_id = '.$hotelId.' and rs.weight<>0 
					group by rq.review_question_id ORDER BY rq.review_question_nr';

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	function getHotelReviews($hotelId,$limitStart=0,$limit=0){
		
		$query = ' select SQL_CALC_FOUND_ROWS rc.*,hc.*, avg(rs.weight) as average
						from #__hotelreservation_review_answers ra 
						inner join #__hotelreservation_review_customers rc on ra.review_id = rc.review_id
						inner join #__hotelreservation_review_questions rq on ra.review_question_id = rq.review_question_id 
						inner join #__hotelreservation_review_rating_scale rs on rs.rating_scale_id = ra.rating_scale_id
						inner join #__hotelreservation_confirmations hc on rc.confirmation_id=hc.confirmation_id
						where rc.hotel_id = '.$hotelId.' and rc.published=1 and rs.weight<>0 group by rc.review_id ORDER BY rc.review_id desc';
		

		$this->_db->setQuery( $query, $limitStart, $limit );
		$result =  $this->_db->loadObjectList();
		
		if(!empty($result)){
			$this->_db->setQuery( "SELECT FOUND_ROWS() as totalReviewsCount");
			$totalRows =  $this->_db->loadResult();
			$result[0]->totalReviewsCount = $totalRows;
		}
		return $result;
	}
	
	function deleteByReview($reviewId){
		$query = ' delete 	from #__hotelreservation_review_answers 
							where review_id = '.$reviewId;
		//dmp($query);
		$this->_db->setQuery( $query );
		return $this->_db->query();
	}

}