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

class JTableReviewQuestion extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function __construct(&$db) {

		parent::__construct('#__hotelreservation_review_questions', 'review_question_id', $db);
	}


	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	/**
	 * This Method is used by its model to change the order direction of a question based on its question Id
	 * the method is used to build an SQL query to update the new direction param bases on the questionId param
	 * @param $questionId
	 * @param $direction
	 * @return mixed
	 */
	function changequestionorder($questionId, $direction)
	{
		$db= JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__hotelreservation_review_questions'));
		if(is_numeric($direction) && is_numeric($questionId)) {
			$query->set('review_question_nr=' .(int)$direction);
			$query->where('review_question_id=' .(int)$questionId);
		}
		$db->setQuery((string)$query);
		$db->query();
		return $db->getErrorMsg();
	}
}