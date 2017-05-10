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
require_once 'defines_versions.php';

if( !defined( 'PREAUTHORIZATION_PAYMENT_ID') )
	define( 'PREAUTHORIZATION_PAYMENT_ID',1);
	
if( !defined( 'PENALTY_PAYMENT_ID') )
	define( 'PENALTY_PAYMENT_ID',3); 
if( !defined( 'CANCELED_PAYMENT_ID') )
	define( 'CANCELED_PAYMENT_ID',2);
if( !defined( 'DONE_PAYMENT_ID') )
	define( 'DONE_PAYMENT_ID',4);
if( !defined( 'PAYPAL_ID') )
	define( 'PAYPAL_ID',5); 
if( !defined( 'BANK_ORDER_ID') )
	define( 'BANK_ORDER_ID',6); 
if( !defined( 'CASH_ID') )
	define( 'CASH_ID',7); 
if( !defined( 'MPESA_ID') )
	define( 'MPESA_ID',8); 
if( !defined( 'IDEAL_OMNIKASSA_ID') )
	define( 'IDEAL_OMNIKASSA_ID',9);
if( !defined( 'P4B_ID') )
	define( 'P4B_ID',10);
if( !defined( 'BUCKAROO_ID') )
	define( 'BUCKAROO_ID',11);
if( !defined( 'EENMALIGE_INCASO_ID') )
	define( 'EENMALIGE_INCASO_ID',12);

//reservations
if( !defined( 'RESERVED_ID') )
	define( 'RESERVED_ID',1);
if( !defined( 'CANCELED_ID') )
	define( 'CANCELED_ID',2);
if( !defined( 'CHECKEDIN_ID') )
	define( 'CHECKEDIN_ID',3);
if( !defined( 'CHECKEDOUT_ID') )
	define( 'CHECKEDOUT_ID',4);
if( !defined( 'LATE_ID') )
	define( 'LATE_ID',5);
if( !defined('CANCELED_PENDING_ID' ) )
	define( 'CANCELED_PENDING_ID', 99);

//reservations
if( !defined( 'JHP_PAYMENT_STATUS_PENDING') )
	define( 'JHP_PAYMENT_STATUS_PENDING','0');
if( !defined( 'JHP_PAYMENT_STATUS_WAITING') )
	define( 'JHP_PAYMENT_STATUS_WAITING','1');
if( !defined( 'JHP_PAYMENT_STATUS_FAILURE') )
	define( 'JHP_PAYMENT_STATUS_FAILURE','2');
if( !defined( 'JHP_PAYMENT_STATUS_PAID'))
	define( 'JHP_PAYMENT_STATUS_PAID','3');
if( !defined( 'JHP_PAYMENT_STATUS_CANCELED') )
	define( 'JHP_PAYMENT_STATUS_CANCELED','4');

if( !defined( 'EMAIL_COMPANY_LOGO') )
	define( 'EMAIL_COMPANY_LOGO','[company_logo]');
if( !defined( 'EMAIL_SOCIAL_SHARING') )
	define( 'EMAIL_SOCIAL_SHARING','[social_sharing]');
if( !defined( 'EMAIL_RESERVATIONGENDER') )
	define( 'EMAIL_RESERVATIONGENDER','[gender]');
if( !defined( 'EMAIL_RESERVATIONFIRSTNAME') )
	define( 'EMAIL_RESERVATIONFIRSTNAME','[first_name]');	
if( !defined( 'EMAIL_RESERVATIONLASTNAME') )
	define( 'EMAIL_RESERVATIONLASTNAME','[last_name]');	
if( !defined( 'EMAIL_HOTEL_IMAGE') )
	define( 'EMAIL_HOTEL_IMAGE','[hotel_image]');
if( !defined( 'EMAIL_START_DATE') )
	define( 'EMAIL_START_DATE','[start_date]');
if( !defined( 'EMAIL_END_DATE') )
	define( 'EMAIL_END_DATE','[end_date]');
