<?

// YShout: A PHP + AJAX Shoutbox
// By: Yuri Vishnevsky [http://yurivish.com/yshout/]
// Contact: email - yurivish@gmail.com, AIM - yurivish42
//
// Thanks to: Travis Roman for hours of help and testing.

include_once "../../lib/json.php";

$prefs = array (
	'adminPassword' => 'jinzora',
	'shoutMaxLines' => 5,
	'logMaxLines' => 200,
	'curseFilter' => true,
	'floodTimeout' => 2500,
	'refreshInterval' => 5000,
	'showTimestamps' => true
);

$logFiles = array (
	1 => 'main.txt'
);

// Do not edit below this line unless you know what you are doing.

// Cursewords...
$curseWords = array(
	'fuck',
	'fuk',
	'fucker',
	'fucking',
	'shit',
	'bitch',
	'bitching',
	'ass',
	'asshole',
	'asswipe',
	'dipshit',
	'cunt',
	'cock',
	'douche',
	'bullshit'	
);


// Set the log file which will be used for this session.
if (isset($_POST['file'])) {
	$fIndex = $_POST['file'];
	if (isset($logFiles[$fIndex]))
		$logFiles['current'] = $logFiles[$fIndex];
	else
		$logFiles['current'] = $logFiles[1];
}
else
	$logFiles['current'] = $logFiles[1];

// Set directories
$dirs = array (
	'logs' => 'logs',
	'prefs' => 'config'
);

// Set the paths for files
$paths = array (
	'ban' => $dirs['prefs'] . '/bans.txt',
	'log' => $dirs['logs'] . '/' . $logFiles['current'],
	'history' => $dirs['logs'] . '/history.' . $logFiles['current']
);

// Init the JSON parser
$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

error_reporting(E_ALL);
session_start();

// Show the shout history, if that's what the client wants.
if (isset($_GET['history'])){
	echo history();
	exit;
}

// Exit if there are no POST variables
if (!isset($_POST['reqType'])) doError('No reqType.');
if (isset($_SESSION['AdminLoggedIn'])) checkBanned();


$reqType = $_POST['reqType'];

switch($reqType) {
	case 'init':
		initVars();
		checkCookies();
		checkBanned();

		echo newShouts(true);
		break;

	case 'shout':
		$shoutText = $_POST['shout'];
		$shoutName = $_POST['name'];

		// Parse the message
		if(!processCommand($shoutText)) 
			shout($shoutName, $shoutText);

		// Allow execution to flow into refresh
	case 'refresh':
		$newShouts = newShouts();
		if ($newShouts) echo $newShouts;
		break;	
}

// Initialize the session variables to be used for the rest of this chat session
function initVars() {
	$_SESSION['AdminLoggedIn'] = false;
	$_SESSION['MostRecentTimestamp'] = 0;
	$_SESSION['YPath'] = $_POST['yPath'];
}

// Parse the cookie data (currently only for auto-login)
function checkCookies() {
	if (isset($_COOKIE['yshoutPHP']))
		login($_COOKIE['yshoutPHP']);
}

// Process commands such as logging in and banning
function processCommand($cmdString) {
	if (substr($cmdString, 0, 1) != '/') return false;

	$firstSpace = strpos($cmdString, ' ');

	if ($firstSpace) {
		$command = substr($cmdString, 1, $firstSpace - 1);
		$args = substr($cmdString, $firstSpace + 1);
		$args = trim($args);
		
		if ($args == '') unset($args);
		else $args = explode(' ', $args);
		
	} else {
		$command = substr($cmdString, 1);
	}

	
	switch($command) {
		case 'help':
			if (isset($args))
				showHelp($args[0]);
			else
				showHelp('help');
			break;
			
		case 'login':
			if (isset($args))
				if (login(md5($args[0])))
					sysShout('Logged in! <a href="javascript:window.location.reload()">Refresh</a> this browser window to view information about shouts that were made before you logged in.');
				else
					sysShout('Login failed.');
			break;

		case 'logout':
			if (logout())
				sysShout('Logged out.');
			else
					sysShout('Logout failed.');
			break;

		case 'ban':
			if (isset($args)) {
				if (ban($args[0]))
					sysShout('Banned ' . formatString($args[0]) . '.');
				else
					sysShout('Couldn\'t ban ' . formatString($args[0]) . '.');
			}
			break;

		case 'unban':
			if (isset($args)) {
				if (unban($args[0]))
					sysShout('Unbanned ' . formatString($args[0]) . '.');
				else
					sysShout('Couldn\'t unban ' . formatString($args[0]) . '.');
					
			}
			break;

		case 'bans':
			listBans();
			break;

		case 'clearbans':
			if (clearBans())
				sysShout('Bans cleared.');
			else
				sysShout('Couldn\'t clear bans.');
				break;

		case 'clear':
			if (clearLog())
				sysShout('All shouts cleared; <a href="javascript:window.location.reload()">refresh</a> your browser window.');
			else
				sysShout('Couldn\'t clear the chat.');
			break;
	}

	return true;
}

