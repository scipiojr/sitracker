<?php
// tasks.inc.php - List tasks
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Authors: Ivan Lucas <ivanlucas[at]users.sourceforge.net>
//          Kieran Hogg <kieran_hogg[at]users.sourceforge.net>
//          Paul Heaney <paulheaney[at]users.sourceforge.net>
// called by tasks.php

// External variables
$user = cleanvar($_REQUEST['user']);
$show = cleanvar($_REQUEST['show']);
$sort = cleanvar($_REQUEST['sort']);
$order = cleanvar($_REQUEST['order']);
$incident = cleanvar($_REQUEST['incident']);
$mode;

?>
<script type='text/javascript'>
<!--
function Activity()
{
    var id;
    var start;
}

var dataArray = new Array();
var count = 0;
var closedDuration = 0;

function addActivity(act)
{
    dataArray[count] = act;
    count++;
}

function setClosedDuration(closed)
{
    closedDuration = closed;
}

function formatSeconds(secondsOpen)
{
    var str = "";
    if(secondsOpen >= 86400)
    {   //days
        var days = Math.floor(secondsOpen/86400);
        str += days+" days ";
        secondsOpen-=(days*86400);
    }

    if(secondsOpen >= 3600)
    {   //hours
        var hours = Math.floor(secondsOpen/3600);
        str += hours+" hours ";
        secondsOpen-=(hours*3600);
    }

    if(secondsOpen > 60)
    {   //minutes
        var minutes = Math.floor(secondsOpen/60);
        str += minutes+" minutes ";
        secondsOpen-=(minutes*60);
    }

    if(secondsOpen > 0)
    {  // seconds
        str += secondsOpen+" seconds";
    }

    return str;
}

function countUp()
{
    var now = new Date();

    var sinceEpoch = Math.round(new Date().getTime()/1000.0);

    var closed = closedDuration;

    var i = 0;
    for(i=0; i < dataArray.length; i++)
    {
        var secondsOpen = sinceEpoch-dataArray[i].start;

        closed += secondsOpen;

        var str = formatSeconds(secondsOpen);

        byId("duration"+dataArray[i].id).innerHTML = "<em>"+str+"</em>";
    }

    byId('totalduration').innerHTML = formatSeconds(closed);
}

setInterval("countUp()", 1000); //every 1 seconds
//-->
</script>
<?php


