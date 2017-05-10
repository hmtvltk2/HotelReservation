<?php
/**
 * @copyright	Copyright (C) 2008-2016 CMSJunkie. All rights reserved.
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

class JTableReviewComments extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(& $db) {
        parent::__construct('#__hotelreservation_hotel_review_comments', 'id', $db);

    }

    function setKey($k){
        $this->_tbl_key = $k;
    }

    function getHotelCommentsReviews($reviewId){
        $reviewId = isset($reviewId)?$reviewId:0;
        $query = ' select ra.id,ra.comment,u.username,u.name,ra.userId
						from #__hotelreservation_hotel_review_comments ra
						inner join #__hotelreservation_review_customers rc on rc.review_id=ra.reviewId
						INNER JOIN #__users u on u.id = ra.userId
						inner join #__hotelreservation_confirmations hc on rc.confirmation_id = hc.confirmation_id where ra.reviewId = '.$reviewId.' group by ra.id ORDER BY ra.id asc';
        $this->_db->setQuery( $query );
        $result =  $this->_db->loadObjectList();
        return $result;
    }


    function getReviewComment($id,$reviewId,$userId){
        $user = JFactory::getUser();
        $where = " and userId=".$userId;
        if($user->get("isRoot")) {
            if ($userId == $user->id) {
                $where = " ";
            }
        }
        $query = ' select ra.id,ra.comment,u.username,u.name,ra.userId,ra.reviewId
						from #__hotelreservation_hotel_review_comments ra
						INNER JOIN #__users u on u.id = ra.userId
				where ra.id = '.$id.' and ra.reviewId = '.$reviewId.$where.' group by ra.id';
        $this->_db->setQuery( $query );
        $result =  $this->_db->loadObject();
        return $result;
    }

    function updateReviewComment($id, $reviewId, $comment, $userId){
        $user = JFactory::getUser();
        $where = " and userId=".$userId;
        if($user->get("isRoot")) {
            if ($userId == $user->id) {
                $where = " ";
            }
        }
        $db =JFactory::getDBO();
        $query = "UPDATE #__hotelreservation_hotel_review_comments SET comment='".(string)$comment."' WHERE id=".$id." and reviewId=".$reviewId.$where;
        $db->setQuery($query);
        if (!$db->query() )
        {
            echo 'INSERT / UPDATE sql STATEMENT error !';
            return false;
        }
        return $this->getReviewComment($id,$reviewId,$userId);
    }

    function deleteCommentByReview($commentId,$reviewId){
        $query = "delete from #__hotelreservation_hotel_review_comments where id =".$commentId." and reviewId = ".$reviewId;
        //dmp($query);
        $this->_db->setQuery( $query );
        return $this->_db->query();
    }
}