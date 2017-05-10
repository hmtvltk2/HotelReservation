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

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modellist');

class JHotelReservationModelRatingQuestions extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'review_question_id', 'hrq.review_question_id',
                'review_question_desc', 'hrq.review_question_desc',
                'review_question_nr','hrq.review_question_nr',
                'ordering', 'hrq.ordering',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  string  An SQL query
     *
     * @since   1.6
     */
    public function getListQuery()
    {
        // Create a new query object.
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //Get All fields from the table
        $query->select($this->getState('list.select', 'hrq.*'));
        $query->from($db->quoteName('#__hotelreservation_review_questions') . 'AS hrq');

        $query->order('hrq.ordering');

        return $query;
    }


    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        $published = $this->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $type = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', 0, 'int');
        $this->setState('filter.type', $type);

        // Check if the ordering field is in the white list, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
        $this->setState('list.ordering', $value);

        // Check if the ordering direction is valid, otherwise use the incoming value.
        $value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
        $this->setState('list.direction', $value);

        // List state information.
        parent::populateState('hrq.ordering', 'asc');
    }

    /**
     * Method to adjust the ordering of a row.
     *
     * @param   integer  The ID of the primary key to move.
     * @param   integer	Increment, usually +1 or -1
     * @return  boolean  False on failure or error, true otherwise.
     */
    public function reorder($pk, $direction = 0)
    {
        // Sanitize the id and adjustment.
        $pk	= (!empty($pk)) ? $pk : (int) $this->getState('ratingquestion.review_question_id');
        $user = JFactory::getUser();

        // Get an instance of the record's table.
        $table = JTable::getInstance('ReviewQuestion','JTable');

        // Load the row.
        if (!$table->load($pk))
        {
            $this->setError($table->getError());
            return false;
        }

        // Access checks.
        $allow = true; //$user->authorise('core.edit.state', 'com_users');

        if (!$allow)
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED',true));
            return false;
        }

        // Move the row.
        // TODO: Where clause to restrict category.
        $table->move($pk);

        return true;
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array  An array of primary key ids.
     * @param   integer  +/-1
     */
    public function saveorder($pks, $order)
    {
        $table		= JTable::getInstance('ReviewQuestion','JTable');
        $user 		= JFactory::getUser();
        $conditions	= array();

        if (empty($pks))
        {
            return JError::raiseWarning(500, JText::_('COM_USERS_ERROR_LEVELS_NOLEVELS_SELECTED',true));
        }

        // update ordering values
        foreach ($pks as $i => $pk)
        {
            $table->load((int) $pk);

            // Access checks.
            $allow = true; //$user->authorise('core.edit.state', 'com_users');

            if (!$allow)
            {
                // Prune items that you can't change.
                unset($pks[$i]);
                JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED',true));
            }
            elseif ($table->ordering != $order[$i])
            {
                $table->ordering = $order[$i];
                if (!$table->store())
                {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond)
        {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        return true;
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  string  An SQL query
     *
     * @since   1.6
     */
    public function getReviewQuestions()
    {
        // Get all review questions
        $query = 	' SELECT * FROM #__hotelreservation_review_questions order by review_question_nr';
        $this->_db->setQuery( $query );
        return  $this->_db->loadObjectList();
    }

}