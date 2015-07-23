<?php
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Display parameter description in popup window
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

session_start();
include "config/config.php";
include "config/jefferson.css";
include "advantis-functions.php";
$paramName =  $_GET["paramName"];
$appClass  =  $_GET["appClass"];
?>

<html>
<head>
<title>
    <?php print "$paramName Description"; ?>
</title>
<script>
function changeScreenSize(w,h) {
    window.resizeTo( w,h )
}
</script>

</head>
<body class="report" onload="changeScreenSize(700,500)">
<h2 style="padding-bottom:20px";>
    <?php print "$paramName  ($appClass)"; ?>
</h2>

<fieldset class="inset">
<legend>Parameter Description</legend>


<?php

# We need to determine what checkbox was checked

# We need to get the category first
$result = get_param_desc_for_appClass($appClass, $paramName);

if (count ($result)) {
    $result = $result[0];
    $result = str_replace("___", "\M", $result);
    $resultArray = explode ("\M", $result);
    $paramDescription = $resultArray[0];
    if ($paramDescription == "") { $paramDescription = "Not Found";}
    $collector  = $resultArray[1];
    if ($collector == "") { $collector = "Not Found";}
    ?>
    <TABLE BORDER>
        <TR>
            <TH>Parameter Name</TH>
            <TH>Description</TH>
            <TH>Collector</TH>
        </TR>
        <TR>
            <TD><?php print $paramName; ?></TD>
            <TD><?php print $paramDescription; ?></TD>
            <TD><?php print $collector; ?></TD>
        </TR>
    </TABLE>
    <?php
}
else {
    echo "No description available for $paramName";
}
?>
</fieldset>
</div>

<!--
/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/
-->
