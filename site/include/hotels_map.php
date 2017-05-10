<script>
var mcOptions = {
    			imagePath: 
    			"<?php echo  JURI::base()."components/com_jhotelreservation/assets/img/" ?>mapcluster/m"
    			};
</script>

<?php
$lang = JFactory::getLanguage()->getTag();
$key= "";
if(!empty($appSettings->google_map_key))
	$key="&key=".$appSettings->google_map_key;

JHtml::_('script', 'https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&language='.$lang.$key );
JHtml::_('script', 'components/com_jhotelreservation/assets/js/markercluster.js');
JHtml::_('script', 'components/com_jhotelreservation/assets/js/hotelsmap.js'); ?>
<script>
    function initialize(tmapId,hotels) {

        var markerImage = '<?php echo JURI::base() ."/components/com_jhotelreservation/assets/img/newmarker.png"?>';
        var marker2Path = "<?php echo JURI::base() .'/components/com_jhotelreservation/assets/img/marker_blue.png'?>";
		var pinImagePath = "https://maps.google.com/mapfiles/kml/shapes/library_maps.png";

        init_map(tmapId, hotels, markerImage, marker2Path, pinImagePath);
    }

    function loadMapScript(id,hotels) {
            initialize(id,hotels);
        }

</script>

