document.write('<div id=\"jrs_calendar_form\">\n');
document.write('        <form action=\"http://mydomain.com/index.php?option=com_jhotelreservation\" method=\"get\" id=\"widget_booking_form\" name=\"widget_booking_form\" target=\"_blank\">\n');
document.write('    \n');
document.write('    \n');
document.write('    \n');
document.write('    <input type=\"hidden\" name=\"task\" id=\"task\" value=\"hotels.searchHotels\" />\n');
document.write('    <input type=\"hidden\" name=\"option\" id=\"option\" value=\"com_jhotelreservation\" />\n');
document.write('    <input type=\"hidden\" name=\"view\" id=\"view\" value=\"hotels\" />\n');
document.write('    <input type=\"hidden\" name=\"day_start\" id=\"sDay\" value=\"\" />\n');
document.write('    <input type=\"hidden\" name=\"month_start\" id=\"sMonth\" value=\"\" />\n');
document.write('    <input type=\"hidden\" name=\"year_start\" id=\"sYear\" value=\"\" />\n');
document.write('    <input type=\"hidden\" name=\"day_end\" id=\"eDay\" value=\"\" />\n');
document.write('    <input type=\"hidden\" name=\"month_end\" id=\"eMonth\" value=\"\" />\n');
document.write('    <input type=\"hidden\" name=\"year_end\" id=\"eYear\" value=\"\" />\n');
document.write('    <input type=\"hidden\" name=\"jhotelreservation_rooms\" value=\"1\" />\n');
document.write('    <input type=\"hidden\" name=\"resetSearch\" id=\"resetSearch\" value=\"true\" />\n');
document.write('    <input type=\"hidden\" name=\"widget_id\" id=\"widget_id\" value=\"1\" />\n');
document.write('    <input type=\"hidden\" name=\"rooms\" id=\"rooms\" value=\"1\" />\n');
document.write('    <input type=\"hidden\" name=\"guest_adult\" id=\"guest_adult\" value=\"1\" />\n');
document.write('    <input type=\"hidden\" name=\"guest_child\" id=\"guest_child\" value=\"1\" />\n');
document.write('    <input type=\"hidden\" name=\"jhotelreservation_datas\" id=\"jhotelreservation_datas" value=\"\" />\n');
document.write('    <input type=\"hidden\" name=\"jhotelreservation_datae\" id=\"jhotelreservation_datae\" value=\"\" />\n');
var cssStyle = document.currentScript.getAttribute('cssStyle');
document.write('    <input type=\"hidden\" name=\"cssStyle\" id=\"cssStyle\" value=\"'+cssStyle+'\" />\n');
var searchType = document.currentScript.getAttribute('searchType');
document.write('    <input type=\"hidden\" name=\"searchType\" id=\"searchType\" value=\"'+searchType+'\" />\n');
var searchId = document.currentScript.getAttribute('searchId');
document.write('    <input type=\"hidden\" name=\"searchId\" id=\"searchId\" value=\"'+searchId+'\" />\n');
var filterParams = document.currentScript.getAttribute('filterParams');
if ( filterParams == null)
	filterParams= "";
document.write('    <input type=\"hidden\" name=\"filterParams\" id=\"filterParams\" value=\"'+filterParams+'\" />\n');
var Itemid = document.currentScript.getAttribute('Itemid');
document.write('    <input type=\"hidden\" name=\"Itemid\" id=\"Itemid\" value=\"'+Itemid+'\" />\n');


document.write('    <!-- Check-In Date -->\n');
document.write('    <div id=\"jrs_arrival_input\">\n');
document.write('        <div class=\"jrdp_calendar_pos\" id=\"start-jrdatepicker\"></div>\n');
document.write('                <input type=\"text\" name=\"start_date\" id=\"start_date\" class=\"date-pick\" readonly=\"readonly\" placeholder=\"Check-In\" onclick=\"start_datepicker.show();\" />\n');
document.write('        <img class=\"ui-datepicker-trigger\" src=\"//s3.amazonaws.com/bookdirect_template_assets/widget_1046/assets/cal.png\" onclick=\"start_datepicker.show();\" />\n');
document.write('    </div>\n');
document.write('    <!-- Check-Out Date -->\n');
document.write('    <div id=\"jrs_departure_input\">\n');
document.write('        <div class=\"jrdp_calendar_pos\" id=\"end-jrdatepicker\"></div>\n');
document.write('                <input type=\"text\" name=\"end_date\" id=\"end_date\" class=\"date-pick\" readonly=\"readonly\" placeholder=\"Check-Out\" onclick=\"end_datepicker.show();\" />\n');
document.write('        <img class=\"ui-datepicker-trigger\" src=\"//s3.amazonaws.com/bookdirect_template_assets/widget_1046/assets/cal.png\" onclick=\"end_datepicker.show();\" />\n');
document.write('    </div>\n');

document.write('            <div id=\"jhr_adults_select_div\">\n');
document.write('            <div class=\"jrs_form_text\">Type:</div>\n');
document.write('            <select id=\"jhotelreservation_guest_adult\" name=\"guest_adult\" class=\"jrs_form_select_item\" style=\"width:55px;\">\n');
document.write('<option value=\"2\" selected=\"selected\">Adults</option>	\n');
document.write('<option value=\"1\" >1</option>	\n');
document.write('<option value=\"2\" >2</option>	\n');
document.write('<option value=\"3\" >3</option>	\n');
document.write('<option value=\"4\" >4</option>	\n');
document.write('<option value=\"5\" >5</option>	\n');
document.write('<option value=\"6\" >6</option>	\n');
document.write('<option value=\"7\" >7</option>	\n');
document.write('				\n');
document.write('</select>        </div>\n');
document.write('    \n');
document.write('    \n');


document.write(' <div id=\"jhr_children_select_div\">\n');
document.write('            <div class=\"jrs_form_text\">Type:</div>\n');
document.write('            <select id=\"guest_child\" name=\"guest_child\" class=\"jrs_form_select_item\" style=\"width:65px;\">\n');
document.write('<option value=\"0\" selected=\"selected\">Children</option>	\n');
document.write('<option value=\"1\" >1</option>	\n');
document.write('<option value=\"2\" >2</option>	\n');
document.write('<option value=\"3\" >3</option>	\n');
document.write('<option value=\"4\" >4</option>	\n');
document.write('<option value=\"5\" >5</option>	\n');
document.write('<option value=\"6\" >6</option>	\n');
document.write('<option value=\"7\" >7</option>	\n');
document.write('				\n');
document.write('</select>        </div>\n');
document.write('    \n');
document.write('    \n');

document.write('    <div id=\"jhr_voucher_input\">\n');
document.write('                <input type=\"text\" name=\"voucher\" id=\"voucher\" class=\"date-pick\"  placeholder=\"Offer code\" />\n');
document.write('    </div>\n');

document.write('    <div class=\"reservation-cell-button\">\n');
document.write('    <label for=\"\" class=\"\">&nbsp;</label>\n');
document.write('  	<button type=\"submit\" class=\"ui-hotel-button\" >');
document.write('  		<span class=\"ui-button-text\">Search</span>');
document.write('  	</button>');
document.write('  	</div>	');


