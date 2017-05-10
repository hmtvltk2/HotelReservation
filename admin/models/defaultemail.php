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

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.modeladmin');


class JHotelReservationModelDefaultEmail extends JModelAdmin
{

    function __construct()
    {
        parent::__construct();
    }


    /**
     * @var        string    The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHOTELRESERVATION_DEFAULTEMAIL';

    /**
     * Model context string.
     *
     * @var        string
     */
    protected $_context = 'com_jhotelreservation.defaultemail';


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param       type    The table type to instantiate
     * @param       string  A prefix for the table class name. Optional.
     * @param       array   Configuration array for model. Optional.
     * @return      JTable  A database object
     * @since       2.5
     */
    public function getTable($type = 'DefaultEmails', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param       array $data Data for the form.
     * @param       boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     * @since       2.5
     */
    public function getForm($data = array(), $loadData = true)
    {
        //Get the form
        $form = $this->loadForm('com_jhotelreservation.defaultemail', 'defaultemail', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
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
        //Check the session for previously entered form data
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.defaultemail.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object    A record object.
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
     * @param   object    A record object.
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
        $pk = (int)JRequest::getInt('id');
        if (!$pk)
            $pk = (int)JRequest::getInt('email_default_id');
        $this->setState('defaultemail.email_default_id', $pk);

        $app->setUserState('com_jhotelreservation.edit.defaultemail.email_default_id', $pk);
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
        $itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('defaultemail.email_default_id');

        $false = false;

        // Get a menu item row instance.
        $table = $this->getTable("DefaultEmails");

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

        return $value;
    }

    /**
     * Method to delete a record
     * @return bool
     */
    function remove()
    {
        $cids = JRequest::getVar( 'email_default_id', array(0), 'post', 'array' );


        $row = $this->getTable();

        if (count( $cids )) {
            foreach($cids as $cid) {
                if (!$row->delete( $cid )) {
                    $this->setError( $row->getErrorMsg() );
                    return false;
                }
            }
        }
        return true;

    }

    /**
     * Method to store a record
     * @param $data
     * @return bool
     */
    function store($data)
    {
        $row = $this->getTable();

        // Bind the form fields to the table
        if (!$row->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError( $this->_db->getErrorMsg() );
            return false;
        }
        $this->_default_email_id = $row->email_default_id;

        return true;
    }


    /**
     * Method to save the default email content
     * @param $data
     */
    function saveEmailContent($data){
        try{
            $path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
            $dirs = JFolder::folders( $path );
            sort($dirs);
            $modelHotelTranslations = new JHotelReservationLanguageTranslations();
            $modelHotelTranslations->deleteTranslationsForObject(EMAIL_TEMPLATE_TRANSLATION,$data['email_default_id']);
            foreach( $dirs  as $_lng ){
                if(isset($data['email_content_'.$_lng]) && strlen($data['email_content_'.$_lng])>0){
                    $offerDescription = JRequest::getVar( 'email_content_'.$_lng, '', 'post', 'string', JREQUEST_ALLOWHTML );
                    $modelHotelTranslations->saveTranslation(EMAIL_TEMPLATE_TRANSLATION,$data['email_default_id'],$_lng,$offerDescription);
                }
            }
        }
        catch(Exception $e){
            print_r($e);
            exit;
            JError::raiseWarning( 500,$e->getMessage());
        }

    }
}