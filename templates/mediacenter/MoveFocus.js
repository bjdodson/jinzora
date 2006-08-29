// Variable to track where the focus is at any time. Its value will always be a focusable object on the page
var oCurFocus

// variable to track where the focus was previous to current focus.
var oPrevFocus = null

// variable to track which direction arrow was used to get between previous focus and current focus
var sPrevArrowDirection = null

// variable to trap first mouseover event
var bFirstMouseover = true

function startFocus()
// this function sets the initial focus on the page, plus it takes care of a few other tasks that are linked to the onload event
{
    // make sure that scrollbar does not appear at the side of the page
    // This is not integral to the rest of the function, but it's a convenient place to do it
     body.scroll="no"

    //Reset the variable that traps the first mouseover event on the page
    // This is not integral to the rest of the function, but it's a convenient place to do it
    // The MouseOver function in the Hilite.js file checks this variable to handle mouseover events
    // For more info, see comments in that file
    window.setTimeout("bFirstMouseover = false", 100)

    // check if media is playing and Shared viewport needs to be opened
    try
    {
        // call needViewport function, which checks if media is playing
        if (needViewport() == true)
        {
            window.external.MediaCenter.SharedViewPort.Visible = true
        }
    }
    catch(e)
    {
        // ignore error
    }

    /* if you are arriving at the page via the Back button, then focus should start on the same
    element it was on when you left. The browser will automatically focus on the correct element,
    but for the element to highlight correctly, you have to update a variable, move the focus to
    the BODY element, and then continue with the rest of this function. */
    // Check to see if initial focus is already on a focusable element
    if (document.activeElement.MCFocusable == "true")
    {
        // reset MCFocusStart variable, which instructs the page where to start the focus
        body.MCFocusStart = document.activeElement
        // move focus temporarily to BODY element, or focusable item will not highlight correctly
        body.focus()
    }

    // if oCurFocus does not already have a value, check the value for body.MCFocusStart
    if (oCurFocus == null)
    {
        try
        {
            oCurFocus = eval(body.MCFocusStart)
        }
        catch(e)
        {
            // if MCFocusStart is invalid, ignore error
        }
    }

    // if body.MCFocusStart has no value, and oCurFocus still has no value,
    // set oCurFocus to the first focusable item on the page. This will be the default state
    if (oCurFocus == null)
    {
        try
        {
            oCurFocus = aFocusableItemsArray[0]
            // make sure starting focus is not Shared Viewport
            if (oCurFocus.id == "SVP")
            {
                oCurFocus = aFocusableItemsArray[1]
            }
        }
        catch(e)
        {
            //ignore error
        }
    }

    try
    {
        oCurFocus.focus()
    }
    catch(e)
    {
        //ignore error
    }
}


// array that will contain all focusable objects on the page
var aFocusableItemsArray = new Array()

function setArray()
 /* This function makes an array of all the focusable objects on the page and
 finds their exact locations on the page, making top and left properties for the center of each */
{
    // variable to track which focusable element we are dealing with
    var nextElement = 0

    // for all objects on the page ...
    for (i=0; i<body.all.length; i++)
    {

        //variable to identify object
        var obj = body.all(i)
        // If object is focusable ...
        if (obj.MCFocusable == "true")
        {
            // Set position, width, height, left and right as properties for the object
            var objPosition = obj.getBoundingClientRect();
            obj.nLeftPos = objPosition.left -2
            obj.nRightPos = objPosition.right -2
            obj.nTopPos = objPosition.top - 2
            obj.nBottomPos = objPosition.bottom -2
            obj.nWidth = (obj.nRightPos - obj.nLeftPos)
            obj.nHeight = (obj.nBottomPos - obj.nTopPos)

            //find top coordinate for center of item: Y position on page plus half of height
            var objCenterTop = (obj.nTopPos+(obj.nHeight/2))
            //find left coordinate for center of item: X position on page plus half of width
            var objCenterLeft = (obj.nLeftPos+(obj.nWidth/2))
            // assign top and left as custom property of object
            obj.nCenterYCoord = objCenterTop
            obj.nCenterXCoord = objCenterLeft
            // Place object in array of focusable items, in correct position
            aFocusableItemsArray[nextElement] = obj
            // update nextElement variable
            nextElement++
        }

        // check if object is a container for a "spinner"; if so, call function to show correct value
        if (obj.MCSpinnerContainer == "true")
        {
            try
            {
                initializeSpinner(obj)
            }
            catch(e)
            {
             // if above function call is not valid, ignore error
             }
        }
    }
}