// Command help
function showHelp($cmd) {
	$existsFor = 	' Help exists for:<br>' .
		makeSetShoutText('/help login', 'login') . ', ' .
		makeSetShoutText('/help logout', 'logout') . ', ' .
		makeSetShoutText('/help ban', 'ban') . ', ' .
		makeSetShoutText('/help unban', 'unban') . ', ' .
		makeSetShoutText('/help bans', 'bans') . ', ' .
		makeSetShoutText('/help clearbans', 'clearbans') . ', and ' .
		makeSetShoutText('/help help', 'help') . '.';
		
	$help = 'Sorry, help for "'. $cmd .'" does not exist.<br>' .
		$existsFor;

	switch($cmd) {
		case 'login':
			$help = '/login [password]<br>' .
				'Logs you in an administrator.';
				break;
				
		case 'logout':
			$help = '/logout<br>' .
				'Logs you out (removes administrator privileges).';
				break;
				
		case 'ban':
			$help = '/ban [ip]<br>' .
				'Admin only.<br>' .
				'Bans [ip] from viewing the shoutbox, and from shouting.';
				break;
				
		case 'unban':
			$help = '/unban [ip]<br>' .
				'Admin only.<br>' .
				'Unbans [ip], if they were banned. Otherwise it does absolutely nothing, which is also pretty cool.';
				break;
				
		case 'bans':
			$help = '/bans<br>' .
				'Admin only.<br>' .
				'Lists bans, allowing you to easily unban people.';
				break;

		case 'clearbans':
			$help = '/clearbans<br>' .
				'Admin only.<br>' .
				'Purges all bans from the banfile.';
				break;

		case 'clear':
			$help = '/clear<br>' .
				'Admin only.<br>' .
				'Clears the chat and history.';
				break;

		case 'help':
			$help = '/help [command]<br>' .
				'Shows you the syntax for [command], along with a short description.' .
				$existsFor;
				break;
	}
	
	sysShout($help);
}

// List all the banned IP's
function listBans() {
	global $dirs, $paths;
	if (!isAdmin()) return false;

	ensureExists($dirs['prefs']);
	$jData = decode($paths['ban']);

	if ($jData == null) {
		sysShout('There aren\'t any bans to list.');
		return;
	}

	$shoutText = 'Here\'s the list of bans. Click on an IP to unban.<br>';
	
	foreach ($jData as $key => $value) {
		$shoutText .= '<a href="javascript:yS.setShoutText(\'/unban ' . $value['ip'] . '\');">' . $value['ip'] . '</a><br>';
	}

	sysShout($shoutText);
}

// Shouts from the system, these don't get logged.
function sysShout($message) {
	global $dirs, $paths, $prefs, $sysShouts;
	ensureExists($dirs['logs']); // Ensure the log directory exists

	$message = $message;
	$nickname = 'YShout';
	$timestamp =  microtime_float();
	$time = date('h:i a');
	$admin = true;

	$shout = array(
		'admin' => $admin,
		'timestamp' => $timestamp,
		'nickname' => $nickname,
		'message' => $message,
		'time' => $time,
		'showuserinfo' => false,
		'shouttype' => 'system'
	);

	if (!isset($sysShouts)) $sysShouts = array();

	$sysShouts[] = $shout;

}

