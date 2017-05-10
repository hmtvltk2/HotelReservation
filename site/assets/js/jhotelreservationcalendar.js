var isOpera, isIE = false;
if(typeof(window.opera) != 'undefined'){isOpera = true;}
if(!isOpera && navigator.userAgent.indexOf('Internet Explorer') != - 1 ){ isIE = true;}
//fix both IE and Opera (adjust when they implement this method properly)
if(isOpera || isIE)
	{
	document.nativeGetElementById = document.getElementById;
	//redefine it!
	document.getElementById = function(id)
							{
								//alert('xxx');
								var elem = document.nativeGetElementById(id);
								if(elem)
								{
								  //verify it is a valid match!
									if(elem.id == id)
									{
									//valid match!
										return elem;
									} 
									else 
									{
									//not a valid match!
									//the non-standard, document.all array has keys for all name'd, and id'd elements
									//start at one, because we know the first match, is wrong!
										for(var i=1;i<document.all[id].length;i++)
										{
											if(document.all[id][i].id == id)
											{
												return document.all[id][i];
											}
										}
									}
								}
								return null;
							};
	}

function JHotelReservationCalendar(nameParent, Month,Year,pathImg, is_event)
{
	if( is_event ==null )
		is_event = true;
	Month 				= parseInt(Month,10);
	Year 				= parseInt(Year,10);
	
	//var names 			= new my_array("January","February","March","April","May","June","July","August","September","October","November","December");
	var days 	 		= new my_array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	//var dow   			= new my_array("Sun","Mon","Tue","Wed","Thu","Fri","Sat","","","","","");
	var today     		= new Date();
	var thisDay   		= today.getDate();
	var thisMonth 		= today.getMonth();
	var thisYear  		= y2k(today.getYear());
	var form 			= document.getElementById('userForm');
	var thisObjParent 	= document.getElementById( nameParent ) ;

	if(thisObjParent==null)
		return;
	firstDay = new Date(Year,Month-1,1);
	startDay = firstDay.getDay();

	
	
	if (((Year % 4 == 0) && (Year % 100 != 0)) || (Year % 400 == 0))
	  days[1] = 29; 
	else
	  days[1] = 28;

	
	strBuffer = '';
	strBuffer += "<TABLE CELLSPACING=0 CELLPADDING=0 bgcolor='#F1F1E5' style='border:1px solid #D2D2B9;' heigth='250px'><TR>";

	d1 = new Date( Year, Month-1, 1 );
	d2 = new Date( thisYear, thisMonth, 1 );
	strBuffer += "<TR>";
	strBuffer += "	<TD ALIGN=CENTER VALIGN=MIDDLE bgcolor='#D2D2B9'>";
	if( d1 > d2 )
	{
		strBuffer += "	<img"; 
		strBuffer += "		src 				=	\""+pathImg+"\arrow_left.gif\"";
		strBuffer += "		width				= 	\"16\""; 
		strBuffer += "		height				=	\"16\"";
		strBuffer += "		id					= 	'"+nameParent+"_back'"; 
		strBuffer += "		name				=	'"+nameParent+"_back'"; 
		strBuffer += "		border				=	\"0\"";
		strBuffer += "		onmouseover			=	\"this.style.cursor='hand';this.style.cursor='pointer'\"";
		strBuffer += "		onmouseout			=	\"this.style.cursor='default'\"";
		if(is_event==true)
		{
			strBuffer += "		onclick				= 	\"";
			strBuffer += "							 		JHotelReservationCalendar('"+nameParent+"',  '"+(parseInt(Month,10)==1? 12 : parseInt(Month,10)-1)+"','"+(parseInt(Month,10)==1? parseInt(Year,10)-1 : Year)+"','"+pathImg+"');";
			strBuffer += "							 		createControls('"+nameParent+"', '1', '"+(parseInt(Month,10)==1? 12 : parseInt(Month,10)-1)+"','"+(parseInt(Month,10)==1? parseInt(Year,10)-1 : Year)+"','"+pathImg+"');";
			strBuffer += "							 		updateCalendars('"+nameParent+"', '"+pathImg+"');";
			strBuffer += "							 		markSelectInterval();";
			strBuffer += "						 	\"";
		}
		strBuffer += "	/>";
	}
	strBuffer += "</TD>";
	strBuffer += "<TD  bgcolor='#D2D2B9' height=25 align=center colspan=5><B>"+myMonths[Month-1] + " / " + Year+"</B></TD>";
	strBuffer += "<TD ALIGN=CENTER valign='middle' bgcolor='#D2D2B9'>";
	strBuffer += "	<img"; 
	strBuffer += "		src 				=	\""+pathImg+"\arrow_right.gif\"";
	strBuffer += "		width				= 	\"16\""; 
	strBuffer += "		height				=	\"16\""; 
	strBuffer += "		id					= 	'"+nameParent+"_next'"; 
	strBuffer += "		name				=	'"+nameParent+"_next'"; 
	strBuffer += "		border				=	\"0\"";
	strBuffer += "		onmouseover			=	\"this.style.cursor='hand';this.style.cursor='pointer'\"";
	strBuffer += "		onmouseout			=	\"this.style.cursor='default'\"";
	if(is_event==true)
	{
		strBuffer += "		onclick				= 	\"";
		strBuffer += "							 		JHotelReservationCalendar('"+nameParent+"', '"+(parseInt(Month,10)==12? 1 : parseInt(Month,10)+1)+"','"+(parseInt(Month,10)==12? parseInt(Year,10)+1 : Year)+"','"+pathImg+"');";
		strBuffer += "							 		createControls('"+nameParent+"', '1', '"+(parseInt(Month,10)==12? 1 : parseInt(Month,10)+1)+"','"+(parseInt(Month,10)==12? parseInt(Year,10)+1 : Year)+"','"+pathImg+"');";
		strBuffer += "							 		updateCalendars('"+nameParent+"', '"+pathImg+"');";
		strBuffer += "							 		markSelectInterval();";
		strBuffer += "							 	\"";
	}
	//trBuffer += "		onclick				= 		updateCalendars('"+nameParent+"', '"+pathImg+"');";

	strBuffer += "	/>";
	strBuffer += "</TD>";
	strBuffer += "</TR>";
	strBuffer += "<TR>";
	for (i=0; i<7; i++)
	{
	
		strBuffer += "<TD WIDTH=50 ALIGN=CENTER VALIGN=MIDDLE bgcolor='#ffffff' style='border-top:1px solid #D2D2B9;border-bottom:1px solid #D2D2B9'>";
		strBuffer += "	<B>" + myDays[i].substr(0,3) + "</B>";
		strBuffer += "</TD>";
	}
	strBuffer += "</TR>";
	strBuffer += "<TR ALIGN=CENTER VALIGN=MIDDLE>";

	var column = 0;
	var lastMonth = Month - 1;
	if (lastMonth == -1)
		lastMonth = 11;
	for (i=0; i<startDay; i++)
	{
		strBuffer += "<TD style='color:#FFFFFF'>&nbsp;</TD>";
		column++;
	}

	for (i=1; i<=days[Month-1]; i++)
	{
		d1 = new Date( thisYear, thisMonth, thisDay );
		d2 = new Date( Year, Month-1, i );
		if( d1 > d2 )
			strBuffer += "<TD style='color:#000000;' bgcolor='#D2D2B9' id='"+nameParent+"_day_"+ i + "' name='"+nameParent+"_day_"+ i + "' >" + i + "</TD>";
		else if ((i == thisDay)  && (Month-1 == thisMonth) && (Year == thisYear))
		{
			strBuffer += "<TD id='"+nameParent+"_day_"+i + "' name='"+nameParent+"_day_"+i + "' ";
			if(is_event==true)
			{
				strBuffer += "	onclick				= 	\"";
				strBuffer += "							 	selectDay('" + nameParent + "', '" + i + "');";
				strBuffer += "						 		markSelectInterval();";
				strBuffer += "						 	\"";
			}
			strBuffer += "	onmouseover =\"this.style.cursor='hand';this.style.cursor='pointer'\" onmouseout=	\"this.style.cursor='default'\" style='background-color:#000000;color:#FFFF00' title='Current day'><B>"+i+"</B></TD>";
		}
		else
		{
			strBuffer += "<TD style='color:#000000;bgcolor:#F1F1E5' id='"+nameParent+"_day_"+i + "' name='"+nameParent+"_day_"+i + "'  onmouseover =\"this.style.cursor='hand';this.style.cursor='pointer'\" onmouseout=	\"this.style.cursor='default'\"";
			if(is_event==true)
			{
				strBuffer += "	onclick				= 	\"";
				strBuffer += "							 	selectDay('" + nameParent + "', '" + i + "');";
				strBuffer += "						 		markSelectInterval();";
				strBuffer += "						 	\"";
			}
			strBuffer += ">"+i+"</TD>";
		}
		column++;
		if (column == 7)
		{
			strBuffer += "</TR>";
			if( i<days[Month-1])
			strBuffer += "<TR ALIGN=CENTER VALIGN=MIDDLE>";
			column = 0;
		}
	}

	if (column > 0)
	{
		for (i=1; column<7; i++)
		{
			strBuffer += "<TD style='color:#DD00DDD'>&nbsp;</TD>";
			column++;
		}
	}
	strBuffer += "</TR></TABLE>";

	// alert(objParent.innerHTML);
	
	thisObjParent.innerHTML = strBuffer;
	// alert(objParent.innerHTML);
}
function y2k(number) { return (number < 1000) ? number + 1900 : number; }


