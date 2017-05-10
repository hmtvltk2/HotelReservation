<?php if($this->state->get('room.room_id')!=0){?>
	<fieldset>
        <input type='button' name='btn_removefile' id='btn_removefile' value='x' style='display:none'>
		<input type='hidden' name='crt_pos' id='crt_pos' value=''>
		<input type='hidden' name='crt_path' id='crt_path' value=''>
		<legend><?php echo JText::_('LNG_ROOM_PICTURES'); ?></legend>
		<TABLE class='admintable' align=left border=0>
			<TR>
				<TD align=left class="key"><?php echo JText::_('LNG_PICTURES',true); ?>:</TD>
				<TD>
					<TABLE class="admintable" align=center  id='table_room_pictures' name='table_room_pictures' >
						<?php
						$pos = 0;
						foreach( $this->pictures as $picture ) {
                            if (file_exists(JPATH_ROOT . DS . PATH_PICTURES . $picture['room_picture_path']) ) {
                                ?>
                                <TR>
                                    <TD align=left>
                                    <textarea cols=50 rows=2 name='room_picture_info[]'
                                              id='room_picture_info'><?php echo $picture['room_picture_info'] ?></textarea>
                                    </TD>
                                    <td align=center>
                                        <img class='img_picture_room'
                                             alt="<?php echo JHotelUtil::setAltAttribute($picture['room_picture_path']); ?>"
                                             src='<?php echo JURI::root() . PATH_PICTURES . $picture['room_picture_path'] ?>'/>
                                        <BR>
                                        <?php
                                        $shortName = substr(basename($picture['room_picture_path']), -14);
                                        echo $picName = strlen(basename($picture['room_picture_path'])) < 14 ? basename($picture['room_picture_path']) : $shortName;
                                        ?>
                                        <input type='hidden'
                                               value='<?php echo $picture['room_picture_enable'] ?>'
                                               name='room_picture_enable[]'
                                               id='room_picture_enable'
                                            >
                                        <input type='hidden'
                                               value='<?php echo $picture['room_picture_path'] ?>'
                                               name='room_picture_path[]'
                                               id='room_picture_path'
                                            >
                                    </td>
                                    <td align=center>
                                        <img class='btn_picture_delete'
                                             src='<?php echo JURI::base() . "components/" . getBookingExtName() . "/assets/img/del_icon.png" ?>'
                                             onclick="
                                                 var row        = jQuery(this).parents('tr:first');
                                                 var row_idx    = row.prevAll().length;

                                                 jQuery('#crt_pos').val(row_idx);
                                                 jQuery('#crt_path').val('<?php echo $picture['room_picture_path'] ?>');
                                                 jQuery('#btn_removefile').click();
                                                 "

                                            />
                                    </td>
                                    <td align=center>
                                        <img class='btn_picture_status'
                                             src='<?php echo JURI::base() . "components/" . getBookingExtName() . "/assets/img/" . ($picture['room_picture_enable'] ? 'checked' : 'unchecked') . ".gif" ?>'
                                             onclick="
                                                 var form        = document.adminForm;
                                                 var v_status    = null;
                                                 if( form.elements['room_picture_enable[]'].length == null )
                                                 {
                                                 v_status  = form.elements['room_picture_enable[]'];
                                                 }
                                                 else
                                                 {
                                                 v_status  = form.elements['room_picture_enable[]'][<?php echo $pos ?>];
                                                 }

                                                 if( v_status.value == '1')
                                                 {
                                                 jQuery(this).attr('src', '<?php echo JURI::base() . "components/" . getBookingExtName() . "/assets/img/unchecked.gif" ?>');
                                                 v_status.value ='0';
                                                 }
                                                 else
                                                 {
                                                 jQuery(this).attr('src', '<?php echo JURI::base() . "components/" . getBookingExtName() . "/assets/img/checked.gif" ?>');
                                                 v_status.value ='1';
                                                 }"

                                            />
                                    </td>
                                    <td>
								<span
                                    class="span_up"
                                    onclick='var row = jQuery(this).parents("tr:first");  row.insertBefore(row.prev());'>
									<?php echo JText::_('LNG_STR_UP', true) ?>
								</span>
                                    </td>
                                    <td>
								<span
                                    class="span_down"
                                    onclick='var row = jQuery(this).parents("tr:first"); row.insertAfter(row.next());'
                                    >
									<?php echo JText::_('LNG_STR_DOWN', true) ?>
								</span>
                                    </td>


                                </TR>
                                <?php
                                $pos++;
                            }
                        }
						?>
					</TABLE>
				</TD>
			</TR>
			<TR>
				<TD align=left class="key">
					<?php echo JText::_('LNG_PLEASE_CHOOSE_A_FILE',true); ?>
				</TD>
                <TD>
                    <div class="dropzone dropzone-previews" id="file-upload">
                        <div id="actions" class="row">
                            <div class="col-lg-7">
                                <!-- The fileinput-button span is used to style the file input field as button -->
                                 <span class="btn btn-success fileinput-button dz-clickable">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span><?php echo JText::_('LNG_ADD_FILES',true); ?></span>
                                 </span>
                                <button  class="btn btn-primary start" id="submitAll">
                                    <i class="glyphicon glyphicon-upload"></i>
                                    <span><?php echo JText::_('LNG_UPLOAD_ALL',true);?></span>
                                </button>
                            </div>

                        </div>
                    </div>
                    <script>
                        jQuery(document).ready(function () {
                            imageUploader("#file-upload",'<?php echo JURI::base()?>components/<?php echo getBookingExtName()?>/helpers/upload.php?t=<?php echo strtotime('now')?>&_root_app=<?php echo urlencode(JPATH_ROOT)?>&_target=<?php echo urlencode(PATH_PICTURES.PATH_ROOM_PICTURES.($this->item->room_id+0).'/')?>',".fileinput-button","<?php echo JText::_('LNG_DRAG_N_DROP',true); ?>","<?php echo PATH_ROOM_PICTURES.($this->item->room_id+0).'/'?>",100,"addPicture");
                        });
                    </script>
				</TD>
			</TR>
		</TABLE>
	</fieldset>
    <script>

        function addPicture(path, name)
        {
            var shortName = photosNameFormater(name);
            var tb = document.getElementById('table_room_pictures');
            if( tb==null )
            {
                alert('Undefined table, contact administrator !');
            }

            var td1_new			= document.createElement('td');
            td1_new.style.textAlign='left';
            var textarea_new 	= document.createElement('textarea');
            textarea_new.setAttribute("name","room_picture_info[]");
            textarea_new.setAttribute("id","room_picture_info");
            textarea_new.setAttribute("cols","50");
            textarea_new.setAttribute("rows","2");
            td1_new.appendChild(textarea_new);

            var td2_new			= document.createElement('td');
            td2_new.style.textAlign='center';
            var img_new		 	= document.createElement('img');
            img_new.setAttribute('src', "<?php echo JURI::root().PATH_PICTURES?>" + path );
            img_new.setAttribute('class', 'img_picture_room');
            td2_new.appendChild(img_new);
            var span_new		= document.createElement('span');
            span_new.innerHTML 	= "<BR>"+ shortName;
            td2_new.appendChild(span_new);

            var input_new_1 		= document.createElement('input');
            input_new_1.setAttribute('type',		'hidden');
            input_new_1.setAttribute('name',		'room_picture_enable[]');
            input_new_1.setAttribute('id',			'room_picture_enable[]');
            input_new_1.setAttribute('value',		'1');
            td2_new.appendChild(input_new_1);

            var input_new_2		= document.createElement('input');
            input_new_2.setAttribute('type',		'hidden');
            input_new_2.setAttribute('name',		'room_picture_path[]');
            input_new_2.setAttribute('id',			'room_picture_path[]');
            input_new_2.setAttribute('value',		path);
            td2_new.appendChild(input_new_2);

            var td3_new			= document.createElement('td');
            td3_new.style.textAlign='center';

            var img_del		 	= document.createElement('img');
            img_del.setAttribute('src', "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/del_icon.png"?>");
            img_del.setAttribute('class', 'btn_picture_delete');
            img_del.setAttribute('id', 	tb.rows.length);
            img_del.setAttribute('name', 'del_img_' + tb.rows.length);
            img_del.onmouseover  	= function(){ this.style.cursor='hand';this.style.cursor='pointer' };
            img_del.onmouseout 		= function(){ this.style.cursor='default' };
            img_del.onclick  		= function(){
                var row 		= jQuery(this).parents('tr:first');
                var row_idx 	= row.prevAll().length;

                jQuery('#crt_pos').val(row_idx);
                jQuery('#crt_path').val( path );
                jQuery('#btn_removefile').click();
            };

            td3_new.appendChild(img_del);

            var td4_new			= document.createElement('td');
            td4_new.style.textAlign='center';
            var img_enable	 	= document.createElement('img');
            img_enable.setAttribute('src', "<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/checked.gif"?>");
            img_enable.setAttribute('class', 'btn_picture_status');
            img_enable.setAttribute('id', 	tb.rows.length);
            img_enable.setAttribute('name', 'enable_img_' + tb.rows.length);

            img_enable.onclick  		= function() {
                var form = document.adminForm;
                var v_status = null;
                if (form.elements['room_picture_enable[]'].length == null) {
                    v_status = form.elements['room_picture_enable[]'];
                }
                else {
                    pos = this.id;
                    var tb = document.getElementById('table_room_pictures');
                    if (pos >= tb.rows.length)
                        pos = tb.rows.length - 1;
                    v_status = form.elements['room_picture_enable[]'][pos];
                }

                if (v_status.value == '1') {
                    jQuery(this).attr('src', '<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/unchecked.gif"?>');
                    v_status.value = '0';
                }
                else {
                    jQuery(this).attr('src', '<?php echo JURI::base() ."components/".getBookingExtName()."/assets/img/checked.gif"?>');
                    v_status.value = '1';
                }
            };
            td4_new.appendChild(img_enable);


            var td5_new			= document.createElement('td');
            td5_new.style.textAlign='center';

            td5_new.innerHTML	= 	"<span class=\'span_up\' onclick=\'var row = jQuery(this).parents(\"tr:first\");  row.insertBefore(row.prev());\'><?php echo JText::_('LNG_STR_UP',true)?></span>"+
            '&nbsp;' +
            "<span class=\'span_down\' onclick=\'var row = jQuery(this).parents(\"tr:first\"); row.insertAfter(row.next());\'><?php echo JText::_('LNG_STR_DOWN',true)?></span>";

            var tr_new = tb.insertRow(tb.rows.length);

            tr_new.appendChild(td1_new);
            tr_new.appendChild(td2_new);
            tr_new.appendChild(td3_new);
            tr_new.appendChild(td4_new);
            tr_new.appendChild(td5_new);
        }
    </script>
	
<?php }
	else echo JText::_('LNG_PIC_UPLOAD_AVAILABLE_AFTER_SAVE',true);
?>	