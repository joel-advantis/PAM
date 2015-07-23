<?php
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Approve and appply change request
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

session_start();
$requestId = $_GET["requestId"];
include "config/config.php";
include "config/jefferson.css";
include "advantis-functions.php";

?>
<html>
<head>
<title>Final Approval (request #<? echo "$requestId"; ?>)
</title>
<script src="advantis-common.js"></script>
<?
if (!verify()) {
    exit;
}
else {    // Make sure user is authorized for this page
        if (!check_rights("2")) {
            print_lack_of_privledge_warning();
    }
}

?>

</head>
<form action="<? print $PHP_SELF; ?>" method="post">
<body class="report">
<?
$userId = $_SESSION['user'];
# Setting a session token
$_SESSION["token"] = rand (0, 10000);
$token = $_SESSION["token"];

if (!check_rights($userId, '2')) {
    print_lack_of_privledge_warning();
}

show_request_detail($requestId, $showAffectedHosts);

?>
<fieldset>
<legend>Ruleset Selection</legend>
<?
echo "<p>Select the ruleset you would like to add these rules to - If  none of them are accurate, you can select a new ruleset and export the data to PCM </p>";

$totalRulesets = get_all_rulesets();
$totalRulesets = implode("\n", $totalRulesets);
$totalRulesets = str_replace(".cfg", "__cfg", $totalRulesets);
$totalRulesets = explode ("\n", $totalRulesets);
$allGroupServersList = implode (",", $totalRulesets);
$firstLevel = preg_grep ("/\./", $totalRulesets, PREG_GREP_INVERT);
?>

<TABLE class="menu">

<TR><TH class="menu">Rulesets</TH><TH class="menu"></TH></TR>
<TR><TD colspan = "4" class="menu" id="groupSelected"></TD><TD class="menu"></TD><TD class="menu"></TD></TR>

<TR><TD class="menu">
<?
echo "<SELECT class=\"groupswide\" name = \"list3\" size = \"5\" ONCLICK=\"populate_ruleset()\" onDblClick=\"navigate_next_level('$allGroupServersList');\">";

foreach ($firstLevel as $value) {

    $valueTmp = str_replace("___", "\M", $value);
    $valueArray = explode ("\M", $valueTmp);
    $serverGroup  = $valueArray[0];
    echo "<option value =\"$serverGroup\">$serverGroup";
}
?>
</SELECT>
</TD></TR>

<TR><TD class="menu" style="padding-top:20px;">
</TD></TR>
</TABLE>
</fieldset>
<br>
<br/>
<fieldset>
<legend>RuleSet To Update</legend>
<BR>
<TABLE class = "pconfig">
<TR></TD><TD><input class="input-box"  STYLE="width=50em" name="enteredRuleset"></TD></TR>

</TABLE>
<BR>
<p>If the ruleset you enter does not exist, it will be created under the selected folder. <br>
For example, to create a new ruleset under a folder called TEST, the format is TEST.newruleset.cfg</p>
</fieldset>
<br>
<br>
<input type = "hidden" name = "previousSelection">
<fieldset>
<legend>Decision</legend>

<p> Enter Comments <p>
<textarea name=Approvercomment rows="5"></textarea>
<BR>
<input class="submit-button" name = "finalApproval" type="button" onClick="submit_to_pcm();" value="Approve and Update PCM">
<input class="submit-button" type="button" onClick="rejectRules();" value="Reject Selected request">
</fieldset> <? /* End of Decision */ ?>
<?
echo "<input type=\"hidden\" name=\"formToken\" value=\"$token\">";
echo "<input type=\"hidden\" name=\"requestId\" value=\"$requestId\">";
?>

</form>
<form name="approve" action="approve.php" method="post">

<form>
<?
echo "<input type=\"hidden\" name=\"formToken\" value=\"$token\">";
?>
<input type="hidden" name="requestToReject">
<input type="hidden" name="requestToApprove">
<input type="hidden" name="Approvercomment">
<input type="hidden" name="rulesetName">
<?
?>
<br>
<br>
</form>
<?

/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/

