<?php 
/**
 * Main view responsable for creating the extension menu structure and admin template
 * 
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later; 
 */
 
JHtml::_('stylesheet', 'components/'.getBookingExtName().'/assets/style/font-awesome.min.css');
JHtml::_('stylesheet', 'components/'.getBookingExtName().'/assets/style/metisMenu.css');

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JHotelReservationAdminView extends JViewLegacy{
	
	var $section_name="";
	var $section_description = "";
	
	function __construct($config = array()){
		parent::__construct($config);
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$this->section_name= JText::_("LNG_".strtoupper($this->_name));
		$this->section_description = JText::_("LNG_".strtoupper($this->_name)."_HEADER_DESCR");
	}
	
	/**
	 * Generate the main display for extension views
	 * 
	 * @param unknown_type $tpl
	 */
	public function display($tpl = null)
	{
        $content = $this->loadTemplate($tpl);
	
		if ($content instanceof Exception)
		{
			return $content;
		}
		
		$input = JFactory::getApplication()->input;
		if($input->get('hidemainmenu')){
			echo $content;
			return;
		}
	
		$template = new stdClass();
		$template->content = $content;
		$template->menus = $this->generateMenu();
		$this->checkAccessRights($template->menus);
		$this->setActiveMenus($template->menus, $this->_name);
		
		//include the template and create the view
		$path = JPATH_ADMINISTRATOR . '/components/'.getBookingExtName().'/theme/template.php';
		$templateFileExists = JFile::exists($path);
		
		$templateContent = $content;
	
		if($templateFileExists){
			ob_start();
			
			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $path;
			
			// Done with the requested template; get the buffer and
			// clear it.
			$templateContent = ob_get_contents();
			ob_end_clean();
		}
		
		echo $templateContent;
	}
	
	/**
	 * Check for selected menu and set it active
	 * 
	 */
	private function setActiveMenus(&$menus, $view){
		foreach($menus as &$menu){
			if($menu["view"] == $view){
				$menu["active"] = true;
			}
			if(isset($menu["submenu"])){
				foreach($menu["submenu"] as &$submenu){
					if($submenu["view"] == $view){
						$submenu["active"] = true;
						$menu["active"] = true;
					}
				}
			}
		}
	}
	
	/**
	 * Check the access rights for the menu items
	 * @param unknown_type $menus
	 */
	private function checkAccessRights(&$menus){
		$actions = JHotelReservationHelper::getActions();
		
		foreach($menus as &$menu){
			if(!$actions->get($menu["access"])){
				unset($menu);
				continue;
			}
			if(isset($menu["submenu"])){
				foreach($menu["submenu"] as &$submenu){
					if(!$actions->get($submenu["access"])){
						unset($submenu);
						continue;
					}
				}
			}
		}
		
		return $menus;
	}
	
	/**
	 * Build the menu items with all subments
	 * 
	 */
	private function generateMenu(){
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$menus = array();
		
		$menuItem  = array(
				"title" => JText::_('LNG_DASHBOARD',true),
                "access" => "core.admin",
				"link" => "index.php?option=com_jhotelreservation&view=jhotelreservation",
				"view" => "jhotelreservation",
				"icon" => "fa-th-large");
		$menus[] = $menuItem;

        if (checkUserAccess(JFactory::getUser()->id,"application_settings")) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_SETTINGS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=applicationsettings",
                "view" => "applicationsettings",
                "icon" => "fa-cog");
            $menus[] = $menuItem;
        }
        if (checkUserAccess(JFactory::getUser()->id,"manage_hotels")) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_HOTELS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=hotels",
                "view" => "hotels",
                "icon" => "fa-building");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"manage_rooms")) {
            $menuItem = array(
                "title" => JText::_('LNG_ROOMS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=rooms",
                "view" => "rooms",
                "icon" => "fa-bed");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"manage_offers") && PROFESSIONAL_VERSION==1 && $appSettings->is_enable_offers) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_OFFERS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=offers",
                "view" => "offers",
                "icon" => "fa-gift");
            $menus[] = $menuItem;
        }
        
        if (checkUserAccess(JFactory::getUser()->id,"manage_offers") && $appSettings->enable_children_categories) {
        	$menuItem = array(
        			"title" => JText::_('LNG_SUBMENU_CHILDREN_CATEGORIES', true),
        			"access" => "core.admin",
        			"link" => "index.php?option=com_jhotelreservation&view=childcategories",
        			"view" => "childcategories",
        			"icon" => "fa fa-child");
        	$menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"manage_taxes") && PROFESSIONAL_VERSION==1) {
            $menuItem = array(
                "title" => JText::_('LNG_MANAGE_TAXES', true),
                "description" => JText::_('LNG_MANAGE_TAXES_DESC',true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=taxes",
                "view" => "taxes",
                "icon" => "fa-calculator");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"manage_room_discounts") && PROFESSIONAL_VERSION==1) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_ROOM_DISCOUNTS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=roomdiscounts",
                "view" => "roomdiscounts",
                "icon" => "fa-percent");

            $menus[] = $menuItem;
        }
        if (checkUserAccess(JFactory::getUser()->id,"POI") && PROFESSIONAL_VERSION==1) {
            $menuItem = array(
                "title" => JText::_('LNG_INTERESTPOINTS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=interestpoints",
                "view" => "interestpoints",
                "icon" => "fa-map-marker");
            $menus[] = $menuItem;
        }

     if (checkUserAccess(JFactory::getUser()->id,"availability_section")) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_AVAILABILITY', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=availability",
                "view" => "availability",
                "icon" => "fa-calendar-o");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"currencies")) {
            $menuItem = array(
                "title" => JText::_('LNG_CURRENCY_SETTINGS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=currencies",
                "view" => "currencies",
                "icon" => "fa-money");
            $menus[] = $menuItem;
        }


        if (checkUserAccess(JFactory::getUser()->id,"payment_processors") && STARTER_VERSION==0) {
            $menuItem = array(
                "title" => JText::_('LNG_PAYMENT_PROCESSORS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=paymentprocessors",
                "view" => "paymentprocessors",
                "icon" => "fa-credit-card");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"manage_extra_options") && PROFESSIONAL_VERSION==1 && $appSettings->is_enable_extra_options) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_EXTAS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=extraoptions",
                "view" => "extraoptions",
                "icon" => "fa-puzzle-piece");
            $menus[] = $menuItem;
        }
        
        if (checkUserAccess(JFactory::getUser()->id,"manage_excursions") && PROFESSIONAL_VERSION==1 && $appSettings->enable_excursions) {
            $menuItem = array(
                "title" => JText::_('LNG_COURSE_EXCURSION_PANEL', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=excursions",
                "view" => "excursions",
                "icon" => "fa-globe");
            $menus[] = $menuItem;
        }
        if (checkUserAccess(JFactory::getUser()->id,"manage_airport_transfers") && PROFESSIONAL_VERSION==1 && $appSettings->is_enable_screen_airport_transfer) {
            $menuItem = array(
                "title" => JText::_('LNG_AIRPORT_TRANSFER', true),
                "access" => "core.admin",
                "link" => "#",
                "view" => "airporttransfertypes",
                "icon" => "fa-plane");
            $submenu = array();
            $smenuItem = array(
                "title" => JText::_('LNG_AIRPORT_TRANSFER', true),
                "description" =>JText::_('LNG_AIRPORT_TRANSFER_DESC',true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=airporttransfertypes",
                "view" => "airporttransfertypes",
                "icon" => "fa-plane");
            $submenu[] = $smenuItem;
            $smenuItem = array(
                "title" => JText::_('LNG_AIRLINES', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=airlines",
                "view" => "airlines",
                "icon" => "fa-map-signs");
            $submenu[] = $smenuItem;
            $menuItem["submenu"] = $submenu;
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"manage_email_templates")) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_EMAILS_TEMPLATES', true),
                "description" => JText::_('LNG_MANAGE_EMAIL_TEMPLATES_DESC',true),
                "access" => "core.admin",
                "link" => "#",
                "view" => "emails",
                "icon" => "fa-envelope");
            $submenu = array();
            $smenuItem = array(
                "title" => JText::_('LNG_SUBMENU_EMAILS_TEMPLATES', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=emails",
                "view" => "emails",
                "icon" => "fa-envelope");
            $submenu[] = $smenuItem;
            $smenuItem = array(
                "title" => JText::_('LNG_DEFAULTEMAILS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=defaultemails",
                "view" => "defaultemails",
                "icon" => "fa-envelope-o");
            $submenu[] = $smenuItem;
            $menuItem["submenu"] = $submenu;
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"hotel_ratings") && PROFESSIONAL_VERSION==1) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_HOTEL_REVIEWS', true),
                "access" => "core.admin",
                "link" => "#",
                "view" => "reviews",
                "icon" => "fa-comments");
            $submenu = array();
            $smenuItem = array(
                "title" => JText::_('LNG_RATING', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=hotelratings",
                "view" => "hotelratings",
                "icon" => "fa-star");
            $submenu[] = $smenuItem;

            $smenuItem = array(
                "title" => JText::_('LNG_RATING_QUESTION', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=ratingquestions",
                "view" => "ratingquestions",
                "icon" => "fa-comments");
            $submenu[] = $smenuItem;
	        $smenuItem = array(
		        "title" => JText::_('LNG_RATING_CLASSIFICATIONS', true),
		        "access" => "core.admin",
		        "link" => "index.php?option=com_jhotelreservation&view=ratingclassifications",
		        "view" => "ratingclassifications",
		        "icon" => "fa-numbers");
	        $submenu[] = $smenuItem;

            $menuItem["submenu"] = $submenu;
            $menus[] = $menuItem;
        }


        if (checkUserAccess(JFactory::getUser()->id,"users_management") && PORTAL_VERSION==1) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_HOTEL_USER_ACCESS', true),
                "access" => "core.admin",
                "link" => "#",
                "view" => "usersmanagement",
                "icon" => "fa-users");
            $submenu = array();
            $smenuItem = array(
                "title" => JText::_('USERS_TAB', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=usersmanagement&task=usersmanagement.listing",
                "view" => "usersmanagement",
                "icon" => "fa-users");
            $submenu[] = $smenuItem;

            $smenuItem = array(
                "title" => JText::_('GROUPS_TAB', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=usersmanagement&task=usersmanagement.listgroups",
                "view" => "usersmanagement",
                "icon" => "fa-users");
            $submenu[] = $smenuItem;

            $smenuItem = array(
                "title" => JText::_('ROLES_TAB', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=usersmanagement&task=usersmanagement.listRoles",
                "view" => "usersmanagement",
                "icon" => "fa-user");
            $submenu[] = $smenuItem;

            $menuItem["submenu"] = $submenu;
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"add_reservations")) {
            $menuItem = array(
                "title" => JText::_('LNG_ADD_RESERVATION', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=reservation&sourceId=0&layout=edit",
                "view" => "reservation",
                "icon" => "fa-plus");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"manage_reservations")) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_MANAGE_RESERVATION', true),
                "description"=> JText::_('LNG_MANAGE_RESERVATIONS_DESC',true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=reservations",
                "view" => "reservations",
                "icon" => "fa-calendar");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"reservations_reports") && STARTER_VERSION!=1) {
            $menuItem = array(
                "title" => JText::_('LNG_SUBMENU_RESERVATION_REPORTS', true),
                "description"=> JText::_('LNG_RESERVATIONS_REPORTS_DESC',true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=reservationsreports",
                "view" => "reports",
                "icon" => "fa-bar-chart");
            $submenu = array();
            $smenuItem = array(
                "title" => JText::_('LNG_RESERVATIONSREPORTS', true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=reports",
                "view" => "reservationsreports",
                "icon" => "fa-users");
            $submenu[] = $smenuItem;

	        if (checkUserAccess(JFactory::getUser()->id,"income_report") && PROFESSIONAL_VERSION==1) {
		        $smenuItem = array(
			        "title"  => JText::_( 'LNG_RESERVATIONS_INCOME_REPORT', true ),
			        "access" => "core.admin",
			        "link"   => "index.php?option=com_jhotelreservation&view=reservationsreports&task=reservationsreports.incomeReport",
			        "view"   => "reservationsreports",
			        "icon"   => "fa-users"
		        );
		        $submenu[] = $smenuItem;
	        }
	        if (checkUserAccess(JFactory::getUser()->id,"country_report") && PROFESSIONAL_VERSION==1) {
		        $smenuItem = array(
			        "title"  => JText::_( 'LNG_RESERVATIONS_BY_COUNTRY_REPORT', true ),
			        "access" => "core.admin",
			        "link"   => "index.php?option=com_jhotelreservation&task=reservationsreports.countryReservationReport",
			        "view"   => "reservationsreports",
			        "icon"   => "fa-user"
		        );
		        $submenu[] = $smenuItem;
	        }

	        if (checkUserAccess(JFactory::getUser()->id,"offers_report") && PROFESSIONAL_VERSION==1) {
		        $smenuItem = array(
			        "title"  => JText::_( 'LNG_RESERVATIONS_OFFERS_REPORT', true ),
			        "access" => "core.admin",
			        "link"   => "index.php?option=com_jhotelreservation&task=reservationsreports.offersReport",
			        "view"   => "reservationsreports",
			        "icon"   => "dir-icon-user"
		        );
		        $submenu[] = $smenuItem;
	        }
	        if (checkUserAccess(JFactory::getUser()->id,"commission_report") && PROFESSIONAL_VERSION==1) {
		        $smenuItem = array(
			        "title"  => JText::_( 'LNG_RESERVATIONS_COMMISSION_REPORT', true ),
			        "access" => "core.admin",
			        "link"   => "index.php?option=com_jhotelreservation&task=reservationsreports.commissionReport",
			        "view"   => "reservationsreports",
			        "icon"   => "dir-icon-user"
		        );
		        $submenu[] = $smenuItem;
	        }

            $menuItem["submenu"] = $submenu;
            $menus[] = $menuItem;
        }
		
		if(checkUserAccess(JFactory::getUser()->id,"manage_invoices") && PORTAL_VERSION==1){
			$menuItem  = array(
					"title" => JText::_('LNG_SUBMENU_INVOICES',true),
                    "access" => "core.admin",
                    "link" => "index.php?option=com_jhotelreservation&view=invoices",
					"view" => "invoices",
					"icon" => "fa-fax");
			$menus[] = $menuItem;
		}

        if (checkUserAccess(JFactory::getUser()->id,"updates_hotelreservation") ) {

            $menuItem = array(
                "title" => JText::_('LNG_UPDATE', true),
                "description"=> JText::_('LNG_UPDATE_DESC',true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&view=updates",
                "view" => "updates",
                "icon" => "fa-download");
            $menus[] = $menuItem;
        }

        if (checkUserAccess(JFactory::getUser()->id,"about") ) {

            $menuItem = array(
                "title" => JText::_('LNG_ABOUT', true),
                "description" =>JText::_('LNG_ABOUT_DESC',true),
                "access" => "core.admin",
                "link" => "index.php?option=com_jhotelreservation&about&view=about",
                "view" => "about",
                "icon" => "fa-info-sign");
            $menus[] = $menuItem;
        }
	
		return $menus;
	}
	
	public function setSectionDetails($name, $description){
		$this->section_name = $name;
		$this->section_description = $description;
	}
}