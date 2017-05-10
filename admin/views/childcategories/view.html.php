<?php
/**
 * @package    JHotelReservation
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if (!checkUserAccess(JFactory::getUser()->id,"manage_extra_options")){
	$msg = "You are not authorized to access this resource";
	$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
}
/**
 * The HTML Menus Menu Menus View.
 *
 * @package    JHotelReservation
 * @subpackage  com_jbusinessdirectory

 */

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JHotelReservationViewChildCategories extends JHotelReservationAdminView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$layout = JRequest::getVar("layout", null);
		if(isset($layout)){
			$tpl = $layout;
		}
		
		//JHotelReservationHelper::addSubmenu('childcategories');
		
		$hotels		= $this->get('Hotels');
		$this->hotels = checkHotels(JFactory::getUser()->id,$hotels);
		
		$this->statuses = JHotelReservationHelper::getStatuses();
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$this->hoteltranslationsModel = new JHotelReservationLanguageTranslations();

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
		JToolBarHelper::title('J-HotelReservation : '.JText::_('LNG_CHILDREN_CATEGORIES',true), 'generic.png' );

		$hotelId = $this->state->get('filter.hotel_id');
		if(!empty($hotelId))
		{
			JToolbarHelper::addNew( 'childcategory.add' );
			JToolbarHelper::editList( 'childcategory.edit' );
		}
		JToolbarHelper::divider();
		JToolbarHelper::deleteList('','childcategories.delete');
				
		JToolbarHelper::divider();
		JToolBarHelper::custom( 'childcategories.back', JHotelUtil::getDashBoardIcon(), 'preview.png', JText::_('LNG_HOTEL_DASHBOARD',true), false, false );
        JToolBarHelper::help('', false, DOCUMENTATION_URL.'hotelreservationadministration.html#children-categories');
	}
}