document.write('    </form></div>\n');
document.write('\n');
var jrDatePicker = function(params) {
    // Define getElementsByClassName() for browsers that do not have this method defined (IE7).
    if(typeof document.getElementsByClassName != 'function') {
        document.getElementsByClassName = function() {
            var elms = document.getElementsByTagName('*');
            var ei = new Array();
            for(i=0; i<elms.length; i++) {
                if(elms[i].getAttribute('class')) {
                    ecl = elms[i].getAttribute('class').split(' ');
                    for(j=0; j<ecl.length; j++) {
                        if(ecl[j].toLowerCase() == arguments[0].toLowerCase()) {
                            ei.push(elms[i]);
                       }
                    }
                }
                else if(elms[i].className) {
                    ecl = elms[i].className.split(' ');
                    for (j=0;j<ecl.length;j++) {
                        if (ecl[j].toLowerCase() == arguments[0].toLowerCase()) {
                            ei.push(elms[i]);
                        }
                    }
                }
            }
            return ei;
        }
    }

    //
    // Private methods
    //
    var leap_year = function(yr) { return(yr % 400 === 0) || (yr % 4 === 0 && yr % 100 !== 0); };

    var get_dow_names = function(loc) {
        if(loc === undefined || loc === null) { loc = 'en'; }

        if(loc === 'es' || loc === 'fr') { return(['D', 'L', 'M', 'M', 'J', 'V', 'S']); }

        if(loc === 'de') { return(['S', 'M', 'D', 'M', 'D', 'F', 'S']); }

        if(loc === 'zh') { return(['æ˜ŸæœŸæ—¥', 'æ˜ŸæœŸä¸€ ', 'æ˜ŸæœŸäºŒ', 'æ˜ŸæœŸä¸‰', 'æ˜ŸæœŸå››', 'æ˜ŸæœŸäº”', 'æ˜ŸæœŸå…­']); }

        return(['S', 'M', 'T', 'W', 'T', 'F', 'S']);
    };

    var get_month_names = function(loc) {
        if(loc === undefined || loc === null) { loc = 'en'; }

        if(loc === 'es') {
            return(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
                    'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']);
        }

        if(loc === 'fr') {
            return(['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
                    'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre']);
        }

        if(loc === 'de') {
            return(['Januar', 'Februar', 'Marz', 'April', 'Mai', 'Juni', 'Juli',
                    'August', 'September', 'Oktober', 'November', 'Dezember']);
        }

        if(loc === 'zh') {
            return(['1æœˆ', '2æœˆ', '3æœˆ', '4æœˆ', '5æœˆ', '6æœˆ', '7æœˆ',
                    '8æœˆ', '9æœˆ', '10æœˆ', '11æœˆ', '12æœˆ']);
        }

        return(['January', 'February', 'March', 'April', 'May', 'June', 'July',
                'August', 'September', 'October', 'November', 'December']);
    };

    var days_in_month = function(month_num, full_year) {
        // Jan == 0, Feb == 1, Mar == 2, ...
        if(month_num === 0 || month_num === 2 || month_num === 4 ||
           month_num === 6 || month_num === 7 || month_num === 9 || month_num === 11) {
            return(31);
        }

        if(month_num === 3 || month_num === 5 ||
                month_num === 8 || month_num === 10) {
            return(30);
        }

        return(leap_year(full_year) ? 29 : 28);
    };

    var get_max_date = function(param_max_date) {
        // Return a date object set to max_date.
        // Acceptable parameter formats: 3M, 6M, 9M, 1Y, 2Y, * (infinity)
        var date = new Date();
        var month = date.getMonth();
        var year = date.getFullYear();

        // If a Date object is passed in, use that.
        if(param_max_date instanceof Date) { return(param_max_date); }

        switch(param_max_date) {
            case '3M':
                if((month + 3) > 11) { month = (month +3) % 12; year++; }
                else { month += 3; }
                break;
            case '6M':
                if((month + 6) > 11) { month = (month +6) % 12; year++; }
                else { month += 6; }
                break;
            case '9M':
                if((month + 9) > 11) { month = (month +9) % 12; year++; }
                else { month += 9; }
                break;
            case '2Y':
                year += 2;
                break;
            case '*':
                year = 3125;
                break;
            default:       // '1Y' is the default
                year += 1;
        }
        return(new Date(year, month, date.getDate()));
    };

    var get_min_date = function(param_min_date) {
        // Return a date object set to min_date.
        // Acceptable parameter formats: 0, 3M, 6M, 9M, 1Y, 2Y, * (infinity)
        var date = new Date();
        var month = date.getMonth();
        var year = date.getFullYear();

        // If a Date object is passed in, use that.
        if(param_min_date instanceof Date) { return(param_min_date); }

        switch(param_min_date) {
            case '3M':
                if((month - 3) < 0) { month = (month -3) % 12; year--; }
                else { month -= 3; }
                break;
            case '6M':
                if((month - 6) < 0) { month = (month -6) % 12; year--; }
                else { month -= 6; }
                break;
            case '9M':
                if((month - 9) < 0) { month = (month -9) % 12; year--; }
                else { month -= 9; }
                break;
            case '1Y':
                year -= 1;
                break;
            case '2Y':
                year -= 2;
                break;
            case '*':
                year = 1900;
                break;
            default:
                break;  // default is the current date
        }
        return(new Date(year, month, date.getDate()));
    };

    // jrDatePicker options and variables
    var widget_number = params.widget_number || '';
    var MAX_CALENDARS = 2;
    var dp_id_name = params.dp_id_name || '';  // selector id where to display the datepicker
    var id_name = params.id_name || '';        // selector id where to populate a selected date
    var locale = params.locale || 'en';
    var ondateselected_callback = (params.onDateSelected instanceof Function) ? params.onDateSelected : null;
    var onclose_callback = (params.onClose instanceof Function) ? params.onClose : null;
    var display_count = params.display_count || 1;
        display_count = (display_count > MAX_CALENDARS) ? MAX_CALENDARS : display_count;
    var close_onselect = params.close_onselect;
        close_onselect = (close_onselect == undefined) ? true : close_onselect;
    var max_date = get_max_date((params.max_date || '1Y'));  // max date user can scroll forward to
    var min_date = get_min_date((params.min_date || '*'));   // min date user can scroll back to

    var currdate = new Date();
    var today = new Date(currdate.getFullYear(), currdate.getMonth(), currdate.getDate());
    var month_names = get_month_names(locale);  // array of month names
    var day_names = get_dow_names(locale);      // array of day of week names
    //var mn = currdate.getMonth();               // month 0 - 11
    //var yy = currdate.getFullYear();            // 4-digit year
    var mn = (currdate.getTime() < min_date.getTime()) ? min_date.getMonth() : currdate.getMonth();
    var yy = (currdate.getTime() < min_date.getTime()) ? min_date.getFullYear() : currdate.getFullYear();

    var citem = {
        day: 0,
        month: 0,
        year: 1900,
        first_dow: 0,
        total_days: 0,
        offset: 0,
        multi_cal: '',

        markup: function(unique_id) {
            var the_html = '';
            if(this.offset >= this.first_dow) {
                var tmp_date = new Date(this.year, this.month, this.day);
                var td_id = unique_id + this.month+ '_' + this.day + '_' + this.year;
                if(tmp_date.valueOf() > max_date.valueOf()) {
                    the_html += '<td id="' + td_id + '" class="jrdp_calendar_day1_noselect' + widget_number + this.multi_cal + '">' + this.day + '</td>';
                }
                else if(tmp_date.valueOf() < min_date.valueOf()) {
                    the_html += '<td id="' + td_id + '" class="jrdp_calendar_day1_noselect' + widget_number + this.multi_cal + '">' + this.day + '</td>';
                }
                else if(tmp_date.valueOf() == today.valueOf()) {
                    the_html += '<td id="' + td_id + '" class="jrdp_calendar_current_day' + widget_number + this.multi_cal + '">' + this.day + '</td>';
                }
                else {
                    the_html += '<td id="' + td_id + '" class="jrdp_calendar_day1' + widget_number + this.multi_cal + '">' + this.day + '</td>';
                }

                if(this.day >= this.total_days) { this.first_dow = 999; }
            }
            else { the_html += '<td class="jrdp_calendar_day2' + widget_number + this.multi_cal + '">&nbsp;</td>'; }
            this.offset++;
            if(this.offset > this.first_dow) { this.day++; }
            return(the_html);
        }
    };

    var close_datepicker = function() {
        if(close_onselect) {
            document.getElementById(dp_id_name).innerHTML = '';
            if(id_name != '') {
                document.getElementById(id_name).blur();
            }

            if(onclose_callback != undefined) { onclose_callback(); }
        }
    };

    var select_date = function(mm, dd, yy) {
        var the_month, the_day;

        mm++;    // Note: mm is the month number 0 - 11 so always add 1.
        if(mm < 10) { the_month = "0" + mm; } else { the_month = mm.toString(); }
        if(dd < 10) { the_day = "0" + dd;   } else { the_day = dd.toString();   }

        if(id_name != '') {
            if(locale === 'en') {
                document.getElementById(id_name).value = the_month + '/' + the_day + '/' + yy;
            }
            else {
                document.getElementById(id_name).value = the_day + '/' + the_month + '/' + yy;
            }
        }

        if(ondateselected_callback != undefined) { ondateselected_callback(); }
        close_datepicker();
    };

    var month_inc = function() {
        var scroll_date = new Date(yy, mn, today.getDate());
        if((scroll_date.getFullYear() == max_date.getFullYear()) &&
           (scroll_date.getMonth() >= max_date.getMonth())) {
            return;
        }

        if(mn < 11) { mn++; }
        else { mn = 0; yy++; }
        that.show();
    };

    var month_dec = function() {
        var scroll_date = new Date(yy, mn, today.getDate());
        if((scroll_date.getFullYear() == min_date.getFullYear()) &&
           (scroll_date.getMonth() <= min_date.getMonth())) {
            return;
        }

        if(mn > 0) { mn--; }
        else { mn = 11; yy--; }
        that.show();
    };

    var dump_html = function(calendar_html) {
        var the_html = '<tt>';
        for(var j=0; j<calendar_html.length; j++) {
            var ch = calendar_html.charAt(j);
            if(ch == '<') ch = '&lt;';
            else if(ch == '>') ch = '&gt;<br />';
            else if(ch == ' ') ch = '&nbsp;';
            the_html += ch;
        }
        the_html += '</tt>';
        if(document.getElementById('htmldump') != null)
            document.getElementById('htmldump').innerHTML = the_html;
    };


    // Public methods
    //
    // Create an object literal (that) which includes properties and methods
    // for public use.  Any local variables defined outside of that{} or
    // passed to jrDatePicker will remain private but still accessible
    // from functions within that{}.
    var that = {
        version: '1.6.3',

        hide: function() {
            close_datepicker();
        },

        set_min_date: function(mdate) {
            // This will override the min_date param.
            if(mdate instanceof Date) {
                min_date = mdate;
                mn = (currdate.getTime() < min_date.getTime()) ? min_date.getMonth() : currdate.getMonth();
                yy = (currdate.getTime() < min_date.getTime()) ? min_date.getFullYear() : currdate.getFullYear();
            }
        },

        show: function() {
            if(dp_id_name == undefined) return;
            var calendar_html = '';
            var unique_id = 'jrdp_' + dp_id_name + '_';


            calendar_html = '<table id="jrdp_' + dp_id_name + '" class="jrdp_encapsulated_table' + widget_number + '" cellspacing="0" cellpadding="0">';
            calendar_html += '<tr>';

            for(i = 0; i < display_count; i++) {
                citem.day = 1;
                citem.month = mn;
                citem.year = yy;

                if(i > 0) {
                    if(mn < 11) { citem.month = mn +1; }
                    else { citem.month = 0; citem.year = yy +1; }
                }

                currdate.setDate(1);                 // set day of month to the 1st
                currdate.setMonth(citem.month);      // set the month
                currdate.setFullYear(citem.year);    // set to 4-digit year

                citem.offset = 0;
                citem.first_dow = currdate.getDay(); // 0 - 6 (sun - sat)
                citem.total_days = days_in_month(currdate.getMonth(), currdate.getFullYear());
                citem.multi_cal = (display_count > 1) ? '_multi' : '';

                calendar_html += '<td>';
                calendar_html += '<table class="jrdp_calendar' + widget_number + citem.multi_cal + '" cellspacing="0" cellpadding="0">';

                // This below snippet needs to eventually come out as it serves no purpose.
                calendar_html += '    <tr><td colspan="7">';
                calendar_html += '        <table id="jrdp_calendar_table_inner' + widget_number + '" width="100%" border="0" cellspacing="0" cellpadding="0">';
                calendar_html += '        <tr class="jrdp_calendar_tbar' + widget_number + citem.multi_cal + '">';
                if(close_onselect) {
                    calendar_html += '            <td align="right">';
                    calendar_html += '            <span id="' + unique_id + 'close" style="cursor: pointer;">';
                    calendar_html += '                <span class="jrdp_calendar_close_btn' + widget_number + citem.multi_cal + '"></span>';
                    calendar_html += '            </span>';
                    calendar_html += '            </td>';
                }
                else { calendar_html += '         <td align="right">&nbsp;</td>'; }
                calendar_html += '        </tr></table>';
                calendar_html += '    </td></tr>';
                // This above snippet needs to eventually come out as it serves no purpose.

                calendar_html += '    <tr class="jrdp_calendar_month_tbar' + widget_number + citem.multi_cal + '">';
                calendar_html += '            <td colspan="1" class="jrdp_calendar_month_prev' + widget_number + citem.multi_cal + '" align="left">';
                calendar_html += '                <span id="' + unique_id + 'prevmonth' + citem.multi_cal + '_' + i +'">&lsaquo;</span></td>';
                calendar_html += '            <td colspan="5" class="jrdp_calendar_month' + widget_number + citem.multi_cal + '" align="center">' + month_names[citem.month] + ' ' + citem.year + '</td>';
                calendar_html += '            <td colspan="1" class="jrdp_calendar_month_next' + widget_number + citem.multi_cal + '" align="right">';
                calendar_html += '                <span id="' + unique_id + 'nextmonth' + citem.multi_cal + '_' + i +'">&rsaquo;</span></td>';
                calendar_html += '    </tr>';

                calendar_html += '    <tr>';
                for(var j = 0; j < 7; j++) { calendar_html += '<td class="jrdp_calendar_days' + widget_number + citem.multi_cal + '">' + day_names[j] + '</td>'; }
                calendar_html += '    </tr>';

                var rows_printed = 0;
                for(var j = 0; j < 6; j++) {
                    if(citem.first_dow == 999) { break; }
                    calendar_html += '            <tr>';
                    for(var k = 0; k < 7; k++) {
                        calendar_html += citem.markup(unique_id);
                    }
                    calendar_html += '            </tr>';
                    rows_printed++;
                }
                // Output empty rows if needed so we have a total of 6 rows printed.
                for(var j = 0; j < (6-rows_printed); j++) {
                    calendar_html += '<tr>';
                    for(var k = 0; k < 7; k++) {
                        calendar_html += '<td class="jrdp_calendar_day2' + widget_number + citem.multi_cal + '">&nbsp;</td>';
                    }
                    calendar_html += '</tr>';
                }
                calendar_html += '</table>';
                calendar_html += '</td>';
            }
            calendar_html += '</tr></table>';
            document.getElementById(dp_id_name).innerHTML = calendar_html;

            // Setup event listeners for elements.
            //
            // These methods replace the existing click event listener(s) on the element if there are any.
            // Because this was essentially part of DOM 0, this method is very widely supported and requires
            // no special crossâ€“browser code; hence it is normally used to register event listeners dynamically.
            for(var j = 0; j < i; j++) {
                document.getElementById(unique_id + 'prevmonth' + citem.multi_cal + '_' + j).onclick = month_dec;
                document.getElementById(unique_id + 'nextmonth' + citem.multi_cal + '_' + j).onclick = month_inc;

                document.getElementById(unique_id + 'prevmonth' + citem.multi_cal + '_' + j).style.display = 'block';
                document.getElementById(unique_id + 'nextmonth' + citem.multi_cal + '_' + j).style.display = 'block';
            }

            // Attach event listeners to the following events so that the datepicker
            // will close when the user clicks outside of the calendar.
            document.getElementsByTagName('body')[0].onmousedown = close_datepicker;

            document.getElementById('jrdp_' + dp_id_name).onmouseover = function(e) {
                // IE 7-8 does not support event.currentTarget but does so for event.srcElement;
                var target, target_id, ev = e || window.event;
                var using_srcElement = false;
                try { target = ev.currentTarget; }
                catch(err) { target = ev.srcElement; using_srcElement = true; }
                try { target_id = target.id; }
                catch(err) { target_id = (target) ? target : 'jrdp_' + dp_id_name; }
                if(target_id) {
                    //console.log('MOUSE OVER: target_id of triggered element: "' + target_id + '" using_srcElement: ' + using_srcElement);
                    document.getElementById(target_id).onmouseover = function() {
                        document.getElementsByTagName('body')[0].onmousedown = null;
                    };
                }
                document.getElementsByTagName('body')[0].onmousedown = null;
            };

            document.getElementById('jrdp_' + dp_id_name).onmouseout = function(e) {
                // IE 7-8 does not support event.currentTarget but does so for event.srcElement;
                var target, target_id, ev = e || window.event;
                var using_srcElement = false;
                try { target = ev.currentTarget; }
                catch(err) { target = ev.srcElement; using_srcElement = true; }
                try { target_id = target.id; }
                catch(err) { target_id = (target) ? target : 'jrdp_' + dp_id_name; }
                if(target_id) {
                    //console.log('MOUSE OUT: target_id of triggered element: "' + target_id + '" using_srcElement: ' + using_srcElement);
                    document.getElementById(target_id).onmouseout = function() {
                        document.getElementsByTagName('body')[0].onmousedown = close_datepicker;
                    };
                }
                document.getElementsByTagName('body')[0].onmousedown = close_datepicker;
            };

            // Bind event listeners to each day for the onclick event.  Get an array of
            // elements by the class name so we can get the element id name.
            //var day_tds = document.querySelectorAll('.jrdp_calendar_day1' + citem.multi_cal);
            var day_tds = document.getElementsByClassName('jrdp_calendar_day1' + widget_number + citem.multi_cal);
            for(var i = 0; i < day_tds.length; i++) {
                // The id is in the format of 'jrdp_idname_mm_dd_yy'.
                // So if we split on the '_' then we can use the last three elements.
                var items = day_tds[i].id.split('_');
                var mmtmp = items[items.length -3];
                var ddtmp = items[items.length -2];
                var yytmp = items[items.length -1];

                var tmp_id = unique_id + mmtmp + '_' + ddtmp + '_' + yytmp;

                var s  = 'document.getElementById("' + tmp_id + '").onclick = ';
                    s += 'function() { select_date(' + mmtmp + ',' + ddtmp + ',' + yytmp + '); };';

                if(document.getElementById(tmp_id)) {
                    eval(s);
                }
            }

            // Check for the current day node because it will have a different class name.
            //var curr_day_td = document.querySelectorAll('.jrdp_calendar_current_day' + citem.multi_cal);
            var curr_day_td = document.getElementsByClassName('jrdp_calendar_current_day' + widget_number + citem.multi_cal);
            if(curr_day_td.length > 0) {
                var items = curr_day_td[0].id.split('_');
                var mmtmp = items[items.length -3];
                var ddtmp = items[items.length -2];
                var yytmp = items[items.length -1];

                var tmp_id = unique_id + mmtmp + '_' + ddtmp + '_' + yytmp;

                var s  = 'document.getElementById("' + tmp_id + '").onclick = ';
                    s += 'function() { select_date(' + mmtmp + ',' + ddtmp + ',' + yytmp + '); };';
                if(document.getElementById(tmp_id)) {
                    eval(s);
                }
            }

            // Uncomment the below to dump the html for debugging.  Need an element with id='htmldump'
            //dump_html(calendar_html);
        }
    };

    return(that);
};
document.write('<style type=\"text/css\">\n');
document.write('/* -------------------------------------------------------------------- */\n');
document.write('/* jrDatePicker.css                                                     */\n');
document.write('/*                                                                      */\n');
document.write('/* There are two different types of datepickers that we can display and */\n');
document.write('/* therefore styling of each: single and multi.  A single datepicker    */\n');
document.write('/* displays only one month.  A multi datepicker can display multiple    */\n');
document.write('/* months.  All of the class names below are required for styling.      */\n');
document.write('/* -------------------------------------------------------------------- */\n');
document.write('.jrdp_encapsulated_table {\n');
document.write('    /* all datepickers are encapsulated within this class */\n');
document.write('    position: relative;\n');
document.write('    padding: 0;\n');
document.write('    margin: 0;\n');
document.write('    border: 1px solid #c3c3c3 !important; /* Added !important here to prevent our border from being messed with by DMO css */\n');
document.write('    -moz-border-radius: 4px;\n');
document.write('    -webkit-border-radius: 4px;\n');
document.write('    -khtml-border-radius: 4px;\n');
document.write('    border-radius: 4px;\n');
document.write('}\n');
document.write('/* Added the following rules to prevent our table elements from being messed with by DMO css */\n');
document.write('table.jrdp_encapsulated_table, .jrdp_encapsulated_table table, .jrdp_encapsulated_table tr, .jrdp_encapsulated_table th {\n');
document.write('	padding: 0;\n');
document.write('	margin: 0;\n');
document.write('}\n');
document.write('.jrdp_encapsulated_table td {\n');
document.write('	width: auto !important;\n');
document.write('	padding: 0 !important;\n');
document.write('	vertical-align: middle !important;\n');
document.write('}\n');
document.write('/* Endd added rules */\n');
document.write('.jrdp_calendar {\n');
document.write('    /* main calendar style - 2/8/13 Anna took out border from below and added it as important above */\n');
document.write('    width: 200px;\n');
document.write('    margin: 0;\n');
document.write('    padding: 0;\n');
document.write('}\n');
document.write('.jrdp_calendar_pos {\n');
document.write('    position: absolute;\n');
document.write('    display: inherit; /* previously set to inline-block - caused calendars to load to the right in Chrome */\n');
document.write('    z-index: 9999; \n');
document.write('	margin: 22px 0 0 0; /* Added 22px margin top to put calendars below instead over over the date fields */\n');
document.write('}\n');
document.write('.jrdp_calendar_tbar {\n');
document.write('    /* top title bar */\n');
document.write('    background-color: #d94867;\n');
document.write('}\n');
document.write('.jrdp_calendar_close_btn {\n');
document.write('    /* close button displayed in top title bar (not used if close_onselect option is false) */\n');
document.write('    background-color: #d94867;  /*#343434; */\n');
document.write('    width: 1px;\n');
document.write('    height: 1px;\n');
document.write('    display: inline;\n');
document.write('    position:absolute;\n');
document.write('    color: #ffffff;\n');
document.write('    float: right;\n');
document.write('/*\n');
document.write('    width: 24px;\n');
document.write('    height: 23px;\n');
document.write('    -moz-border-radius: 12px;\n');
document.write('    border-radius: 12px;\n');
document.write('    top: -12px;\n');
document.write('    right: -12px;\n');
document.write('    font-family: verdana;\n');
document.write('    font-weight: bold;\n');
document.write('    text-align: center;\n');
document.write('    padding-top: 1px;\n');
document.write('*/\n');
document.write('}\n');
document.write('#jrdp_calendar_table_inner {\n');
document.write('    /* The table that uses this will eventually be taken out as it serves no */\n');
document.write('    /* purpose (it was used for the close button but that has been removed). */\n');
document.write('    display: none;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_tbar {\n');
document.write('    background-color: #e3e3e3;\n');
document.write('    border-bottom: 1px solid #c3c3c3;\n');
document.write('}\n');
document.write('.jrdp_calendar_month {\n');
document.write('    /* style for month name */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 13px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: bold;\n');
document.write('    color: #343434;\n');
document.write('    text-decoration: none;\n');
document.write('    text-align: center;\n');
document.write('    background-color: rgb(227, 227, 227) !important;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_prev {\n');
document.write('    /* style for the previous button */\n');
document.write('    font-family: Verdana, Geneva, sans-serif;\n');
document.write('    font-size: 18px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: bold;\n');
document.write('    color: #343434;\n');
document.write('    text-decoration: none;\n');
document.write('    cursor: pointer;\n');
document.write('    text-align: center;\n');
document.write('    display: block;\n');
document.write('    background-color: rgb(227, 227, 227) !important;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_next {\n');
document.write('    /* style for the next button */\n');
document.write('    font-family: Verdana, Geneva, sans-serif;\n');
document.write('    font-size: 18px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: bold;\n');
document.write('    color: #343434;\n');
document.write('    text-decoration: none;\n');
document.write('    cursor: pointer;\n');
document.write('    text-align: center;\n');
document.write('    display: block;\n');
document.write('    background-color: rgb(227, 227, 227) !important;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_prev:hover { background-color: #c3c3c3; }\n');
document.write('.jrdp_calendar_month_next:hover { background-color: #c3c3c3; }\n');
document.write('.jrdp_calendar_days {\n');
document.write('    /* style for the day of the week abbrevations */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 11px;\n');
document.write('    font-weight: bold;\n');
document.write('    color: #444;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: none;\n');
document.write('    width: 20px;\n');
document.write('    height: 21px;\n');
document.write('    text-align: center;\n');
document.write('    vertical-align: middle;\n');
document.write('    border-bottom: 1px solid #C3C3C3;\n');
document.write('}\n');
document.write('.jrdp_calendar_current_day {\n');
document.write('    /* style for the current day */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 12px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #343434;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #b5dce5;\n');
document.write('    height: 16px;\n');
document.write('    width: 20px;\n');
document.write('    text-align: center;\n');
document.write('    cursor: pointer;\n');
document.write('}\n');
document.write('.jrdp_calendar_current_day:hover { background-color: #ffff99; cursor: pointer; }\n');
document.write('.jrdp_calendar_day1 {\n');
document.write('    /* style for a cell that has a day and is selectable */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 12px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #7A7A7A;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #ffffff;\n');
document.write('    width: 24px;\n');
document.write('    text-align: center;\n');
document.write('    cursor: pointer;\n');
document.write('    line-height: 2em;\n');
document.write('}\n');
document.write('.jrdp_calendar_day1:hover {\n');
document.write('    background-color: #ffff99;\n');
document.write('    cursor: pointer;\n');
document.write('}\n');
document.write('.jrdp_calendar_day1_noselect {\n');
document.write('    /* style for a cell that has a day and is not selectable */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 12px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #C1C1C1;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #ffffff;\n');
document.write('    width: 20px;\n');
document.write('    text-align: center;\n');
document.write('  line-height: 2em;\n');
document.write('}\n');
document.write('.jrdp_calendar_day1_noselect:hover {\n');
document.write('    background-color: #EAEAEA;\n');
document.write('}\n');
document.write('.jrdp_calendar_day2 {\n');
document.write('    /* style for an empty cell */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 5px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #000000;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #ffffff;\n');
document.write('    height: 0px;\n');
document.write('    width: 20px;\n');
document.write('    text-align: center;\n');
document.write('}\n');
document.write('.jrdp_calendar_icon {\n');
document.write('    /* style for a datepicker icon; used in html and is thus optional */\n');
document.write('    cursor: pointer;\n');
document.write('    border-width: 0px;\n');
document.write('    height: 13px;\n');
document.write('    width: 13px;\n');
document.write('}\n');
document.write('/* ----------------------------------- */\n');
document.write('/* Styles for a multiple month display */\n');
document.write('/* ----------------------------------- */\n');
document.write('.jrdp_calendar_multi {\n');
document.write('    /* main multi month style */\n');
document.write('    border: 1px solid #0000ff;\n');
document.write('    width: 400px;\n');
document.write('    margin: 0;\n');
document.write('    padding: 0;\n');
document.write('}\n');
document.write('.jrdp_calendar_pos_multi {\n');
document.write('    position: absolute;\n');
document.write('    z-index: 9999;\n');
document.write('}\n');
document.write('.jrdp_calendar_tbar_multi {\n');
document.write('    /* top title bar */\n');
document.write('    background-color: #d94867;\n');
document.write('}\n');
document.write('.jrdp_calendar_close_btn_multi {\n');
document.write('    /* close button displayed in top title bar (not used if close_onselect option is false) */\n');
document.write('    background: url(\'jrDatePicker_close_button.jpg\') no-repeat;\n');
document.write('    width: 15px;\n');
document.write('    height: 15px;\n');
document.write('    display: block;\n');
document.write('    float: right;\n');
document.write('}\n');
document.write('.jrdp_calendar_close_btn_multi:hover { cursor: pointer; }\n');
document.write('.jrdp_calendar_month_tbar_multi {\n');
document.write('    /* title bar where the month name and next / previous buttons are displayed */\n');
document.write('    background-color: #64d7f5;\n');
document.write('}\n');
document.write('#jrdp_calendar_table_inner_multi {\n');
document.write('    /* The table that uses this will eventually be taken out as it serves no */\n');
document.write('    /* purpose (it was used for the close button but that has been removed). */\n');
document.write('    display: none;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_multi {\n');
document.write('    /* style for the month name */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #000000;\n');
document.write('    height: 30px;\n');
document.write('    text-decoration: none;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_prev_multi {\n');
document.write('    /* style for the previous button */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: bold;\n');
document.write('    color: #000000;\n');
document.write('    text-decoration: none;\n');
document.write('    cursor: pointer;\n');
document.write('    padding-left: 4px;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_next_multi {\n');
document.write('    /* style for the next button */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: bold;\n');
document.write('    color: #000000;\n');
document.write('    text-decoration: none;\n');
document.write('    cursor: pointer;\n');
document.write('    padding-right: 4px;\n');
document.write('}\n');
document.write('.jrdp_calendar_month_prev_multi:hover { background-color: #ffffff; }\n');
document.write('.jrdp_calendar_month_next_multi:hover { background-color: #ffffff; }\n');
document.write('.jrdp_calendar_days_multi {\n');
document.write('    /* style for the day of the week abbrevations */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: italic;\n');
document.write('    font-weight: bold;\n');
document.write('    color: #ffffff;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #396221;\n');
document.write('    width: 40px;\n');
document.write('    height: 40px;\n');
document.write('    text-align: center;\n');
document.write('    vertical-align: middle;\n');
document.write('}\n');
document.write('.jrdp_calendar_current_day_multi {\n');
document.write('    /* style for the current day */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #000000;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #b5dce5;\n');
document.write('    height: 16px;\n');
document.write('    width: 20px;\n');
document.write('    text-align: center;\n');
document.write('    cursor: pointer;\n');
document.write('}\n');
document.write('.jrdp_calendar_current_day_multi:hover { background-color: #ffff99; cursor: pointer; }\n');
document.write('.jrdp_calendar_day1_multi {\n');
document.write('    /* style for a cell that has a day and is selectable */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #000000;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #f3e1a5;\n');
document.write('    height: 40px;\n');
document.write('    width: 40px;\n');
document.write('    text-align: center;\n');
document.write('    cursor: pointer;\n');
document.write('}\n');
document.write('.jrdp_calendar_day1_multi:hover {\n');
document.write('    background-color: #ffff99;\n');
document.write('    cursor: pointer;\n');
document.write('}\n');
document.write('.jrdp_calendar_day1_noselect_multi {\n');
document.write('    /* style for a cell that has a day and is not selectable */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #000000;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #f3e1a5;\n');
document.write('    height: 40px;\n');
document.write('    width: 40px;\n');
document.write('    text-align: center;\n');
document.write('}\n');
document.write('.jrdp_calendar_day1_noselect_multi:hover {\n');
document.write('    background-color: #c0c0c0;\n');
document.write('}\n');
document.write('.jrdp_calendar_day2_multi {\n');
document.write('    /* style for an empty cell */\n');
document.write('    font-family: Arial, Helvetica, sans-serif;\n');
document.write('    font-size: 14px;\n');
document.write('    font-style: normal;\n');
document.write('    font-weight: normal;\n');
document.write('    color: #000000;\n');
document.write('    text-decoration: none;\n');
document.write('    background-color: #f3e1a5;\n');
document.write('    height: 40px;\n');
document.write('    width: 40px;\n');
document.write('    text-align: center;\n');
document.write('}\n');
document.write('.jrdp_calendar_icon_multi {\n');
document.write('    /* style for a datepicker icon; used in html and is thus optional */\n');
document.write('    cursor: pointer;\n');
document.write('    border-width: 0px;\n');
document.write('    height: 13px;\n');
document.write('    width: 13px;\n');
document.write('}\n');



