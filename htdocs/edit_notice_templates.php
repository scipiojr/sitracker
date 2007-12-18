<?php
// edit_notice_templates.php - Form for editing notice templates
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2007 Salford Software Ltd. and Contributors
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

@include ('set_include_path.inc.php');
//FIXME
$permission=0;

require ('db_connect.inc.php');
require ('functions.inc.php');

// This page requires authentication
require ('auth.inc.php');

// FIXME i18n Whole page

// External variables
$id = cleanvar($_REQUEST['id']);
$action = $_REQUEST['action'];

if (empty($action) OR $action == "showform")
{
    // Show select notice type form
    include ('htmlheader.inc.php');
    ?>
    <script type="text/javascript">
    function confirm_submit()
    {
        return window.confirm('Are you sure you want to edit this notice template?');
    }
    </script>
    <h2>Select Notice Template</h2>
    <p align='center'><a href="add_notice_templates.php?action=showform">Add Notice Template</a></p>
    <?php

    echo "<div style='margin-left: auto; margin-right: auto; width: 70%;'>";
    $sql = "SELECT * FROM noticetemplates ORDER BY name,id";
    $result = mysql_query($sql);
    if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
    while ($notice = mysql_fetch_object($result))
    {
        echo "<dl>\n";
        echo "<dt>";
        if ($notice->type=='system') echo "<em>";
        echo "<a href='{$_SERVER['PHP_SELF']}?id={$notice->id}&amp;action=edit'>{$notice->name}</a> ";
        echo "<dd>{$notice->description}</dd>\n";
        echo "</dl>\n";
    }
    echo "</div>";
    include ('htmlfooter.inc.php');
}
elseif ($action == "edit")
{
    include ('htmlheader.inc.php');
    // Show edit notice type form
    if ($id > 0)
    {
        // extract notice type details
        $sql = "SELECT * FROM noticetemplates WHERE id='$id'";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);
        $noticetype = mysql_fetch_array($result);
        echo "<h2>{$strEdit} Notice Template</h2>";
        echo "<h5>".sprintf($strMandatoryMarked,"<sup class='red'>*</sup>")."</h5>";
        ?>
        <p align='center'>A list of special identifiers that can be used in these fields is given at the bottom of the page.</p>
        <form name='edittemplate' action="<?php echo $_SERVER['PHP_SELF'] ?>?action=update" method="post" onsubmit="return confirm_submit()">

        <table align='center' class='vertical'>
        <?php
        echo "<tr><th>{$strNoticeTemplate}: <sup class='red'>*</sup></th><td>";
        echo "<input maxlength='50' name='name' size='35' value='{$noticetype['name']}' ";
        // if ($noticetype['type']=='system') echo "readonly='readonly' ";
        echo "/>";
        echo "</td></tr>\n";
        echo "<tr><th>{$strDescription}: <sup class='red'>*</sup></th><td><input name='description' size='50' value=\"{$noticetype["description"]}\" /></td></tr>\n";
        echo "<tr><th>{$strType} <sup class='red'>*</sup></th><td><input name='type' size='50' value=\"{$noticetype["type"]}\" /></td></tr>\n";
        echo "<tr><th>{$strText} <sup class='red'>*</sup></th><td>";
        echo "<textarea>{$noticetype["text"]}</textarea></td></tr>\n";
        echo "<tr><th>{$strLinkText}</th><td><input maxlength='100' name='linktext' size='30' value=\"{$noticetype["linktext"]}\" /></td></tr>\n";
        echo "<tr><th>{$strLink}</th><td><input maxlength='100' name='link' size='30' value=\"{$noticetype["link"]}\" /></td></tr>\n";
        echo "</td></tr>";
        echo "</table>";

        echo "<p>";
        echo "<input name='type' type='hidden' value='{$noticetype['type']}' />";
        echo "<input name='id' type='hidden' value='{$id}' />";
        echo "<input name='submit' type='submit' value=\"{$strSave}\" />";
        echo "</p>\n";
        if ($noticetype['type']=='user') echo "<p align='center'><a href='{$_SERVER['PHP_SELF']}?action=delete&amp;id={$id}'>{$strDelete}</a></p>";
        // FIXME i18n email templates
        ?>
        <p align='center'>The following special identifiers can be used in these fields:</p>
        <table align='center' class='vertical'>
        <tr><th>&lt;contactemail&gt;</th><td>Email address of incident contact</td></tr>
        <tr><th>&lt;contactname&gt;</th><td>Full Name of incident contact</td></tr>
        <tr><th>&lt;contactfirstname&gt;</th><td>First Name of incident contact</td></tr>
        <tr><th>&lt;contactsite&gt;</th><td>Site name of incident contact</td></tr>
        <tr><th>&lt;contactphone&gt;</th><td>Phone number of incident contact</td></tr>
        <tr><th>&lt;contactnotify&gt;</th><td>The 'Notify Contact' email address (if set)</td></tr>
        <tr><th>&lt;contactnotify2&gt;</th><td>(or 3 or 4) The 'Notify Contact' email address (if set) of the notify contact recursively</td></tr>
        <tr><th>&lt;incidentid&gt;</th><td>ID number of incident</td></tr>
        <tr><th>&lt;incidentexternalid&gt;</th><td>External ID number of incident</td></tr>
        <tr><th>&lt;incidentexternalengineer&gt;</th><td>Name of External engineer dealing with incident</td></tr>
        <tr><th>&lt;incidentexternalengineerfirstname&gt;</th><td>Name of External engineer dealing with incident</td></tr>
        <tr><th>&lt;incidentexternalemail&gt;</th><td>Email address of External engineer dealing with incident</td></tr>
        <tr><th>&lt;incidentccemail&gt;</th><td>Extra email addresses to CC regarding incidents</td></tr>
        <tr><th>&lt;incidenttitle&gt;</th><td>Title of incident</td></tr>
        <tr><th>&lt;incidentpriority&gt;</th><td>Priority of incident</td></tr>
        <tr><th>&lt;incidentsoftware&gt;</th><td>Skill assigned to an incident</td></tr>
        <tr><th>&lt;incidentowner&gt;</th><td>The full name of the person who owns the incident</td></tr>
        <tr><th>&lt;incidentreassignemailaddress&gt;</th><td>The email address of the person a call has been reassigned to</td></tr>
        <tr><th>&lt;incidentfirstupdate&gt;</th><td>The first customer visible update in the incident log</td></tr>
        <tr><th>&lt;useremail&gt;</th><td>Email address of current user</td></tr>
        <tr><th>&lt;userrealname&gt;</th><td>Real name of current user</td></tr>
        <tr><th>&lt;signature&gt;</th><td>Signature of current user</td></tr>
        <tr><th>&lt;applicationname&gt;</th><td>Name of this application</td></tr>
        <tr><th>&lt;applicationshortname&gt;</th><td>Short name of this application</td></tr>
        <tr><th>&lt;applicationversion&gt;</th><td>Version number of this application</td></tr>
        <tr><th>&lt;supportemail&gt;</th><td>Technical Support email address</td></tr>
        <tr><th>&lt;supportmanageremail&gt;</th><td>Technical Support mangers email address</td></tr>
        <tr><th>&lt;globalsignature&gt;</th><td>Current Global Signature</td></tr>
        <tr><th>&lt;todaysdate&gt;</th><td>Current Date</td></tr>
        <tr><th>&lt;info1&gt;</th><td>Additional Info #1 (template dependent)</td></tr>
        <tr><th>&lt;info2&gt;</th><td>Additional Info #2 (template dependent)</td></tr>
        <?php
        echo "<tr><th>&lt;useremail&gt;</th><td>The current users email address</td></tr>";
        echo "<tr><th>&lt;userrealname&gt;</th><td>The full name of the current user</td></tr>";
        echo "<tr><th>&lt;salespersonemail&gt;</th><td>The email address of the salesperson attached to the contacts site</td></tr>";
        echo "<tr><th>&lt;applicationname&gt;</th><td>'{$CONFIG['application_name']}'</td></tr>";
        echo "<tr><th>&lt;applicationversion&gt;</th><td>'{$application_version_string}'</td></tr>";
        echo "<tr><th>&lt;applicationshortname&gt;</th><td>'{$CONFIG['application_shortname']}'</td></tr>";
        echo "<tr><th>&lt;supportemail&gt;</th><td>The support email address</td></tr>";
        echo "<tr><th>&lt;signature&gt;</th><td>The current users signature</td></tr>";
        echo "<tr><th>&lt;globalsignature&gt;</th><td>The global signature</td></tr>";
        echo "<tr><th>&lt;todaysdate&gt;</th><td>Todays date</td></tr>";

        plugin_do('emailtemplate_list');
        echo "</table>\n";
        echo "</form>";

        include ('htmlfooter.inc.php');
    }
    else
    {
        echo "<p class='error'>You must select an email template</p>\n";
    }
}
elseif ($action == "delete")
{
    if (empty($id) OR is_numeric($id)==FALSE)
    {
        // id must be filled and be a number
        header("Location: {$_SERVER['PHP_SELF']}?action=showform");
        exit;
    }
    // We only allow user templates to be deleted
    $sql = "DELETE FROM emailtype WHERE id='$id' AND type='user' LIMIT 1";
    mysql_query($sql);
    if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
    header("Location: {$_SERVER['PHP_SELF']}?action=showform");
    exit;
}
elseif ($action == "update")
{
    // Add new email type

    // External variables
    $name = cleanvar($_POST['name']);
    $description = cleanvar($_POST['description']);
    // We don't strip tags because that would also strip our special tags
    $tofield = mysql_real_escape_string($_POST['tofield']);
    $fromfield = mysql_real_escape_string($_POST['fromfield']);
    $replytofield = mysql_real_escape_string($_POST['replytofield']);
    $ccfield = mysql_real_escape_string($_POST['ccfield']);
    $bccfield = mysql_real_escape_string($_POST['bccfield']);
    $subjectfield = mysql_real_escape_string($_POST['subjectfield']);
    $bodytext = mysql_real_escape_string($_POST['bodytext']);
    $cust_vis = cleanvar($_POST['cust_vis']);
    $storeinlog = cleanvar($_POST['storeinlog']);
    $id = cleanvar($_POST['id']);
    $type = cleanvar($_POST['type']);

    // check form input
    $errors = 0;
    // check for blank name
    if ($name == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a name for the email type</p>\n";
    }
    // check for blank to field
    if ($tofield == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a 'To' field</p>\n";
    }
    // check for blank from field
    if ($fromfield == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a 'From' field</p>\n";
    }
    // check for blank reply to field
    if ($replytofield == "")
    {
        $errors = 1;
        echo "<p class='error'>You must enter a 'Reply To' field</p>\n";
    }
    // check for blank type
    if ($type == "")
    {
        $errors = 1;
        trigger_error("Invalid input, blank type",E_USER_ERROR);
    }
    if ($type == 'system' AND is_numeric($name))
    {
        $errors++;
        echo "<p class='error'>System email templates cannot have a name that consists soley of numbers</p>\n";
    }

    // User templates may not have _ (underscore) in their names, we replace with spaces
    // in contrast system templates must have _ (underscore) instead of spaces, so we do a replace
    // the other way around for those
    // We do this to help prevent user templates having names that clash with system templates
    if ($type == 'user') $name = str_replace('_', ' ', $name);
    else $name = str_replace(' ', '_', strtoupper(trim($name)));

    if ($cust_vis=='yes') $cust_vis='show';
    else $cust_vis='hide';

    if ($storeinlog=='Yes') $storeinlog='Yes';
    else $storeinlog='No';


    if ($errors == 0)
    {
        $sql  = "UPDATE emailtype SET name='$name', description='$description', tofield='$tofield', fromfield='$fromfield', ";
        $sql .= "replytofield='$replytofield', ccfield='$ccfield', bccfield='$bccfield', subjectfield='$subjectfield', ";
        $sql .= "body='$bodytext', customervisibility='$cust_vis', storeinlog='$storeinlog' ";
        $sql .= "WHERE id='$id' LIMIT 1";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error("MySQL Query Error ".mysql_error(), E_USER_ERROR);

        if (!$result) echo "<p class='error'>Update of Email Type Failed\n";
        else
        {
            journal(CFG_LOGGING_NORMAL, 'Email Template Updated', "Email Template $type was modified", CFG_JOURNAL_ADMIN, $type);
            html_redirect("edit_emailtype.php");
        }
    }
}
?>
