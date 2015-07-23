<?
include "header.php";
?>
<div id="centercontent" class="main">
    <script src="advantis-common.js"></script>
    <?

    if (!verify()) {
        exit;
    } else {
        $userId = $_SESSION['user'];
        if (!check_rights("1")) {
            print_lack_of_privledge_warning();
        }
    }

    $sessionToken = $_SESSION["token"];

    ?>
    <form name="handleChange" action="<? print $PHP_SELF; ?>" method="post">
    <INPUT TYPE="hidden" NAME=changeType  VALUE=<?php print "\"$changeType\">";

    debug_var("Change type",$changeType);
    if ($changeType == "1") {
        $changeTypeLabel = "Threshold";
    ?>
        <h2>Creating Changes for Threshold - (Step 1 of 2)</h2>
        <?
    }
    elseif ($changeType == "2") {
        $changeTypeLabel = "Message Wording";
    ?>
        <h2>Creating Changes for Message Wording - (Step 1 of 2)</h2>
    <?
    }
    elseif ($changeType == "3") {
        $changeTypeLabel = "Polling Interval";
    ?>
        <h2>Creating Changes for Polling Interval - (Step 1 of 2)</h2>
        <?
    }

    elseif ($changeType == "4") {
        $changeTypeLabel = "Blackout";
    ?>
        <h2>Creating Changes for Blackout - (Step 1 of 2)</h2>
    <?
    }
    elseif ($changeType == "5") {
        $changeTypeLabel = "Notification";
    ?>
        <h2>Creating Changes for Notification Target - (Step 1 of 2)</h2>
    <?
    }
    elseif ($changeType == "6") {
        $changeTypeLabel = "Ping";
    ?>
        <h2>Creating Changes for Device Ping - (Step 1 of 2)</h2>
    <?
    }
    elseif ($changeType == "7") {
        $changeTypeLabel = "Process Count";
    ?>
        <h2>Creating Changes for Process Monitoring - (Step 1 of 2)</h2>
    <?
    }

    ?>
    <fieldset>
    <legend><? print ($changeTypeLabel) ?> Changes in Queue</legend>
    <?
    if (($rulesToDelete != "") && ($sessionToken == $formToken)) {
        $rulesToDelete = trim ($rulesToDelete);
        delete_rules($rulesToDelete);
        $rulesToDelete = "";
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
    }

    elseif (($changeType == "1") && ($sessionToken == $formToken) && ($newRule == "1")) {

        if ($appClass == "") {
            echo "Something went wrong, please contact Advantis support<br>";
            exit;
        }

        $operation = "REPLACE";

        if ($alarm1range == "1")    { $alarm1Max = $alarm1Min;}
        if ($list1 == "")    { $list1 = 0;}       if ($alarm1Status == "") { $alarm1Status = 0;}
        if ($alarm1range == "")  { $alarm1range = 0;} if ($alarm1Min == "")    { $alarm1Min = 0;}
        if ($alarm1Max == "")    { $alarm1Max = 0;}   if ($alarm1Cycle == "")  { $alarm1Cycle = 0;}
        if (($alarm1Cycle == "0") || ($alarm1Cycle == "1")) {
            $alarm1Action = "0";
            $alarm1Cycle  = 0;
        }
        else {
            $alarm1Action = "1";
        }
        $alarm1Final = "$list1 $alarm1Min $alarm1Max $alarm1Action $alarm1Cycle $alarm1Status";

        if ($alarm2range == "1")   { $alarm2Max = $alarm2Min;}
        if ($list2 == "")    { $list2 = 0;}       if ($alarm2Status == "") { $alarm2Status = 0;}
        if ($alarm2range == "")  { $alarm2range = 0;} if ($alarm2Min == "")    { $alarm2Min = 0;}
        if ($alarm2Max == "")    { $alarm2Max = 0;}   if ($alarm2Cycle == "")  { $alarm2Cycle = 0;}
        if (($alarm2Cycle == "0") || ($alarm2Cycle == "1")) {
            $alarm2Action = "0";
            $alarm2Cycle  = 0;
        }
        else {
            $alarm2Action = "1";

        }
        $alarm2Final = "$list2 $alarm2Min $alarm2Max $alarm2Action $alarm2Cycle $alarm2Status";

        if ($alarm3range == "1")   { $alarm3Max = $alarm3Min;}
        if ($list3 == "")    { $list3 = 0;}       if ($alarm3Status == "") { $alarm3Status = 0;}
        if ($alarm3range == "")  { $alarm3range = 0;} if ($alarm3Min == "")    { $alarm3Min = 0;}
        if ($alarm3Max == "")    { $alarm3Max = 0;}   if ($alarm3Cycle == "")  { $alarm3Cycle = 0;}
        if (($alarm3Cycle == "0") || ($alarm3Cycle == "1")) {
            $alarm3Action = "0";
            $alarm3Cycle  = 0;
        }
        else {
            $alarm3Action = "1";
        }
        $borderFinal = "$list3 $alarm3Min $alarm3Max $alarm3Action $alarm3Cycle $alarm3Status";
        if ($paramEnable == "") {
            $paramEnable = 0;
        }
        $value = "$paramEnable,$borderFinal,$alarm1Final,$alarm2Final";

        $instance = $_POST['instance'];

        if (!is_array($instance)) {
            $instance[] = "";
        }
        $instance = array_unique ($instance);
        foreach ($instance as $indInstance) {

            if ($indInstance == "") {
                $indInstance = "__ANYINST__";
            }
            $variable = "/AS/EVENTSPRING/PARAM_SETTINGS/THRESHOLDS/" . $appClass . "/$indInstance/" . "$param";

            new_change_request($userId, $variable, $operation, $value, $changeType);
        }
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }
    elseif (($changeType == "2") && ($sessionToken == $formToken) && ($newRule == "1")) {

        # Message wording change request
        if ($appClass == "") {
            echo "Something went wrong, please contact Advantis support<br>";
            exit;
        }
        $value = $message;
        $operation = "REPLACE";

        $instance = $_POST['instance'];

        if (!is_array($instance)) {
            $instance[] = "";
        }
        $instance = array_unique ($instance);
        foreach ($instance as $indInstance) {
            if ($indInstance == "") {
                $indInstance = "__ANYINST__";
            }
            $variable = "/AS/EVENTSPRING/ALERT/MSG/" . $appClass . "/$indInstance/" . "$param/msgText" . "$status";
            new_change_request($userId, $variable, $operation, $value, $changeType);
        }

        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
    }
    elseif (($changeType == "3") && ($sessionToken == $formToken) && ($newRule == "1")) {

        # PollTime change request
        if ($appClass == "") {
            echo "Something went wrong, please contact Advantis support<br>";
            exit;
        }

        $instance = $_POST['instance'];

        if (!is_array($instance)) {
            $instance[] = "";
        }
        $instance = array_unique ($instance);
        foreach ($instance as $indInstance) {
            if ($indInstance == "") {
                $indInstance = "__ANYINST__";
            }

            if ($paramEnable) {
                # The parameter is enable
                $variable = "/AS/EVENTSPRING/PARAM_SETTINGS/POLLTIMES/" . $appClass . "/$indInstance/" . "$collParam/interval";
                $value = $pollTime;
                $switchType = $changeType;
            }
            else {
                $variable = "/AS/EVENTSPRING/PARAM_SETTINGS/THRESHOLDS/" . $appClass . "/$indInstance/" . "$collParam";
                $value = "0,0 0 0 0 0 0,0 0 0 0 0 0,0 0 0 0 0 0";
                $switchType = "1";
            }

            $operation = "REPLACE";

            new_change_request($userId, $variable, $operation, $value, $switchType);
        }

        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
    }

    elseif (($changeType == "4") && ($sessionToken == $formToken) && ($newRule == "1")) {

        if ($appClass == "") {
            $appClass = "ALLAPPS";
        }

        if ($param == "") {
            $param = "ALLPARAM";
        }

        # Blackout Change Request
        if ($appClass == "ALLAPPS") {
            # Selection is made at the category level
            $param = "ALLPARAM";
        }

        $operation = "$action";

        $value = "";

        # Get the start and end time from the form, then convert
        # to seconds of the week starting from midnight Sunday

        $starttime = trim($starttime);
        $startTimeArray = explode (":", $starttime);

        $startHour = $startTimeArray[0];
        $startMin  = $startTimeArray[1];

        $starttime = trim($starttime);
        $startTimeArray = explode (":", $starttime);

        $startHour = $startTimeArray[0];
        $startMin  = $startTimeArray[1];


        $endtime = trim($endtime);
        $endTimeArray = explode (":", $endtime);

        $endHour = $endTimeArray[0];
        $endMin  = $endTimeArray[1];

        $endtime = trim($endtime);
        $endTimeArray = explode (":", $endtime);

        $endHour = $endTimeArray[0];
        $endMin  = $endTimeArray[1];

        # Validate the start and end times
        if (! (preg_match ("/([0-2])?[0-9]/",$startHour)) && (preg_match ("/([0-2])?[0-9]/",$startMin))) {
            echo "Invalid start time for blackout<br/>";
            exit;
        }

        if (! (preg_match ("/([0-2])?[0-9]/",$endHour)) && (preg_match ("/([0-2])?[0-9]/",$endMin))) {
            echo "Invalid end time for blackout<br/>";
            exit;
        }

        echo "Start offset $startday end offset $endday<br/>";

        # Verify that start and end days were selected
        if (($operation != "DELETE_ALL") &&(($startday == "") || ($endday == ""))) {
            echo "Invalid day selection(s) <br/>";
            exit;
        }

        #$startTimeSeconds = $startHour*60*60 + $startMin*60;
        #$endTimeSeconds = $endHour*60*60 + $endMin*60;

        $starttimeseconds = $startday + $startHour*60*60 + $startMin*60;
        $endtimeseconds = $endday + $endHour*60*60 + $endMin*60 + 59;

        # If the start time is before the end time, then we do all the days between the start and end
        # otherwise, do the days that are not between the start and end

        if ($starttimeseconds < $endtimeseconds)  {
            $inside = 1;

            # If it's the same day, we can simplify the logic.

            if ($startday == $endday) {
                $sameDay = 1;
            } else {
                $sameDay = 0;
            }

        } else {
            $inside = 0;
            $sameDay = 0;
        }

        echo "Start time:$starttimeseconds :\n";

        #$starttimeseconds = trim ($starttimeseconds);
        #$endtimeseconds = trim ($endtimeseconds);


        #################################
        ### Define a function to handle converting the start and end time per day to a string
        ### input parameters:
        ### $current = ordinal day of the week to convert, 0-6
        ### $begin = start time in seconds since midnight Sunday
        ### $end = end time in seconds since midnight Sunday
        #
        #
        function blackout_string ($current,$begin,$end) {

            # if the start and end time are within the same day, then we use
            # the times as-is.  otherwise the start or end time needs to be adjusted
            # $single is true if begin and end times are within the same day
            # $simple is true if we use days between start and end or false if we use the outside days

            $beginDay = intval( $begin / 86400);
            $endDay   = intval( $end / 86400);
            $today    = $current;

            # Convert the current day to a string

            if      ($today == 1) { $dayString = "Mon"; }
            else if ($today == 2) { $dayString = "Tue"; }
            else if ($today == 3) { $dayString = "Wed"; }
            else if ($today == 4) { $dayString = "Thu"; }
            else if ($today == 5) { $dayString = "Fri"; }
            else if ($today == 6) { $dayString = "Sat"; }
            else if ($today == 0) { $dayString = "Sun"; }

            if (($beginDay == $endDay) && ($begin < $end)) {

                # This is the easy one - Begin and end times are on the same day

                $beginSeconds = $begin % 86400;
                $endSeconds =   $end % 86400;

            } else {

                if ($beginDay == $current) { $beginSeconds = $begin % 86400; }
                else                       { $beginSeconds = 0; }

                if ($endDay == $current)   { $endSeconds = $end % 86400; }
                else                       { $endSeconds = 86399; }

            }

            $blackoutString = "$dayString $beginSeconds $endSeconds,";
            return ($blackoutString);
        }
        #
        # End of function
        #################################

        ###
        # Loop through the days of the week and setup the blackout value using blackout_string()
        #

        $firstDay = intval( $startday / 86400);
        $lastDay  = intval( $endday / 86400);

        echo "First day: $firstDay Last Day: $lastDay time1 $starttimeseconds time2 $endtimeseconds Same Day? $sameDay<br/>";

        if ($sameDay) {

            $value = blackout_string ($firstDay,$starttimeseconds,$endtimeseconds);

        } else if ($inside) {

            for ($day=$firstDay; $day <= $lastDay; $day++) {
                $value = $value . blackout_string ($day,$starttimeseconds,$endtimeseconds);
            }

        } else {

            for ($day=0; $day <= $lastDay; $day++) {
                $value = $value . blackout_string ($day,$starttimeseconds,$endtimeseconds);
            }

            for ($day=$firstDay; $day <= 6; $day++) {
                $value = $value . blackout_string ($day,$starttimeseconds,$endtimeseconds);
            }

        }

        #
        # finished looping
        ###

        $value = trim ($value, ",");
        if ($operation == "DELETE_ALL") {
            $operation = "DELETE";
            $value = "";
        }

        $instance = $_POST['instance'];

        if (!is_array($instance)) {
            $instance[] = "";
        }
        $instance = array_unique ($instance);
        foreach ($instance as $indInstance) {
            if ($indInstance == "") {
                $indInstance = "__ANYINST__";
            }
            $variable = "/KM/PAM_PROCESS_CONFIG/BLACKOUT_RULE/$category/$appClass/$indInstance/$param";
            $value = ":$operation $value";
            new_change_request($userId, $variable, "APPEND", $value, $changeType);
        }

        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
    }

    elseif (($changeType == "5") && ($sessionToken == $formToken) && ($newRule == "1")) {

        if ($appClass == "") {
            $appClass = "ALLAPPS";
        }

        if ($param == "") {
            $param = "ALLPARAM";
        }

        # Notification target Change Request
        if ($appClass == "ALLAPPS") {
            # Selection is made at the category level
            $param = "ALLPARAM";
        }

        $operation = "$action";
        if ($operation == "DELETE_ALL") {
            $operation = "DELETE";
            $value = "";
        }
        $value = $notificationTarget;

        $instance = $_POST['instance'];

        if (!is_array($instance)) {
            $instance[] = "";
        }
        foreach ($instance as $indInstance) {
            if ($indInstance == "") {
                $indInstance = "__ANYINST__";
            }
            $value = ":$operation $value";
            if ($status != "BOTH") {
                $variable = "/KM/PAM_PROCESS_CONFIG/NOTIFICATION_RULE/$category/$appClass/$indInstance/$param/$status";
                new_change_request($userId, $variable, "APPEND", $value, $changeType);
            }
            else {
                $variable = "/KM/PAM_PROCESS_CONFIG/NOTIFICATION_RULE/$category/$appClass/$indInstance/$param/ALARM";
                new_change_request($userId, $variable, "APPEND", $value, $changeType);
                $variable = "/KM/PAM_PROCESS_CONFIG/NOTIFICATION_RULE/$category/$appClass/$indInstance/$param/WARNING";
                new_change_request($userId, $variable, "APPEND", $value, $changeType);

            }
        }


        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
    }
    elseif (($changeType == "6") && ($sessionToken == $formToken) && ($newRule == "1")) {
            
        $operation = "$action";
        $variable = "/DIRK/pingkm";
        $value = $pingHosts;
        $value = str_replace ("\n", ",",$value);
        $value = str_replace ("\r", ",",$value);
        $value = str_replace (",,", ",",$value);
        $value = trim ($value,",");    
        new_change_request($userId, $variable, $operation, $value, $changeType);


        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
        
    }

    elseif (($changeType == "7") && ($sessionToken == $formToken) && ($newRule == "1")) {
            
        $operation = "REPLACE";
        $processCheckbox = 1;
        $processCountPicked = $_POST['processCountPicked'];
        while ($processCheckbox <= $count) {
            $arrayNumber = $processCheckbox -1;
            $procName = "list" . $processCheckbox;
            if (${$procName}) {
                $variable = "/AS/EVENTSPRING/PARAM_SETTINGS/THRESHOLDS/NT_PROCESS_GROUP/" . ${$procName} ."/PROCActiveProcessCount";
                $value = "1,1 $processCountPicked[$arrayNumber] $processCountPicked[$arrayNumber] 0 0 2,0 0 0 0 0 0,0 0 0 0 0 0";
                new_change_request($userId, $variable, $operation, $value, $changeType);
            }
            $processCheckbox++;
        }


        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
        
    }
    elseif (($changeType == "999") && ($sessionToken == $formToken)) {

       # Generic changes
        new_change_request($userId, $ruleName, $operationSelected, $ruleValue, $changeType);
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }

    # Setting a session token
    $_SESSION["token"] = rand (0, 10000);
    $token = $_SESSION["token"];


    $totalRules = get_requests_rules_for_function($userId,$changeType);
    if (!count($totalRules)) {
    ?>
    <p>Use this screen to create <? print ($changeTypeLabel) ?> changes and save them in the queue</p>
    <?
    }
    else {
        ?>
       <p>You will need to click on the <a href="processChanges.php">Apply Changes</a> Link in order to deploy changes to the servers. You will have the option to review your changes before applying them</p>
       <br>
        <?
        echo "<TABLE BORDER>";
        echo "<TR><TH>CheckBox</TH><TH>CHANGE TYPE</TH><TH>CATEGORY</TH><TH>APPLICATION CLASS</TH><TH>INSTANCE</TH><TH>PARAMETER</TH><TH>STATUS</TH><TH>OPERATION</TH><TH>VALUE</TH></TR>";
        foreach ($totalRules as $value) {
            $textToDisplay = translate_rules_into_english ($value);
            echo "<TR><TD><INPUT class=\"checkbox\" TYPE=\"CHECKBOX\" NAME =\"list\" value=\"$textToDisplay[7]\" CHECKED></TD><TD>$textToDisplay[0]</TD><TD>$textToDisplay[1]</TD><TD>$textToDisplay[2]</TD><TD>$textToDisplay[3]</TD><TD>$textToDisplay[4]</TD><TD>$textToDisplay[5]</TD><TD>$textToDisplay[8]</TD><TD>$textToDisplay[6]</TD></TR>";
        }
        echo "</TABLE>";
    ?>
        <input class="submit-button3" type="button" name="CheckAll" value="Check All" onClick="checkAll(document.handleChange.list)">
        <input class="submit-button3" type="button" name="UnCheckAll" value="Uncheck All" onClick="uncheckAll(document.handleChange.list)">
        <BR>
        <input class="submit-button3" name = "deleteRequest" type="button" onClick="deleteRules(document.handleChange.list)" value="Delete From change request">
    <?
    }
    ?>
    </fieldset>
    <BR/><BR/>
    <?
    if ($changeType == "1") {
        include "threshold.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitThrehold" type="button" value="CREATE THRESHOLD CHANGE" onClick = "validate_threshold()">
        <?
    }
    elseif ($changeType == "2") {
        include "messageWording.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitMsg" type="button" value="CREATE MESSAGE CHANGE" onClick = "validate_message_wording()">
        <?
    }
    elseif ($changeType == "3") {
        include "pollTime.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitPoll" type="button" value="CREATE POLLTIME CHANGE" onClick = "validate_polltime_changes()">
        <?
    }

    elseif ($changeType == "4") {
        include "blackout.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitMsg" type="button" value="CREATE BLACKOUT CHANGE" onClick = "validate_blackout()">
        <!-- <input class="submit-button" name = "submitMsg" type="button" value="ADD BLACKOUT RULE"> -->
        <?
    }
    elseif ($changeType == "5") {
        include "notificationTarget.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitMsg" type="button" value="CREATE NOTIFICATION CHANGE" onClick = "validate_notification()">
        <?
    }
    elseif ($changeType == "6") {
        include "ping.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitMsg" type="button" value="UPDATE PING LIST" onClick = "validate_ping()">
        <?
    }
    elseif ($changeType == "7") {
        include "processCount.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitMsg" type="button" value="UPDATE PROCESS COUNT" onClick = "validate_process_count()">
        <?
    }
    elseif ($changeType == "999") {
        # This is not used but we have it here in case
        include "genericChange.php";
        ?>
        <BR/>
        <input class="submit-button" name = "submitGeneric" type="button" value="Next" onClick = "validate_generic_changes()">
        <?
    }
    if ($setfocus) {
    ?>
        <SCRIPT>
         document.forms[0].category.focus();
         </SCRIPT>
    <?
    }

    ?>

    <input type="hidden" name="rulesToDelete">
    <input type="hidden" name="newRule">
    <input type="hidden" name="setfocus">
    <!-- </form> -->

    <!-- <form action="processChanges.php" method="post"> -->


    <INPUT TYPE="hidden" NAME=formToken value="<?php print "$token";?>">


    <INPUT TYPE="hidden" NAME=ruleName>
    <INPUT TYPE="hidden" NAME=ruleValue>
    <INPUT TYPE="hidden" NAME=operationSelected>
    </form>
    <br/>

</div>
</body>
</html>
