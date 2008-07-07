<?php
// control_panel.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2008 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//
// Author: Paul Heaney <paulheaney[at]users.sourceforge.net>

@include ('set_include_path.inc.php');
$permission=11; // View sites

require ('db_connect.inc.php');
require ('functions.inc.php');
// This page requires authentication
require ('auth.inc.php');

include ('htmlheader.inc.php');

$title = $strShowOrphandedContacts;

$sql = "SELECT * FROM `{$dbContacts}` WHERE siteid = 0";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);

echo "<h2>{$title}</h2>";

if (mysql_num_rows($result) > 0)
{
    echo "<p align='center'>{$strOrphanedSites}</p>";

    echo "<div><table class='vertical'>";
    echo "<tr><th>{$strSiteName}</th></tr>";

    while ($contact = mysql_fetch_object($result))
    {
        echo "<tr><td>{$contact->forenames} {$contact->surname}</td></tr>";
    }

    echo "</table></div>";
}
else
{
    echo $strNoOrphandedContacts;
}

include ('htmlfooter.inc.php');

?>
