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

class JHotelReservationControllerJHotelReservation extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
	}
	function back()
	{
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName(), $msg );
	}

    public function getLatestServerNewsAjax() {
        $model = $this->getModel('JHotelReservation');
        $model->getLatestServerNews();
        exit;
    }

}
?>

		