// Say something! This logs all shouts made.
function shout($nickname, $message) {
	global $dirs, $paths, $prefs;
	ensureExists($dirs['logs']); // Ensure the log directory exists

	$timestamp =  microtime_float();

	$nickname = substr($nickname, 0, 25);
	$message = substr($message, 0, 175);

	$nickname = formatString($nickname);
	$message = formatString($message);

	$nickname = cursesGetAway($nickname);
	$message = cursesGetAway($message);
	
	$message = parseLinks($message);
	
	$shouttype =  (isAdmin()? 'admin' : 'user');
	$ip = getIP();
	$time = date('h:i a');
	$date = date('F j');

	$shout = array(
		'timestamp' => $timestamp,
		'nickname' => $nickname,
		'message' => $message,
		
		'ipaddress' => $ip,
		'time' => $time,
		'date' => $date,
		'showuserinfo' => true,
		'shouttype' => $shouttype
	);

	$jData = decode($paths['log']);

	if ($jData == null)
		$jData = array();

	$jData[] = $shout;

	writeHistory($shout);
	truncate($jData);
	$jData = array_values($jData);
	$output = encode($jData);
	write($paths['log'], $output, 0773);
	
}

// Echo all new shouts back to the client
function newShouts($includeOptions = false) {
	global $paths, $prefs, $sysShouts;
	
	$newShouts = array();

	if ($includeOptions) {
		$newShouts['options'] = array(
			'shoutMaxLines' => $prefs['shoutMaxLines'],
			'floodTimeout' => $prefs['floodTimeout'],
			'refreshInterval' => $prefs['refreshInterval'],
			'showTimestamps' => $prefs['showTimestamps']
		);
	}
	
	$jData = decode($paths['log']);
	
	$admin = isAdmin();
	
	if ($jData != null) 
		foreach($jData as $shout) {
			$shoutTimestamp = $shout['timestamp'];
			if ($shoutTimestamp > $_SESSION['MostRecentTimestamp']) {
				if (!isset($newShouts['shouts'])) $newShouts['shouts'] = array();
				if (!$admin) {
					unset($shout['ipaddress']);
					unset($shout['showuserinfo']);
				}
	
				if (preg_match("(http:\/\/(.+?) )is", $shout['message']) == false)
					$shout['message'] = parseEmoticons($shout['message']);
	
				$newShouts['shouts'][] = $shout;
			}
		}


	if (isset($sysShouts))
		foreach($sysShouts as $shout) {
			$newShouts['shouts'][] = $shout;
		}

	
	if (isset($newShouts['shouts'])) {
		$numNew = sizeof($newShouts['shouts']);
		$_SESSION['MostRecentTimestamp'] = $newShouts['shouts'][$numNew - 1]['timestamp'];
		return encode($newShouts);
	} else {
		if ($includeOptions)
			return encode($newShouts);
	}

	return null;
}

// Write a shout to the history file
function writeHistory($shout) {
	global $paths, $prefs;

	$history = read($paths['history']);

	$aHistory = explode("\n", $history);

	$numItems = sizeof($aHistory);
	
	if ($numItems > $prefs['logMaxLines']) {
		$aHistory = array_slice($aHistory, $numItems - $prefs['logMaxLines']);
		$history = implode("\n", $aHistory);	
	}

	$msgClass = 'yshout-shout ';

	switch($shout['shouttype']) {
		case 'admin':
			$msgClass .= 'yshout-admin-shout';
			break;
		case 'system':
			$msgClass .= 'yshout-system-shout';

			break;
		case 'user':
		
			break;
	}
	
	$htmlShout = 
		'<div class="' . $msgClass . ' yshout-shout"> ' . 
			'<span class="yshout-nickname">' . $shout['nickname'] . ':</span> ' .
			'<span class="yshout-message">' . $shout['message'] . '</span>' .
		'</div>' . "\n";

	$toWrite = $history . $htmlShout;
	write($paths['history'], $toWrite);
}

