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

class JHotelReservationControllerLanguage extends JControllerLegacy
{

//    /**
//     * File Operation Trait
//     */
//    use FileOperations;

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();
    }

    function editLanguage(){
        JRequest::setVar( 'layout', 'language'  );
        return parent::display();
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function apply()
    {
        $code = JRequest::getString('code');
        $model = $this->getModel('language');
        $msg = $model->saveLanguage($code);
        $link = 'index.php?option='.getBookingExtName().'&tmpl=component&controller=language&view=language&task=language.editLanguage&code='.$code;
        $this->setRedirect($link, $msg);
    }

    function save()
    {
        $code = JRequest::getString('code');
        $model = $this->getModel('language');
        $msg = $model->saveLanguage($code);
        $link = 'index.php?option='.getBookingExtName().'&view=applicationsettings';
        $this->setRedirect($link, $msg);
    }

    function send_email() {
        $code = JRequest::getString('code');
        $model = $this->getModel('language');
        $msg = $model->send_email($code);

        $link = 'index.php?option='.getBookingExtName().'&tmpl=component&controller=language&view=language&task=language.editLanguage&code='.$code;
        $this->setRedirect($link, $msg);
    }

    function delete() {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app =JFactory::getApplication();
        $model = $this->getModel('language');

        // Get items to remove from the request.
        $codes = JRequest::getVar('cid', array(), '', 'array');

        if (!is_array($codes) || count($codes) < 1){
            $msg = $app->enqueueMessage(JText::_('COM_NO_ITEM_SELECTED'), 'error');
            $link = 'index.php?option=com_jhotelreservation&view=applicationsettings';
            $this->setRedirect($link, $msg);
        }
        else{
            foreach($codes as $code){
                $msg = $model->deleteFolder($code);
                $link = 'index.php?option=com_jhotelreservation&view=applicationsettings';
                $this->setRedirect($link, $msg);
            }
        }
    }

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
        $msg = JText::_('LNG_OPERATION_CANCELLED',true);
        $this->setRedirect( 'index.php?option='.getBookingExtName().'&view=applicationsettings', $msg );
    }
}