if( !defined( 'EMAIL_CHECKIN_TIME') )
	define( 'EMAIL_CHECKIN_TIME','[checkin_time]');
if( !defined( 'EMAIL_CHECKOUT_TIME') )
	define( 'EMAIL_CHECKOUT_TIME','[checkout_time]');

if( !defined( 'EMAIL_RESERVATIONDETAILS') )
	define( 'EMAIL_RESERVATIONDETAILS','[reservation_details]');	
if( !defined( 'EMAIL_BILINGINFORMATIONS') )
	define( 'EMAIL_BILINGINFORMATIONS','[billing_information]');	
if( !defined( 'EMAIL_PAYMENT_METHOD') )
	define( 'EMAIL_PAYMENT_METHOD','[payment_method]');	
if( !defined( 'EMAIL_GUEST_DETAILS') )
	define( 'EMAIL_GUEST_DETAILS','[guest_details]');
if( !defined( 'EMAIL_TOURIST_TAX') )
	define( 'EMAIL_TOURIST_TAX','[tourist_tax]');
if( !defined( 'EMAIL_PARKING_TAX') )
	define( 'EMAIL_PARKING_TAX','[parking_info]');
if( !defined( 'EMAIL_HOTEL_CANCELATION_POLICY') )
	define( 'EMAIL_HOTEL_CANCELATION_POLICY','[hotel_cancellation_policy]');
if( !defined( 'EMAIL_HOTEL_NAME') )
define( 'EMAIL_HOTEL_NAME','[hotel_name]');

if( !defined( 'EMAIL_MAX_DAYS_PAYD') )	
	define( 'EMAIL_MAX_DAYS_PAYD','[max_days_payd]');
if( !defined( 'EMAIL_RESERVATION_COST') )
	define( 'EMAIL_RESERVATION_COST','[reservation_cost]');
if( !defined( 'EMAIL_RESERVATION_ID') )
	define( 'EMAIL_RESERVATION_ID','[reservation_id]');
if( !defined( 'EMAIL_PLACEHOLDER') )	
	define( 'EMAIL_PLACEHOLDER','[email]');
if( !defined( 'EMAIL_COMPANY_NAME') )	
	define( 'EMAIL_COMPANY_NAME','[company_name]');
if( !defined( 'EMAIL_RATING_URL') )
	define( 'EMAIL_RATING_URL','[rating_url]');
if( !defined( 'EMAIL_INVOICE_HOTEL_DETAILS') )
	define( 'EMAIL_INVOICE_HOTEL_DETAILS','[invoice_hotel_details]');
if( !defined( 'EMAIL_INVOICE_DATE') )
	define( 'EMAIL_INVOICE_DATE','[invoice_date]');
if( !defined( 'EMAIL_INVOICE_NUMBER') )
	define( 'EMAIL_INVOICE_NUMBER','[invoice_number]');
if( !defined( 'EMAIL_INVOICE_FIELDS') )
	define( 'EMAIL_INVOICE_FIELDS','[invoice_fields]');
if( !defined( 'EMAIL_BOOKINGS_LIST') )
	define( 'EMAIL_BOOKINGS_LIST','[bookings_list]');
if( !defined( 'EMAIL_BANK_TRANSFER_DETAILS') )
	define( 'EMAIL_BANK_TRANSFER_DETAILS','[bank_transfer_details]');
if( !defined( 'EMAIL_SWIFT_CODE') )
	define( 'EMAIL_SWIFT_CODE','[swift_code]');
if( !defined( 'EMAIL_ACCOUNT_IBAN') )
	define( 'EMAIL_ACCOUNT_IBAN','[account_iban]');
if( !defined( 'EMAIL_CURRENT_DATE') )
	define( 'EMAIL_CURRENT_DATE','[current_date]');
if( !defined( 'EMAIL_HOTEL_NUMBER') )
	define( 'EMAIL_HOTEL_NUMBER','[hotelnumber]');

if( !defined( 'EMAIL_GUEST_LIST') )
	define( 'EMAIL_GUEST_LIST','[guest_list]');

