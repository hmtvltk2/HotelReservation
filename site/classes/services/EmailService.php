<?php 

class EmailService{
	static function getEmailTemplate($languageTag,$hotelId, $template, $loadDefault = true)
	{
        $translations = new JHotelReservationLanguageTranslations();
        $db = JFactory::getDBO();
		//$languageTag = JRequest::getVar( '_lang');
		if(empty($languageTag)){
			$languageTag = "en-GB";
		}
		
		$query = " SELECT a.*
				   FROM #__hotelreservation_emails a
				   WHERE hotel_id='$hotelId' AND email_type = '".$template."'";
		$db->setQuery( $query );
		$templ = $db->loadObject();
		
		if(empty($templ) && $loadDefault){
			$query = "SELECT a.*
					  FROM #__hotelreservation_emails_default a
					  WHERE email_default_type = '".$template."'";
			$db->setQuery( $query );
			$templ= $db->loadObject();
			$templ->email_id = $templ->email_default_id;
			$templ->email_subject = $templ->email_default_subject;
				
		}
		//dmp($templ);exit;
        $emailTranslations = $translations->getObjectTranslation(EMAIL_TEMPLATE_TRANSLATION,$templ->email_id,$languageTag);
        $templ->email_content = $emailTranslations->content;

		if(!isset($templ) && $loadDefault){
			$query = "SELECT a.*
					  FROM #__hotelreservation_emails a
					WHERE email_type = '".$template."'";
			$db->setQuery( $query );
			$templ= $db->loadObject();
            $defaultEmailTranslations = $translations->getObjectTranslation(EMAIL_TEMPLATE_TRANSLATION,$templ->email_id,$languageTag);
            $templ->email_content = $defaultEmailTranslations->content;
		}
		return $templ;
	}

	function sendHotelEmail($data){
		//dmp($data);
		$body = $data["email_note"];
		$mode		 = 1 ;//html
		//dmp($body);
		$ret = JMail::sendMail(
				$data["email_from_address"],
				$data["email_from_name"],
				$data["email_to_address"],
				"Share hotel",
				$body,
				$mode
		);
	
		if($data["copy_yourself"]==1){
			$ret = JMail::sendMail(
					$data["email_from_address"],
					$data["email_from_name"],
					$data["email_from_address"],
					"Share hotel",
					$body,
					$mode
			);
		}
		return $ret;
	}
	
