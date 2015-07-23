<?

/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Host selection box/Server Group selection tree
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/


include "header.php";
?>
<script src="advantis-common.js"></script>
<?
set_time_limit( 180 );

$startTime = time();

$reportType = $_GET["reportType"];
if ($reportType == "") {

    $reportType = $_POST["reportType"];
}

    
if ($reportType == "1") {
    $reportTitle = "Threshold";
}
elseif ($reportType == "2") {
    $reportTitle = "Alertable Metric";
}
elseif ($reportType == "3") {
    $reportTitle = "Compliance";
}
elseif ($reportType == "4") {
    $reportTitle = "Agent Version / OS Version";
}
elseif ($reportType == "5") {
    $reportTitle = "Agent Misconfiguration";
}
elseif ($reportType == "6") {
    $reportTitle = "Stale data points";
}

?>
<div id="centercontent" class="main">
<h2><? print ("$reportTitle"); ?> Report - Host Selection</h2>
<?
?>
<form action="hostSelection.php" method="post" name="configReport">

    <fieldset>
    <legend>Group Selection</legend>
    <p>Select a group of servers and click on the >> to move it to the group selected box. by double clicking on a group name, you can see the subgroups that exists under it. You can select a group or a subgroup. Once a group is moved to the Group Selected box, the servers that belong to this group will be highlighted in the Server Selection section</p>
    <?

    $userToGet = $_SESSION['user'];
    if ($userToGet == "") {
        $allGroupServersVal = get_all_groups_for_user('1');
    }
    else {
        $allGroupServersVal = get_all_groups_for_user($userToGet);
    }
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
    echo "<SELECT class=\"groups\" MULTIPLE name = \"list3\" size = \"10\" onDblClick=\"navigate_next_level('$allGroups');\">";

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
    <SELECT class="groupsmed" name = "list4" size = "10" onDblClick="removeSelectedGroup(this.form['list4'])">
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
    if ($userToGet == "") {
        $totalHosts = get_all_hosts_for_user('1');
    }
    else {
        $totalHosts = get_all_hosts_for_user($userToGet);
    }

    $totalHostString = implode (",", $totalHosts);
    $totalHostString = str_replace("___", " ", $totalHostString);
    ?>

    <br>
    <br>    
    <fieldset>
    <legend>Server Selection</legend>
    <p>To select Multiple agent, click on the <i>ctll</i> key. If you selected a group in the previous section, all the servers that belong to that group will be highlighted. The number of servers that are selected appears right below the selection box</p>
    
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
              $display = trim ($display, "'");
              
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
<br>
<br>

<br>
<?
if (($reportType == 1) || ($reportType == 2)) {
?>
    <fieldset>
    <legend>Report Scope</legend>
    <p>Field below is an inclusion field for report content. (i.e. for a report which only includes CPU, enter CPU)</p>

    <div align = "left">
    <input class="requestid-box" type="input" name="filterList" >
    </div>
    <br/>
    </fieldset>
    <br/>
    <br/>
<?
}
else {
?>
    <INPUT TYPE="hidden" NAME="filterList">
<?
}
if ($reportType == 6) {
?>
    <fieldset>
    <legend>Report Scope</legend>
    <p>Minimum age of most recent data point. This will select any agents with parameters whose most recent data points is older than this thresholds (in minutes) </p>

    <div align = "left">
    <input class="requestid-box" type="input" name="dataPointTime" >
    </div>
    <br/>
    <?
    $allAppClass = get_all_active_application_class();
    ?>
    <p>Select the application class you want to filter out of the report. To select mutiple, use the CTRL Key</p>
    <SELECT name = "filterAppClass[]" multiple size = 5>
    <?
        foreach ($allAppClass as $indAppClass) {
         if ($indAppClass == "AS_EVENTSPRING") {
            echo "<OPTION VALUE=\"$indAppClass\" SELECTED>$indAppClass";
         }
         else {
            echo "<OPTION VALUE=\"$indAppClass\">$indAppClass";
         }

    }
    ?>
    </SELECT>
    </fieldset>
    <br/>
    <br/>


<?
}
?>

<fieldset>
<legend>Submission</legend>
<div align = "left">
<?
echo "<input class=\"submit-button\" type=\"button\" onClick=\"submitReport()\" value=\"GENERATE REPORT\">";
echo "<INPUT TYPE=\"hidden\" NAME=\"reportType\" value = \"$reportType\">";
echo "<INPUT TYPE=\"hidden\" NAME=\"filterAppClassSelect\" value = \"$filterAppClassSelect\">";
?>

</div>
</fieldset>
<br/>
<br/>
<input type="hidden" name="previousSelection">
<input type="hidden" name="hosts">
<input type="hidden" name="groups">

</form>

<form action="generateConfigReport.php" target="_blank" method="post" name="generateReport">
<input type="hidden" name="hostsToReport">
<INPUT TYPE="hidden" NAME="reportType">
<INPUT TYPE="hidden" NAME="filterList">
</form>

</div>
