<?php
require_once('utils.php');
require_once('defines.php');
require_once( '../libraries/class.resizeImage.php');

$_target		= '';
$is_error		= false;

if( !extension_loaded('gd') && !extension_loaded('gd2') )
{
	$p=$n='';
	$i='GD is not loaded !';
	$e=3;
	$is_error		= true;
}

if($is_error==false )
{
	if(
	!isset( $_GET['_target'] )
	||
	$_GET['_target']==''
	||
	!isset( $_GET['_root_app'] )
	||
	$_GET['_root_app']==''
	)
	{
		$p=$n='';
		$i='Invalid params !';
		$e=2;
		$is_error		= true;
	}

	if($is_error==false )
	{
		$resizeImage= true;
		$_root_app	= $_GET['_root_app'];
		$_target	= $_GET['_target'];
		if(isset($_GET['resizeImage'])&&$_GET['resizeImage']==0)
			$resizeImage= false;
		$ex			= array();
		$ex			+= explode('/', $_target);

		if( $_root_app[ strlen( $_root_app )-1 ] != '/' )
		$_root_app .= '/';
		$_target_tmp	= JHotelUtil::makePathFile($_root_app);

		foreach( $ex as $e )
		{
			if( $e == '' )
			continue;
			$dir = $_target_tmp.$e;
			if( !is_dir( $dir ) )
			{
				if( !@mkdir($dir) )
				{
					$p=$n='';
					$i='Error create directory '.$_target_tmp.$e.' !';
					$e=2;
					$is_error		= true;
					echo $i;
					break;
				}

				/*if (is_dir($dir))
				 {
				if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
				echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
				}
				closedir($dh);
				}
				}
				*/

			}
			else
			{
				//dmp('Am '.$dir);
			}

			$_target_tmp.=$e.DIRECTORY_SEPARATOR;
		}

		if( $is_error == false  )
		{
//            var_dump($_FILES['file']['tmp_name']);
            $filename = JHotelUtil::removeSpecialChars($_FILES['file']['name']);
			$_target = $_root_app.$_target.basename($filename);
			$file_tmp = JHotelUtil::makePathFile($_target);
			/* if( is_file($file_tmp) )
			 {
			$p	=	'';
			$n	= 	basename( $file_tmp);
			$i	=	'This file exist !';
			$e	=	1;
			}
			else
			{ */
			//dmp($_FILES['uploadedfile']['name']);


			if(move_uploaded_file($_FILES['file']['tmp_name'], $file_tmp))
			{
				if($resizeImage){
					$image = new Resize_Image;
					$image->ratio		= true;
					
					$ratio = PICTURE_WIDTH/PICTURE_HEIGHT;
					$size = getimagesize($file_tmp);
					$imageRatio = $size[0]/$size[1];
					//set new height or new width depending on image ratio
					if($ratio<$imageRatio){
						if(!empty($_GET['room_picture']) && $_GET['room_picture']==1)
							$image->new_width 	= ROOM_PICTURE_WIDTH;
						else
							$image->new_width 	= PICTURE_WIDTH;
					}
					else{
						if(!empty($_GET['room_picture']) && $_GET['room_picture']==1)
							$image->new_width 	= ROOM_PICTURE_HEIGHT;
						else
							$image->new_height 	= PICTURE_HEIGHT;
					}
					
					$image->image_to_resize = $file_tmp; 	// Full Path to the file
					$image->new_image_name 	= basename($file_tmp);
					$image->save_folder 	= dirname($file_tmp).DIRECTORY_SEPARATOR;
					
					$process 			= $image->resize();
				}
				else{
					$process['result'] = true;
					$image->save_folder 	= dirname($file_tmp).DIRECTORY_SEPARATOR;
				} 


				if($process['result'] && $image->save_folder)
				{
					$p	=	basename( $file_tmp );
					$n	= 	basename( $file_tmp);
					$i	=	$file_tmp;
					$e	=	0;
				}
				else
				{
					unlink($file_tmp);
					$p=$n='';
					$i='Error resize uploaded file';
					$e=4;
				}

					
			}
			else
			{
				$p=$n='';
				$i='Error move uploaded file';
				$e=2;
			}
			//	}
		}
	}
}
ob_clean();
echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<uploads>';
echo '<picture path="'.$p.'" info="'.$i.'" name="'.$n.' " error="'.$e.'" />';
echo '</uploads>';
?>