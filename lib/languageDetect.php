<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
/**
 * This file is included in both
 * the installer and the main app,
 * so it has been split from other
 * utilities files. **/
function getDefaultLanguage() {
  if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $locale = substr($_SERVER['HTT_ACCEPT_LANGUAGE'],0,2);
  } else {
    return getFallbackLanguage();
  }

  switch ($locale) {
  case "bg":
    return "bulgarian";
    break;
  case "de":
    return "german";
    break;
  case "nl":
    return "dutch";
    break;
  case "sv":
    return "swedish";
    break;
  default:
    return getFallbackLanguage();
  }
}

function getFallbackLanguage() {
  return "english";
}

?>