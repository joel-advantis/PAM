<?

// Load header, scripts, etc
include "header.php";
?>
<script src="advantis-common.js"></script>
<div id="centercontent" class="main">


<?
if (!verify()) {
    exit;
} else {
    $userId = $_SESSION['user'];
    if (!check_rights("1")) {
        print_lack_of_privledge_warning();
    }
}

// Main page starts here

?>
<h2>Step 2 of 2: Applying Changes</h2>

<?
$userId = $_SESSION['user'];
$sessionToken  = $_SESSION["token"];

?>

<form action="<? print $PHP_SELF; ?>" method="post" name = "processChange" >
<fieldset>
<legend>All Change in Queue</legend>
<?
if (($rulesToDelete != "") && ($sessionToken == $formToken)) {
    $rulesToDelete = trim ($rulesToDelete);
    delete_rules($rulesToDelete);
    $rulesToDelete = "";
    $sessionToken  = $sessionToken + 1;
    $_SESSION["token"] = $sessionToken;
}

elseif (($rulesToSubmit != "") && ($sessionToken == $formToken)) {
    $rulesToSubmit = trim ($rulesToSubmit);
    # FORCING PRIORITY TO BE 3 FOR EDS
    $priority = 3;
    # Reset groups to 0 since the agents are populated in the manualagents field
    $groups = "";
    $manualAgents = $_POST['manualAgents'];
    $agentCount = count($manualAgents);
    $manualAgents = implode ("\n", $manualAgents);
    update_change_request($rulesToSubmit, $userId, $priority, $comments, $manualAgents, $groups, $changeControl);
    ?><SCRIPT>
    msg = "Changes submitted successfully to <?print ($agentCount) ?> servers";
    alert(msg);
    </SCRIPT>
    <?
    $rulesToSubmit = "";
    $sessionToken  = $sessionToken + 1;
    $_SESSION["token"] = $sessionToken;
}

echo "<INPUT TYPE=\"hidden\" NAME=formToken value=\"$sessionToken\">";