function my_array(m0, m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11)
{
	this[0] = m0; this[1] = m1; this[2]  = m2;  this[3]  = m3;
	this[4] = m4; this[5] = m5; this[6]  = m6;  this[7]  = m7;
	this[8] = m8; this[9] = m9; this[10] = m10; this[11] = m11;
}

function createControls(nameCalendar, d, m, y, pathImg, force, recreateYear )
{
	if( recreateYear ==null )
		force = false;
		
	if( force ==null )
		force = false;
	
	
	
	var today     		= new Date();

	var thisDay   		= today.getDate();
	var thisMonth 		= today.getMonth();
	var thisYear  		= y2k(today.getYear());
	
	form 				= document.getElementById('userForm');
	
	
	if( nameCalendar =='td_data_calendar_1') //1
	{
		var dNameObj 	= form.elements["day_name_start"];
		var yearObj		= form.elements["year_start"];
		var monthObj	= form.elements["month_start"];
		var dayObj		= form.elements["day_start"];
		
	}
	else if( nameCalendar =='td_data_calendar_2') //1
	{
		var dNameObj 	= form.elements["day_name_end"];
		var yearObj		= form.elements["year_end"];
		var monthObj	= form.elements["month_end"];
		var dayObj		= form.elements["day_end"];
	}
	
	if( recreateYear  )
	{
		var var_Year = yearObj.value;
		cleanSelect(yearObj);
		for( i = thisYear; i <= thisYear + 10 ; i++ )
		{
			var dateObj 		= new Date(y,i-1,1);
			var elOptNew 		= document.createElement('OPTION');
			elOptNew.text 		= i;
			elOptNew.value 		= i;
			//elOptNew.disabled 	= dateObj < today ? true : false;
			
			if(
				i == var_Year
			)
				elOptNew.selected 	= true;
			else
				elOptNew.disabled 	= false;
			
			
			// if( thisYear == y && m -1 > thisMonth )
			// {
				// alert(5);
			// }
			
			yearObj.options.add(elOptNew);
		}
	}
	
	createMonths 	= force;
	createDays 		= force;

	
	if( y != yearObj.value )
	{
		createMonths 	= true;
		createDays 		= true;
	}
	
	if( m != thisMonth )
	{
		createDays 		= true;
	}
	
	if( yearObj.value != y )
	{
		yearObj.options[ y - parseInt(thisYear,10)].selected = true;
		
	}
	var nMonthSelected = -1;
	
	if( createMonths == false )
	{
		if( parseInt(monthObj.value,10) != m )
		{
			if( thisMonth > m - 1 && thisYear == y )
			{
				monthObj.options[ thisMonth ].selected = true;
				nMonthSelected = thisMonth;
			}
			else
			{
				monthObj.options[ m - 1 ].selected = true;
				nMonthSelected = m - 1;
			}
		}
	}
	else
	{
		cleanSelect(monthObj);
		//alert(thisMonth);
		for( i = 1; i <= 12; i++ )
		{
			var dateObj 		= new Date(y,i-1,1);
			var elOptNew 		= document.createElement('OPTION');
			
			var myMonth	 	= dateObj.getMonth();
			var nameMonth 	= myMonths[myMonth];
			elOptNew.text	= nameMonth;//.substring(0,3);
			//alert(myMonth + "" + nameMonth );
		
			//elOptNew.text 		= dateObj.getMonthName().substring(0,3);
			elOptNew.value 		= i;
			//elOptNew.disabled 	= dateObj < today ? true : false;
			
			if(
				(thisYear > y )
				||
				(thisYear == y && parseInt(thisMonth,10) > i - 1)
			)
				elOptNew.disabled 	= true;
			else
				elOptNew.disabled 	= false;
			
			
			// if( thisYear == y && m -1 > thisMonth )
			// {
				// alert(5);
			// }
			
			if( nMonthSelected == -1 )
			{
				if( thisYear == y && m == i && thisMonth <= i - 1)
				{
					elOptNew.selected 	= true;
					nMonthSelected		= i;
					
				}
				else if( thisYear == y && m < thisMonth && thisMonth == i - 1)
				{
					elOptNew.selected 	= true;
					nMonthSelected		= i;
				}
				else if(thisYear < y && m == i )
				{
					elOptNew.selected 	= true;
					nMonthSelected		= i;
				}
			}
			
			
			
			monthObj.options.add(elOptNew);
		}
		if( nMonthSelected != -1 )
		{
			m = nMonthSelected;
		}
	}
	
	if( createDays == false )
	{
		if( dayObj.value != d )
		{
			dayObj.options[ d-1 ].selected = true;
		}
	}
	else
	{
	
		var crtDay = parseInt(dayObj.value);
		//alert(thisDay + "=>" + thisMonth+" <>"+(m-1));
		if( 
			(crtDay < thisDay && thisYear == y && thisMonth <= m-1)
			||
			(thisYear <= y && thisMonth < m-1)
			||
			(thisYear < y )
		)
		{
			//alert(0);
			crtDay = thisDay;
		}
		cleanSelect(dayObj);
		maxDays = daysInMonth(y, m);
		for( i = 1; i <= maxDays; i++ )
		{
			var dateObj 		= new Date(y,m-1,i,0,0,0,0);
			var elOptNew 		= document.createElement('OPTION');
			elOptNew.text 		= i < 10 ? "0"+i : i;
			elOptNew.value 		= i;
			
			
			if(
				thisYear > y
				||
				(thisYear == y && thisMonth > m-1)
				||
				(thisYear == y && thisMonth == (m-1) && i < thisDay )
			)
			{
				elOptNew.disabled 	= true;
			}
			else
			{
				elOptNew.disabled 	= false;
			}
			if( ( thisYear < y || (thisYear == y && thisMonth < m-1 ) ) && d == i )
			{
				elOptNew.selected 	= true;
			}
			else if( thisYear == y && thisMonth == m-1 && i==crtDay )
				elOptNew.selected 	= true;
			
				
			dayObj.options.add(elOptNew);
		}
	}
	
	dataCtrl = new Date( parseInt(yearObj.value,10), parseInt(monthObj.value,10)-1, parseInt(dayObj.value,10) );
	var myDay	 	= dataCtrl.getDay();
	var nameDay 	= myDays[myDay];
	if( nameDay.length > 0 )
		dNameObj.value = nameDay.substring(0,3);
	
	/*	
	var nameDay = dataCtrl.getDayName();
	if( nameDay.length > 0 )
		dNameObj.value = nameDay.substring(0,3);
	*/
	
	
}