if( !defined( 'EMAIL_ARRIVAL_DATE') )
	define( 'EMAIL_ARRIVAL_DATE','[arrival_date]');

if( !defined( 'PAYMENT_PROCESSORS_PAYFLOW_ID') )
	define( 'PAYMENT_PROCESSORS_PAYFLOW_ID',1);


if( !defined( 'PATH_ASSETS_IMG') )
	define( 'PATH_ASSETS_IMG', '/assets/img');	
if( !defined( 'PATH_PICTURES') )
	define( 'PATH_PICTURES', 'media/com_jhotelreservation/pictures');	
if( !defined( 'PATH_ROOM_PICTURES') )
	define( 'PATH_ROOM_PICTURES', '/rooms/');
if( !defined( 'PATH_EXCURSION_PICTURES') )
	define( 'PATH_EXCURSION_PICTURES', '/excursions/');
if( !defined( 'PATH_HOTEL_PICTURES') )
	define( 'PATH_HOTEL_PICTURES', 'media/com_jhotelreservation/pictures/hotels/');
if( !defined( 'PATH_OFFER_PICTURES') )
	define( 'PATH_OFFER_PICTURES', '/offers/');
if( !defined( 'EXTRA_OPTIONS_PICTURE_PATH') )
	define( 'EXTRA_OPTIONS_PICTURE_PATH', '/extraoptions');
if( !defined( 'POINTS_OF_INTEREST_PICTURE_PATH') )
    define( 'POINTS_OF_INTEREST_PICTURE_PATH', '/pointsofinterest');
if( !defined( 'LOGO_PICTURE_PATH') )
define( 'LOGO_PICTURE_PATH', '/logo/');

if( !defined( 'PICTURE_TYPE_EXTRA_OPTION') )
	define( 'PICTURE_TYPE_EXTRA_OPTION', 'picture_type_extra_option');

if( !defined( 'PICTURE_HEIGHT') )
	define( 'PICTURE_HEIGHT', 900);
if( !defined( 'PICTURE_WIDTH'))
	define( 'PICTURE_WIDTH', 1100);

if( !defined( 'ROOM_PICTURE_HEIGHT') )
	define( 'ROOM_PICTURE_HEIGHT', 250);
if( !defined( 'ROOM_PICTURE_WIDTH'))
	define( 'ROOM_PICTURE_WIDTH', 300);


if( !defined( 'ICON_SIZE') )
	define( 'ICON_SIZE', 100);	
		
if( !defined( 'POPUP_WINDOW_GALERY_SIZE_W') )
	define( 'POPUP_WINDOW_GALERY_SIZE_W', 1000);
if( !defined( 'POPUP_WINDOW_GALERY_SIZE_H') )
	define( 'POPUP_WINDOW_GALERY_SIZE_H', 1000);
	
if( !defined( 'POPUP_WINDOW_CALENDAR_SIZE_W') )
	define( 'POPUP_WINDOW_CALENDAR_SIZE_W', 800);
if( !defined( 'POPUP_WINDOW_CALENDAR_SIZE_H') )
	define( 'POPUP_WINDOW_CALENDAR_SIZE_H', 400);

if( !defined( 'LENGTH_ID_CONFIRMATION') )
	define( 'LENGTH_ID_CONFIRMATION',6);	

if( !defined( 'PROCESSOR_PAYFLOW') )
	define( 'PROCESSOR_PAYFLOW','PROCESSOR_PAYFLOW');
if( !defined( 'PROCESSOR_PAYPAL') )
	define( 'PROCESSOR_PAYPAL','paypal');
if( !defined( 'PROCESSOR_WIRE_TRANSFER') )
	define( 'PROCESSOR_WIRE_TRANSFER','wiretransfer'); 
if( !defined( 'PROCESSOR_CASH') )
	define( 'PROCESSOR_CASH','Cash'); 
if( !defined( 'PROCESSOR_MPESA') )
	define( 'PROCESSOR_MPESA','mpesa');  
