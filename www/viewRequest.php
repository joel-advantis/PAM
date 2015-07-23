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
    $userId = $_SESSION['user'];
    #if (!check_rights("2")) {
        #    print_lack_of_privledge_warning();
        #}
}
?>

<form name = "view" action="viewRequest.php" method="post">
<h2>Viewing Requests</h2>
<fieldset>
<legend>Select Status</legend>
<p>Select the status to display</p>
<SELECT  name = "status" onChange="submit()">

<?
$statusSelected = 0;

if ($status == '0') {
    echo "<OPTION value = \"0\" SELECTED>IN PROGRESS";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"0\" >IN PROGRESS";
}

if ($status == 1) {
    echo "<OPTION value = \"1\" SELECTED>PENDING APPROVAL";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"1\" >PENDING APPROVAL";
}

if ($status == 2) {
    echo "<OPTION value = \"2\" SELECTED>APPROVED";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"2\" >APPROVED";
}	

if ($status == 3) {
    echo "<OPTION value = \"3\" SELECTED>REJECTED";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"3\" >REJECTED";
}	

if  ($status == 4) {
    echo "<OPTION value = \"4\" SELECTED>SCHEDULED";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"4\" >SCHEDULED";
}

if  ($status == 5) {
    echo "<OPTION value = \"5\" SELECTED>SUCCESS";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"5\" >SUCCESS";
}

if  ($status == 6) {
    echo "<OPTION value = \"6\" SELECTED>FAILED";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"6\" >FAILED";
}

if  ($status == 7) {
    echo "<OPTION value = \"7\" SELECTED>PARTIAL FAIL";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"7\" >PARTIAL";
}

if  ($status == 8) {
    echo "<OPTION value = \"8\" SELECTED>RETRY MODE";
    $statusSelected = 1;
}
else {
    echo "<OPTION value = \"8\" >RETRY MODE";
}

if (!$statusSelected) {
	echo "<OPTION value = \"ALL\" SELECTED>ALL_STATUS";
}
else { 
	echo "<OPTION value = \"ALL\">ALL_STATUS";
}


?>
</SELECT>
<br/>
<br/>

</fieldset>

<br/>
<br/>
<br/>

<fieldset>
<legend>Details</legend>
<?  
if (($status == "ALL") || ($status == "")) {
    $status = "%";
}
    
$totalRequests = get_all_requests($status);


if (!count($totalRequests)) {
    echo "No requests with the selected status<BR>";
}
else {
    echo "<TABLE BORDER>";
    echo "<TR><TH>STATUS</TH><TH>REQUEST ID</TH><TH>PRIORITY</TH><TH>COMMENT</TH><TH>DATE ENTERED</TH><TH>DATE MODIFIED</TH></TR>";

    foreach ($totalRequests as $indRequest) {
        $indRequest = str_replace("___", "\M", $indRequest);
        $indRequestArray = explode ("\M", $indRequest);
        if ($indRequestArray[3] == "0") {
            $dispStatus = "IN PROGRESS";
        }
        elseif ($indRequestArray[3] == "1") {
            $dispStatus = "PENDING APPROVAL";
        }
        elseif ($indRequestArray[3] == "2") {
            $dispStatus = "APPROVED";
        }
        elseif ($indRequestArray[3] == "3") {
            $dispStatus = "REJECTED";
        }
        elseif ($indRequestArray[3] == "4") {
            $dispStatus = "SCHEDULED";
        }
        elseif ($indRequestArray[3] == "5") {
            $dispStatus = "SUCCESS";
        }
        elseif ($indRequestArray[3] == "6") {
            $dispStatus = "FAILED";
        }
        elseif ($indRequestArray[3] == "7") {
            $dispStatus = "PARTIAL FAIL";
        }
        elseif ($indRequestArray[3] == "8") {
            $dispStatus = "RETRY MODE";
        }


        echo "<TR><TD>$dispStatus</TD><TD><a href=\"getRequestIdDetails.php?requestId=$indRequestArray[0]\" target=\"_blank\">$indRequestArray[0]</a></TD><TD>$indRequestArray[1]</TD><TD>$indRequestArray[2]</TD><TD>$indRequestArray[5]</TD><TD>$indRequestArray[6]</TD></TR>";
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

