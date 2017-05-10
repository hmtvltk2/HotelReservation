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

class JHotelReservationModelTranslationTool extends JModelItem
{ 
	function __construct()
	{
		parent::__construct();
	}

	//get translation using google api 
	public function getTranslation($lngTo = 'hr', $lngFrom = 'en' , $word = "word",$apiKey = ''){
		try{
				$url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($word) . '&source='.rawurlencode($lngFrom).'&target='.rawurlencode($lngTo);
				$timeout = 0;
				$handle = curl_init($url);
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1");
				curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				$response = curl_exec($handle);
				$responseDecoded = json_decode($response, true);
				curl_close($handle);

				$result = '';
				if(isset($responseDecoded['data']))
					$result = $responseDecoded['data']['translations'][0]['translatedText'];
				dmp($result);
				return  $result;
		}
		catch(Exception $e){
			print_r($e);exit;
		}
	}


	/**
	 *
	 * @return array
	 *
	 * @since version
	 */
	//get current languages in the extension
	function getLanguages(){
		$jhotelLanguagesPath = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR);
		$dirs = JFolder::folders($jhotelLanguagesPath);
		sort($dirs);
		$languages = array();
		foreach ($dirs as $dir)
		{
			if(strlen($dir) != 5) continue;
			$oneLanguage = new stdClass();
			$oneLanguage->language 	=	 $dir;
			$oneLanguage->name = JHotelUtil::languageNameTabs($dir);
			$tag = explode('-', $dir);
			$oneLanguage->tag = $tag[0];

			$languages[] = $oneLanguage;
		}

		return $languages;
	}

	/**
	 * @param $post
	 *
	 *
	 * @since version
	 */
	public function prepareLanguages($post){

		$allLanguages = $this->getLanguages();
		$languages = array();
		/**
		 * if the user has selected to translate to all languages the other selection will be ignored
		 * new array of language tags will be used instead the one from $POST
		 *Notice: Undefined index: data in /var/www/jhotelia/administrator/components/com_jhotelreservation/models/translationtool.php on line 41

		 */
			foreach ($allLanguages as $k => $item) {
				if ( in_array( $item->tag, $post["languages"] ) ) {
					$languages[] = $item;
				} elseif ( in_array( '-1', $post['languages'] ) && $post["sourceLanguage"]!=$item->tag) {//all languages, exclude source language 				
					$languages[] = $item;
				}
			}
		return $languages;

	}

	/**
	 * @param $post
	 *
	 *
	 * @since version for new values only
	 */
	public function translate($post){
	
		if(!empty($post['textToTranslate']) && isset($post['languages']))
		{
			/**
			 * default array from the use selection
			 */
				
			$languages = $this->prepareLanguages($post);

				
			$sourceLanguage = $post['sourceLanguage'];
			$apiKey         = $post['apiKey'];
			$textToTranslate           = (string)$post['textToTranslate'];
			$result = '';
			
			foreach ($languages as $item){

				$value = $this->getTranslation($item->tag,$sourceLanguage,$textToTranslate,$apiKey);
				
				//write the translated value in the file
				$value = $this->make_safe_for_utf8_use($value); 
				if($item->tag === $sourceLanguage){
					$value = $textToTranslate;
				}
				$translatedValue = htmlspecialchars_decode($value).PHP_EOL;
				$translatedValue = str_replace("LNG_", PHP_EOL."LNG_", $translatedValue);
				
				$internalLocation = JPATH_COMPONENT_ADMINISTRATOR.DS.'language';
				$result .= $this->writeValueToFile($item,$internalLocation,$translatedValue);
			}
			return $result;
		}
	}


	/**
	 * @param $item
	 * @param $fileLocation
	 * @param $translatedValue
	 * @param $translationWithPrefix
	 * @param string $color1
	 * @param string $color2
	 *
	 * @return string
	 *
	 * @since version
	 */
	private function writeValueToFile($item,$fileLocation,$translatedValue,$color1 = '#2c9810',$color2 = '#5c9829'){

		$filePath = $fileLocation.DS.$item->language.DS.$item->language.'.'.getBookingExtName().'.ini';

		$this->fileAppend($filePath,$translatedValue);

		$result = "Value <b style='color:".$color1."'>".$translatedValue." </b> is written in the file: <b style='color: ".$color2."'>".$filePath."</b><br><hr>";

		return $result;
	}

	/**
	 * @param $path
	 * @param $content
	 * @return bool
	 * Function to append/override translation Values
	 */
	protected function fileAppend($path, $content)
	{
		$filePath = fopen($path, 'a+') or die("Could not open the file!");
		// Read/write functions with write/create new file
		fwrite($filePath, $content);
		fclose($filePath);
		return true;
	}

	/**
	 * @param $string
	 * @return string
	 * Function to check and format the content to UTF-8 Encoding
	 */
	private function make_safe_for_utf8_use($string) {
		$encoding = mb_detect_encoding($string, "UTF-8,WINDOWS-1252");

		if ($encoding != 'UTF-8') {
			return iconv($encoding, 'UTF-8//TRANSLIT', $string);
		} else {
			return $string;
		}
	}

}
?>