if( !defined( 'PROCESSOR_IDEAL_OMNIKASSA') )
	define( 'PROCESSOR_IDEAL_OMNIKASSA','omnikassa');
if( !defined( 'PROCESSOR_BUCKAROO') )
	define( 'PROCESSOR_BUCKAROO','buckaroo');
if( !defined( 'PROCESSOR_AUTHORIZE') )
	define( 'PROCESSOR_AUTHORIZE','authorize');
if( !defined( 'PROCESSOR_4B') )
	define( 'PROCESSOR_4B','4b');
if( !defined( 'PROCESSOR_EENMALIGE_INCASO') )
	define( 'PROCESSOR_EENMALIGE_INCASO','eenmaligeincaso');

if( !defined( 'LIVE_ADDRESS_PAYPAL_EXPRESS') )
	define( 'LIVE_ADDRESS_PAYPAL_EXPRESS','https://www.paypal.com/cgi-bin/webscr'); 
if( !defined( 'TEST_ADDRESS_PAYPAL_EXPRESS') )
	define( 'TEST_ADDRESS_PAYPAL_EXPRESS','https://www.sandbox.paypal.com/cgi-bin/webscr'); 
	
if( !defined( 'LIVE_ADDRESS_PAYFLOW') )
	define( 'LIVE_ADDRESS_PAYFLOW','https://payflowpro.verisign.com/transaction'); 
if( !defined( 'TEST_ADDRESS_PAYFLOW') )
	define( 'TEST_ADDRESS_PAYFLOW','https://pilot-payflowpro.verisign.com/transaction'); 	


if( !defined( 'LIVE_ADDRESS_IDEAL_OMNIKASSA') )
	define( 'LIVE_ADDRESS_IDEAL_OMNIKASSA','https://payment-webinit.omnikassa.rabobank.nl/paymentServlet');
if( !defined( 'TEST_ADDRESS_IDEAL_OMNIKASSA') )
	define( 'TEST_ADDRESS_IDEAL_OMNIKASSA','https://payment-webinit.simu.omnikassa.rabobank.nl/paymentServlet');


if( !defined( 'LIVE_ADDRESS_BUCKAROO') )
	define( 'LIVE_ADDRESS_BUCKAROO','https://checkout.buckaroo.nl/html/');
if( !defined( 'TEST_ADDRESS_BUCKAROO') )
	define( 'TEST_ADDRESS_BUCKAROO','https://testcheckout.buckaroo.nl/html/');


if( !defined( 'ONLY_CHECKBOX_ROOMS_SELECTION') )
	define( 'ONLY_CHECKBOX_ROOMS_SELECTION',0);

if( !defined( 'KEY') )
	define( 'KEY',"\xc8\xd9\xb9\x06\xd9\xe8\xc9\xd2");

//used for manual processor - this is the time between the date when the payment is done and the date when the money enters in hotel bank account
if( !defined( 'BANK_TRANSFER_DELAY_TIME') )
	define( 'BANK_TRANSFER_DELAY_TIME',6);
	
if( !defined( 'LOGGER_PAYPAL_EXPRESS') )
	define( 'LOGGER_PAYPAL_EXPRESS',1);
	
if( !defined( 'DSC_FILL_GUEST') )
	define( 'DSC_FILL_GUEST',1);
	
if( !defined( 'VAT') )
	define( 'VAT', 19);
if( !defined( 'VAT_HOLLAND') )
	define( 'VAT_HOLLAND', 21);


if( !defined( 'MAX_LENGTH_HOTEL_DESCRIPTION') )
	define( 'MAX_LENGTH_HOTEL_DESCRIPTION', 200);

if( !defined( 'MAX_LENGTH_OFFER_DESCRIPTION') )
	define( 'MAX_LENGTH_OFFER_DESCRIPTION', 250);

if( !defined('MINIMUM_HOTEL_REVIEWS'))
	define( 'MINIMUM_HOTEL_REVIEWS', 5);

if( !defined( 'MAX_LENGTH_ROOM_NAME' ))
	define( 'MAX_LENGTH_ROOM_NAME', 55);
