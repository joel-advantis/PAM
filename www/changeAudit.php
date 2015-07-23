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
    <script>
        //ChangeLoginLink();
    </script>

    <?
}
?>

<form name = "view" action="changeAudit.php" method="post">
<? 
$userId = $_SESSION['user'];
$availableUsers = get_user_request();
$availableUsers[] = "0____ALL____ALL";
#$availableGroups = get_group_request();
#$availableGroups[] = "0____ALL_";
$availableHosts = get_host_request();
$availableHosts[] = "0____ALL_";
$availableStatus = array("0 SCHEDULED","1 SUCCESS","2 FAILED", "3 RETRY_MODE");
$availableStatus[] = "ALL _ALL_";

if ($user == "") { $user = $userId;}
#if ($group == "") {$group = "0";}
if ($host == "") {$host = "0";}
if ($status == "") {$status = "ALL";}
?>

<h2>Viewing Requests</h2>
<fieldset>
<legend>Selection Criteria</legend>
<p>Please select your filter criteria</p>
<TABLE>
<TR>
<TD>User: </TD>
<TD>
<SELECT  name = "user">
<?
    foreach ($availableUsers as $indUser) {
        $indUser = str_replace ("___", "\M", $indUser);
        $indUserArray = explode ("\M", $indUser);
        $indUserId = $indUserArray[0];
        $diplayName = $indUserArray[1] . " ($indUserArray[2] $indUserArray[3])";
        if ($user == $indUserId) {
            echo "<OPTION value = \"$indUserId\" SELECTED>$diplayName";    
        }
        else {
            echo "<OPTION value = \"$indUserId\">$diplayName";    
        }
    }
     
?>
</SELECT>
</TD>
<!-- <TD>Group: </TD>
<TD>
<SELECT  name = "group"> -->
<?
#foreach ($availableGroups as $indGroup) {
    #        $indGroup = str_replace ("___", "\M", $indGroup);
    #   $indGroupArray = explode ("\M", $indGroup);
    #   $indGroupId = $indGroupArray[0];
    #    $diplayName = $indGroupArray[1];
    #    if ($group == $indGroupId) {
        #        echo "<OPTION value = \"$indGroupId\" SELECTED>$diplayName";    
        #}
        #else {
            #    echo "<OPTION value = \"$indGroupId\">$diplayName";    
            #}
            #}
?>
<!-- </SELECT>
</TD> -->
<TD>Host: </TD>
<TD>
<SELECT  name = "host">
<?
    foreach ($availableHosts as $indHost) {
        $indHost = str_replace ("___", "\M", $indHost);
        $indHostArray = explode ("\M", $indHost);
        $indHostId = $indHostArray[0];
        $diplayName = $indHostArray[1];
        if ($host == $indHostId) {
            echo "<OPTION value = \"$indHostId\" SELECTED>$diplayName";    
        }
        else {
            echo "<OPTION value = \"$indHostId\">$diplayName";    
        }
    }
?>
</SELECT>
</TD>
<TD>Request Status: </TD>
<TD>
<SELECT  name = "status">

<?
    foreach ($availableStatus as $indStatus) {
        $indStatusArray = explode (" ", $indStatus);
        $statusId = $indStatusArray[0];
        $statusDisp = $indStatusArray[1];
        
        if ($status == $statusId) {
            echo "<OPTION value = \"$statusId\" SELECTED>$statusDisp";    
        }
        else {
            echo "<OPTION value = \"$statusId\">$statusDisp";    
        }
    }

?>
</SELECT>
</TD>
</TR>
</TABLE>
<br>
<input class="submit-button" name = "SUBMIT" type="submit">
<br>
<br/>

</fieldset>

<br/>
<br/>
<br/>

<fieldset>
<legend>Details</legend>
<?  
$totalRequests = get_requests_audit($user,$host,$status);

