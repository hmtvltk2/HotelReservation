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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JTableInvoiceDetails extends JTable
{

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function JTableInvoiceDetails(&$db) {

		parent::__construct('#__hotelreservation_invoice_details', 'id', $db);
	}

	function setKey($k)
	{
		$this->_tbl_key = $k;
	}

	function getInvoiceDetail($id){

		$db= JFactory::getDBO();
		$query = $db->getQuery(true);
		//Get All fields from the table
		$query->select('hi.*');
		$query->from($db->quoteName('#__hotelreservation_invoice_details'). ' AS hi');
		if(is_numeric($id)) {
			$query->where('hi.id=' . (int)$id);
		}
		$db->setQuery((string)$query);
		return $db->loadObject();
	}
	
	function getInvoiceDetails($invoiceId){
		$db =JFactory::getDBO();
		$query = $db->getQuery(true);
		//Get All fields from the table
		$query->select('hi.*');
		$query->from($db->quoteName('#__hotelreservation_invoice_details'). ' AS hi');
		if(is_numeric($invoiceId)) {
			$query->where('hi.invoiceId=' . (int)$invoiceId);
		}
		$query->order('hi.arrival');
		// 		dmp($query);
		$db->setQuery((string)$query);

	//	$db->setQuery($query);
		return $db->loadObjectList();
	}

	function updateInvoiceDetailStatus($detailId, $status){
		$db =JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__hotelreservation_invoice_details'));
		if(is_numeric($status) && is_numeric($detailId)) {
			$query->set('status='.(int)$status);
			$query->where('id=' .(int)$detailId);
		}

		$db->setQuery((string)$query);
		//$db->setQuery($query);
		return $db->query();
	}
}