if( !defined( 'MAX_LENGTH_ROOM_DESCRIPTION' ))
	define( 'MAX_LENGTH_ROOM_DESCRIPTION', 10);

//types for translation 
if( !defined( 'HOTEL_TRANSLATION') )
define( 'HOTEL_TRANSLATION',1);

if( !defined( 'ROOM_TRANSLATION') )
	define( 'ROOM_TRANSLATION',2);

if( !defined( 'OFFER_TRANSLATION') )
	define( 'OFFER_TRANSLATION',3);

if( !defined( 'EXCURSION_TRANSLATION') )
	define( 'EXCURSION_TRANSLATION',4);

if( !defined( 'OFFER_SHORT_TRANSLATION') )
	define( 'OFFER_SHORT_TRANSLATION',5);

if( !defined( 'OFFER_CONTENT_TRANSLATION') )
	define( 'OFFER_CONTENT_TRANSLATION',6);

if( !defined( 'OFFER_INFO_TRANSLATION') )
	define( 'OFFER_INFO_TRANSLATION',7);

if( !defined( 'EXTRA_OPTIONS_TRANSLATION') )
	define( 'EXTRA_OPTIONS_TRANSLATION',8);

if( !defined( 'EMAIL_TEMPLATE_TRANSLATION') )
	define( 'EMAIL_TEMPLATE_TRANSLATION',9);

if (!defined('TERMS_AND_CONDITIONS_TRANSLATION') )
    define('TERMS_AND_CONDITIONS_TRANSLATION',10);

if (!defined('CANCELLATION_CONDITIONS') )
    define('CANCELLATION_CONDITIONS',11);

if (!defined('CHILDREN_CATEGORY') )
    define('CHILDREN_CATEGORY',12);

if (!defined('REVIEW_QUESTIONS_TRANSLATION') )
    define('REVIEW_QUESTIONS_TRANSLATION',13);

if (!defined('ROOM_NAME') )
    define('ROOM_NAME',14);

if( !defined( 'AIRPORT_TRANSFER_TRANSLATION') )
	define( 'AIRPORT_TRANSFER_TRANSLATION',15);

if( !defined( 'AIRPORT_TRANSFER_TRANSLATION_NAME') )
	define( 'AIRPORT_TRANSFER_TRANSLATION_NAME',16);

if( !defined( 'OFFER_NAME') )
    define( 'OFFER_NAME',17);

if( !defined( 'EXTRA_OPTION_NAME') )
    define( 'EXTRA_OPTION_NAME',18);

if( !defined( 'CHILDREN_CATEGORY_TRANSLATION') )
	define( 'CHILDREN_CATEGORY_TRANSLATION',19);

if( !defined( 'POI_TRANSLATION') )
    define( 'POI_TRANSLATION',20);
if( !defined('RATE_CLASSIFICATION_TRANSLATION'))
	define('RATE_CLASSIFICATION_TRANSLATION',21);
//end types for translation

if( !defined( 'HOTEL_TYPE_ID') )
	define( 'HOTEL_TYPE_ID',2);
if( !defined( 'PARK_TYPE_ID') )
	define( 'PARK_TYPE_ID',8);

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

if( !defined( 'CANCELATION_EMAIL') )
	define( 'CANCELATION_EMAIL','Cancelation Email');

if( !defined( 'RESERVATION_EMAIL') )
	define( 'RESERVATION_EMAIL','Reservation Email');

if( !defined( 'REVIEW_EMAIL') )
	define( 'REVIEW_EMAIL','Review Email');

if( !defined( 'INVOICE_EMAIL') )
	define( 'INVOICE_EMAIL','Invoice Email');

if( !defined( 'BOOKING_LIST') )
	define( 'BOOKING_LIST','Bookings List');

if( !defined( 'GUEST_LIST_EMAIL') )
	define( 'GUEST_LIST_EMAIL','Guest List Email');
if( !defined( 'INVOICE_CLIENT_EMAIL') )
	define( 'INVOICE_CLIENT_EMAIL','Client Invoice Email');

