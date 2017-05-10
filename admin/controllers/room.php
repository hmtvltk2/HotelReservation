<?php


defined('_JEXEC') or die;

jimport( 'joomla.application.component.controllerform' );

/**
 * The Room Controller
 *
 */
class JHotelReservationControllerRoom extends JControllerForm
{
	
	
	public function add()
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= 'com_jhotelreservation.edit.room';
	
		$result = parent::add();
		if ($result) {
			$hotelId = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');
			//dmp("H: ".$hotelId);
			$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=room'.$this->getRedirectToItemAppend(), false));
		}
	
		return $result;
	}
	
	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   1.6
	 */
	public function cancel($key = null)
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

		// Initialise variables.
		$app = JFactory::getApplication();
		$context = 'com_jhotelreservation.edit.room';
		$result = parent::cancel();

		if ($result)
		{
			// Clear the ancillary data from the session.
			$app->setUserState($context . '.type', null);
			$app->setUserState($context . '.link', null);
		}
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
	 * @since   1.6
	 */
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$result = parent::edit();
		
		return true;
	}

	
	public function editRatePrices($key = null, $urlVar = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$result = parent::edit();
		$recordId = JRequest::getInt('room_id');
		$rateId = JRequest::getInt('rate_id');
		// Save the data in the session.
		$app->setUserState('com_jhotelreservation.edit.room.roomid', $recordId);
		
		// Redirect back to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=roomrateprices&layout=edit&rate_id=' .$rateId, false));
		
		return true;
	}
	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('Room', '', array());
		$data = JRequest::get('post');
		$task = $this->getTask();
		$context = 'com_jhotelreservation.edit.room';
		$recordId = JRequest::getInt('room_id');

		// Populate the row id from the session.
		$data['room_id'] = $recordId;
        $data['max_children'] = $data['base_children'];

		$option_ids = array();
		$pictures = array();
		foreach( $data as $key => $value )
		{
			if(
					strpos( $key, 'room_picture_info' ) !== false
					||
					strpos( $key, 'room_picture_path' ) !== false
					||
					strpos( $key, 'room_picture_enable' ) !== false
			)
			{
				foreach( $value as $k => $v )
				{
					if( !isset($pictures[$k]) )
						$pictures[$k] = array('room_picture_info'=>'', 'room_picture_path'=>'','room_picture_enable'=>1);
		
					$pictures[$k][$key] = $v;
				}
			}
		}
		$data['option_ids'] = $option_ids;
		$data['pictures'] = $pictures;
	
		
		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{

			$data['room_id'] = 0;
			$task = 'apply';
		}
		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.room.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data))
		{
			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.room.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

			return false;
		}
		$recordId = $model->getState($this->context . '.room_id');
		$data["room_id"]= $recordId;
		// Attempt to save the data.
		if (!$model->saveRate($data))
		{
			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.room.data', $data);
		
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
		
			return false;
		}
		if (!$model->savePictures($data))
		{
			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.room.data', $data);
		
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
			return false;
		}
		
		$model->saveRoomTranslations($data);
		$model->saveChildrenCategoryPrices($data);
		
		$this->setMessage(JText::_('LNG_SAVE_SUCCESS',true));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the row data in the session.
				$recordId = $model->getState($this->context . '.room_id');
				//dmp($recordId);
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jhotelreservation.edit.room.data', null);
			
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				break;

			case 'save2new':
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);  
				$app->setUserState('com_jhotelreservation.edit.room.data', null);
			
				$app->setUserState('com_jhotelreservation.edit.room.hotel_id', $model->getState('room.hotel_id'));
                $hotelId = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item.$this->getRedirectToItemAppend(), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jhotelreservation.edit.room.data', null);
			

				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
		}
	}
}