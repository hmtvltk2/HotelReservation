<?php
/**
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

if (!checkUserAccess(JFactory::getUser()->id,"currency_settings")){
    $msg = "You are not authorized to access this resource";
    $this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}

class JHotelreservationViewCurrency extends JHotelReservationAdminView
{
    protected $item;
    protected $state;
    protected $form;

    /**
     * Display the view
     */
    public function display($tpl = null){

        $item = $this->get('Item');
        $this->item =  $item;

        $this->state = $this->get('State');

        $form = $this->get('Form');
        $this->from = $form;

        $this->states = JHotelReservationHelper::getStatuses();


        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        $canDo = JHotelReservationHelper::getActions();

        $input = JFactory::getApplication()->input;
        $input->set('hidemainmenu', true);

        $isNew = ($this->item->currency_id > 0);

        JToolbarHelper::title(JText::_($isNew ? JText::_('LNG_EDIT',true): JText::_('LNG_ADD_NEW',true) ).' '.JText::_('LNG_CURRENCY',true), 'generic.png' );

        if ($canDo->get('core.edit')){
            JToolbarHelper::apply('currency.apply');
            JToolbarHelper::save('currency.save');
        }
        JToolbarHelper::cancel('currency.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
        JToolbarHelper::divider();
        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#currency-settings');

    }
}