function checkForOppositeArrow(direction)
{

    /* this function checks if the arrow button pressed is in the opposite
    direction from the last arrow button pressed (e.g. up, then down, or left, then right) */
    // make sure that oPrevFocus and sPrevArrowDirection are not null
    if (oPrevFocus == null || sPrevArrowDirection == null) return false
    
    switch(direction)
    {
        case "up":
        {
            if  (sPrevArrowDirection == "down")
            {
                return true;
            }
            break;
        }
        case "down":
        {
            if  (sPrevArrowDirection == "up")
            {
                return true;
            }
            break;
        }
        case "left":
        {
            if  (sPrevArrowDirection == "right")
            {
                return true;
            }
            break;
        }
        case "right":
        {
            if  (sPrevArrowDirection == "left")
            {
                return true;
            }
            break;
        }
    }
    return false
}

function changeFocus(direction)
// this function moves the focus from one object to another based on input from the remote
{
    /* if the arrow button pressed is in the opposite
    direction from the last arrow button pressed (e.g. up, then down, or left, then right)
    move focus to the previous button, update variables, and end function */
    if (checkForOppositeArrow(direction) == true)
    {
        // remember oCurFocus value before changing it
        var oTemp = oCurFocus       
        // set focus back to previous focus button
        oCurFocus = oPrevFocus
        oCurFocus.focus()       
        // update oPrevFocus
        oPrevFocus = oTemp
        // update variable to track which direction arrow was pressed
        sPrevArrowDirection = direction
        // end function
        return      
    }
    

    
    // update variable to track where the focus was previous to current focus.
    oPrevFocus = oCurFocus
    // update variable to track which direction arrow was used to get between previous focus and current focus
    sPrevArrowDirection = direction
    
    
    // call function (below) to find nearest object to the one that has focus, in the direction of whatever arrow is pressed
    var nearestObj = findClosestItem(direction)
    
    // if nearestObj is null, then make sPrevArrowDirectio null
    if (nearestObj == null) sPrevArrowDirection = null

    // if focus is on the Shared Viewport and the user arrows in a direction that
    // has no other focusable items, put focus back on viewport
    if (oCurFocus.id == "SVP" && nearestObj == null)
    {
        window.external.MediaCenter.SharedViewPort.focus();
        return
    }
    if (oCurFocus.id == "CVP" && nearestObj == null)
    {
        window.external.MediaCenter.CustomViewPort.focus();
        return
    }
    // if there are no focusable objects in the direction indicated by arrow click, return
    if (nearestObj == null)
    {
        return
    }
    // set focus on new object
    oCurFocus = nearestObj
    oCurFocus.focus()

    // If focus is on shared viewport placeholder, move focus to real shared viewport
    if (oCurFocus.id == "SVP")
    {
        window.external.MediaCenter.SharedViewPort.focus();
        return
    }

    //If focus is on custom viewport placeholder, move focus to real custom viewport
    if (oCurFocus.id == "CVP")
    {
        window.external.MediaCenter.CustomViewPort.focus();
        return
    }
    try
    {
        // check if scrolling has taken place, using the didScroll function, and update positions of scrollable elements if needed
        if (didScroll())
        {
            updateScrollPositions()
        }
    }
    catch(e)
    {
        // if didScroll function is not present, ignore error
    }
}


