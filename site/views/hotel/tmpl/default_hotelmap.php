<?php
$hotel =  $this->hotel;
$key= "";
if(!empty($this->appSettings->google_map_key))
	$key="&key=".$this->appSettings->google_map_key;
?>
<div id="hotel_map" class="jhotelmap">
</div>
<script>

	var mapData = {
		'lat': '<?php echo $this->hotel->hotel_latitude?>',
		'long': '<?php echo $this->hotel->hotel_longitude?>',
		'markerIcon': '<?php echo JURI::base() ."/components/".getBookingExtName()?>/assets/img/marker_blue.png',
		'markerTitle': '<?php echo addslashes((string)$this->hotel->hotel_name)?>',
		'mapDivId': 'hotel_map',
		'map_key': '<?php echo $key;?>'
	};
	window.onload = loadJHotelMap(mapData);

</script>