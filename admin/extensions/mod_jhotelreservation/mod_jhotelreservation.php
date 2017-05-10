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


JHtml::_('jquery.framework', true, true); //load jQuery before other js
JHtml::_('behavior.framework');
JHTML::_('script','components/com_jhotelreservation/assets/js/jquery-ui.min.js');

jimport( 'joomla.session.session' );


JHTML::_('script', 'components/com_jhotelreservation/assets/js/utils.js');
JHTML::_('script', 'components/com_jhotelreservation/assets/js/search.js');

$tag = JHotelUtil::getJoomlaLanguage();
JHTML::_('stylesheet', 	'components/com_jhotelreservation/assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.css');
JHTML::_('script', 	'components/com_jhotelreservation/assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js');
JHTML::_('script', 	'components/com_jhotelreservation/assets/js/bootstrap-datepicker/locales/bootstrap-datepicker.'.$tag.'.min.js');
JHtml::_('stylesheet', 'components/com_jhotelreservation/assets/style/font-awesome.min.css');

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
		window.onload = function()	{
			jQuery.noConflict();
		};  
	');
JHTML::_('script', 'components/com_jhotelreservation/assets/js/jquery.blockUI.js');
JHTML::_('stylesheet', 	'https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css');

$title 			= modJHotelReservationHelper::getTitle( $params );
$cssStyle		= $params->get('cssstyle', '');
$hotels = modJHotelReservationHelper::getHotelItems();

$language 		= JFactory::getLanguage();

JHTML::_('stylesheet',  'modules/mod_jhotelreservation/assets/css/slick.css');
JHTML::_('stylesheet', 	'modules/mod_jhotelreservation/assets/jhotelreservationGeneral.css');
JHTML::_('stylesheet', 	'components/com_jhotelreservation/assets/style/form.css');
JHTML::_('stylesheet', 	'components/com_jhotelreservation/assets/style/joomlacalendar.css');
JHTML::_('stylesheet', 	'components/com_jhotelreservation/assets/style/responsive.css');
JHTML::_('script',  'modules/mod_jhotelreservation/assets/js/slick.js');


if(isset($_SESSION['cssStyle'])){
    JHTML::_('stylesheet', 'modules/mod_jhotelreservation/assets/css/'.$_SESSION['cssStyle']);
}else{
    JHTML::_('stylesheet', 	'modules/mod_jhotelreservation/assets/css/'.$cssStyle);
}

$language = JFactory::getLanguage();
$language_tag 	= $language->getTag();

