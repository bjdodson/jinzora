CurTicker_RIGHTTOLEFT = false;
CurTicker_SPEED = 2;
CurTicker_STYLE = "";
CurTicker_PAUSED = false;

function CurTicker_start() {
	return;
	CurTicker_CONTENT = document.getElementById("CurTicker").innerHTML;
	var CurTickerSupported = false;
	CurTicker_WIDTH = document.getElementById("CurTicker").style.width;
	var img = "<img src=CurTicker_space.gif width="+CurTicker_WIDTH+" height=0>";

	// Firefox
	if (navigator.userAgent.indexOf("Firefox")!=-1 || navigator.userAgent.indexOf("Safari")!=-1) {
		document.getElementById("CurTicker").innerHTML = "<TABLE  cellspacing='0' cellpadding='0' width='100%'><TR><TD nowrap='nowrap'>"+img+"<SPAN style='"+CurTicker_STYLE+"' ID='CurTicker_BODY' width='100%'>&nbsp;</SPAN>"+img+"</TD></TR></TABLE>";
		CurTickerSupported = true;
	}
	// IE
	if (navigator.userAgent.indexOf("MSIE")!=-1 && navigator.userAgent.indexOf("Opera")==-1) {
		document.getElementById("CurTicker").innerHTML = "<DIV nowrap='nowrap' style='width:100%;'>"+img+"<SPAN style='"+CurTicker_STYLE+"' ID='CurTicker_BODY' width='100%'></SPAN>"+img+"</DIV>";
		CurTickerSupported = true;
	}
	if(!CurTickerSupported) document.getElementById("CurTicker").outerHTML = ""; else {
		document.getElementById("CurTicker").scrollLeft = CurTicker_RIGHTTOLEFT ? document.getElementById("CurTicker").scrollWidth - document.getElementById("CurTicker").offsetWidth : 0;
		document.getElementById("CurTicker_BODY").innerHTML = CurTicker_CONTENT;
		document.getElementById("CurTicker").style.display="block";
		CurTicker_tick();
	}
}
ticking = false;
function CurTicker_tick() {
	if(!CurTicker_PAUSED) document.getElementById("CurTicker").scrollLeft += CurTicker_SPEED * (CurTicker_RIGHTTOLEFT ? -1 : 1);
	if(CurTicker_RIGHTTOLEFT && document.getElementById("CurTicker").scrollLeft <= 0) document.getElementById("CurTicker").scrollLeft = document.getElementById("CurTicker").scrollWidth - document.getElementById("CurTicker").offsetWidth;
	if(!CurTicker_RIGHTTOLEFT && document.getElementById("CurTicker").scrollLeft >= document.getElementById("CurTicker").scrollWidth - document.getElementById("CurTicker").offsetWidth) document.getElementById("CurTicker").scrollLeft = 0;
	window.setTimeout("CurTicker_tick()", 50);
}

 
NextTicker_RIGHTTOLEFT = false;
NextTicker_SPEED = 2;
NextTicker_STYLE = "";
NextTicker_PAUSED = false;

function NextTicker_start() {
	return;
	NextTicker_CONTENT = document.getElementById("NextTicker").innerHTML;
	var NextTickerSupported = false;
	NextTicker_WIDTH = document.getElementById("NextTicker").style.width;
	var img = "<img src=NextTicker_space.gif width="+NextTicker_WIDTH+" height=0>";

	// Firefox
	if (navigator.userAgent.indexOf("Firefox")!=-1 || navigator.userAgent.indexOf("Safari")!=-1) {
		document.getElementById("NextTicker").innerHTML = "<TABLE  cellspacing='0' cellpadding='0' width='100%'><TR><TD nowrap='nowrap'>"+img+"<SPAN style='"+NextTicker_STYLE+"' ID='NextTicker_BODY' width='100%'>&nbsp;</SPAN>"+img+"</TD></TR></TABLE>";
		NextTickerSupported = true;
	}
	// IE
	if (navigator.userAgent.indexOf("MSIE")!=-1 && navigator.userAgent.indexOf("Opera")==-1) {
		document.getElementById("NextTicker").innerHTML = "<DIV nowrap='nowrap' style='width:100%;'>"+img+"<SPAN style='"+NextTicker_STYLE+"' ID='NextTicker_BODY' width='100%'></SPAN>"+img+"</DIV>";
		NextTickerSupported = true;
	}
	if(!NextTickerSupported) document.getElementById("NextTicker").outerHTML = ""; else {
		document.getElementById("NextTicker").scrollLeft = NextTicker_RIGHTTOLEFT ? document.getElementById("NextTicker").scrollWidth - document.getElementById("NextTicker").offsetWidth : 0;
		document.getElementById("NextTicker_BODY").innerHTML = NextTicker_CONTENT;
		document.getElementById("NextTicker").style.display="block";
		NextTicker_tick();
	}
}

function NextTicker_tick() {
	if(!NextTicker_PAUSED) document.getElementById("NextTicker").scrollLeft += NextTicker_SPEED * (NextTicker_RIGHTTOLEFT ? -1 : 1);
	if(NextTicker_RIGHTTOLEFT && document.getElementById("NextTicker").scrollLeft <= 0) document.getElementById("NextTicker").scrollLeft = document.getElementById("NextTicker").scrollWidth - document.getElementById("NextTicker").offsetWidth;
	if(!NextTicker_RIGHTTOLEFT && document.getElementById("NextTicker").scrollLeft >= document.getElementById("NextTicker").scrollWidth - document.getElementById("NextTicker").offsetWidth) document.getElementById("NextTicker").scrollLeft = 0;
	window.setTimeout("NextTicker_tick()", 50);
}