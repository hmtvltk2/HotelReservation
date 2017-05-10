<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 CMSJunkie,  All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport( 'joomla.application.component.controlleradmin' );


/**
 * The Contest List Controller
 *
 */
class JHotelReservationControllerExcursionRatePrices extends JControllerAdmin
{
	
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
		$this->registerTask( 'apply', 'save');
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
	{
	}

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
	public function getModel($name = 'ExcursionRatePrices', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	
	
	public function cancel($key = null)
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));
	
		// Initialise variables.
		$app		= JFactory::getApplication('administrator');
		//$context	= 'com_menus.edit.item';
		
		// Clear the row id and data in the session.
		//$this->releaseEditId($context, $recordId);
		$app->setUserState('com_jhotelreservation.edit.excursionrateprices.data', null);
		$excursionId = $app->getUserState('com_jhotelreservation.edit.excursion.id');
		
		// Redirect to the list screen.
		$onlyAvailability = JRequest::getVar("onlyAvailability", false);
		if($onlyAvailability)
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option .'&view=availability', false));
		else 
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option .'&view=excursion&layout=edit&id=' . $excursionId[0], false));
		
	}
	
	/**
	 * Method to save multiple records.
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
		$model = $this->getModel('ExcursionRatePrices', '', array());
		$data = JRequest::get('post');
		$task = $this->getTask();
		$context = 'com_jhotelreservation.edit.excursionrateprices.';
		$recordId = JRequest::getInt('rate_id');
	
		// Populate the row id from the session.
		$data['rate_id'] = $recordId;
		
		$onlyAvailability = JRequest::getVar("onlyAvailability", false);

		//exit;
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
			$app->setUserState('com_jhotelreservation.edit.excursionrateprices.data', $data);
	
					// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=excursionrateprices&rate_id=' . $recordId, false));
	
			return false;
		}
	
		// Attempt to save the data.
		if (!$model->save($data))
		{
			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.excursionrateprices.data', $data);
		
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=excursionrateprices&layout=edit&rate_id=' . $recordId, false));
	
			return false;
		}
	
		
		$this->setMessage(JText::_('LNG_SAVE_SUCCESS',true));
		//exit;
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
			// Set the row data in the session.
			//$recordId = $model->getState($this->context . '.id');
			//$this->holdEditId($context, $recordId);
			//$app->setUserState('com_jhotelreservation.edit.excursionrateprices.data', null);
			// Redirect back to the edit screen.
			if($onlyAvailability)
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=excursionrateprices&layout=edit&onlyAvailability=trues&rate_id=' . $recordId, false));
			else
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=excursionrateprices&layout=edit&rate_id=' . $recordId, false));
			break;
		
			default:
			// Clear the row id and data in the session.
			//$this->releaseEditId($context, $recordId);
			$app->setUserState('com_jhotelreservation.edit.excursionrateprices.data', null);
			$excursionId= $app->getUserState('com_jhotelreservation.edit.excursion.id');

		
			// Redirect to the list screen.
				
			if($onlyAvailability)
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option .'&view=availability', false));
			else
				$this->setRedirect(JRoute::_('index.php?option=' . $this->option .'&view=excursion&layout=edit&id=' . $excursionId[0], false));
			break;
		}
	}

	function quickSetup(){
		$data = JRequest::get('post');
	
		$model = $this->getModel('ExcursionRatePrices', '', array());
		if($model->quickSetup($data)){
			$this->setMessage(JText::_('LNG_RATES_SAVE_SUCCESS',true));
		}
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=excursionrateprices&layout=edit&rate_id=' . $data["rate_id"], false));
	}
}
