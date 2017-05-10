<?php 
	defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div id="loading-info" style="display:none;">
	<h1><?php echo JText::_("LNG_LOADING_HEADER")?></h1>
	<h3><?php echo JText::_("LNG_LOADING_INFO")?></h3>

	<div class="loading-image">
		<img src="<?php echo JURI::base()."components/com_jhotelreservation/assets/img/loading2.gif"?>" />
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		jQuery("#userModuleForm").submit(function(event){
		});

	});

	
	function showLoadingAnimation(){
		jQuery.blockUI({ message: jQuery('#loading-info'), centerX: true, css: {width: '80%',top: '15%'} });
		jQuery('.blockUI.blockMsg').center();
	}	
</script>