document.write('#jrs_calendar_form {\n');
document.write('    color: #555;\n');
document.write('    font-family: arial, helvetica, sans-serif;\n');
document.write('    font-size: 10px;\n');
document.write('    height: 100px;\n');
document.write('    text-align: left;\n');
document.write('    width: 600px;\n');
document.write('    position:relative;\n');
document.write('}\n');
document.write('.date-pick {\n');
document.write('	background: transparent!important;\n');
document.write('    border: 1px solid #777!important;\n');
document.write('    font-size: 10px!important;\n');
document.write('	color: #777!important;\n');
document.write('	font-family: Gotham, \"Helvetica Neue\", Helvetica, Arial, sans-serif;\n');
document.write('    height: 26px!important;\n');
document.write('    padding: 4px !important;\n');
document.write('    width: 98px!important;\n');
document.write('}\n');
document.write('#jrs_booking_header {\n');
document.write('}\n');
document.write('.jrs_form_text {\n');
document.write('display:none;\n');
document.write('}\n');
document.write('.jrs_clr {\n');
document.write('    clear: both;\n');
document.write('}\n');
document.write('#jrs_arrival_input {\n');
document.write('    left: 28px;\n');
document.write('    position: absolute;\n');
document.write('}\n');
document.write('#jrs_departure_input {\n');
document.write('    left: 133px;\n');
document.write('    position: absolute;\n');
document.write('}\n');
document.write('#jrs_lodging_select_div {\n');

