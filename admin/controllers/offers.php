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
JHTML::_('script', 'components/'.getBookingExtName().'/assets/js/jquery.selectlist.js');
JHTML::_('script', 'components/'.getBookingExtName().'/assets/js/jquery.blockUI.js');
JHTML::_('script', 'components/'.getBookingExtName().'/assets/js/offers.js');
jimport( 'joomla.application.component.controlleradmin' );

class JHotelReservationControllerOffers extends JControllerAdmin
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */

	function __construct($config = array())
	{
		parent::__construct($config);
	//	$this->registerTask( 'state', 'state');
	//	$this->registerTask( 'add', 'edit');

		$this->registerTask('saveOrderAjax','saveorder');

		if( JRequest::getVar('is_error')=="1" && JRequest::getVar('task')=="save")
		{
			JRequest::setVar( 'view', 'offers' ); 
			//$this->display();
		}
		if(JRequest::getVar('task')!="back")
			JRequest::setVar( 'view', 'offers' );
	}

	function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN', true));

        // Get items to remove from the request.
        $cid = JRequest::getVar('cid', array(), '', 'array');

        jimport('joomla.utilities.arrayhelper');
        JArrayHelper::toInteger($cid);
        $app		= JFactory::getApplication();
        $hotelId = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');

        if (!is_array($cid) || count($cid) < 1) {
            JError::raiseWarning(500, JText::_('LNG_NO_ROOM_SELECTED', true));
        } else {
            $model = $this->getModel('offers');

            if ($model->remove($hotelId,$cid)) {
                $msg = JText::_('LNG_OFFER_HAS_BEEN_DELETED', true);
            } else {
                $this->setMessage($model->getError());
            }
        }
            // Check the table in so it can be edited.... we are done with it anyway
            $this->setRedirect('index.php?option=' . getBookingExtName() . '&controller=offers&view=offers', $msg);

    }

    /**
     * Display the view
     *
     * @param	boolean			If true, the view output will be cached
     * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.

     */
    public function display($cachable = false, $urlparams = false)
    {}

    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     */
    public function getModel($name = 'Offer', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

	function state()
	{
        $app		= JFactory::getApplication();
		$model = $this->getModel('offers');
		if ($model->state()) {
			$msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_( 'LNG_ERROR_CHANGE_OFFER_STATE' ,true);
		}

	
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers', $msg );
	}
	
	
	function changeFeaturedState(){
		$model = $this->getModel('offers');
        $app		= JFactory::getApplication();
		if ($model->changeFeaturedState()) {
			$msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_OFFER_STATE',true);
		}
		
		$this->setMessage($msg);
		
		//JRequest::setVar( 'view', 'offers' );
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers', $msg );
	}
	
	function changeTopState(){
		$model = $this->getModel('offers');
        $app		= JFactory::getApplication();
		if ($model->changeTopState()) {
			$msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
		} else {
			$msg = JText::_('LNG_ERROR_CHANGE_OFFER_STATE',true);
		}
	
		$this->setMessage($msg);
	
		//JRequest::setVar( 'view', 'offers' );
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&controller=offers&view=offers', $msg );
	}
	
	function offer_order()
	{
		$model = $this->getModel('offers');
	    return $model->changeOfferOrder();
	}
	
	function getOfferContent(){
		$model = $this->getModel('offers');
		$content= $model->getOfferContent();
		echo $content; 
		exit; 
	}
	
	function getWarningContent(){
		$model = $this->getModel('offers');
		$content= $model->getWarningContent();
		echo $content;
		exit;
	}

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        $pks = JRequest::getVar('cid', array(), 'array');
        $order = JRequest::getVar('order', array(), 'array');


        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel("Offers");

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    function publishList()
    {
        $model = $this->getModel();
        $cid	= JRequest::getVar('cid', array(), '', 'array');
        $app		= JFactory::getApplication();
        if ($model->publishList($cid))
        {
            $msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
        } else {
            $msg = JText::_('LNG_ERROR_CHANGE_OFFER_STATE',true);
        }

        $this->setMessage($msg);
        $this->setRedirect('index.php?option=com_jhotelreservation&view=offers');
    }

    function unPublishList()
    {
        $model = $this->getModel();
        $cid	= JRequest::getVar('cid', array(), '', 'array');
        $app		= JFactory::getApplication();
        if ($model->unPublishList($cid))
        {
            $msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
        } else {
            $msg = JText::_('LNG_ERROR_CHANGE_OFFER_STATE',true);
        }

        $this->setMessage($msg);
        $this->setRedirect('index.php?option=com_jhotelreservation&view=offers');
    }
	
}