if(!empty($incident))
{
    $mode = 'incident';

    //get info for incident-->task linktype
    $sql = "SELECT DISTINCT origcolref, linkcolref ";
    $sql .= "FROM links, linktypes ";
    $sql .= "WHERE links.linktype=4 ";
    $sql .= "AND linkcolref={$incident} ";
    $sql .= "AND direction='left'";
    $result = mysql_query($sql);

    //get list of tasks
    $sql = "SELECT * FROM tasks WHERE 1=0 ";
    while($tasks = mysql_fetch_object($result))
    {
        $sql .= "OR id={$tasks->origcolref} ";
    }
    $result = mysql_query($sql);

    if($mode == 'incident')
    {
        echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> ";
        echo "{$strActivities}</h2>";
    }
    echo "<p align='center'>{$strIncidentActivitiesIntro}</p>";
}
else
{// Defaults
    if (empty($user) OR $user=='current') $user=$sit[2];
    // If the user is passed as a username lookup the userid
    if (!is_number($user) AND $user!='current' AND $user!='all')
    {
        $usql = "SELECT id FROM users WHERE username='{$user}' LIMIT 1";
        $uresult = mysql_query($usql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_WARNING);
        if (mysql_num_rows($uresult) >= 1) list($user) = mysql_fetch_row($uresult);
        else $user=$sit[2]; // force to current user if username not found
    }
    echo "<h2><img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/32x32/task.png' width='32' height='32' alt='' /> ";
    echo user_realname($user,TRUE) . "'s {$strTasks}:</h2>";

    // show drop down select for task view options
    echo "<form action='{$_SERVER['PHP_SELF']}' style='text-align: center;'>";
    echo "{$strView}: <select class='dropdown' name='queue' onchange='window.location.href=this.options[this.selectedIndex].value'>\n";
    echo "<option ";
    if ($show == '' OR $show == 'active') echo "selected='selected' ";
    echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;show=active&amp;sort=$sort&amp;order=$order'>{$strActive}</option>\n";
    echo "<option ";
    if ($show == 'completed') echo "selected='selected' ";
    echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;show=completed&amp;sort=$sort&amp;order=$order'>{$strCompleted}</option>\n";
    echo "<option ";
    if ($show == 'incidents') echo "selected='selected' ";
    echo "value='{$_SERVER['PHP_SELF']}?user=$user&amp;show=incidents&amp;sort=$sort&amp;order=$order'>{$strIncidents}</option>";
    
    echo "</select>\n";
    echo "</form><br />";

    $sql = "SELECT * FROM tasks WHERE owner='$user' ";
    if ($show=='' OR $show=='active' ) $sql .= "AND (completion < 100 OR completion='' OR completion IS NULL)  AND distribution != 'incident' ";
    elseif ($show=='completed') $sql .= "AND (completion = 100) AND distribution != 'incident' ";
    elseif ($show=='incidents') $sql .= "AND distribution = 'incident' ";
    else $sql .= "AND 1=2 "; // force no results for other cases
    if ($user != $sit[2]) $sql .= "AND distribution='public' ";

    if (!empty($sort))
    {
        if ($sort=='id') $sql .= "ORDER BY id ";
        elseif ($sort=='name') $sql .= "ORDER BY name ";
        elseif ($sort=='priority') $sql .= "ORDER BY priority ";
        elseif ($sort=='completion') $sql .= "ORDER BY completion ";
        elseif ($sort=='startdate') $sql .= "ORDER BY startdate ";
        elseif ($sort=='duedate') $sql .= "ORDER BY duedate ";
        elseif ($sort=='enddate') $sql .= "ORDER BY enddate ";
        elseif ($sort=='distribution') $sql .= "ORDER BY distribution ";
        else $sql = "ORDER BY id ";
        if ($order=='a' OR $order=='ASC' OR $order='') $sql .= "ASC";
        else $sql .= "DESC";
    }
    else $sql .= "ORDER BY IF(duedate,duedate,99999999) ASC, duedate ASC, startdate DESC, priority DESC, completion ASC";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
}

