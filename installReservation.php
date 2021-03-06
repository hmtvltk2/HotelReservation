<?php
jimport('joomla.installer.helper');
jimport('joomla.installer.installer');
jimport( 'joomla.filesystem.folder' );

class com_JHotelReservationInstallerScript
{

	function install($parent)
	{
		 
	}
	
	/**
	 * Method to uninstall the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		 
	}
	
	/**
	 * Method to update the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function update($parent)
	{
	
	}
	
	/**
	 * Method to run before an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		 
	}
	
	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		 
		jimport('joomla.installer.helper');
		$basedir = dirname(__FILE__);
		$packageDir = $basedir .'/admin/'.'extensions';
		$extensionsDirs = JFolder::folders($packageDir);
		foreach( $extensionsDirs as $extensionDir)
		{
			$tmpInstaller = new JInstaller();
			$tmpInstaller->setOverwrite(true);
			if(!$tmpInstaller->install($packageDir.'/'.$extensionDir))
			{
				JError::raiseWarning(100,"Extension :". $extensionDir);
			}
		}
		 
		$db = JFactory::getDBO();
		$db->setQuery( " UPDATE #__extensions SET enabled=1 WHERE name='Hotel Url Translator' " );
		$db->query();
		
		$db->setQuery( " UPDATE #__extensions SET enabled=1 WHERE name='Hotel Gallery' " );
		$db->query();
		
		$position = "cpanel" ;
		$name="mod_jhotelreservation_adminlinks";
		$db->setQuery("UPDATE #__modules SET `position`=".$db->quote($position).",`published`='1' WHERE `module`=".$db->quote($name));
		$db->query();
		
		$db->setQuery("SELECT id FROM #__modules WHERE `module` = ".$db->quote($name));
		$id = (int)$db->loadResult();
		
		$db->setQuery("INSERT IGNORE INTO #__modules_menu (`moduleid`,`menuid`) VALUES (".$id.", 0)");
		$db->query();

		$path = JPATH_ADMINISTRATOR . '/components/com_jhotelreservation/help/readme.php';
		include( $path);
	}
}
?>