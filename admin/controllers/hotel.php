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

//jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'hotel'.DS.'view.html.php' );
//jimport('joomla.application.component.controllerform');

class JHotelReservationControllerHotel extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	function __construct()
	{
		parent::__construct();	
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function apply(){
		$msg = $this->saveHotel();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=hotel.edit&cid[]='.JRequest::getVar('hotel_id'), $msg );
	}
	function save(){
		$msg = $this->saveHotel();
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=hotels&view=hotels', $msg );
	}
	function saveHotel()
	{
		$model = $this->getModel('hotel');
		$post = JRequest::get( 'post' );

        $post['hotel_description'] 			= JRequest::getVar('hotel_description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['hotel_selling_points'] 		= JRequest::getVar('hotel_selling_points', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['remarks'] 		            = JRequest::getVar('remarks', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if(strlen($post['hotel_website'])>1){
			$post['hotel_website']= str_replace("http://", "", $post['hotel_website'] );
			$post['hotel_website'] = "http://".$post['hotel_website'];
		}

		//save images
		$pictures					= array();
		foreach( $post as $key => $value )
		{
		 if(
		 strpos( $key, 'hotel_picture_info' ) !== false
		 ||
		 strpos( $key, 'hotel_picture_path' ) !== false
		 ||
		 strpos( $key, 'hotel_picture_enable' ) !== false
			){
				foreach( $value as $k => $v )
				{
					if( !isset($pictures[$k]) )
					$pictures[$k] = array('hotel_picture_info'=>'', 'hotel_picture_path'=>'','hotel_picture_enable'=>1);
					$pictures[$k][$key] = $v;
				}
			}
		}
		//dmp($pictures);
		//exit;
		$post['pictures'] 				= $pictures;
		//dmp($post);
		//exit;
		$reservation_cost_val	= $post['reservation_cost_val'];
		$reservation_cost_proc	= $post['reservation_cost_proc'];
		$post['hotel_name']= (string)($post['hotel_name']);


        //$post['hotel_name'] = str_replace('"', "'", $post['hotel_name']);

        if ($model->store($post))
		{
			$post["hotel_id"] = $model->_hotel_id;
			JRequest::setVar('hotel_id',$model->_hotel_id);
			$post["informationId"] = $model->_informationId;
            $model->saveHotelDescriptions($post);
            $model->saveMultiLangImportantInfo($post);
			$msg = JText::_('LNG_HOTEL_SAVED',true);
		}
		else
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_HOTEL',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=hotels&view=hotels&task=edit&hotel_id='.$post['hotel_id'], '' );
		}

		return $msg;
	}


	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_('LNG_OPERATION_CANCELLED',true);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=hotels&view=hotels', $msg );
	}

	
	function edit()
	{
		$model = $this->getModel('hotel');
		$view  = $this->getView('hotel');
		$view->setModel( $model, true );  // true is for the default model;
		
		$lodgingtypes = $this->getModel ( 'lodgingtypes' );
		$facilities = $this->getModel ( 'facilities' );
		$accomodationtypes = $this->getModel ( 'accomodationtypes' );
		$environmenttypes = $this->getModel ( 'environmenttypes' );
		$paymentoptions = $this->getModel ( 'paymentoptions' );
		$regiontypes = $this->getModel ( 'regiontypes' );
		
		$view->setModel($lodgingtypes);
		$view->setModel($facilities);
		$view->setModel($accomodationtypes);
		$view->setModel($environmenttypes);
		$view->setModel($paymentoptions);
		$view->setModel($regiontypes);

		$view->display();
	}

	function updateFacilities()
	{
		$model = $this->getModel('facilities');
		$model->updateFacilities();
	}


	function updateTypes()
	{
		$model = $this->getModel('lodgingtypes');
		$model->updateTypes();
	}

	function updateAccommodationTypes()
	{
		$model = $this->getModel('accomodationtypes');
		$model->updateAccommodationTypes();
	}

	function updateEnvironments()
	{
		$model = $this->getModel('environmenttypes');
		$model->updateEnvironments();
	}

	function updateRegions()
	{
		$model = $this->getModel('regiontypes');
		$model->updateRegions();
	}


	function updatePaymentOptions()
	{
		$model = $this->getModel('paymentoptions');
		$model->updatePaymentOptions();
	}
}