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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JHotelReservationControllerMyTools extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
		$this->registerTask( 'convertLng', 'convertLng');  
		$data = JRequest::get( 'get' );
		if( isset($data['task']) && $data['task'] =='convertLng')
		{
			$this->convertLng($data);
			exit;
		}
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('tools');

		$post = JRequest::get( 'post' );
		
		if ($model->store($post)) 
		{
			$msg = JText::_('LNG_TAX_SAVED',true);
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&view=taxes', $msg );
		} 
		else 
		{
			$msg = "";
			JError::raiseWarning( 500, JText::_('LNG_ERROR_SAVING_TAX',true));
			$this->setRedirect( 'index.php?option='.getBookingExtName().'&view=taxes', $msg );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		
		
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		
	}
	
	function delete()
	{
		
	}
	
	function edit()
	{
		parent::display(); 
	}
	
	function convertLng($data)
	{
		session_cache_expire(1800000); 
		
		$path = JLanguage::getLanguagePath(JPATH_COMPONENT_ADMINISTRATOR).DS.$data['lng'].DS.'*.ini';
		$dirs = glob($path);
		foreach( $dirs as $file )
		{
			header("Content-type: application/csv");  
			header("Content-Disposition: attachment; filename=".basename($file));  
			header("Pragma: no-cache");  
			header("Expires: 0");  
			
			$arr = file( $file );
			foreach( $arr as $line )
			{
				$line = str_replace('"', "'", $line );
				// dmp($line);	
				$ex = explode( "=", $line );
				// dmp($ex);
				if( count($ex) > 1 )
				{
					echo $ex[0]."=";
					if( count($ex)>2)
					{
						unset($ex[0]);
						$str  = '"'.implode("=",$ex);
						$str = str_replace("\r\n", "\"\r\n", $str);
						echo $str;
					}
					else
					{
						$str  	= '"'.$ex[1];
						$str 	= str_replace("\r\n", "\"\r\n", $str);
						echo $str;
					}
				}
			}
		}
	}

}