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

class JHotelReservationControllerReservationsReports extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}

	function incomeReport(){
		if (checkUserAccess(JFactory::getUser()->id,"income_report") && PROFESSIONAL_VERSION==1) {
			JRequest::setVar( 'layout', 'incomeReport' );
			JRequest::setVar( 'view', 'reservationsreports' );
			return $this->display();
		}else{
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect( 'index.php?option='.getBookingExtName() );
		}
	}

	function countryReservationReport(){
		if (checkUserAccess(JFactory::getUser()->id,"country_report") && PROFESSIONAL_VERSION==1) {
			JRequest::setVar( 'layout', 'countriesreport' );
			JRequest::setVar( 'view', 'reservationsreports' );
			return $this->display();
		}else{
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect( 'index.php?option='.getBookingExtName() );
		}
	}

	function offersReport(){
		if (checkUserAccess(JFactory::getUser()->id,"offers_report") && PROFESSIONAL_VERSION==1) {
			JRequest::setVar( 'layout', 'offersReport' );
			JRequest::setVar( 'view', 'reservationsreports' );
			return $this->display();
		}else{
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect( 'index.php?option='.getBookingExtName() );
		}
	}


	function commissionReport(){
		if (checkUserAccess(JFactory::getUser()->id,"commission_report") && PROFESSIONAL_VERSION==1) {
			JRequest::setVar( 'layout', 'commissionReport' );
			JRequest::setVar( 'view', 'reservationsreports' );
			return $this->display();
		}else{
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			$this->setRedirect( 'index.php?option='.getBookingExtName() );
		}
	}

	function getJsonOffersData(){
		$model = $this->getModel('reservationsreports');
		$content = $model->getJsonOffersData();
		print_r($content);
		exit;
	}

	function getJsonIncomeData(){
		$model = $this->getModel('reservationsreports');
		$content = $model->getJsonIncomeData();
		print_r($content);
		exit;	
	}
	function getJsonCountriesData(){
		$model = $this->getModel('reservationsreports');
		$content = $model->getJsonCountriesData();
		print_r($content);
		exit;
	}
	function getJsonReservationData(){
		$model = $this->getModel('reservationsreports');
		$content = $model->getJsonReservationData();
		print_r($content);
		exit;
	}

	/**
	 * Controller method used to export offer report data to csv ,
	 * checks the user session token , accesses the method from the model to do the export
	 */
	public function exportToCsv(){
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('reservationsreports');
		$model->exportToCsv();
		exit;
	}

	/**
	 * Controller method used to export revenue report data to csv ,
	 * checks the user session token , accesses the method from the model to do the export
	 */
	public function exportCommissionIncomeToCsv(){
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('reservationsreports');
		$model->exportCommissionIncomeToCsv();
		exit;
	}
	
	function back(){
		$this->setRedirect('index.php?option='.getBookingExtName().'&view=reservationsreports');
	}

    function backHome(){
        $this->setRedirect('index.php?option='.getBookingExtName());
    }
}