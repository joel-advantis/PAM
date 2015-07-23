<?
// Include header, scripts, etc

include "header.php";
include "php_calendar.php";
?>
<div id="centercontent" class="main">
    <script src="advantis-common.js"></script>

    <?
    // Verify login

    if (!verify()) {
        exit;
    } else {
        // Make sure user has access to this page

        $userId = $_SESSION['user'];
        if (!check_rights("2")) {
            print_lack_of_privledge_warning();
        }

    }

    ?>
    <form action="<? print $PHP_SELF; ?>" method="post" name = "scheduleChange" >
    <h2>Schedule Changes</h2>

    <?
    $userId = $_SESSION['user'];
    $sessionToken  = $_SESSION["token"];

    if (($action == 1) && ($sessionToken == $formToken)) {
        echo "Scheduling requeset ID $requestId <br>";
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;
        schedule_request($requestId, $comments, $startTime, $endTime, $retries, $retryInterval, $changeControl, $priority, $hosts, $groups);
    }
    # Setting a session token
    $_SESSION["token"] = rand (0, 10000);  
    $token = $_SESSION["token"];
    echo "<INPUT TYPE=\"hidden\" NAME=formToken value=\"$token\">";
    ?>
    <fieldset>
        <legend>Requests</legend>
        
        <? // Show table of approved requests
        
        $totalRequests = get_all_requests(2);

        if (!count($totalRequests)) {
            ?>
            No requests pending scheduling <BR/>
        <br/>
        <br/>
            </fieldset> <!-- End of Requests --> 

            <?

        } else {
            ?>

            <p>Click on the request id to view all details</p><BR>
            <TABLE BORDER id = "requestTable">
                <TR><TH>REQUEST ID</TH><TH>SCHEDULE</TH><TH>COMMENT</TH><TH>Change Control</TH><TH>DATE ENTERED</TH><TH>DATE APPROVED</TR>
                    <?
                foreach ($totalRequests as $indRequest) {
                        $indRequest = str_replace("___", "\M", $indRequest);
                        $indRequestArray = explode ("\M", $indRequest);
                    $requestId = $indRequestArray[0];
                    $priority  = $indRequestArray[1];
                    $comments  = $indRequestArray[2];
                    $changeControl = $indRequestArray[4];
                    $dateEntered = $indRequestArray[5];
                    $dateModified = $indRequestArray[6];
                    $detailForRequest = get_details_for_request_id ($requestId);
                    $hostTotal     = array();
                    $groupTotal    = array();

                    foreach ($detailForRequest as $indRequest) {
                        $indRequest = str_replace("___", "\M", $indRequest);
                        $indRequestArray = explode ("\M", $indRequest);
    
                        if ($indRequestArray[11] != "") {
                            array_push ($hostTotal, $indRequestArray[11] . " " . $indRequestArray[12]);
                        }
                        if ($indRequestArray[13] != "") {
                            array_push ($groupTotal, $indRequestArray[13]);
                        }
                    }

                    sort ($hostTotal);
                    $hostTotal = array_unique ($hostTotal);
                    $hostTotalString = implode (",", $hostTotal);

                    sort ($groupTotal);
                    $groupTotal = array_unique ($groupTotal);
                    $groupTotalString = implode (",", $groupTotal);
                    ?> 
                    <TR <? print "id = \"$requestId\"";       ?> >
                    <TD><input class="submit-button3" name = "populateRequeset" type="button" onClick="populate_request_id(<? print "'$requestId', '$groupTotalString','$hostTotalString','$comments','$changeControl','$priority'"; ?>);" value="X"></TD>
                    <TD><a href="getRequestIdDetails.php?<? print "requestId=$requestId"; ?>" target="_blank"><? print "$requestId"; ?></a></TD>
                    <TD><? print "$comments";       ?></TD>
                    <TD><? print "$changeControl";  ?></TD>
                    <TD><? print "$dateEntered";    ?></TD>
                    <TD><? print "$dateModified";   ?></TD>
                </TR>
                <?
                }
                ?> 
            </TABLE>        
            <script>
            var r3=document.getElementById('requestTable').getElementsByTagName('tr'); 
            var l=r.length; 

            for (i=0;i<l;i++){
                 r3[i].onmouseover = function(){this.style.backgroundColor="#333"};
                 r3[i].onmouseout = function(){this.style.backgroundColor=""};
                }
            </script>
            <br/>
            </fieldset> <? /* end of Requests */
        ?>

    <BR>
    <br>

    <fieldset>
        <legend>Schedule</legend>
        <BR/>

        <? /* Calendar popups for start and end times */ ?>
        <script src="calendar2.js"></script><!-- Date only with year scrolling -->
        <table class="menu">
        <tr class="menu">
            <td class="menu">
            <b>Start time:</b><br/>
            <input class="requestid-box" type="Text" name="startTime" value="">
            </td><td class="menu">
            <a href="javascript:startTime.popup();"><img src="images/cal.gif" width="16" height="16" border="0" align="top" alt="Set start time"></a><br>
            </td><td class="menu" style="width:15em;"></td>
            <td class="menu"><b>Retries:</b><br/>
            <input class="requestid-box" type="Text" name="retries" value="3">
            </td>
        </tr>
        <tr class="menu"><td class="menu">
            <b>End time:</b><br/>
            <input class="requestid-box" type="Text" name="endTime" value="">
            </td><td class="menu">
            <a href="javascript:endTime.popup();"><img src="images/cal.gif" width="16" height="16" border="0" align="top" alt="Set end time"></a><br/>
            </td><td class="menu" style="width:15em;"></td>
            <td class="menu"><b>Retry interval (minutes):</b><br/>
            <input class="requestid-box" type="Text" name="retryInterval" value="10">
            </td>
        </tr>
        </table>

        <script language="JavaScript">
            var startTime = new calendar2(document.forms['scheduleChange'].elements['startTime']);
            startTime.year_scroll = false;
            startTime.time_comp = true;
            var endTime = new calendar2(document.forms['scheduleChange'].elements['endTime']);
            endTime.year_scroll = false;
            endTime.time_comp = true;
        </script>
    </fieldset><? /* end of Schedule */ ?>
    <BR/>
    <br/>
    <fieldset>
        <legend>Target servers</legend>
        <?
        // Draw Server Group selection boxes
        $allGroupServersVal = get_all_groups_for_user($_SESSION['user']);
        $allGroups = "";
        foreach ($allGroupServersVal as $value) {

            $value = trim ($value);
            if ($value == "") {
                continue;
            }
            $valueArray = explode (".", $value);
            $valueLength = count ($valueArray);
            $i = 0;
            while ($i < $valueLength) {
                $sliceLength = $i + 1;
                $arraySlice = array_slice($valueArray, 0, $sliceLength);
                $arraySliceString = implode (".", $arraySlice);
                $allGroups = $allGroups . $arraySliceString . "\n";
                $i++;
            }
        }

        $allGroupServers = explode ("\n", $allGroups);
        sort($allGroupServers);
        $allGroupServers = array_unique($allGroupServers);
        $allGroups = implode (",", $allGroupServers);
        $allGroups = $allGroups . ",";
        $firstLevel = preg_grep ("/\./", $allGroupServers, PREG_GREP_INVERT);
        $totalGroupGroup =  "";
        ?>

        <TABLE class="menu">

            <TR><TH class="menu">Server groups</TH><TH class="menu"></TH><TH class="menu">Selected groups</TH></TR>
            <TR><TD colspan = "4" class="menu"  id="groupSelected"></TD></TR>

            <TR><TD class="menu">

            <SELECT class="groups" name = "list3" size = "5" onDblClick="navigate_next_level('<? print "$allGroups"; ?>');">
            <?
            foreach ($firstLevel as $value) {

                $value = trim ($value);
                if ($value == "") {
                    continue;
                }
                $valueTmp = str_replace("___", "\M", $value);
                $valueArray = explode ("\M", $valueTmp);
                $serverGroup  = $valueArray[0];
                ?>
                <option value =<? print "\"$serverGroup\">$serverGroup"; 
            }
            ?>
            </SELECT>
            </TD>


            <TD class="menu" style="padding-top:20px;">
            <INPUT class="little-button" TYPE="button" NAME="down" VALUE="&gt;&gt;" ONCLICK="moveSelectedGroup(this.form['list3'],this.form['list4'])">
            </TD>
            <TD class="menu">
            <SELECT class="groupsmed" name = "list4" size = "5" onDblClick="removeSelectedItem(this.form['list4'])">
            </SELECT>
            </TD></TR>

        </TABLE>

        <? // Draw Agent entry/verification box  
        $totalHosts = get_agent_for_mutiple_parent("%");
        $totalHostString = implode (",", $totalHosts);
        $totalHostString = str_replace("___", " ", $totalHostString);
        ?>

        <br><br>
        <div style="float:left;">
            <b>Agents:</b><br/>
            <script language="JavaScript" type="text/javascript" src="richtext.js"></script>
            <p>
            <script language="JavaScript" type="text/javascript">
                theForm = document.forms[0];
                initRTE("images/","","",false);
                writeRichText('manualAgents','',350,100,false,false);
            </script>
        </div>
        <div><br/><br/></div>
        <div><table><tr><td>
            Enter a list of agents in the form <pre>'hostname [port_number]'</pre> (hostname wildcards: *,?).
            If you do not specify a port, it will default to the first port it finds.
            </td></tr></table>
        </div>
        </p>
  
        <input class="submit-button2" name = "validateAgent" type="button" onClick="validate_agents('<? print "$totalHostString"; ?>', '1');" value="Validate">
    </fieldset><? /* end of Schedule */ ?>


    <br/>
    <br/>

    <fieldset>
        <legend>Submit</legend>
        <? /* Change Ticket & Priority floater */ ?>
        <div style="float:right;border:0px solid #333;">
            <p>
                <b>Change Ticket (optional):</b></br>
                <input class="requestid-box"  name="changeControl"/><br/>
                <b>Priority</b><br/>
                <SELECT  name = "priority">
                    <OPTION value = "2">HIGH
                    <OPTION SELECTED value = "3">MEDIUM
                    <OPTION value = "4">LOW
                </SELECT>
            </p>
        </div>

        <? /* Comment box */ ?>
        <div style="padding-top:1em;border:0px solid #333;">
            <b>Comments</b><br/>
            <textarea class="comment-box"name="comments" rows="5"></textarea>
        </div>
        <br/>
        <br/>
        <br/>
        <BR/>
        <?
        $allGroupServersValString = "," . implode (",", $allGroupServersVal) . ",";
        ?>
        <input class="submit-button" name = "schedule" type="button" onClick="validate_agents('<? print "$totalHostString"; ?>', '1');submit_schedule();" value="Submit Schedule">
    <script>
        scheduleChange.schedule.disabled = "true";
    </script>
        <br>
    </fieldset> <? /* end of Submit */ ?>
    <br>
    <br>
    <br>
</div>
<input type="hidden" name="requestId">
<input type="hidden" name="rulesToSubmit">
<input type="hidden" name="hosts">
<input type="hidden" name="groups">
<input type="hidden" name="previousSelection">
<input type="hidden" name="lastSelection">
<input type="hidden" name="action">
</form>
</body>
</html>
<?
}

