<?php
// base.inc.php - core constants and files
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.

// Prevent script from being run directly (ie. it must always be included
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']))
{
    exit;
}

//**** Begin constant/variable definitions ****//

// For tempincoming
define("REASON_POSSIBLE_NEW_INCIDENT", 1);
define("REASON_INCIDENT_CLOSED", 2);

// Version number of the application, (numbers only)
$application_version = '3.45';

// Revision string, e.g. 'beta2' or 'svn' or ''
$application_revision = 'svn';

// Clean PHP_SELF server variable to avoid potential XSS security issue
$_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0,
                            (strlen($_SERVER['PHP_SELF'])
                            - @strlen($_SERVER['PATH_INFO'])));

$fsdelim = DIRECTORY_SEPARATOR;

// Time settings
$now = time();
// next 16 hours, based on reminders being run at midnight this is today
$today = $now + (16 * 3600);
$lastweek = $now - (7 * 86400); // the previous seven days
$todayrecent = $now -(16 * 3600);  // past 16 hours

$CONFIG['upload_max_filesize'] = return_bytes($CONFIG['upload_max_filesize']);

//**** Begin internal functions ****//
// Append SVN data for svn versions
if ($application_revision == 'svn')
{
    // Add the svn revision number
    preg_match('/([0-9]+)/','$LastChangedRevision$',$revision);
    $application_revision .= $revision[0];
}

// Set a string to be the full version number and revision of the application
$application_version_string = trim("v{$application_version} {$application_revision}");

// Report all PHP errors
error_reporting(E_ALL);
$oldeh = set_error_handler("sit_error_handler");

// Decide which language to use and setup internationalisation
require (dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'i18n/en-GB.inc.php');
if ($CONFIG['default_i18n'] != 'en-GB')
{
    include (dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."i18n/{$CONFIG['default_i18n']}.inc.php");
}
if (!empty($_SESSION['lang'])
    AND $_SESSION['lang'] != $CONFIG['default_i18n'])
{
    include (dirname( __FILE__ ).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR."i18n/{$_SESSION['lang']}.inc.php");
}
ini_set('default_charset', $i18ncharset);


//**** Begin functions ****//

/**
    * Strip slashes from an array
    * @param $data an array
    * @return An array with slashes stripped
*/
function stripslashes_array($data)
{
    if (is_array($data))
    {
        foreach ($data as $key => $value)
        {
            $data[$key] = stripslashes_array($value);
        }
        return $data;
    }
    else
    {
        return stripslashes($data);
    }
}

?>