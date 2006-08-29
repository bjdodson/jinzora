function openPopup(obj, boxWidth, boxHeight, resizeable, name){
		var sw = screen.width;
		var sh = screen.height;
		if (resizeable == true){
			var winOpt = "width=" + boxWidth + ",height=" + boxHeight + ",left=" + ((sw - boxWidth) / 2) + ",top=" + ((sh - boxHeight) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes";
		} else {
			var winOpt = "width=" + boxWidth + ",height=" + boxHeight + ",left=" + ((sw - boxWidth) / 2) + ",top=" + ((sh - boxHeight) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=no";
		}
		if (name == ""){
			name = 'jzPopup';
		}
		
		if (obj.href != null) {
			thisWin = window.open(obj.href,'PopupWindow',winOpt);
		} else {
			thisWin = window.open(obj,'PopupWindow',winOpt);
		}

		return thisWin;
	}		

function openMediaPlayer(myurl, boxWidth, boxHeight){
		var sw = screen.width;
		var sh = screen.height;
		var winOpt = "width=" + boxWidth + ",height=" + boxHeight + ",left=" + ((sw - boxWidth) / 2) + ",top=" + ((sh - boxHeight) / 2) + ",menubar=no,toolbar=no,location=no,directories=no,status=yes,scrollbars=no,resizable=no";
		thisWin = window.open(myurl,'embeddedPlayer',winOpt);
	  return thisWin;
	}
	
function SubmitForm(form, a, replace) {  
		if (replace) { 
			document.forms[form].action = a; 
		} else {
			document.forms[form].action = document.forms[form].action + a;  
		}
		document.forms[form].submit(); 
	}

function CheckBoxes(form, v){
		for (var i=0; i < document.forms[form].elements.length; i++) {
			var j = document.forms[form].elements[i];
			if (j.type == "checkbox") { j.checked = v; }
		}
	}

function variablePrompt(formname,varname,text) {
	var v = prompt(text);
	document.forms[formname].elements[varname].value = v; 
}