<?php
/**
 * @package JHotelReservation
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');
require_once(JPATH_SITE.'/components/com_jhotelreservation/classes/services/UserDataService.php');
require_once(JPATH_SITE.'/components/com_jhotelreservation/classes/services/CurrencyService.php');


$mod_name = 'mod_jhotel_currency';

JHtml::_('stylesheet', 'components/com_jhotelreservation/assets/style/font-awesome.min.css');
JHTML::_('stylesheet', JURI::base().'/modules/'.$mod_name.'/css/style.css');
$input = JFactory::getApplication()->input;
//do not proccess outside request
if($input->get('option') != 'com_jhotelreservation' || $input->get('task') == 'hotel.getRoomCalendars' || $input->get('task') == 'viewConfirmation')
	return;
	
$userData =  UserDataService::getUserData();
$currencies = CurrencyService::getAllCurrencies();
$hotelCurrency = $input->get('hotelCurrency');

require JModuleHelper::getLayoutPath($mod_name, $params->get('layout','default'));