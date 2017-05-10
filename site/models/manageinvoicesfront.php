<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'invoice.php');

class JHotelReservationModelManageInvoicesFront extends JHotelReservationModelInvoice{
	
	function __construct()
	{
		parent::__construct();
	}
}