// Truncate the log to a set amount of shouts
function truncate(&$jData) {
	global $prefs;
	$numItems = sizeof($jData);
	if ($numItems > $prefs['shoutMaxLines']) 
		$jData = array_slice($jData, $numItems - $prefs['shoutMaxLines']);
}

// Log in as a chat admin
function login($pwHash) {
	global $prefs;

	if ($pwHash == md5($prefs['adminPassword'])) {
		$_SESSION['AdminLoggedIn'] = true;
		$cdata = $pwHash;
		setcookie('yshoutPHP', $cdata, time() + 60 * 60 * 24 * 30);
		return true;
	}

	return false;
}

// Log out...
function logout() {
	if (!$_SESSION['AdminLoggedIn']) return false;
	$_SESSION['AdminLoggedIn'] = false;
	setcookie('yshoutPHP', 'Forty-two.', time() - 1);
	return true;
}

// Ban someone! They won't be able to access the chat until they get unbanned.
function ban($ip) {
	global $dirs, $paths;
	if (!isAdmin()) return false;
	if (!isValidIP($ip)) return false;
	
	$ip = formatString($ip); 
	
	ensureExists($dirs['prefs']);
	if (isBanned($ip)) return;
	
	$jData = decode($paths['ban']);

	if ($jData == null) $jData = array();
	
	$jData[] = array (
		'ip' => $ip
	);
	
	$jData = array_values($jData);
	$output = encode($jData);
	write($paths['ban'], $output, 0773);

	return true;
}

// How nice of you to unban people.
function unban($ip) {
	global $dirs, $paths;
	if (!isAdmin()) return false;
	if (!isValidIP($ip)) return false;

	$ip = formatString($ip); 
	ensureExists($dirs['prefs']);
	$jData = decode($paths['ban']);

	if ($jData == null) $jData = array();

	foreach ($jData as $key => $value) {
		if ($value['ip'] == $ip) {
 			unset($jData[$key]);
		}
	}

	$jData = array_values($jData);
	$output = encode($jData);
	write($paths['ban'], $output, 0773);

	return true;
}

// Check of the client is banned and if so, exit.
function checkBanned() {
	global $reqType;
	$ip = getIP();
		
	if (isBanned($ip)) {
		if (isAdmin()) {
			sysShout('Looks like someone tried to ban you! You\'re an admin though, so I\'ll take the liberty of unbanning you. You see, if all the admins are banned then the site owner\'s in a bit of a pesky situation, as he has to go and clear the ban file manually. So it\'s for your own good, I assure you.');
			unban($ip);
		}
		
		if ($reqType == 'init') {
			sysShout('You\'re banned.');
		}
		exit;
	}
}

// Clear the current log and history.
function clearLog() {
	global $paths;
	if (!isAdmin()) return false;

	if(file_exists($paths['log'])) unlink($paths['log']);
	if(file_exists($paths['history'])) unlink($paths['history']);

	return true;
}

function clearBans() {
	global $paths;
	if (!isAdmin()) return false;

	if(file_exists($paths['ban'])) unlink($paths['ban']);
	return true;
}


