<?php


defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
/**
 * Company Model for Companies.
 *
 */
class JHotelReservationModelReservation extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JHOTELRESERVATION_RESERVATION';

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context		= 'com_jhotelreservation.reservation';


	/**
	 * @var array to save the changed made
	 */
	protected $modifications = array();

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
	public function getTable($type = 'Confirmations', $prefix = 'Table', $config = array())
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
	public function populateState()
	{
		$app = JFactory::getApplication('administrator');

		$id = JRequest::getInt('reservationId');
		$this->setState('reservation.id', $id);
		
		$statusId = JRequest::getInt('statusId',0);
		$this->setState('reservation.status',$statusId);

		$hotelId = JRequest::getInt('hotel_id',0);
		//dmp($hotelId);
		if($hotelId){
			$this->setState('reservation.hotel_id',$hotelId);
		}
	}

	/**
	 * Method to get a menu item.
	 *
	 * @param   integer	The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function &getItem($itemId = null)
	{
		$reservation = null;
		$itemId = (!empty($itemId)) ? $itemId : (int) $this->getState('reservation.id');
		
		if(!$this->getState('reservation.hotel_id') && !$itemId)
			return $reservation;
		
		$reservationService = new ReservationService();
		$reservation = $reservationService->getReservation($itemId, $this->getState('reservation.hotel_id'), false);
		if($reservation)
			$this->setState('reservation.hotel_id', $reservation->reservationData->userData->hotelId);
		
		return $reservation;
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
		$form = $this->loadForm('com_jhotelreservation.reservation', 'item', array('control' => 'jform', 'load_data' => $loadData), true);
		if (empty($form))
		{
			return false;
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
		$data = JFactory::getApplication()->getUserState('com_jhotelreservation.edit.reservation.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	/**
	 * Method to save the form data.
	 *
	 * @param   array  The form data.
	 * @return  boolean  True on success.
	 */
	public function save($data)
	{
		$id	= (!empty($data['reservationId'])) ? $data['reservationId'] : (int) $this->getState('reservation.id');
		$isNew = empty($id);

		$reservationDetails = $this->getReservationDetails($data);

		if(!$isNew){
			$reservationService = new ReservationService();
			$oldReservationDetails = $reservationService->getReservation($id);
			$this->saveRaservationChanges($reservationDetails->reservationData->userData,$oldReservationDetails->reservationData->userData);
		}
		require_once JPATH_COMPONENT_SITE .'/models/confirmation.php';
		$confirmationModel = new JHotelReservationModelConfirmation();
		$reservaitonId = $confirmationModel->saveConfirmation($reservationDetails);

		if($isNew && $reservaitonId!=-1){
			$reservationDetails->confirmation_id = $reservaitonId;
			$processor = PaymentService::createPaymentProcessor(PROCESSOR_CASH);
			$paymentDetails = $processor->processTransaction($reservationDetails);
			PaymentService::addPayment($paymentDetails);
		}
		
		if($reservaitonId!=-1){
			$this->setState('reservation.id', $reservaitonId);
			
		}else{
			$this->setState('reservation.id', $id);
			$this->setError($confirmationModel->getError());
			return false;
		}
			
		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	function getReservationDetails($data){
		
		$userData = $this->populateReservationDetails($data);
		$reservationData = new stdClass;
		$reservationData->userData = $userData;
		$reservationData->appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$reservationData->hotel = HotelService::getHotel($userData->hotelId);
		
		UserDataService::setReservedItems($userData->reservedItems);
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->generateReservationSummary($reservationData, false);
		
		$reservationDetails->reservationData = $reservationData;
		UserDataService::setReservationDetails($reservationDetails);
		
		return $reservationDetails;
	}
	
	public function populateReservationDetails($data){
		$userData = new stdClass();
		$userData->confirmation_id = $data["reservationId"];
		$guestInfo = $this->getNumberOfGuests($data["roomdetails"]);
		$userData->roomGuests = $guestInfo->adults;
		$userData->roomGuestsChildren = $guestInfo->children;

		$userData->total_adults = 0;
		if(isset($userData->roomGuests) && count($userData->roomGuests)>=1){
			foreach($userData->roomGuests as $guestPerRoom){
				$userData->total_adults+= $guestPerRoom;
			}
		}
		
		$userData->adults = $userData->total_adults;
		$userData->children =0;
		
		$userData->first_name = $data["first_name"];
		$userData->last_name = $data["last_name"];
		$userData->address	= $data["address"];
		$userData->city	= $data["city"];
		$userData->state_name	= $data["state_name"];
		$userData->country	= $data["country"];
		$userData->postal_code= $data["postal_code"];
		$userData->phone = $data["phone"];
		$userData->email= $data["email"];
		$userData->company_name=$data["company_name"];
		$userData->guest_type = isset($data["guest_type"])?$data["guest_type"]:0;
		$userData->discount_code =$data["discount_code"];
		$userData->reservedItems = $data["reservedItem"];
		$userData->hotelId = $data["hotelId"];
		$userData->totalPaid = $data["totalPaid"];

		$userData->voucher=$data["voucher"];
		$userData->remarks=$data["remarks"];
		$userData->remarks_admin=$data["remarks_admin"];
		
		$userData->start_date=JHotelUtil::convertToMysqlFormat($data["start_date"]);
		$userData->end_date=JHotelUtil::convertToMysqlFormat($data["end_date"]);
		$hotel = HotelService::getHotel($userData->hotelId);
		$userData->currency=HotelService::getHotelCurrency($hotel); 
		
		$userData->arrival_time = $data["arrival_time"];
		
		$userData->rooms = count($data["roomdetails"]);
		if($data["update_price_type"]==2 || empty ($data["update_price_type"])){
			$userData->roomCustomPrices = $this->prepareCustomPrices($userData->reservedItems, $data["roomdetails"], $userData->start_date);
		}

		if(!empty($data["extraOptionIds"])){
			$extraOptions = array();
			if(isset($data["extraOptionIds"])){
				$extraOptions = ExtraOptionsService::parseExtraOptions($data);
			}
			$userData->extraOptionIds = $extraOptions;
		}

		$guestDetails = array();
		if(isset($data["guest_first_name"]))
		for($i=0;$i<count($data["guest_first_name"]);$i++){
			$guestDetail = new stdClass();
			$guestDetail->first_name = $data["guest_first_name"][$i];
			$guestDetail->last_name = $data["guest_last_name"][$i];
			$guestDetail->identification_number= $data["guest_identification_number"][$i];
			$guestDetails[] = $guestDetail;
		}
		
		$userData->guestDetails = $guestDetails;
		
		
		return $userData;
	}

	function prepareCustomPrices($reservedItems, $roomdetails, $startDate){
		$result = array();
		foreach($reservedItems as $reservedItem){
			$prices = $roomdetails[$reservedItem]["price"];
			
			$d = strtotime($startDate);
			foreach($prices as $price){
				$date = date("Y-m-d", $d);
				$result[]=$reservedItem."|".$date."|".$price;
				$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
			}
		}
	
		return $result;
	}
	
	function getNumberOfGuests($roomdetails){
		$result = new stdClass();
		$result->adults=array();
		$result->children=array();
		
		foreach($roomdetails as $roomdetail){
			$result->adults[]= $roomdetail["adults"];
			if(isset($roomdetail["children"])){
				$result->children[]= $roomdetail["children"];
			}
		}
		return $result;
	}
	
	/**
	 * Method to delete groups.
	 *
	 * @param   array  An array of item ids.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function delete(&$itemIds)
	{
		// Sanitize the ids.
		$itemIds = (array) $itemIds;
		JArrayHelper::toInteger($itemIds);
	
		// Get a group row instance.
		$table = $this->getTable("Confirmations");
	
		// Iterate the items to delete each one.
		foreach ($itemIds as $itemId)
		{
	
			if (!$table->delete($itemId))
			{
				$this->setError($table->getError());
				return false;
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}
	
	public function setStatus(){
		$reservationId = $this->getState('reservation.id');
		$status = $this->getState('reservation.status');
		

		$table = $this->getTable("Confirmations");
		$table->setStatus($reservationId, $status);
		$table->resetCubilisStatus($reservationId);
		
		if($status==CANCELED_ID)
			$this->sendCancellationEmail($reservationId);
	}
	
	public function setPaymentStatus($reservationId, $paymentStatusId){
		$table = $this->getTable("ConfirmationsPayments");
		
		if($paymentStatusId == JHP_PAYMENT_STATUS_PAID){
			$confirmationPaymentDetails = PaymentService::getConfirmationPaymentDetails($reservationId);
				
			if($confirmationPaymentDetails->processor_type == strtolower(PROCESSOR_CASH)){
				$appSettings = JHotelUtil::getApplicationSettings();
				$tableConfirmation = $this->getTable("Confirmations","Table");
				$tableConfirmation->load($reservationId);
				$confirmationPaymentDetails->amount = $appSettings->charge_only_reservation_cost==1? $tableConfirmation->cost: $tableConfirmation->total; 
				$confirmationPaymentDetails->payment_status = $paymentStatusId;
				PaymentService::updateReservationPayment($confirmationPaymentDetails);
			}
		}
		return $table->setPaymentStatus($reservationId, $paymentStatusId);
	}
	
	function getRoomTypesOptions(){
		$options = array();
		$languageTag = JRequest::getVar('_lang');

		$translationTable = new JHotelReservationLanguageTranslations();


		$hotelId = $this->getState('reservation.hotel_id');
		if(!$hotelId){
			return $options;
		}
		$query = " SELECT *
					FROM #__hotelreservation_rooms
					where hotel_id = $hotelId
					ORDER by room_name ";
		$rooms = $this->_getList( $query );
	
		foreach($rooms as $room){
			$roomTranslations = $translationTable->getObjectTranslation(ROOM_NAME,$room->room_id,$languageTag);
			$room->room_name = !isset($roomTranslations->content)?$room->room_name:$roomTranslations->content;
			$options[]	= JHtml::_('select.option', $room->room_id, $room->room_name);
		}
		return $options;
	}
	
	function getRoomHtmlContent($room, $startDate, $endDate){
		ob_start();
		?><fieldset class="roomrate" id="<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>">
			<legend>
				<?php echo (isset( $room->offer_name)?$room->offer_name." - ":"") ?> <?php echo $room->room_name ?>  &nbsp; <span
							onclick="removeRoom('<?php echo $room->offer_id."-".$room->room_id."-".$room->current?>')" class="removeroom">[ <?php echo JText::_('LNG_DELETE',true)?> ]</span>
			</legend>
			<div>
				<input type="hidden" name="reservedItem[]" value="<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>" />
				<div class="persons">
					<?php echo JText::_('LNG_ADULTS',true)?>: 
					<select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][adults]" id="room[<?php echo $room->room_id?>][adults]">
						<?php for($i=1; $i<=$room->max_adults;$i++){?>
							<option	value="<?php echo $i?>" <?php echo $i==$room->adults ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select>
					 <?php echo JText::_('LNG_JUNIORS',true)?>: 
					 <select name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][children]">
					 	<?php for($i=1; $i<=$room->max_children;$i++){?>
							<option	value="<?php echo $i?>" <?php echo $i==$room->children ?'selected="selected"':''?>><?php echo $i?></option>
						<?php } ?>
					</select> 
				</div>
				<div class="nights">
					<ul>
						<?php 
						
						for( $d = strtotime($startDate);$d < strtotime($endDate); ){
							$dayString = date( 'Y-m-d', $d);
							$price = $room->daily[$dayString]["price_final"];
							if(isset($room->customPrices) && isset($room->customPrices[$dayString])){
								$price = $room->customPrices[$dayString];
							}
						?>
						<li>
							<?php echo JText::_('LNG_PRICE',true)." - ".$dayString?>: <input type="text"	name="roomdetails[<?php echo $room->offer_id."|".$room->room_id."|".$room->current?>][price][<?php echo $dayString?>]" id="room_price_<?php echo $room->id?>_<?php echo $dayString?>" value="<?php echo $price?>">
							( <?php echo number_format($room->daily[$dayString]["price_final"],2) ?> )
						</li>
						<?php 
							$d = strtotime( date('Y-m-d', $d).' + 1 day ' );
						} 
						?>
					</ul>
				</div>
			</div>
		</fieldset><?php 
		
		$buff = ob_get_contents();
		$buff = htmlspecialchars($buff);
		ob_end_clean();
		
		return $buff;
	}
	
	function getHotels()
	{
        $hotelsTable = $this->getTable("Hotels");
        $hotels = $hotelsTable->getAllHotels();
        return $hotels;
	}
	
	function sendEmail($reservationId){
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId);
		return EmailService::sendConfirmationEmail($reservationDetails);
	}
	
	function sendClientInvoiceEmail($reservationId){
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId);
		return EmailService::sendClientInvoiceEmail($reservationDetails);
	}
	
	function sendCancellationEmail($reservationId){
		$reservationService = new ReservationService();
		$reservationDetails = $reservationService->getReservation($reservationId);
		return EmailService::sendCancelationEmail($reservationDetails);
	}
	
	
	function secretizeCard($reservationId){
		$creditCard = JRequest::getVar("card_number");
		if(isset($creditCard)){
			$creditCard = JHotelUtil::getInstance()->secretizeCreditCard($creditCard);
			$table = $this->getTable("ConfirmationsPayments");
			$table ->secretizeCard($reservationId,$creditCard);
		}
		return true;
	}


	function saveEmailCounterToChangeLog ($reservationId , $resent = 1) {

		$changeLog = new stdClass();
		$table = $this->getTable('ChangeLog','JTable');

		if($resent != 0 ) {
			$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
			$var = JText::_('LNG_CONFIRMATION_EMAIL')." ".JText::_('LNG_RESENT_BY');
			array_push( $this->modifications, $var );
		}

		$changeLog->description = implode(" || ",$this->modifications);


		if ( ! empty( $changeLog ) ) {
			$userId    = JFactory::getUser()->id;
			$timestamp = date( 'Y-m-d G:i:s' );

			$changeLog->reservation_id = $reservationId;
			$changeLog->date           = $timestamp;
			$changeLog->user_id        = $userId;

			if ($changeLog->reservation_id > 0) {
				$table->load($changeLog->reservation_id);
			}

			if (!$table->bind($changeLog)) {
				$this->setError($table->getError());
				return false;
			}

			// Check the data.
			if (!$table->check()) {
				$this->setError($table->getError());
				return false;
			}


			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			return true;

		}
	}


	function getEmailCounter($reservationId) {
		$table = $this->getTable('EmailTracking','JTable');
		$counter = $table->getConfirmationEmailCounter($reservationId);

		return $counter;
	}


	function getChangeLogs(){
		$reservationId = (int)$this->getState('reservation.id');
		$table = $this->getTable('ChangeLog','JTable');
		$changeLogs = $table->getChangeLogs($reservationId);
		$createdByUser = $table->getCreatedByUser($reservationId);


		foreach($changeLogs as $changeLog){

			$changeLog->createdByUsername = (isset($createdByUser) && !empty($createdByUser->createdByUsername))?$createdByUser->createdByUsername:JText::_('LNG_GUEST');

			if(strlen($changeLog->description) > 0){
				$changeLog->description = explode("||",$changeLog->description);
			}
		}
		return $changeLogs;
	}

    /**
     * @param $post
     * @return stdClass
     */
    function checkForReservationChanges($new, $old) {
	    $change = $this->getReservationDifferences( $new, $old );
	    if ( ! empty( $change ) ) {
		    $userId    = JFactory::getUser()->id;
		    $timestamp = date( 'Y-m-d G:i:s' );

		    $change->reservation_id = $old->confirmation_id;
		    $change->date           = $timestamp;
		    $change->user_id        = $userId;

		    return $change;
	    }
	    return false;
    }

    /**
     * @param $post
     * @return bool
     */
    function saveRaservationChanges($new, $old) {
        $table = $this->getTable('ChangeLog','JTable');

        $change = $this->checkForReservationChanges($new, $old);

        if(isset($change) && strlen($change->description) > 0) {

            $changesToSave = new stdClass();

            $changesToSave->reservation_id = $change->reservation_id;
            $changesToSave->date = $change->date;
            $changesToSave->user_id = $change->user_id;
            $changesToSave->description = $change->description;
            


            if ($change->reservation_id > 0) {
                $table->load($change->reservation_id);
            }

            //reset cubilis status 
            if ($change->resetCubilisStatus){
            	$tableConfirmations = $this->getTable("Confirmations");
            	$tableConfirmations->resetCubilisStatus($change->reservation_id);
            }

            if (!$table->bind($changesToSave)) {
                $this->setError($table->getError());
                return false;
            }

            // Check the data.
            if (!$table->check()) {
                $this->setError($table->getError());
                return false;
            }


            if (!$table->store()) {
                $this->setError($table->getError());
                return false;
            }

            return true;
        }
    }

	public function getReservationDifferences($newUserData, $oldUserData){

		$changeLog = new stdClass();
		$resetCubilisStatus = false; 
		


		if($newUserData->first_name!=$oldUserData->first_name){
			$var = JText::_('LNG_FIRST_NAME')." ".JText::_('LNG_FROM')." ".$oldUserData->first_name." ".JText::_('LNG_TO')." ".$newUserData->first_name;
			array_push($this->modifications,$var);
			$resetCubilisStatus = true;
		}
		if($newUserData->last_name!=$oldUserData->last_name){
			$var = JText::_('LNG_LAST_NAME')." ".JText::_('LNG_FROM')." ".$oldUserData->last_name." ".JText::_('LNG_TO')." ".$newUserData->last_name;
			array_push($this->modifications,$var);
			$resetCubilisStatus = true;
		}
		if($newUserData->address!=$oldUserData->address){
			$var = JText::_('LNG_ADDRESS')." ".JText::_('LNG_FROM')." ".$oldUserData->address." ".JText::_('LNG_TO')." ".$newUserData->address;
			array_push($this->modifications,$var);
		}
		if($newUserData->city!=$oldUserData->city){
			$var = JText::_('LNG_CITY')." ".JText::_('LNG_FROM')." ".$oldUserData->city." ".JText::_('LNG_TO')." ".$newUserData->city;
			array_push($this->modifications,$var);
		}
		if($newUserData->state_name!=$oldUserData->state_name){
			$var = JText::_('LNG_STATE')." ".JText::_('LNG_FROM')." ".$oldUserData->state_name." ".JText::_('LNG_TO')." ".$newUserData->state_name;
			array_push($this->modifications,$var);
		}
		if($newUserData->country!=$oldUserData->country){
			$var = JText::_('LNG_COUNTRY')." ".JText::_('LNG_FROM')." ".$oldUserData->country." ".JText::_('LNG_TO')." ".$newUserData->country;
			array_push($this->modifications,$var);
		}
		if($newUserData->postal_code!=$oldUserData->postal_code){
			$var = JText::_('LNG_POSTAL_CODE')." ".JText::_('LNG_FROM')." ".$oldUserData->postal_code." ".JText::_('LNG_TO')." ".$newUserData->postal_code;
			array_push($this->modifications,$var);
		}
		if($newUserData->phone!=$oldUserData->phone){
			$var = JText::_('LNG_PHONE')." ".JText::_('LNG_FROM')." ".$oldUserData->phone." ".JText::_('LNG_TO')." ".$newUserData->phone;
			array_push($this->modifications,$var);
		}
		if($newUserData->email!=$oldUserData->email){
			$var = JText::_('LNG_EMAIL')." ".JText::_('LNG_FROM')." ".$oldUserData->email." ".JText::_('LNG_TO')." ".$newUserData->email;
			array_push($this->modifications,$var);
		}
		if($newUserData->company_name!=$oldUserData->company_name){
			$var = JText::_('LNG_COMPANY_NAME')." ".JText::_('LNG_FROM')." ".$oldUserData->company_name." ".JText::_('LNG_TO')." ".$newUserData->company_name;
			array_push($this->modifications,$var);
		}
		if($newUserData->guest_type!=$oldUserData->guest_type){
			$var = JText::_('LNG_GENDER_TYPE')." ".JText::_('LNG_FROM')." ".JText::_('LNG_GUEST_TYPE_'.$oldUserData->guest_type)." ".JText::_('LNG_TO')." ".JText::_('LNG_GUEST_TYPE_'.$newUserData->guest_type);
			array_push($this->modifications,$var);
		}
		if($newUserData->discount_code!=$oldUserData->discount_code){
			$var = JText::_('LNG_DISCOUNT_CODE')." ".JText::_('LNG_FROM')." ".$oldUserData->discount_code." ".JText::_('LNG_TO')." ".$newUserData->discount_code;
			array_push($this->modifications,$var);
		}
		if($newUserData->voucher!=$oldUserData->voucher){
			$var = JText::_('LNG_VOUCHER')." ".JText::_('LNG_FROM')." ".$oldUserData->voucher." ".JText::_('LNG_TO')." ".$newUserData->voucher;
			array_push($this->modifications,$var);
		}
		if($newUserData->remarks!=$oldUserData->remarks){
			$var = JText::_('LNG_REMARKS')." ".JText::_('LNG_FROM')." ".$oldUserData->remarks." ".JText::_('LNG_TO')." ".$newUserData->remarks;
			array_push($this->modifications,$var);
		}
		if($newUserData->remarks_admin!=$oldUserData->remarks_admin){
			$var = JText::_('LNG_AMDIN_REMARKS')." ".JText::_('LNG_FROM')." ".$oldUserData->remarks_admin." ".JText::_('LNG_TO')." ".$newUserData->remarks_admin;
			array_push($this->modifications,$var);
		}
		if($newUserData->start_date!=$oldUserData->start_date){
			$var = JText::_('LNG_START_DATE')." ".JText::_('LNG_FROM')." ".$oldUserData->start_date." ".JText::_('LNG_TO')." ".$newUserData->start_date;
			array_push($this->modifications,$var);
		}
		if($newUserData->end_date!=$oldUserData->end_date){
			$var = JText::_('LNG_END_DATE')." ".JText::_('LNG_FROM')." ".$oldUserData->end_date." ".JText::_('LNG_TO')." ".$newUserData->end_date;
			array_push($this->modifications,$var);
		}
		if($oldUserData->arrival_time != "") {
			if ( $newUserData->arrival_time != $oldUserData->arrival_time ) {
				$var = JText::_( 'LNG_ARRIVAL_TIME' ) . " " . JText::_( 'LNG_FROM' ) . " " . $oldUserData->arrival_time . " " . JText::_( 'LNG_TO' ) . " " . $newUserData->arrival_time;
				array_push( $this->modifications, $var );
			}
		}


		foreach($newUserData->guestDetails as  $k => $newGuest) {
			foreach ( $newGuest as $prop => $value ) {
				if ( $oldUserData->guestDetails[ $k ]->$prop != $newGuest->$prop ) {
					$var = JText::_('LNG_GUEST_DETAILS')." ".JText::_('LNG_'.strtoupper($prop))." ".JText::_('LNG_FROM')." ".$oldUserData->guestDetails[ $k ]->$prop." ".JText::_('LNG_TO')." ".$newGuest->$prop;
					array_push($this->modifications,$var);
				}
			}
		}
		if(!empty($newUserData->airportTransfer))
		foreach ( $newUserData->airportTransfer as $k => $item ) {
			if ( $oldUserData->airportTransfer[ $k ] != $item ) {
				$var = JText::_('LNG_AIRPORT_TRANSFER_TYPES')." ".JText::_('LNG_FROM')." ".$oldUserData->airportTransfer[$k]." ".JText::_('LNG_TO')." ".$item;
				array_push($this->modifications,$var);
			}
		}

		if(!empty($newUserData->airportTransfer))
		foreach ( $newUserData->excursions as $k => $item ) {
			if ( $oldUserData->excursions[ $k ] != $item ) {
				$var = JText::_('LNG_EXCURSIONS')." ".JText::_('LNG_FROM')." ".$oldUserData->excursions[$k]." ".JText::_('LNG_TO')." ".$item;
				array_push($this->modifications,$var);
			}
		}

		foreach($oldUserData->reservedItems as $room) {
			$roomArray = explode("|", $room);
			$roomId = $roomArray[1];
			
			$oldExtras = ExtraOptionsService::parseRervationExtraOptions($oldUserData->extraOptionIds);
			$oldExtras = ExtraOptionsService::getHotelExtraOptions( $oldUserData->hotelId, $oldUserData->start_date, $oldUserData->end_date, $oldExtras, $roomId, null, $onlySelected = true,$oldUserData->confirmation_id);

			$newExtras = ExtraOptionsService::parseRervationExtraOptions($newUserData->extraOptionIds);
			$newExtras = ExtraOptionsService::getHotelExtraOptions( $newUserData->hotelId, $newUserData->start_date, $newUserData->end_date, $newExtras, $roomId, null, $onlySelected = true,$newUserData->confirmation_id);


			sort($oldExtras);
			sort($newExtras);
			foreach($oldExtras as $k  =>  $oldExtra) {
				foreach($newExtras as $newExtra) {
					if ( (int) $oldExtra->id == (int) $newExtra->id ) {

						if ( isset( $newExtra->checked ) && !isset( $oldExtra->checked ) && $newExtra->checked ) {
							$var = JText::_( 'LNG_EXTRA_OPTIONS' ) . " " . $newExtra->name .  " : " . JText::_( 'LNG_ADDED' );
							array_push($this->modifications,$var);
							$resetCubilisStatus = true;
						}

						if ( !isset( $newExtra->checked ) && isset( $oldExtra->checked ) && $oldExtra->checked  ) {

							$var = JText::_( 'LNG_EXTRA_OPTIONS' ) . " " . $newExtra->name .  " : " . Jtext::_( 'LNG_REMOVED' ) ;
							array_push($this->modifications,$var);
							$resetCubilisStatus = true;
						}

						if ( isset($oldExtra->persons) && isset($newExtra->persons) ) {
							if ( (int)$oldExtra->persons != (int)$newExtra->persons ) {
								$var = JText::_( 'LNG_EXTRA_OPTIONS' ) ." " .$newExtra->name." " . " " . JText::_( 'LNG_PERSONS' )." ".JText::_('LNG_FROM')." ".$oldExtra->persons ." ".JText::_('LNG_TO')." ".$newExtra->persons;
								array_push($this->modifications,$var);
								$resetCubilisStatus = true;
							}
						}
						if ( isset( $oldExtra->days ) && isset( $newExtra->days ) ) {
							if ( $oldExtra->days != $newExtra->days ) {
								$var = JText::_( 'LNG_EXTRA_OPTIONS' )." " .$newExtra->name." " . JText::_( 'LNG_PERSONS' )." ".JText::_('LNG_FROM')." ".$oldExtra->days ." ".JText::_('LNG_TO')." ".$newExtra->days;
								array_push($this->modifications,$var);			
								$resetCubilisStatus = true;
							}
						}

						if ( isset( $oldExtra->dates ) && isset( $newExtra->dates ) ) {
							if ( $oldExtra->dates != $newExtra->dates ) {
								$var = JText::_( 'LNG_EXTRA_OPTIONS' ) ." " .$newExtra->name." " . " " . JText::_( 'LNG_DATES' )." ".JText::_('LNG_FROM')." ".$oldExtra->dates ." ".JText::_('LNG_TO')." ".$newExtra->dates;
								array_push($this->modifications,$var);
								$resetCubilisStatus = true;
							}
						}
					}
				}
			}
		}

		//room adults
		foreach($newUserData->roomGuests  as $k => $roomGuest) {
			if ( $roomGuest != $oldUserData->roomGuests[ $k ] ) {
				$var = JText::_( 'LNG_ADULTS' ) . " " . JText::_( 'LNG_FROM' ) . " " . $oldUserData->roomGuests[$k] . " " . JText::_( 'LNG_TO' ) . " " . $roomGuest;
				array_push( $this->modifications, $var );
				$resetCubilisStatus = true;
			}
		}

		//room children
		foreach($newUserData->roomGuestsChildren as $k => $roomGuestChildren) {
			$oldRoomGuestChildren = explode("|",$oldUserData->roomGuestsChildren[ $k ]);
			if ( $roomGuestChildren != $oldRoomGuestChildren[0] ) {
				$var = $oldRoomGuestChildren[1]."=>".JText::_( 'LNG_CHILDREN' ) . " " . JText::_( 'LNG_FROM' ) . " " . $oldRoomGuestChildren[0] . " " . JText::_( 'LNG_TO' ) . " " . $roomGuestChildren;
				array_push( $this->modifications, $var );
			}
		}

		$existingRooms = $oldUserData->reservedItems;
		$newRooms= $newUserData->reservedItems;

		$deletedRooms = array_diff($existingRooms, $newRooms);
		$addedRooms = array_diff($newRooms, $existingRooms);
		$lang = JFactory::getLanguage();
		$languageTag = $lang->getTag();
		$hoteltranslationsModel = new JHotelReservationLanguageTranslations();

		if(count($deletedRooms)>0){
			foreach($deletedRooms as $room){
				$roomArray = explode("|",$room);
				$room = $roomArray[1];
				$roomTable = $this->getTable('Room',"JTable");
				$roomTable->load($room);
				if(isset($roomTable->room_id)) {
					$roomName = !empty($roomTable->room_name)?$roomTable->room_name:'undefinded room name';
					$room_name = $hoteltranslationsModel->getObjectTranslation( ROOM_NAME, $roomTable->room_id, $languageTag );
					$roomName  = isset( $room_name ) && ! empty( $room_name->content ) ? $room_name->content : $roomName;
					if ( $roomName != "" ) {
						$var = JText::_( 'LNG_RESERVATION' ) . "- " . JText::_( 'LNG_ROOM' ) . " " . JText::_( 'LNG_REMOVED' ) . ": " . $roomName;
						array_push( $this->modifications, $var );
						$resetCubilisStatus = true;
					}
				}
			}
		}
		if(count($addedRooms)>0){
			foreach($addedRooms as $room) {
				$roomArray = explode("|",$room);
				$room = $roomArray[1];
				$roomTable = $this->getTable( 'Room', "JTable" );
				$roomTable->load( $room );
				if ( isset( $roomTable->room_id ) ) {
					$roomName  = ! empty( $roomTable->room_name ) ? $roomTable->room_name : 'undefinded room name';
					$room_name = $hoteltranslationsModel->getObjectTranslation( ROOM_NAME, $roomTable->room_id, $languageTag );
					$roomName  = isset( $room_name ) && ! empty( $room_name->content ) ? $room_name->content : $roomName;
					if ( $roomName != "" ) {
						$var = JText::_( 'LNG_RESERVATION' ) . "- " . JText::_( 'LNG_ROOM' ) . " " . JText::_( 'LNG_ADDED',true) . ": " . $roomName;
						array_push( $this->modifications, $var );
						$resetCubilisStatus = true;
					}
				}
			}
		}

		$changeLog->description = implode(" || ",$this->modifications);
		$changeLog->resetCubilisStatus = $resetCubilisStatus;
		
		return $changeLog;
	}
}
