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
jimport('joomla.filesystem.file');
jimport('joomla.application.component.model');

class JHotelReservationModelLanguage extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();
	}

	function getData()
	{
		// Load the data
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

    /**
     * @param $array1
     * @param $array2
     * @return array
     */
    private function uniqueValues($array1,$array2)
    {
        $finalArray = array();
        if (!empty($array1)) {
            foreach ($array1 as $k => $value) {
                foreach ($array2 as $key => $values) {
                    $value2 = preg_replace('/([\r\n\t])/','', $values);
                    if ($value == $value2) {
                        $finalArray[$key] = $value2;
                    }
                }
            }
        }

        $a = array_diff($array1,$finalArray);
        $b = array_diff($finalArray,$array1);
        $c = array_merge($a,$b);

        return array_unique($c);
    }
    function saveLanguage($code)
    {
        set_time_limit(300);
        $content = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $custom_contentt = JRequest::getVar('custom_content', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $app = JFactory::getApplication();

        if (empty($code)) {
            $app->enqueueMessage(JText::_('Code not specified', true));
            return;
        }

        $path = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$code.DS.$code.'.'.getBookingExtName().'.ini';
        $newPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$code.DS.$code.'-custom.'.getBookingExtName().'.ini';

        if(file_exists($path)) {
            if (!empty($content)){
                $content = $this->make_safe_for_utf8_use($content);

                JFile::write($path,$content);
                $msg = JText::_('LNG_SETTINGS_APPLICATION_SAVED', true);
            }else{
                $msg = JText::_('LNG_LANGUAGE_SUCCESSFULLY_SAVED', true);
            }
        } else {
            $app->enqueueMessage('File not found : '.$path);
            $msg = $app->enqueueMessage(JText::_('LNG_ERROR_SAVING_SETTINGS_APPLICATION', true),'error');
        }


        $formattedContentWithUTF8 = $this->make_safe_for_utf8_use($custom_contentt);

        $contentToSave = preg_split('/([\r\n\t])/', $formattedContentWithUTF8);
        $uniqueValues = array_unique($contentToSave);


        switch(file_exists($newPath)){
            case false:
                if (!empty($custom_contentt) && $custom_contentt!='') {
                    foreach ($uniqueValues as $k => $value) {
                        if($k == 0 ) {
                            $firstValue = $uniqueValues[0];
                            $firstValue = PHP_EOL.$firstValue.PHP_EOL;
                            $this->fileAppend($path,$firstValue);
                        } else if($k  >= 1) {
                            $formatedContent = $value.PHP_EOL;
                            $this->fileAppend($path,$formatedContent);
                        }
                    }
                    JFile::write($newPath,$formattedContentWithUTF8);
                    $msg = JText::_('LNG_SETTINGS_APPLICATION_SAVED', true);
                }
                break;

            case true:
                if (!empty($custom_contentt) && !empty($uniqueValues)) {
                    $fileValues = file($newPath);
                    $array = $this->uniqueValues($uniqueValues,$fileValues);
                    foreach ($array as $k => $value) {
                        if($k == 0 ) {
                            $firstValue = $array[0];
                            $firstValue = PHP_EOL.$firstValue.PHP_EOL;
                            $this->fileAppend($path,$firstValue);
                        } else if($k  >= 1) {
                            $formatedContent = $value.PHP_EOL;
                            $this->fileAppend($path,$formatedContent);
                        }
                    }
                    JFile::write($newPath,$formattedContentWithUTF8);
                    $msg = JText::_('LNG_SETTINGS_APPLICATION_SAVED', true);

                }
                if(empty($custom_contentt) || $custom_contentt==''){
                    $delete = $this->deleteFile($newPath);
                    $msg = JText::_($delete, true);
                }
                break;

            default:
                $app->enqueueMessage('File not found : '.$newPath);
                $msg = $app->enqueueMessage(JText::_('LNG_ERROR_SAVING_SETTINGS_APPLICATION', true),'error');
                break;

        }
        set_time_limit(60);
        return $msg;
    }

    function send_email($code) {

        $subject = "New language proposal for J-HotelReservation -  $code";
        $body = "Hi,<br/><br/>Please find attached the language files for $code language.";
        $to = LANGUAGE_RECEIVING_EMAIL;

        # Invoke JMail Class
        $mailer = JFactory::getMailer();
        $mailer->addRecipient($to);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        $mailer->isHTML(true);

        $languageFile = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$code.DS.$code.'.'.getBookingExtName().'.ini';
        $systemLanguageFile = JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$code.DS.$code.'.'.getBookingExtName().'.sys.ini';

        if (file_exists($languageFile)) { $mailer->addAttachment($languageFile); }
        if (file_exists($systemLanguageFile)) { $mailer->addAttachment($systemLanguageFile); }

        if( $mailer->send() ) {
            $msg = JText::_('LNG_LANGUAGE_FILES_SUCCESSFULLY_SEND', true);
        } else {
            $msg = JText::_('LNG_SOMETHING_WENT_WRONG', true);
        }
        return $msg;
    }

    /**
     * @param $code
     * @return mixed
     */
    function deleteFolder($code) {
        $deleteFolder = JFolder::delete(JPATH_COMPONENT_ADMINISTRATOR.DS.'language'.DS.$code);
        if($deleteFolder) {
            $msg = JText::_('LNG_LANGUAGE_PACK_SUCCESSFULLY_DELETED', true);
        } else {
            $msg = JText::_('LNG_LANGUAGE_PACK_NOT_DELETED', true);
        }
        return $msg;
    }
    /**
     * @param $absolutePath
     * @param $contentt
     * @return bool
     *
     * Method to create or write a file the custom translation content
     */
    protected function fileWriteCreate($absolutePath, $contentt)
    {
        //chmod($absolutePath,0777);
        $newFilePath = fopen($absolutePath, 'w') or die("Could not open or create file!");
        // Read/write functions with write/create new file
        fwrite($newFilePath, $contentt);
        fclose($newFilePath);
        //chmod($absolutePath,0755);
        return true;
    }

    /**
     * @param $path
     * @param $content
     * @return bool
     * Function to append/override translation Values
     */
    protected function fileAppend($path, $content)
    {
        //chmod($path,0777);
        $filePath = fopen($path, 'a+') or die("Could not open the file!");
        // Read/write functions with write/create new file
        fwrite($filePath, $content);
        fclose($filePath);
        //chmod($path,0755);
        return true;
    }

    /**
     * @param $path
     * @return string
     * Method to unlink or delete the file if the content is left empty
     */
    protected function deleteFile($path)
    {
        if(unlink($path)) {
            return 'Custom Language Values deleted';
        }
        else {
            return 'Unable to delete Custom Language Values in file:'.$path;
        }
    }
}
?>