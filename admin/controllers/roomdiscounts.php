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

jimport('joomla.application.component.controlleradmin');

class JHotelReservationControllerRoomDiscounts extends JControllerAdmin
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the view
     *
     * @param   boolean			If true, the view output will be cached
     * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController		This object to support chaining.
     * @since   1.6
     */
    public function display($cachable = false, $urlparams = false){
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.6
     */
    public function getModel($name='RoomDiscount',$prefix='JHotelReservationModel' , $config = array('ignore_request'=>true))
    {
        $model = parent::getModel($name,$prefix,$config);
        return $model;
    }


    /**
     * Method to delete one or more records
     * @return bool
     */
    function delete() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $post = JRequest::get( 'post' );
        if( !isset($post['hotel_id']) )
            $post['hotel_id'] = 0;

        $app = JFactory::getApplication();
        $result = parent::delete();

        if($result)
        {
            $msg = JError::raiseWarning(500,JText::_('LNG_ERROR_DELETE_DISCOUNT',true));

            //return true;
        } else {
            $msg = JText::_('LNG_DISCOUNT_HAS_BEEN_DELETED',true);
            // return false;
        }

        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=roomdiscounts&hotel_id='.$post['hotel_id'], $msg );

        return false;
    }

    function updateOffers()
    {

        $roomIds = $_POST['roomIDs'];
        $offerIds = $_POST['offerIDs'];

        $model = $this->getModel('RoomDiscounts');
        $buff =  $model->getHTMLContentOffers($roomIds,$offerIds);

        echo '<?xml version="1.0" encoding="utf-8" ?>';
        echo '<room_statement>';
        echo '<answer error="0"  content_records="'.$buff.'" />';
        echo '</room_statement>';
        echo '</xml>';
        exit;
    }

    function state()
    {
        $model = $this->getModel();
        $cid	= JRequest::getVar('cid', array(), '', 'array');
        $cid = $cid[0];
        if ($model->state($cid))
        {
            $msg = JText::_( '' ,true);
        } else {
            $msg = JText::_('LNG_ERROR_CHANGE_ROOM_STATE',true);
        }

        $this->setMessage($msg);
        $this->setRedirect('index.php?option=com_jhotelreservation&view=roomdiscounts',$msg);
    }


}