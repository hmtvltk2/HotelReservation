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


class JHotelReservationModelRatingQuestion extends JModelAdmin
{
    /**
     * @var		string	The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_JHOTELRESERVATION_RATINGQUESTION';

    /**
     * Model context string.
     *
     * @var		string
     */
    protected $_context		= 'com_jhotelreservation.ratinquestion';

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
     * Returns a Table object, always creating it
     *
     * @param   type	The table type to instantiate
     * @param   string	A prefix for the table class name. Optional.
     * @param   array  Configuration array for model. Optional.
     * @return  JTable	A database object
     */
    public function getTable($type = 'ReviewQuestion', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
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
        $pk = (int) JRequest::getInt('id',0,'');
        if(!$pk)
            $pk = (int) JRequest::getInt('review_question_id', 0 ,'');
        $this->setState('ratingquestion.review_question_id', $pk);

        $app->setUserState('com_jhotelreservation.edit.ratingquestion.review_question_id',$pk);


    }

    /**
     * Method to get a menu item.
     *
     * @param   integer	The id of the menu item to get.
     *
     * @return  mixed  Menu item data object on success, false on failure.
     */
    public function getItem($itemId = null)
    {
        $itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('ratingquestion.review_question_id');

        $false	= false;

        // Get a menu item row instance.
        $table = $this->getTable();

        // Attempt to load the row.
        $return = $table->load($itemId);

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
     * Method to get the menu item form.
     *
     * @param   array  $data		Data for the form.
     * @param   boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return  JForm	A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // The folder and element vars are passed when saving the form.
        if (empty($data))
        {
            $item		= $this->getItem();
            // The type should already be set.
        }
        // Get the form.
        $form = $this->loadForm('com_jhotelreservation.ratingquestion', 'ratingquestion', array('control' => 'jform', 'load_data' => $loadData), true);
        if (empty($form))
        {
            return false;
        }
        // Determine correct permissions to check.
        if ($this->getState('ratingquestion.review_question_id'))
        {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data))
        {
            // Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.ratingquestion.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to save rating Questions
     * @return mixed
     */
    function save($data){

        $id	= (!empty($data['review_question_id'])) ? $data['review_question_id'] : (int)$this->getState('ratingquestion.review_question_id');

        $data['review_question_desc'] = $this->setDefaultName($data);

        $row = $this->getTable();


        if ($id > 0) {
            $row->load($id);
            $isNew = false;
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
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
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        $this->setState('ratingquestion.review_question_id', $row->review_question_id);

        return true;

    }

    /**
     * Method to get the ReviewQuestions Data from the Table
     * @return JTable|null
     */
    public function getReviewQuestion()
    {
        // Get all review question
        $questionId = JRequest::getVar('cid');
        if(isset($questionId[0])){
            $table = $this->getTable('ReviewQuestion','JTable');
            $table->load($questionId[0]);
            return $table;
        }
        return null;
    }

    /**
     * Method to delete the rating Questions
     * @return mixed
     */
    function deleteratingquestions(){
        $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $post = JRequest::get('post');

        $row = $this->getTable('ReviewQuestion', 'JTable');

        if (count( $cids )) {
            foreach($cids as $cid) {
                if (!$row->delete( $cid )) {
                    return $row->getErrorMsg();
                }
            }
        }
        return JText::_('LNG_RATING_QUESTION_DELETED',true);
    }

    /**
     * Method to change the order of a question in the layout
     * @return mixed
     */
    function changequestionorder(){
        $questionId = JRequest::getVar( 'review_question_id');
        $tipOrder = JRequest::getVar( 'tip_order');
        if($tipOrder =="up")
            $direction = -1;
        else if ($tipOrder =="down")
            $direction = 1;
        $row = $this->getTable('ReviewQuestion', 'JTable');
        return $row->changequestionorder($questionId,$direction);
    }

    /**
     * Method to save the review questions in multiple languages
     * @param $post
     */
    function saveRatingQuestions($post){
        try{
            $path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
            $dirs = JFolder::folders( $path );
            sort($dirs);
            $modelHotelTranslations = new JHotelReservationLanguageTranslations();
            $modelHotelTranslations->deleteTranslationsForObject(REVIEW_QUESTIONS_TRANSLATION,$post['review_question_id']);
            foreach( $dirs  as $_lng ){
                if(isset($post['review_question_desc_'.$_lng]) && strlen($post['review_question_desc_'.$_lng])>0){
                    $reviewquestionDescription = JRequest::getVar( 'review_question_desc_'.$_lng, '', 'post', 'string');
                    $modelHotelTranslations->saveTranslation(REVIEW_QUESTIONS_TRANSLATION,$post['review_question_id'],$_lng,$reviewquestionDescription);
                }
            }
        }
        catch(Exception $e){
            JError::raiseWarning( 500,$e->getMessage());
        }

    }

    /**
     * @param $post
     */
    function setDefaultName($post){
        $languageTag = JRequest::getVar('_lang');
        $dirs = JHotelUtil::languageTabs();

        if(!empty($post['review_question_desc_'.$languageTag])) {
            $post['review_question_desc'] = $post['review_question_desc_'.$languageTag];
            return $post['review_question_desc'];
        }else{
            foreach($dirs as $_lng){
                if(!empty($post['review_question_desc_'.$_lng])){
                    $post['review_question_desc'] = $post['review_question_desc_'.$_lng];
                    return $post['review_question_desc'];
                }
            }
        }
    }

}