// Parse emoticons in the message
function parseEmoticons($shout) {
	$imgString = '<img src="' . $_SESSION['YPath'] . 'smileys/%s.gif" />';

	$shout = str_replace(
		array('8)', '8-)', '8]'), 
		sprintf($imgString, 'cool'), $shout);
		
	$shout = str_replace(
		array(':?', ':-?'), 
		sprintf($imgString, 'confused'), $shout);
	
	$shout = str_replace(
		array(':|', ':-|'), 
		sprintf($imgString, 'neutral'), $shout);
	
	$shout = str_replace(
		array(':(', ':-(', '=(', '=-(', ':[', ':-[', '=[', ':{', ':-{'), 
		sprintf($imgString, 'sad'), $shout);
		
	$shout = str_replace(
		array(':)', ':-)', '=)', '=-)', ':]', ':-]', '=]', ':}', ':-}'), 
		sprintf($imgString, 'smile'), $shout);
	
	$shout = str_replace(
		array(';)', ';-)', ';]', ';-]', ';}', ';-}'), 
		sprintf($imgString, 'wink'), $shout);
	
	$shout = stri_replace(
		array(':D', ':-D', '=D') , 
		sprintf($imgString, 'biggrin'), $shout);
	
	$shout = stri_replace(
		array(':p', ':-p', '=p', '=-p'), 
		sprintf($imgString, 'razz'), $shout);

	$shout = stri_replace(
		array(':o', ':-o', '=o', '=-o',
		 ':0', ':-0', '=0', '=-0'), 
		sprintf($imgString, 'surprised'), $shout);

	$shout = stri_replace(':cry:', sprintf($imgString, 'cry'), $shout);
	$shout = stri_replace(':shock:', sprintf($imgString, 'eek'), $shout);
	$shout = stri_replace(':evil:', sprintf($imgString, 'evil'), $shout);
	$shout = stri_replace(':lol:', sprintf($imgString, 'lol'), $shout);
	$shout = stri_replace(':x', sprintf($imgString, 'mad'), $shout);
	$shout = stri_replace(':mrgreen:', sprintf($imgString, 'mrgreen'), $shout);
	$shout = stri_replace(':oops:', sprintf($imgString, 'redface'), $shout);
	$shout = stri_replace(':roll:', sprintf($imgString, 'rolleyes'), $shout);
	$shout = stri_replace(':twisted:', sprintf($imgString, 'twisted'), $shout);

	return $shout;
}

// Make textual links into <a>'s
function parseLinks($text) {
	$text .= ' ';
	$text = preg_replace("(http:\/\/(.+?) )is", '<a href="http://$1" target="_blank">http://$1</a>', $text);
	return trim($text);
}

// Make sure the message doesn't have any HTML or other unwanted stuff, and that it is the right encoding.
function formatString($toFormat) {
	$temp = trim($toFormat);
	if (strlen($temp) > 0) $toFormat = $temp;
	 
	$toFormat = htmlentities($toFormat);	
	$toFormat = str_replace('\"', '"', $toFormat);
	$toFormat = str_replace("\'", "'", $toFormat); 
	$toFormat = utf8_decode($toFormat);
	$toFormat = ereg_replace('%u([[:alnum:]]{4})', '&#x\1;',$toFormat);

	return $toFormat;
}

// Nobody likes curses, so...
function cursesGetAway($fromHere) {
	global $prefs, $curseWords;
	if (!$prefs['curseFilter']) return $fromHere;
	
	foreach($curseWords as $curse)
		$fromHere = preg_replace("/\b$curse\b/i", str_repeat('*', strlen($curse)), $fromHere);

	return $fromHere;
}

// Is the client a chat administrator?
function isAdmin() {
	if ($_SESSION['AdminLoggedIn'] == true)
		return true;
	return false;
}

// Or could this be a banned troublemaker? :o
function isBanned($ip) {
	global $dirs, $paths;
	ensureExists($dirs['prefs']);
	$jData = decode($paths['ban']);

	if ($jData == null) return false;

	foreach ($jData as $key => $value)
		if ($value['ip'] == $ip) return true;

	return false;
}

// Get the client's IP address
function getIP() {
	if(isset($HTTP_X_FORWARDED_FOR) && $HTTP_X_FORWARDED_FOR) 
		return $HTTP_X_FORWARDED_FOR;
	else
		return $_SERVER['REMOTE_ADDR'];
}


function isValidIP($ip) {
	if ($ip == long2ip(ip2long($ip)))
		return true;
}

// Errors. :(
function doError($err) {
	echo 'Error: ' . $err . "\n";
	exit;
}

// File Functions

function read($fPath) {
	if (file_exists($fPath))
		return file_get_contents($fPath);
	else
		return;
}

function write($fPath, $fContents, $chmod = 0777) {
	$hFile = fopen($fPath, 'w');

	flock($hFile, LOCK_EX);
	fwrite($hFile, $fContents);
	flock($hFile, LOCK_UN);

	chmod($fPath, $chmod);
	
	fclose($hFile);
}

function encode($obj) {
	global $json;
	return $json->encode($obj);
}

function decode($fPath) {
	global $json;
	$fData = read($fPath);
	
	if ($fData == null) return;
	
	$jData = $json->decode($fData);
	return $jData;
}

