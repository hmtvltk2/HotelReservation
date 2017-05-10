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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}
JHtml::_('behavior.framework');



require_once(JPATH_SITE.'/components/com_jhotelreservation/classes/services/UserDataService.php');
require_once JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/defines.php';
require_once JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/utils.php';
require_once JPATH_ADMINISTRATOR.'/components/com_jhotelreservation/helpers/jhotelreservationtranslations.php';


// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );


jimport( 'joomla.session.session' );

JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/responsiveRooms.css');
JHTML::_('stylesheet', 	'modules/mod_jhotelviewedproperties/assets/css/style.css');

JHotelUtil::loadSiteLanguage();
$language 		= JFactory::getLanguage();
$language_tag 	= JRequest::getVar( '_lang' );

$user = JFactory::getUser();


$recentProperties = modJHotelViewedProperties::getItems($params);
//dmp($pois);

$userData =  UserDataService::getUserData();

//if (empty( $recentProperties )) {
//    return;
//}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require( JModuleHelper::getLayoutPath( 'mod_jhotelviewedproperties',  $params->get('layout', 'default')));


?>
