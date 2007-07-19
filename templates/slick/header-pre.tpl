<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<link rel="shortcut icon" href="{$fav_icon}">
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<title>
			{$site_title}
		</title>
		<link rel="alternate" type="application/rss+xml" title="Jinzora Most Played" href="{$rss_link}">
		{if $secure_links == "true"}
			{literal}
			<SCRIPT LANGUAGE="JavaScript1.1">
				function noContext(){
					return false;
				}
				document.oncontextmenu = noContext;
				// -->
			</script>
			{/literal}
		{/if}
	</head>	
	
	{php} $display = new jzDisplay(); echo returnJavascript(); {/php}
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
	
	<link rel="stylesheet" title="{$skin}" type="text/css" media="screen" href="{$css}">
	
	<table width="100%" cellspacing="0" cellpadding="0" align="left" border="0">
		<tr>
			<td>