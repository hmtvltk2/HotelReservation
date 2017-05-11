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

function getBookingExtName()
{
	$componentname = "com_jhotelreservation";
	return $componentname;
}

if (!function_exists('dmp')) {

	function dmp( $text )
	{
		echo "<pre>";
		var_dump($text);
		echo "</pre>";
	}
}


class JHotelUtil{

	var $applicationSettings ;
	
	private function __construct()
	{
	
	}
	
	public static function getInstance()
	{
		static $instance;
		if ($instance === null) {
			$instance = new JHotelUtil();
		}
		return $instance;
	}
	
	public static function getApplicationSettings(){
		$instance = JHotelUtil::getInstance();
		if(!isset($instance->applicationSettings)){
			$db		=JFactory::getDBO();
			$query	= "	SELECT * FROM #__hotelreservation_applicationsettings fas
						inner join  #__hotelreservation_date_formats df on fas.date_format_id=df.id
						";
			$db->setQuery( $query );
			$instance->applicationSettings =  $db->loadObject();
		}
		return $instance->applicationSettings;
	}
	
	public static function getCurrencyDisplay($currency,$hotelCurrencyCode,$hotelCurrencySymbol){
		$applicationSettings = self::getApplicationSettings();
		$hotelCurrency = "";
		$sessionCurrency = "";
		
		if($applicationSettings->currency_display==1){
			 $sessionCurrency= empty($currency->name)?"": $currency->name;
			 $hotelCurrency = $hotelCurrencyCode;
		}
		else {
			$sessionCurrency =  empty($currency->symbol)?"": $currency->symbol;
			$hotelCurrency = $hotelCurrencySymbol;
				
		}
		
		return  !empty($sessionCurrency)?$sessionCurrency:$hotelCurrency;
	}
	
	public static function getHotelCurrencyDisplay(){
		$applicationSettings = self::getApplicationSettings();
	
		if($applicationSettings->currency_display==1)
			return $code;
		else
			return $symbol;
	}
	
	public static function loadAdminLanguage(){
		$user 	=JFactory::getUser();
		$db 	= JFactory::getDBO();
		
		//languages
		$language 		= JFactory::getLanguage();
		$language_tag 	= isset($language->_lang) ? $language->_lang : $language->getTag();
		JRequest::setVar('_lang',$language_tag);
		
		$x = $language->load(
								'com_installer' ,
					dirname(JPATH_ADMINISTRATOR.DS.'language') ,
					$language_tag,
					true
		);
		
		$x = $language->load(
				'com_jhotelreservation' ,
				dirname(JPATH_COMPONENT_ADMINISTRATOR. DS.'language') ,
				$language_tag,
				true
		);
	}
	
	public static function loadSiteLanguage(){
		$language 		= JFactory::getLanguage();
		$language_tag 	= JRequest::getVar( '_lang' );
		if($language_tag==""){
			$language_tag = isset($language->_lang) ? $language->_lang : $language->getTag();
			JRequest::setVar('_lang',$language_tag);
		}
		$x = $language->load(
				'com_jhotelreservation' ,
				dirname(JPATH_ADMINISTRATOR."/components/com_jhotelreservation/language") ,
				$language_tag,
				true
		);
		$x = $language->load(   'com_users' ,
				dirname( JPATH_SITE.DS.'language') ,
				$language_tag,
				true
		);	
		
		JRequest::setVar( 'language_tag',$language_tag);
		
		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');
	}
	
	public static function loadClasses(){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		
		//load payment processors
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'payment'.DS.'processors';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}
		
