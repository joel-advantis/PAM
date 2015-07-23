<?
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Build and display configuration reports
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

session_start();
include "config/config.php";
include "config/jefferson.css";
include "advantis-functions.php";
?>
<html>
<head><title>Advantis Management Solutions - PATROL Configuration Report</title>
</head>
<body class="report">
<form action="<? print $PHP_SELF; ?>" method="post">
<script src="advantis-common.js"></script>

<?
#<fieldset>

$timeStart = time();

# We need to determine what checkbox was checked

# Only get the host list for agent report and not pcm

if ($reportType < 10) {

    $hostToSubmit = $_POST['hostsToReport'];
    $hostToSubmit = str_replace(" ", "___", $hostToSubmit);
    $totalAgentsToProcess = explode ("#", $hostToSubmit);
    $filterList = $_POST['filterList'];
    $filterList = trim ($filterList);
    $dataPointTime = trim ($dataPointTime);
}
else {
    $totalAgentsToProcess = "";
}

if (($reportType == "12") && ($removeRogue != "")) {
    remove_from_rogue($removeRogue);
    echo "Agent Removed from Rogue List <br>";
}


$myReport = $_GET["myReport"];
if ($myReport) {        
    $reportType = $_GET["reportType"];
    $totalAgentsToProcess = get_all_hosts_for_user($_SESSION['user']);

}


run_report($reportType, $totalAgentsToProcess, $filterList,$dataPointTime,$filterAppClassSelect);
$timeEnd = time();
$timeToComplete = $timeEnd - $timeStart;
#</fieldset>
?>
<BR><BR>
<p class="silver">Report completed in <?php print howmany ($timeToComplete, "second.","seconds."); ?></p>
</body>
</html>
<INPUT TYPE="hidden" NAME=removeRogue>
<INPUT TYPE="hidden" NAME=reportType VALUE="<?php print ($reportType) ?>">
</form>
