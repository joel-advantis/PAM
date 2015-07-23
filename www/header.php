<?
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Header (banner and navigation pane)
 *
 *  Copyright © 2015 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

include "session.php";
set_time_limit( 120 );
// Print HTML header
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>

<?
// Load style sheets
include "config/jefferson.css";

//  Official version definition
$pamVersion = "v1.0.9";


// Register Globals
global $USE_LDAP;

?>
<title>Advantis Management Solutions - Patrol Agent Manager - <? print "$pamVersion"; ?></title>
</head>
<body>

<? /* Print title and version with link to release notes */ ?>
<div class="banner">
    <div style="float:left;">
        <a href="http://www.advantisms.com/">
        <img src="images/sm_adv_logo.png" alt="Advantis Management Solutions Logo" width="225" height="70" border="0"/>
        </a>
    </div>
    <div style="float:left;width:60%;border:0px solid black;text-align:center;">
        <h1><b style="font-size:80%;vertical-align:sub;">Patrol Agent Manager -
            <a href="ReleaseNotes.txt" target="_blank">
            <? print "$pamVersion"; ?></b>
            </a>

        </h1>
        <p class="silver">Copyright &copy; 2015 Advantis Management Solutions, Inc.
        </p>
    </div>
</div>
<table class="bannerblue">
    <tr class="bannerblue">
        <td class="bannerblue"></td>
    </tr>
</table>
<table class="bannersilver">
    <tr class="bannersilver">
        <td class="bannersilver"></td>
    </tr>
</table>
<table class="bannerwhite">
    <tr class="bannerwhite">
        <td class="bannerwhite"></td>
    </tr>
</table>


<div class="bannerlogout">
<?
    // Print logout banner
    if (!isset($_SESSION['user'])) {
        ?>
        <a id="loginbanner" href="login.php">login</a>
        <?
    }
    else {

        $userName = $_SESSION['username'];
        ?>
        <a id="loginbanner" href="logout.php">logout <? print "$userName"; ?></a>
        <?
    }
?>
</div>

<? /* Navigation pane down the left side of the page */ ?>
<div id="leftcontent">

<a href="index.php"         >Home</a><br/>


</p>
                            <h1>Changes</h1><p class="it">
<a href="changeMgt.php"     >Create Changes</a><br/>
<a href="processChanges.php">Apply Changes</a><br/>
 

                            <h1>Reports</h1>

<p class="it">
<a href="changeAudit.php">Change Report - By user</a><br/>
<a href="viewRequest.php">Change Report - By Request</a><br/>
<!-- <a href="configReport.php"  >System Settings Reports</a><br/> -->
<a href="hostSelection.php?reportType=1"  >Parameter Report</a><br/>
<! -- EDS Changes -->

<?
    // Print logout banner
    if (isset($_SESSION['user'])) {
        ?>
        <a href="generateConfigReport.php?reportType=1&myReport=1" target="_blank">My Report</a><br/>
        <?
    }
    else {
        ?>
        <br>
        <?
    }
?>
<a href="paramDescQuery.php">Parameter Definitions</a><br/>
<?
 // Only display these for admins...otherwise, disable the links

if (check_rights("2")) {
?>
 <a href="approve.php"   >Approve/Reject Requests</a><br/> 
<!--    <a href="schedule.php"  >Schedule Changes</a><br/> -->
    <a href="pcmReports.php"    >Patrol Auditing Reports</a><br/>
    <br>
    <br>
<?
}
else {
?>
<br>
<br>
<br>
<?
}

 // Only display these for admins...otherwise, disable the links

?>
</p>
                            <h1>Other</h1><p class="it">

<?php
    if ( ($USE_LDAP == 0) or ($_SESSION['username'] == "admin") ) {
        ?>
        <a href="changePassword.php">Change Password</a><br/>
        <?php
    } else {
        ?>
        <br/>
        <?php
    }

// Only display these for admins...otherwise, disable the links
if (check_rights("2")) {
    ?>

    <a href="addUser.php"   >Add/Modify Users</a><br/>
    <a href="addGroup.php"  >Add/Modify Groups</a><br/>
    <a href="addCategory.php">Add/Modify Categories</a><br/>
    <!-- <a href="queryDb.php"   >Query Database</a><br/> -->
    <br/>
    <a href="paramUpdateTable.php">Update Parameter Definition</a><br/>
    <!-- <a href="updateDbPass.php">Change DB password</a><br/> -->
    <br>
    <br>
    <?
}
else {
    ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

    <?
}
/*
// Don't even show this option to non-admins
if (check_rights("2")) {
    ?>
    <?
} else {
    ?>
        <!--<a href="login.php">login</a>--><br/>
    <?
}
*/
?>
</p>
<br/>
<br/>
<br/>


<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
</div>

