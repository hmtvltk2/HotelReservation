<?php

defined('_JEXEC') or die('Restricted Access');

jimport( 'joomla.plugin.plugin' );

class plgSystemHotelGallery extends JPlugin
{
		public function __construct( &$subject, $config )
		{
		parent::__construct( $subject, $config );
		}
	
    function onAfterRender()
    {
    //check if component is installed.	   
    if (!file_exists(JPATH_SITE.'/administrator/components/com_jhotelreservation/helpers/utils.php'))
    		return;
    // return if in administrator
    $app = JFactory::getApplication();
		if($app->isAdmin()) return;	

    $input = $output = JResponse::getBody();
    $bparts = explode('<body',$input);
    
    if (count($bparts)>1)
    	{
    	$before = $bparts[0];
    	$input = '<body';
    	for($c=1; $c < count($bparts); $c++) $input .= $bparts[$c];
    	$output = $input;
    	}	
    	
    if (preg_match_all("#{(.*?)}#s", $input, $matches) > 0)      // any plugins?
    	{
		foreach ($matches[0] as $match)                             // loop through all plugins
			{	
			$parts = explode('|',trim($match,'{}'));
  			if ($parts[0]=='hotelgallery' || $parts[0]=='roomgallery')  // found a match!
  				{
	 				$pluginRoot = JURI::root()."/plugins/system/hotelgallery";
  					$id = $parts[1];
  					$picturePath="";
  					
  					$db = JFactory::getDBO();
  					
  					switch($parts[0]){
  						case "hotelgallery":
  							$db->setQuery("SELECT * FROM `#__hotelreservation_hotel_pictures` WHERE hotel_picture_enable=1 and hotel_id=$id");
  							$picturePath = "hotel_picture_path";
  							break;
  						case "roomgallery":
  							$db->setQuery("SELECT * FROM `#__hotelreservation_rooms_pictures` WHERE room_id=$id");
  							$picturePath = "room_picture_path";
  							break;
  						default:		
  							$db->setQuery("SELECT * FROM `#__hotelreservation_hotel_pictures` WHERE hotel_picture_enable=1 and hotel_id=$id");
  							break;
  					}
  					$pictures= $db->loadObjectList();
  					ob_start();
?>
		<div class="gamma-container gamma-loading" id="gamma-container">
	
		    <ul class="gamma-gallery">
		 
  					<?php
  					 foreach( $pictures as $index=>$picture ){
  						$picture->hotel_picture_path = JURI::root() .PATH_PICTURES.$picture->{$picturePath};
					?>
			        <li>
			            <div data-alt="img01" data-description="<h3><?php echo $picture->hotel_picture_info;?></h3>" data-max-width="1800" data-max-height="2400">
			                <div data-src="<?php echo $picture->hotel_picture_path;?>" data-min-width="1300"></div>
			                <div data-src="<?php echo $picture->hotel_picture_path;?>" data-min-width="1000"></div>
			                <div data-src="<?php echo $picture->hotel_picture_path;?>" data-min-width="700"></div>
			                <div data-src="<?php echo $picture->hotel_picture_path;?>" data-min-width="300"></div>
			                <div data-src="<?php echo $picture->hotel_picture_path;?>" data-min-width="200"></div>
			                <div data-src="<?php echo $picture->hotel_picture_path;?>" data-min-width="140"></div>
			                <div data-src="<?php echo $picture->hotel_picture_path;?>"></div>
			                <noscript>
			                    <img src="<?php echo $picture->hotel_picture_path;?>" alt="img01"/>
			                </noscript>
			            </div>
			        </li>
		        <?php } ?>
		    </ul>
		 
		    <div class="gamma-overlay"></div>
		 
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="<?php echo $pluginRoot;?>/gallery/js/modernizr.custom.70736.js"></script>
		<script src="<?php echo $pluginRoot;?>/gallery/js/gamma.js"></script>
		<script src="<?php echo $pluginRoot;?>/gallery/js/jquery.masonry.min.js"></script>
		<script src="<?php echo $pluginRoot;?>/gallery/js/jquery.history.js"></script>
		<script src="<?php echo $pluginRoot;?>/gallery/js/js-url.min.js"></script>
		<script src="<?php echo $pluginRoot;?>/gallery/js/jquerypp.custom.js"></script>
		<link rel="stylesheet" type="text/css" href="<?php echo $pluginRoot;?>/gallery/css/style.css"/>

		<script type="text/javascript">

				$ = jQuery.noConflict( true );
				$(function() {

				var GammaSettings = {
						// order is important!
						viewport : [ {
							width : 1200,
							columns : 5
						}, {
							width : 900,
							columns : 4
						}, {
							width : 500,
							columns : 3
						}, { 
							width : 320,
							columns : 2
						}, { 
							width : 0,
							columns : 2
						} ]
				};

				Gamma.init( GammaSettings, fncallback );
				function fncallback() {
				}

			});
				
		</script>	
		
		<?php			
				$buff = ob_get_contents();
				ob_end_clean();
				$output	= str_replace($match,$buff,$output);
   				}
    		}
    	}
		if ($input != $output) JResponse::setBody($before . $output);
		return true;
		}
}

?>