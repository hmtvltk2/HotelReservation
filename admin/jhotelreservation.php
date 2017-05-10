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

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/logger.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'defines.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'utils.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'userAccess.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'jhotelreservationtranslations.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

require_once JPATH_COMPONENT_ADMINISTRATOR.'/views/jhpview.php';


if (version_compare(JVERSION, '3.4.0', '<'))
{
    die('Your host needs to use Joomla 3.4.0 or higher to run this version of Hotel Reservation!');
}

JHtml::_('jquery.framework', true, true); //load jQuery before other js
JHTML::_('script', 		'components/'.getBookingExtName().'/assets/js/utils.js');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/styles/style.css');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/styles/general.css');
JHTML::_('stylesheet', 	'administrator/components/'.getBookingExtName().'/assets/styles/joomlatabs.css');
JHtml::_('stylesheet',  'administrator/components/'.getBookingExtName().'/assets/css/jhp-template.css');
JHtml::_('script',      'components/'.getBookingExtName().'/assets/js/metisMenu.js');
JHtml::_('stylesheet',  'administrator/components/'.getBookingExtName().'/assets/styles/responsivegrid.css');


$tag = JHotelUtil::getJoomlaLanguage();
JHTML::_('stylesheet', 	JURI::root().'components/'.getBookingExtName().'/assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.css');
JHTML::_('stylesheet', 	JURI::root().'components/'.getBookingExtName().'/assets/style/joomlacalendar.css');
JHTML::_('script', 	JURI::root().'components/'.getBookingExtName().'/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js');
JHTML::_('script', 	JURI::root().'components/'.getBookingExtName().'/assets/js/bootstrap-datepicker/locales/bootstrap-datepicker.'.$tag.'.min.js');
JHTML::_('script',		JURI::root().'components/'.getBookingExtName().'/assets/js/jquery.blockUI.js');
JHTML::_('script',		JURI::root().'components/'.getBookingExtName().'/assets/js/common.js');
JHTML::_('script', 	    JURI::root().'components/'.getBookingExtName().'/assets/js/joomlaInterfaces.js');



JHotelUtil::loadAdminLanguage();
JHotelUtil::loadClasses();
JHotelUtil::includeValidation();

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
		window.onload = function()	{
			jQuery.noConflict();
		};
		var baseUrl="'.(JURI::base().'index.php?option='.getBookingExtName()).'";
');

$controller	= JControllerLegacy::getInstance('JHotelReservation');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