function findClosestItem(direction)
{
/* this function finds the the best item to navigate to based on which arrow key is pressed and on which item currently has the focus. To do this, it does up to three loops through all of the focusable items. On the first loop, it looks for items that are in a direct line in the direction selected, and finds the closest one. If the first loop finds no results, the second loop widens the search, looking for the closest item in the quadrant of the page (dividing the page like an X, not like a cross, with the current focus item at the center) in the direction selected. If there are no focusable items in the correct quadrant, the search widens a bit more, finding the closest item anywhere on the page (or, in effect, half of the page) in the direction selected. */
    //Variable for object that currently has focus
    var oOldObj = oCurFocus;
    // variable to track shortest item distance. Start with a high number (10,000) for value
    var nShortestItemDist = 10000
    // variable to track which object is nearest to the one that currently has focus
    var oNearestObj = null

    // first try to find any items that are exactly in the direction indicated; if any, return closest item
    for (i=0; i < aFocusableItemsArray.length; i++)
    {

        // Locate new object in array
        oNewObj = aFocusableItemsArray[i]
        // make sure object is not temporarily unfocusable
        if (oNewObj.MCTempUnFocusable == "true") continue
        // Make sure oNewObj is not oOldObj
        if (oOldObj == oNewObj)
        {
            continue
        }

        // call function to check if new object is in a straight line from old object, in direction indicated by selected arrow key;
        // if so, set nTempDist variable for distance
        var nTempDist = isInLine(oOldObj, oNewObj, direction)

        // if current object's distance is closest so far ...
        if (nTempDist != null && nTempDist < nShortestItemDist)
        {
            // update variable for shortest distance so far
            nShortestItemDist = nTempDist
            // update variable for closest object so far
            oNearestObj = oNewObj
        }
    }
    // if above process finds any objects in a straight line from old object, return the closest one.
    if (oNearestObj != null)
    {
        return oNearestObj
    }

    ////////////////////////////
    /* If no items exactly in correct direction, try to find any items that are in the correct quadrant
    for the direction indicated; if any, return closest item */
    for (i=0; i < aFocusableItemsArray.length; i++)
    {
        // Locate new object in array
        oNewObj = aFocusableItemsArray[i]
        // make sure object is not temporarily unfocusable
        if (oNewObj.MCTempUnFocusable == "true") continue
        // Make sure oNewObj is not oOldObj
        if (oOldObj == oNewObj) continue

        // call function to check if new object is in the correct quadrant from old object, in direction indicated by selected arrow key;
        // if so, set nTempDist variable for distance
        var nTempDist = isInQuadrant(oOldObj, oNewObj, direction)

        // if current object's distance is closest so far (of items in this loop) ...
        if (nTempDist != null && nTempDist < nShortestItemDist)
        {
            // update variable for shortest distance so far
            nShortestItemDist = nTempDist
            // update variable for closest object so far
            oNearestObj = oNewObj
        }
    }
    // if above process finds any objects in the correct quadrant from old object, return closest one
    if (oNearestObj != null)
    {
        return oNearestObj
    }

    /////////////////////////////////////
    /* If no items are in correct quadrant, try to find any items that are in the correct half of the page
    for the direction indicated; if any, return closest item */
    for (i=0; i < aFocusableItemsArray.length; i++)
    {
        // Locate new object in array
        oNewObj= aFocusableItemsArray[i]
        // make sure object is not temporarily unfocusable
        if (oNewObj.MCTempUnFocusable == "true") continue
        // Make sure nNewObj is not oOldObj
        if (oOldObj == oNewObj) continue

        // call function to check if new object is in the correct quadrant from old object, in direction indicated by selected arrow key;
        // if so, set nTempDist variable for distance
        var nTempDist = isInHalf(oOldObj, oNewObj, direction)

        // if current object's distance is closest so far (in this loop) ...
        if (nTempDist != null && nTempDist < nShortestItemDist)
        {
            // update variable for shortest distance so far
            nShortestItemDist = nTempDist
            // update variable for closest object so far
            oNearestObj = oNewObj
        }
    }

    // if above process finds any objects in the correct quadrant from old object, return closest object
    if (oNearestObj != null)
    {
        return oNearestObj
    }
    // If not, return null
    return null
}


