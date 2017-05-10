<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

<style type="text/css" media="screen">
	.fa {
    display: inline-block;
    font: normal normal normal 14px/1 FontAwesome;
    font-size: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
	#jhotelreservation-wrap .dir-box-icon-wrapper { float:left; display: block !important; width: auto !important; height :auto!important; line-height:12px !important; background: none; }
	#jhotelreservation-wrap .dir-box-icon { text-align:center; margin-right:15px; float:left; margin-bottom:15px; }
	#jhotelreservation-wrap .dir-box-icon a { background-color:#fff; background-position:-30px; display:block; float:left; height:100px; width:100px; color:#565656; vertical-align:middle; text-decoration:none; border:1px solid #CCC; -webkit-border-radius:5px; -moz-border-radius:5px; border-radius:5px; -webkit-transition-property:background-position, 0 0; -moz-transition-property:background-position, 0 0; -webkit-transition-duration:.8s; -moz-transition-duration:.8s; }
	#jhotelreservation-wrap .dir-box-icon a:hover,
	#jhotelreservation-wrap .dir-box-icon a:focus,
	#jhotelreservation-wrap .dir-box-icon a:active { background-position:0; -webkit-border-bottom-left-radius:50% 20px; -moz-border-radius-bottomleft:50% 20px; border-bottom-left-radius:50% 20px; -webkit-box-shadow:-5px 10px 15px rgba(0,0,0,0.25); -moz-box-shadow:-5px 10px 15px rgba(0,0,0,0.25); box-shadow:-5px 10px 15px rgba(0,0,0,0.25); position:relative; z-index:10; }
	#jhotelreservation-wrap .dir-box-icon a span { display:block; text-align:center; line-height: 1.1; }
	
	#jhotelreservation-wrap .dir-box-icon a .fa { margin:10px 0; border-radius: 50%; color: #fff; font-size: 22px; height: 44px; width: 44px; line-height: 44px;}
	#jhotelreservation-wrap .dir-box-icon .fa-gear { background-color: #0180CD; }
	#jhotelreservation-wrap .dir-box-icon .fa-building { background-color: #1abc9c; }
	#jhotelreservation-wrap .dir-box-icon .fa-gift { background-color: #e67e22; }
	#jhotelreservation-wrap .dir-box-icon .fa-calendar { background-color: #536FA5; }
	#jhotelreservation-wrap .dir-box-icon .fa-bed { background-color: #F0C619; }
	#jhotelreservation-wrap .dir-box-icon .fa-bar-chart { background-color: #27ae60; }
	#jhotelreservation-wrap .dir-box-icon .fa-puzzle-piece { background-color: #3498db; }
	#jhotelreservation-wrap .dir-box-icon .fa-comments { background-color: #e74c3c; }
    
</style>
<div class="text">
	Thank you for using J-HotelReservation, the way to automate reservations on your site.
    <br> <br>
    <b>Start your setup by choosing of the options below</b>
    <br>
    <?php
 				jimport('joomla.application.module.helper');
				$module = JModuleHelper::getModule('jhotelreservation_adminlinks','J-HotelReservation');
				echo JModuleHelper::renderModule($module);
	?>
   <div class="box">
        <div class="title">We provide all the support that you need</div>	
		<div class="content"> 
			<ul>
				<li><a target="_blank" href="http://www.cmsjunkie.com/forum/hotel_reservation/?p=1"> Comunity forum </a> - get in touch with our comunity to find the best solutions </li>
				<li><a target="_blank" href="https://www.cmsjunkie.com/helpdesk/customer/index/">Support Ticket</a> - cannot find a solution for your issue? Post a ticket.</li>
				<li><a target="_blank" href="http://www.cmsjunkie.com/contacts/">Contact us</a> - post a sales question</li>
				<li><a target="_blank" href="http://www.cmsjunkie.com/docs/jhotelreservation/index.html">Online documentation</a> - find details about the extension features & functionality </li>
			</ul>
		</div>
    </div>
</div>
