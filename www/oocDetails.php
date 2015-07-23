<?

/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Compliance Report Details
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

session_start();
include "config/config.php";
include "advantis-functions.php";
include "config/jefferson.css";
$agentId =  $_GET["agentId"];
$hostName = $_GET["hostName"];
$portNumber = $_GET["portNumber"];

?>
<html>
<head>
<title>Out of Compliance Details for <?php print $hostName; ?></title>

<script>
function changeScreenSize(w,h) {
    window.resizeTo( w,h )
}
</script>

</head>

<body onload="changeScreenSize(1000,1000)" class="report">
<div align="center">

<H2>Out of Compliance Details for <?php print "$hostName on port $portNumber"; ?></H2>


<?

# We need to determine what checkbox was checked

$result = ooc_details($agentId);
?>
<fieldset><legend>Mismatched rules</legend>
<TABLE class="report">
<TR><TH>VARIABLE</TH><TH>RULE VALUE</TH><TH>ACTUAL VALUE</TH></TD></TR>
<?
foreach ($result as $value) {
    $resultArray = explode ("___", $value);
    echo "<TR><TD><font size = \"-3\">$resultArray[2]</TD><TD><font size = \"-3\">$resultArray[3]</TD><TD><font size = \"-3\">$resultArray[4]</TD></TR>";
}
?>
</TABLE></fieldset>
</body>
</html>

<!--
/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/
-->