document.write('}\n');


document.write('.ui-hotel-button {\n');
document.write('  background: #0095dd;\n');
document.write('  background: -moz-linear-gradient(center top, #0095dd 0%, #0077b7 80%) repeat scroll 0 0 rgba(0, 0, 0, 0);\n');
document.write('  background: -webkit-gradient(linear, left top, left bottom, from(#0095dd), to(#0077b7));\n');
document.write('  background: -webkit-linear-gradient(top, #0095dd, #0077b7);\n');
document.write('  background: linear-gradient(top, #0095dd, #0077b7);\n');
document.write('}\n');

document.write(' button.ui-hotel-button{ \n');
document.write('  min-width: 110px !important;\n');
document.write('  border: 1px solid #0087D0;\n');
document.write('  border-radius: 0px !important;\n');
document.write(' color: #FFFFFF!important;\n');
document.write('  display: inline-block;\n');
document.write('  font-family: \"DepotNewRegular\", \"Helvetica Neue\", Arial, Helvetica, sans-serif;\n');
document.write('  font-size: 14px;\n');
document.write('  font-weight: 500;\n');
document.write('  line-height: 1;\n');
document.write('  margin: 3px;\n');
document.write('  overflow: visible;\n');
document.write('  padding: 6px 14px !important;\n');
document.write('  position: relative;\n');
document.write('  text-decoration: none !important;\n');
document.write('  text-shadow: 1px 1px #00659B;\n');	
document.write('  text-align: center;\n');
document.write(' }\n');



