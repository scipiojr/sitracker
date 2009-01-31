<?php
// incident_reopen.php - Form for re-opening a closed incident
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2009 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
$permission = 34; // Reopen Incidents

require ($lib_path.'db_connect.inc.php');
require ($lib_path.'functions.inc.php');

// This page requires authentication
require ($lib_path.'auth.inc.php');

// External variables
$submit = cleanvar($_REQUEST['submit']);
$id = cleanvar($_REQUEST['id']);
$newstatus = cleanvar($_REQUEST['newstatus']);
$bodytext = cleanvar($_REQUEST['bodytext']);

$sql = "SELECT * FROM `{$dbIncidents}` WHERE id = $id LIMIT 1";
$result = mysql_query($sql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($result) > 0)
{
    $incident = mysql_fetch_object($result);
}

// Find out whether the service level in use allows reopening
$slsql = "SELECT allow_reopen FROM `{$dbServiceLevels}` ";
$slsql .= "WHERE tag = '{$incident->servicelevel}' ";
$slsql .= "AND priority = '{$incident->priority}' LIMIT 1";

$slresult = mysql_query($slsql);
if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
if (mysql_num_rows($slresult) > 0)
{
    list($allow_reopen) = mysql_fetch_row($slresult);
}

if ($allow_reopen == 'yes')
{
    if (empty($submit))
    {
        // No submit detected show update form
        $incident_title = incident_title($id);
        $title = "{$strReopen}: ".$id . " - " . $incident_title;
        include ('inc/incident_html_top.inc.php');

        echo "<h2>{$strReopenIncident}</h2>";
        echo "<form action='{$_SERVER['PHP_SELF']}?id={$id}' method='post'>";
        echo "<table class='vertical'>";
        echo "<tr><th>{$strUpdate}</th><td><textarea name='bodytext' rows='20' ";
        echo "cols='60'></textarea></td></tr>";
        echo "<tr><th>{$strStatus}</th><td>".incidentstatus_drop_down("newstatus", 1);
        echo "</td></tr>\n";
        echo "</table>";
        echo "<p><input name='submit' type='submit' value='{$strReopen}' /></p>";
        echo "</form>";
        include ('inc/incident_html_bottom.inc.php');
    }
    else
    {
        // Reopen the incident
        // update incident
        $time = time();
        $sql = "UPDATE `{$dbIncidents}` SET status='$newstatus', ";
        $sql .= "lastupdated='$time', closed='0' WHERE id='$id' LIMIT 1";
        mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        $owner = incident_owner($id);
        // add update
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, ";
        $sql .= "bodytext, timestamp, currentowner, currentstatus) ";
        $sql .= "VALUES ({$id}, {$sit[2]}, 'reopening', '{$bodytext}', {$time}, ";
        $sql .= "{$owner}, ".STATUS_ACTIVE.")";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);

        // Insert the first SLA update for the reopened incident, this indicates
        // the start of an sla period
        // This insert could possibly be merged with another of the 'updates'
        // records, but for now we keep it seperate for clarity
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, ";
        $sql .= "timestamp, currentowner, currentstatus, customervisibility, ";
        $sql .= "sla, bodytext) ";
        $sql .= "VALUES ('{$id}', '{$sit[2]}', 'slamet', '{$now}', '{$owner}'";
        $sql .= STATUS_ACTIVE.", 'show', 'opened','The incident is open and awaiting action.')"; // FIXME i18n
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        // Insert the first Review update, this indicates the review period of an incident has restarted
        // This insert could possibly be merged with another of the 'updates' records, but for now we keep it seperate for clarity
        $sql  = "INSERT INTO `{$dbUpdates}` (incidentid, userid, type, timestamp, currentowner, currentstatus, customervisibility, sla, bodytext) ";
        $sql .= "VALUES ('{$id}', '0', 'reviewmet', '{$now}', '{$owner}', ".STATUS_ACTIVE.", 'hide', 'opened','')";
        mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result)
        {
            include ('inc/incident_html_top.inc.php');
            echo "<p class='error'>{$strUpdateIncidentFailed}</p>\n";
            include ('inc/incident_html_bottom.inc.php');
        }
        else
        {
            html_redirect("incident_details.php?id={$id}");
        }
    }
}
else
{
    html_redirect("incident_details.php?id={$id}", FALSE, $strServiceLevelPreventsReopen);
}

?>
