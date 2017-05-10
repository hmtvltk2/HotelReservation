<?php
/**
 * @package JHotelReservation
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

$mod_name = 'mod_jhotelreservation_adminlinks';

//$document 	= JFactory::getDocument();
$input 		= JFactory::getApplication()->input;

JHtml::_('stylesheet', 'components/com_jhotelreservation/assets/style/font-awesome.min.css');
JHtml::_('stylesheet', 'administrator/modules/'.$mod_name.'/css/style.css');
require_once JPATH_ADMINISTRATOR.'/components/com_jhotelreservation/helpers/utils.php';
require_once JPATH_ADMINISTRATOR.'/components/com_jhotelreservation/helpers/defines_versions.php';

require_once JPATH_ADMINISTRATOR.'/components/com_jhotelreservation/helpers/userAccess.php';


require JModuleHelper::getLayoutPath($mod_name, $params->get('layout','default'));