	public static function sendConfirmationEmail($reservationDetails, $sendOnlyToAdmin = false){

        $languageTag = JRequest::getVar('_lang');
		$emailTemplate = self::getEmailTemplate($languageTag,$reservationDetails->reservationData->hotel->hotel_id, RESERVATION_EMAIL,true);
		
		$content = EmailService::prepareReservationEmail($reservationDetails, $emailTemplate->email_content);
		$from = $reservationDetails->reservationData->appSettings->company_email;
		$fromName = $reservationDetails->reservationData->appSettings->company_name;
		$toEmail =  $reservationDetails->reservationData->userData->email;
		$subject = $emailTemplate->email_subject;
		$subject = str_replace(EMAIL_RESERVATION_ID, JHotelUtil::getStringIDConfirmation($reservationDetails->confirmation_id), $subject);
		$isHtml = true;

		$bcc = array($from, $reservationDetails->reservationData->hotel->email);
		if($reservationDetails->reservationData->appSettings->hide_user_email == 1){
			$bcc = array($toEmail, $reservationDetails->reservationData->hotel->email);
			$toEmail = $from;
		}
        if(isset($reservationDetails->reservationData->hotel->contact->booking_email) && !empty($reservationDetails->reservationData->hotel->contact->booking_email)) {
            $bcc[] = $reservationDetails->reservationData->hotel->contact->booking_email;
        }
		
		if($sendOnlyToAdmin){
			$subject = JText::_("LNG_WAITING_CONFIRMATION_EMAIL_ADMIN_SUBJECT");
			$subject = str_replace(EMAIL_RESERVATION_ID, JHotelUtil::getStringIDConfirmation($reservationDetails->confirmation_id), $subject);
			$bcc = null;
			$toEmail = $from;
		}
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml);
	}
	
	public static function sendClientInvoiceEmail($reservationDetails, $sendOnlyToAdmin = false){

        $languageTag = JRequest::getVar('_lang');
        $emailTemplate = self::getEmailTemplate($languageTag,$reservationDetails->reservationData->hotel->hotel_id, INVOICE_CLIENT_EMAIL,true);
		
		$content = self::prepareInvoiceEmail($reservationDetails, $emailTemplate->email_content);
		$from = $reservationDetails->reservationData->appSettings->company_email;
		$fromName = $reservationDetails->reservationData->appSettings->company_name;
		$toEmail =  $reservationDetails->reservationData->userData->email;
		$subject = $emailTemplate->email_subject;
		$subject = str_replace(EMAIL_RESERVATION_ID, JHotelUtil::getStringIDConfirmation($reservationDetails->confirmation_id), $subject);
		$isHtml = true;
	
		$bcc = array($from, $reservationDetails->reservationData->hotel->email);
		if($reservationDetails->reservationData->appSettings->hide_user_email == 1){
			$bcc = array($toEmail, $reservationDetails->reservationData->hotel->email);
			$toEmail = $from;
		}
	
		if($sendOnlyToAdmin){
			$subject = JText::_("LNG_WAITING_CONFIRMATION_EMAIL_ADMIN_SUBJECT");
			$bcc = null;
			$toEmail = $from;
		}
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml);
	}
	
	
	
	function sendReviewEmail($reservationDetails)
	{
        $languageTag = $reservationDetails->reservationData->userData->language_tag;
		$emailTemplate = self::getEmailTemplate($languageTag,$reservationDetails->reservationData->hotel->hotel_id,REVIEW_EMAIL );
	
		if( $emailTemplate ==null )
			return false;
		
		$content = EmailService::prepareReservationEmail( $reservationDetails,$emailTemplate->email_content);
		$from = $reservationDetails->reservationData->appSettings->company_email;
		$fromName = $reservationDetails->reservationData->appSettings->company_name;
		$toEmail =  $reservationDetails->reservationData->userData->email;
		
		$isHtml = 1;
	
		$subject = str_replace(EMAIL_HOTEL_NAME, $hotelName =$reservationDetails->reservationData->hotel->hotel_name
		,$emailTemplate->email_subject);

		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml);	
	}
	
	function sendCancelationEmail($reservationDetails)
	{
        $languageTag = JRequest::getVar('_lang');
        $emailTemplate = self::getEmailTemplate($languageTag,$reservationDetails->reservationData->hotel->hotel_id,CANCELATION_EMAIL );
	
		if( $emailTemplate ==null )
		return false;
	
		$content = EmailService::prepareReservationEmail( $reservationDetails,$emailTemplate->email_content);
		$from = $reservationDetails->reservationData->appSettings->company_email;
		$fromName = $reservationDetails->reservationData->appSettings->company_name;
		$toEmail =  $reservationDetails->reservationData->userData->email;

		$isHtml = 1;

        $sendOnlyToAdmin = $reservationDetails->reservationData->appSettings->send_cancellation_email_admin_only;

        $bcc = array($from, $reservationDetails->reservationData->hotel->email);

        if($sendOnlyToAdmin){
            $toEmail =  $from;
            $bcc = null;
        }

		$subject = str_replace(EMAIL_HOTEL_NAME, $hotelName = $reservationDetails->reservationData->hotel->hotel_name, $emailTemplate->email_subject);
	
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml);
	}
	
	function sendReviewSubmitedEmail($reservationDetails, $review){
		$mode		 = 1 ;//html
		$body = JText::_('LNG_REVIEW_RECEIVED',true);
	
		$body = str_replace("<<hotelname>>", $reservationDetails->reservationData->hotel->hotel_name, $body);
		$companyLogo = "<img src=\"".JURI::root().PATH_PICTURES.$reservationDetails->reservationData->appSettings->logo_path."\" alt=\"Company logo\" />";
		$body = str_replace(EMAIL_COMPANY_LOGO, $companyLogo, $body);
		$body = str_replace(EMAIL_COMPANY_NAME, $reservationDetails->reservationData->appSettings->company_name, $body);

		$body .= "<br/><br/>";
		$body.=$review->review_short_description;
		$body .= "<br/>";
		$body.=$review->review_remarks;

        if(!empty($review->contact)){
            $body .= "<br/>";
            $body.=$review->contact;
        }

		
		$subject = JText::_('LNG_NEW_REVIEW',true);
		$subject = str_replace("<<hotelname>>", $reservationDetails->reservationData->hotel->hotel_name, $subject);
	
		$from = $reservationDetails->reservationData->appSettings->company_email;
		$fromName = $reservationDetails->reservationData->appSettings->company_name;
		$toEmail =  $reservationDetails->reservationData->appSettings->company_email;
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $body, $mode);
	}
	
	public static function sendGuestListEmail($hotelId, $hotelName, $hotelEmail, $guestList, $arrivalDate){
		dmp("Send guest list for hotel ".$hotelName);
		dmp($guestList);

        $languageTag = JRequest::getVar( '_lang');
		$emailTemplate = self::getEmailTemplate($languageTag,$hotelId, GUEST_LIST_EMAIL, true);
		if(empty($emailTemplate))
			return;
		$subject = $emailTemplate->email_subject;
		$content = $emailTemplate->email_content;
		
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		$companyLogo = "<img src=\"".JURI::root().PATH_PICTURES.$appSettings->logo_path."\" alt=\"Company logo\" />";
		$content = str_replace(EMAIL_COMPANY_LOGO, $companyLogo, $content);
	
		$fromName	= $appSettings->company_name;
		$content = str_replace(EMAIL_COMPANY_NAME, $fromName, $content);
		
		$content = str_replace(EMAIL_GUEST_LIST, $guestList, $content);
		$content = str_replace(EMAIL_HOTEL_NAME, $hotelName, $content);
		$content = str_replace(EMAIL_ARRIVAL_DATE, JHotelUtil::convertToFormat($arrivalDate), $content);
		
		
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		self::sendEmail($appSettings->company_email, $appSettings->company_name, $appSettings->company_email,$hotelEmail, null, null, $subject, $content, true);
		
	}
	
	public static function sendNoAvailabilityEmail($hotelId, $startDate, $endDate){
		
		$log = Logger::getInstance();
		$log->LogDebug("No availabaility ".$hotelId." ".$startDate." ".$endDate);
		 
		$hotel=HotelService::getHotel($hotelId);
		
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		
		$datas =  JHotelUtil::convertToFormat($startDate);
		$datae =  JHotelUtil::convertToFormat($endDate);
	
		$mode		 = 1 ;//html
		$emailContent = JText::_('LNG_NO_AVAILABILITY_EMAIL',true);
		$emailContent = str_replace("<<hotel>>", $hotel->hotel_name, $emailContent);
		$emailContent = str_replace("<<start_date>>", $datas, $emailContent);
		$emailContent = str_replace("<<end_date>>", $datae, $emailContent);
	
		$email_subject = JText::_('LNG_NO_AVAILABILITY_EMAIL_SUBJECT',true);
		$email_subject = str_replace("<<hotel>>", $hotel->hotel_name, $email_subject);
		
		return self::sendEmail($appSettings->company_email, $appSettings->company_name, null, $appSettings->company_email, null, null, $email_subject, $emailContent, $mode);
	}
	
	public static function sendReservationFailureEmail($reservation){
		
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		$mode		 = 1 ;//html
		
		$log = Logger::getInstance();
		$log->LogDebug("Reservation failure ".serialize($reservation));
		
		$email = JText::_('LNG_RESERVAION_FAILURE_EMAIL',true);
		$email = str_replace("<<reservation_id>>", $reservation->confirmation_id, $email);
		$email = str_replace("<<start_date>>",  $reservation->start_date, $email);
		$email = str_replace("<<end_date>>",  $reservation->end_date, $email);
		$email = str_replace("<<name>>",  $reservation->last_name.' '. $reservation->first_name, $email);
		
		$email_subject = JText::_('LNG_RESERVAION_FAILURE_EMAIL_SUBJECT',true);
		$email_subject = str_replace("<<reservation_id>>", $reservation->confirmation_id, $email_subject);
		
		return self::sendEmail($appSettings->company_email, $appSettings->company_name, null, $appSettings->company_email, null, null, $email_subject, $email, $mode);
	}
	
	
	
	function prepareReservationEmail($reservationDetails, $emailTemplate){
		
		$datas = JHotelUtil::getDateGeneralFormat($reservationDetails->reservationData->userData->start_date);
		$datae = JHotelUtil::getDateGeneralFormat($reservationDetails->reservationData->userData->end_date);
	
		$ratingURL='<a href="'.JURI::root().'index.php?option='.getBookingExtName().'&controller=hotelratings&view=hotelratings&confirmation_id='.$reservationDetails->reservationData->userData->confirmation_id.'&lang='.$reservationDetails->reservationData->userData->language_tag.'">'.JText::_('LNG_CLICK_TO_RATE',true).'</a>';
		$companyLogo = "<img style='max-width:200px' src=\"".JURI::root().PATH_PICTURES.$reservationDetails->reservationData->appSettings->logo_path."\" alt=\"Company logo\" />";
		$zooverLogo = "<img style='max-width:200px;height=200px' src=\"http://www.goedverblijf.nl/images/zoover-award-logo.jpg\" alt=\"Zoover logo\" />";
		$zooverLogo = "";
		
		$chekInTime = $reservationDetails->reservationData->hotel->informations->check_in;
		$chekOutTime = $reservationDetails->reservationData->hotel->informations->check_out;
		$hotelName =$reservationDetails->reservationData->hotel->hotel_name;
		$cancellationPolicy =  $reservationDetails->reservationData->hotel->informations->cancellation_conditions;
		
		$currency = JHotelUtil::getCurrencyDisplay($reservationDetails->reservationData->userData->currency,null,null);
		$touristTax = $reservationDetails->reservationData->hotel->informations->city_tax_percent==1? $reservationDetails->reservationData->hotel->informations->city_tax.'% ': JHotelUtil::fmt($reservationDetails->reservationData->hotel->informations->city_tax, 2);
		$parkingTax = HotelService::getHotelParkingInfoStatus($reservationDetails->reservationData->hotel,$currency,true);
		
		$emailTemplate = str_replace(EMAIL_COMPANY_LOGO, 								$companyLogo,						$emailTemplate);
		$emailTemplate = str_replace(EMAIL_SOCIAL_SHARING, 								$zooverLogo,						$emailTemplate);
	
		$gender = JText::_("LNG_EMAIL_GUEST_TYPE_".$reservationDetails->reservationData->userData->guest_type,true);
	
		$emailTemplate = str_replace(EMAIL_RESERVATIONGENDER, 								$gender,						$emailTemplate);

		$emailTemplate = str_replace(EMAIL_RESERVATIONFIRSTNAME, 							$reservationDetails->reservationData->userData->first_name,									$emailTemplate);
		$emailTemplate = str_replace(EMAIL_RESERVATIONLASTNAME, 							$reservationDetails->reservationData->userData->last_name,					$emailTemplate);
	
		$emailTemplate = str_replace(EMAIL_START_DATE, 										$datas,								$emailTemplate);
		$emailTemplate = str_replace(EMAIL_END_DATE,	 									$datae,								$emailTemplate);
		$emailTemplate = str_replace(EMAIL_CHECKIN_TIME, 									$chekInTime,						$emailTemplate);
		$emailTemplate = str_replace(EMAIL_CHECKOUT_TIME, 									$chekOutTime,						$emailTemplate);
	
		$emailTemplate = str_replace(EMAIL_RESERVATIONDETAILS,								$reservationDetails->reservationInfo, 	$emailTemplate);
		$emailTemplate = str_replace(EMAIL_BILINGINFORMATIONS,								$reservationDetails->billingInformation,$emailTemplate);
		$emailTemplate = str_replace(EMAIL_PAYMENT_METHOD,									$reservationDetails->paymentInformation,$emailTemplate);
		$emailTemplate = str_replace(EMAIL_GUEST_DETAILS,									"", 				$emailTemplate);
	
		//$emailTemplate = str_replace(EMAIL_HOTEL_CANCELATION_POLICY, 						EMAIL_PARKING_TAX.$cancellationPolicy,				$emailTemplate);
		$emailTemplate = str_replace(EMAIL_HOTEL_CANCELATION_POLICY, 						$cancellationPolicy,				$emailTemplate);
		$emailTemplate = str_replace(EMAIL_HOTEL_NAME, 										$hotelName,							$emailTemplate);
		$emailTemplate = str_replace(EMAIL_TOURIST_TAX, 									$touristTax,						$emailTemplate);
		$emailTemplate = str_replace(EMAIL_PARKING_TAX, 									$parkingTax,						$emailTemplate);

		$emailText = "";
		$emailTemplate = str_replace(EMAIL_BANK_TRANSFER_DETAILS,							$emailText, 						$emailTemplate);
		$emailTemplate = str_replace(EMAIL_RATING_URL,										$ratingURL, 						$emailTemplate);

		$fromName	= $reservationDetails->reservationData->appSettings->company_name;
		$emailTemplate = str_replace(EMAIL_COMPANY_NAME,									$fromName, 							$emailTemplate);
	
		return $emailTemplate;
	}
	function prepareInvoiceEmail($reservationDetails, $emailTemplate)
	{
	
		$datas = JHotelUtil::getDateGeneralFormat($reservationDetails->reservationData->userData->start_date);
		$datae = JHotelUtil::getDateGeneralFormat($reservationDetails->reservationData->userData->end_date);

		$companyLogo = "<img src=\"".JURI::root().PATH_PICTURES.$reservationDetails->reservationData->appSettings->logo_path."\" alt=\"Company logo\" />";

		$currentDate = JHotelUtil::getDateGeneralFormat(date("Y-m-d H:i:s"));
		
		$chekInTime = $reservationDetails->reservationData->hotel->informations->check_in;
		$chekOutTime = $reservationDetails->reservationData->hotel->informations->check_out;
		$hotelName =$reservationDetails->reservationData->hotel->hotel_name;
		$cancellationPolicy =  $reservationDetails->reservationData->hotel->informations->cancellation_conditions;
		$touristTax = $reservationDetails->reservationData->hotel->informations->city_tax_percent==1? $reservationDetails->reservationData->hotel->informations->city_tax.'% ': JHotelUtil::fmt($reservationDetails->reservationData->hotel->informations->city_tax, 2);
	
		$emailTemplate = str_replace(EMAIL_COMPANY_LOGO, 								$companyLogo,						$emailTemplate);
	
		$gender = JText::_("LNG_EMAIL_GUEST_TYPE_".$reservationDetails->reservationData->userData->guest_type,true);
	
		$emailTemplate = str_replace(EMAIL_RESERVATIONGENDER, 								$gender,						$emailTemplate);

		$emailTemplate = str_replace(EMAIL_RESERVATIONFIRSTNAME, 							$reservationDetails->reservationData->userData->first_name,									$emailTemplate);
		$emailTemplate = str_replace(EMAIL_RESERVATIONLASTNAME, 							$reservationDetails->reservationData->userData->last_name,					$emailTemplate);
	
		$emailTemplate = str_replace(EMAIL_START_DATE, 										$datas,								$emailTemplate);
		$emailTemplate = str_replace(EMAIL_END_DATE,	 									$datae,								$emailTemplate);
		$emailTemplate = str_replace(EMAIL_CURRENT_DATE,	 								$currentDate,								$emailTemplate);
		$emailTemplate = str_replace(EMAIL_RESERVATION_ID, JHotelUtil::getStringIDConfirmation($reservationDetails->confirmation_id), $emailTemplate);
		
		
		$emailTemplate = str_replace(EMAIL_RESERVATIONDETAILS,								$reservationDetails->reservationInfo, 	$emailTemplate);
		$emailTemplate = str_replace(EMAIL_BILINGINFORMATIONS,								$reservationDetails->billingInformation,$emailTemplate);
		$emailTemplate = str_replace(EMAIL_PAYMENT_METHOD,									$reservationDetails->paymentInformation,$emailTemplate);
	
		$emailTemplate = str_replace(EMAIL_HOTEL_CANCELATION_POLICY, 						$cancellationPolicy,				$emailTemplate);
		$emailTemplate = str_replace(EMAIL_HOTEL_NAME, 										$hotelName,							$emailTemplate);

		$emailText = "";

		$fromName	= $reservationDetails->reservationData->appSettings->company_name;
		$emailTemplate = str_replace(EMAIL_COMPANY_NAME,									$fromName, 							$emailTemplate);
	
		return $emailTemplate;
		
	}
	
	public static function sendEmail($from, $fromName, $replyTo, $toEmail, $cc, $bcc, $subject, $content, $isHtml){

		$mail = JFactory::getMailer();
		$mail->setSender(array($from, $fromName));
		if(isset($replyTo))
			$mail->addReplyTo($replyTo);
		$mail->addRecipient($toEmail);
		if(isset($cc))
			$mail->addCC($cc);
		if(isset($bcc))
			$mail->addBCC($bcc);
		$mail->setSubject($subject);
		$mail->setBody($content);
		$mail->IsHTML($isHtml);

		
		$ret = $mail->send();
		
		$log = Logger::getInstance();
		$log->LogDebug("E-mail with subject ".$subject." sent from ".$from." to ".$toEmail." ".serialize($bcc)." result:".$ret);
		
		return $ret;
	}
}

?>