function updateCalendars(nameCalendar,pathImg, noCalendar)
{
	if(noCalendar==null)
		noCalendar = false;
	var form 		= document.getElementById('userForm');
	
	
	yearObjS		= form.elements["year_start"];
	monthObjS		= form.elements["month_start"];
	dayObjS			= form.elements["day_start"];
	
	yearObjE		= form.elements["year_end"];
	monthObjE		= form.elements["month_end"];
	dayObjE			= form.elements["day_end"];
	
	if( nameCalendar =='td_data_calendar_1')
	{
		y				= parseInt(yearObjS.value,10);
		m				= parseInt(monthObjS.value,10);
		d				= parseInt(dayObjS.value,10);
		//alert(m);
		if( 
			parseInt(yearObjE.value,10) < y 
			||
			(
				parseInt(yearObjE.value,10) 	== y 
				&&
				parseInt(monthObjE.value,10) 	< m  
			)
		)
		{
			objNext = document.getElementById( 'td_data_calendar_2_next');
			if(noCalendar==false)
				JHotelReservationCalendar('td_data_calendar_2', m, y, pathImg);
		}
		
		dataS	=	new Date( y,m-1,d);
		dataE	=	new Date( yearObjE.value,parseInt(monthObjE.value,10)-1,dayObjE.value);
		//alert(m);
		//alert(d);
		if( dataS > dataE )
		{
			createControls('td_data_calendar_2', '1', m, y,pathImg);
		}
	}
	else if( nameCalendar =='td_data_calendar_2') //1
	{
		y				= parseInt(yearObjE.value,10);
		m				= parseInt(monthObjE.value,10);
		d				= parseInt(dayObjE.value,10);
		
		dataE	=	new Date( y,m-1,d);
		dataS	=	new Date( yearObjS.value,parseInt(monthObjS.value,10)-1,dayObjS.value);
		
		if( dataS > dataE )
		{
			if(noCalendar==false)
				JHotelReservationCalendar('td_data_calendar_1', m, y, pathImg);
			createControls('td_data_calendar_1', '1', m, y,pathImg);
		}
		else
		{
			if(noCalendar==false)
				JHotelReservationCalendar('td_data_calendar_2', m, y, pathImg);
		}
	}
}

