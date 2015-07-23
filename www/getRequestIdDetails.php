<?

/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Get Request Details
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

session_start();
$requestId =  $_GET["requestId"];
?>
<html>
<head>
<title>Change Request Details (request #<? echo "$requestId"; ?>)
</title>
<?
include "config/config.php";
include "advantis-functions.php";
include "config/jefferson.css";
?>
</head>
<body>
<form action="<? print $PHP_SELF; ?>" method="post">

<body class="report">
<?

show_request_detail($requestId, $showAffectedHosts);


?>
</form>

<!--
/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/
-->