function isInLine(oOldObj, oNewObj, direction)
{
    var nStraightDist = null
    // make sure object is not temporarily unfocusable

    if (direction == "down")
    {
        // if old object is above (less in y coordinate) new object, return null
        if (oOldObj.nCenterYCoord > oNewObj.nCenterYCoord)
        {
            return null
        }
        // if the current focus object's left edge is to the left of the new object's right edge, and the
        // current focus object's right edge is to the right of the new object's left edge ...
        if (oOldObj.nLeftPos < oNewObj.nRightPos && oNewObj.nLeftPos < oOldObj.nRightPos)
        {
            // then the new object and the old object overlap in the X coordinate
            // calculate distance based on (top of oNewObj - bottom of oOldObj)
            nStraightDist = (oNewObj.nTopPos - oOldObj.nBottomPos)
        }
        else
        {
            return null
        }
    }
    if (direction == "up")
    {
        // if old object is below (greater in y coordinate) new object, return null
        if (oOldObj.nCenterYCoord < oNewObj.nCenterYCoord)
        {
            return null
        }
        // if the current focus object's left edge is to the left of the new object's right edge, and the
        // current focus object's right edge is to the right of the new object's left edge ...
        if (oOldObj.nLeftPos < oNewObj.nRightPos && oNewObj.nLeftPos < oOldObj.nRightPos)
        {
            // then the new object and the old object overlap in the X coordinate
            // calculate distance based on (top of oOldObj - bottom of oNewObj)
            nStraightDist = (oOldObj.nTopPos - oNewObj.nBottomPos)
        }
        else
        {
            return null
        }
    }
    if (direction == "left")
    {
        // if old object is left of (less in x coordinate) new object, return null
        if (oOldObj.nCenterXCoord < oNewObj.nCenterXCoord)
        {
            return null
        }
        // if the current focus object's top edge is above of the new object's bottom edge,
        // and the current focus object's bottom edge is below the new object's top edge ...
        if (oOldObj.nTopPos < oNewObj.nBottomPos && oOldObj.nBottomPos > oNewObj.nTopPos)
        {
            // then the new object and the old object overlap in the Y coordinate
            // calculate distance based on (left edge of oOldObj - Right edge of oNewObj)
            nStraightDist = (oOldObj.nLeftPos - oNewObj.nRightPos)
        }
        else
        {
            return null
        }
    }
    if (direction == "right")
    {
        // if old object is right of (greater in x coordinate) new object, return null
        if (oOldObj.nCenterXCoord > oNewObj.nCenterXCoord)
        {
            return null
        }
        // if the current focus object's top edge is above of the new object's bottom edge,
        // and the current focus object's bottom edge is below the new object's top edge ...
        if (oOldObj.nTopPos < oNewObj.nBottomPos && oOldObj.nBottomPos > oNewObj.nTopPos)
        {
            // then the new object and the old object overlap in the Y coordinate
            // calculate distance based on (left edge of oNewObj - Right edge of oOldObj )
            nStraightDist = (oNewObj.nLeftPos - oOldObj.nRightPos)
        }
        else
        {
            return null
        }
    }
    return nStraightDist
}

function isInQuadrant(oOldObj, oNewObj, direction)
{
    // compare the difference between top and left for two objects to see what direction
    // the new object is in relative to the object that currently has focus.
    // Check distance between objects
    var xDif = Math.abs(oOldObj.nCenterXCoord - oNewObj.nCenterXCoord);
    var yDif = Math.abs(oOldObj.nCenterYCoord - oNewObj.nCenterYCoord);

    /* Determine quadrant (relative to the center of the current-focus object) that the new object falls in.
    Note that the term "quadrant" is used loosely here; the dividing lines between the quadrants form
    an X rather than a +. That way the arrow keys point to the center of each quadrant  */
    var quadrant
    if(xDif > yDif && oOldObj.nCenterXCoord > oNewObj.nCenterXCoord) quadrant = "left";
    if(xDif > yDif && oOldObj.nCenterXCoord <= oNewObj.nCenterXCoord) quadrant = "right";
    if(xDif <= yDif && oOldObj.nCenterYCoord > oNewObj.nCenterYCoord) quadrant = "up";
    if(xDif <= yDif && oOldObj.nCenterYCoord <= oNewObj.nCenterYCoord) quadrant = "down";

    // make sure object is in correct quadrant ...
    if (quadrant != direction) return null

    // Find distance between centers of objects; (project a right triangle and calculate hypotenuse)
    var nQuadDist = Math.sqrt((xDif * xDif) + (yDif * yDif))
    return nQuadDist
}

