<?php
// This Page Is Valid XHTML 1.0 Transitional! 27Oct05
echo "\n</div>"; // mainframe
echo "\n<div id='statusbar'>";
if ($_SESSION['auth'] == TRUE) echo "<a href='about.php'>";
echo "<img src='{$CONFIG['application_webpath']}images/sitting_man_logo16x16.png' width='16' height='16' border='0' alt='About {$CONFIG['application_shortname']}' />";
if ($_SESSION['auth'] == TRUE) echo "</a>";
echo " <strong><a href='http://sitracker.sourceforge.net/'>Support Incident Tracker</a>";
if ($_SESSION['auth'] == TRUE) echo " {$application_version_string}";
echo "</strong>";
if ($_SESSION['auth'] == TRUE)
{
    echo " running ";
    if ($CONFIG['demo']) echo "in DEMO mode ";
    echo "on ".strip_tags($_SERVER["SERVER_SOFTWARE"]);
    echo " at ".ldate('H:i',$now);
}
echo "</div>\n";
if (!empty($application_revision) AND (substr($application_revision, 0, 4)=='beta')
                                   OR (substr($application_revision, 0, 5)=='alpha'))
{
    echo "<p class='warning'>You are using a pre-release version of SiT (v{$application_version} {$application_revision}). <br />";
    echo "Pre-release versions are for you to test, provide feedback and to help with further development and should never be used in a live production environment. ";
    echo "<a href=\"{$CONFIG['bugtracker_url']}\">{$strReportBug}</a></p>";
}

if ($CONFIG['debug'] == TRUE)
{
    echo "\n<div id='tail'><strong>DEBUG</strong><br />";
    $exec_time_end = getmicrotime();
    $exec_time = $exec_time_end - $exec_time_start;
    echo "<p>CPU Time: ".number_format($exec_time,3)." seconds</p>";
    if (isset($dbg)) echo "<hr /><pre>".print_r($dbg,true)."</pre>";
    echo "</div>";
}
echo "\n</body>\n</html>\n";
?>