document.write(' .reservation-cell-button{ \n');
document.write('    left: 480px;\n');
document.write('    top: -14px;\n');

document.write('    bottom: 5px;\n');
document.write('    position: absolute;\n');
document.write(' }\n');

document.write(' #jhr_adults_select_div{ \n');
document.write('    left: 240px;\n');
document.write('    top: 0px;\n');
document.write('    position: absolute;\n');
document.write(' }\n');

document.write(' #jhr_children_select_div{ \n');
document.write('    left: 300px;\n');
document.write('    top: 0px;\n');
document.write('    position: absolute;\n');
document.write(' }\n');

document.write(' #jhr_voucher_input{ \n');
document.write('    left: 370px;\n');
document.write('    top: 0px;\n');
document.write('    position: absolute;\n');
document.write(' }\n');

document.write('.ui-datepicker{\n');
document.write('	z-index:1000;\n');
document.write('}\n');
document.write('.jrs_form_select_item {\n');
document.write('	background: transparent;\n');
document.write('    border: 1px solid #777;\n');
document.write('    font-size: 10px;\n');
document.write('	color: #777;\n');
document.write('	font-family: Gotham, \"Helvetica Neue\", Helvetica, Arial, sans-serif;\n');
document.write('      height: 27px !important;\n');
document.write('}\n');
document.write('.ui-datepicker-trigger{\n');
document.write('	display:inline !important;\n');
document.write('	border:none !important;\n');
document.write('	position:absolute;\n');
document.write('	right: 4px;\n');
document.write('	top: 4px !important;\n');
document.write('}\n');
document.write('#jrs_search_submit {\n');
document.write('    right: 0;\n');
document.write('    bottom: 5px;\n');
document.write('    position: absolute;\n');
document.write('}\n');
document.write('@media screen and (max-width:959px){\n');
document.write('	#jrs_calendar_form {\n');
document.write('		margin-top:0px;\n');
document.write('	}\n');
document.write('	.fp-roksprocket-slideshow {\n');
document.write('    margin-top: -53px!important;\n');
document.write('}\n');
document.write('}\n');
document.write('	#jrs_calendar_form {\n');
document.write('	width:360px;	\n');
document.write('	    background: none;\n');
document.write('	}\n');
document.write('	#jrs_lodging_select_div {\n');
document.write('    float: left;\n');
document.write('    width: 120px;\n');
document.write('}\n');

document.write('#jrs_arrival_input {\n');
document.write('    left: 28px;\n');
document.write('}\n');
document.write('#jrs_departure_input {\n');
document.write('    left: 133px;\n');
document.write('}\n');
document.write('.jack {\n');
document.write('    margin: -11px 0px 15px 6px!important;\n');
document.write('	\n');
document.write('}\n');
document.write('}\n');

document.write('	#jrs_calendar_form {\n');
document.write('	width:360px;	\n');
document.write('	    background: none;\n');
document.write('	}\n');
document.write('	#jrs_lodging_select_div {\n');

document.write('    width: 120px;\n');
document.write('}\n');


document.write('}\n');
document.write('@media only all and (max-width: 47.938rem) and (min-width: 35.062rem){\n');
document.write('.jack {\n');
document.write('    margin: -10px -35px 11px 0!important;\n');
document.write('}}\n');
document.write('@media only all and (max-width: 35rem){\n');
document.write('.jack {\n');
document.write('    margin: -10px 10px 10px 0!important;\n');
document.write('}}\n');
document.write('</style>\n');
document.write('\n');

var flights_activated = 0;
var widget_filename = 'lenawee-home-responsive-3';
var datepicker_last_date = '1Y';
    datepicker_last_date = (typeof(datepicker_last_date) == 'object') ? new Date(datepicker_last_date) : null;

if(flights_activated) {
    simpleAutocomplete.init();
}

var start_datepicker = jrDatePicker({
    dp_id_name: 'start-jrdatepicker',      // selector id where to display the datepicker
    id_name: 'start_date',                 // selector id where to populate a selected date
    max_date: '1Y',
    min_date: '0',
    locale: 'en',
    onDateSelected: function() { DatePicked('start_date', end_datepicker); }
});

var end_datepicker = jrDatePicker({
    dp_id_name: 'end-jrdatepicker',        // selector id where to display the datepicker
    id_name: 'end_date',                   // selector id where to populate a selected date
    max_date: '1Y',
    min_date: '0',
    locale: 'en',
    onDateSelected: function() { DatePicked('end_date'); }
});





