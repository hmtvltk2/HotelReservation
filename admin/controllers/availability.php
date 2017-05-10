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
class JHotelReservationControllerAvailability extends JControllerAdmin
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
	public function getModel($name = 'Availability', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back(){
		$this->setRedirect('index.php?option='.getBookingExtName());
	}
	
	public function saveHotelAvailability(){
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));
		$model = $this->getModel('Availability', '', array());
		$data = JRequest::get('post');
		
		if (!$model->saveHotelAvailability($data))
		{
			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=availability' , false));
	}
}
