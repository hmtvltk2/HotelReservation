<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * Extras model
 *
 */
class JHotelReservationModelViewedProperties extends JModelItem{


	public function getItems($pk = null){

		$user = JFactory::getUser();

		$table = $this->getTable( 'ViewedProperties', 'JTable' );

		$items = $table->getAllViewedProperties($user->id);

		return $items;

	}

	function getItem($propertyId,$userId){
		$table = $this->getTable( 'ViewedProperties', 'JTable' );

		$item = $table->getUserProperty($propertyId,$userId);

		return $item;
	}
	function saveRecentViewedProperties($post) {

		$table = $this->getTable( 'ViewedProperties', 'JTable' );
		$userData = isset( $_SESSION['userData'] ) ? $_SESSION['userData'] : UserDataService::getUserData();

		$user_properties = $post['properties'];
		$user = JFactory::getUser();

		// dmp($user_properties);


		if ( !empty( $user_properties ))
		{
			foreach ( $user_properties as $user_property )
			{

				$item = $this->getItem($user_property["hotel_id"],$user->id);

				if($item->user_id != $user->id && $item->hotel_id != $user_property["hotel_id"])
				{
					$viewedProperty           = new stdClass();
					$viewedProperty->user_id  = $user->id;
					$viewedProperty->hotel_id = $user_property["hotel_id"];


					if ( ! $table->bind( $viewedProperty ) )
					{
						$this->setError( $table->getError() );

						return false;
					}

					// Check the data.
					if ( ! $table->check() )
					{
						$this->setError( $table->getError() );

						return false;
					}

					if ( ! $table->store() )
					{
						$this->setError( $table->getError() );

						return false;
					}

				}
			}
			echo $this->getResponse();
			exit;
		}
	}

	function getResponse(){
		ob_start();
		?>
		<div class='info-phone'><i class='fa fa-check-circle'></i>Properties Saved!</div>
		<?php
		$buff = ob_get_contents();
		ob_end_clean();
		return $buff;
	}


    public function deleteProperty($propertyId){
	    $user = JFactory::getUser();

        if(isset($propertyId) && $user->id > 0  )
        {
            $table = $this->getTable("ViewedProperties","JTable");
            $table->deleteProperty($propertyId,$user->id);
            exit();
        }
    }

	public function deleteAllProperties(){
		$userData = isset( $_SESSION['userData'] ) ? $_SESSION['userData'] : UserDataService::getUserData();
		$user = JFactory::getUser();

		if(isset($propertyId) && isset($user->id))
		{
			$table = $this->getTable("ViewedProperties","JTable");
			$table->deleteAllProperties($userData->user_properties,$user->id);
			exit();
		}
	}

}
