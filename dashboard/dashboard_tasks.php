<?php

function dashboard_tasks()
{
    global $sit;
    $user = $sit[2];
    echo "<span id='dragList1' >";
    echo "<div class='windowbox' style='width: 95%;'>";
    echo "<div class='windowtitle'>".user_realname($user)."'s Tasks:</div>";
    echo "<div class='window'>";
    echo "<br />";


    $sql = "SELECT * FROM tasks WHERE owner='$user' AND (completion < 100 OR completion='' OR completion IS NULL) ";
    if (!empty($sort))
    {
        if ($sort=='id') $sql .= "ORDER BY id ";
        elseif ($sort=='name') $sql .= "ORDER BY name ";
        elseif ($sort=='priority') $sql .= "ORDER BY priority ";
        elseif ($sort=='completion') $sql .= "ORDER BY completion ";
        elseif ($sort=='startdate') $sql .= "ORDER BY startdate ";
        elseif ($sort=='duedate') $sql .= "ORDER BY duedate ";
        if ($order=='a' OR $order=='ASC' OR $order='') $sql .= "ASC";
        else $sql .= "DESC";
    }
    else $sql .= "ORDER BY duedate ASC, startdate DESC, priority DESC";

    $result = mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);



    if (mysql_num_rows($result) >=1 )
    {
        echo "<table align='center'>";
        echo "<tr>";
        echo colheader('id', 'ID', $sort, $order);
        echo colheader('name', 'Task', $sort, $order);
        echo colheader('priority', 'Priority', $sort, $order);
        echo colheader('completion', 'Completion', $sort, $order);
        echo colheader('startdate', 'Start Date', $sort, $order);
        echo colheader('duedate', 'Due Date', $sort, $order);
        echo "</tr>\n";
        $shade='shade1';
        while ($task = mysql_fetch_object($result))
        {
            $duedate = mysql2date($task->duedate);
            $startdate = mysql2date($task->startdate);
            echo "<tr class='$shade'>";
            echo "<td>{$task->id}</td>";
            echo "<td><a href='edit_task.php?id={$task->id}' class='info'>{$task->name}";
            if (!empty($task->description)) echo "<span>".nl2br($task->description)."</span>";
            echo "</a></td>";
            echo "<td>".priority_icon($task->priority).priority_name($task->priority)."</td>";
            echo "<td>".percent_bar($task->completion)."</td>";
            echo "<td";
            if ($startdate > 0 AND $startdate <= $now) echo " class='notice'";
            echo ">";
            if ($startdate > 0) echo date($CONFIG['dateformat_date'],$startdate);
            echo "</td>";
            echo "<td";
            if ($duedate > 0 AND $duedate <= $now) echo " class='critical'";
            echo ">";
            if ($duedate > 0) echo date($CONFIG['dateformat_date'],$duedate);
            echo "</td>";

            echo "</tr>\n";
            if ($shade=='shade1') $shade='shade2';
            else $shade='shade1';
        }
        echo "</table>\n";
    }
    else
    {
        echo "<p align='center'>No tasks</p>";
    }

    echo "</div>";
    echo "</div>";
    echo "</span>";
}

?>