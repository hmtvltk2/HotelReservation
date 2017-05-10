<?php
/**
 * @copyright	Copyright (C) 2009-2011 ACYBA SARL - All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');


?>
<div id="mangeReservations" class="manage-reservation">
	<form action="<?php echo JRoute::_('index.php') ?>" method="post" name="reservationForm" id="reservationForm"  class="form-validate">
		<fieldset>
					<div id="boxes">
						<div  class="right">
							<button id="reservation-BackButtom" class="ui-hotel-button ui-hotel-button grey right" name="checkRates" type="button"  onclick="jQuery('#task').val('customeraccount.back');document.forms['reservationForm'].submit();">
								<i class="fa fa-tachometer"></i>
								<span class="ui-button-text">
									<?php echo JText::_('LNG_DASHBOARD');?>
								</span>
							</button>
						</div>
						<?php if(count($this->rows)>0){?>
							<table class="admintable table" width=100% style="padding:15px;">
								<thead>	
									<th style="padding: 15px;" colspan="6"><B class="Reservation_details"><?php echo JText::_('LNG_RESERVATION_DETAILS',true)?></B></th>

								</thead>
								<tbody>
								<?php
								for($i = 0; $i <  count( $this->rows ); $i++)
								{
									$reservation = $this->rows[$i]; 
									$date_parts1=explode("-", $reservation->start_date);   
									$date_parts2=explode("-", date('Y-m-d'));

									//gregoriantojd() Converts a Gregorian date to Julian Day Count   
									$start_date		=	gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);   
									$end_date		=	gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);   
									$day_dif 		= 	$start_date - $end_date;
									$canCancell 	= 	true;
								?>
								<tr class="row<?php echo $i%2 ?>"
									onmouseover	=	"this.style.cursor='hand';this.style.cursor='pointer'"
									onmouseout	=	"this.style.cursor='default'"
								>
                                    <td class="reviewQuestion" align=left style="padding:10px;border-right:0px;">
											<label title="<?php echo JText::_('LNG_NAME',true);?>">
												<?php echo JText::_('LNG_NAME',true);?>:
											</label>										
											<B><a href='<?php echo JRoute::_( 'index.php?option='.getBookingExtName().'&task=customeraccount.editreservation&reservationId='. $reservation->confirmation_id )?>'
                                                ><?php echo $reservation->first_name.' '.$reservation->last_name?></a></B>
											<label title="<?php echo JText::_('LNG_HOTEL',true);?>">
												<?php echo JText::_('LNG_HOTEL',true);?>:
												</label>
											<B><?php echo $reservation->hotel_name?></B>
										</td>
                                        <td>
											<label title="<?php echo JText::_('LNG_PERIOD',true);?>">
												<?php echo JText::_('LNG_PERIOD',true);?>:
											</label>
											
											<B><?php echo date('d-M-Y', strtotime($reservation->start_date))?>
											to
											<?php echo date('d-M-Y', strtotime($reservation->end_date))?></B>
											<label title="<?php echo JText::_('LNG_DESCRIPTION',true);?>">
												<?php echo JText::_('LNG_DESCRIPTION',true);?>:
											</label>

											<B>
											<?php echo JText::_('LNG_ADULT_S',true)?> : <?php echo $reservation->adults?>
											,
											<?php echo JText::_('LNG_CHILD_S',true)?> : <?php echo $reservation->children?>
											,
											<?php echo JText::_('LNG_ROOMS',true)?> : <?php echo $reservation->rooms?> 
											
											</B>
                                        </td>
										<td>
                                            <label title="<?php echo JText::_('LNG_NAME',true);?>"><?php echo JText::_('LNG_STATUS',true);?>:</label>
												<B><?php echo $this->reservationStatuses[$reservation->reservation_status];?> </B>
											<br/><br/>
											<!-- <label title="<?php echo JText::_('LNG_NAME',true);?>"><?php echo JText::_('LNG_PAYMENT',true);?>:</label>		
												<B><?php echo $this->reservationStatuses[$reservation->payment_status];?></B> -->	
										</td>	
										<td align="center" valign="middle" style="border-left:0px;">
											<?php if(($reservation->reservation_status==1 || $reservation->reservation_status==5) && $canCancell)
											{?>
												<a class="ui-hotel-button small" id="editReservation" onclick='javascript:editReservation("<?php echo $reservation->confirmation_id; ?>")'>
													<i class="fa fa-pencil" alt=""></i> 
													<?php echo JText::_('LNG_EDIT')?>&nbsp;
												</a>
											<?php }	?>

											<?php if(($reservation->reservation_status==1 || $reservation->reservation_status==5) && $canCancell)
												{?>
												<a id="cancelReservation" class="ui-hotel-button grey small" onclick='cancelReservation("<?php echo $reservation->confirmation_id; ?>")'>		
													<i class="fa fa-times-circle-o red" alt=""></i> 
													<?php echo JText::_('LNG_CANCEL')?>&nbsp;
												</a>
											<?php }	?>		
										</td>
                                </tr>
									<?php
									
										$tipConfirmation = 'cash';
										if($reservation->payment_processor!=null){
										?>
											<input type="hidden" name="payment_processor_sel_id" id="payment_processor_sel_id" value="<?php echo $reservation->payment_processor?>" />
										<?php 
										}
										?>
										<input type="hidden" name="payment_id" value="<?php echo $reservation->confirmation_payment_id;?>" />
								 	<?php 								
								    }	
								 	?>
									</tbody>
									</table>
									
								<div  class="right">
									<button id="reservation-BackButtom" class="ui-hotel-button ui-hotel-button grey right" name="checkRates" type="button"  onclick="jQuery('#task').val('customeraccount.back');document.forms['reservationForm'].submit();">
										<i class="fa fa-tachometer"></i>
										<span class="ui-button-text">
											<?php echo JText::_('LNG_DASHBOARD');?>
										</span>
									</button>
								</div>	
								<?php 
								}
								else { 
									echo JText::_("LNG_CLIENT_NO_RESERVATIONS");  
									}
								?>
			<input type="hidden" name="option" value="<?php echo getBookingExtName()?>" />
			<input type="hidden" name="task" id ="task" value="" />
			<input type="hidden" name="statusId" id ="statusId" value="" />
			<input type="hidden" name="reservationId" id="reservationId" value="" />
			<input type="hidden" name="controller" value="" />
			<input type="hidden" name="view" value="customeraccount" />
		</fieldset>
	</form>
 </div>
<script type="text/javascript">
function editReservation(confirmationId){
	
		var form = document.getElementById('reservationForm');
		document.getElementById('reservationId').value=confirmationId;
		document.getElementById('task').value = "customeraccount.editreservation";
		form.submit();
}
function cancelReservation(confirmationId){
	if(confirm("Are you sure you want yo cancel the reservation")){
		var form = document.getElementById('reservationForm');
		document.getElementById('reservationId').value=confirmationId;
		document.getElementById('task').value = "customeraccount.cancelReservation";
		document.getElementById('statusId').value='<?php echo CANCELED_PAYMENT_ID;?>';
		form.submit();
	}
}
</script>