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
class JHotelReservationViewDefaultEmail extends JHotelReservationAdminView
{

    protected $item;
    protected $state;
    protected $form;

    /**
     * @param null $tpl
     */
    public function display($tpl = null)
    {
        $item				= $this->get('Item');
        $this->item =  $item;

        $this->state = $this->get('State');


        $hoteltranslationsModel = new JHotelReservationLanguageTranslations();
        $this->translations = $hoteltranslationsModel->getAllTranslations(EMAIL_TEMPLATE_TRANSLATION, $this->item->email_default_id);

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

        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', 1 );
        $isNew = ($this->item->email_default_id > 0);
        JToolBarHelper::title(   'J-Hotel Reservation : '.( $isNew > 0 ? JText::_('LNG_EDIT',true): JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_EMAIL',true), 'generic.png' );

        if ($canDo->get('core.edit')) {
            JToolBarHelper::apply('defaultemail.apply');
            JToolBarHelper::save('defaultemail.save');
        }

        JToolBarHelper::cancel('defaultemail.cancel',$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE' );
    }
}