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

class TableReviewCustomers extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(& $db) {

		parent::__construct('#__hotelreservation_review_customers', 'review_id', $db);
	}

	function setKey($k){
		$this->_tbl_key = $k;
	}

	function getHotelReviews($hotelId){

		$query = 'select concat(d.first_name," ",d.last_name) as clientsName,c.review_short_description,c.review_remarks,c.published,c.review_id
					from  #__hotelreservation_review_customers c,
					      #__hotelreservation_confirmations d
					where c.confirmation_id = d.confirmation_id
					      and d.hotel_id='.$hotelId;
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();

	}
	function setPublished($reviewId){
		$query = 	" UPDATE #__hotelreservation_review_customers SET published = IF(published, 0, 1) WHERE review_id = ".$reviewId;
		$this->_db->setQuery( $query );
		if (!$this->_db->query())
		{
			//JError::raiseWarning( 500, "Error change Room state !" );
			return false;

		}
		return true;
	}
	function calculateHotelRatingScore($hotelId){
		$query = ' 	update #__hotelreservation_hotels set hotel_rating_score=(
							select sum(b.weight)/count(*)
							from  #__hotelreservation_review_answers a, 
							      #__hotelreservation_review_rating_scale b,
							      #__hotelreservation_review_customers c,
							      #__hotelreservation_confirmations d
							where c.review_id = a.review_id 
							      and a.rating_scale_id= b.rating_scale_id
							      and b.rating_scale_id !=11 
							      and c.confirmation_id = d.confirmation_id
							      and c.published=1
							      and d.hotel_id='.$hotelId.'
							)
							where hotel_id ='.$hotelId;

		$this->_db->setQuery( $query );
		$this->_db->query();
	}
	function getReviewByCofirmation($confirmationId){
		$query = 'select *	from  #__hotelreservation_review_customers c
							where c.confirmation_id='.$confirmationId;
		$this->_db->setQuery( $query );
		return $this->_db->loadObject();
	}

    public function getReviewHotel($id){
        $query = ' SELECT
							h.hotel_name,h.hotel_city,h.hotel_id,
							c1.country_name as hotel_country
						FROM #__hotelreservation_review_customers rc
						INNER JOIN #__hotelreservation_hotels 	h USING (hotel_id)
						INNER JOIN #__hotelreservation_countries c1 on c1.country_id = h.country_id

						WHERE rc.review_id = '.$id.' AND h.is_available = 1
						ORDER BY h.hotel_name';
        $this->_db->setQuery( $query );
        $hotel = $this->_db->loadObject();
        return $hotel;
    }

}