if(document.getElementById('nights')) { document.getElementById('nights').onchange = DatePicked; }

// Helper functions for tabs and extensions select list.
var create_input_element = function(id) {
    // Create a hidden input element node and return it.
    var node = document.createElement('input');
    node.setAttribute('id', id);
    node.setAttribute('name', id);
    node.setAttribute('value', '1');
    node.setAttribute('type', 'hidden');
    return(node);
}

var remove_element_node = function(parent_node, id) {
    if(document.getElementById(id)) {
        parent_node.removeChild(document.getElementById(id));
    }
}

var toggle_widget_tab = function(tab_id) {
    // Toggle the selected tab.
    document.getElementById('widget_tab_flights').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_activities').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_lodgings').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_packages').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_deals').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_restaurants').removeAttribute('class', 'active_tab');

    // Toggle the selected custom tab
    document.getElementById('widget_tab_activities_ctab').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_lodgings_ctab').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_packages_ctab').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_deals_ctab').removeAttribute('class', 'active_tab');
    document.getElementById('widget_tab_restaurants_ctab').removeAttribute('class', 'active_tab');

    // Activate the proper tab.
    if(document.getElementById(tab_id)) {

            var start_label_text, end_label_text;
            switch(tab_id) {
                case 'widget_tab_lodgings':
                case 'widget_tab_lodgings_ctab':
                    ;
                                        break;
                case 'widget_tab_packages':
                case 'widget_tab_packages_ctab':
                     ;
                                         break;
                case 'widget_tab_activities':
                case 'widget_tab_activities_ctab':
                     ;
                                         break;
                case 'widget_tab_deals':
                case 'widget_tab_deals_ctab':
                     ;
                                         break;
                case 'widget_tab_restaurants':
                case 'widget_tab_restaurants_ctab':
                     ;
                                         break;
                case 'widget_tab_flights':
                     ;
                                         break;
            }
            if(start_label_text) {
                var jrs_start_label = document.getElementById("jrs_start_label");
                if(jrs_start_label) { jrs_start_label.innerHTML = start_label_text; }
            }
            if(end_label_text) {
	      var jrs_end_label = document.getElementById("jrs_end_label");
              if(jrs_end_label) { jrs_end_label.innerHTML = end_label_text; }
            }
        document.getElementById(tab_id).setAttribute('class', 'active_tab');
    }

   
    

};

var onclick_button_actions = function() {
    jrs_search_button_update();

    var asp_compat = 0;
    if(asp_compat) {
        submit_asp('http://visitlenawee.bookdirect.net/redirect_booking.php');
        return false;
    }

    var ng_compat = 0;
    if(ng_compat) {
        var site_url = 'http://visitlenawee.bookdirect.net/redirect_booking.php' + active_tab_name() + '/';
        site_url = site_url.replace(/\/\/$/, '/');
        submit_ng(site_url, 'en');
        return false;
    }

    // NOTE: this is for seetorontonow.  Let's come up with a more generic way
    // to do but this will suffice for now.
    if(typeof ebConversionTracker == 'function') {
        ebConversionTracker();
    }
}

var active_tab_name = function() {
    var widget_booking_form = document.getElementById('widget_booking_form');
    var jrs_calendar_form = document.getElementById('jrs_calendar_form');
    var parent_element = widget_booking_form ? widget_booking_form : jrs_calendar_form;

    if(document.getElementById('widget_tab_activities').getAttribute('class')) {
        return 'activities';
    } else if(document.getElementById('widget_tab_activities_ctab').getAttribute('class')) {
        return 'activities_ctab';
    } else if(document.getElementById('widget_tab_packages').getAttribute('class')) {
        return 'packages';
    } else if(document.getElementById('widget_tab_packages_ctab').getAttribute('class')) {
        return 'packages_ctab';
    } else if(document.getElementById('widget_tab_deals').getAttribute('class')) {
        return 'deals';
    } else if(document.getElementById('widget_tab_deals_ctab').getAttribute('class')) {
        return 'deals_ctab';
    } else if(document.getElementById('widget_tab_restaurants').getAttribute('class')) {
        return 'restaurants';
    } else if(document.getElementById('widget_tab_restaurants_ctab').getAttribute('class')) {
        return 'restaurants_ctab';
    } else if(document.getElementById('widget_tab_flights').getAttribute('class')) {
        return 'flights';
    } else {
        return '';
    }
};

var remove_tab_input_elements = function(parent_element) {
    remove_element_node(parent_element, 'showSpecialsInit');
    remove_element_node(parent_element, 'showCustomSpecialsInit');
    remove_element_node(parent_element, 'showEventsInit');
    remove_element_node(parent_element, 'showCustomEventsInit');
    remove_element_node(parent_element, 'showRestaurantsInit');
    remove_element_node(parent_element, 'showCustomRestaurantsInit');
    remove_element_node(parent_element, 'showFlightsInit');
    remove_element_node(parent_element, 'showDealsInit');
    remove_element_node(parent_element, 'showCustomDealsInit');
    remove_element_node(parent_element, 'showCustomLodgingsInit');
}

var jrs_search_button_update = function() {
    // Search through the ids to see which one has the 'active_tab' class.
    var widget_booking_form = document.getElementById('widget_booking_form');
    var jrs_calendar_form = document.getElementById('jrs_calendar_form');
    var parent_element = widget_booking_form ? widget_booking_form : jrs_calendar_form;
    var input_tag = null;

    // Note nothing to do for lodgings since it is the default.
    if(parent_element) {
        // clean up input elements
        remove_tab_input_elements(parent_element);

        if(document.getElementById('widget_tab_activities').getAttribute('class')) {
            input_tag = create_input_element('showEventsInit');
        }
        else if(document.getElementById('widget_tab_activities_ctab').getAttribute('class')) {
            input_tag = create_input_element('showCustomActivitiesInit');
        }
        else if(document.getElementById('widget_tab_packages').getAttribute('class')) {
            input_tag = create_input_element('showSpecialsInit');
        }
        else if(document.getElementById('widget_tab_packages_ctab').getAttribute('class')) {
            input_tag = create_input_element('showCustomSpecialsInit');
        }
        else if(document.getElementById('widget_tab_deals').getAttribute('class')) {
            input_tag = create_input_element('showDealsInit');
        }
        else if(document.getElementById('widget_tab_deals_ctab').getAttribute('class')) {
            input_tag = create_input_element('showCustomDealsInit');
        }
        else if(document.getElementById('widget_tab_restaurants').getAttribute('class')) {
            input_tag = create_input_element('showRestaurantsInit');
        }
        else if(document.getElementById('widget_tab_restaurants_ctab').getAttribute('class')) {
            input_tag = create_input_element('showCustomRestaurantsInit');
        }
        else if(document.getElementById('widget_tab_flights').getAttribute('class')) {
            input_tag = create_input_element('showFlightsInit');
        }
        else if(document.getElementById('widget_tab_lodgings_ctab').getAttribute('class')) {
            input_tag = create_input_element('showCustomLodgingsInit');
        }

        if(input_tag) { parent_element.appendChild(input_tag); }
    }

    check_date_fields('1', '2');
}

var reset_campaign = function() {
    // This function resets the campaign name back to its original form.
    // It removes any widget extension name (tab name) appended to the campaign name.
    var campaign = document.getElementById('campaign').value;

    if(campaign.indexOf('(widget)-lodgings') !== -1) {
        var new_campaign = campaign.substring(0, campaign.indexOf('-lodgings'));
        document.getElementById('campaign').value = new_campaign;

    } else if(campaign.indexOf('(widget)-specials') !== -1) {
        var new_campaign = campaign.substring(0, campaign.indexOf('-specials'));
        document.getElementById('campaign').value = new_campaign;

    } else if(campaign.indexOf('(widget)-deals') !== -1) {
        var new_campaign = campaign.substring(0, campaign.indexOf('-deals'));
        document.getElementById('campaign').value = new_campaign;

    } else if(campaign.indexOf('(widget)-activities') !== -1) {
        var new_campaign = campaign.substring(0, campaign.indexOf('-activities'));
        document.getElementById('campaign').value = new_campaign;

    } else if(campaign.indexOf('(widget)-restaurants') !== -1) {
        var new_campaign = campaign.substring(0, campaign.indexOf('-restaurants'));
        document.getElementById('campaign').value = new_campaign;

    } else if(campaign.indexOf('(widget)-flights') !== -1) {
        var new_campaign = campaign.substring(0, campaign.indexOf('-flights'));
        document.getElementById('campaign').value = new_campaign;
    }
}

var update_campaign = function(tab_name) {
    reset_campaign();

    var campaign = document.getElementById('campaign').value;
    document.getElementById('campaign').value = campaign + '-' + tab_name;
}

var display_checkout_field = function(display) {
    display = display ? 'block' : 'none';
    document.getElementById('jrs_departure_input').style.display = display;
}

