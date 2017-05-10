<?php
/*------------------------------------------------------------------------
# JHotelReservation
# author CMSJunkie
# copyright Copyright (C) 2013 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/hotel_reservation/?p=1
# Technical Support:  Forum Multiple - http://www.cmsjunkie.com/forum/joomla-multiple-hotel-reservation/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableGuestDetailsAttributes extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db){

        parent::__construct('#__hotelreservation_guest_details_attributes', 'id', $db);
    }

    function setKey($k)
    {
        $this->_tbl_key = $k;
    }

    public function getAttributesConfiguration(){
        $query = 'SELECT id,name,config_type FROM #__hotelreservation_guest_details_attributes';
        $this->_db->setQuery( $query );
        return $this->_db->loadObjectList();
    }
}