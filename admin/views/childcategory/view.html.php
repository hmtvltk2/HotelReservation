<?php
/**
 * @package    JHotelReservation
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The HTML  View.
 */
if (!checkUserAccess(JFactory::getUser()->id,"manage_child_category")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}

class JHotelReservationViewChildCategory extends JHotelReservationAdminView
{
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
	
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();
		$this->translations = $hoteltranslationsModel->getAllTranslations(CHILDREN_CATEGORY_TRANSLATION, $this->item->id);

        $this->addScripts();
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
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);

		$user  = JFactory::getUser();
		$isNew = ($this->item->id == 0);

		JToolbarHelper::title(JText::_($isNew ? 'LNG_NEW_CHILDREN_CATEGORY' : 'LNG_EDIT_CHILDREN_CATEGORY',true), 'menu.png');
		
		JToolbarHelper::apply('childcategory.apply');
			
		JToolbarHelper::save('childcategory.save');
		
		JToolbarHelper::cancel('childcategory.cancel', 'JTOOLBAR_CLOSE');
		
		JToolbarHelper::divider();
        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#children-categories');
	}

     function addScripts(){
        $doc =JFactory::getDocument();
    }
}
