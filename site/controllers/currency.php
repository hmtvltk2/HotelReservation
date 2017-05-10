<?php
/**
 * @copyright	Copyright (C) 2008-2016 CMSJunkie. All rights reserved.
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

defined('_JEXEC') or die('Restricted access');

class JHotelReservationControllerCurrency extends JControllerLegacy {
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	function __construct()
	{
		parent::__construct();
	}

	//override controller methods

	function setCurrency()
	{
		$currencySelector = JRequest::getVar("currencySelector");
		$currencyArray = explode("_",$currencySelector);
		$currencyName = $currencyArray[0];
		$currencySymbol = $currencyArray[1];
		UserDataService::setCurrency($currencyName,$currencySymbol);
	}

}