if (!count($totalRequests)) {
    echo "No requests with the selected criteria<BR>";
}
else {
    echo "Request that matched the criteria " . count($totalRequests). "<br><br>";
    echo "<TABLE BORDER>";
    echo "<TR><TH>USER</TH><TH>SERVER</TH><TH>STATUS</TH><TH>DATE APPLIED</TH><TH>TYPE OF CHANGE</TH><TH>OPERATION</TH><TH>CHANGE DETAILS</TH><TH>COMMENT</TH></TR>";

    foreach ($totalRequests as $indRequest) {
        $indRequest = str_replace("___", "\M", $indRequest);
        $indRequestArray = explode ("\M", $indRequest);
        
        $username = $indRequestArray[0];
        $firstname = $indRequestArray[1];
        $lastname = $indRequestArray[2];
        $display_name = $indRequestArray[3];
        $port = $indRequestArray[4];
    	$changeStatus = $indRequestArray[5];
        $variable = $indRequestArray[6];
        $operation = $indRequestArray[7];
        $value = $indRequestArray[8];
        $applyDate = $indRequestArray[9];
        $change_control = $indRequestArray[10];
        $comment = $indRequestArray[11];
            
        if ($changeStatus == "0") {
            $dispStatus = "SCHEDULED";
        }
        elseif ($changeStatus == "1") {
            $dispStatus = "SUCCESS";
        }
        elseif ($changeStatus == "2") {
            $dispStatus = "FAILED";
        }
        elseif ($changeStatus == "3") {
            $dispStatus = "RETRY MODE";
        }

        if ($port != "3181") {
            $display_name = $display_name . " " . $port;
        }

        # Format the output
        $variableArray = explode ("/", $variable);

        if (strpos($variable, "DIRK/pingkm"))  {
            $variableDisp = "DEVICE PING";
            $operationDisp = $operation;
            $valueDisp = str_replace (",", "<br>",$value);
        }
        elseif (strpos($variable, "PROCActiveProcessCount"))  {
            $processName = $variableArray[6];
            $variableDisp = "PROCESS MONITORING";
            $operationDisp = "CHANGE";
            $valueArray = explode (",", $value);
            $borderAlarm = $valueArray[1];
            $borderAlarmArray = explode (" ", $borderAlarm);
            $valueDisp = "Process Name=$processName <br>" . "ALARM if Count not equal to $borderAlarmArray[2]";
        }
        elseif (strpos($variable, "THRESHOLDS"))  {
            $instance = $variableArray[6];
            if (strpos ($instance, ";")) {
                # Perfmon counter
                $instance = explode (";", $instance);
                $instance = $instance[count($instance) - 1];
            }
            
            $parameter = $variableArray[7];
            $appClass = $variableArray[5];
            
            $variableDisp = "THRESHOLD CHANGE";
            $value = str_replace(",", " ", $value);
            $valueArray = explode (" ", $value);            
            $scan = "UNDEFINED";
            $stringToTranslate = "1___2___3___4___$valueArray[1]___$valueArray[2]___$valueArray[3]___$valueArray[4]___$valueArray[5]___$valueArray[6]___$valueArray[7]___$valueArray[8]___$valueArray[9]___$valueArray[10]___$valueArray[11]___$valueArray[12]___$valueArray[13]___$valueArray[14]___$valueArray[15]___$valueArray[16]___$valueArray[17]___$valueArray[18]___$valueArray[18]___$valueArray[19]___A___$scan" . "___$valueArray[0]";
            
            $valueDisp = "parameter=$parameter, instance=$instance,application=$appClass <br>" . translate_threshold_to_english($stringToTranslate);             
            $operationDisp = "CHANGE";
        }
        elseif (strpos($variable, "BLACKOUT_RULE"))  {
            $variableDisp = "BLACKOUT CHANGE";
            $category = $variableArray[4];
            $appClass = $variableArray[5];
            $instance = $variableArray[6];
            $parameter = $variableArray[7];
            $value = str_replace (":","",$value);
            $valueArray = explode (" ", $value);
            $operationDisp = $valueArray[0];
            $value = str_replace ($operationDisp, "", $value);
            $value = trim ($value);
            $valueDisp = "category=$category,application=$appClass,instance=$instance, parameter=$parameter <br>" . translate_Blackout_to_english($value);
        }        
       elseif (strpos($variable, "NOTIFICATION_RULE"))  {
                $variableDisp = "NOTIFICATION CHANGE";
                $category = $variableArray[4];
                $appClass = $variableArray[5];
                $instance = $variableArray[6];
                $parameter = $variableArray[7];
                $paramStatus= $variableArray[8];
                $value = str_replace (":","",$value);
                $valueArray = explode (" ", $value);
                $operationDisp = $valueArray[0];
                $value = str_replace ($operationDisp, "", $value);
                $value = trim ($value);
                $valueDisp = "category=$category,application=$appClass,instance=$instance, parameter=$parameter,status=$paramStatus <br>" . $value;
       }
       else {
            $variableDisp = $variable;
            $operationDisp = $operation;
            $valueDisp = $value;
        }
            
        echo "<TR><TD NOWRAP>$username($firstname $lastname)</TD><TD NOWRAP>$display_name</TD><TD NOWRAP>$dispStatus</TD><TD NOWRAP>$applyDate</TD><TD NOWRAP>$variableDisp</TD><TD NOWRAP>$operationDisp</TD><TD NOWRAP>$valueDisp</TD><TD NOWRAP>$comment</TD></TR>";
    }
    # Close the last table
    echo "</TABLE>";
    echo "<BR>";
    
}

?>
</fieldset>
<br/>
<br/>
</form>
</div>