// Attach event handler functions to the tabs.
if(document.getElementById('widget_tab_activities')) {
    if(active_tab_name() == 'activities') {
        display_checkout_field(true);
        update_campaign('activities');
    }

    document.getElementById('widget_tab_activities').onclick = function() {
        toggle_widget_tab('widget_tab_activities');
        display_checkout_field(true);
        update_campaign('activities');
    };
}
if(document.getElementById('widget_tab_activities_ctab')) {
    if(active_tab_name() == 'activities_ctab') {
        display_checkout_field(true);
        update_campaign('activities');
    }

    document.getElementById('widget_tab_activities_ctab').onclick = function() {
        toggle_widget_tab('widget_tab_activities_ctab');
        display_checkout_field(true);
        update_campaign('activities');
    };
}
if(document.getElementById('widget_tab_flights')) {
    if(active_tab_name() == 'flights') {
        display_checkout_field(true);
        update_campaign('flights');
    }

    document.getElementById('widget_tab_flights').onclick = function() {
        toggle_widget_tab('widget_tab_flights');
        display_checkout_field(true);
        update_campaign('flights');
    };
}
if(document.getElementById('widget_tab_lodgings')) {
    if(active_tab_name() == '') {
        display_checkout_field(true);
        update_campaign('lodgings');
    }

    document.getElementById('widget_tab_lodgings').onclick = function() {
        toggle_widget_tab('widget_tab_lodgings');
        display_checkout_field(true);
        update_campaign('lodgings');
    };
}
if(document.getElementById('widget_tab_lodgings_ctab')) {
    if(active_tab_name() == '') {
        display_checkout_field(true);
        update_campaign('lodgings');
    }

    document.getElementById('widget_tab_lodgings_ctab').onclick = function() {
        toggle_widget_tab('widget_tab_lodgings_ctab');
        display_checkout_field(true);
        update_campaign('lodgings');
    };
}
// Assign an onclick event handler to the search button.
if(document.getElementById('jrs_search_submit_button')) {
    document.getElementById('jrs_search_submit_button').onclick = onclick_button_actions;
}

// This function is used to load non-blocking scripts.
function load_script(url, callback) {
    // The callback function would be where analytics might be added.
    //
    var script = document.createElement('script')
    script.type = 'text/javascript';

    if(script.readyState) {  // IE
        script.onreadystatechange = function() {
            if(script.readyState == 'loaded' || script.readyState == 'complete') {
                script.onreadystatechange = null;
                if(callback) { callback(); }
            }
        };
    } else {  // Others
        script.onload = function() {
            if(callback) { callback(); }
        };
    }

    script.src = url;
    document.body.appendChild(script);
}
// widgets_jrdatepicker.js.php
function format_date(ms) {
    var d = new Date(ms);
    return zeropad((d.getMonth() +1), 2) + '/' + zeropad(d.getDate(), 2) + '/' + d.getFullYear();  // Jan = 0
}

function zeropad(num, zeros) {
    var retval = '';
    var numstr = num.toString();
    for(var i = numstr.length; i < zeros; i++) {
        retval += '0';
    }
    retval += numstr;
    return retval;
}

function DatePicked(trigger, datepicker) {
    var checkin_date = (document.getElementById('start_date')) ? document.getElementById('start_date').value : null;
    var checkout_date = (document.getElementById('end_date')) ? document.getElementById('end_date').value : null;
    var language = (document.getElementById('language')) ? document.getElementById('language').value : 'en';
    var nights = document.getElementById('nights');
    var oneDay = 1000*60*60*24;
    var DSToffset = 1000*60*60*4;    // Take into consideration daylight-savings time change.
    var cindate, coutdate;

    // What triggered this function?
    trigger = (trigger == 'start_date' || trigger == 'end_date') ? trigger : 'nights';

    // Make format adjustments for locales.
    if(language !== 'en') {
        cindate =  (checkin_date.match(/^\d{2}\/\d{2}\/\d{4}$/)) ?
                       new Date(checkin_date.split('/').reverse().join('/')) : null;
        coutdate = (checkout_date.match(/^\d{2}\/\d{2}\/\d{4}$/)) ?
                       new Date(checkout_date.split('/').reverse().join('/')) : null;
    }
    else {
        cindate = (checkin_date.match(/^\d{2}\/\d{2}\/\d{4}$/)) ? new Date(checkin_date) : null;
        coutdate = (checkout_date.match(/^\d{2}\/\d{2}\/\d{4}$/)) ? new Date(checkout_date) : null;
    }

    if(trigger == 'start_date' && cindate != null) {
        var num_nights = (nights && !isNaN(nights.value)) ? parseInt(nights.value) : 1;

        // Customer can stay longer than min_stay; once nights are set use this
        // value, until he resets by picking a different end date. The end_date
        // picker gets its min-date reset to reflect min-stay.
        var search_min_stay = 1;
        var max_nights = Math.max(search_min_stay, num_nights);

        var max_checkout_date = new Date(cindate.getTime() + (oneDay * max_nights) + DSToffset);
        var min_checkout_date = new Date(cindate.getTime() + (oneDay * search_min_stay) + DSToffset);

        if(language !== 'en') {
            var dmy_date = zeropad(max_checkout_date.getDate(), 2) + '/' +
                           zeropad((max_checkout_date.getMonth() +1), 2) + '/' +
                           max_checkout_date.getFullYear();
            document.getElementById('end_date').value = dmy_date;
        } else {
            document.getElementById('end_date').value = format_date(max_checkout_date);
        }

        // Set the minimum checkout date (minimum date selectable in datepicker).
        coutdate = max_checkout_date;
        if(datepicker) {
            var mdate = new Date(min_checkout_date.getFullYear(), min_checkout_date.getMonth(), min_checkout_date.getDate());
            datepicker.set_min_date(mdate);
        }
    }

    if(cindate != null && coutdate != null && trigger != 'nights') {
        var difference = parseInt((coutdate.getTime() - cindate.getTime()) / oneDay);

        if(nights) nights.value = difference;

        // Set the end_date to check-in date + nights.
        var new_checkout_date = new Date(cindate.getTime() + (oneDay * difference) + DSToffset);

        if(datepicker_last_date && new_checkout_date.getTime() > datepicker_last_date.getTime()) {
            var new_checkout_date = datepicker_last_date;
        }

        if(language !== 'en') {
            document.getElementById('end_date').value = zeropad(new_checkout_date.getDate(), 2) + '/' +
                                                        zeropad((new_checkout_date.getMonth() +1), 2) + '/' +
                                                        new_checkout_date.getFullYear();
        } else {
            document.getElementById('end_date').value = format_date(new_checkout_date);
        }

    }
    else if(cindate != null && nights != null && trigger == 'nights') {//only fires on first entry
        if(parseInt(nights.value) < 1) {
            alert("You must select 1 or more nights.");
        }
    }

    if(language != 'en') { jrs_local_dates(); }
    else { set_date_fields(); }
}

function set_date_fields() {
    var start_date = document.getElementById('start_date');
    var end_date = document.getElementById('end_date');
    var nights = document.getElementById('nights');

    var sdate = (start_date) ? new Date(start_date.value) : null;
    var edate = (end_date) ? new Date(end_date.value) : null;

    if(nights && sdate && edate) {
        var ONE_DAY = 1000*60*60*24;
        nights.value = parseInt((edate.getTime() - sdate.getTime()) / ONE_DAY);
    }

    if(sdate) {
        document.getElementById('sDay').value = zeropad(sdate.getDate(), 2);
        document.getElementById('sMonth').value = zeropad((sdate.getMonth() +1), 2);
        document.getElementById('sYear').value = sdate.getFullYear();
		document.getElementById('jhotelreservation_datas').value = start_date.value;
    }
	if(edate) {
        document.getElementById('eDay').value = zeropad(edate.getDate(), 2);
        document.getElementById('eMonth').value = zeropad((edate.getMonth() +1), 2);
        document.getElementById('eYear').value = edate.getFullYear();
		document.getElementById('jhotelreservation_datae').value = end_date.value;
	}
}

function submit_asp(site_url) {
    // Not using jquery and need to encode the form elements (which jquery .serialize() would be used).
    var form_data = '';
    var container = document.getElementById('jrs_calendar_form');
    var inputs = container.getElementsByTagName('input');
    for(var i = 0; i < inputs.length; ++i) {
       // deal with inputs[index] element.
       var name = inputs[i].name;
       var value = inputs[i].value;
       form_data += name + '=' + value + '&';
    }

    if(document.getElementById('lodgingID')) {
        var categories = document.getElementById('lodgingID');
        var selected_value = categories.options[categories.selectedIndex].value;
        form_data += 'lodgingID=' + selected_value + '&';
    }

    form_data = form_data.replace(/&$/, '');      // remove the last ampersand

    window.open(site_url + '?' + form_data);
}

function to_YYYYMMDD(date_param, locale) {
    // This function assumes that date_param is MM/DD/YYYY
    // or DD/MM/YYYY depending upon the locale.
    var result = ''
    var d = date_param.split('/');
    if(d.length !== 3) { return result; }
    if(!locale) { locale = 'en'; }

    if(locale === 'en') {
        result = d[2].toString() + d[0].toString() + d[1].toString();
    }
    else {
        result = d[2].toString() + d[1].toString() + d[0].toString();
    }

    return result;
}

function location_params() {
    var form_data = '';
    var group_ids, region_group_ids, city_group_ids;

    group_ids = document.getElementById('jrs_location_group_id');
    if(group_ids) {
        form_data += 'group_ids[]=' + group_ids.value + '&';
    }

    city_group_ids = document.getElementById('jrs_location_city_group_id');
    if(city_group_ids && city_group_ids.value) {
        form_data += 'jrs_location_city_group_id=' + city_group_ids.value + '&';
        //document.getElementById('group_id').value = city_group_ids.value;
    }

    if(city_group_ids && !city_group_ids.value) {
        region_group_ids = document.getElementById('jrs_location_region_group_id_flex');
        if(region_group_ids && region_group_ids.value) {
            form_data += 'jrs_location_region_group_id=' + region_group_ids.value + '&';
        }
    }

    return form_data;
}

