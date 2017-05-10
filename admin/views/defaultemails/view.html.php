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



if (!checkUserAccess(JFactory::getUser()->id,"manage_email_templates")){
    $msg = "You are not authorized to access this resource";
    $this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
JHTML::_('stylesheet', 	JURI::root().'components/com_jhotelreservation/assets/style/responsiveMaterialTable.css');
class JHotelReservationViewDefaultEmails extends JHotelReservationAdminView
{

    protected $items;
    protected $state;
    protected $form;
    protected $pagination;

    /**
     * @param null $tpl
     */
    public function display($tpl = null)
    {
        $items		= $this->get('Items');

        $this->pagination	= $this->get('Pagination');
        $this->state = $this->get('State');

        $this->items =  $items;
        $this->hoteltranslationsModel = new JHotelReservationLanguageTranslations();

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);

    }

    protected function addToolbar()
    {
        $canDo = JHotelReservationHelper::getActions();

        JToolBarHelper::title(  'J-Hotel Reservation : '. JText::_('LNG_MANAGE_EMAILS_DEFAULT',true), 'generic.png' );
        JToolBarHelper::custom( 'emails.backToEmails', JHotelUtil::getDashBoardIcon(), 'home', 'Back', false, false );
        if ($canDo->get('core.create')) {
            JToolBarHelper::editList('defaultemail.edit');
        }
    }
}