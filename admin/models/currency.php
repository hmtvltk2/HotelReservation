<?php
// No direct access to this file
defined('_JEXEC') or die('No script kiddies Allowed');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class JHotelReservationModelCurrency extends JModelAdmin
{
    /**
     * @var		string	The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHOTELRESERVATION_CURRENCY';

    /**
     * Model context string.
     *
     * @var		string
     */
    protected $_context		= 'com_jhotelreservation.currency';


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'Currency', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }


    /**
     * Method to get the record form.
     *
     * @param       array   $data           Data for the form.
     * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jhotelreservation.currency', 'currency', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return      mixed   The data for the form.
     * @since       2.5
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.currency.data', array());
        if (empty($data))
        {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object	A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record)
    {
        return true;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object	A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     */
    protected function canEditState($record)
    {
        return true;
    }



    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('administrator');

        // Load the User state.
        $pk = (int) JRequest::getInt('id');
        if(!$pk)
            $pk = (int) JRequest::getInt('currency_id');
        $this->setState('currency.currency_id', $pk);

        $app->setUserState('com_jhotelreservation.edit.currency.currency_id',$pk);
    }


    /**
     * Method to get a menu item.
     *
     * @param   integer	The id of the menu item to get.
     *
     * @return  mixed  Menu item data object on success, false on failure.
     */
    public function getItem($itemId = NULL )
    {
        $itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('currency.currency_id');
        //print $itemId;

        $false = false;

        // Get a menu item row instance.
        $table = $this->getTable("Currency");

        // Attempt to load the row.
        $return = $table->load($itemId);

        //$return = $table->getItem($itemId);

        // Check for a table object error.
        if ($return === false && $table->getError())
        {
            $this->setError($table->getError());
            return $false;
        }
        $properties = $table->getProperties(1);
        $value = JArrayHelper::toObject($properties, 'JObject');

        $countryCurrency = $this->getTable('Country');
        $value->countries = $countryCurrency->getCountryCurrencies();

        return $value;
    }



    /**
     * Method to delete groups.
     *
     * @param   array  An array of item ids.
     * @return  boolean  Returns true on success, false on failure.
     */
    public function delete(&$items)
    {
        // Sanitize the ids.
        $items = (array) $items;
        JArrayHelper::toInteger($items);

        // Get a group row instance.
        $table = $this->getTable('Currency');

        // Iterate the items to delete each one.
        foreach ($items as $item)
        {

            if (!$table->delete($item))
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }
}