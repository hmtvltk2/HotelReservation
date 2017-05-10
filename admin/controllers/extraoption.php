<?php


defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

/**
 * The Company Controller
 *
 */
class JHotelReservationControllerExtraOption extends JControllerForm
{
	/**
	 * Dummy method to redirect back to standard controller
	 *
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=extraoptions', false));
	}

	public function add()
	{
		$app = JFactory::getApplication();
		$context = 'com_jhotelreservation.edit.extraoption';

		$result = parent::add();
        $hotel_id = $app->getUserStateFromRequest('filter.hotel_id', 'hotel_id', '1', 'cmd');
        $sourceId = JRequest::getInt('dId');

        $urlParam = $sourceId>0?'&dId='.$sourceId:'';

        if ($result)
		{
            $this->setRedirect(JRoute::_('index.php?option=com_jhotelreservation&view=extraoption'.$this->getRedirectToItemAppend().$urlParam, false));
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
		$context = 'com_jhotelreservation.edit.extraoption';
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
        $context = 'com_jhotelreservation.edit.extraoption';
        $result = parent::edit();
	
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
		$model = $this->getModel('extraoption');
		$post = JRequest::get( 'post' );
		$post['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['name'] = JRequest::getVar('name', '', 'post', 'string',JREQUEST_ALLOWRAW);
		
		$context  = 'com_jhotelreservation.edit.extraoption';
		$task     = $this->getTask();

        $recordId = JRequest::getInt('id');
        // Populate the row id from the session.
        $post['id'] = $recordId;

        $post['room_ids']	= implode(',', $post['room_ids']);
        $post['offer_ids']	= implode(',', $post['offer_ids']);
        $post['commission'] = $post['commission'] !=''?$post['commission']:-1;



        if (!$model->save($post)){
			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.extraoption.data', $post);
			
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
			
			return false;
		}
        $recordId = $model->getState($this->context . '.id');
        $post['id'] = $recordId;
        $model->saveDescriptions($post);

		$this->setMessage(JText::_('LNG_EXTRA_OPTION_SAVE_SUCCESS',true));

        // Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the row data in the session.
				$recordId = $model->getState($this->context.'.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_jhotelreservation.edit.extraoption.data', null);
			
				// Redirect back to the edit screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item .$this->getRedirectToItemAppend($recordId), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_jhotelreservation.edit.extraoption.data', null);
							
				// Redirect to the list screen.
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
		}
	}
	
	function updateOffers()
	{
	
		$roomIds = $_POST['roomIDs'];
		$offerIds = $_POST['offerIDs'];
	
		$model = $this->getModel('roomdiscount');
		$buff =  $model->getHTMLContentOffers($roomIds,$offerIds);
			
		echo '<?xml version="1.0" encoding="utf-8" ?>';
		echo '<room_statement>';
		echo '<answer error="0"  content_records="'.$buff.'" />';
		echo '</room_statement>';
		echo '</xml>';
		exit;
	}
}
