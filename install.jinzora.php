<?php 
function com_install() {
global $database;

// Set up new icons for admin menu
$database->setQuery("UPDATE #__components SET admin_menu_img='components/com_jinzora/style/images/jinzora.png' WHERE admin_menu_link='option=com_jinzora'");
$iconresult[0] = $database->query();

// Show installation result to user
$str = '
<center>
<table width="100%" border="0">
  <tr>
    <td>
      <strong>Jinzora Component</strong><br/>
      <br/>
      This component is released under the terms and conditions of the <a href="index2.php?option=com_admisc&task=license">GNU General Public License</a>.
    </td>
  </tr>
  <tr>
    <td>
      <code>Installation: <font color="green">succesfull</font></code>
	<br/><br/>
	In the Mambo Administration page click on <strong>Menu - Main Menu</strong> <br/>
	Click on <strong>New</strong> (at the top of the page) <br/>
	Click on <strong>Component</strong> <br/>
	Choose a name (ie Jinzora) <br/>
	Select <strong>Jinzora</strong> as the <strong>Component</strong> <br/>
	click <strong>Save</strong> <br/>
	Click the menu item for <strong>Jinzora</strong> (or whatever you called it) - You will now be taken through the Jinzora installer <br/>
	- Choose your language and click <strong>Next</strong> <br/>
	Go through the installer, it is documented. <br/>
	DONE!!
    </td>
  </tr>
</table>
</center>';
return $str;
}
?>