function ensureExists($dir) {
	if (!is_dir($dir)) mkdir($dir);
}

function makeSetShoutText($js, $linkText) {
	return '<a href="javascript:yS.setShoutText(\'' . $js . '\');">' . $linkText . '</a>';
}

// PHP4 Compatibility stuff 

function stri_replace($find, $replace, $string) {
	if(!is_array($find)) $find = array($find);
	if(!is_array($replace)) {
		if(!is_array($find)) $replace = array($replace);
		else {
			// this will duplicate the string into an array the size of $find
			$c = sizeof($find);
			$rString = $replace;
			unset($replace);
			for ($i = 0; $i < $c; $i++)
				$replace[$i] = $rString;
		}

		foreach($find as $fKey => $fItem) {
			$between = explode(strtolower($fItem),strtolower($string));
			$pos = 0;
			foreach($between as $bKey => $bItem) {
				$between[$bKey] = substr($string,$pos,strlen($bItem));
				$pos += strlen($bItem) + strlen($fItem);
			}
			
			$string = implode($replace[$fKey],$between);
		}
			
		return($string);
	}
}

function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}


// History

// Display the history
function history() {
	global $paths;
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>YShout History</title>
		<style>

			body {
				padding-top: 50px;
			}
			
			h1 {
				font-family: "Trebuchet MS", Arial, sans-serif;
				font-size: 20px;
				color: #7E7E7E;
				width: 600px;
				margin: 0 auto;
				margin-bottom: 20px;
			}
		
			#yshout {
				width: 600px;
				margin: 0 auto;
				font-size: 14px;
				font-family: Helvetica, sans-serif;
			}

			.yshout-nickname {
				font-weight: bold;
				color: #A75E9E;
			}

			.yshout-shout {
				padding: 4px 0;
				border-top: 1px solid #DDDDDD;
			}
			
		</style>
	</head>
	<body>
		<h1>YShout History</h1>
		<div id="yshout">
			<?
					if (file_exists($paths['history']))
						echo file_get_contents($paths['history']);
					else
						echo 'No shouts, yay!';
			?>
		</div>
	</body>#yshout * {
	margin: 0;
	padding: 0;
	line-height: 1.8;
}
		
#yshout {
	font-family: Lucida Grande, Veranda, sans-serif;
	font-size: 11px;
	width: 460px;
	margin: 0 auto;
	overflow: hidden;
	margin-top: 10px;
	color: #404040;
	background: #FFF;
}

#yshout fieldset {
	border: 0;
}

#yshout-form {
	padding: 10px;
	height: 20px;
	background: #EECBDA;
}

#yshout-shout-nickname {
	width: 100px;
	margin-right: 5px;
}

#yshout-shout-text {
	width: 270px;
	margin-right: 5px;
}

#yshout-shout-button {
	width: 50px;
	color: #000 !important;
}

#yshout-shouts {
	padding-bottom: 10px;
}

#yshout .yshout-before-focus {
	color: #8B8B8B;
}

#yshout .yshout-after-focus {
	color: #000;
}

#yshout .yshout-invalid {
	background: #F9FFBB;
}

#yshout .yshout-message-timestamp {
	color: #747474;
}

#yshout .yshout-nickname {
	font-weight: bold;
	color: #973161;
}

#yshout .yshout-shout {
	padding: 3px 10px;
	border-left: 1px solid #E6E6E6;
	border-right: 1px solid #E6E6E6;
}

#yshout .yshout-admin-shout {
	border-left: 1px solid #E23980;
	border-right: 1px solid #E23980;
	color: #000;
	background: #F2F2F2;
}

#yshout .yshout-system-shout {
	border-left-color: #B3B3B3;
	border-right-color: #B3B3B3;
	color: #000;
	background: #FAFAFA;
}

#yshout .yshout-shout-infovisible {
	border-left-color: #E6B1C7;
	border-right-color: #E6B1C7;
	background: #FFF;
	color: #000;
	padding-bottom: 5px;
}

#yshout a {
	color: #CA286C;
}

#yshout a:hover {
	color: #8D8D8D;
	text-decoration: none;
}

</html>
	<?
} 

?>