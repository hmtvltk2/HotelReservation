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
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/logger.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/defines.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'utils.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'userAccess.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'jhotelreservationtranslations.php';

$appSettings =JHotelUtil::getInstance()->getApplicationSettings();
JRequest::setVar('show_price_per_person', $appSettings->show_price_per_person);

JHTML::_('script', 'components/'.getBookingExtName().'/assets/js/utils.js');
JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/js/validation/css/template.css' );
JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/js/validation/css/customMessages.css' );
JHTML::_('stylesheet', 	'components/com_jhotelreservation/assets/style/responsive.css');
JHtml::_('stylesheet', 'components/com_jhotelreservation/assets/style/font-awesome.min.css');

JHtml::_('jquery.framework', true, true);
JHtml::_('behavior.framework');
define('J_JQUERY_LOADED', 1);

JHTML::_('script',	'components/'.getBookingExtName().'/assets/js/jquery-ui.min.js');
JHTML::_('script', 	'components/'.getBookingExtName().'/assets/js/jquery.blockUI.js');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/style/tabs.css');

$tag = JHotelUtil::getJoomlaLanguage();
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/general.css');
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/form.css');

JHTML::_('script', 	    'components/'.getBookingExtName().'/assets/js/rangedatetime/moment.min.js');
JHTML::_('script', 'components/'.getBookingExtName().'/assets/js/combodate.js');

JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/style/joomlacalendar.css');


JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/style/jquery-ui.min.css');
JHTML::_('stylesheet', 	'https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css');

JHotelUtil::includeValidation();

$doc =JFactory::getDocument();

$doc->addScriptDeclaration('
		window.onload = function()	{
		jQuery.noConflict();
		};
		var baseUrl="'.(JRoute::_('index.php?option=com_jhotelreservation')).'";
');

if( isset($_SESSION['cssStyleComp'] ) ){
	JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/style/extension/'.$_SESSION['cssStyleComp']);
}else if( isset($appSettings->css_style) ){
	JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/style/extension/'.$appSettings->css_style);
}else{
	JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/style/extension/style.css');
}

JHotelUtil::setExtensionMenuId($appSettings);

$task = JRequest::getCmd('task');
$task = trim($task);
$view = JRequest::getCmd('view');
$view = trim($view);
if(empty($task) && empty($view)){
	return;
}

JHotelUtil::loadSiteLanguage();
JHotelUtil::loadClasses();
if($task!="hotel.getRoomCalendars" && $task!="hotel.checkReservationPendingPayments")
	UserDataService::initializeUserData();

if( strpos($_SERVER['REQUEST_URI'],"buckarooautomaticresponse") ){
	$task = "paymentoptions.processAutomaticResponse";
	JRequest::setVar( 'task', $task);
	JRequest::setVar( 'processor', "buckaroo");
}
//$log->LogDebug($task);
$controller	= JControllerLegacy::getInstance('JHotelReservation');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

