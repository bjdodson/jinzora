<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
?>
<style>
	/* These are the important items for the menu */
/* This one should be the same as the page jz_header_table_outer background color */
.jzMenuItemHover,.jzMenuItemActive{
   background-color:	<?php echo jz_bg_color; ?>;
}
/* This should be the same as the page backgound */
.jzMenuItem{
  background-color:	<?php echo jz_pg_bg_color; ?>;
}
/* This one should be the same as the page jz_header_table_outer background color */
.jzMenuItem .jzMenuFolderLeft,
.jzMenuItem .jzMenuItemLeft{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	2px;
	padding-right:	3px;

	white-space:	nowrap;

	border:		0;
	background-color:	<?php echo jz_bg_color; ?>;
}
/* This one should be the same as the page jz_header_table_outer background color */
.jzMainItemHover,.jzMainItemActive
{
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
   font-size: 11px;
   background-color:	<?php echo jz_bg_color; ?>;
}
 
 
 
 
 
 
 /* jzMenu Style Sheet */

.jzMenu,.jzSubMenuTable
{
	font-family:	Arial, sans-serif;
	font-size:	11px;

	padding:	0;

	white-space:	nowrap;
	cursor:		default;
}

.jzSubMenu
{
	position:	absolute;
	visibility:	hidden;

	/*
	   Netscape/Mozilla renders borders by increasing
	   their z-index.  The following line is necessary
	   to cover any borders underneath
	*/
	z-index:	100;
	border:		0;
	padding:	0;

	overflow:	visible;
	border:		1px solid #8C867B;

	filter:progid:DXImageTransform.Microsoft.Shadow(color=#BDC3BD, Direction=135, Strength=4);
}

.jzSubMenuTable
{
	overflow:	visible;
}

.jzMainItem,.jzMainItemHover,.jzMainItemActive,
.jzMenuItem,.jzMenuItemHover,.jzMenuItemActive
{
	border:		0;
	cursor:		default;
	white-space:	nowrap;
}

.jzMainItem
{
	font-weight: bold;
	font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
}


/* horizontal main menu */

.jzMainItem
{
	padding:	4px;
	border:		0;
}

td.jzMainItemHover,td.jzMainItemActive
{
	padding:	4px;
	border:		0px solid #808080;
}

.jzMainFolderLeft,.jzMainItemLeft,
.jzMainFolderText,.jzMainItemText,
.jzMainFolderRight,.jzMainItemRight
{
	background-color:	inherit;
}

/* vertical main menu sub components */

td.jzMainFolderLeft,td.jzMainItemLeft
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	0px;
	padding-right:	2px;
	border-top:	1px solid #3169C6;
	border-bottom:	1px solid #3169C6;
	border-left:	1px solid #3169C6;
	background-color:	inherit;
}

td.jzMainFolderText,td.jzMainItemText
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	5px;
	padding-right:	5px;

	border-top:	1px solid #3169C6;
	border-bottom:	1px solid #3169C6;

	background-color:	inherit;
	white-space:	nowrap;
}

td.jzMainFolderRight,td.jzMainItemRight
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	0px;
	padding-right:	0px;
	border-top:	1px solid #3169C6;
	border-bottom:	1px solid #3169C6;
	border-right:	1px solid #3169C6;
	background-color:	inherit;
}

tr.jzMainItem td.jzMainFolderLeft,
tr.jzMainItem td.jzMainItemLeft
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	1px;
	padding-right:	2px;

	white-space:	nowrap;

	border:		0;
	background-color:	inherit;
}

tr.jzMainItem td.jzMainFolderText,
tr.jzMainItem td.jzMainItemText
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	5px;
	padding-right:	5px;

	border:		0;
	background-color:	inherit;
}

tr.jzMainItem td.jzMainItemRight,
tr.jzMainItem td.jzMainFolderRight
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	0px;
	padding-right:	1px;

	border:		0;
	background-color:	inherit;
}

/* sub menu sub components */