$totalRules = get_all_requests_rules($userId);
if (!count($totalRules)) {
    ?>
    <p>There is nothing to apply. You must first create a change(s) using the <a href="changeMgt.php">Create Changes</a> dialogue</p>
    <BR><BR><BR><BR><BR>
    </fieldset>
    <BR><BR><BR><BR><BR>
    <BR><BR><BR><BR><BR>
<?
}
else {
    ?>
    <p>Here are all the changes in your queue, please select the one you want to apply to the server(s):</p>
    <TABLE BORDER>
    <TR><TH>CheckBox</TH><TH>CHANGE TYPE</TH><TH>CATEGORY</TH><TH>APPLICATION CLASS</TH><TH>INSTANCE/PROCESS NAME</TH><TH>PARAMETER</TH><TH>STATUS</TH><TH>OPERATION</TH><TH>VALUE</TH></TR>
    <?
    # Need to see if we have any process add rules to merge it into 1
    $processRules = preg_grep ("/ProcessConfigurationList/", $totalRules);
    if (count($processRules)) {
            # We have a process add rule, need to do some formatting. 
            $totalRules = format_display_rules($totalRules);
    }
    foreach ($totalRules as $value) {
        $textToDisplay = translate_rules_into_english ($value);
        echo "<TR><TD><INPUT TYPE=\"CHECKBOX\" NAME =\"list\" value=\"$textToDisplay[7]\"></TD><TD>$textToDisplay[0]</TD><TD>$textToDisplay[1]</TD><TD>$textToDisplay[2]</TD><TD>$textToDisplay[3]</TD><TD>$textToDisplay[4]</TD><TD>$textToDisplay[5]</TD><TD>$textToDisplay[8]</TD><TD NOWRAP>$textToDisplay[6]</TD></TR>";

    }
    echo "</TABLE>";
?>
    <input class="submit-button3" type="button" name="CheckAll" value="Check All" onClick="checkAll(document.processChange.list)">
    <input class="submit-button3" type="button" name="UnCheckAll" value="Uncheck All" onClick="uncheckAll(document.processChange.list)">
    <BR>
    <input class="submit-button3" name = "deleteRequest" type="button" onClick="deleteRules(document.processChange.list);" value="Delete From Queue">

    <BR>
    </fieldset>
    <BR>
    <br>
    <fieldset>
    <legend>Group Selection</legend>
    <p>Select a group of servers and click on the >> to move it to the group selected box. by double clicking on a group name, you can see the subgroups that exists under it. You can select a group or a subgroup. Once a group is moved to the Group Selected box, the servers that belong to this group will be highlighted in the Server Selection section</p>
    <?

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

    <TR><TH class="menu">Server Groups</TH><TH class="menu"></TH><TH class="menu">Group Selected</TH></TR> 
    <TR><TD colspan = "4" class="menu" id="groupSelected"></TD></TR>

    <TR><TD class="menu"> 
    <?
    echo "<SELECT class=\"groups\" MULTIPLE name = \"list3\" size = \"5\" onDblClick=\"navigate_next_level('$allGroups');\">";

    foreach ($firstLevel as $value) {

        $value = trim ($value);
        if ($value == "") {
            continue;
        }
        $valueTmp = str_replace("___", "\M", $value);
        $valueArray = explode ("\M", $valueTmp);
        $serverGroup  = $valueArray[0];
        echo "<option value =\"$serverGroup\">$serverGroup";
    }
    ?>  
    </SELECT>
    </TD>


    <TD class="menu" style="padding-top:20px;">
    <INPUT TYPE="button" NAME="down" VALUE="&gt;&gt;" ONCLICK="moveSelectedAgentGroup(this.form['list3'],this.form['list4'])">
    </TD>
    <TD class="menu">
    <SELECT class="groupsmed" name = "list4" size = "5" onDblClick="removeSelectedGroup(this.form['list4'])">
    <?
    $groupAgentSelected = array();
    if ($groups != "") {
        $groupsArray = explode (" ", $groups);
        foreach ($groupsArray as $indGroup) {
                  $indGroup = trim ($indGroup);
                  if ($indGroup == "") {
                      continue;
                  }
                  echo "<option value = \"$indGroup\" >$indGroup";        
                  $groupAgentSelected = array_merge($groupAgentSelected, get_agent_for_mutiple_parent("$indGroup"));
        }
    }
    $groupAgentSelected = array_unique ($groupAgentSelected);
    
    ?>
    </SELECT>
    </TD></TR>

    </TABLE>
    </fieldset>

    <?  
    $totalHosts = get_all_hosts_for_user($_SESSION['user']);
    $totalHostString = implode (",", $totalHosts);
    $totalHostString = str_replace("___", " ", $totalHostString);
    ?>

    <br>
    <br>    
    <fieldset>
    <legend>Server Selection</legend>
    <p>To select Multiple agent, click on the <i>ctll</i> key. If you selected a group in the previous section, all the servers that belong to that group will be highlighted. The number of servers that are selected appears right below the selection box</p>
    <!-- <p>You can enter 1 or multiple agent separated by newline. The format is 'hostname port_number' if you do not specify a port, it will default to the first port it finds</p>
    <script language="JavaScript" type="text/javascript" src="richtext.js"></script>
    <script language="JavaScript" type="text/javascript">
    theForm = document.forms[0];
    initRTE("images/","","",false);
    writeRichText('manualAgents','',350,100,false,false);
    
    </script> --> 
    
    <?
            $hostCount = count($totalHosts);
            $groupAgentSelected = array_intersect($groupAgentSelected, $totalHosts);
            $agentSelected = count($groupAgentSelected) . " server(s) selected";
            echo "You have $hostCount servers you can select from <br>";
    ?>
    <SELECT class="groups" MULTIPLE name = "manualAgents[]" size = "10" onChange="update_count()">

    <?
    # We need to get all agents in the group and merge it with totalHost
        foreach ($totalHosts as $indAgent) {
              $indAgent = str_replace ("___", " ", $indAgent);
              $indAgentArray = explode (" ", $indAgent);
              $value = $indAgentArray[0] . " " . $indAgentArray[1];
              $display = $indAgentArray[0];
              $display = trim ($display);
              if ($display == "") {
                  continue;
              }
              if (preg_grep ("/$display/", $groupAgentSelected)) {
                  echo "<option value = \"$indAgent\" SELECTED>$display";
              }
              else {
                  echo "<option value = \"$indAgent\" >$display";
              }
          
        }
    ?>

    </SELECT>
    <br><br>
    <b><input class="inputbox" name="numberOfAgent"  disabled value="<?print ($agentSelected) ?>"></b><br>

    <?

    # echo "<input class=\"submit-button\" name = \"validateAgent\" type=\"button\" onClick=\"validate_agents('$totalHostString', '1');\" value=\"VALIDATE AGENTS\">";

    ?>
    </fieldset>

    <br/>
    <br>

    <fieldset>
    <legend>Submit</legend>

    <p>
    Enter a change request label, this can be the change control (Optional):
    <br/>
    <br/>
    <label for="changeControl">Change Control ID:</label>
    <input class="username-box"  name="changeControl">
    </p>
    <p><b>Comments</b><p>
    <textarea name=comments cols="50" rows="4"></textarea>
    <br/>
    <br/>
    <br/>
    <!-- <p>Select the urgency for this request</p>
    <br/>
    <SELECT  name = "priority">
    <OPTION value = "3">MEDIUM
    <OPTION value = "4">LOW
    <OPTION value = "2">HIGH
    </SELECT>
    <BR/> --> 
    <?
    $allGroupServersValString = "," . implode (",", $allGroupServersVal) . ",";
    
    # echo "<input class=\"submit-button\" name = \"submitRequest\" type=\"button\" onClick=\"validate_agents('$totalHostString', '0');submitRules(document.processChange.list, '$allGroupServersValString');\" value=\"APPLY TO SELECTED AGENTS\">";
    echo "<input class=\"submit-button\" name = \"submitRequest\" type=\"button\" onClick=\"submitRules(document.processChange.list)\" value=\"APPLY TO SELECTED AGENTS\">";
?>
    <br>
    </fieldset>
    <br>
    <br>
    <br>
    </div>
    <input type="hidden" name="rulesToDelete">
    <input type="hidden" name="rulesToSubmit">
    <input type="hidden" name="hosts">
    <input type="hidden" name="groups">
    <input type="hidden" name="previousSelection">
    </form>
    <?
}

?>
</div>
</body>
</html>
<?
