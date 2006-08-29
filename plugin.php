<?php
/*
+ ----------------------------------------------------------------------------+
|     e107 website system
|
|     ©Steve Dunstan 2001-2002
|     http://e107.org
|     jalist@e107.org
|
|     Released under the terms and conditions of the
|     GNU General Public License (http://gnu.org).
|
|     $Source$
|     $Revision$
|     $Date$
|     $Author$
+----------------------------------------------------------------------------+
*/

if (!defined('e107_INIT')) { exit; }

// Plugin info -------------------------------------------------------------------------------------------------------
$eplug_name = "Jinzora";
$eplug_version = "2.5";
$eplug_author = "Ross Carlson / Ben Dodson";
$eplug_url = "http://jinzora.org";
$eplug_email = "ross@jinzora.org";
$eplug_description = "A complete, web-based multimedia manager.";
//$eplug_compatible = "e107v7";
//$eplug_readme = "readme.rtf";
// leave blank if no readme file
$eplug_compliant = TRUE;

// Name of the plugin's folder -------------------------------------------------------------------------------------
$eplug_folder = "jinzora2";

// Mane of menu item for plugin ----------------------------------------------------------------------------------
$eplug_menu_name = "Jinzora";

// Name of the admin configuration file --------------------------------------------------------------------------
//$eplug_conffile = "admin_config.php";

// Icon image and caption text ------------------------------------------------------------------------------------
$eplug_icon = $eplug_folder."/style/images/asx_banner.gif";
$eplug_icon_small = $eplug_folder."/style/images/asx_banner.gif";
$eplug_caption = "Configure Jinzora";

// List of preferences -----------------------------------------------------------------------------------------------
$eplug_prefs = array();

// List of table names -----------------------------------------------------------------------------------------------
$eplug_table_names = array();

// List of sql requests to create tables -----------------------------------------------------------------------------
$eplug_tables = array();


// Create a link in main menu (yes=TRUE, no=FALSE) -------------------------------------------------------------
$ec_dir = e_PLUGIN."jinzora2/";
$eplug_link = TRUE;
$eplug_link_name = "Jinzora"; // "Calendar";
$eplug_link_url = "".$ec_dir."index.php";
$eplug_link_perms = "Everyone"; // Everyone, Guest, Member, Admin 


// Text to display after plugin successfully installed ------------------------------------------------------------------
$eplug_done = "Jinzora has been installed.";


// upgrading ... //

$upgrade_add_prefs = "";

$upgrade_remove_prefs = "";

$upgrade_alter_tables = array();

$eplug_upgrade_done = "Upgrade complete.";





?>