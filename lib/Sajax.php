<?php	
if (!isset($SAJAX_INCLUDED)) {

	/*  
	 * GLOBALS AND DEFAULTS
	 *
	 */ 
	$sajax_debug_mode = 0;
	$sajax_export_list = array();
	$sajax_request_type = "GET";
	$sajax_remote_uri = "";
	
	/*
	 * CODE
	 *
	 */ 
	function sajax_init() {
	}
	
	function sajax_get_my_uri() {
	  global $my_frontend, $skin, $jz_language,$include_path;
		$str = $include_path . "ajax_request.php";
		$str .= "?frontend=" . $my_frontend . "&theme=" . $skin . "&language=" . $jz_language;

		return $str;
	}
	
	$sajax_remote_uri = sajax_get_my_uri();

	function sajax_handle_client_request() {
		global $sajax_export_list;
		
		$mode = "";
		
		if (! empty($_GET["rs"])) 
			$mode = "get";
		
		if (!empty($_POST["rs"]))
			$mode = "post";
			
		if (empty($mode)) 
			return;

		if ($mode == "get") {
			// Bust cache in the head
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			// always modified
			header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
			header ("Pragma: no-cache");                          // HTTP/1.0
			$func_name = $_GET["rs"];
			if (! empty($_GET["rsargs"])) 
				$args = $_GET["rsargs"];
			else
				$args = array();
		}
		else {
			$func_name = $_POST["rs"];
			if (! empty($_POST["rsargs"])) 
				$args = $_POST["rsargs"];
			else
				$args = array();
		}
		
		if (! in_array($func_name, $sajax_export_list))
			echo "-:$func_name not callable";
		else {
			echo "+:";
			$result = call_user_func_array($func_name, $args);
			echo $result;
		}
		exit;
	}
	
	function sajax_get_common_js() {
		global $sajax_debug_mode;
		global $sajax_request_type;
		global $sajax_remote_uri;
		
		$t = strtoupper($sajax_request_type);
		if ($t != "GET" && $t != "POST") 
			return "// Invalid type: $t.. \n\n";
		
		ob_start();
		?>
		
		// remote scripting library
		// (c) copyright 2005 modernmethod, inc
		var sajax_debug_mode = <?php echo $sajax_debug_mode ? "true" : "false"; ?>;
		var sajax_request_type = "<?php echo $t; ?>";
		
		function sajax_debug(text) {
			if (sajax_debug_mode)
				alert("RSD: " + text)
		}
 		function sajax_init_object() {
 			sajax_debug("sajax_init_object() called..")
 			
 			var A;
			try {
				A=new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					A=new ActiveXObject("Microsoft.XMLHTTP");
				} catch (oc) {
					A=null;
				}
			}
			if(!A && typeof XMLHttpRequest != "undefined")
				A = new XMLHttpRequest();
			if (!A)
				sajax_debug("Could not create connection object.");
			return A;
		}
		function sajax_do_call(func_name, args, directurl) {
			var i, x, n;
			var uri;
			var post_data;
			
			uri = "<?php echo $sajax_remote_uri; ?>";
			if (sajax_request_type == "GET") {
				if (uri.indexOf("?") == -1) 
					uri = uri + "?rs=" + escape(func_name);
				else
					uri = uri + "&rs=" + escape(func_name);
				for (i = 0; i < args.length-1; i++) 
					uri = uri + "&rsargs[]=" + escape(args[i]);
				uri = uri + "&rsrnd=" + new Date().getTime();
				post_data = null;
			} else {
				post_data = "rs=" + escape(func_name);
				for (i = 0; i < args.length-1; i++) 
					post_data = post_data + "&rsargs[]=" + escape(args[i]);
			}
			if (directurl != false) {
			  uri = directurl;
			  if (args.length == 2) {
			    post_data = args[0];
			  } else {
			    post_data = null;
			  }
			}

			x = sajax_init_object();
			x.open(sajax_request_type, uri, true);
			if (sajax_request_type == "POST") {
				x.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
				x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			}
			x.onreadystatechange = function() {
				if (x.readyState != 4) 
					return;
				sajax_debug("received " + x.responseText);
				
				var status;
				var data;
				if (directurl != false) {
				  status = "+";
				  data = x.responseText;
				} else {
				  status = x.responseText.charAt(0);
				  data = x.responseText.substring(2);
				}
				if (status == "-") 
					alert("Error: " + data);
				else  
					args[args.length-1](data);
			}
			x.send(post_data);
			sajax_debug(func_name + " uri = " + uri + "/post = " + post_data);
			sajax_debug(func_name + " waiting..");
			delete x;
		}

		function ajax_direct_call(url, cb_function) {
		  func = Array(1);
		  func[0] = cb_function;
		  sajax_do_call(false,func,url);
		}

		function ajax_submit_form(form, url, cb_function) {
		  sajax_request_type = "POST";
		  func = Array(2);
		  func[1] = cb_function;
		  func[0] = getFormValues(form);
		  sajax_do_call(false,func,url);
		}


function getFormValues(fobj)
{
  var str = "";
  var valueArr = null;
  var val = "";
  var cmd = "";
  for(var i = 0;i < fobj.elements.length;i++) {
    switch(fobj.elements[i].type) {
    case "checkbox":
      if (fobj.elements[i].checked) {
		str += escape(fobj.elements[i].name) +
	 "=" + escape(fobj.elements[i].value) + "&";
      }
      break;
    case "submit":
    case "button":
    case "image":
      if (document.pressed == fobj.elements[i].name) {
	str += escape(fobj.elements[i].name) +
	  "=" + escape(fobj.elements[i].value) + "&";
	document.pressed = null;
      }
      break;
    case "text":
    case "hidden":
      str += escape(fobj.elements[i].name) +
	"=" + escape(fobj.elements[i].value) + "&";
      break;
    case "select-one":
      str += escape(fobj.elements[i].name) +
	"=" + escape(fobj.elements[i].options[fobj.elements[i].selectedIndex].value) + "&";
      break;	  
    case "select-multiple":
      for (var j = 0; j < fobj.elements[i].options.length; j++) {
	if (fobj.elements[i].options[j].selected) {
	  str += escape(fobj.elements[i].name) + 
	    "=" + escape(fobj.elements[i].options[j].value) + "&";
	}
      }
      break;
    }
  }
  if (document.pressed != null) {
    
    if (null != document.pressedVal) {
      str += escape(document.pressed) + "=" + escape(document.pressedVal) + "&";
    } else {
      str += escape(document.pressed) + "=t&";
    }
  }
  str = str.substr(0,(str.length - 1));
  return str;  
}


		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function sajax_show_common_js() {
		echo sajax_get_common_js();
	}
	
	// javascript escape a value
	function sajax_esc($val)
	{
		return str_replace('"', '\\\\"', $val);
	}

	function sajax_get_one_stub($func_name) {
		ob_start();	
		?>
		
		// wrapper for <?php echo $func_name; ?>
		
		function x_<?php echo $func_name; ?>() {
			sajax_do_call("<?php echo $func_name; ?>",
				x_<?php echo $func_name; ?>.arguments, false);
		}
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function sajax_show_one_stub($func_name) {
		echo sajax_get_one_stub($func_name);
	}
	
	function sajax_export() {
		global $sajax_export_list;
		
		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$sajax_export_list[] = func_get_arg($i);
		}
	}
	
	$sajax_js_has_been_shown = 0;
	function sajax_get_javascript()
	{
		global $sajax_js_has_been_shown;
		global $sajax_export_list;
		
		$html = "";
		if (! $sajax_js_has_been_shown) {
			$html .= sajax_get_common_js();
			$sajax_js_has_been_shown = 1;
		}
		foreach ($sajax_export_list as $func) {
			$html .= sajax_get_one_stub($func);
		}
		return $html;
	}
	
	function sajax_show_javascript()
	{
		echo sajax_get_javascript();
	}
	
	$SAJAX_INCLUDED = 1;
}
?>
