<?php
	$db = JFactory::getDBO();
	
	$db->setQuery('SELECT `id` FROM #__modules WHERE `module` = "mod_jhotelreservation" ');
	
	$id = $db->loadResult();
	if($id)
	{ 
		$installer = new JInstaller; 
		$result = $installer->uninstall('module',$id,1); 
		// $status->plugins[] = array('name'=>'plg_srp','group'=>'system', 'result'=>$result);
	}
	
?>