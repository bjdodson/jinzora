/* Though the cursor is invisible until you move the mouse, extensibility pages get a mouseover event
when they load. If your invisible cursor is over a focusable item, this mouseover event can cause the
focus to land on the wrong item when the page loads. The bFirstMouseover variable (originally set in the
MoveFocus.js file) is used to identify that initial mouseover event and nullify it. 
The first mouseover event fires when the page loads. Then there is a statement in the 
StartFocus function, in the MoveFocus.JS file, which uses the setTimeout method to wait until
after that first mouseover event fires and then resets bFirstMouseover to false, so the subsequent 
mouseovers will be handled normally */

function mouseOver(item)
{
    // if this is the first mouseover event that fires automatically when the page loads, return
    if (bFirstMouseover == true) return

    // make sure item is focusable
    if (event.srcElement.MCFocusable != "true" || event.srcElement.MCTempUnFocusable == "true") return
    // update oCurFocus variable
    oCurFocus = item
    // since user used mouse instead of an arrow key, reset sPrevArrowDirection variable to null
    sPrevArrowDirection = null
    // move focus to new item
    item.focus()
}

function hilite(item)
{
    // if item is not focusable, return
    if (event.srcElement.MCFocusable != "true") return

    var sClass = item.className
    //check if class name already ends in "_hilite" -- if so, quit function
    if (checkSubstring(sClass) == true) return
    // change element's class name for highlighting
   item.className = sClass + "_hilite"
    try
    {
            /*as needed, update the numeric value for the item counter found at the lower right of
            each scrollable menu in the templates */
           updateCounter()
    }
    catch(e)
    {
        //ignore error
    }
}

function restore(item)
{
    //if (event.srcElement.MCFocusable != "true") return
    var sClass = item.className
    //check if class name ends in "_hilite" -- if not, quit function
    if (checkSubstring(sClass) == false) return
    // remove "_hilite" (last 8 characters) from class name to return to unhighlighted state
   item.className = sClass.substring(0,(sClass.length -7))
}

function checkSubstring(sClass)
{
    // this function checks whether a class name ends in "_hilite"
    if (sClass.substring((sClass.length -7), sClass.length) == "_hilite")
    {
        return true
    }
    return false
}