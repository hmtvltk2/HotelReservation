<?php

class UserService{
	
	public static function isUserLoggedIn($retunUrl=null){
		
		$user = JFactory::getUser();
		if(!$user->id){
			$app = JFactory::getApplication();
			$msg =  JText::_('LNG_UNAUTHORIZED_ACCESS',true);
			if($retunUrl==null)
				$retunUrl = base64_encode('index.php');
			$retunUrl = base64_encode($retunUrl);
			$app->redirect( JRoute::_( "index.php?option=com_users&return=".$retunUrl), $msg );
		}
		else
			return true;
	}

	public static function getUserByEmail($email){
		$usersTable = JTable::getInstance('Users','Table', array());
		return $usersTable->getUserByEmail($email);
	}

	/**
	 * @param $hotelId
	 *
	 * @return bool if the rooms part of a hotel has a cubilis channel return true
	 *
	 * @since version jhotel portal 6.0.1
	 */
	public static function isChannelManagerSet($hotelId, $type){
		$hotelTable =JTable::getInstance("Hotels","Table", array());
		$channelManagers = $hotelTable->getChannelManagers($hotelId);
		$hasChannelSet = false;
		
		if(!empty($channelManagers[$type]->user) && !empty( $channelManagers[$type]->password)){
			$hasChannelSet = true;
		}


		return $hasChannelSet;
	}
	
	public static function generatePassword($text, $is_cripted = false)
	{
		$password 	=  $text;   
		if( $is_cripted ==false )
			return $password;
		jimport('joomla.user.helper');
		$salt 		= JUserHelper::genRandomPassword(8);  
		$crypt 		= JUserHelper::getCryptedPassword($password, $salt);  
		$password 	= $salt;  
		return $password;
	}
	public static function logOutUser($userId)
	{
		$user = JFactory::getUser();
		if(!empty($userId)){
			$usersTable = JTable::getInstance('Users','Table', array());
			$usersTable->load($userId);
			$usersTable->lastvisitDate = '0000-00-00 00:00:00';//otherwise won't activate the user
			$usersTable->store();
			
		}
	}
	
	/**
	 *   Get any component's model
	 **/
	public static function getModel($name, $path = JPATH_COMPONENT_ADMINISTRATOR, $component = 'yourcomponentname')
	{
		// load some joomla helpers
		JLoader::import('joomla.application.component.model');
		// load the model file
		JLoader::import( $name, $path . '/models' );
		// return instance
		return JModelLegacy::getInstance( $name, $component.'Model' );
	}
	
	/**
	 * Greate user and update given table
	 */
	public static function createJoomlaUser($new)
	{
		// load the user registration model
		$model = self::getModel('registration', JPATH_ROOT. '/components/com_users', 'Users');
		if (JLanguageMultilang::isEnabled())
		{
			JForm::addFormPath(JPATH_ROOT . '/components/com_users/models/forms');
			JForm::addFieldPath(JPATH_ROOT . '/components/com_users/models/fields');
		}
		// lineup new user data
		$data = array(
				'username' => $new['username'],
				'name' => $new['name'],
				'email1' => $new['email'],
				'password1' => $new['password'], // First password field
				'password2' => $new['password2'], // Confirm password field
				'block' => 0 );
		// register the new user
		$userId = $model->register($data);
		
		// if user is created
		if ($userId > 0)
		{
			return $userId;
		}
		return $model->getError();
	}
	
	//retrieve group id based on name
	function getJoomlaGroupIdByName($groupName){
		$db = JFactory::getDBO();
		$db->setQuery($db->getQuery(true)
				->select('*')
				->from("#__usergroups")
		);
		$groups = $db->loadRowList();
		foreach ($groups as $group) {
			if (strtolower($group[4]) == strtolower($groupName)) // $group[4] holds the name of current group
				return $group[0];        // $group[0] holds group ID
		}
		return 0; // return false if group name not found
	}
	
}