//common code
if (mysql_num_rows($result) >=1 )
{
    if($show) $filter=array('show' => $show);
    echo "<br /><table align='center'>";
    echo "<tr>";

    if($mode != 'incident')
    {
        $totalduration = 0;
        $closedduration = 0;
        if ($user == $sit[2])
        {
            echo colheader('distribution', "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='Public/Private' alt='Private' style='border: 0px;' />", $sort, $order, $filter);
        }
        else $filter['user'] = $user;

        echo colheader('id', $strID, $sort, $order, $filter);
        echo colheader('name', $strTask, $sort, $order, $filter);
        echo colheader('priority', $strPriority, $sort, $order, $filter);
        echo colheader('completion', $strCompletion, $sort, $order, $filter);
        echo colheader('startdate', $strStartDate, $sort, $order, $filter);
        echo colheader('duedate', $strDueDate, $sort, $order, $filter);
        if ($show=='completed') echo colheader('enddate', $strEndDate, $sort, $order, $filter);
    }
    else
    {
        echo colheader('id', $strID, $sort, $order, $filter);
        echo colheader('startdate', $strStartDate, $sort, $order, $filter);
        echo colheader('completeddate', $strCompleted, $sort, $order, $filter);
        echo colheader('duration', $strDuration, $sort, $order, $filter);
        echo colheader('lastupdated', $strLastUpdated, $sort, $order, $filter);
        echo colheader('owner', $strOwner, $sort, $order, $filter);
    }
    echo "</tr>\n";
    $shade='shade1';
    while ($task = mysql_fetch_object($result))
    {
        $duedate = mysql2date($task->duedate);
        $startdate = mysql2date($task->startdate);
        $enddate = mysql2date($task->enddate);
        $lastupdated = mysql2date($task->lastupdated);
        echo "<tr class='$shade'>";
        if ($user == $sit[2])
        {
            echo "<td>";
            if ($task->distribution=='private') echo " <img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/private.png' width='16' height='16' title='Private' alt='Private' />";
            echo "</td>";
        }
        if($mode == 'incident')
        {
            if($enddate == '0') echo "<td><a href='view_task.php?id={$task->id}&amp;mode=incident&amp;incident={$id}' class='info'>{$task->id}</a></td>";
            else echo "<td>{$task->id}</td>";
        }
        else
        {
            echo "<td>";
            echo "{$task->id}";
            echo "</td>";
            echo "<td>";
            echo "<a href='view_task.php?id={$task->id}' class='info'>".stripslashes($task->name);
            echo "</a>";

            echo "</td>";
            echo "<td>".priority_icon($task->priority).priority_name($task->priority)."</td>";
            echo "<td>".percent_bar($task->completion)."</td>";
        }

        if($mode != 'incident')
        {
            echo "<td";
            if ($startdate > 0 AND $startdate <= $now AND $task->completion <= 0) echo " class='urgent'";
            elseif ($startdate > 0 AND $startdate <= $now AND $task->completion >= 1 AND $task->completion < 100) echo " class='idle'";
            echo ">";
            if ($startdate > 0) echo date($CONFIG['dateformat_date'],$startdate);
            echo "</td>";
            echo "<td";
            if ($duedate > 0 AND $duedate <= $now AND $task->completion < 100) echo " class='urgent'";
            echo ">";
            if ($duedate > 0) echo date($CONFIG['dateformat_date'],$duedate);
            echo "</td>";
        }
        else
        {
            echo "<td>".format_date_friendly($startdate)."</td>";
            if($enddate == '0')
            {
                echo "<td><script type='text/javascript'>";
                echo "var act = new Activity();";
                echo "act.id = {$task->id};";
                echo "act.start = {$startdate}; ";
                echo "addActivity(act);";
                echo "</script>";

                echo "$strNotCompleted</td>";
                $duration = $now - $startdate;
                //echo "<td id='duration{$task->id}'><em><div id='duration{$task->id}'>".format_seconds($duration)."</div></em></td>";
                echo "<td id='duration{$task->id}'>".format_seconds($duration);
                //echo "<script type='text/javascript'>countUp();</script></td>";  //force a quick udate

            }
            else
            {
                $duration = $enddate - $startdate;
                echo "<td>".format_date_friendly($enddate)."</td>";
                echo "<td>".format_seconds($duration)."</td>";
                $closedduration += $duration;
            }
            $totalduration += $duration;

            echo "<td>".format_date_friendly($lastupdated)."</td>";
        }

        if ($show=='completed')
        {
            echo "<td>";
            if ($enddate > 0) echo date($CONFIG['dateformat_date'],$enddate);
            echo "</td>";
        }
        if($mode == 'incident')
        {
            echo "<td>".user_realname($task->owner)."</td>";
        }
        echo "</tr>\n";
        if ($shade=='shade1') $shade='shade2';
        else $shade='shade1';
    }

    if($mode == 'incident')
    {
        echo "<script type='text/javascript'>";
        echo "setClosedDuration({$closedduration});";
        echo "</script>";

        echo "<tr class='{$shade}'><td><strong>{$strTotal}:</strong></td><td colspan='5'>".format_seconds($totalduration)."</td></tr>";
        echo "<tr class='{$shade}'><td><strong>{$strExact}:</strong></td><td colspan='5' id='totalduration'>".exact_seconds($totalduration)."</td></tr>";
    }
    echo "<script type='text/javascript'>countUp();</script></td>";  //force a quick udate
    echo "</table>\n";
}
else
{
    echo "<p align='center'>";
    if ($sit[2]==$user) echo "No tasks";
    else echo "No public tasks";
    echo "</p>";
}
if($mode == 'incident') echo "<p align='center'><a href='add_task.php?incident={$id}'>{$strStartNewActivity}</a></p>";
else echo "<p align='center'><a href='add_task.php'>{$strAddTask}</a></p>";
?>