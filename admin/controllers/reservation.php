<?php


defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

/**
 * The Company Controller
 *
 */
class JHotelReservationControllerReservation extends JControllerForm
{
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=reservation', false));
	}

	public function add()
	{
		$app = JFactory::getApplication();
		$context = 'com_jhotelreservation.edit.reservation';
	
		$result = parent::add();
		if ($result)
		{
			$sourceId = JRequest::getInt('sourceId');
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=reservation&sourceId='.$sourceId. $this->getRedirectToItemAppend(), false));
		}
	
		return $result;
	}
	
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.

	 */
	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));
	
		$app = JFactory::getApplication();
		$context = 'com_jhotelreservation.edit.reservation';
		$result = parent::cancel();
	
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 */
	public function edit($key = null, $urlVar = null)
	{
		$app = JFactory::getApplication();
		$result = parent::edit(null,"reservationId");
	
		return true;
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save($key = NULL, $urlVar = NULL)
	{
		
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

		$app      = JFactory::getApplication();
		$model = $this->getModel('reservation');
		$post = JRequest::get( 'post' );
		$post["current"] = (int)$post["current"];
		$data = JRequest::get( 'post' );
		$context  = 'com_jhotelreservation.edit.reservation';
		$task     = $this->getTask();
		$recordId = JRequest::getInt('reservationId');
		//dmp($task);
		//exit;
		
		if (!$model->save($post)){
			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.reservation.data', $data);
			
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			JRequest::setVar("view",$this->view_item);
			parent::display();
		}
		else
			$this->setMessage(JText::_('LNG_RESERVATION_SAVE_SUCCESS',true));
		
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the row data in the session.
				$recordId = $model->getState("reservation.id");
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jhotelreservation.edit.reservation.data', null);
			
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId,"reservationId"), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jhotelreservation.edit.reservation.data', null);
							
				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
		}
	}
	
	function addHotelRoom(){
		$roomId = JRequest::getInt("roomId");
		$current = (int)JRequest::getInt("current");
		$hotelId = JRequest::getInt("hotelId");
		$discountCode = JRequest::getVar("discountCode");
		$startDate = JHotelUtil::convertToMysqlFormat(JRequest::getVar("startDate"));
		$endDate = JHotelUtil::convertToMysqlFormat(JRequest::getVar("endDate"));
		
		$adults=JRequest::getInt("adults");
		$children=JRequest::getInt("children");
		
		$room = HotelService::getHotelRooms($hotelId, $startDate, $endDate, array($roomId), $adults, $children, $discountCode);
		$room = $room[0];
		$room->current = $current;
		
		$this->appSettings = JHotelUtil::getInstance()->getApplicationSettings();

		$model = $this->getModel('reservation');
		$buff = RoomService::getRoomHtmlContent($room, $startDate, $endDate,$this->appSettings->show_children);
		$buff = htmlspecialchars($buff);
		
		//header("Content-type:text/xml");
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="0" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		JFactory::getApplication()->close();
	}
	
	function addHotelOffer(){
		$offerId = JRequest::getInt("offerId");
		$current = (int)JRequest::getInt("current");
		$hotelId = JRequest::getInt("hotelId");
		$startDate = JRequest::getVar("startDate");
		$endDate = JRequest::getVar("endDate");
		
		$room = HotelService::getHotelOffers($hotelId, $startDate, $endDate, array($offerId));
		$room = $room[0];
		$room->current = $current;
		
		$model = $this->getModel('reservation');
		$buff = $model->getRoomHtmlContent($room, $startDate, $endDate);
		
		//header("Content-type:text/xml");
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="0" content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		JFactory::getApplication()->close();
	}
	function secretizeCard(){
		$model = $this->getModel('reservation');
		$recordId = JRequest::getInt('reservationId');
		$model->secretizeCard($recordId);
		$this->setRedirect('index.php?option=com_jhotelreservation&view=reservation&layout=edit&reservationId='.$recordId );
	}
	
}