.jzMenuFolderLeft,.jzMenuItemLeft
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	1px;
	padding-right:	3px;
	border-top:	1px solid #d5d5d5;
	border-bottom:	1px solid #d5d5d5;
	border-left:	1px solid #d5d5d5;
	background-color:	inherit;
	white-space:	nowrap;
}

.jzMenuFolderText,.jzMenuItemText
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	5px;
	padding-right:	5px;
	border-top:	1px solid #d5d5d5;
	border-bottom:	1px solid #d5d5d5;
	background-color:	inherit;
	white-space:	nowrap;
}

.jzMenuFolderRight,.jzMenuItemRight
{
	padding-top:	2px;
	padding-bottom:	2px;
	padding-left:	0px;
	padding-right:	0px;
	border-top:	1px solid #d5d5d5;
	border-bottom:	1px solid #d5d5d5;
	border-right:	1px solid #d5d5d5;
	background-color:	inherit;
	white-space:	nowrap;
}



.jzMenuItem .jzMenuFolderText,
.jzMenuItem .jzMenuItemText
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	5px;
	padding-right:	5px;

	border:		0;
	background-color:	inherit;
}

.jzMenuItem .jzMenuFolderRight,
.jzMenuItem .jzMenuItemRight
{
	padding-top:	3px;
	padding-bottom:	3px;
	padding-left:	0px;
	padding-right:	1px;

	border:		0;
	background-color:	inherit;
}

/* menu splits */

