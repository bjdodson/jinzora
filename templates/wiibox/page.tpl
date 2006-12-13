{literal}
<script type="text/javascript">
/*************************************************************************
  This code is from Dynamic Web Coding at http://www.dyn-web.com/
  See Terms of Use at http://www.dyn-web.com/bus/terms.html
  regarding conditions under which you may use this code.
  This notice must be retained in the code as is!
*************************************************************************/

function getDocHeight(doc) {
  var docHt = 0, sh, oh;
  if (doc.height) docHt = doc.height;
  else if (doc.body) {
    if (doc.body.scrollHeight) docHt = sh = doc.body.scrollHeight;
    if (doc.body.offsetHeight) docHt = oh = doc.body.offsetHeight;
    if (sh && oh) docHt = Math.max(sh, oh);
  }
  return docHt;
}

function setIframeHeight(iframeName) {
  var iframeWin = window.frames[iframeName];
  var iframeEl = document.getElementById? document.getElementById(iframeName): document.all? document.all[iframeName]: null;
  if ( iframeEl && iframeWin ) {
    iframeEl.style.height = "auto"; // helps resize (for some) if new doc shorter than previous  
    var docHt = getDocHeight(iframeWin.document);
    // need to add to height to be sure it will all show
    if (docHt) iframeEl.style.height = docHt + 30 + "px";
  }
}

function loadIframe(iframeName, url) {
  if ( window.frames[iframeName] ) {
    window.frames[iframeName].location = url;   
    return false;
  }
  else return true;
}
</script>
{/literal}


<iframe name="embeddedPlayer" id="embeddedPlayer" style="width:100%; height:{$playerHeight}; border:none;" src="{$playerURL}">
</iframe>
<iframe name="body" id="body" style="width:100%; border:none;" src="{$bodyURL}">
Your browser doesn't support iframes, sorry...
</iframe>