function isInHalf(oOldObj, oNewObj, direction)
/* if checkItemDist function cannot find any focusable items in the correct quadrant, this function
widens the search a bit. Instead of using quadrants, it considers focusable items that lie at least 41 pixels away
in the direction (up, down, left or right) indicated by the arrow key. Example: if the user hits the right arrow key,
this function will consider any focusable item whose X coordinate is at least 41 pixels greater than that of the
current-focus item, no matter what its Y coordiant is. From among those, it then calculates which is closest.        */
{
    // variable for distance between old and new objects
    var nHalfDist

        var bIsInCorrectArea = false
        // determine whether new item lies at least 41 pixels into the half of the page (relative to the current-focus item)
        // that matches the arrow key pressed (example: if right arrowkey pressed, new item must
        // have an X coodrinate that is at least 41 px greater than that of the current-focus item, or bIsInCorrectArea
        // will not be set to true)
        if (direction == "up" && (oOldObj.nCenterYCoord - 40) > oNewObj.nCenterYCoord)
        {
            bIsInCorrectArea = true
        }
        if (direction == "down" && (oOldObj.nCenterYCoord + 40) < oNewObj.nCenterYCoord)
        {
            bIsInCorrectArea = true
        }
        if (direction == "left" && (oOldObj.nCenterXCoord - 40) > oNewObj.nCenterXCoord)
        {
            bIsInCorrectArea = true
        }
        if (direction == "right" && (oOldObj.nCenterXCoord + 40) < oNewObj.nCenterXCoord)
        {
            bIsInCorrectArea = true
        }

        // If object is not in correct area, end function
        if (bIsInCorrectArea == false) return null

        // Compare the difference between top and left for two objects to see what direction
        // the second object is in relative to the first.
        // Check distance between objects
        var xDif = Math.abs(oOldObj.nCenterXCoord - oNewObj.nCenterXCoord);
        var yDif = Math.abs(oOldObj.nCenterYCoord - oNewObj.nCenterYCoord);

        // Find distance between centers of objects; (project a right triangle and calculate hypotenuse)
        var nHalfDist = Math.sqrt((xDif * xDif) + (yDif * yDif))
    return (nHalfDist)
}




function checkSVP()
// this function makes the Shared Viewport focusable. To make it work, you must place a span (or div, or whatever)
// in the lower left corner of the page to act as a placeholder for the actual Viewport; its ID must be "SVP"
{
    // make it so you can focus on the SVP placeholder span only if the real SVP is visible
    try
    {
        if (window.external.MediaCenter.SharedViewPort.Visible == true)
         {
            /* MCTempUnFocusable is a custom property with which you can make an item temporarily non-focusable, without
                having to remove it from the aFocusableItemsArray. Set it to "false" */
            SVP.MCTempUnFocusable="false"
        }
        else
        {
            // If the Shared Viewport is hidden, maks its stand-in non-focusable
            SVP.MCTempUnFocusable="true"
        }
    }
    catch(e)
    {
        // If above gets error, probably not using Media Center, so just make placeholder unfocusable
        try
        {
            SVP.MCTempUnFocusable="true"
        }
        catch(e)
        {
            // if no object with id of "SVP" exists, ignore error
        }
    }
}

function checkCVP()
// If you are using a Custom Viewport this function makes it focusable.
// To make it work, you must have a span (or div, or whatever) on your page to act as a placeholder.
//Its ID must be "CVP" and it should be in the same position and size as your custom viewport
{
    // make it so you can focus on the CVP placeholder span only if the real CVP is visible
    try
    {
        if (window.external.MediaCenter.CustomViewPort.Visible == true)
         {
            CVP.MCTempUnFocusable="false"
        }
        else
        {
            CVP.MCTempUnFocusable="true"
        }
    }
    catch(e)
    {
        // If above gets error, probably not using Media Center, so just make placeholder unfocusable
        try
        {
            CVP.MCTempUnFocusable="true"
        }
        catch(e)
        {
            // if no object with id of "CVP" exists, ignore error
        }
    }
}