function markSelectInterval()
{
	var today     		= new Date();
	var thisDay   		= today.getDate();
	var thisMonth 		= today.getMonth();
	var thisYear  		= y2k(today.getYear());
	
	var form 			= document.getElementById('userForm');
	yearS				= form.elements["year_start"];
	monthS				= form.elements["month_start"];
	dayS				= form.elements["day_start"];
	
	yearE				= form.elements["year_end"];
	monthE				= form.elements["month_end"];
	dayE				= form.elements["day_end"];
	
	yS 					= yearS.value;
	mS 					= monthS.value;
	dS 					= dayS.value;
	var dataStart 		= new Date(yS,mS-1,dS);

	yE 					= yearE.value;
	mE 					= monthE.value;
	dE 					= dayE.value;
	var dataEnd 		= new Date(yE,mE-1,dE);
	
	//td_data_calendar_1
	maxDays = daysInMonth(yS, mS);
	for( i = 1; i <= maxDays; i++ )
	{
		var dataS = new Date(yS,mS-1,i);
		tdDay = document.getElementById("td_data_calendar_1_day_" + i ); 
		
		if( dataS <= dataEnd && dataS >= dataStart)
		{
			if(tdDay)
				tdDay.style.backgroundColor='#CCCCCC';
			continue;
		}
		else if(tdDay)
		{
			//alert(5);
			if( thisYear == yS && thisMonth == mS-1 && thisDay == i )
			{
				tdDay.style.backgroundColor='#D2D2B9';
				tdDay.style.color='#FFFFFF';
			}
			else
				tdDay.style.backgroundColor='#F1F1E5';
		}
	}
	
	//td_data_calendar_2
	maxDays = daysInMonth(yE, mE);
	for( i = 1; i <= maxDays; i++ )
	{
		var dataE = new Date(yE,mE-1,i);
		tdDay = document.getElementById( "td_data_calendar_2_day_" + i ); 
		if( dataE <= dataEnd && dataE >= dataStart)
		{
			if(tdDay)
				tdDay.style.backgroundColor='#CCCCCC';
			continue;
		}
		else if(tdDay)
		{
			//alert(5);
			if( thisYear == yE && thisMonth == mE-1 && thisDay == i )
			{
				tdDay.style.backgroundColor='#D2D2B9';
				tdDay.style.color='#FFFFFF';
			}
			else
				tdDay.style.backgroundColor='#F1F1E5';
		}
	}
	
}

