<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controlleradmin');

/**
 * Class JHotelReservationControllerInterestPoints
 */
class JHotelReservationControllerInterestPoints extends JControllerAdmin
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
    public function getModel($name = 'InterestPoint', $prefix = 'JHotelReservationModel', $config = array('ignore_request' => true))
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
            JError::raiseWarning(500, JText::_('LNG_NO_POINTS_OF_INTEREST_SELECTED',true));
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            // Remove the items.
            if (!$model->delete($cid))
            {
                $this->setMessage($model->getError());
            } else {
                $this->setMessage(JText::plural('LNG_N_POINTS_OF_INTEREST_DELETED', count($cid)));
            }
        }

        $this->setRedirect('index.php?option=com_jhotelreservation&view=interestpoints');
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
        $pks = JRequest::getVar('cid', array(), '', 'array');
        $order = JRequest::getVar('order', array(), '', 'array');


        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }
    function state()
    {
        $model = $this->getModel();
        $cid	= JRequest::getVar('cid', array(), '', 'array');
        $cid = $cid[0];
        if ($model->state($cid))
        {
            $msg = JText::_( '' ,true);
        } else {
            $msg = JText::_('LNG_ERROR_CHANGE_POI_STATE',true);
        }

        $this->setMessage($msg);
        $this->setRedirect('index.php?option=com_jhotelreservation&view=interestpoints',$msg);
    }
}