.jzMenuSplit
{
	margin:		2px;
	height:		1px;
	overflow:	hidden;
	background-color:	inherit;
	border-top:	1px solid #C6C3BD;
}
</style>
<script type="text/javascript">
	<!--//
	/*
	JSCookMenu v1.23.  (c) Copyright 2002 by Heng Yuan
	
	Permission is hereby granted, free of charge, to any person obtaining a
	copy of this software and associated documentation files (the "Software"),
	to deal in the Software without restriction, including without limitation
	the rights to use, copy, modify, merge, publish, distribute, sublicense,
	and/or sell copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included
	in all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	ITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	DEALINGS IN THE SOFTWARE.
	*/
	
	// Globals
	var _cmIDCount = 0;
	var _cmIDName = 'cmSubMenuID';		// for creating submenu id
	
	var _cmTimeOut = null;			// how long the menu would stay
	var _cmCurrentItem = null;		// the current menu item being selected;
	
	var _cmNoAction = new Object ();	// indicate that the item cannot be hovered.
	var _cmSplit = new Object ();		// indicate that the item is a menu split
	
	var _cmItemList = new Array ();		// a simple list of items
	
	// default node properties
	var _cmNodeProperties =
	{
		// main menu display attributes
		//
		// Note.  When the menu bar is horizontal,
		// mainFolderLeft and mainFolderRight are
		// put in <span></span>.  When the menu
		// bar is vertical, they would be put in
		// a separate TD cell.
	
		// HTML code to the left of the folder item
		mainFolderLeft: '',
		// HTML code to the right of the folder item
		mainFolderRight: '',
		// HTML code to the left of the regular item
		mainItemLeft: '',
		// HTML code to the right of the regular item
		mainItemRight: '',
	
		// sub menu display attributes
	
		// HTML code to the left of the folder item
		folderLeft: '',
		// HTML code to the right of the folder item
		folderRight: '',
		// HTML code to the left of the regular item
		itemLeft: '',
		// HTML code to the right of the regular item
		itemRight: '',
		// cell spacing for main menu
		mainSpacing: 0,
		// cell spacing for sub menus
		subSpacing: 0,
		// auto dispear time for submenus in milli-seconds
		delay: 300
	};
	
	//////////////////////////////////////////////////////////////////////
	//
	// Drawing Functions and Utility Functions
	//
	//////////////////////////////////////////////////////////////////////
	
	//
	// produce a new unique id
	//
	function cmNewID ()
	{
		return _cmIDName + (++_cmIDCount);
	}
	
	//
	// return the property string for the menu item
	//
	function cmActionItem (item, prefix, isMain, idSub, orient, nodeProperties)
	{
		// var index = _cmItemList.push (item) - 1;
		_cmItemList[_cmItemList.length] = item;
		var index = _cmItemList.length - 1;
		idSub = (!idSub) ? 'null' : ('\'' + idSub + '\'');
		orient = '\'' + orient + '\'';
		prefix = '\'' + prefix + '\'';
		return ' onmouseover="cmItemMouseOver (this,' + prefix + ',' + isMain + ',' + idSub + ',' + orient + ',' + index + ')" onmouseout="cmItemMouseOut (this,' + nodeProperties.delay + ')" onmousedown="cmItemMouseDown (this,' + index + ')" onmouseup="cmItemMouseUp (this,' + index + ')"';
	}
	
	function cmNoActionItem (item, prefix)
	{
		return item[1];
	}
	
	function cmSplitItem (prefix, isMain, vertical)
	{
		var classStr = 'cm' + prefix;
		if (isMain)
		{
			classStr += 'Main';
			if (vertical)
				classStr += 'HSplit';
			else
				classStr += 'VSplit';
		}
		else
			classStr += 'HSplit';
		var item = eval (classStr);
		return cmNoActionItem (item, prefix);
	}
	
	//
	// draw the sub menu recursively
	//
	function cmDrawSubMenu (subMenu, prefix, id, orient, nodeProperties)
	{
		var str = '<span class="' + prefix + 'SubMenu" id="' + id + '"><table summary="sub menu" cellspacing="' + nodeProperties.subSpacing + '" class="' + prefix + 'SubMenuTable">';
		var strSub = '';
	
		var item;
		var idSub;
		var hasChild;
	
		var i;
	
		var classStr;
	
		for (i = 5; i < subMenu.length; ++i)
		{
			item = subMenu[i];
			if (!item)
				continue;
	
			hasChild = (item.length > 5);
			idSub = hasChild ? cmNewID () : null;
	
			str += '<tr class="<?php echo $jz_MenuItem; ?>"' + cmActionItem (item, prefix, 0, idSub, orient, nodeProperties) + '>';
	
			if (item == _cmSplit)
			{
				str += cmSplitItem (prefix, 0, true);
				str += '</tr>';
				continue;
			}
	
			if (item[0] == _cmNoAction)
			{
				str += cmNoActionItem (item, prefix);
				str += '</tr>';
				continue;
			}
	
			classStr = prefix + 'Menu';
			classStr += hasChild ? 'Folder' : 'Item';
	
			str += '<td class="' + classStr + 'Left">';
	
			if (item[0] != null && item[0] != _cmNoAction)
				str += item[0];
			else
				str += hasChild ? nodeProperties.folderLeft : nodeProperties.itemLeft;
	
			str += '<td class="' + classStr + 'Text">' + item[1];
	
			str += '<td class="' + classStr + 'Right">';
	
			if (hasChild)
			{
				str += nodeProperties.folderRight;
				strSub += cmDrawSubMenu (item, prefix, idSub, orient, nodeProperties);
			}
			else
				str += nodeProperties.itemRight;
			str += '</td></tr>';
		}
	
		str += '</table></span>' + strSub;
		return str;
	}
	
	//
	// The function that builds the menu inside the specified element id.
	//
	// @param	id	id of the element
	//		orient	orientation of the menu in [hv][ab][lr] format
	//		menu	the menu object to be drawn
	//		nodeProperties	properties for each menu node
	//
	function cmDraw (id, menu, orient, nodeProperties, prefix)
	{
		var obj = cmGetObject (id);
	
		if (!nodeProperties)
			nodeProperties = _cmNodeProperties;
		if (!prefix)
			prefix = '';
	
		var str = '<table summary="main menu" class="' + prefix + 'Menu" cellspacing="' + nodeProperties.mainSpacing + '">';
		var strSub = '';
	
		if (!orient)
			orient = 'hbr';
	
		var orientStr = String (orient);
		var orientSub;
		var vertical;
	
		// draw the main menu items
		if (orientStr.charAt (0) == 'h')
		{
			// horizontal menu
			orientSub = 'v' + orientStr.substr (1, 2);
			str += '<tr>';
			vertical = false;
		}
		else
		{
			// vertical menu
			orientSub = 'v' + orientStr.substr (1, 2);
			vertical = true;
		}
	
		var i;
		var item;
		var idSub;
		var hasChild;
	
		var classStr;
	
		for (i = 0; i < menu.length; ++i)
		{
			item = menu[i];
	
			if (!item)
				continue;
	
			str += vertical ? '<tr' : '<td';
			str += ' class="' + prefix + 'MainItem"';
	
			hasChild = (item.length > 5);
			idSub = hasChild ? cmNewID () : null;
	
			str += cmActionItem (item, prefix, 1, idSub, orient, nodeProperties) + '>';
	
			if (item == _cmSplit)
			{
				str += cmSplitItem (prefix, 1, vertical);
				str += vertical? '</tr>' : '</td>';
				continue;
			}
	
			if (item[0] == _cmNoAction)
			{
				str += cmNoActionItem (item, prefix);
				str += vertical? '</tr>' : '</td>';
				continue;
			}
	
			classStr = prefix + 'Main' + (hasChild ? 'Folder' : 'Item');
	
			str += vertical ? '<td' : '<span';
			str += ' class="' + classStr + 'Left">';
	
			str += (item[0] == null) ? (hasChild ? nodeProperties.mainFolderLeft : nodeProperties.mainItemLeft)
						 : item[0];
			str += vertical ? '</td>' : '</span>';
	
			str += vertical ? '<td' : '<span';
			str += ' class="' + classStr + 'Text">';
			str += item[1];
	
			str += vertical ? '</td>' : '</span>';
	
			str += vertical ? '<td' : '<span';
			str += ' class="' + classStr + 'Right">';
	
			str += hasChild ? nodeProperties.mainFolderRight : nodeProperties.mainItemRight;
	
			str += vertical ? '</td>' : '</span>';
	
			str += vertical ? '</tr>' : '</td>';
	
			if (hasChild)
				strSub += cmDrawSubMenu (item, prefix, idSub, orientSub, nodeProperties);
		}
		if (!vertical)
			str += '</tr>';
		str += '</table>' + strSub;
		obj.innerHTML = str;
		//document.write ("<xmp>" + str + "</xmp>");
	}
	
	//////////////////////////////////////////////////////////////////////
	//
	// Mouse Event Handling Functions
	//
	//////////////////////////////////////////////////////////////////////
	
	//
	// action should be taken for mouse moving in to the menu item
	//
	function cmItemMouseOver (obj, prefix, isMain, idSub, orient, index)
	{
		clearTimeout (_cmTimeOut);
	
		if (!obj.cmPrefix)
		{
			obj.cmPrefix = prefix;
			obj.cmIsMain = isMain;
		}
	
		var thisMenu = cmGetThisMenu (obj, prefix);
	
		// insert obj into cmItems if cmItems doesn't have obj
		if (!thisMenu.cmItems)
			thisMenu.cmItems = new Array ();
		var i;
		for (i = 0; i < thisMenu.cmItems.length; ++i)
		{
			if (thisMenu.cmItems[i] == obj)
				break;
		}
		if (i == thisMenu.cmItems.length)
		{
			//thisMenu.cmItems.push (obj);
			thisMenu.cmItems[i] = obj;
		}
	
		// hide the previous submenu that is not this branch
		if (_cmCurrentItem)
		{
			// occationally, we get this case when user
			// move the mouse slowly to the border
			if (_cmCurrentItem == thisMenu)
				return;
	
			var thatPrefix = _cmCurrentItem.cmPrefix;
			var thatMenu = cmGetThisMenu (_cmCurrentItem, thatPrefix);
			if (thatMenu != thisMenu.cmParentMenu)
			{
				if (_cmCurrentItem.cmIsMain)
					_cmCurrentItem.className = thatPrefix + 'MainItem';
				else
					_cmCurrentItem.className = thatPrefix + 'MenuItem';
				if (thatMenu.id != idSub)
					cmHideMenu (thatMenu, thisMenu, thatPrefix);
			}
		}
	
		// okay, set the current menu to this obj
		_cmCurrentItem = obj;
	
		// just in case, reset all items in this menu to MenuItem
		cmResetMenu (thisMenu, prefix);
	
		var item = _cmItemList[index];
		var isDefaultItem = cmIsDefaultItem (item);
	
		if (isDefaultItem)
		{
			if (isMain)
				obj.className = '<?php echo $jz_MainItemHover; ?>';
			else
				obj.className = '<?php echo $jz_MenuItemHover; ?>';
		}
	
		if (idSub)
		{
			var subMenu = cmGetObject (idSub);
			cmShowSubMenu (obj, prefix, subMenu, orient);
		}
	
		var descript = '';
		if (item.length > 4)
			descript = (item[4] != null) ? item[4] : (item[2] ? item[2] : descript);
		else if (item.length > 2)
			descript = (item[2] ? item[2] : descript);
	
		window.defaultStatus = descript;
	}
	
	//
	// action should be taken for mouse moving out of the menu item
	//
	function cmItemMouseOut (obj, delayTime)
	{
		if (!delayTime)
			delayTime = _cmNodeProperties.delay;
		_cmTimeOut = window.setTimeout ('cmHideMenuTime ()', delayTime);
		window.defaultStatus = '';
	}
	
	//
	// action should be taken for mouse button down at a menu item
	//
	function cmItemMouseDown (obj, index)
	{
		if (cmIsDefaultItem (_cmItemList[index]))
		{
			if (obj.cmIsMain)
				obj.className = obj.cmPrefix + 'MainItemActive';
			else
				obj.className = obj.cmPrefix + 'MenuItemActive';
		}
	}
	
	//
	// action should be taken for mouse button up at a menu item
	//
	function cmItemMouseUp (obj, index)
	{
		var item = _cmItemList[index];
	
		var link = null, target = '_self';
	
		if (item.length > 2)
			link = item[2];
		if (item.length > 3)
			target = item[3] ? item[3] : target;
	
		if (link != null && target=='moswindow') 
		{
			window.open (link, '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550');
		}
		else if (link != null)
		{
			window.open (link, target);
		}
	
		var prefix = obj.cmPrefix;
		var thisMenu = cmGetThisMenu (obj, prefix);
	
		var hasChild = (item.length > 5);
		if (!hasChild)
		{
			if (cmIsDefaultItem (item))
			{
				if (obj.cmIsMain)
					obj.className = prefix + 'MainItem';
				else
					obj.className = '<?php echo $jz_MenuItem; ?>';
			}
			cmHideMenu (thisMenu, null, prefix);
		}
		else
		{
			if (cmIsDefaultItem (item))
			{
				if (obj.cmIsMain)
					obj.className = '<?php echo $jz_MainItemHover; ?>';
				else
					obj.className = '<?php echo $jz_MenuItemHover; ?>';
			}
		}
	}
	
	//////////////////////////////////////////////////////////////////////
	//
	// Mouse Event Support Utility Functions
	//
	//////////////////////////////////////////////////////////////////////
	
	//
	// move submenu to the appropriate location
	//
	// @param	obj	the menu item that opens up the subMenu
	//		subMenu	the sub menu to be shown
	//		orient	the orientation of the subMenu
	//
	function cmMoveSubMenu (obj, subMenu, orient)
	{
		var mode = String (orient);
		var p = subMenu.offsetParent;
		if (mode.charAt (0) == 'h')
		{
			if (mode.charAt (1) == 'b')
				subMenu.style.top = (cmGetYAt (obj, p) + obj.offsetHeight) + 'px';
			else
				subMenu.style.top = (cmGetYAt (obj, p) - subMenu.offsetHeight) + 'px';
			if (mode.charAt (2) == 'r')
				subMenu.style.left = (cmGetXAt (obj, p)) + 'px';
			else
				subMenu.style.left = (cmGetXAt (obj, p) + obj.offsetWidth - subMenu.offsetWidth) + 'px';
		}
		else
		{
			if (mode.charAt (2) == 'r')
				subMenu.style.left = (cmGetXAt (obj, p) + obj.offsetWidth) + 'px';
			else
				subMenu.style.left = (cmGetXAt (obj, p) - subMenu.offsetWidth) + 'px';
			if (mode.charAt (1) == 'b')
				subMenu.style.top = (cmGetYAt (obj, p)) + 'px';
			else
				subMenu.style.top = (cmGetYAt (obj, p) + obj.offsetHeight - subMenu.offsetHeight) + 'px';
			//alert (subMenu.style.top + ', ' + cmGetY (obj) + ', ' + obj.offsetHeight);
		}
	}
	
	//
	// show the subMenu w/ specified orientation
	// also move it to the correct coordinates
	//
	// @param	obj	the menu item that opens up the subMenu
	//		subMenu	the sub menu to be shown
	//		orient	the orientation of the subMenu
	//
	function cmShowSubMenu (obj, prefix, subMenu, orient)
	{
		if (!subMenu.cmParentMenu)
		{
			// establish the tree w/ back edge
			var thisMenu = cmGetThisMenu (obj, prefix);
			subMenu.cmParentMenu = thisMenu;
			if (!thisMenu.cmSubMenu)
				thisMenu.cmSubMenu = new Array ();
			//thisMenu.cmSubMenu.push (subMenu);
			thisMenu.cmSubMenu[thisMenu.cmSubMenu.length] = subMenu;
		}
	
		// position the sub menu
		cmMoveSubMenu (obj, subMenu, orient);
		subMenu.style.visibility = 'visible';
	
		//
		// On IE, controls such as SELECT, OBJECT, IFRAME (before 5.5)
		// are window based controls.  So, if sub menu and these controls
		// overlap, sub menu would be hid behind them.  Thus, one needs to
		// turn the visibility of these controls off when the
		// sub menu is showing, and turn their visibility back on
		//
		if (document.all)	// it is IE
		{
			subMenu.cmOverlap = new Array ();
	/*@cc_on @*/
	/*@if (@_jscript_version >= 5.5)
	@else @*/
			cmHideControl ("IFRAME", subMenu);
	/*@end @*/
			cmHideControl ("SELECT", subMenu);
			cmHideControl ("OBJECT", subMenu);
		}
	}
	
	//
	// reset all the menu items to class MenuItem in thisMenu
	//
	function cmResetMenu (thisMenu, prefix)
	{
		if (thisMenu.cmItems)
		{
			var i;
			var str;
			var items = thisMenu.cmItems;
			for (i = 0; i < items.length; ++i)
			{
				if (items[i].cmIsMain)
					str = prefix + 'MainItem';
				else
					str = '<?php echo $jz_MenuItem; ?>';
				if (items[i].className != str)
					items[i].className = str;
			}
		}
	}
	
	//
	// called by the timer to hide the menu
	//
	function cmHideMenuTime ()
	{
		if (_cmCurrentItem)
		{
			var prefix = _cmCurrentItem.cmPrefix;
			cmHideMenu (cmGetThisMenu (_cmCurrentItem, prefix), null, prefix);
		}
	}
	
	//
	// hide thisMenu, children of thisMenu, as well as the ancestor
	// of thisMenu until currentMenu is encountered.  currentMenu
	// will not be hidden
	//
	function cmHideMenu (thisMenu, currentMenu, prefix)
	{
		var str = prefix + 'SubMenu';
	
		// hide the down stream menus
		if (thisMenu.cmSubMenu)
		{
			var i;
			for (i = 0; i < thisMenu.cmSubMenu.length; ++i)
			{
				cmHideSubMenu (thisMenu.cmSubMenu[i], prefix);
			}
		}
	
		// hide the upstream menus
		while (thisMenu && thisMenu != currentMenu)
		{
			cmResetMenu (thisMenu, prefix);
			if (thisMenu.className == str)
			{
				thisMenu.style.visibility = 'hidden';
				cmShowControl (thisMenu);
			}
			else
				break;
			thisMenu = cmGetThisMenu (thisMenu.cmParentMenu, prefix);
		}
	}
	
	//
	// hide thisMenu as well as its sub menus if thisMenu is not
	// already hidden
	//
	function cmHideSubMenu (thisMenu, prefix)
	{
		if (thisMenu.style.visibility == 'hidden')
			return;
		if (thisMenu.cmSubMenu)
		{
			var i;
			for (i = 0; i < thisMenu.cmSubMenu.length; ++i)
			{
				cmHideSubMenu (thisMenu.cmSubMenu[i], prefix);
			}
		}
		cmResetMenu (thisMenu, prefix);
		thisMenu.style.visibility = 'hidden';
		cmShowControl (thisMenu);
	}
	
	//
	// hide a control such as IFRAME
	//
	function cmHideControl (tagName, subMenu)
	{
		var x = cmGetX (subMenu);
		var y = cmGetY (subMenu);
		var w = subMenu.offsetWidth;
		var h = subMenu.offsetHeight;
	
		var i;
		for (i = 0; i < document.all.tags(tagName).length; ++i)
		{
			var obj = document.all.tags(tagName)[i];
			if (!obj || !obj.offsetParent)
				continue;
	
			// check if the object and the subMenu overlap
	
			var ox = cmGetX (obj);
			var oy = cmGetY (obj);
			var ow = obj.offsetWidth;
			var oh = obj.offsetHeight;
	
			if (ox > (x + w) || (ox + ow) < x)
				continue;
			if (oy > (y + h) || (oy + oh) < y)
				continue;
			//subMenu.cmOverlap.push (obj);
			subMenu.cmOverlap[subMenu.cmOverlap.length] = obj;
			obj.style.visibility = "hidden";
		}
	}
	
	//
	// show the control hidden by the subMenu
	//
	function cmShowControl (subMenu)
	{
		if (subMenu.cmOverlap)
		{
			var i;
			for (i = 0; i < subMenu.cmOverlap.length; ++i)
				subMenu.cmOverlap[i].style.visibility = "";
		}
		subMenu.cmOverlap = null;
	}
	
	//
	// returns the main menu or the submenu table where this obj (menu item)
	// is in
	//
	function cmGetThisMenu (obj, prefix)
	{
		var str1 = prefix + 'SubMenu';
		var str2 = prefix + 'Menu';
		while (obj)
		{
			if (obj.className == str1 || obj.className == str2)
				return obj;
			obj = obj.parentNode;
		}
		return null;
	}
	
	//
	// return true if this item is handled using default handlers
	//
	function cmIsDefaultItem (item)
	{
		if (item == _cmSplit || item[0] == _cmNoAction)
			return false;
		return true;
	}
	
	//
	// returns the object baring the id
	//
	function cmGetObject (id)
	{
		if (document.all)
			return document.all[id];
		return document.getElementById (id);
	}
	
	//
	// functions that obtain the coordinates of an HTML element
	//
	function cmGetX (obj)
	{
		var x = 0;
	
		do
		{
			x += obj.offsetLeft;
			obj = obj.offsetParent;
		}
		while (obj);
		return x;
	}
	
	function cmGetXAt (obj, elm)
	{
		var x = 0;
	
		while (obj && obj != elm)
		{
			x += obj.offsetLeft;
			obj = obj.offsetParent;
		}
		return x;
	}
	
	function cmGetY (obj)
	{
		var y = 0;
		do
		{
			y += obj.offsetTop;
			obj = obj.offsetParent;
		}
		while (obj);
		return y;
	}
	
	function cmGetYAt (obj, elm)
	{
		var y = 0;
	
		while (obj && obj != elm)
		{
			y += obj.offsetTop;
			obj = obj.offsetParent;
		}
		return y;
	}
	
	//
	// debug function, ignore :)
	//
	function cmGetProperties (obj)
	{
		if (obj == undefined)
			return 'undefined';
		if (obj == null)
			return 'null';
	
		var msg = obj + ':\n';
		var i;
		for (i in obj)
			msg += i + ' = ' + obj[i] + '; ';
		return msg;
	}
	
	/* JSCookMenu v1.23	1. correct a position bug when the container is positioned.
						  thanks to Andre <anders@netspace.net.au> for narrowing down
						  the problem.
	*/
	/* JSCookMenu v1.22	1. change Array.push (obj) call to Array[length] = obj.
						   Suggestion from Dick van der Kaaden <dick@netrex.nl> to
						   make the script compatible with IE 5.0
						2. Changed theme files a little to add z-index: 100 for sub
						   menus.  This change is necessary for Netscape to avoid
						   a display problem.
						3. some changes to the DOM structure to make this menu working
						   on Netscape 6.0 (tested).  The main reason is that NN6 does
						   not do absolute positioning with tables.  Therefore an extra
						   div layer must be put around the table.
	*/
	/* JSCookMenu v1.21	1. fixed a bug that didn't add 'px' as part of coordinates.
						   JSCookMenu should be XHTML validator friendly now.
						2. removed unnecessary display attribute and corresponding
						   theme entry to fix a problem that Netscape sometimes
						   render Office theme incorrectly
	*/
	/* JSCookMenu v1.2.	1. fix the problem of showing status in Netscape
						2. changed the handler parameters a bit to allow
						   string literals to be passed to javascript based
						   links
						3. having null in target field would cause the link
						   to be opened in the current window, but this behavior
						   could change in the future releases
	*/
	/* JSCookMenu v1.1.		added ability to hide controls in IE to show submenus properly */
	/* JSCookMenu v1.01.	cmDraw generates XHTML code */
	/* JSCookMenu v1.0.		(c) Copyright 2002 by Heng Yuan */
	
	
	// directory of where all the images are
	var cmjzBase = 'includes/js/jz/';
	
	var cmjz =
	{
		// main menu display attributes
		//
		// Note.  When the menu bar is horizontal,
		// mainFolderLeft and mainFolderRight are
		// put in <span></span>.  When the menu
		// bar is vertical, they would be put in
		// a separate TD cell.
	
		// HTML code to the left of the folder item
		mainFolderLeft: '&nbsp;',
		// HTML code to the right of the folder item
		mainFolderRight: '&nbsp;',
		// HTML code to the left of the regular item
		mainItemLeft: '&nbsp;',
		// HTML code to the right of the regular item
		mainItemRight: '&nbsp;',
	
		// sub menu display attributes
	
		// 0, HTML code to the left of the folder item
		folderLeft: '<img alt="" src="<?php echo $main_img_dir; ?>/spacer.png">',
		// 1, HTML code to the right of the folder item
		folderRight: '<img alt="" src="<?php echo $main_img_dir; ?>/arrow.png">',
		// 2, HTML code to the left of the regular item
		itemLeft: '<img alt="" src="<?php echo $main_img_dir; ?>/spacer.png">',
		// 3, HTML code to the right of the regular item
		itemRight: '<img alt="" src="<?php echo $main_img_dir; ?>/blank.png">',
		// 4, cell spacing for main menu
		mainSpacing: 0,
		// 5, cell spacing for sub menus
		subSpacing: 0,
		// 6, auto dispear time for submenus in milli-seconds
		delay: 300
	};
	
	// for horizontal menu split
	var cmjzHSplit = [_cmNoAction, '<td class="<?php echo $jz_MenuItemLeft; ?>"></td><td colspan="2"><span class="<?php echo $jz_MenuSplit; ?>"></span></td>'];
	var cmjzMainHSplit = [_cmNoAction, '<td class="<?php echo $jz_MenuItemLeft; ?>"></td><td colspan="2"><span class="<?php echo $jz_MenuSplit; ?>"></span></td>'];
	var cmjzMainVSplit = [_cmNoAction, '&nbsp;'];
	//-->
</script>