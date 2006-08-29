
var counter = 0
var spinnerText
    
function initializeSpinner(oParentContainer)
    {
        // Box for display of select value
        var oSpinnerBox;
        // selected <option> element
        var oSpinnerSelect;
        for (n=0; n<oParentContainer.all.length; n++)
        {
            var obj = oParentContainer.all[n];
            if (obj.MCSpinnerSelect == "true") oSpinnerSelect = obj;
            if (obj.MCSpinnerBox == "true") oSpinnerBox = obj;
        }
        var oSpinnerOptSelected = oSpinnerSelect.options[oSpinnerSelect.selectedIndex];
        if (oSpinnerOptSelected == null) oSpinnerOptSelected = oSpinnerSelect.options[0];
        // show selected option in Spinner box
        oSpinnerBox.innerText = oSpinnerOptSelected.innerText
    }
    
function setSpinner()
    {        
        // button user clicked
        var oClickedBtn = event.srcElement;
        // direction (up/down) to move selections, based on button clicked        
        var direction
        if (oClickedBtn.MCSpinnerPlus=="true")direction = "down";
        else direction = "up";
        // identifyl all the elements of the spinner; these include
        // parent container, plus btn, minus btn, box for display of value, and hidden <select> element.
        // Parent container
        //var oParentContainer = .parentElement // change this
        oParentContainer = getSpinnerParent(oClickedBtn)

        // Box for display of select value
        var oSpinnerBox;
        // selected <option> element
        var oSpinnerSelect;
        // Plus btn
        var oSpinnerPlus;
        //Minus btn
        var    oSpinnerMinus;
        // selected <option> in <select>
        var oSpinnerOptSelected;
        // spinner box is parent of <select>
        //spinnerBox = oClickedBtn.parentElement;
        // find values for all of above, based on their CSS class names
        for (i=0; i<oParentContainer.all.length; i++)
        {
            var obj = oParentContainer.all[i];
            // MCSpinnerPlus and MCSpinnerMinus are custom properties set in the HTML tags
            // to mark the items as spinner buttons
            if (obj.MCSpinnerPlus == "true") oSpinnerPlus = obj;
            if (obj.MCSpinnerMinus == "true") oSpinnerMinus = obj;
            if (obj.MCSpinnerSelect == "true") oSpinnerSelect = obj;
            if (obj.MCSpinnerBox == "true") oSpinnerBox = obj;
        }    
            

        // find selected option
        oSpinnerOptSelected = oSpinnerSelect.options[oSpinnerSelect.selectedIndex];
        // variable for next option to display
        var oNextOpt;
        // set value for next option to display, based on direction (up/down) button clicked
        if (direction == "down") oNextOpt = oSpinnerOptSelected.nextSibling;
        else oNextOpt = oSpinnerOptSelected.previousSibling;
        // if you are on first or last option, oNextOpt might not be valid. If not, stay on current option
        if (oNextOpt == null || oNextOpt.tagName == null) 
        {
            oNextOpt = oSpinnerOptSelected
        }
        // set oNextOpt as selected element
        oNextOpt.selected = true
        // (remember, the real select box is invisible) Show selection contents in spinner box
        oSpinnerBox.innerText = oNextOpt.innerText
        // if there is no next sibling for new selected option, you are at the end, so disable plus btn
        if (oNextOpt.nextSibling == null || oNextOpt.nextSibling.tagName == null) 
        { 
            disableBtn(oSpinnerPlus);
            oCurFocus = oSpinnerMinus;
            oSpinnerMinus.focus();
        }
        // else make sure plus btn is enabled
        else enableBtn(oSpinnerPlus)
        // if there is no previous sibling for new selected option, you are at the beginning, so disable minus btn
        if (oNextOpt.previousSibling == null || oNextOpt.previousSibling.tagName == null)
        {
             disableBtn(oSpinnerMinus);
             oCurFocus = oSpinnerPlus;
             oSpinnerPlus.focus();
        }
        // else make sure minus btn is enabled
        else enableBtn(oSpinnerMinus)        
    }
    function getSpinnerParent(btn)
    {
        var oTempObj = btn
        for (i=0; i<5000; i++)
        {
             oTempObj = oTempObj.parentElement
            if (oTempObj.MCSpinnerContainer == "true") 
            {
                return oTempObj;
                break;
            }
            if (oTempObj.tagName == "BODY") return null
        }
    }
    function disableBtn(obj)
    {
        obj.MCTempUnFocusable = "true"
        obj.style.filter = "alpha (opacity = 30)"
    }
    function enableBtn(obj)
    {
        // remove temporary unfocusability
        if (obj.MCTempUnFocusable == "true")obj.MCTempUnFocusable = "false"
        // remove transparency filter
        obj.style.filter = "none"
    }