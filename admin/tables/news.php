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

class JTableNews extends JTable
{

    /**
     * @var
     */
    private $db;

    /**
     * @var
     */
    private $pk;

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db)
    {

        parent::__construct('#__hotelreservation_news', 'id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    /**
     * Retrieve the last news from database
     *
     */
    public function getLocalLastNews() {
        $db = JFactory::getDBO();
        $query = "select * from `#__hotelreservation_news` order by retrieve_date desc limit 1";
        $db->setQuery($query);
        $lastNews = $db->loadObject();
        return $lastNews;
    }

    /**
     * Get the latest news from local database and prepare them
     *
     * @param unknown_type $limit
     */
    public function getLocalNews($limit=3)
    {
        // $limit -> the limit of the news to be displayed in the dashboard
        $db = JFactory::getDBO();
        $query = "select * from `#__hotelreservation_news` order by publish_date desc limit $limit";
        $db->setQuery($query);
        $news = $db->loadObjectList();
        return $news;
    }


}