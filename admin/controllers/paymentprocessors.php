<?php


defined('_JEXEC') or die;
jimport('joomla.application.component.controlleradmin'); 

class JHotelReservationControllerPaymentProcessors extends JControllerAdmin
{

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->registerTask('saveOrderAjax','saveorder');
    }


    /**
	 * Display the view
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = false){
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
	 * @since   1.6
	 */
	public function getModel($name = 'PaymentProcessor', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back(){
		$this->setRedirect('index.php?option=com_jhotelreservation');
	}
	
	/**
	 * Removes an item
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));

		// Get items to remove from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_('COM_JHOTELRESERVATION_NO_PAYMENT_SELECTED',true));
		}
		else
		{
			// Get the model.
			$model = $this->getModel("PaymentProcessor");

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid))
			{
				$this->setMessage($model->getError());
			} else {
			$this->setMessage(JText::plural('COM_JHOTELRESERVATION_N_PAYMENT_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jhotelreservation&view=paymentprocessors');
	}

	function changeState()
	{
		$model = $this->getModel('PaymentProcessor');
	
		if ($model->changeState()){
            $msg = JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true);
            //$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE',true), 'warning');
		}else{
            $msg = JText::_( 'LNG_ERROR_CHANGE_STATE' ,true);
        }
	
		$this->setRedirect('index.php?option=com_jhotelreservation&view=paymentprocessors',$msg);
	}

	function changeFrontState()
	{
		$model = $this->getModel('PaymentProcessor');
	
		if (!$model->changeFrontState()){
			$this->setMessage(JText::_('LNG_ERROR_CHANGE_STATE',true), 'warning');
		}else{
            $this->setMessage(JText::_( 'LNG_STATE_CHANGED_SUCCESSFULLY' ,true));
        }
	
		$this->setRedirect('index.php?option=com_jhotelreservation&view=paymentprocessors');
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
		$model = $this->getModel("PaymentProcessors");
	
		// Save the ordering
		$return = $model->saveorder($pks, $order);
	
		if ($return)
		{
			echo "1";
		}
	
		// Close the application
		JFactory::getApplication()->close();
	}
}
