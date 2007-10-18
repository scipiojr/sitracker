<?php
// reset_user_password.php - Resets the users password to a known value
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// FIXME This isn't a very secure way of handling forgotten passwords
// We should do something better really
// FIXME with the new forgotten password feature introduced for 3.30
// we may not need this now?


$permission=9; // change user permissions
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$id = cleanvar($_REQUEST['id']);

// Don't allow resetting in DEMO MODE
if ($CONFIG['demo'])
{
    confirmation_page("3", "manage_users.php", "<h2>You cannot reset passwords while in DEMO MODE</h2><p align='center'>{$strPleaseWaitRedirect}...</p>");
    exit;
}


if ($id > 1)
{
    if (empty($id)) throw_error('!Error setting password.  User ID number was zero or blank','');

    $newpasswordplain = generate_password();
    $newpassword=md5($newpasswordplain);

    $sql = "UPDATE users SET password='$newpassword' WHERE id='$id'";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    confirmation_page("7", "manage_users.php", "<h2>Password was reset to: '$newpasswordplain' (sans-quotes)</h2><p align='center'>Please wait while you are redirected...</p>");
}
else
{
    confirmation_page("3", "manage_users.php", "<h2>You cannot reset this users password</h2><p align='center'>{$strPleaseWaitRedirect}...</p>");
}
?>
