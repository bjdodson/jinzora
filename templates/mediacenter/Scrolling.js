// when you focus on an item, the browser will automatically scroll it into view. This file provides additional functionality.
// It enables the pg up/dn buttons, and it updates the buttons' positions when scrolling takes place (these positions are needed
// when you move the focus, in order to calculate which other buttons on the page are closest)

function pageUpDown(direction)
{
    // This function determines what to do if the user presses the Page Up (+) or Page Down (-) key

    // Call the getScrollParent function to find the scrollable parent object
    var oScrollParent = getScrollParent()
    if (oScrollParent == null)
    {
        return
    }
    // find correct button to focus on
    var oScrollToBtn = getScrollToButton(oScrollParent, direction);
    oCurFocus = oScrollToBtn;
    oCurFocus.focus();
    // check if scrolling has taken place, and update positions of scrollable elements if needed
    if (didScroll())
    {
        updateScrollPositions()
    }
}

function getScrollParent()
{
    /* For an item to scroll correctly when the user presses the Page Up (+) or Page Down (-) keys, it has to have a parent object that
    has its custom "MCScrollable" attribute set to "true". This function locates that parent object */
    // Start with the object that currently has focus ...
    var oTempObj = oCurFocus
    // ... then check its parent, grandparent, great-grandparent, and so on.
    for (i=0; i<5000; i++)
    {
         oTempObj = oTempObj.parentElement
        // Stop when you get to the scrollable parent element
        if (oTempObj.MCScrollable == "true")
        {
            return oTempObj
        }
        // If you get as high as the BODY tag without finding a scrollable parent element, return null
        if (oTempObj.tagName == "BODY")
        {
            return null
        }
    }
}

function getScrollToButton(oScrollParent, direction)
{
    // This function determines which item to focus on next when the user presses the Page Up (+) or Page Down (-) key

    // find top & bottom of scroll parent relative to top of pg (don't worry about 2 px offset)
    var oBottomVisibleBtn = oCurFocus
    var oTopVisibleBtn = null
    var oLowerBtn
    var oHigherBtn = null
    //var oScrollToBtn
    var oScrollParentPos = oScrollParent.getBoundingClientRect()
    var nScrollParentTop = oScrollParentPos.top
    var nScrollParentBottom = oScrollParentPos.bottom
    var nScrollParentHeight = (nScrollParentBottom - nScrollParentTop)

    // go through all focusable elements in order;
    for (i=0; i<oScrollParent.all.length; i++)
    {
        //identify object
        var obj = oScrollParent.all(i)
        // make sure object is focusable
        if (obj.MCFocusable == "true" && direction == "down")
        {
            //find top of object
            var nObjTop = obj.nTopPos;
            // for the lowest btn whose top is visible (by at least two pixels), assign bottomVisibleBtn to object
            if ((nObjTop + 2) < nScrollParentBottom)
            {
                oBottomVisibleBtn = obj
            }
            // Find lowest button down to one Scroll Parent Height below bottom visible btn
            if (nObjTop < (nScrollParentBottom + nScrollParentHeight))
            {
                oLowerBtn = obj
            }
        }
        if (obj.MCFocusable == "true" && direction == "up")
        {
            var nObjBottom = obj.getBoundingClientRect().bottom;
            // for the first btn that's even partly visible, assign topVisibleBtn to object
            if (nObjBottom > nScrollParentTop && oTopVisibleBtn == null)
            {
                oTopVisibleBtn = obj
            }
            // Find first button that's within one Scroll Parent Height above top visible btn
            if (nObjBottom > (nScrollParentTop - nScrollParentHeight) && oHigherBtn == null)
            {
                oHigherBtn = obj
            }
        }
    }
    if (direction == "up")
    {
        if (oTopVisibleBtn != oCurFocus)
        {
            // if focus is not already on bottom visible btn, move focus to there and end function
            return oTopVisibleBtn
        }
        else
        {
            // If focus is already on bottom visible, focus on button one box height lower
            return oHigherBtn
        }
    }

    if (direction == "down")
    {
        if (oBottomVisibleBtn != oCurFocus)
        {
            // if focus is not already on bottom visible btn, return bottom visible btn and end function
            return oBottomVisibleBtn
        }
        else
        {
            // If focus is already on bottom visible, return button one box height lower than that
            return oLowerBtn
        }
    }
}

function didScroll()
{
    // This function indicates whether current-focus item has scrolled or otherwise moved vertically on the page.
    // If the current-focus item's top position does not equal the position it was in when the page loaded, return true
    if((oCurFocus.getBoundingClientRect().top - 2) != oCurFocus.nTopPos)
    {
        return true
    }
}

function updateScrollPositions()
{
    // object has scrolled; reset "nCenterYCoord" property for all scrolling buttons in box
    // so that auto nav will be based on new positions for buttons
    var oScrollParent = getScrollParent()
    if (oScrollParent == null)
    {
        oScrollParent = body
    }
    // for all objects in the parent container of the scrolling element
    for (i=0; i<oScrollParent.all.length; i++)
    {
        //identify object
        var obj = oScrollParent.all(i)
        // make sure object is focusable
        if (obj.MCFocusable == "true")
        {
            // reassign Y coordinate values for based on its new top position
            var tempObjPosition = obj.getBoundingClientRect();
            obj.nTopPos = tempObjPosition.top - 2
            obj.nBottomPos = tempObjPosition.bottom - 2
            var objCenterTop = (obj.nTopPos+(obj.nHeight/2))
            obj.nCenterYCoord = objCenterTop
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////
// code for counter that indicates how many buttons are in scrolling menu, etc.

// variable to indicate whether there is a counter on the page
var isCounter = false
// variable to hold array of scrolling buttons
var aScrollBtnsArray

function setCounter()
{
    /* this function sets the numeric value for the item counter found at the lower right of
    each scrollable menu in the templates, to indicate how many focusable items are in the menu. */
    // Reset isCounter value to true, indicating that there is a counter on the page
    isCounter = true
    //Make array for scrolling buttons
    aScrollBtnsArray = new Array()
    // Loop through all focusable items in the scrolling span
    for (i=0; i<scrollspan.all.length; i++)
    {
        var obj = scrollspan.all(i)
        if(obj.MCFocusable == "true")
        {
            var nextElement = aScrollBtnsArray.length
            aScrollBtnsArray[nextElement] = obj
        }
    }
    // update the SPAN whose ID is "counterTotal" to reflect total number of focusable items in scrolling span
    counterTotal.innerHTML = aScrollBtnsArray.length
}

function updateCounter()
{
     /* this function updates the numeric value for the item counter found at the lower right of
    each scrollable menu in the templates, to indicate which item currently has focus. */

    if (isCounter != true)
    {
        // if there is no counter on the page, return
        return
    }
    // variable to track whether focus is in scrollable menu
    var bFocusInMenu = false
    //Loop through all focusable items in scrolling menu
    for (i=0; i<aScrollBtnsArray.length; i++)
    {
        // if item currently has focus ...
        if (aScrollBtnsArray[i] == oCurFocus)
        {
            // set counter number to show which item has focus
            counterNum.innerHTML = (i + 1);
            // update variable to indicate focus is in scrollable menu
            bFocusInMenu = true
        }
    }
    // if focus in in menu, make sure arrows are not grayed out.
    if (bFocusInMenu == true)
    {
        itemCounterSpan.style.filter = "none"
    }
    // Else gray out arrows to indicate disabled state.
    else
    {
        itemCounterSpan.style.filter = "alpha(opacity=50)"
    }
}
