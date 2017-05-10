<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelGuestDetails extends JModelItem{
	
	protected function populateState(){
		$app = JFactory::getApplication('site');
		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);
	}
	
	function getGuestDetails(){
		
	}
	
	function getReservationDetails(){
		$userData = UserDataService::getUserData();
		$reservationData = new stdClass;
		$reservationData->userData = $userData;
	
		$reservationData->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$reservationData->hotel = HotelService::getHotel($userData->hotelId);
	
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->generateReservationSummary($reservationData);
	
		UserDataService::setReservationDetails($reservationDetails);
		$reservationDetails->reservationData = $reservationData;
	
		return $reservationDetails;
	}
	
	protected function getCountries(){
		$query = ' SELECT * FROM #__hotelreservation_countries order by country_name';
		$this->_db->setQuery($query);
		$countries = $this->_db->loadObjectList();
		return $countries;
	}


    /**
     * @param $guestdetail
     * Helper function for @getGuestDetailsFields to check the Guest attributes if they are
     * hidden , optional or mandatory
     * @return object with the attributes span for the span tag with an asteric , cssClass for the validate[required] for the required fields
     * and showField to show a field or not
     */
    private function checkGuestDataConfigAttributes($guestdetail){
                $attribute = new stdClass();
                $attribute->span = '';
                $attribute->cssClass = '';
                switch($guestdetail->config_type){
                    case JHP_HIDDEN_FIELD:
                        $attribute->showField = 0;
                        return $attribute;//class name from the css display : none;
                        break;
                    case JHP_OPTIONAL_FIELD:
                        $attribute->showField = 1;
                        return $attribute;//empty string
                        break;
                    case JHP_MANDATORY_FIELD:
                        $attribute->span = '<span class="mand">*</span>'; //span for mandatory fields
                        $attribute->cssClass = 'validate[required]'; //class name for mandatory fields
                        $attribute->showField = 1;
                        return $attribute;
                        break;
                }
    }

    /**
     * Render function to get and show the guest details fields with their attributes
     * @return string
     */
    public function getGuestDetailsFields()
    {
        //get the guest details field ids, names and attributes
        $table = JTable::getInstance("GuestDetailsAttributes", "JTable");
        $guestDetailsAttributes = $table->getAttributesConfiguration();

        //special cases to be handled before rendering
        $salutation = 'salutation';
        $country =  'country';
        $remarks = 'remarks';
        $email = 'email';
        $autocomplete = 'autocomplete_address';
        //user data typed in the generated fields
        $userData = UserDataService::getUserData();
        //all countries used in the country select options field
        $countries = $this->getCountries();
        //get the guest types used in the salutations radiolist
        $guestTypes = JHotelReservationHelper::getGuestTypes();

        ob_start();
        ?>

        <h4>
            <?php echo JText::_('LNG_BILLING_INFORMATION'); ?>
        </h4>

        <?php
        //For each of the guest type information generate a field
        foreach($guestDetailsAttributes as $guestdetail){
            //check based on guest type information name
            switch($guestdetail->name){
                // in the case of salutation radiolist is generated
                case $salutation:
                    $salutation_attr = $this->checkGuestDataConfigAttributes($guestdetail);
                        if($salutation_attr->showField) {
                            $radionListField = '<dt>'.JText::_('LNG_GENDER_TYPE').$salutation_attr->span.'</dt>';
                                $radionListField    .= '<dd id="mandatory">';
                                  $radionListField  .= '<div>' . JHtml::_('select.radiolist', $guestTypes, 'guest_type', 'class="'.$salutation_attr->cssClass.'"', 'value', 'text', $userData->guest_type, 'guest_type') . '</div>';
                                $radionListField    .= '</dd>';
                            echo $radionListField;
                        }
                    break;
                case $country:
                    //in case of country selected option is generated
                    $country_attr = $this->checkGuestDataConfigAttributes($guestdetail);
                    if($country_attr->showField) {
                        $selectOptionField = '<dt>'.JText::_('LNG_COUNTRY').$country_attr->span.'</dt>';
                        $selectOptionField   .='<dd id="countrydd">';
                        $selectOptionField   .= '<select id="'.$country.'" class="'.$country_attr->cssClass.'" name="'.$country.'">';
                        $selectOptionField    .= '<option value="">'.JText::_("LNG_SELECT_COUNTRY").'</option>';
                        foreach ($countries as $country) {
                            $countrySelected = ($country->country_name == $userData->country || $country->country_name == "Nederland") ? "selected":"";
                            $selectOptionField.='<option value="'.$country->country_name.'" '.$countrySelected.'>'. $country->country_name.'</option>';
                        }
                        $selectOptionField .= '</select>';
                        $selectOptionField  .= '</dd>';
                        echo $selectOptionField;
                    }
                    break;
                case $email:
                    //in the case of email the input of email , the info label
                    // and the confirmation email input field along with it
                    $email_attr = $this->checkGuestDataConfigAttributes($guestdetail);
                    $conf= 'conf_';
                    if($email_attr->showField){
                        $emailField = '<dt>'.JText::_("LNG_".strtoupper(str_replace(" ", "_",$email))).$email_attr->span.'</dt>';
                        $emailField.= '<dd><input type="text" class="validate[required,custom[email]]" name="'.$email.'" id="'.$email.'" size=25 value="'.$userData->email.'" ></dd>';
                        $emailField .= '<dt></dt>';
                        $emailField.=  '<dd>'.JText::_('LNG_PLEASE_NOTE_THAT_YOUR_EMAIL_WILL_BE_USED_AS_A_USERNAME').'</dd>';
                        $emailField.= '<dt>'.JText::_("LNG_CONFIRM_EMAIL").$email_attr->span.'</dt>';
                        $emailField.= '<dd><input type="text" class="validate[required,equals[email]]" name="'.$conf.$email.'" id="'.$conf.$email.'" size=25 value="'.$userData->conf_email.'"></dd>';
                        echo $emailField;
                    }
                    break;

                case $remarks:
                    //text Area for the remarks field
                    $remarks_attr = $this->checkGuestDataConfigAttributes($guestdetail);
                    if($remarks_attr->showField) {
                        $textAreaRemarks = '<dt>'.JText::_("LNG_EXTRA_INFO").$remarks_attr->span.'</dt>';
                        $textAreaRemarks .= '<dd><textarea class="'.$remarks_attr->cssClass.'" name="remarks" id="remarks" rows="3" cols="38">'.$userData->remarks.'</textarea></dd>';

                        echo $textAreaRemarks;
                    }
                    break;
                case $autocomplete:
                    $input_attr = $this->checkGuestDataConfigAttributes($guestdetail);
                    $input_name = $guestdetail->name;
                    if($input_attr->showField){
                        $inputField = '<dt>'.JText::_("LNG_".strtoupper(str_replace(" ", "_",$input_name))).$input_attr->span.'</dt>';
                        $inputField  .= '<dd><input type="text" class="'.$input_attr->cssClass.'" name="'.$input_name.'" id="'.$input_name.'" size=25></dd>';
                        echo $inputField;
                    }
                    break;

                //by default generate all of the other inputs
                // translated label by field name
                default :
                    $input_attr = $this->checkGuestDataConfigAttributes($guestdetail);
                    $input_name = $guestdetail->name;
                    if($input_attr->showField){
                        $inputField = '<dt>'.JText::_("LNG_".strtoupper(str_replace(" ", "_",$input_name))).$input_attr->span.'</dt>';
                        $inputField  .= '<dd><input type="text" class="'.$input_attr->cssClass.'" name="'.$input_name.'" id="'.$input_name.'" size=25 value="'.$userData->$input_name.'"></dd>';
                        echo $inputField;
                    }
                    break;

            }

        }
        $buff = ob_get_contents();
        ob_end_clean();

        return $buff;
    }
}