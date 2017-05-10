<?php
/**
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * BusinessDirectory component helper.
 *
 * @package    JHotelReservation
 * @subpackage  com_jhotelreservation

 */
class JHotelReservationHelper
{
	/**
	 * Defines the valid request variables for the reverse lookup.
	 */
	protected static $_filter = array('option', 'view', 'layout');

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		
		if (checkUserAccess(JFactory::getUser()->id,"application_settings")){
			JSubMenuHelper::addEntry(
				JText::_('LNG_SUBMENU_SETTINGS',true),
				'index.php?option=com_jhotelreservation&view=applicationsettings',
				$vName == 'applicationsettings'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"manage_hotels")){
			JSubMenuHelper::addEntry(
				JText::_('LNG_SUBMENU_HOTELS',true),
				'index.php?option=com_jhotelreservation&view=hotels',
				$vName == 'hotels'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"manage_rooms")){
			JSubMenuHelper::addEntry(
					JText::_('LNG_SUBMENU_ROOMS',true),
					'index.php?option=com_jhotelreservation&view=rooms',
					$vName == 'rooms'
			);
		}
		if (checkUserAccess(JFactory::getUser()->id,"manage_offers") && PROFESSIONAL_VERSION==1){
			JSubMenuHelper::addEntry(
					JText::_('LNG_SUBMENU_OFFERS',true),
					'index.php?option=com_jhotelreservation&view=offers',
					$vName == 'offers'
			);
		}
		if (checkUserAccess(JFactory::getUser()->id,"manage_room_discounts") && PROFESSIONAL_VERSION==1){
			JSubMenuHelper::addEntry(
					JText::_('LNG_SUBMENU_ROOM_DISCOUNTS',true),
					'index.php?option=com_jhotelreservation&view=roomdiscounts',
					$vName == 'roomdiscounts'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"availability_section")){
			JSubMenuHelper::addEntry(
					JText::_('LNG_SUBMENU_AVAILABILITY',true),
					'index.php?option=com_jhotelreservation&view=availability',
					$vName == 'availability'
			);
		}
		if (checkUserAccess(JFactory::getUser()->id,"payment_processors") && STARTER_VERSION==0){
			JSubMenuHelper::addEntry(
				JText::_('LNG_PAYMENT_PROCESSORS',true),
				'index.php?option=com_jhotelreservation&view=paymentprocessors',
				$vName == 'paymentprocessors'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"manage_extra_options") && PROFESSIONAL_VERSION==1){
			JSubMenuHelper::addEntry(
					JText::_('LNG_SUBMENU_EXTAS',true),
					'index.php?option=com_jhotelreservation&view=extraoptions',
					$vName == 'extraoptions'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"manage_airport_transfers") && PROFESSIONAL_VERSION==1){
			JSubMenuHelper::addEntry(
					JText::_('LNG_AIRPORT_TRANSFER',true),
					'index.php?option=com_jhotelreservation&view=airporttransfertypes',
					$vName == 'airporttransfertypes'
			);
		}


		
		if (checkUserAccess(JFactory::getUser()->id,"manage_email_templates")){
			JSubMenuHelper::addEntry(
					JText::_('LNG_SUBMENU_EMAILS_TEMPLATES',true),
								'index.php?option=com_jhotelreservation&view=emails',
					$vName == 'emailtemplates'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"hotel_ratings") && PROFESSIONAL_VERSION==1){
			JSubMenuHelper::addEntry(
				JText::_('LNG_SUBMENU_HOTEL_REVIEWS',true),
				'index.php?option=com_jhotelreservation&task=hotelratings.menuhotelratings',
				$vName == 'reviews'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"manage_hotel_users") && PORTAL_VERSION==1){
			JSubMenuHelper::addEntry(
				JText::_('LNG_SUBMENU_HOTEL_USER_ACCESS',true),
								'index.php?option=com_jhotelreservation&view=usersmanagement',
				$vName == 'useraccess'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"manage_reservations")){
			JSubMenuHelper::addEntry(
				JText::_('LNG_SUBMENU_MANAGE_RESERVATION',true),
				'index.php?option=com_jhotelreservation&view=reservations',
				$vName == 'reservations'
			);
		}
		if (checkUserAccess(JFactory::getUser()->id,"reservations_reports") && STARTER_VERSION!=1){
			JSubMenuHelper::addEntry(
				JText::_('LNG_SUBMENU_RESERVATION_REPORTS',true),
							'index.php?option=com_jhotelreservation&view=reservationsreports',
				$vName == 'reports'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"manage_invoices") && PORTAL_VERSION==1){
			JSubMenuHelper::addEntry(
					JText::_('LNG_SUBMENU_INVOICES',true),
					'index.php?option=com_jhotelreservation&view=invoices',
					$vName == 'invoices'
			);
		}
		