if(!defined( 'PAYMENT_REDIRECT'))
	define( 'PAYMENT_REDIRECT',1);
if(!defined( 'PAYMENT_SUCCESS'))
	define( 'PAYMENT_SUCCESS',2);
if(!defined( 'PAYMENT_WAITING'))
	define( 'PAYMENT_WAITING',3);
if(!defined( 'PAYMENT_ERROR'))
	define( 'PAYMENT_ERROR',4);
if(!defined( 'PAYMENT_CANCELED'))
	define( 'PAYMENT_CANCELED',5);
if(!defined( 'PAYMENT_IFRAME'))
	define( 'PAYMENT_IFRAME',6);

if(!defined( 'CUBILIS_RESERVATION_NEW'))
	define( 'CUBILIS_RESERVATION_NEW',0);
if(!defined( 'CUBILIS_RESERVATION_SENT'))
	define( 'CUBILIS_RESERVATION_SENT',1);
if(!defined( 'CUBILIS_RESERVATION_MODIFIED'))
	define( 'CUBILIS_RESERVATION_MODIFIED',2);
if(!defined( 'CUBILIS_RESERVATION_CANCELED'))
	define( 'CUBILIS_RESERVATION_CANCELED',3);
if(!defined( 'CUBILIS_RESERVATION_DELETED'))
	define( 'CUBILIS_RESERVATION_DELETED',4);

if(!defined( 'CUBILIS_MAX_RESERVATIONS'))
	define( 'CUBILIS_MAX_RESERVATIONS',3);

if(!defined( 'SHOW_PAYMENT_ADMIN_ONLY'))
	define('SHOW_PAYMENT_ADMIN_ONLY',0);
if(!defined( 'CHANNEL_MANAGER_CUBILIS'))
	define('CHANNEL_MANAGER_CUBILIS',"CUBILIS");

if(!defined( 'CHANNEL_MANAGER_BEDS24'))
	define('CHANNEL_MANAGER_BEDS24',"BEDS24");

if(!defined( 'HOTEL_COURSES'))
	define('HOTEL_COURSES',0);
if(!defined( 'HOTEL_EXCURSIONS'))
	define('HOTEL_EXCURSIONS',1);

if(!defined('DOCUMENTATION_URL'))
	define('DOCUMENTATION_URL','http://cmsjunkie.com/docs/jhotelreservation/');
if( !defined( 'LANGUAGE_RECEIVING_EMAIL'))
    define('LANGUAGE_RECEIVING_EMAIL','support@cmsjunkie.com');

if( !defined( 'NEWS_REFRESH_PERIOD'))
    define( 'NEWS_REFRESH_PERIOD',7);

/**
 * Constants for Guest Details Attributes
 */

if(!defined('JHP_HIDDEN_FIELD'))
    define('JHP_HIDDEN_FIELD',0);
if(!defined('JHP_OPTIONAL_FIELD'))
    define('JHP_OPTIONAL_FIELD',1);
if(!defined('JHP_MANDATORY_FIELD'))
    define('JHP_MANDATORY_FIELD',2);
if(!defined('JHP_PRICEFILTER_STEPS'))
	define('JHP_PRICEFILTER_STEPS',5);


/**
 * Constants for csv delimiters
 */
if(!defined('JHP_SEMICOLON'))
	define('JHP_SEMICOLON',';');
if(!defined('JHP_COMMA'))
	define('JHP_COMMA',',');
if(!defined('JHP_DOT'))
	define('JHP_DOT','.');

/**
 * Payment proccessors access groups
 */
if(!defined('JHP_PROCCESSOR_GUEST'))
	define('JHP_PROCCESSOR_GUEST',0);
if(!defined('JHP_PROCCESSOR_SUPER_USER'))
	define('JHP_PROCCESSOR_SUPER_USER',1);

if(!defined('HOTEL_MAX_CHILDREN_AGE'))
	define('HOTEL_MAX_CHILDREN_AGE',16);
?>