		//load payment processors
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'payment';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}
		
		//load services
		$classpath = JPATH_COMPONENT_SITE  .DS.'classes'.DS.'services';
		foreach( JFolder::files($classpath) as $file ) {
			JLoader::register(JFile::stripExt($file), $classpath.DS.$file);
		}
	}
	public static function getJoomlaLanguage(){
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		$tagArray = explode("-",$language_tag);
		return $tagArray[0];
	}
	
	public static function getStringIDConfirmation($confirmationId)
	{
		return str_pad($confirmationId, LENGTH_ID_CONFIRMATION, "0", STR_PAD_LEFT);
	}
	
	public static function secretizeCreditCard($creditCardNumber){
		$ex = $creditCardNumber;
	
		if( strlen($ex) <= 4 )
		{
			for( $i =0; $i < strlen($ex); $i++ )
			{
				$cc = $cc."".str_repeat("X", strlen($ex[$i]));
				if( $i < count($ex)-1 )
				$cc = $cc. "-";
			}
			$creditCardNumber = $cc;
		}
		else
		$creditCardNumber = str_repeat("*", strlen($creditCardNumber)-4).substr($creditCardNumber,-4);
	
		return $creditCardNumber;
	}
	public static function showUnavailable(){
		$app = JFactory::getApplication();
		$app->redirect(JRoute::_('index.php?option='.getBookingExtName().'&task=hotelsettings.showUnavailable'),"");
	}	
	public static function getDashBoardIcon(){
		return "home";
	}
	
	public static function getEmailDefaultIcon(){
		return "generic.png";
	}
	
	public static function getExportIcon(){
		return "download";
	}
	
	public static function getCoordinates($zipCode){
		try{
			if(empty($zipCode)){
				return null;
			}
			$url ="http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($zipCode);
			$data = file_get_contents($url);
			$search_data = json_decode($data);
			if(empty($search_data->results[0]->geometry->location->lat)){
				return null;
			}
			$lat =  $search_data->results[0]->geometry->location->lat;
			$lng =  $search_data->results[0]->geometry->location->lng;
		
		
			$location =  array();
			$location["latitude"] = $lat;
			$location["longitude"] = $lng;
		
			return $location;
		}
		catch(Exception $e){
			
		}
		
		return null;
	}
	
	//included functions
	

	public static function my_round($x,$decimals=2)
	{
		if ($x < 0)
			$semn=-1;
		else
			$semn = 1;
	
		$fuzzy = 0.0000001;
		return round(abs($x) + $fuzzy, $decimals)*$semn;
	}
	
	public static function checkIndexKey( $table_name, $arr_fields, $key_autoincrement, $current_id )
	{
		$conditions = '';
	
		foreach( $arr_fields as $key => $value )
		{
			if( $conditions != '' )
				$conditions .= ' AND ';
			$conditions .= " $key = '$value' ";
		}
		$db		=JFactory::getDBO();
		$query	= "
		SELECT *
		FROM $table_name
		WHERE
		1
		".( strlen($conditions)>0? " AND $conditions " : "") ."
		".($current_id !=''? " AND $key_autoincrement <> $current_id" : "")."
		";
	
		$db->setQuery( $query );
		if (!$db->query() )
			{
			JError::raiseWarning( 500, JText::_('LNG_UNKNOWN_ERROR',true) );
			return true;
		}
		return  $db->getNumRows() > 0;
	}
	
	public static function fmt($val, $decimals=2 )
	{
		if(!isset($val) || !is_numeric($val) ){
			return "N/A";
		}
		// TODO have number format in application settings 
		$val = number_format($val, $decimals); // english format:  1,235.50 
		//$val = number_format($val, $decimals,',','.'); //french format 1.234,56 
		 
		return $val;
	}
	public static function padNumber($val, $length=2 )
	{
		return str_pad($val, $length, "0", STR_PAD_LEFT);
	}
	public static function makePathFile($path){
		$path_tmp = str_replace( '\\', DIRECTORY_SEPARATOR, $path );
		$path_tmp = str_replace( '/', DIRECTORY_SEPARATOR, $path_tmp);
		return $path_tmp;
	}

	public static function getCurrentJoomlaVersion()
	{
		$version = new JVersion;
		$version = new JVersion;
		return $version->RELEASE + 0.00;
	}
	
	public static function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
		$ret = array();
	
		foreach ( (array) $data as $k => $v ) {
			if ( $urlencode)
				$k = urlencode($k);
			if ( is_int($k) && $prefix != null )
				$k = $prefix.$k;
			if ( !empty($key) )
				$k = $key . '%5B' . $k . '%5D';
			if ( $v === NULL )
				continue;
			elseif ( $v === FALSE )
				$v = '0';
		
			if ( is_array($v) || is_object($v) )
				array_push($ret,JHotelUtil::_http_build_query($v, '', $sep, $k, $urlencode));
			elseif ( $urlencode )
				array_push($ret, $k.'='.urlencode($v));
			else
				array_push($ret, $k.'='.$v);
		}
		if ( NULL === $sep )
			$sep = "|";

		return implode($sep, $ret);
	}
	
	public static function convertToFormat($date){
		if(!isset($date) || $date=='' || strcmp($date,"0000-00-00")==0)
			return $date;
	
			$appSettings = self::getApplicationSettings();
			$date = date($appSettings->dateFormat, strtotime($date));
			return $date;
	}

	public static function convertToMysqlFormat($date){
		if(empty($date))
			return $date;
		$date = date("Y-m-d", strtotime($date));
		return $date;
	}
	
	public static function getDateGeneralFormat($data){
	
		if(!isset($data) || $data=='' || strcmp($data,"0000-00-00")==0)
			return $data;

		$data =strtotime($data);
		$appSettings= self::getApplicationSettings();
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();

		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');

		switch ($appSettings->dateFormat){
			case "Y-m-d":
				if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
					$dateS =  strftime("%Y %B %#d", $data);
				else
					$dateS =  strftime("%Y %B %e", $data);
				break;
            case "m-d-Y":
                if(PHP_OS == "WIN32" || PHP_OS == "WINNT")
                    $dateS =  strftime("%b %#d, %Y", $data);
                else
                    $dateS =  strftime("%b %e, %Y", $data);
                break;
			case "m/d/Y":
				if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
					$dateS =  strftime("%A, %B %#d, %Y", $data);
				else
					$dateS =  strftime("%A, %B %e, %Y", $data);
				break;
            case "d-m-Y":
                if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
                    $dateS =  strftime("%A, %#d %B, %Y", $data);
                else
                    $dateS =  strftime("%A, %e %B, %Y", $data);
                break;
            default: 
            	$dateS =  strftime("%Y %B %e", $data);
            break;
            	
        }
	
		return $dateS;
	}
	

	
	
	public static function getDateGeneralFormatDay($data){

		if(!isset($data) || $data=='' || strcmp($data,"0000-00-00")==0)
			return $data;

		$data =strtotime($data);

		$appSettings= self::getApplicationSettings();
		setlocale(LC_TIME, $appSettings->date_language/*.'.UTF-8'*/);
		if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
		$dateS =  strftime("%A %#d", $data);
				else
		$dateS =  strftime("%A %e", $data);

		return $dateS;
	}
	
	
	public static function getDateGeneralFormatWithTime($data){
		if(!isset($data) || $data=='' || strcmp($data,"0000-00-00")==0)
			return $data;
	
		$data =strtotime($data);
		$dateS = date( 'j M Y  G:i:s', $data );
	
		return $dateS;
	}
	
	public static function truncate($text, $length, $suffix = '&hellip;', $isHTML = true){
		return substr(strip_tags($text), 0, $length);
	}
	
	public static function truncateOld($text, $length, $suffix = '&hellip;', $isHTML = true){
		$i = 0;
		$tags = array();
		if($isHTML){
		preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		foreach($m as $o){
			if($o[0][1] - $i >= $length)
				break;
				$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
				if($t[0] != '/')
					$tags[] = $t;
					elseif(end($tags) == substr($t, 1))
					array_pop($tags);
					$i += $o[1][1] - $o[0][1];
			}
		}
	
		$output = substr($text, 0, $length = min(strlen($text),  $length + $i)) . (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');
	
		// Get everything until last space
		$one = substr($output, 0, strrpos($output, " "));
		// Get the rest
		$two = substr($output, strrpos($output, " "), (strlen($output) - strrpos($output, " ")));
			// Extract all tags from the last bit
			preg_match_all('/<(.*?)>/s', $two, $tags);
			// Add suffix if needed
			if (strlen($text) > $length) {
			$one .= $suffix;
		}
			// Re-attach tags
			$output = $one . implode($tags[0]);
	
		return $output;
	}
	
	public static function getAvailabilityCalendar($hotelId, $month, $year, $rooms, $nrDays=2, $identifier, $loading = false){

		/* draw table */
		$calendar = '<table cellpadding="0" cellspacing="0" class="availability-calendar">';

		$appSettings= self::getApplicationSettings();
		setlocale(LC_TIME, $appSettings->date_language/*.'.UTF-8'*/);

		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		$language_tag = str_replace("-","_",$language->getTag());
		setlocale(LC_TIME , $language_tag.'.UTF-8');
		/* table headings */
		// start first day with Monday
		for($i=1;$i<8;$i++)
			$headings [] = strftime("%a ", mktime(0,0,0,3,29,2009)+$i * (3600*24));

		$calendar.= '<tr><td colspan="7" align="center"><table align="center" width="50%" class="room-calendar-header"><tr>';
		$calendar.= '<td><a href="javascript:void(0)" class="prevent-click" onclick="showRoomCalendar('.$hotelId.','.date('\'Y\',\'n\'',mktime(0,0,0,$month-1,1,$year)).',\''.$identifier.'\')"><i class="fa fa-arrow-circle-left calendarArrow"></i></a></td><td class="calendar-month">'. strftime("%B", mktime(0,0,0,$month,1,$year)).'&nbsp;'.$year.'</td><td><a href="javascript:void(0)" class="prevent-click" onclick="showRoomCalendar('.$hotelId.','.date('\'Y\',\'n\'',mktime(0,0,0,$month+1,1,$year)).',\''.$identifier.'\')"><i class="fa fa-arrow-circle-right calendarArrow"></i></a></td>';
					$calendar.= '</tr></table></td></tr>';
		
					$calendar.=  '<tr><td colspan="7">';
					$calendar.=  '<div id="loader-'.$identifier.'" class="room-loader" style="display:'.($loading?'block':'none').'"></div>';
		
		$calendar.= '<table id="room-calendar-'.$identifier.'" style="display:'.($loading?'none':'block').'">';
		
					$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
		
					/* days and weeks vars now ... */
		// start first day with Monday
		$running_day = (date('w',mktime(0,0,0,$month,1,$year))+6)%7;
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
					$days_in_this_week = 1;
					$day_counter = 0;
					$dates_array = array();
		
						/* row for week one */
		$calendar.= '<tr class="calendar-row">';
	
		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++){
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			$days_in_this_week++;
		}
	
		$appSetings = self::getApplicationSettings();
		$dateFormat = $appSetings->dateFormat;
		/* keep going with days.... */
		if(!$loading){
			for($list_day = 1; $list_day <= $days_in_month; $list_day++){
				$calendar.= '<td class="calendar-day">';
				/* add in the day number */
				
				$startDate = date($dateFormat,mktime(0,0,0,$month,$list_day,$year));
				$endDate = date($dateFormat,mktime(0,0,0,$month,$list_day+$nrDays,$year));
		
				$currentMonthDay = date('j');
				if(($list_day<($currentMonthDay) && $month==date('n') && $year==date('Y')) || ($month<date('n')&& $year==date('Y'))|| $year<date('Y'))
					$rooms[$list_day-1]["isAvailable"] = false;
				
				if($rooms[$list_day-1]["isAvailable"]){
					$priceClass=$rooms[$list_day-1]["price"]>1000?"small":"";
					$calendar.='<div class="day-cell" onclick="selectCalendarDate('.$hotelId.',\''.$startDate.'\',\''.$endDate.'\');">
					<div class="date">
							<div class="date '.($rooms[$list_day-1]["isAvailable"]==false?'not-available':'').'">'.sprintf('%02s',$list_day).'/'.sprintf('%02s',$month).'</div>
							<div class="price '.$priceClass.'">'.$rooms[$list_day-1]["price"].'</div>
					</div>
					</div>';
				}else{
					$cssClass = "not-available";
					if($rooms[$list_day-1]["lockArrival"]) 
						$cssClass .= " lock-arrival";
					if($rooms[$list_day-1]["lockDeparture"])
						$cssClass .= " lock-departure";
					$calendar.='<div class="day-cell '.$cssClass.'" >
					<div class="date">
					<div class="date not-available">'.sprintf('%02s',$list_day).'/'.sprintf('%02s',$month).'</div>
					<div class="price">'.$rooms[$list_day-1]["price"].'</div>
					</div>
					</div>';
				}
		
				$calendar.= '</td>';
				if($running_day == 6){
					$calendar.= '</tr>';
					if(($day_counter+1) != $days_in_month){
						$calendar.= '<tr class="calendar-row">';
					}
					$running_day = -1;
					$days_in_this_week = 0;
				}
				$days_in_this_week++; $running_day++; $day_counter++;
			}	
		}
					/* finish the rest of the days in the week */
		if($days_in_this_week < 8){
			for($x = 1; $x <= (8 - $days_in_this_week); $x++){
				$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			}
		}
	
		/* final row */
		$calendar.= '</tr>';
		$calendar.= '</table></td></tr>';
		$calendar.= '<tr><td>'.JText::_('LNG_ROOM_CALENDAR_INFO',true).'</td></tr>';
		
		$calendar.= '<tr>';
		$calendar.= '<td>';
		$calendar.= '<div class="legend"><div class="available"></div>'.JText::_('LNG_AVAILABLE',true).'</div>';
		$calendar.= '<div class="legend"><div class="not-available"></div>'.JText::_('LNG_NOT_AVAILABLE',true).'</div>';
		$calendar.= '<div class="legend"><div class="lock-arrival"></div>'.JText::_('LNG_LOCK_FOR_ARRIVAL',true).'</div>';
		$calendar.= '<div class="legend"><div class="lock-departure"></div>'.JText::_('LNG_LOCK_FOR_DEPARTURE',true).'</div>';
		$calendar.= '</td>';
		$calendar.= '</tr>';
		/* end the table */
		$calendar.= '</table>';

		/* all done, return result */
		return $calendar;
	}

    /**
     * @param $title
     * @param $alias
     * @return string
     * Method to Autogenerate alias for hotel_name If Alias field is left empty
     * or save its values and format it for Safe URL usage
     */
    public static function getAlias($title, $alias){
        if (empty($alias) || trim($alias) == ''){
            $title = strtolower($title);
            $title = str_replace("'", '', $title);
            $title = JApplication::stringURLSafe($title);
            $alias = $title;
            return $alias;
        }
        else {
            $alias = strtolower($alias);
            $alias = JApplication::stringURLSafe($alias);
            return $alias;
        }
    }

	public static function getHotelLink($hotel){
		$itemidS = self::getItemIdS();
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

	

		$url = JRoute::_('index.php?option=com_jhotelreservation&view=hotel&hotel_id='.$hotel->hotel_id.$itemidS,false);

		if($appSettings->enable_seo) {
			
			$conf = JFactory::getConfig();
			$index ="";
			
			if(!JFactory::getConfig()->get("sef_rewrite")){
				$index ="index.php/";
			}
			$menuAlias = self::getCurrentMenuAlias();
			$lang               = JFactory::getLanguage()->getTag();
			$lang               = explode( "-", $lang );

			$hotel->hotel_alias = ! empty( $hotel->hotel_alias ) ? $hotel->hotel_alias : '';
			$hotelAlias         = stripslashes( strtolower( $hotel->hotel_alias ) );
			//$hotelAlias         = htmlentities( urlencode( $hotelAlias ) );
			$url                = JURI::base() . $index . $lang[0].'/'.$menuAlias . "/hotel-" . $hotelAlias;
		}

		return $url;
	}

	public static function getHotelPoiLink($poi,$hotelId = ''){
		$itemidS = self::getItemIdS();
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();


		$hotel = '';
		$hotelUrl = '';
		if(isset($hotelId) && !empty($hotelId)){
			$hotel = '&hotelId='.$hotelId;
			$hotelUrl = $hotelId."-";
		}

		$url = JRoute::_('index.php?option=com_jhotelreservation&view=poi'.$hotel.'&poid='.$poi->id,false);
//
		if($appSettings->enable_seo) {

			$conf = JFactory::getConfig();
			$index ="";

			if(!JFactory::getConfig()->get("sef_rewrite")){
				$index ="index.php/";
			}
			$menuAlias = self::getCurrentMenuAlias();
			$menu= '';
			if(!empty($menuAlias)){
				$menu = '/'.$menuAlias;
			}
			$lang               = JFactory::getLanguage()->getTag();
			$lang               = explode( "-", $lang );

			$poi->name       = ! empty( $poi->name ) ? $poi->name : '';
			$poiName         = stripslashes( strtolower(str_replace(" ", "", $poi->name) ));
			$url             = JUri::base() . $index . $lang[0].$menu."/poi-".$hotelUrl. $poiName;
		}

		return $url;
	}

	/**
	 * Get the current menu alias
	 */
	static function getCurrentMenuAlias(){
		$menualias =  "";

		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

		$currentMenu = null;
		if(!empty($appSettings->menu_item_id)){
			$currentMenu = JFactory::getApplication()->getMenu()->getItem($appSettings->menu_id);
		}

		if(empty($currentMenu)){
			$currentMenu = JFactory::getApplication()->getMenu()->getActive();
		}

		if(!empty($currentMenu))
			$menualias = $currentMenu->alias;

		return $menualias;
	}
	
	public static function getRoomLink($hotel){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		
		$conf = JFactory::getConfig();
		$index ="";
		
		$hotelName = stripslashes(strtolower($hotel->hotel_name));
		$hotelName = str_replace(" ", "-", $hotelName);
	
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
		$lang = JFactory::getLanguage()->getTag();
		$lang = explode("-",$lang);
	
		$url = JURI::base().$index.$lang[0]."/hotel-".$hotelName."?rm_id=".$hotel->room_id;
		
		return $url;
	}
	
	public static function getExcursionLink($hotel){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		
		$conf = JFactory::getConfig();
		$index ="";
		
		if(!JFactory::getConfig()->get("sef_rewrite")){
			$index ="index.php/";
		}
		
		$hotelName = stripslashes(strtolower($hotel->hotel_name));
		$hotelName = str_replace(" ", "-", $hotelName);
	
		$url = JURI::base().$index."hotel-".$hotelName."?rm_id=".$hotel->id;
	
		return $url;
	}
	
	public static function getOfferLink2($offer, $mediaReferer=null, $voucher=null){
	
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		$containsIndex = strpos($path, "index.php");
		$path = substr($path, 0, strpos($path,"/"));
	
		$offerName = stripslashes(strtolower($offer->offer_name));
		$offerName = str_replace(" ", "-", $offerName);
		if($containsIndex!==false){
			$url = "index.php/"."hotelarrangement-".$offerName;
		}
		else
			$url = ""."hotelarrangement-".$offerName;
		//$url = $current."/".$path."/hotelarrangement-".$offerName;
		//$url = $current."/hotelarrangement-".$offerName;
	
		if(!empty($mediaReferer) || !empty($voucher)){
			$url = $url."?";
			$isMediaSet = false;
			if(!empty($mediaReferer)){
				$url.="mediaReferer=".$mediaReferer;
				$isMediaSet = true;
			}
	
			if(!empty($voucher)){
				if($isMediaSet){
					$url .= "&";
				}
				$url.="voucher=".$voucher;
			}
		}
		return $url;
	}
	
	public static function getOfferLink($offer, $mediaReferer=null, $voucher=null){

		$itemidS = self::getItemIdS();
		$uri     = JURI::getInstance();
		$current = $uri->toString( array('scheme', 'host', 'port'));
		$path =$uri->toString( array('path'));
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();
		if(strpos($path, "/")==0)
			$path = substr($path, 1);
		$containsIndex = strpos($path, "index.php");
		$path = substr($path, 0, strpos($path,"/"));
	
		$city = stripslashes(strtolower($offer->hotel_city));
		$city = str_replace(" ", "-", $city);


		$url = JRoute::_('index.php?option=com_jhotelreservation&view=offer&offerId='.$offer->offer_id.$itemidS);

		if($appSettings->enable_seo)
		{
			if ( $containsIndex !== false )
			{
				$url = "index.php/" . "hotelarrangement-" . $city . "-" . $offer->offer_id;
			}
			else
			{
				$url = "" . "hotelarrangement-" . $city . "-" . $offer->offer_id;
			}
			//$url = $current."/hotelarrangement-".$city."-".$offer->offer_id;
			if ( ! empty( $mediaReferer ) || ! empty( $voucher ) )
			{
				$url        = $url . "?";
				$isMediaSet = false;
				if ( ! empty( $mediaReferer ) )
				{
					$url .= "mediaReferer=" . $mediaReferer;
					$isMediaSet = true;
				}

				if ( ! empty( $voucher ) )
				{
					if ( $isMediaSet )
					{
						$url .= "&";
					}
					$url .= "voucher=" . $voucher;
				}
			}
		}
	
		return $url;
	}
	
	public static function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit) {
	
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);
	
		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
	
	public static function getNumberOfDays($startData, $endDate){
	
		$dStart = new DateTime($startData);
		$dEnd  = new DateTime( $endDate);
		$dDiff = $dStart->diff($dEnd);
  
		return $dDiff->days;
	}
	
	public static function includeFile($type, $file, $path){
		$version = new JVersion();
		$versionA =  explode(".", $version->getShortVersion());
		if($versionA[0] =="3"){
			JHTML::_($type, $path.$file);
		}else{
			JHTML::_($type, $file, $path);
		}
	}

    public static  function  getLanguageTag(){
        $language = JFactory::getLanguage();
        $language_tag = $language->getTag();
        return $language_tag;
    }
	
	public static function shiftDate($date,$nrDays){
		$d = strtotime( date('Y-m-d', strtotime($date)).' + '.$nrDays.' day ' );
		return date('Y-m-d', $d);
	}
	public static function shiftDateDown($date,$nrDays){
		$d = strtotime( date('Y-m-d', strtotime($date)).' - '.$nrDays.' day ' );
		return date('Y-m-d', $d);
	}
	public static function setExtensionMenuId($appSettings){
	
		//setting menu item Id
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		
		$menu = $app->getMenu();
		$activeMenu = $app->getMenu()->getActive();
		
		if (!empty($activeMenu) && $activeMenu != $menu->getDefault($language_tag)) {
			$menuId = $activeMenu->id;
			$session->set('menuId', $menuId);
		}
		
		$menuId = $session->get('menuId');
		
		if(!empty($appSettings->menu_id) && ($menuId == $menu->getDefault($language_tag)->id || empty($menuId))){
			$menuId = $appSettings->menu_id;
		}
		
		if(!empty($menuId)){
			JFactory::getApplication()->getMenu()->setActive($menuId);
		}
	}

    /**
     * @return array of jhotel reservataion languages which are  joomlas installed languages
     */
    public static function languageTabs()
    {
        $jhotelLanguagesPath = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
        //Returns an array
        $dirs = JFolder::folders($jhotelLanguagesPath);
        // dmp($dirs);
        sort($dirs);

        $joomlaLanguagesPath = JLanguage::getLanguagePath(JPATH_ADMINISTRATOR);
        //Returns an array
        $joomlaDirs = JFolder::folders($joomlaLanguagesPath);
        // dmp($dirs);
        sort($joomlaDirs);

        $language = array();
        foreach($dirs as $jhotelLang){
            if(in_array($jhotelLang,$joomlaDirs)){
                $language[] = $jhotelLang;
            }
        }
        return $language;
    }

    /**
     * @param $languageTag language tag  like vn-VN
     * @return Returns a sting with a name of a languge based on its language tag $languageTag
     */
    public static  function languageNameTabs($languageTag){

        //Active Language type Array
        $activeLanguages = self::languageTabs();

            if(in_array($languageTag,$activeLanguages)) {
                $path = JLanguage::getLanguagePath(JPATH_ROOT);
                $xmlFiles = JFolder::files($path.DS.$languageTag,'^([-_A-Za-z]*)\.xml$');
                $xmlFile = reset($xmlFiles);

                $data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$languageTag.DS.$xmlFile);

                return $data['name'] !=''? $data['name']:$languageTag;
            }
        return $languageTag;
    }

    /**
     * @param $translatedValue
     * @param $value_id
     * @param $value_name
     * @return mixed
     */
    public static function printTranslatedValues($translatedValue,$value_id,$value_name){
       foreach ($translatedValue as $k=>$rnt) {
          if($rnt->object_id == $value_id) {
               return $rnt->content;
          }
       }
       
       return $value_name;
    }

    public static function getHotel($hotelId){
		$db		=JFactory::getDBO();
        $query = "select h.*,c.*,c2.description as hotel_currency 
        		  from #__hotelreservation_hotels h
 				  left join #__hotelreservation_countries c on h.country_id=c.country_id
        		  left join #__hotelreservation_currencies 	c2 using (currency_id)
				  where  h.hotel_id=".$hotelId;
        $db->setQuery( $query );
        $hotel =  $db->loadObject();
        return $hotel;
    }

    static function getCurrentLanguageCode(){
    	$lang = JFactory::getLanguage()->getTag();
    	$lang = explode("-",$lang);
    	return $lang[0];
    }
    
    //get 2 letter language code out default language
    static function getLanguageCode(){
    	$lang = JFactory::getLanguage()->getTag();
    	$lang = explode("-",$lang);
    	return $lang[1];
    }
    
    static public function includeValidation(){
    	$tag = JHotelUtil::getCurrentLanguageCode();
    	 
    	if(!file_exists(JPATH_COMPONENT_SITE.'/assets/js/validation/js/languages/jquery.validationEngine-'.$tag.'.js'))
    		$tag ="en";
    	
		JHTML::_('stylesheet', 'components/'.getBookingExtName().'/assets/js/validation/css/validationEngine.jquery.css' );
		JHTML::_('script','components/'.getBookingExtName().'/assets/js/validation/js/languages/jquery.validationEngine-'.$tag.'.js');
		JHTML::_('script','components/'.getBookingExtName().'/assets/js/validation/js/jquery.validationEngine.js');

    }

    /**
     * @param $roomOffer
     * @param $room
     * @return string
     */
    static public function discountPricePercentage($roomOffer,$room)
    {
        if ($room > 0) {
            return ceil(100 - (($roomOffer * 100) / $room));
        }
    }

    // The ago time function
    public static function convertTimestampToAgo($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * Method to get the guest details information settings with 3 option to select
     * hidden , optional and mandatory
     * @return array of configuration attribute objects
     */
    public static function getAttributeConfiguration(){

        //array list containing an object for each of the 3 status attributes
        $states = array();

        //simple object representing the type of configuration
        //its text label and its constant value
        $state = new stdClass();
        $state->value = JHP_HIDDEN_FIELD;
        $state->text = JTEXT::_("LNG_NOT_SHOW");
        $states[] = $state;
        $state = new stdClass();
        $state->value = JHP_OPTIONAL_FIELD;
        $state->text = JTEXT::_("LNG_OPTIONAL");
        $states[] = $state;
        $state = new stdClass();
        $state->value = JHP_MANDATORY_FIELD;
        $state->text = JTEXT::_("LNG_MANDATORY");
        $states[] = $state;

        return $states;
    }

    /**
     * @param $text
     * @return mixed|string
     */
    public static function formatDescriptionForDisplay($text,$numberOfChars,$htmlTags= true){
        $text = strip_tags($text);
        $text=preg_replace('/([\r\n\t])/',' ', $text);
        $text = self::truncate($text, $numberOfChars, $htmlTags);

        return !empty($text)?$text.'...':$text;
    }

    /**
     * @param $filename the original name of the uploaded file from the user's computer.
     * @return string the formatted name(without special chars) of the uploaded file from the user's computer
     */
    public static function removeSpecialChars($filename)
    {
        if(isset($filename) && !empty($filename)) {
            // replace special characters from the filename of the
            // client side uploaded image
            $result = preg_replace('/[^.a-zA-Z0-9-_]/', '', $filename);
            return (string)utf8_encode((string)$result);
        }
    }
    public static function generateMapInfoContent($hotels)
    {
    	ob_start();
    	$db = JFactory::getDBO();
    	$index = 1;
    	foreach($hotels as $hotel){
    		if(empty($hotel->hotelDescription))
    			$hotel->hotelDescription = "";
    		
    		$hotel->hotelDescription = empty($hotel->hotel_description)?$hotel->hotelDescription:$hotel->hotel_description;
    		$description = $db->escape($hotel->hotelDescription);
    		$description = "<div>".JHotelUtil::truncate($description,100)."</div>";
    	
    		$marker =JURI::base() ."/components/com_jhotelreservation/assets/img/newmarker.png";
    		$markerNearBy = JURI::base() .'/components/com_jhotelreservation/assets/img/marker_blue.png';
    		$starsImg = JURI::base() ."administrator/components/".getBookingExtName()."/assets/img/star.png";
    	
    		$starsContent = "";
    		for ($i=1;$i<=$hotel->hotel_stars;$i++){
    			$starsContent .='<img  src="'.$starsImg.'"/>';
    		}
    		$ratingContent = empty($hotel->hotel_rating_score)?"":JHotelUtil::fmt($hotel->hotel_rating_score,1);
    		if(!empty($ratingContent)){
    			$ratingContent = '<br><div class="clearHotel">'.JText::_("LNG_RATING"). ":<b> ".$ratingContent."</b></div>";
    			$ratingContent .= JText::_("LNG_REVIEWS_DESCRIPTION_TEXT_1")." <b>".JText::_("LNG_REVIEWS_DESCRIPTION_TEXT_2")."</b>";
    		}
    	
    		$nearBy = isset($hotel->nearBy) &&  $hotel->nearBy==1?true:false;
    		$markerHotel = $nearBy==true?$markerNearBy:$marker;
    		$hotelNear = $nearBy==true?1:0;
    		$contentPhone = !empty($hotel->hotel_phone)?'<div class="info-phone"><i class="fa fa-phone"> </i>'.$db->escape($hotel->hotel_phone).'</div>':"";
    		$contentPhone = "+31 (0)85 489 90 19";
    		$contentString = '<div class="info-box">'.
    				'<div class="info-box-image">'.
    				'<img style="max-width:100% !important;" src="'.JURI::root().PATH_PICTURES.$hotel->hotel_picture_path.'" alt="'.$db->escape($hotel->hotel_name).'">'.
    				'</div>'.
    				'<div class="title hotelName">'.$db->escape($hotel->hotel_name).'<br>'.$starsContent.'</div>'.
    				'<div class="info-box-content">'.
    				'<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">'.$db->escape(($hotel->hotel_address)).'</div>'.
    				$contentPhone.$description.$ratingContent.
    				'<a class="ui-hotel-button right" href="'.$db->escape(JHotelUtil::getHotelLink($hotel)).'"> '.JText::_('LNG_BOOK_HOTEL',true).'</a>'.
    				'</div>'.
    	
    				'</div>';
    		echo "['".htmlspecialchars($hotel->hotel_name, ENT_QUOTES)."', \"$hotel->hotel_latitude\",\"$hotel->hotel_longitude\", 4,'".$contentString."','".$index."','".$hotelNear."','".$markerHotel."'],"."\n";
    	
    		$index++;
    	}
    	$content = ob_get_contents();
    	ob_end_clean();
	   	return $content;

    }

    public static function setLocateLanguage(){
    	$appSettings= self::getApplicationSettings();
    	$language = JFactory::getLanguage();
    	$language_tag = $language->getTag();
    	
    	$language_tag = str_replace("-","_",$language->getTag());
    	setlocale(LC_TIME , $language_tag.'.UTF-8');
    }
    
    /**
     * @param $fromdate
     * @param $todate
     * @return DatePeriod
     */
    public static function generateIntervalDates($fromdate,$todate){
   		self::setLocateLanguage();
    	
        $fromdate = self::convertToMysqlFormat($fromdate);
        $todate = self::convertToMysqlFormat($todate);

        //$days = $end_date - $start_date/86400; //
        $fromdate = \DateTime::createFromFormat('Y-m-d', $fromdate);
        $todate = \DateTime::createFromFormat('Y-m-d', $todate);
        

        $dateIntervals =  new \DatePeriod(
            $fromdate,
            new \DateInterval('P1D'),
            $todate->modify('+1 day')
        );

        return $dateIntervals;
    }


    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param $unit
     * @return float the distance from 2 geolocation points
     */
    public static function distance($lat1, $lon1, $lat2, $lon2) {

        $theta = (float)$lon1 - (float)$lon2;

        $dist = sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2)) +  cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * cos(deg2rad((float)$theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);

        $kilometers = $dist * 60 * 1.1515;

        // only kilometers
        return ($kilometers * 1.609344);
//        if ($unit == "K") {
//        } else if ($unit == "N") {
//            return ($miles * 0.8684);
//        } else {
//            return $miles;
//        }
    }

	/**
	 * @param $picturePathValue
	 * @param string $delimiter
	 *
	 * @return mixed
	 */
	public static function setAltAttribute($picturePathValue,$delimiter = DS) {
		$imageName = '';
		if ( isset( $picturePathValue ) ) {
			$value    = explode( $delimiter, $picturePathValue );
			$fileName = count( $value ) - 1;
			if ( $fileName != - 1 ) {
				$load = $value[ $fileName ];
				$imageName = explode(".",$load);
				$imageName = $imageName[0];
			}
		}
		return $imageName;
	}

	static function getItemIdS() {
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$menu = $app->getMenu();
		$itemid="";
		$appSettings = JHotelUtil::getInstance()->getApplicationSettings();

		$activeMenu = JFactory::getApplication()->getMenu()->getActive();
		if(isset($activeMenu)){
			$itemid= JFactory::getApplication()->getMenu()->getActive()->id;
		}

		$defaultMenu = $menu->getDefault($lang->getTag());
		if(!empty($defaultMenu) && $itemid == $defaultMenu->id){
			$itemid = "";
		}

		if(empty($itemid) && !empty($appSettings->menu_id)){
			$itemid = $appSettings->menu_id;
		}
		

		$itemidS="";
		if(!empty($itemid)){
			$itemidS = '&Itemid='.$itemid;
		}
		return $itemidS;
	}
	public static function getTabIndex()
	{
		$defaultLanguage = JComponentHelper::getParams('com_languages')->get('site');
		$dirs = JHotelUtil::languageTabs();
		foreach( $dirs  as $ixd=>$_lng ){
			if($defaultLanguage == $_lng )
				return $ixd;
		}
	}
    
    //check if current page is home page
    public static function isHomePage()
    {
    	$app = JFactory::getApplication();
	    $language = JFactory::getLanguage();
	    $language_tag = $language->getTag();
	    
	    $menu = $app->getMenu();
	    $activeMenu = $app->getMenu()->getActive();
	    
	    if (!empty($activeMenu) && $activeMenu == $menu->getDefault($language_tag)) {
	    	return true;
   		}
   		return false;
    }


	/**
	 * @param $itemName
	 *
	 * @return string
	 *
	 * @since version
	 */
	public static function getTranslatedItemName($itemName){

		if(false !== strpos($itemName,'LNG_')) {
			return JText::_($itemName);
		} else {
			$languagePaymentOptions = JText::_( 'LNG_' . strtoupper( str_replace( " ", "_", $itemName ) ) );
			return $languagePaymentOptions;
		}
	}
	

	public static function formatToDefaultDate($dateString){
		$app = self::getApplicationSettings();
		
		$dateFormat = $app->dateFormat;
		$formatedIgnoredDates = '';
		if(isset($dateString) && !empty($dateString)){
			$dates = explode(",",$dateString);
			$result = array();
			foreach($dates as $date){
				if($date=="NaN-NaN-NaN")
					continue;
				$dateObj = new DateTime($date);
				$formatedDate = $dateObj->format($dateFormat);
				$result[] = $formatedDate;
			}
		
			$formatedIgnoredDates = implode(",",$result);
		}
		
		return $formatedIgnoredDates;
		
	}
	
	function validateDate($date)
	{
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}
}


?>