$x = $language->load('com_jhotelreservation' ,dirname(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jhotelreservation'. DS.'language'),
    $language_tag,true);


$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

$post			= JRequest::get( 'post' );
$userData =  UserDataService::getUserData();

$startDate = $params->get('start-date');
$endDate = $params->get('end-date');

//current dates today and today + 1 used only when checkbox for no dates is checked
$date = date('Y-m-d');
$next_date = date('Y-m-d', strtotime($date .' +1 day'));

//create dates & default values
$jhotelreservation_datas = JRequest::getVar('jhotelreservation_datas');
if( strlen($jhotelreservation_datas)==0 )
{
    if(
        JRequest::getVar('year_start') != ''
        &&
        JRequest::getVar('month_start') != ''
        &&
        JRequest::getVar('day_start') != ''
    )
    {
        $jhotelreservation_datas = JRequest::getVar('year_start').'-';
        $jhotelreservation_datas .= strlen(JRequest::getVar('month_start'))>1	? JRequest::getVar('month_start') 	: ("0".JRequest::getVar('month_start'));
        $jhotelreservation_datas .= '-';
        $jhotelreservation_datas .= strlen(JRequest::getVar('day_start'))>1		? JRequest::getVar('day_start') 	: ("0".JRequest::getVar('day_start'));
    }else if(isset($startDate)){
        $jhotelreservation_datas = $params->get('start-date');
        if(strtotime($jhotelreservation_datas) < strtotime(date("Y-m-d"))){
            $jhotelreservation_datas = date("Y-m-d");
        }
    }
    else if(isset($userData->start_date)){
        $jhotelreservation_datas = $userData->start_date;
    }else{
        $jhotelreservation_datas = date('Y-m-d');
    }
}
$jhotelreservation_datas = JHotelUtil::convertToFormat($jhotelreservation_datas);
$jhotelreservation_datae = JRequest::getVar('jhotelreservation_datae');
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
if( !isset($jhotelreservation_datae) || strlen($jhotelreservation_datae)==0)
{
    if(
        JRequest::getVar('year_end') != ''
        &&
        JRequest::getVar('month_end') != ''
        &&
        JRequest::getVar('day_end') != ''
    )
    {
        $jhotelreservation_datae = JRequest::getVar('year_end').'-';
        $jhotelreservation_datae .= strlen(JRequest::getVar('month_end'))>1	? JRequest::getVar('month_end') 	: ("0".JRequest::getVar('month_end'));
        $jhotelreservation_datae .= '-';
        $jhotelreservation_datae .= strlen(JRequest::getVar('day_end'))>1	? JRequest::getVar('day_end') 	: ("0".JRequest::getVar('day_end'));
    }else if(isset($endDate) && strlen($endDate)>0){

        $jhotelreservation_datae = $params->get('end-date');
        if(strtotime($jhotelreservation_datae) < strtotime(date("Y-m-d"))){
            $jhotelreservation_datae = date("Y-m-d");
        }
    }
    else if(isset($userData->end_date)){
        $jhotelreservation_datae = $userData->end_date;
    }else{
        $jhotelreservation_datae = date('Y-m-d', strtotime( ' + 1 day '));
    }
}
$jhotelreservation_datae = JHotelUtil::convertToFormat($jhotelreservation_datae);
$jhotelreservation_rooms 		= JRequest::getVar('jhotelreservation_rooms');
$jhotelreservation_guest_adult 	= JRequest::getVar('jhotelreservation_guest_adult');
$jhotelreservation_guest_child	= JRequest::getVar('jhotelreservation_guest_child');
$jhotelreservation_hotel_id		= JRequest::getVar('jhotelreservation_hotel_id');
$jinput = JFactory::getApplication()->input;
$activeComponent = $jinput->get('option');
$activeView= $jinput->get('view');


if( strlen($jhotelreservation_rooms)==0 )
{
    if( JRequest::getVar('rooms') != '' )
        $jhotelreservation_rooms		= JRequest::getVar('rooms');
    else
        $jhotelreservation_rooms		= 1;
}

if( strlen($jhotelreservation_guest_adult)==0 )
{
    if( JRequest::getVar('guest_adult') != '' )
        $jhotelreservation_guest_adult	= JRequest::getVar('guest_adult');
    else
        $jhotelreservation_guest_adult	= 2;
}

if( strlen($jhotelreservation_guest_child)==0 )
{
    if( JRequest::getVar('guest_child') != '' )
        $jhotelreservation_guest_child		= JRequest::getVar('guest_child');
    else
        $jhotelreservation_guest_child = 0;
}

if(isset($userData->total_adults))
    $jhotelreservation_guest_adult = $userData->total_adults;
if(isset($userData->total_children))
    $jhotelreservation_guest_child = $userData->total_children;

if(isset($userData->rooms))
    $jhotelreservation_rooms = $userData->rooms;


$layoutType = 	$params->get('layout-type', 'vertical');
if(JRequest::getVar('layout-type')!=null)
    $layoutType = JRequest::getVar('layout-type');

if($layoutType=="vertical")
    JHTML::_('stylesheet', 	'modules/mod_jhotelreservation/assets/vertical.css');

require( JModuleHelper::getLayoutPath( 'mod_jhotelreservation',  'default-'.$layoutType));
?>