function lodging_type_params() {
    // Using a select list, there can be only one value however if there
    // are multiple categories (lodging types) we would construct the
    // following: category_ids[]=101&category_ids[]235&...
    var form_data = '';
    var category_ids = document.getElementById('lodgingID');
    if(category_ids) {
        form_data += 'category_ids[]=' + category_ids.value + '&';
    }

    var lodging_detail_id = document.getElementById('lodging_detail_id');
    if(lodging_detail_id) {
        form_data += 'lodging_detail_id=' + lodging_detail_id.value + '&';
    }
    return form_data;
}

function submit_ng(site_url, locale) {
    // This function is used to submit form data to our angular-js
    // responsive template via querystring params.

    var form_data = '';
    var container = document.getElementById('jrs_calendar_form');
    var inputs = container.getElementsByTagName('input');
    if(!locale) { locale = 'en'; }

    // Encode the form elements like jquery .serialize() would do.
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].name === 'start_date') {
            var checkin = to_YYYYMMDD(inputs[i].value, locale);
            form_data += 'checkin=' + checkin + '&';
        }
        else if(inputs[i].name === 'end_date') {
            var checkout = to_YYYYMMDD(inputs[i].value, locale);
            form_data += 'checkout=' + checkout + '&';
        }
    }

    // We want checkin and checkout to be the first url params.
    for(var i = 0; i < inputs.length; ++i) {
        if(inputs[i].name === 'campaign') {
            form_data += 'campaign=' + inputs[i].value + '&';
        }
        else if(inputs[i].name === 'airport_code') {
            form_data += 'airport_fragment=' + inputs[i].value + '&';
        }
    }

    form_data += lodging_type_params();
    form_data += location_params();

    if(locale !== 'en') {
        form_data += 'locale=' + locale + '&';
    }

    form_data = form_data.replace(/&$/, '');  // remove last ampersand

    window.open(site_url + '?' + form_data);
}

function jrs_local_dates() {
    var now = new Date();
    var one_day = 1000*60*60*24;
    var DSToffset = 1000*60*60*4;    // Take into consideration daylight-savings time change.

    // Reverse a date in the format of dd/mm/yyyy into yyyy/mm/dd and get a Date object.
    var checkin_date = new Date(document.getElementById('start_date').value.split('/').reverse().join('/'));
    var checkout_date = new Date(document.getElementById('end_date').value.split('/').reverse().join('/'));

    document.getElementById('sDay').value = zeropad(checkin_date.getDate(), 2);
    document.getElementById('sMonth').value = zeropad((checkin_date.getMonth() +1), 2);
    document.getElementById('sYear').value = checkin_date.getFullYear();

    var nights = Math.ceil((checkout_date.getTime() - DSToffset - checkin_date.getTime()) / (one_day));
    document.getElementById('nights').value = Math.max(nights, 1);
}

function check_date_fields(start_date_offset, end_date_offset) {
    // Set default start_date & end_date values using the offset parameters.
    var start_date = (document.getElementById('start_date')) ? document.getElementById('start_date').value : '';
    var one_day = 1000*60*60*24;
    var language = (document.getElementById('language')) ? document.getElementById('language').value : 'en';

    // If date is valid return, else continue.
    if(start_date.match(/^\d{2}\/\d{2}\/\d{4}$/)) { return; }

    // isNaN(null) returns false so check for null values.
    if(start_date_offset == null || end_date_offset == null) {
        start_date_offset = one_day;
        end_date_offset = one_day * 2;
    }
    else {
        start_date_offset = (isNaN(start_date_offset)) ? one_day : (parseInt(start_date_offset) * one_day);
        end_date_offset = (isNaN(end_date_offset)) ? one_day : (parseInt(end_date_offset) * one_day);
    }

    var curdate = new Date();
    var dsdate = new Date(curdate.getTime() + start_date_offset);  // default start date
    var dedate = new Date(curdate.getTime() + end_date_offset);    // default end date

    if(language !== 'en') {
        document.getElementById('start_date').value = zeropad(dsdate.getDate(), 2) + '/' +
                                                      zeropad((dsdate.getMonth() +1), 2) + '/' +
                                                      dsdate.getFullYear();
        document.getElementById('end_date').value = zeropad(dedate.getDate(), 2) + '/' +
                                                    zeropad((dedate.getMonth() +1), 2) + '/' +
                                                    dedate.getFullYear();
    }
    else {
        document.getElementById('start_date').value = zeropad((dsdate.getMonth() +1), 2) + '/' +
                                                      zeropad(dsdate.getDate(), 2) + '/' +
                                                      dsdate.getFullYear();
        document.getElementById('end_date').value = zeropad((dedate.getMonth() +1), 2) + '/' +
                                                    zeropad(dedate.getDate(), 2) + '/' +
                                                    dedate.getFullYear();
    }
    DatePicked('nights', null);
}
        // Define functions
function track_event(action, label, value, boolean){
    if(typeof ga !== "function" || typeof __gaTracker !== "undefined") { return; }
    //Event: WidgetLoad
    if(AGA_on && action == 'WidgetLoad'){
        if(disable_WidgetLoad_event) {
            // if the WidgetLoad event is disabled,
            // we only track the JRS event.
            ga('ga_jrs_tracker.send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
            return;
        }
        if(GA_universal){
            ga('send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
            ga('ga_jrs_tracker.send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
        } else {
            _gaq.push(['_trackEvent', 'BookDirect', action, label, value, boolean]);
            ga('ga_jrs_tracker.send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
        }
    } else if (!AGA_on && action == 'WidgetLoad') {
        ga('ga_jrs_tracker.send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
    }

    //Event: WidgetSearch
    if(AGA_on && action == 'WidgetSearch'){
        if(GA_universal) {
            ga('send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
            ga('ga_jrs_tracker.send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
        } else {
            _gaq.push(['_trackEvent', 'BookDirect', action, label, value, boolean]);
            ga('ga_jrs_tracker.send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
        }
    } else if (!AGA_on && action == 'WidgetSearch') {
        ga('ga_jrs_tracker.send', 'event', 'BookDirect', action, label, value, { nonInteraction: boolean });
    }
}

function track_pageview(){
    if(typeof ga !== "function" || typeof __gaTracker !== "undefined") { return; }
    // widget_filename set in search_widget_js.php
    ga('ga_jrs_tracker.send', 'pageview', 'Widget/' + widget_filename);
}

function convert_jrs_date(date){
    date = date.split('/');
    month = date[0];
    day = date[1];
    year = date[2];
    yymmdd = year+'/'+month+'/'+day;
    return yymmdd;
}

function days_between(date1, date2) {
    // First we split the values to arrays date1[0] is the year, [1] the month and [2] the day
    date1 = date1.split('/');
    date2 = date2.split('/');
    // Now we convert the array to a Date object, which has several helpful methods
    date1 = new Date(date1[0], date1[1], date1[2]);
    date2 = new Date(date2[0], date2[1], date2[2]);
    // We use the getTime() method and get the unixtime (in milliseconds, but we want seconds, therefore we divide it through 1000)
    date1_unixtime = parseInt(date1.getTime() / 1000);
    date2_unixtime = parseInt(date2.getTime() / 1000);
    // This is the calculated difference in seconds
    var timeDifference = date2_unixtime - date1_unixtime;
    // in Hours
    var timeDifferenceInHours = timeDifference / 60 / 60;
    // and finaly, in days :)
    var timeDifferenceInDays = timeDifferenceInHours  / 24;
    return(timeDifferenceInDays);
}

// Click submit button send ga info
function send_ga_info(){
    var date = new Date();
    var day = date.getDate();
    var month = date.getMonth()+1;
    var year = date.getFullYear();
    var today = year+'/'+month+'/'+day;
    var start_date=document.getElementById("start_date").value;
    start_date = convert_jrs_date(start_date);
    var proximity = days_between(today, start_date);

    var end_date=document.getElementById("end_date").value;
    end_date = convert_jrs_date(end_date);

    var length_of_stay = days_between(start_date, end_date);

    track_event('WidgetSearch', 'Proximity: ' + proximity +
                                ' | checkin: ' + start_date +
                                ' | length of stay: ' + length_of_stay, 1, false);
}

if (window.addEventListener) {
    window.addEventListener('load', function() {
        var element = document.getElementById('jrs_search_submit');
        element.addEventListener('click', send_ga_info, false);
    }, false);
} else if (window.attachEvent) {
    window.attachEvent('load', function() {
        var element = document.getElementById('jrs_search_submit');
        element.attachEvent('onclick', send_ga_info);
    });
}

var _gaq = _gaq || [];
AGA_on = false;
GA_universal = false;
disable_WidgetLoad_event = false;

GA_universal = true;
//BEGIN Google Analytics Tracking Code
if(typeof ga == "undefined" && typeof __gaTracker == "undefined") {
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
}
if(typeof ga == "function" && typeof __gaTracker == "undefined") {
    // ga has to be a function and __gaTracker needs to be undefined.
    ga('create', '', {
                    'name' : 'ga_jrs_tracker',
                    'cookieName': '_ga_jrs'});

            // Below we add cloneID to all GA widgets
        // events when cloneID is present.
        ga('ga_jrs_tracker.set', 'dimension1', '526');
    }
// Not tracking page view here.

//END Google Analytics Tracking Code

// Track page view for jrs
//track_pageview();
// Track page load event for client
//track_event('WidgetLoad', widget_filename + '|' + window.location.href, 1, true);