		if (checkUserAccess(JFactory::getUser()->id,"updates_hotelreservation") ){
			JSubMenuHelper::addEntry(
				JText::_('LNG_UPDATE',true),
				'index.php?option=com_jhotelreservation&view=updates',
				$vName == 'updates'
			);
		}
	}
	
	/**
	 * Configure the Linkbar.
	 */
	public static function addUserAccessSubmenu($submenu)
	{
		JSubMenuHelper::addEntry(JText::_('USERS_TAB'), 'index.php?option='.getBookingExtName().'&task=usersmanagement.listing', $submenu == 'users');
		JSubMenuHelper::addEntry(JText::_('GROUPS_TAB'), 'index.php?option='.getBookingExtName().'&task=usersmanagement.listgroups', $submenu == 'groups');
		JSubMenuHelper::addEntry(JText::_('ROLES_TAB'), 'index.php?option='.getBookingExtName().'&task=usersmanagement.listRoles', $submenu == 'roles');
		// set some global property
	
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
	}
	
	public static function getReservationStatuses(){
		$statuses = array(RESERVED_ID=>JText::_('LNG_RESERVED',true),
				CANCELED_ID=>JText::_('LNG_CANCELED',true),
				CHECKEDIN_ID=>JText::_('LNG_CHECKED_IN',true),
				CHECKEDOUT_ID=>JText::_('LNG_CHECKED_OUT',true),
				LATE_ID=>JText::_('LNG_LATE',true));
		return $statuses;
	}

	public static function getPaymentStatuses(){
		$statuses = array(JHP_PAYMENT_STATUS_PENDING=>JText::_('LNG_PENDING',true),
				JHP_PAYMENT_STATUS_WAITING=>JText::_('LNG_WAITING',true),
				JHP_PAYMENT_STATUS_PAID=>JText::_('LNG_PAID',true),
				JHP_PAYMENT_STATUS_CANCELED=>JText::_('LNG_CANCELED',true),
				JHP_PAYMENT_STATUS_FAILURE=>JText::_('LNG_FAILURE',true)
			);
		
		return $statuses;
	}
	
	
	public static function getOrderStates(){
			$states = array();
			$state = new stdClass();
			$state->value = 0;
			$state->text = JText::_('LNG_NOT_PAID',true);
			$states[] = $state;
			$state = new stdClass();
			$state->value = 1;
			$state->text = JText::_('LNG_PAID',true);
			$states[] = $state;
			
			return $states;
	}
	
	public static function getStatuses(){
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JText::_('LNG_INACTIVE',true);
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JText::_('LNG_ACTIVE',true);
		$states[] = $state;
	
		return $states;
	}
	
	public static function getModes(){
		$modes = array();
		$state = new stdClass();
		$state->value = "test";
		$state->text = JText::_('LNG_TEST',true);
		$modes[] = $state;
		$state = new stdClass();
		$state->value = "live";
		$state->text = JText::_('LNG_LIVE',true);
		$modes[] = $state;
		
		return $modes;
	}
	public static function getActions()
	{
		// Reverted a change for version 2.5.6
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		$assetName = 'com_jhotelreservation';
	
		$actions = array(
					'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);
	
		foreach ($actions as $action)
		{
			$result->set($action,	$user->authorise($action, $assetName));
		}
	
		return $result;
	}
	
	public static function addAccessSubmenu($submenu)
	{
		JSubMenuHelper::addEntry(JText::_('USERS_TAB',true), 'index.php?option=com_jhotelreservation&controller=usersmanagement&view=usersmanagement&task=', $submenu == 'users');
		JSubMenuHelper::addEntry(JText::_('GROUPS_TAB',true), 'index.php?option=com_jhotelreservation&controller=usersmanagement&view=usersmanagement&task=listgroups', $submenu == 'groups');
		JSubMenuHelper::addEntry(JText::_('ROLES_TAB',true), 'index.php?option=com_jhotelreservation&controller=usersmanagement&view=usersmanagement&task=listRoles', $submenu == 'roles');
		// set some global property
	
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/tux-48x48.png);}');
	}
	
	
	public static function getGuestTypes(){
		$guest_types = array();
		$guest_types[] = JHTML::_('select.option',1,JText::_('LNG_GUEST_TYPE_1',true));
		$guest_types[] = JHTML::_('select.option',2,JText::_('LNG_GUEST_TYPE_2',true));
		$guest_types[] = JHTML::_('select.option',3,JText::_('LNG_GUEST_TYPE_3',true));
		
		return $guest_types;
	}


    public static function getExtraOptionTypes(){
        $extraOption_types = array();
        $extraOption_types[] = JHTML::_('select.option',1,JText::_('LNG_PER_DAY',true));
        $extraOption_types[] = JHTML::_('select.option',2,JText::_('LNG_PER_STAY',true));
        $extraOption_types[] = JHTML::_('select.option',3,JText::_('LNG_PER_NIGHT',true));

        return $extraOption_types;
    }
    
    public static function getAccessGroups(){
    	$userGroups = array();
    	$userGroup = new stdClass();
    	$userGroup->value = JHP_PROCCESSOR_GUEST;
    	$userGroup->text = JText::_('LNG_GUEST',true);
    	$userGroups[] = $userGroup;
    	$userGroup = new stdClass();
    	$userGroup->value = JHP_PROCCESSOR_SUPER_USER;
    	$userGroup->text = JText::_('LNG_SUPER_USER',true);
    	$userGroups[] = $userGroup;
    
    	return $userGroups;
    }
    

}