function cleanSelect( fieldSelect )
{
	for (i = fieldSelect.length - 1; i>=0; i--) 
	{
		fieldSelect.remove(i);
	}
}

function selectDay(nameCalendar, d)
{
	var form 		= document.getElementById('userForm');
	
	var dNameObjS 	= form.elements["day_name_start"];
	var yObjS	 	= form.elements["year_start"];
	var mObjS	 	= form.elements["month_start"];	
	var dObjS	 	= form.elements["day_start"];	
		
	
	var dNameObjE 	= form.elements["day_name_end"];
	var yObjE	 	= form.elements["year_end"];
	var mObjE	 	= form.elements["month_end"];	
	var dObjE	 	= form.elements["day_end"];	
	
	if( nameCalendar=='td_data_calendar_2')
	{
		if( d != null )
			dObjE.options[d-1].selected = true;
		if( 
			parseInt(yObjS.value,10) == parseInt(yObjE.value,10)
			&&
			parseInt(mObjS.value,10) == parseInt(mObjE.value,10)
			&&
			parseInt(dObjS.value,10) > parseInt(dObjE.value,10)
		)
		{
			dObjS.options[ parseInt(dObjE.value,10)-1 ].selected = true;
		}
		
		dataEnd = new Date( parseInt(yObjE.value,10), parseInt(mObjE.value,10)-1, parseInt(dObjE.value,10) );
		var myDay	 = dataEnd.getDay();
		var nameDay = myDays[myDay];
		if( nameDay.length > 0 )
			dNameObjE.value = nameDay.substring(0,3);
		
		// var nameDay = dataEnd.getDayName();
		// if( nameDay.length > 0 )
			// dNameObjE.value = nameDay.substring(0,3);

	}
	else if( nameCalendar=='td_data_calendar_1')
	{
		if( d != null )
			dObjS.options[d-1].selected = true;
		
		if( 
			parseInt(yObjS.value,10) == parseInt(yObjE.value,10)
			&&
			parseInt(mObjS.value,10) == parseInt(mObjE.value,10)
			&&
			parseInt(dObjS.value,10) > parseInt(dObjE.value,10)
		)
		{
			dObjE.options[ parseInt(dObjS.value,10)-1 ].selected = true;
			
		}
		dataStart = new Date( parseInt(yObjS.value,10), parseInt(mObjS.value,10)-1, parseInt(dObjS.value,10) );
		var myDay	 = dataStart.getDay();
		var nameDay = myDays[myDay];
		if( nameDay.length > 0 )
			dNameObjS.value = nameDay.substring(0,3);
		
		// var nameDay = dataStart.getDayName();
		// if( nameDay.length > 0 )
			// dNameObjS.value = nameDay.substring(0,3);
	}
}

															