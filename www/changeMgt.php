<?
include "header.php";
?>
<div id="centercontent" class="main">
<script src="advantis-common.js"></script>

<?
if (!verify()) {
    exit;

} else {
?>
<script language="Javascript">

//ChangeLoginLink();

</script>
<?

    $userId = $_SESSION['user'];
    if (!check_rights("1")) {
        print_lack_of_privledge_warning();
    }
}
# We need to take the category rights that this user have. 
$categoryForUser = get_category_function_for_user($userId);
$isAdmin = check_rights("2");

?>
<h2 style="padding-bottom:20px";>Step 1 of 2: Creating Changes</h2>
<fieldset>
<legend>Change Selection</legend>
<p>Changing the Patrol Agent configuration is a 2-step process:
<LI>Step 1: Create one or multiple configuration changes and save them in the queue.
<LI>Step 2: Review and Apply the changes created in step 1 to one or multiple servers.</LI></p>
Please select the type of change you would like to create by clicking on the title:
<form action="handleChanges.php"  method="post" name="form">
<?
if (($isAdmin) || (preg_grep ("/1/", $categoryForUser))) {
?>
    <h3><a href = "javascript:change_submit(1)">Threshold</a></h3>
    <i>Change Patrol threshold settings</i>
<?
}
if (($isAdmin) || (preg_grep ("/2/", $categoryForUser))) {
?>
    <h3><a href = "javascript:change_submit(2)">Message Wording</a></h3>
    <i>Change the wording for notification messages</i>
<?
}
if (($isAdmin) || (preg_grep ("/3/", $categoryForUser))) {
?>

    <h3><a href = "javascript:change_submit(3)">Polling Interval</a></h3>
    <i>Change the frequency for monitoring metrics</i>
<?
}
?>

<?
if (($isAdmin) || (preg_grep ("/4/", $categoryForUser))) {
?>

    <h3><a href = "javascript:change_submit(4)">Blackout</a></h3>
    <i>Change or create notification blackout periods </i>
  
<?
}
?>
<?
if (($isAdmin) || (preg_grep ("/5/", $categoryForUser))) {
?>

    <h3><a href = "javascript:change_submit(5)">Notification Targets</a></h3>
    <i>Change or create the e-mail recipient(s) for a notification</i>
  
<?
}
?>
<?
if (($isAdmin) || (preg_grep ("/6/", $categoryForUser))) {
?>

    <h3><a href = "javascript:change_submit(6)">Device Ping</a></h3>
    <i>Update the list of devices for remote monitoring using Ping</i>
  
<?
}
?>
<?
if (($isAdmin) || (preg_grep ("/7/", $categoryForUser))) {
?>

    <h3><a href = "javascript:change_submit(7)">Process Monitoring</a></h3>
    <i>Update the process count for a monitored process, or add a new process to the monitoring list</i>
<?
}
?>

<!-- Feature under construction
    <h3><a href = "javascript:change_submit(4)">Generic Change</a></h3>
    <i>Submit a request to add/change/remove a generic Patrol Rule</i>
-->

<?
//}
?>
<BR/>
<BR/>
<BR/>
<?
# Setting a session token
$_SESSION["token"] = rand (0, 10000);

?>
<input type = "hidden" name = "changeType">
</form>
</fieldset>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
