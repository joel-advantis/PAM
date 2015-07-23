<?

/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  PHP Function Library
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/


/////////////////////////////////////////////////////////////////////////////////
// Function: run_query
// Description: Run a SQL query and return output in an array format
//
//

function run_query($cmd) {

    // These are set in the configuration file
    global $SQL_SERVER;
    global $SQL_USER;
    global $SQL_PASSWORD;
    global $SQL_DATABASE;
    global $WITH_MCRYPT;

    if ($WITH_MCRYPT) {
        $key = "pam";

        $iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
        $DECRIPT_PASSWORD = trim(chop(base64_decode($SQL_PASSWORD)));
        $DECRIPT_PASSWORD = mcrypt_cfb (MCRYPT_CAST_256, $key, $DECRIPT_PASSWORD, MCRYPT_DECRYPT, $iv);
    } else {
        $DECRIPT_PASSWORD = $SQL_PASSWORD;
    }


    if (($SQL_SERVER == "") || ($SQL_USER == "") || ($DECRIPT_PASSWORD == "") || ($SQL_DATABASE == "")) {
        echo "Configuration settings missing, please contact the admin";
        exit;
    }

    $connection = mysql_pconnect("$SQL_SERVER","$SQL_USER","$DECRIPT_PASSWORD") or die ("Unable to connect to MySQL server.");
    $db = mysql_select_db("$SQL_DATABASE") or die ("Unable to select requested database.");

    $cmd = $cmd . ";";
    debug_var("Executing query:",$cmd);
    // Run the SQL statement
    $sql = mysql_query($cmd, $connection) or die(mysql_error());
    $i = 0;

    // Store the output result in that array
    $result = array();

    # // Add these 2 lines for Debugging purposes
    # Update: shouldn't ever need this
    #$rs = mysql_fetch_row($sql);
    #echo " error " . mysql_error() . "<BR>";

    while ($rs = mysql_fetch_row($sql)) {

        $numberOfFieds = count($rs);
        $j = 1;
        $result[$i] = "";
        $result[$i] = $rs[0];
        if ($numberOfFieds > 1) {

            while ($j < $numberOfFieds) {
                    $result[$i] = $result[$i] . "___" . $rs[$j];
                $j++;
            }
        }
        $i++;
    }
    debug_var("Query result:", $result);
    return $result;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_sql_cmd
// Description: Run a SQL command, this is used for any command beside queries
//

function run_sql_cmd($cmd) {

    // These are set in the configuration file
    global $SQL_SERVER;
    global $SQL_USER;
    global $SQL_PASSWORD;
    global $SQL_DATABASE;
    global $WITH_MCRYPT;

    debug_var("Executing query:",$cmd);

    if ($WITH_MCRYPT) {
        $key = "pam";

        $iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
        $DECRIPT_PASSWORD = trim(chop(base64_decode($SQL_PASSWORD)));
        $DECRIPT_PASSWORD = mcrypt_cfb (MCRYPT_CAST_256, $key, $DECRIPT_PASSWORD, MCRYPT_DECRYPT, $iv);
    } else {
        $DECRIPT_PASSWORD = $SQL_PASSWORD;
    }

    if (($SQL_SERVER == "") || ($SQL_USER == "") || ($DECRIPT_PASSWORD == "") || ($SQL_DATABASE == "")) {
        echo "Configuration settings missing, please contact the admin";
        exit;
    }

    $connection = mysql_pconnect("$SQL_SERVER","$SQL_USER","$DECRIPT_PASSWORD") or die ("Unable to connect to MySQL server.");
    $db = mysql_select_db("$SQL_DATABASE") or die ("Unable to select requested database.");

    $sql = mysql_query($cmd, $connection) or die(mysql_error());

    // Need to add some error checking statements
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_agent_group_count
// Description:
//
//

function get_agent_group_count() {

    $cmd = "DROP TABLE IF EXISTS AgentsByGroup";
    run_sql_cmd($cmd);

    $cmd = "
        CREATE TEMPORARY TABLE AgentsByGroup AS
        SELECT server_group, display_name, port
        FROM server_groups
            LEFT JOIN agent_groups ON server_groups.id = agent_groups.groupid
            LEFT JOIN agents on agent_groups.agentid = agents.id";

    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS agent_totals";
    run_sql_cmd($cmd);

    $cmd = "
        CREATE TEMPORARY TABLE agent_totals AS
        SELECT *,count(DISTINCT display_name) agents
        FROM agentsbygroup
        GROUP BY server_group";
        run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS at2";
    run_sql_cmd($cmd);
    $cmd = "
        CREATE TEMPORARY TABLE at2 AS
        SELECT * FROM agent_totals";

    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS totals";
    run_sql_cmd($cmd);
    $cmd = "
    CREATE TEMPORARY TABLE totals AS
    SELECT
        a.server_group,
        a.agents,sum(a.agents) totals
    FROM agent_totals a
        INNER JOIN at2 b  ON a.server_group LIKE concat(b.server_group,'%')
    GROUP BY b.server_group";

    run_sql_cmd($cmd);

    $query = "
        SELECT
            a.server_group,
            b.display_name,b.port,
            a.agents,
            a.totals
        FROM totals a
            INNER JOIN AgentsByGroup b ON a.server_group = b.server_group 
            ORDER by length(a.server_group)";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: create_where_statement_for_all_hosts
// Description:
//
//

function create_where_statement_for_all_hosts($totalAgentsToProcess) {

    $whereField = "";
    foreach ($totalAgentsToProcess as $value) {
        $value = trim ($value);
        if ($value == "") {
            continue;
        }

        $value = str_replace("___", "\M", $value);
        $valueArray = explode ("\M", $value);

        $hostName   = $valueArray[0];
        $portNumber = $valueArray[1];
        $hostName = trim ($hostName, "'");
        if ($hostName == "") {
            continue;
        }
        if ($whereField != "") {
            $whereField = $whereField . " OR (display_name = '$hostName' AND port = '$portNumber') \n";
        }
        else {
            $whereField = "((display_name = '$hostName' AND port = '$portNumber')";
        }

    }
    if ($whereField != "") {
        $whereField = $whereField . ")";
    }
    return $whereField;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_group
// Description: Get all groups that exists in PCM.
//          The groups are saved in the server_groups table
//

function get_total_group() {

    $query = "SELECT server_group FROM server_groups ORDER BY server_group";
    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function:  howmany
// Description: return  the plural or singular form of a noun
//

function howmany($number, $singular, $plural) {
  return ($number == 1) ? "$number $singular" : "$number $plural";
}

/////////////////////////////////////////////////////////////////////////////////
// Function:  thisthese
// Description: return  the plural or singular form of a pronoun
//

function thisthese($number, $singular, $plural) {
  return ($number == 1) ? "$singular" : "$plural";
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_agent_for_group
// Description: Get all agents that belong to a certain group.
// Only the first level agents will be returned.
//

function get_agent_for_parent($parentName) {

    $query = "
    SELECT display_name, port FROM agents, agent_groups, server_groups
    WHERE server_groups.server_group = '$parentName'
        AND agent_groups.agentid = agents.id
        AND agents.status != 'INACTIVE'
        AND server_groups.id = agent_groups.groupid";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_agent_for_mutiple_parent
// Description: Get all agents that belong to a certain group, including all subgroups
//

function get_agent_for_mutiple_parent($parentName) {


    // Return all hosts if Root is selected
    if (($parentName == "%") || ($parentName == "%%")) {
        $query = "SELECT display_name,port 
                    FROM agents 
                    WHERE status !='INACTIVE'
                    GROUP BY display_name,port";
    } else {

        $query = "
        SELECT display_name, port
        FROM agents, agent_groups, server_groups
        WHERE server_groups.server_group like '$parentName%'
        AND agent_groups.agentid = agents.id
        AND server_groups.id = agent_groups.groupid
        AND agents.status != 'INACTIVE'
        GROUP BY agents.display_name, agents.port";

    }

    return run_query($query);

}
/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_hosts
// Similar to get_agent_for_parent except it does not include the port number in the return
//

function get_all_hosts($parentName) {

    $query = "
    SELECT display_name 
    FROM agents, agent_groups, server_groups
    WHERE server_groups.server_group like '$parentName%'
        AND agent_groups.agentid = agents.id
        AND server_groups.id = agent_groups.groupid
    GROUP BY agents.display_name, agents.port";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_hosts_for_user
//

function get_all_hosts_for_user($userId) {

    // We need to break down that into several query
    // The first query will give us the agentid and groupid of the user

    $query = "
        SELECT rights.agentid, rights.groupid from  people_groups, rights
        WHERE people_groups.userid = '$userId'
            AND   people_groups.groupid = rights.Usergroupid";

    $result = run_query($query);
    if (preg_grep("/-1/", $result)) {
        // We need to get all servers
        $query = "
        SELECT display_name, port
        FROM agents
        WHERE status != 'INACTIVE'
        GROUP BY display_name";
        return run_query($query);
    }

    // If we don't have a -1, we need to loop and get the associated data
    $totalHost = array();
        foreach ($result as $indResult) {
        $indResult = str_replace ("___", "\M", $indResult);
        $indResultArray = explode ("\M", $indResult);
            $agentId = $indResultArray[0];
        $groupId = $indResultArray[1];
        if ($agentId != "") {
            $query = "select display_name, port from agents
                  where id = '$agentId'
                  AND status != 'INACTIVE'";
                $hostname = run_query($query);
            $totalHost = array_merge($totalHost, $hostname);
        }
        if ($groupId != "") {
            // We need to get the group name and then get all hosts under it
            $query = "
                SELECT server_group
                FROM server_groups
                WHERE id = $groupId";
            $groupName = run_query($query);
            $groupName = $groupName[0];

            $query = "
            SELECT display_name, port from agents, agent_groups, server_groups
            WHERE server_groups.server_group LIKE '$groupName%'
                AND agent_groups.agentid = agents.id
                AND server_groups.id = agent_groups.groupid
                AND agents.status != 'INACTIVE'
            GROUP BY agents.display_name";

            $agentForGroup = run_query($query);
            $totalHost = array_merge($totalHost, $agentForGroup);
        }
    }
    sort($totalHost);
    $totalHost = array_unique($totalHost);
    return $totalHost;

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_hosts_with_process
//

function get_all_hosts_with_appClass($appClass) {

    $query = "SELECT 
                display_name, 
                port 
              FROM 
                agents,
                thresholds 
              WHERE 
                agents.id=thresholds.agentid 
                AND application_class='$appClass'
              GROUP by display_name";
              
    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_groups_for_user
//

function get_all_groups_for_user($userId) {


    // Let's determine if the user have a -1

    $query = "
        SELECT rights.groupid
        FROM  people_groups, rights
        WHERE people_groups.userid = '$userId'
           AND   people_groups.groupid = rights.Usergroupid";

    $result = run_query($query);
    if (preg_grep ("/-1/", $result)) {

        $query = "
        SELECT server_group
        FROM server_groups
        WHERE server_group LIKE '%' group by server_group";

        return run_query($query);
    }



    $allGroups = array();
    $query = "
        SELECT server_group
        FROM people_groups, rights, server_groups
        WHERE people_groups.userid = '$userId'
            AND   people_groups.groupid = rights.Usergroupid
            AND rights.groupid = server_groups.id
        GROUP BY server_group";

        // We need to get all the subgroups
    $groupRight = run_query($query);
    foreach ($groupRight as $indGroup) {
        $query = "
            SELECT server_group
            FROM server_groups
            WHERE server_group like '$indGroup%'";

        $groupForGroup = run_query($query);
        $allGroups = array_merge($allGroups, $groupForGroup);
    }
    sort($allGroups);
    $allGroups = array_unique($allGroups);
    return $allGroups;

}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_report
// Description: It will call the appropriate function to run the selected report
// Reports that have a reportType of 1-10 are agent reports
// Reports that have a reportType of 11-20 are PCM reports

function run_report($reportType, $totalAgentsToProcess, $filterList,$dataPointTime,$filterAppClassSelect) {

    global $REPORT_DIR;
    set_time_limit( 120 );

    $startTime = time();
    $reportData = "";
    if ($reportType < 10) {
        $numberOfHosts = count($totalAgentsToProcess);
        echo "<BR>";
        if (!$numberOfHosts) {
            echo "No Hosts Selected, please select at least 1 host <BR>";
            return;
        }
    }

    // Agent reports
    if ($reportType == 1) {
    $reportData = run_threshold_report ($totalAgentsToProcess, $filterList);
    }
    elseif ($reportType == 2) {
        $reportData = run_alertable_parameter($totalAgentsToProcess, $filterList);
    }
    elseif ($reportType == 3) {
        $reportData = run_out_of_compliance ($totalAgentsToProcess);
    }
    elseif ($reportType == 4) {
        $reportData = run_agent_os($totalAgentsToProcess);
    }
    elseif ($reportType == 5) {
        $reportData = run_sanity_check($totalAgentsToProcess);
    }
    elseif ($reportType == 6) {
    if (($dataPointTime == "") || (!is_numeric($dataPointTime))) {
        echo "Data point not numeric or not entered";
        return;
    }
        $reportData = run_data_point($totalAgentsToProcess, $dataPointTime * 60, $filterAppClassSelect);
    }

    // PCM reports
    elseif ($reportType == 10) {
        run_pcm_dups($filterList);
    }
    elseif ($reportType == 11) {
        ?>
        <h2>PCM Agent/Group relationship</h2>
        </h4>Feature under construction </h4>
        <?php
        // run_pcm_folder_report();
    }
    elseif ($reportType == 12) {
        run_pcm_hosts();
    }
    elseif ($reportType == 13) {
        run_pam_stats();
    }

    // This should never happen in production
    else {
        ?>
        <h2>Report Type <?php print $reportType; ?> not defined </h2>
        <?php
    }

    // Save report in CSV for download
    $reportDataArray = explode ("\n", $reportData);
    if (count($reportDataArray) > 1) {
        $userId = $_SESSION['user'];
    if ($userId == "") {
        $guestNumb = rand(0,10);
        $userId = "GUEST-$guestNumb";
    }
    $fp = fopen("$REPORT_DIR/user-Report-$userId.csv", "w");  // erase textdata.txt if it exists!!
    fwrite($fp,"$reportData\n");
    fclose($fp);
    ?>
    <br>
    <br>
    <fieldset class="inset">
        <legend>Export Data</legend>
        <?
            echo "<p>To Export data to CSV Right click on <a href=\"$REPORT_DIR/user-Report-$userId.csv\">Export Data</a> and select \"Save Target As...\" to save the file to your computer. )</p>"
        ?>
    </fieldset>
    <?
    }
    elseif ($reportType < 10) {
        echo "<p>No data available for the selected hosts</p>";
    }
    $endTime = time() - $startTime;
    echo "<p>Time to run this report: $endTime seconds<br>";
}


/////////////////////////////////////////////////////////////////////////////////
// Function: run_pcm_hosts
//
//
function run_pam_stats() {

    $totalQuery = array();
    $query1 = "SELECT count(*) FROM people where status != 0 \M Number of users";
    $query2 = "SELECT count(*) FROM user_groups \M Number of user groups";
    $query3 = "SELECT count(*) FROM agents \M Number Of agents";
    $query4 = "SELECT count(*) FROM server_groups \M Number of server groups";
    $query5 = "SELECT count(DISTINCT agentid) FROM thresholds \M Number of reporting agents";
    $query6 = "SELECT count(*) FROM rulesets WHERE ruleset LIKE '%cfg' \M Number of rulesets";
    $query7 = "SELECT count(*) FROM rules \M Number of rules";
    $query8 = "SELECT count(*) FROM categories \M Number of categories";
    $query9 = "SELECT count(*) FROM categories WHERE type = 1 \M Number of custom categories";
    $query10 = "SELECT count(*) FROM change_requests WHERE status = 0 \M Number of change requests in progress";
    $query11 = "SELECT count(*) FROM change_requests WHERE status = 1 \M Number of change requests pending approval";
    $query12 = "SELECT count(*) FROM change_requests WHERE status = 2 \M Number of change requests approved";
    $query13 = "SELECT count(*) FROM change_requests WHERE status = 3 \M Number of change requests rejected";

    array_push($totalQuery, $query1, $query2,$query3, $query4, $query5, $query6, $query7, $query8,$query9,$query10,$query11,$query12,$query13);
?>
    <br><br><br>
    <fieldset>
    <legend>Statistics</legend>
    <TABLE class="report" >
    <TR><TH>LEGEND</TH><TH>COUNT</TH></TR>

<?
    foreach ($totalQuery as $indQuery) {
        $indQueryArray = explode ("\M", $indQuery);
        $query = $indQueryArray[0];
        $description = $indQueryArray[1];
        $result = run_query($query);
        $resultToDisplay = $result[0];
        ?>
        <TR>
            <TD><?php print $description; ?></TD>
            <TD><?php print $resultToDisplay; ?></TD>
        </TR>
        <?php
    }

?>
    </TABLE>
    </fieldset>
<?
}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_pcm_hosts
//
//
function run_pcm_hosts() {

    $query = "
        SELECT agents.id, display_name, hostname, port
        FROM agents,
             actual_config
        WHERE status = 'INACTIVE'
              AND agents.id=actual_config.agentid
              AND actual_config.agentid is not NULL
        GROUP BY hostname, port";

    $result = run_query($query);

    ?>
    <h2>Rogue Agents</h2>
    <fieldset>
    <legend>PATROL Agents Without Management (<?php print howmany(count($result),"host","hosts"); ?>)
    </legend>
    <br/>
    <TABLE class="report" >
    <TR><TH>DISPLAY NAME</TH><TH>HOST NAME</TH><TH>PORT</TH><TH>REMOVE AGENT FROM ROGUE LIST</TH></TR>
    <?

    foreach ($result as $value) {
        $value = str_replace ("___", "\M", $value);
        $valueArray = explode ("\M", $value);
        echo "<TR><TD>$valueArray[1]</TD><TD>$valueArray[2]</TD><TD>$valueArray[3]</TD><TD><a href=\"javascript:rogue('$valueArray[0]');\"</a>Remove</TD></TR>";
    }
    ?>
    </TABLE></fieldset>
    <BR><BR><BR><BR><BR>
    <?
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_host_for_where_statement
//
//
function get_host_for_where_statement($totalAgentsToProcess) {

    $whereField = "";
    foreach ($totalAgentsToProcess as $hostField) {
        $hostFiled = str_replace ("___", " ", $hostField);
            $hostFiledArray = explode (" ", $hostFiled);

        $hostName = $hostFiledArray[0];
        $hostPort = $hostFiledArray[1];
        if ($whereField != "") {
            $whereField = $whereField . " or (hostname = '$hostName' and port = '$hostPort') \n";
        }
        else {
            $whereField = "((hostname = '$hostName' and port = $hostPort)";
        }

    }
    if ($whereField != "") {
        $whereField = $whereField . ")";
    }
    return $whereField;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_pcm_dups()
// Description: This will provide duplicate rules that might be in PCM
//          It will check 2 level. 1- The ruleset level - Any ruleset that have duplicate rules
//                     2- At the applyOnNew for group level

function run_pcm_dups($filterList) {

    // Creating a temporary table for speed purposes

    $cmd = "DROP TABLE IF EXISTS dupes";
    run_sql_cmd($cmd);

    $cmd = "
        CREATE TEMPORARY TABLE dupes (rsid INT, ruleset TEXT, variable TEXT BINARY)
        AS
        SELECT
            rulesets.id rsid,
            ruleset,
            variable
        FROM
            rules,
            rulesets
        WHERE
            rulesets.id = rules.rulesetid
            AND rules.operation = 'REPLACE'
        GROUP BY
            ruleset,
            variable
        HAVING  count(*) > 1";
    run_sql_cmd($cmd);

    $query = "
        SELECT
            ruleset,
            rules.variable,
            operation,
            value
        FROM
            rules,
            dupes
        WHERE dupes.rsid = rules.rulesetid
            AND dupes.variable = rules.variable";

    $result = run_query($query);


    // Strip out unwanted rules based on filter
    if ($filterList != "") {
        $result = preg_grep ("/$filterList/", $result, PREG_GREP_INVERT);
    }

    // Remove temporary table
    $cmd = "DROP TABLE IF EXISTS dupes";
    run_sql_cmd($cmd);


    //  Report output starts here
    ?>
    <h2>Duplicate Rule Report</h2>
    <fieldset class="inset">
        <legend>Duplicate Rules in the Same Ruleset</legend>
        <br/>
        <?
        foreach ($result as $value) {
            $value = str_replace ("___", "\M", $value);
            $valueArray = explode ("\M", $value);


            // New ruleset starts here
            if ($rulesetName != $valueArray[0]) {

                //  Close previous fieldsets and tables
                if ($rulesetName != "") {
                    ?>
                    </TABLE>
                    <BR/>
                    </fieldset>
                    <BR><BR>
                    <?
                }

                // Format ruleset name for display
                $disRuleset = str_replace (".cfg", "", $valueArray[0]);
                $disRuleset = str_replace (".", "/", $disRuleset);

                // New fieldset starts here
                ?>
                <fieldset class="inset-light">
                    <h4><?php print $disRuleset;?></h4>

                    <TABLE class="report">
                    <TR>
                        <TH>DUPLICATE RULES</TH>
                        <TH>OPERATION</TH>
                        <TH>VALUE</TH>
                    </TR>
                    <?
            }
            $rulesetName = $valueArray[0];
            ?>
                    <TR>
                        <TD class="pconfig">
                            <?php print $valueArray[1]; ?>
                        </TD><TD class="pconfig">
                            <?php print $valueArray[2]; ?>
                        </TD><TD class="pconfig">
                            <?php print $valueArray[3]; ?>
                        </TD>
                    </TR>
            <?php
        }
        ?>
        </TABLE>
        <BR/>
        </fieldset>
    </fieldset>
    <BR/><BR/><BR/><BR/><BR/>



    <fieldset class="inset">
        <legend>Duplicate Rules by Server Group</legend>
        <br/>
        <?

        $cmd = "DROP TABLE IF EXISTS dupes";
        run_sql_cmd($cmd);

        $cmd = "create TEMPORARY table dupes (gid int, server_group text, rsid int, ruleset text, variable text binary) as
        SELECT
            server_groups.id gid,
            server_groups.server_group,
            rulesets.id rsid,
            rulesets.ruleset,
            variable
        FROM
            rules,
            rulesets,
            ruleset_groups,
            server_groups
        where
            rulesets.id = rules.rulesetid
        and ruleset_groups.rulesetid = rulesets.id
        and server_groups.id = ruleset_groups.groupid
        and     rules.operation = 'REPLACE'
        GROUP BY
            server_group,
            variable
        HAVING  count(*)>1;";
        run_sql_cmd($cmd);

        $query = "select dupes.server_group, rulesets.ruleset, rules.variable, operation, value from dupes, rules, rulesets, ruleset_groups, server_groups
        where   dupes.variable = rules.variable
        and dupes.gid = server_groups.id
        and server_groups.id = ruleset_groups.groupid
        and ruleset_groups.rulesetid = rulesets.id
        and rulesets.id = rules.rulesetid
        order by server_group, variable";

        $result = run_query($query);

        if ($filterList != "") {
            $result = preg_grep ("/$filterList/", $result, PREG_GREP_INVERT);
        }


        // DROP TABLE IF EXISTS dupes;

        $cmd = "DROP TABLE IF EXISTS dupes";
        run_sql_cmd($cmd);

        # $result = run_sql_cmd($cmd);
        foreach ($result as $value) {
            $value = str_replace ("___", "\M", $value);
            $valueArray = explode ("\M", $value);
            if ($serverGroup != $valueArray[0]) {
                if ($serverGroup != "") {
                    ?>
                    </TABLE>
                    <br/>
                    </fieldset>
                    <BR/><BR/>
                    <?
                }

                $serverGroup = $valueArray[0];
                ?>
                <fieldset class="inset-light">
                <h4><? print $serverGroup; ?></h4>
                <br/>
                <TABLE class="report">
                <TR>
                    <TH>RULESET</TH>
                    <TH>VARIABLE</TH>
                    <TH>OPERATION</TH>
                    <TH>VALUE</TH>
                </TR>
                <?
            }
            ?>
            <TR>
                <TD class="pconfig">
                    <?php print $valueArray[1]; ?>
                </TD><TD class="pconfig">
                    <?php print $valueArray[2]; ?>
                </TD><TD class="pconfig">
                    <?php print $valueArray[3]; ?>
                </TD><TD class="pconfig">
                    <?php print $valueArray[4]; ?>
                </TD>
            </TR>
            <?php
        }
        ?>
        </TABLE>
        <br/>
        </fieldset>

    </fieldset>
    <?
}


/////////////////////////////////////////////////////////////////////////////////
// Function: translate_threshold_to_english
//
//
function translate_threshold_to_english($thresholdLine) {

    $statement = "";
    $value = str_replace("___", "\M", $thresholdLine);
    $valueArray = explode ("\M", $value);
    $appClass = $valueArray[2];
    $param    = $valueArray[3];
    $borderActive = $valueArray[4];
    if ($borderActive) {
        $Min = $valueArray[5];
        $Max = $valueArray[6];
        $trigger = $valueArray[7];
        $Occur = $valueArray[8];
        $State = $valueArray[9];
        if ($State == 1) {
            if ($Max == $Min) {
                $statement = $statement . "WARNING if value is not equal to $Max ";
            }
            else {
                $statement = $statement . "WARNING if value ABOVE $Max or BELOW $Min ";
            }
        }
        elseif ($State == 2) {
            if ($Max == $Min) {
                $statement = $statement . "ALARM if value is not equal to $Max ";
            }
            else {
                $statement = $statement . "ALARM if value ABOVE $Max or BELOW $Min ";
            }
        }
        if ($Occur) {
            $statement = $statement . "after $Occur cycles <BR>";
        }
        else {
            $statement = $statement . "<BR>";
        }
    }
    $alarm1Active = $valueArray[10];
    if ($alarm1Active) {
        $Min = $valueArray[11];
        $Max = $valueArray[12];
        $trigger = $valueArray[13];
        $Occur = $valueArray[14];
        $State = $valueArray[15];
        if ($State == 1) {
            if ($Max == $Min) {
                $statement = $statement . "WARNING if value is equal to $Max ";
            }
            else {
        $statement = $statement . "WARNING if value ABOVE $Min or BELOW $Max ";

            }
        }
        elseif ($State == 2) {
            if ($Max == $Min) {
                $statement = $statement . "ALARM if value is equal to $Max ";
            }
            else {
        $statement = $statement . "ALARM if value ABOVE $Min or BELOW $Max ";
            }
        }
        if ($Occur) {
            $statement = $statement . "after $Occur cycles <BR>";
        }
        else {
            $statement = $statement . "<BR>";
        }
    }
    $alarm2Active = $valueArray[16];
    if ($alarm2Active) {
        $Min = $valueArray[17];
        $Max = $valueArray[18];
        $trigger = $valueArray[19];
        $Occur = $valueArray[20];
        $State = $valueArray[21];
        if ($State == 1) {
            if ($Max == $Min) {
                $statement = $statement . "WARNING if value is equal to $Max ";
            }
            else {
        $statement = $statement . "WARNING if value ABOVE $Min or BELOW $Max ";
            }
        }
        elseif ($State == 2) {
            if ($Max == $Min) {
                $statement = $statement . "ALARM if value is equal to $Max ";
            }
            else {
                $statement = $statement . "ALARM if value ABOVE $Min or BELOW $Max ";
            }
        }
        if ($Occur) {
            $statement = $statement . "after $Occur cycles <BR>";
        }
        else {
            $statement = $statement . "<BR>";
        }
    }
    // Check to see if the parameter is active
    if ($valueArray[26] == "0") {
        $statement = $statement . "Parameter is disabled <BR>";
    }
    else {
            if ((!$borderActive) && (!$alarmActive) && (!$alarm2Active)) {
                   $statement = $statement . "No thresholds defined <BR>";
            }
            if ((is_numeric($valueArray[25]) && ($valueArray[25] != "") && ($valueArray[25] > 0))) {
            $pollTime = round ($valueArray[25]/60, 2);
            $statement = $statement . "Cycle time is $pollTime minutes";
        }
        elseif ($valueArray[25] != "UNDEFINED") {
            $statement = $statement . "Can't determine cycle time";
        }
    }
    return $statement;

}


/////////////////////////////////////////////////////////////////////////////////
// Function: convert_time
//
//
function convert_time($startTime) {

    $hours = $startTime/3600;
    $hoursFloor = floor($hours);
    $remainingMin = $hours - $hoursFloor;
    $min = $remainingMin * 60;
    $minFloor = floor($min);
    $seconds = $min - $minFloor;
    if ($hoursFloor < 10) {
        $hoursFloor = "0" . $hoursFloor;
    }
    if ($minFloor < 10) {
        $minFloor = "0" . $minFloor;
    }
    $returnValue = "$hoursFloor:$minFloor";
    return $returnValue;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: translate_Blackout_to_english
//
//
function translate_Blackout_to_english($blackoutLine) {

    $blackoutLine = str_replace ("|", ",", $blackoutLine);
    $blackoutLineArray = explode (",", $blackoutLine);

    foreach ($blackoutLineArray as $indBlackout) {
        $indBlackout = explode (" ", $indBlackout);
        $day = $indBlackout[0];
        $startTime = $indBlackout[1];
        $endTime = $indBlackout[2];
        $startTime = convert_time($startTime);
        $endTime = convert_time($endTime);
        $returnData = $returnData . $day . " " . $startTime . "--" . $endTime . "<br>";
    }

    return $returnData;
}
    
/////////////////////////////////////////////////////////////////////////////////
// Function: display_report
//
//
function display_report($report) {


    $reportData = "Hostname, Application Class, Instance, Parameter, Thresholds, Alarm Email, Warning Email, Blackout Period\n";

    foreach ($report as $value) {
        $statement = "";
        $value = str_replace("___", "\M", $value);
        $valueArray = explode ("\M", $value);
        if (($hostName != $valueArray[0]) || ($port != $valueArray[1])) {
            if ($hostName != "") {
                ?>
                </TABLE></fieldset>

                <BR>
                <?
            }
            ?>
            <fieldset class="inset">
            <legend>
            <?
            echo "$valueArray[0] -- $valueArray[1]";
            ?>
            </legend>
            <br/>
            <TABLE class="pconfig">
            <TR><TH style = "width:20px;">Application Class</TH><TH>Instance</TH><TH>Parameter</TH><TH WIDTH=1000>Thresholds</TH><TH>Alarm Email</TH><TH>Warning Email</TH><TH>Blackout Periods</TH></TR>
            <?
        }
        $hostName = $valueArray[0];
        $port = $valueArray[1];
        $appClass = $valueArray[2];
        $param    = $valueArray[3];
        $borderActive = $valueArray[4];
        if ($borderActive) {
            $Min = $valueArray[5];
            $Max = $valueArray[6];
            $trigger = $valueArray[7];
            $Occur = $valueArray[8];
            $State = $valueArray[9];
            if ($State == 1) {
                if ($Max == $Min) {
                    $statement = $statement . "WARNING if value is not equal to $Max ";
                }
                else {
                    $statement = $statement . "WARNING if value is above $Max or below $Min ";
                }
            }
            elseif ($State == 2) {
                if ($Max == $Min) {
                    $statement = $statement . "ALARM if value is not equal to $Max ";
                }
                else {
                    $statement = $statement . "ALARM if value is above $Max or below $Min ";
                }
            }
            if ($Occur) {
                $statement = $statement . "after $Occur cycles <BR>";
            }
            else {
                $statement = $statement . "<BR>";
            }
        }
        $alarm1Active = $valueArray[10];
        if ($alarm1Active) {
            $Min = $valueArray[11];
            $Max = $valueArray[12];
            $trigger = $valueArray[13];
            $Occur = $valueArray[14];
            $State = $valueArray[15];
            if ($State == 1) {
                if ($Max == $Min) {
                    $statement = $statement . "WARNING if value is equal to $Max ";
                }
                else {
                    $statement = $statement . "WARNING if value is between $Min and $Max ";
                }

            }
            elseif ($State == 2) {
                if ($Max == $Min) {
                    $statement = $statement . "ALARM if value is equal to $Max ";
                }
                else {
                    $statement = $statement . "ALARM if value is between $Min and $Max ";
                }
            }
            if ($Occur) {
                $statement = $statement . "after $Occur cycles <BR>";
            }
            else {
                $statement = $statement . "<BR>";
            }
        }
        $alarm2Active = $valueArray[16];
        if ($alarm2Active) {
            $Min = $valueArray[17];
            $Max = $valueArray[18];
            $trigger = $valueArray[19];
            $Occur = $valueArray[20];
            $State = $valueArray[21];
            if ($State == 1) {
                if ($Max == $Min) {
                    $statement = $statement . "WARNING if value is equal to $Max ";
                }
                else {
                    $statement = $statement . "WARNING if value is between $Min and $Max ";
                }
            }
            elseif ($State == 2) {
                if ($Max == $Min) {
                    $statement = $statement . "ALARM if value is equal to $Max ";
                }
                else {
                    $statement = $statement . "ALARM if value is between $Min and $Max ";
                }
            }
            if ($Occur) {
                $statement = $statement . "after $Occur cycles <BR>";
            }
            else {
                $statement = $statement . "<BR>";
            }
        }

        if ($valueArray[22] == "") {
            $valueArray[22] = "Not Defined";
        }
        if ($valueArray[23] == "") {
            $valueArray[23] = "Not Defined";
        }
        if ($valueArray[24] == "") {
            $valueArray[24] = "Not Defined";
        }

        $pollTime = $valueArray[25];

        if ((is_numeric($pollTime) && ($pollTime != "-1") && ($pollTime != "0"))) {
            $pollTime = round ($valueArray[25]/60, 2);
            $statement = $statement . "Cycle time is $pollTime minutes";
        }
        else {
            $statement = $statement . "Can't determine cycle time";
        }
        
        $blackoutPeriod = $valueArray[29];
        if ($blackoutPeriod == "") {
            $blackoutPeriod = "NONE";
        }
        else {
            $blackoutPeriod = translate_Blackout_to_english($blackoutPeriod);
       }

        $instance = $valueArray[30];
        
    $reportData = $reportData . "$hostName,$appClass,$instance,$param,$statement,$valueArray[26],$valueArray[27],$blackoutPeriod\n";
    echo "<TR><TD style = \"width:20px;\">$appClass</TD><TD>$instance</TD><TD><a href=\"getDescription.php?paramName=$param&appClass=$appClass\" target=\"_blank\">$param</a></TD><TD NOWRAP>$statement</TD><TD>$valueArray[26]</TD><TD>$valueArray[27]</TD><TD NOWRAP>$blackoutPeriod</TD></TR>";
    
    }

    // Close the last table
    ?>
    </TABLE></fieldset>
    <?
    return $reportData;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_sanity_check
//
//
function run_sanity_check($totalAgentsToProcess) {

    $reportData = "Hostname,Port,Issue\n";


    $totalNumHosts =  count ($totalAgentsToProcess);

    $titleString = "Misconfigured Agent Report (".howmany ($totalNumHosts, "host","hosts")." queried)";
    ?>
    <h2><?php print $titleString; ?></h2>
    <?php

    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE a (agentid int) as
        select agentid from actual_config where variable = '/AgentSetup/preloadedKMs'
    and value != ''";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE b (agentid int) as
        select agentid from actual_config group by agentid";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE c (agentid int) as
        select b.agentid from b left outer join a on b.agentid = a.agentid
        where a.agentid is null";
    run_sql_cmd($cmd);

    $whereField = create_where_statement_for_all_hosts($totalAgentsToProcess);

    $query = "select display_name, port from c, agents
    where agents.id = c.agentid
    and $whereField
    group by display_name, port";

    $result = run_query($query);

    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    ?>
    <fieldset class="inset">
        <legend>Missing Preload Lists</legend>
        <h4>
        <?php
            $numMissing = count($result);
            if ($numMissing == 0) {
                ?>
                    All agents have valid preload lists.</h4>
                <?
            } else {
                print thisthese ($numMissing,"This ","These ")
                    . howmany   ($numMissing,"agent","agents")
                    . thisthese ($numMissing," does"," do")
                    . " not have any KMs preloaded:";
                ?>
                </h4>
                <TABLE class="report">
                <TR>
                    <TH>HOSTNAME</TH>
                    <TH>PORT</TH>
                </TR>
                <?

                foreach ($result as $value) {
                    $value = str_replace ("___", "\M", $value);
                    $valueArray = explode ("\M", $value);
                    $reportData = $reportData  . "$valueArray[0],$valueArray[1],Missing Preloaded List\n";
                    ?>
                    <TR>
                        <TD><?php print $valueArray[0]; ?></TD>
                        <TD><?php print $valueArray[1]; ?></TD>
                    </TR>
                    <?php
                }
                ?>

                </TABLE>

                <?php
            }
        ?>

    </fieldset>
    <BR><BR>
    <?
    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE a (agentid int) AS
        SELECT agentid FROM actual_config WHERE variable = '/AS/EVENTSPRING/NOTIFICATION_SERVER1'";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE b (agentid int) AS
        SELECT agentid FROM actual_config GROUP BY agentid";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE c (agentid int) AS
        SELECT b.agentid
        FROM b LEFT OUTER JOIN a ON b.agentid = a.agentid
        WHERE a.agentid IS NULL";
    run_sql_cmd($cmd);


    $query = "SELECT display_name, port
    FROM c, agents
    WHERE agents.id = c.agentid
    and $whereField
    GROUP BY display_name, port";

    $result = run_query($query);

    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    ?>
    <fieldset class="inset">
        <legend>Missing Notification Pointers</legend>

        <h4>
        <?php
            $numMissing = count($result);
            if ($numMissing == 0) {
                ?>
                    All agents are configured with Notification pointers.</h4>
                <?
            } else {
                print thisthese ($numMissing,"This ","These ")
                    . howmany   ($numMissing,"agent","agents")
                    . thisthese ($numMissing," does"," do")
                    . " not have a primary Notification pointer:";
                ?>
                </h4>

                <TABLE class="report">
                <TR>
                    <TH>HOSTNAME</TH>
                    <TH>PORT</TH>
                </TR>

                <?
                foreach ($result as $value) {
                    $value = str_replace ("___", "\M", $value);
                    $valueArray = explode ("\M", $value);
                    $reportData = $reportData  . "$valueArray[0],$valueArray[1],Missing Notification Server Pointer\n";
                    ?>
                    <TR>
                        <TD><?php print $valueArray[0]; ?></TD>
                        <TD><?php print $valueArray[1]; ?></TD>
                    </TR>
                    <?php
                }
                ?>
                </TABLE>
            <?php
            }
        ?>
    </fieldset>
    <BR/><BR/>
    <?
    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE a (agentid int) AS
        SELECT agentid FROM actual_config WHERE variable = '/AgentSetup/rtServers'";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE b (agentid int) AS
        SELECT agentid FROM actual_config GROUP BY agentid";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE c (agentid int) AS
        SELECT b.agentid
        FROM b LEFT OUTER JOIN a ON b.agentid = a.agentid
        WHERE a.agentid IS NULL";
    run_sql_cmd($cmd);


    $query = "SELECT display_name, port
    FROM c, agents
    WHERE agents.id = c.agentid
    and $whereField
    GROUP BY display_name, port";

    $result = run_query($query);

    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    ?>
    <fieldset class="inset">
        <legend>Missing RT Pointers</legend>

        <h4>
        <?php
            $numMissing = count($result);
            if ($numMissing == 0) {
                ?>
                    All agents are configured with RT Server pointers.</h4>
                <?
            } else {
                print thisthese ($numMissing,"This ","These ")
                    . howmany   ($numMissing,"agent","agents")
                    . thisthese ($numMissing," does"," do")
                    . " not have a primary RT Server pointer:";
                ?>
                </h4>

                <TABLE class="report">
                <TR>
                    <TH>HOSTNAME</TH>
                    <TH>PORT</TH>
                </TR>

                <?
                foreach ($result as $value) {
                    $value = str_replace ("___", "\M", $value);
                    $valueArray = explode ("\M", $value);
                    $reportData = $reportData  . "$valueArray[0],$valueArray[1],Missing Notification Server Pointer\n";
                    ?>
                    <TR>
                        <TD><?php print $valueArray[0]; ?></TD>
                        <TD><?php print $valueArray[1]; ?></TD>
                    </TR>
                    <?php
                }
                ?>
                </TABLE>
            <?php
            }
        ?>
    </fieldset>
    <BR/><BR/>
    <?
    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE a (agentid int) AS
        SELECT agentid FROM actual_config WHERE variable = '/AgentSetup/historyRetentionPeriod'
    and value != 1";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE b (agentid int) AS
        SELECT agentid FROM actual_config GROUP BY agentid";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE c (agentid int) AS
        SELECT b.agentid
        FROM b LEFT OUTER JOIN a ON b.agentid = a.agentid
        WHERE a.agentid IS NULL";
    run_sql_cmd($cmd);


    $query = "SELECT display_name, port
    FROM c, agents
    WHERE agents.id = c.agentid
    and $whereField
    GROUP BY display_name, port";

    $result = run_query($query);

    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    ?>
    <fieldset class="inset">
        <legend>Missing History Retention</legend>

        <h4>
        <?php
            $numMissing = count($result);
            if ($numMissing == 0) {
                ?>
                    All agents have a history retention variable.</h4>
                <?
            } else {
                print thisthese ($numMissing,"This ","These ")
                    . howmany   ($numMissing,"agent","agents")
                    . thisthese ($numMissing," does"," do")
                    . " not have history retention variable:";
                ?>
                </h4>

                <TABLE class="report">
                <TR>
                    <TH>HOSTNAME</TH>
                    <TH>PORT</TH>
                </TR>

                <?
                foreach ($result as $value) {
                    $value = str_replace ("___", "\M", $value);
                    $valueArray = explode ("\M", $value);
                    $reportData = $reportData  . "$valueArray[0],$valueArray[1],Missing Notification Server Pointer\n";
                    ?>
                    <TR>
                        <TD><?php print $valueArray[0]; ?></TD>
                        <TD><?php print $valueArray[1]; ?></TD>
                    </TR>
                    <?php
                }
                ?>
                </TABLE>
            <?php
            }
        ?>
    </fieldset>
    <BR/><BR/>
    <?
    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);

    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE a (agentid int) AS
        SELECT DISTINCT agentid FROM actual_config WHERE variable like '%PARAM_SETTINGS/THRESHOLDS/%'";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE b (agentid int) AS
        SELECT DISTINCT agentid FROM actual_config WHERE variable like '%___tuning___%'";
    run_sql_cmd($cmd);

    $cmd = "CREATE TEMPORARY TABLE c (agentid int) AS
        SELECT b.agentid
        FROM a, b
        WHERE a.agentid = b.agentid";
    run_sql_cmd($cmd);


    $query = "SELECT display_name, port
    FROM c, agents
    WHERE agents.id = c.agentid
    and $whereField
    GROUP BY display_name, port";

    $result = run_query($query);

    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS b";
    run_sql_cmd($cmd);
    $cmd = "DROP TABLE IF EXISTS c";
    run_sql_cmd($cmd);

    ?>
    <fieldset class="inset">
        <legend>Thresholds Conflict</legend>

        <h4>
        <?php
            $numMissing = count($result);
            if ($numMissing == 0) {
                ?>
                    No agents have a potential threshold conflict.</h4>
                <?
            } else {
                print thisthese ($numMissing,"This ","These ")
                    . howmany   ($numMissing,"agent","agents")
                    . thisthese ($numMissing," may"," may")
                    . " have a potential conflict in their threshold definition. ___tunning__ and Event Management threshold found:";
                ?>
                </h4>

                <TABLE class="report">
                <TR>
                    <TH>HOSTNAME</TH>
                    <TH>PORT</TH>
                </TR>

                <?
                foreach ($result as $value) {
                    $value = str_replace ("___", "\M", $value);
                    $valueArray = explode ("\M", $value);
                    $reportData = $reportData  . "$valueArray[0],$valueArray[1],Threshold conflict\n";
                    ?>
                    <TR>
                        <TD><?php print $valueArray[0]; ?></TD>
                        <TD><?php print $valueArray[1]; ?></TD>
                    </TR>
                    <?php
                }
                ?>
                </TABLE>
            <?php
            }
        ?>
    </fieldset>
    <BR/><BR/>
    <?


    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);

    $cmd = "
    CREATE TEMPORARY TABLE a (agentid int) AS
    SELECT DISTINCT agentid FROM actual_config";
    # WHERE date_entered > DATE_SUB(NOW(), INTERVAL 24 HOUR)";

    run_sql_cmd($cmd);

    $query = "
    SELECT display_name, port
    FROM agents LEFT OUTER JOIN a ON a.agentId = agents.id
    WHERE agentid IS NULL
    and agents.status='ACTIVE'
    and $whereField
    GROUP BY display_name";

    $result = run_query($query);

    $cmd = "DROP TABLE IF EXISTS a";
    run_sql_cmd($cmd);
    ?>
    <fieldset class="inset">
        <legend>Inactive PATROL Agents</legend>

        <h4>
        <?php
            $numMissing = count($result);
            if ($numMissing == 0) {
                ?>
                    All known agents are actively reporting. </h4>
                <?php
            } else {
                print "No data has been received in the past 24 hours from "
                      . thisthese($numMissing,"this ","these ")
                      . howmany ($numMissing,"agent","agents")
                      . ":";
                ?>
                </h4>
                <TABLE class="report">
                <TR><TH>HOSTNAME</TH><TH>PORT</TH></TR>
                <?php

                foreach ($result as $value) {

                    $value = str_replace ("___", "\M", $value);
                    $valueArray = explode ("\M", $value);
                    $reportData = $reportData  . "$valueArray[0],$valueArray[1],Missing CSV";
                    ?>
                    <TR>
                        <TD><?php print $valueArray[0]; ?></TD>
                        <TD><?php print $valueArray[1]; ?></TD>
                    </TR>
                    <?php
                }
                ?>
                </TABLE>

                <?php
            }
        ?>
    </fieldset>
    <BR><BR>
    <?
    return $reportData;

}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_agent_os
//
//
function run_agent_os($totalAgentsToProcess) {

    $finalArrayAgent = array();
    $finalArrayOS = array();
    $whereField = create_where_statement_for_all_hosts($totalAgentsToProcess);

    #$query = "select a.value,b.value from agents, actual_config a, actual_config b
    #where $whereField
    #and     agents.id = a.agentid
    #and     agents.id = b.agentid
    #and     a.variable = '/AS/agentInfo'
    #and     b.variable = '/AgentSetup/preloadedKMs'";

    $query = "
    SELECT a.value
    FROM agents, actual_config a
    WHERE $whereField
        AND     agents.id = a.agentid
        AND     a.variable = '/AS/agentInfo'";

    $result = run_query($query);
    $hostsWithData = count($result);
    $totalNumHosts = count($totalAgentsToProcess);
    $hostsMissingData = $totalNumHosts - $hostsWithData;

    if ($hostsMissingData == 0)
    {
        $titleString = "Agent/OS Version Report (".howmany ($hostsWithData, "host","hosts").")";
    } else {
        $titleString = "Agent/OS Version Report (".howmany ($hostsWithData, "host","hosts").")";
        # $titleString = "Agent/OS Version Report (".howmany ($hostsWithData, "host","hosts")." [missing data for $hostsMissingData of ".howmany($totalNumHosts,"host","hosts")."])";
    }
    ?>
    <h2>
        <?php print $titleString; ?>
    </h2>
    <?php

    $finalArrayOS = array();
    $finalArrayAgent = array();

    if (count ($result)) {
    $reportData = "OS/Agent Version,Hostname,port,OS/Agent Version,EV Version\n";
        foreach ($result as $indLine) {
            $indLine = str_replace ("___", "\M", $indLine);
            $indLineArray = explode ("\M", $indLine);
            $indLine = $indLineArray[0];
            $preloaded = $indLineArray[1];
            $preloaded = str_replace (",", " ", $preloaded);
            $agentLineArray = explode (",", $indLine);
            $hostName     = $agentLineArray[0];
            $hostPort     = $agentLineArray[2];
            $agentVersion = $agentLineArray[4];
            $hostVersion  = $agentLineArray[5];
            $EventSpringVersion  = $agentLineArray[6];

            $newLineAgent = "$hostName,$hostPort,$hostVersion,$EventSpringVersion,$preloaded";
            $finalArrayAgent[$agentVersion] = $finalArrayAgent[$agentVersion] . "$newLineAgent" . "\n";

                $newLineOS = "$hostName,$hostPort,$agentVersion,$EventSpringVersion,$preloaded";
            $finalArrayOS[$hostVersion] =  $finalArrayOS[$hostVersion] . $newLineOS . "\n";
        }

    }
    else {
        echo "No Config Data <BR>";
    }


    // Now we need to echo the information
    ?>
    <h3>Summary Grouped by OS Version </h3>
    <?php
    foreach($finalArrayOS as $key => $value) {
        ?>
        <fieldset>
            <legend><?php print $key; ?></legend>

            <?php
            $totalEntry = explode ("\n", $value);
            $numberOfEntry = count ($totalEntry) - 1;
            ?>
            <h4>
                <?php print howmany ($numberOfEntry,"Agent","Agents"); ?>
            </h4>
            <TABLE class="report">
                <TR>
                    <TH>HOSTNAME</TH>
                    <TH>PORT</TH>
                    <TH>AGENT VERSION</TH>
                    <TH>EVENT MANGEMENT VERSION</TH>
                </TR>
            <?
        $totalEntry = array_unique($totalEntry);
            foreach ($totalEntry as $indEntry) {
                $indEntry = trim ($indEntry);
            if ($indEntry == "") {
                continue;
            }
                $indEntryArray = explode (",", $indEntry);
                $reportData = $reportData . "$key,$indEntryArray[0],$indEntryArray[1],$indEntryArray[2],$indEntryArray[3]\n";

                ?>
                <TR>
                    <TD><?php print $indEntryArray[0]; ?></TD>
                    <TD><?php print $indEntryArray[1]; ?></TD>
                    <TD><?php print $indEntryArray[2]; ?></TD>
                    <TD><?php print $indEntryArray[3]; ?></TD>
                </TR>
                <?php

            }
            ?>
            </TABLE>
        </fieldset>
    <br/>
    <br/>
        <?

    }

    ?>
    <h3>Summary Grouped by Agent Version </h3>
    <?php

    foreach($finalArrayAgent as $key => $value) {
        ?>
        <fieldset>
            <legend><?php print $key; ?></legend>

            <?php
            $totalEntry = explode ("\n", $value);
            $numberOfEntry = count ($totalEntry) - 1;
            ?>
            <h4>
                <?php print howmany ($numberOfEntry,"Agent","Agents"); ?>
            </h4>

            <TABLE class="report">
                <TR>
                    <TH>HOSTNAME</TH>
                    <TH>PORT</TH>
                    <TH>OS VERSION</TH>
                    <TH>EVENT MANGEMENT VERSION</TH>
                </TR>
            <?
            $totalEntry = array_unique($totalEntry);
                foreach ($totalEntry as $indEntry) {
                    $indEntry = trim ($indEntry);
                if ($indEntry == "") {
                    continue;
                }

                $indEntryArray = explode (",", $indEntry);
                $reportData = $reportData . "$key,$indEntryArray[0],$indEntryArray[1],$indEntryArray[2],$indEntryArray[3]\n";
                ?>
                <TR>
                    <TD><?php print $indEntryArray[0]; ?></TD>
                    <TD><?php print $indEntryArray[1]; ?></TD>
                    <TD><?php print $indEntryArray[2]; ?></TD>
                    <TD><?php print $indEntryArray[3]; ?></TD>
                </TR>
                <?php

            }
            ?>
            </TABLE>
    </fieldset>
    <br/>
    <br/>
        <?
    }
    return $reportData;

}


/////////////////////////////////////////////////////////////////////////////////
// Function: run_threshold_report
//
//
function run_threshold_report($totalAgentsToProcess, $filterList) {

    $whereField = create_where_statement_for_all_hosts($totalAgentsToProcess);

    $query = "
        SELECT
            display_name,
            port,
            Application_class,
            parameter,
            border_active,
            border_min,
            border_max,
            border_trigger,
            border_occur,
            border_state,
            alarm1_active,
            alarm1_min,
            alarm1_max,
            alarm1_trigger,
            alarm1_occur,
            alarm1_state,
            alarm2_active,
            alarm2_min,
            alarm2_max,
            alarm2_trigger,
            alarm2_occur,
            alarm2_state,
            msgTextAlarm,
            msgTextWarning,
            msgTextInformation,
            PollTime,
            emailalarm,
            emailwarning,
            emailinformation,
            blackout,
            instance
        FROM
            agents,
            thresholds
        WHERE $whereField
            AND agents.id = thresholds.agentid
            AND ((border_active != 0 AND border_state != 0)
            OR (alarm1_active != 0 AND alarm1_state != 0)
            OR (alarm2_active != 0 AND alarm2_state != 0))
        GROUP by display_name,
            application_class,
            instance,
            parameter";

    $result = run_query($query);

    if ($filterList != "") {
        $result = preg_grep ("/$filterList/", $result);
    }

    $query = "
        SELECT display_name, port
        FROM agents, thresholds
        WHERE $whereField
            AND agents.id = thresholds.agentid
            AND ((border_active != 0 AND border_state != 0)
            OR (alarm1_active != 0 AND alarm1_state != 0)
            OR (alarm2_active != 0 AND alarm2_state != 0))
        GROUP BY display_name, port";


    $numberOfHosts = run_query ($query);

    $hostsWithData =  count($numberOfHosts);
    $totalNumHosts =  count ($totalAgentsToProcess);
    $hostsMissingData = $totalNumHosts - $hostsWithData;

    if ($hostsMissingData == 0)
    {
        $titleString = "Threshold Report (".howmany ($hostsWithData, "host","hosts").")";
    } else {
        $titleString = "Threshold Report (".howmany ($hostsWithData, "host","hosts").")";
        # $titleString = "Threshold Report (".howmany ($hostsWithData, "host","hosts")." [missing data for $hostsMissingData of ".howmany($totalNumHosts,"host","hosts")."])";
    }
    ?>
    <h2><?php print $titleString; ?></h2>
    <?php

    return display_report($result);
}


/////////////////////////////////////////////////////////////////////////////////
// Function: run_alertable_parameter
//
//
function run_alertable_parameter($totalAgentsToProcess, $filterList) {


    $whereField = create_where_statement_for_all_hosts($totalAgentsToProcess);

    $query = "
    SELECT display_name, port, Application_class, parameter,
        border_active, border_min, border_max, border_trigger, border_occur, border_state,
        alarm1_active, alarm1_min, alarm1_max, alarm1_trigger, alarm1_occur, alarm1_state,
        alarm2_active, alarm2_min, alarm2_max, alarm2_trigger, alarm2_occur, alarm2_state,
        msgTextAlarm, msgTextWarning, msgTextInformation, Polltime, emailalarm, emailwarning, emailinformation
    FROM agents, thresholds
    WHERE $whereField
        AND agents.id = thresholds.agentid
        AND (arsAlarm != 0 OR arsWarning != 0)
        AND ((border_active != 0 AND border_state != 0)
        OR (alarm1_active != 0 AND alarm1_state != 0)
        OR (alarm2_active != 0 AND alarm2_state != 0))
    GROUP BY display_name, application_class, parameter";

    $result = run_query($query);

    if ($filterList != "") {
        $result = preg_grep ("/$filterList/", $result);
    }

    $query = "
    SELECT display_name
    FROM agents, thresholds
    WHERE $whereField
        AND agents.id = thresholds.agentid
        AND (arsAlarm != 0 OR arsWarning != 0)
        AND ((border_active != 0 AND border_state != 0)
        OR (alarm1_active != 0 AND alarm1_state != 0)
        OR (alarm2_active != 0 AND alarm2_state != 0))
    GROUP BY display_name";


    $numberOfHosts = run_query ($query);
    $hostsWithData =  count($numberOfHosts);
    $totalNumHosts =  count ($totalAgentsToProcess);
    $hostsMissingData = $totalNumHosts - $hostsWithData;

    if ($hostsMissingData == 0)
    {
        $titleString = "Alertable Parameters Report (".howmany ($hostsWithData, "host","hosts").")";
    } else {
        $titleString = "Alertable Parameters Report (".howmany ($hostsWithData, "host","hosts").")";
        # $titleString = "Alertable Parameters Report (".howmany ($hostsWithData, "host","hosts")." [missing data for $hostsMissingData of ".howmany($totalNumHosts,"host","hosts")."])";
    }
    ?>
    <h2><?php print $titleString; ?></h2>
    <?php


    return display_report($result);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_data_point
//
//
function run_data_point($totalAgentsToProcess, $dataPointTime,$filterAppClassSelect) {


    $whereField = create_where_statement_for_all_hosts($totalAgentsToProcess);
    $filterAppClassSelect = trim ($filterAppClassSelect, ",");
    $filterAppClassSelect = trim ($filterAppClassSelect);
    $filterAppClassSelect = explode (",",$filterAppClassSelect);

    $query = "
    SELECT display_name, port, Application_class, parameter,last_updated

    FROM agents, thresholds
    WHERE $whereField
        AND agents.id = thresholds.agentid
    AND  last_updated > $dataPointTime
    AND param_type != \"COLL\"
    GROUP BY display_name, port, last_updated DESC,application_class, parameter";

    $result = run_query($query);
    foreach ($filterAppClassSelect as $indAppClassFilter) {
        $indAppClassFilter = trim ($indAppClassFilter);
        if ($indAppClassFilter != "") {
            $result = preg_grep ("/__$indAppClassFilter" . "__/", $result, PREG_GREP_INVERT);
        }
    }

    $query = "
    SELECT display_name, port
    FROM agents, thresholds
    WHERE $whereField
        AND agents.id = thresholds.agentid
    GROUP BY display_name, port";


    $numberOfHosts = run_query ($query);
    $hostsWithData =  count($numberOfHosts);
    $totalNumHosts =  count ($totalAgentsToProcess);
    $hostsMissingData = $totalNumHosts - $hostsWithData;

    if ($hostsMissingData == 0) {
        $titleString = "Stale data points Report (".howmany ($hostsWithData, "host","hosts").")<br>";
    } else {
        $titleString = "Stale data points Report (".howmany ($hostsWithData, "host","hosts").")<br>";
        # $titleString = "Stale data points Report (".howmany ($hostsWithData, "host","hosts")." [missing data for $hostsMissingData of ".howmany($totalNumHosts,"host","hosts")."])<br>";
    }
    if (count($filterAppClassSelect)) {
        $fileterMessage = "Filtered Application Classes: " . implode(",", $filterAppClassSelect);
    }
    ?>
    <h2><?php print $titleString; ?></h2>
    <h3><?php print $fileterMessage; ?></h3>
    <?php


    $reportData = "Hostname,Port,Application_Class, Parameter, last_updated\n";

    foreach ($result as $value) {
        $value = str_replace("___", "\M", $value);
        $valueArray = explode ("\M", $value);
        if (($hostName != $valueArray[0]) || ($port != $valueArray[1])) {
            if ($hostName != "") {
                ?>
                </TABLE></fieldset>

                <BR>
                <?
            }
            ?>
            <fieldset class="inset">
            <legend>
            <?
            echo "$valueArray[0] -- $valueArray[1]";
            ?>
            </legend>
            <br/>
            <TABLE class="pconfig">
            <TR><TH style = "width:20px;">Application Class</TH><TH>Parameter</TH><TH WIDTH=1000>last updated (min)</TH></TR>
            <?
        }
        $hostName = $valueArray[0];
        $port = $valueArray[1];
        $appClass = $valueArray[2];
        $param    = $valueArray[3];
        $lastUpdated = $valueArray[4];
    $lastUpdated = bcdiv($lastUpdated, "60", "0");

        $reportData = $reportData . "$hostName,$port, $appClass,$param,$lastUpdated\n";
        echo "<TR><TD style = \"width:20px;\">$appClass</TD><TD><a href=\"getDescription.php?paramName=$param&appClass=$appClass\" target=\"_blank\">$param</a></TD><TD NOWRAP>$lastUpdated</TD></TR>";
    }

    // Close the last table
    ?>
    </TABLE></fieldset>
    <?
    return $reportData;

}

/////////////////////////////////////////////////////////////////////////////////
// Function: run_out_of_compliance
//
//
function run_out_of_compliance($totalAgentsToProcess) {


    #set_time_limit(120);
    $whereHosts = create_where_statement_for_all_hosts($totalAgentsToProcess);

    $cmd = "DROP TABLE IF EXISTS ooc1";
    run_sql_cmd($cmd);

    // We need to exclude that
    $cmd = "
    CREATE TABLE ooc1 AS (
        SELECT *
        FROM ooc
        WHERE variable != '/AS/EVENTSPRING/PARAM_SETTINGS/STATUSFLAGS/paramSettingsStatusFlag'
    )";
    run_sql_cmd($cmd);



    if (count($totalAgentsToProcess) < 500) {
    $query = "
            SELECT
                ga.agentid agentid,
                display_name,
                port,
                count(*) Mismatches
            FROM
                ooc1 ooc1
                LEFT OUTER JOIN goodagents ga
                ON ooc1.agentid = ga.agentid
            WHERE
                ga.agentid IS NOT NULL
                AND $whereHosts
            GROUP BY agentid
            ORDER BY display_name;";
    }
    else {
            $query = "
            SELECT
                ga.agentid agentid,
                display_name,
                port,
                count(*) Mismatches
            FROM
                ooc1 ooc1
                LEFT OUTER JOIN goodagents ga
                ON ooc1.agentid = ga.agentid
            WHERE  ga.agentid IS NOT NULL
            GROUP BY agentid
            ORDER BY display_name;";
    }

    $result = run_query($query);
    $numNonCompliant = count($result);
    $totalAgentsToProcess = count($totalAgentsToProcess);
    $percentage = round($numNonCompliant / $totalAgentsToProcess * 100);

    if ($numNonCompliant < 1) {
        $titleString = "All Hosts Are in Compliance";
    } else {
        $titleString = "Out of Compliance Report ($percentage% [$numNonCompliant of ".howmany($totalAgentsToProcess,"host","hosts")."])";
    }

    ?>
    <h3><?php print $titleString; ?></h3>

    <fieldset>
        <legend>Non-compliant servers</legend>
        <TABLE class="report">
            <TR><TH>HOSTNAME</TH><TH>MISMATCHED RULES</TH></TR>
            <?

            if (count($result)) {
            $reportData = "hostname, Mismatched Rules Count\n";
            }
            foreach ($result as $value) {
                $resultArray = explode ("___", $value);
            $reportData = $reportData . "$resultArray[1],$resultArray[3]\n";
            ?>
            <TR>
                <TD><a href="oocDetails.php?agentId=<?php
                    print "$resultArray[0]&hostName=$resultArray[1]&portNumber=$resultArray[2]";
                    ?>" target="_blank"><?php print "$resultArray[1]"; ?></a>
                </TD>
                <TD><a href="oocDetails.php?agentId=<?php
                    print "$resultArray[0]&hostName=$resultArray[1]&portNumber=$resultArray[2]";
                    ?>" target="_blank"><?php print "$resultArray[3]"; ?></a>
                </TD>
            </TR>
            <?php
            }
            ?>
        </TABLE>
    </fieldset>
    <?
    return $reportData;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: ooc_details
//
//
function ooc_details($agentId) {

    $query = "
        SELECT
            hostname,
            port,
            variable,
            rulevalue,
            actualvalue
        FROM ooc1 LEFT OUTER JOIN goodagents ga ON ooc1.agentid = ga.agentid
        WHERE ga.agentid IS NOT NULL
            AND ga.agentid = $agentId";

    return  run_query("$query");
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_parameter_description
//
//
function get_parameter_description($kmCategory, $paramName) {

    if ($paramName == "SELECT_ALL") {
        $query = "
            SELECT
                parameter,
                application_class,
                parameter_descriptions.description,
                parameter_descriptions.collector
            FROM
                categories,
                parameter_categories,
                parameter_descriptions
            WHERE category = '$kmCategory'
                AND categories.id = parameter_categories.categoryid
                AND parameter_categories.parameterid = parameter_descriptions.id
            GROUP BY application_class, parameter";
    }
    else {

        $query = "
            SELECT
                parameter,
                application_class,
                parameter_descriptions.description,
                parameter_descriptions.collector
            FROM
                categories,
                parameter_categories,
                parameter_descriptions
            WHERE category = '$kmCategory'
                AND categories.id = parameter_categories.categoryid
                AND parameter_categories.parameterid = parameter_descriptions.id
                AND parameter_descriptions.parameter = '$paramName'
            GROUP BY application_class, parameter";
    }

    $result = run_query($query);
    return $result;
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_collector
//
//
function get_collector($kmCategory, $paramName) {

        $query = "
            SELECT
                parameter_descriptions.collector
            FROM
                categories,
                parameter_categories,
                parameter_descriptions
            WHERE category = '$kmCategory'
                AND categories.id = parameter_categories.categoryid
                AND parameter_categories.parameterid = parameter_descriptions.id
        AND parameter_descriptions.parameter =  '$paramName'";

    return run_query($query);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_collector_alias
//
//
function get_collector_alias($kmCategory, $paramName) {

        $query = "
            SELECT
                parameter_descriptions.collector,
                parameter_descriptions.app_class_alias,
                parameter_descriptions.parameter_alias
            FROM
                categories,
                parameter_categories,
                parameter_descriptions
            WHERE category = '$kmCategory'
                AND categories.id = parameter_categories.categoryid
                AND parameter_categories.parameterid = parameter_descriptions.id
        AND parameter_descriptions.parameter =  '$paramName'";

    return run_query($query);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_param_desc_for_appClass
//
//
function get_param_desc_for_appClass($appClass, $paramName) {

    $query = "
    SELECT
        parameter_descriptions.DESCRIPTION,
        collector
    FROM parameter_descriptions
    WHERE application_class = '$appClass'
        AND parameter = '$paramName'";

    $result = run_query($query);
    return $result;
}


/////////////////////////////////////////////////////////////////////////////////
// Function: update_param_description
//
//
function update_param_description($appSelected, $paramSelected, $description, $collector,$appClassAlias,$paramAlias) {

    $description = str_replace ("'", " ", $description);
    $appClassAlias = str_replace ("'", " ", $appClassAlias);
    $paramAlias = str_replace ("'", " ", $paramAlias);
    $collector = str_replace ("'", "", $collector);
    $paramAlias = trim ($paramAlias);
    $appClassAlias = trim ($appClassAlias);
    
    $cmd = "
    UPDATE parameter_descriptions
    SET parameter_descriptions.DESCRIPTION = '$description', 
        parameter_descriptions.collector = '$collector',
        parameter_descriptions.parameter_alias = '$paramAlias'
        
    WHERE parameter = '$paramSelected'
          AND application_class = '$appSelected'";
          
    run_sql_cmd($cmd);

    $cmd = "UPDATE parameter_descriptions
            SET parameter_descriptions.app_class_alias = '$appClassAlias'
            WHERE application_class = '$appSelected'";

    run_sql_cmd($cmd);

    log_change("Parameter Settings for $appSelected/$paramSelected updated by ".$_SESSION['username']);
}

/**
* print debug information to the current debug window and error log
*
* @access public
* @param $name string variable name
* @param $data unknown variable
* @return null
* @global
*/
function debug_var($name,$data)
{
    $debug = $_SESSION['debug'];
    if (!$debug) {return;}

    log_entry ("DEBUG: ----------- $name ");

    $data = str_replace("'","\\'",$data);
    debug_open_window();
    $captured = explode("\n",debug_capture_print_r($data));
    print "<script type=text/javascript>\n";
    print "debugWindow.document.writeln('<b>$name</b>');\n";
    print "debugWindow.document.writeln('<pre>');\n";


    foreach($captured as $line)
    {
        print "debugWindow.document.writeln('".trim(nl2br(debug_colorize_string($line)))."');\n";
        log_entry ("DEBUG:   ".trim($line));
    }


    print "debugWindow.document.writeln('</pre>');\n";
    print "debugWindow.scrollTo(0,99999);\n";
    print "self.focus();\n";
    print "</script>\n";
    log_entry ("DEBUG: ----------------------------------");
}


/**
* print a message to the debug window
*
* @access public
* @param $mesg string message to display
* @return null
* @global
*/
function debug_msg($mesg)
{
    $debug = $_SESSION['debug'];
    if (!$debug) {return;}

    $tmp1 = str_replace("'","\\'",$mesg);
    $tmp2 = str_replace("\n", '<br />', $tmp1);
    $tmp3 = trim($tmp2);

    // Write debug info to debug browser window
    debug_open_window();
    print "<script type=text/javascript>\n";
    print "debugWindow.document.writeln('".$tmp3."<br />');\n";
    print "debugWindow.scrollTo(0,99999);\n";
    print "self.focus();\n";
    print "</script>\n";

    // Write debug to log
    log_entry ("DEBUG: $mesg");
}

/**
* open a debug window for display
*
* this function may be called multiple times
* it will only print the code to open the
* remote window the first time that it is called.
*
* @access private
* @return null
* @global
*/
function debug_open_window()
{
    static $window_opened = FALSE;
    if(!$window_opened)
    {
        ?>
        <script type=text/javascript>
        debugWindow = window.open("","debugWin","toolbar=no,scrollbars,width=600,height=400");
        debugWindow.document.writeln('<html>');
        debugWindow.document.writeln('<head>');
        debugWindow.document.writeln('<title>Advantis Debug Info</title>');
        debugWindow.document.writeln('</head>');
        debugWindow.document.writeln('<body style="font-size:xx-small;"><font face="verdana,arial">');
        debugWindow.document.writeln('<hr size=1 width="100%">');
        </script>
        <?
        $window_opened = TRUE;
    }
}


/**
* catch the contents of a print_r into a string
*
* @access private
* @param $data unknown variable
* @return string print_r results
* @global
*/
function debug_capture_print_r($data)
{
    ob_start();
    print_r($data);

    $result = ob_get_contents();

    ob_end_clean();

    return $result;
}


/**
* colorize a string for pretty display
*
* @access private
* @param $string string info to colorize
* @return string HTML colorized
* @global
*/

function debug_colorize_string($string)
{
    /* turn array indexes to red */
    $string = str_replace('[','[<font color="red">',$string);
    $string = str_replace(']','</font>]',$string);
    /* turn the word Array blue */
    $string = str_replace('Array','<font color="blue">Array</font>',$string);
    /* turn arrows graygreen */
    $string = str_replace('=>','<font color="#556F55">=></font>',$string);
    return $string;
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_category
//
//

function get_total_category() {


    $query = "
    SELECT category, type, description
    FROM categories
    GROUP BY category";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_categorytype_labels
//
//

function get_categorytype_labels() {


    $query = "
    SELECT id,description
    FROM categorytype_labels
    ORDER BY id";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_function_labels
//
//

function get_function_labels() {


    $query = "
    SELECT id,function
    FROM function_labels
    ORDER BY id";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_category($totalCategoryId)
//
//
function get_category($totalCategoryId,$functionSent) {


    $query = "
    SELECT category
    FROM categories,category_functions
    WHERE categories.id  IN ($totalCategoryId)
    AND category_functions.functionid='$functionSent'
    AND category_functions.categoryid=categories.id
    GROUP BY category";
    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_category_for_user
//
//
function get_total_category_for_user($userId) {


    $query = "
    SELECT rights.categoryid
    FROM people_groups, user_groups, rights
    WHERE people_groups.userid = '$userId'
    AND   people_groups.groupid = user_groups.id
    AND   user_groups.id = rights.usergroupid
    GROUP BY categoryid";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_functions_for_category ($categoryid)
//
//

function get_functions_for_category ($categoryid) {


    $query = "
    SELECT functionid
    FROM category_functions
    WHERE categoryid = '$categoryid'
    ORDER BY functionid";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_category_for_group
//
//
function get_total_category_for_group($id) {


    $query = "
    SELECT category
    FROM categories, rights
    WHERE (categories.id = rights.categoryid
        OR  rights.categoryid=-1)
        AND rights.usergroupid = $id
    GROUP by category";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_agent_for_group
//
//
function get_total_agent_for_group($id) {


    $query = "
    SELECT display_name 
    FROM agents, rights
    WHERE (agents.id = rights.agentid
        OR  rights.agentid=-1)
        AND rights.usergroupid = $id
    GROUP BY display_name";

    return run_query($query);
}
/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_group_group($id)
//
//
function get_total_group_group($id) {


    $query = "
        SELECT groupid
        FROM rights
        WHERE usergroupid = $id";
    $result = run_query($query);
    if (preg_grep ("/-1/", $result)) {
        $query = "SELECT server_group FROM server_groups WHERE pid IS NULL";
    }
    else {
        $query = "
        SELECT server_group
        FROM server_groups, rights
        WHERE server_groups.id = rights.groupid
            AND   rights.usergroupid = $id
        GROUP BY server_group";
    }

    return run_query($query);
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_category_for_appClass
//
//
function get_category_for_appClass($application_class, $parameter) {


    $query = "
    SELECT category
    FROM
        categories,
        parameter_categories,
        parameter_descriptions
    WHERE
        application_class = '$application_class'
        AND  categories.id = parameter_categories.categoryid
        AND parameter_categories.parameterid = parameter_descriptions.id
        AND parameter = '$parameter'
    GROUP BY category";

    return run_query($query);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_parameters
//
//
function get_total_parameters($kmCategory) {


    $query = "
        SELECT parameter
        FROM
            categories,
            parameter_categories,
            parameter_descriptions
        WHERE
            category = '$kmCategory'
            AND categories.id = parameter_categories.categoryid
            AND parameter_categories.parameterid = parameter_descriptions.id";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_param_con_std
//
//
function get_param_con_std($kmCategory) {


    $query = "
        SELECT  parameter,
                app_class_alias,
                parameter_alias
        FROM
            categories,
            parameter_categories,
            parameter_descriptions
        WHERE
            category = '$kmCategory'
            AND  categories.id = parameter_categories.categoryid
            AND parameter_categories.parameterid = parameter_descriptions.id
            AND param_type != 'COL'
        GROUP BY parameter";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_param_coll_std
//
//
function get_param_coll_std($kmCategory) {


    $query = "
        SELECT parameter
        FROM
            categories,
            parameter_categories,
            parameter_descriptions
        WHERE
            category = '$kmCategory'
            AND  categories.id = parameter_categories.categoryid
            AND parameter_categories.parameterid = parameter_descriptions.id
            AND param_type != 'CON'
        GROUP BY parameter";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_consumer_for_coll
//
//
function get_consumer_for_coll($collParam) {

    // Bug in the convertion that's adding a space at the beginning of the collector
    // This is why we have 2 statement in the where

    $query = "
        SELECT parameter
        FROM
            parameter_descriptions
        WHERE
            collector = '$collParam'
        or collector = ' $collParam'
        GROUP BY parameter";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_param_for_app_class
//
//
function get_param_for_app_class($kmCategory, $appClass) {

    $query = "
    SELECT parameter,
           app_class_alias,
           parameter_alias
    FROM
        categories,
        parameter_categories,
        parameter_descriptions

    WHERE
        category = '$kmCategory'
        AND categories.id = parameter_categories.categoryid
        AND parameter_categories.parameterid = parameter_descriptions.id
        AND application_class = '$appClass'
    GROUP BY parameter";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_param_for_app_class
//
//
function get_all_param_for_app_class($appClass) {

    $query = "
    SELECT parameter
    FROM
        parameter_descriptions
    WHERE
        application_class = '$appClass'
    GROUP BY parameter";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_total_application
//
//
function get_total_application($kmCategory) {

    $query = "
    SELECT application_class
    FROM
        categories,
        parameter_categories,
        parameter_descriptions
    WHERE
        category = '$kmCategory'
        AND categories.id = parameter_categories.categoryid
        AND parameter_categories.parameterid = parameter_descriptions.id
    GROUP BY application_class";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_appClass_for_param
//
//
function get_appClass_for_param($category, $param) {



    $query = "
        SELECT
            application_class,
            app_class_alias
        FROM
            categories,
            parameter_categories,
            parameter_descriptions
        WHERE
            category = '$category'
            AND  categories.id = parameter_categories.categoryid
            AND parameter_categories.parameterid = parameter_descriptions.id
            AND parameter = '$param'
        GROUP BY application_class";

    return run_query($query);

}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_application_class
//

function get_all_application_class() {

    $query = "SELECT application_class FROM parameter_descriptions GROUP BY application_class";
    return run_query($query);
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_active_application_class
//

function get_all_active_application_class() {

    $query = "SELECT application_class FROM thresholds GROUP BY application_class";
    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_messageWording
//
//
function get_all_messageWording() {


    $result = array();
    $query = "
    SELECT parameter, msgTextAlarm
    FROM thresholds
    WHERE msgTextAlarm  != ''
    GROUP BY parameter, msgTextAlarm";

    $allAlarm =  run_query($query);

    $query = "
    SELECT parameter, msgTextWarning
    FROM Thresholds
    WHERE msgTextWarning  != ''
    GROUP BY parameter, msgTextWarning";

    $allWarning =  run_query($query);

    $query = "
    SELECT parameter, msgTextInformation
    FROM Thresholds
    WHERE msgTextInformation  != ''
    GROUP BY parameter, msgTextInformation";

    $allInfo =  run_query($query);

    $result = array_merge($allAlarm, $allWarning, $allInfo);
    sort($result);
    $result = array_unique ($result);

    # return $result;
    return $allAlarm;
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_messageWording_for_host
//
//
function get_all_messageWording_for_host($hostToGet) {

    $result = array();

    $hostToGet = explode (" ", $hostToGet);
    $hostName = $hostToGet[0];
    $port = $hostToGet[1];
    $query = "
    SELECT parameter, msgTextAlarm
    FROM agents, thresholds
    WHERE thresholds.agentId = agents.id
        AND agents.hostname = '$hostName'
        AND agents.port = '$port'
        AND thresholds.msgTextAlarm  != ''
    GROUP BY parameter, msgTextAlarm";

    $hostAlarm = run_query($query);

    $query = "
    SELECT parameter, msgTextWarning
    FROM agents, thresholds
    WHERE thresholds.agentId = agents.id
        AND agents.hostname = '$hostName'
        AND agents.port = '$port'
        AND thresholds.msgTextWarning  != ''
    GROUP BY parameter, msgTextWarning";

    $hostWarning = run_query($query);

    $query = "
    SELECT parameter, msgTextInformation
    FROM agents, thresholds
    WHERE thresholds.agentId = agents.id
        AND agents.hostname = '$hostName'
        AND agents.port = '$port'
        AND thresholds.msgTextInformation  != ''
    GROUP BY parameter, msgTextInformation";



    $hostInfo = run_query($query);

    $result = array_merge($hostAlarm, $hostWarning, $hostInfo);
    sort($result);
    $result = array_unique ($result);

    return $result;

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_threshold_for_host
//
//
function get_all_threshold_for_host($hostToGet, $parameter,$instance) {

   $hostToGet = explode (" ", $hostToGet);
   $hostName = $hostToGet[0];
   $port = $hostToGet[1];
   if ($instance != "") {
    $query = "
    SELECT
        display_name, port, Application_class, parameter,
        border_active, border_min, border_max, border_trigger, border_occur, border_state,
        alarm1_active, alarm1_min, alarm1_max, alarm1_trigger, alarm1_occur, alarm1_state,
        alarm2_active, alarm2_min, alarm2_max, alarm2_trigger, alarm2_occur, alarm2_state,
        msgTextAlarm, msgTextWarning, msgTextInformation, PollTime
    FROM
        agents,
        thresholds
    WHERE
            parameter = '$parameter'
        AND display_name = '$hostName'
        AND port = '$port'
        AND instance='$instance'
        AND agents.id = thresholds.agentid
        AND ((border_active != 0 AND border_state != 0)
        OR (alarm1_active != 0 AND alarm1_state != 0)
        OR (alarm2_active != 0 AND alarm2_state != 0))";
    }
    else {
            $query = "
    SELECT
        display_name, port, Application_class, parameter,
        border_active, border_min, border_max, border_trigger, border_occur, border_state,
        alarm1_active, alarm1_min, alarm1_max, alarm1_trigger, alarm1_occur, alarm1_state,
        alarm2_active, alarm2_min, alarm2_max, alarm2_trigger, alarm2_occur, alarm2_state,
        msgTextAlarm, msgTextWarning, msgTextInformation, PollTime
    FROM
        agents,
        thresholds
    WHERE
            parameter = '$parameter'
        AND display_name = '$hostName'
        AND port = '$port'
        AND agents.id = thresholds.agentid
        AND ((border_active != 0 AND border_state != 0)
        OR (alarm1_active != 0 AND alarm1_state != 0)
        OR (alarm2_active != 0 AND alarm2_state != 0))";
    }
    return run_query($query);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_polltime_for_host
//
//
function get_polltime_for_host($hostToGet, $parameter) {

   $hostToGet = explode (" ", $hostToGet);
   $hostName = $hostToGet[0];
   $port = $hostToGet[1];
    $query = "
    SELECT
    PollTime
    FROM
        agents,
        thresholds
    WHERE
        parameter = '$parameter'
        AND hostname = '$hostName'
        AND port = '$port'
        AND agents.id = thresholds.agentid";

    return run_query($query);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_instances
//
//
function get_all_instances ($hostToGet, $parameterToGet, $appClass) {
     
    $whereField = create_where_statement_for_all_hosts($hostToGet);
    $hostToGet = trim ($hostToGet);
    $hostToGetArray = explode (" ", $hostToGet);
    $hostName = $hostToGetArray[0];
    $port     = $hostToGetArray[1];
    if ($whereField != "") {
        $query = "
        SELECT instance
        FROM  agents, 
              thresholds
        WHERE thresholds.agentId = agents.id
              AND $whereField
              AND thresholds.parameter = '$parameterToGet'
              AND thresholds.application_class = '$appClass'
              AND thresholds.Instance  != ''
              
        GROUP BY instance";
        # echo "query $query <br>";
        return run_query($query);

      }
      else {
          return array();
        }
        
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_processes_threshold
//
//
function get_all_processes_threshold ($hostToGet, $parameterToGet, $appClass) {
     
    $whereField = create_where_statement_for_all_hosts($hostToGet);
    $hostToGet = trim ($hostToGet);
    $hostToGetArray = explode (" ", $hostToGet);
    $hostName = $hostToGetArray[0];
    $port     = $hostToGetArray[1];
    if ($whereField != "") {
        $query = "
        SELECT instance,
               border_active,
               border_min 
        FROM  agents, 
              thresholds
        WHERE thresholds.agentId = agents.id
              AND thresholds.parameter = '$parameterToGet'
              AND thresholds.application_class = '$appClass'
              AND thresholds.Instance  != ''
              AND $whereField
              
        GROUP BY instance";
        return run_query($query);

      }
      else {
          return array();
        }
        
}


/////////////////////////////////////////////////////////////////////////////////
// Function: write_log_in
//
//

function write_log_in($text) {

global $USE_LDAP;

// Disable encryption when using LDAP authentication
if ($USE_LDAP == 0) {
    ?>
    <script src="md5.js"></script>
    <script>
    function login(f) {

        f['md5'].value = hex_hmac_md5(f['userName'].value, f['password'].value);
        f['password'].value = null;
        return true;
    }

     </script>

    <?
} else {
    ?>
    <script src="md5.js"></script>
    <script>
    function login(f) {

        if (f['userName'].value == 'admin') {
            f['md5'].value = hex_hmac_md5(f['userName'].value, f['password'].value);
            f['password'].value = null;

        }else {
            f['md5'].value = f['password'].value;
            f['password'].value = null;
        }
        return true;
     }

     </script>

    <?
}
echo "<p>$text</p>
";
?>
<fieldset class="login">
<legend>Login</legend>
<form method='post' action='' onSubmit="return login(this);" >

<label for="userName">User Name:</label>
<input class="input-box2" type="text" name="userName"> <BR>

<label for="password">Password:</label>
<input class="input-box2" type="password" name="password"> <BR>

<input class="submit-button" type="submit" value="login">
<input type="hidden" name="md5" value="">

</form>
<i>User Id and Password are case sensitive</i>
</fieldset>
<br/>
<br/>
<br/>
<br/>

<?
}


/////////////////////////////////////////////////////////////////////////////////
// Function: is_right_password
//
//


function is_right_password($userId, $password) {

    $query = "select id from people where id = '$userId' and password = '$password'";
    $result = run_query($query);

    if (count($result)) {
        return true;
    }
    else {
        return false;
    }
}

///////////////////////////////////////////////////////////////////////////////
// Function: update_password
//
//


function update_password($userId, $newPassword) {

    $cmd = "UPDATE people SET password = '$newPassword' WHERE id = '$userId' ";
    run_sql_cmd($cmd);

    log_change("Password changed for user ID $userId by ".$_SESSION['username']);


}


///////////////////////////////////////////////////////////////////////////////
// Function: sanitize_input
// Description: All user input should be cleansed before passing to database
//


function sanitize_input($inputString,$type) {

    // Types
    // 1: regular input
    // 2: username
    // 3: numeric
    // 4: alpha only
    // 5: alphanumeric

    if ($type == 1) {
        // Regular input

        // Replace special characters with HTML version

        $cleanString = str_replace ("'", "&acute;", $inputString);
        $cleanString = str_replace ("\"", "&quot;", $cleanString);
        $cleanString = str_replace ("&", "&amp;", $cleanString);
        $cleanString = str_replace ("<", "&lt;", $cleanString);
        $cleanString = str_replace (">", "&gt;", $cleanString);

        $cleanString = addslashes($cleanString);

    } elseif ($type == 2) {

        // Username input

        // usernames may contain the following characters:
        // @ - email address format (username@domain.com)
        // . - e.g. firstname.lastname
        // \ - domain delimiter, such as DOMAIN\username
        // _ - e.g. firstname_lastname
        // letters - uppercase or lowercase
        // numbers

        $cleanString = preg_replace ("/[^\\\\0-9A-Za-z_\.@]+/","",$inputString);

        // Remove extra backslashes
        $cleanString = preg_replace ("/[\\\\]+/","\\",$cleanString);


    } elseif ($type == 3) {
        // Numeric data
        $cleanString = preg_replace ("/[^0-9]+/","",$inputString);
    } elseif ($type == 4) {
        // Alpha chars only
        $cleanString = preg_replace ("/[^A-Za-z]+/","",$inputString);
    } elseif ($type == 5) {
        // Numbers and alpha only
        $cleanString = preg_replace ("/[^0-9A-Za-z]+/","",$inputString);
    } else {
        $msg = "sanitize_input(): Invalid type ($type)";
        log_entry ($msg);
        die ($msg);
    }

    debug_var ("Sanitize input - Input string", $inputString);
    debug_var ("Sanitize input - Clean string", $cleanString);

    return $cleanString;

}

///////////////////////////////////////////////////////////////////////////////
// Function: get_user_sans_domain
// Description: Strip the domain from a username, if present
//


function get_user_sans_domain($userName) {

    // Remove the domain portion of the username
    $shortUserName = preg_replace ("/[A-Za-z0-9_\.]+\\\\([A-Za-z0-9\.@_]+)/","$1",$userName);

    if ($shortUserName == "") {
        $shortUserName = $userName;
    }

    return $shortUserName;
}
///////////////////////////////////////////////////////////////////////////////
// Function: ldap_auth
// Description: If ldap is enabled, attempt to authenticate user with ldap server
//
// Note: user 'admin' is never authenticated through ldap...always through database

function ldap_auth($userName,$password) {


    global $USE_LDAP;
    global $LDAP_SERVER;
    global $LDAP_PORT;

    // Return false if LDAP is disabled
    if ($USE_LDAP != 1) {
        return -1;
    }

    // Bypass LDAP for user 'admin'
    if ($userName == "admin") {
        return -1;
    }


    // initialize local vars
    $ldapbind = 0;
    $userValidated = 0;

    // Die with error message if LDAP is not available
    $ldapconn = ldap_connect($LDAP_SERVER, $LDAP_PORT);

    if (!$ldapconn) {
        $msg = "Unable to setup LDAP connection for LDAP server ($LDAP_SERVER) on port $LDAP_PORT";
        log_entry ($msg);
        die ($msg);
    }

    if ($ldapconn) {
        #// Insert default domain, if applicable
        # $userName = $DEFAULT_DOMAIN . "\\" . $userName;

        // Present credentials to LDAP server for authentication
        $ldapbind = ldap_bind($ldapconn, "$userName", "$password");


        // Close connection to ldap
        ldap_close ($ldapconn);
    }

    // Successful bind means credentials are valid
    if ($ldapbind) {

        // User authenticated
        $userValidated = 1;


        $msg = "User ($userName) authenticated with LDAP ($LDAP_SERVER).";
        debug_msg ($msg);

    } else {

        // Unable to authenticate user
        $userValidated = 0;

        $msg = "Unable to authenticate user ($userName) with LDAP ($LDAP_SERVER).";
        debug_msg ($msg);
    }


    return $userValidated;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_ldap_userid
// Description: Return the userid for an LDAP user
//

function get_ldap_userid ($userName) {

    $query = "
        SELECT id
        FROM people
        WHERE username = '$userName'
        AND status > 0";

    $result = run_query($query);

    return $result;
}


/////////////////////////////////////////////////////////////////////////////////
// Function: verify
// Description: Verify that user is logged in, or prompt for credentials
//


function verify() {

    if (isset($_SESSION['user'])) {

        // Return: Already logged in
        return true;

    }

    $userName = $_POST["userName"];
    $password = $_POST["md5"];

    $userName = sanitize_input($userName,2);
    $password = sanitize_input($password,1);

    ### Disable clear text password.  Password is encrypted in the browser with javascript
    #$password = $_POST["password"];


    // Assuming we have a valid username/password, check LDAP for authentication
    if (($userName != "") && ($password != "")) {


        $ldapVerified = ldap_auth ($userName, $password);

    } else {

        // user must log in
        $text = "Please login";
        write_log_in( $text );
        exit;

    }

    // If LDAP is disabled, use database to authenticate
    if ($ldapVerified == -1) {

        // verify username/password and log in
        $query = "
            SELECT id
            FROM people
            WHERE username = '$userName'
            AND password = '$password'
            AND status > 0";

        $result = run_query($query);
        $numMatchingUsers = count($result);

    } elseif ($ldapVerified == 0) {

         // LDAP is enabled but authentication failed -> deny login
         $numMatchingUsers = 0;

    } elseif ($ldapVerified == 1) {

        // LDAP authentication succeeded

        // Remove the domain portion of the username
        $shortUserName = get_user_sans_domain($userName);

        // Get the userid for the short form of the username after authenticating with domain
        $result = get_ldap_userid($shortUserName);
        $numMatchingUsers = count($result);

    } else {

        $msg = "Unable to authenticate: Unknown return code from ldap_auth: ($ldapVerified)";
        log_entry ($msg);
        die ($msg);

    }

    if ( ($numMatchingUsers == 0) and ($ldapVerified == 1) ) {

        // New user exists in LDAP.  Create entry for new user.

        $msg = "Creating new user entry for $userName after authenticating with LDAP.";
        log_entry($msg);

        // Remove the domain portion of the username
        $shortUserName = get_user_sans_domain($userName);
        // add_update_user($username, $md5, $password, $firstname, $lastname, $email, $totalGroup, $randomPass);
        add_update_user($shortUserName, "LDAP", "LDAP", $shortUserName, null,null, "users", null);

        $result = get_ldap_userid($shortUserName);
        $numMatchingUsers = count($result);


    }

    if ($numMatchingUsers > 0) {

        // Make sure we don't have more than one matching user (should be impossible)
        if ($numMatchingUsers > 1) {
            $msg = "Error: Multiple users with the same username: $userName";
            log_entry ($msg);
            die ($msg);
        }

        $user = implode ("", $result);

        // register session variables and log the login
        $_SESSION['user'] = $user;
        $_SESSION['username'] = $userName;

        log_session ("login");

        $cmd = "UPDATE people SET last_login = now() WHERE id = $user";


        // Get the user's rights and save them in a session var
        $query = "
            SELECT rights.Actionid
            FROM  people_groups, rights
            WHERE people_groups.userid = '$user'
            AND   people_groups.groupid = rights.Usergroupid";

        $actionId = run_query($query);
        $_SESSION['rights'] = $actionId;

        run_sql_cmd($cmd);

        // Return: successful login
        return true;

    } elseif (
                ($numMatchingUsers == 0)
                and ( ($ldapVerified == 0) or ($ldapVerified == -1) )
             ) {

        // bad user and password

        $_SESSION['badlogin'] = $userName;
        log_session ("badlogin");

        $text = "Invalid username and/or password";
        write_log_in( $text );
        exit;

    } else {

        // This should never happen.
        debug_msg ("Unanticipated condition: numMatchingUsers=($numMatchingUsers) and ldapVerified=($ldapVerified)");

        $msg = "Error: Unrecoverable condition during login for user ($userName)";
        log_entry ($msg);
        die ($msg);

    }

} // end verify function

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_requests_rules
//
//
function get_all_requests_rules ($userId) {


   $query = "
    SELECT variable,
        value,
        function_labels.function,
        rules.id,
        operation
    FROM 
        change_requests,
        request_rules,
        rules,
        function_labels
    WHERE change_requests.userId = '$userId'
        AND change_requests.id = request_rules.requestid
        AND request_rules.ruleid = rules.id
        AND function_labels.id = request_rules.functionid
        AND change_requests.status = '0'
    ORDER BY rules.id";
    
    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_requests_rules_for_function
//
//
function get_requests_rules_for_function ($userId,$functionId) {


    $query = "
    SELECT variable,
        value,
        function_labels.function,
        rules.id,
        operation
    FROM 
        change_requests,
        request_rules,
        rules,
        function_labels
    WHERE change_requests.userId = '$userId'
        AND change_requests.id = request_rules.requestid
        AND request_rules.ruleid = rules.id
        AND request_rules.functionid = '$functionId'
        AND function_labels.id = '$functionId'
        AND change_requests.status = '0'
    ORDER BY rules.id";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_pending_requesets()
//
//
function get_all_requests($status) {


    $cmd = "
    SELECT change_requests.id,
        priority,
        -- comments.comment,
        comment,
        status,
        change_control,
        change_requests.date_entered,
        date_modified
    FROM
        change_requests -- INNER JOIN comments ON change_requests.id=comments.recordid
    WHERE
        status like '$status'
        -- and comments.table_name = 'CHANGE_REQUESTS'
    ORDER BY priority,
        date_modified desc,
        date_entered desc,
        id";
    return run_query($cmd);
}



/////////////////////////////////////////////////////////////////////////////////
// Function: get_details_for_request_id
//
//
function get_details_for_request_id ($requestId) {

    $cmd = "
        SELECT
            change_requests.id requestid,
            username,
            firstname,
            lastname,
            comment,
            change_requests.priority priority,
            variable,
            operation,
            value,
            change_requests.date_entered date_entered,
            rules.id,
            agents.display_name,
            agents.port,
            null server_group,
            change_control,
	    change_targets.status,
	    change_targets.date_modified
        FROM
            people,
            change_requests,
            request_rules,
            rules,
            change_targets,
            agents
        WHERE
            people.id = change_requests.UserID
            AND change_requests.ID = request_rules.requestid
            AND request_rules.ruleid = rules.id
            AND change_targets.RequestID = change_requests.id
            AND agents.id = change_targets.AgentID
            AND change_requests.id = '$requestId'
        UNION
        SELECT
            change_requests.id requestid,
            username,
            firstname,
            lastname,
            comment,
            change_requests.priority priority,
            variable,
            operation,
            value,
            change_requests.date_entered date_entered,
            rules.id,
            null hostname,
            null port,
            server_groups.server_group,
            change_control,
	    change_targets.status,
	    change_targets.date_modified
        FROM
            people,
            change_requests,
            request_rules,
            rules,
            change_targets,
            server_groups
        WHERE
            people.id = change_requests.UserID
            AND change_requests.ID = request_rules.requestid
            AND request_rules.ruleid = rules.id
            AND change_targets.RequestID = change_requests.id
            AND server_groups.id = change_targets.groupid
            AND change_requests.id = '$requestId'";


    return run_query($cmd);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: display_all_request_for_approval
//
//
function display_all_request_for_approval ($totalRequests) {
    ?>
    <TABLE BORDER>
        <TR>
            <TH>REQUEST ID</TH>
            <TH>PRIORITY</TH>
            <TH>COMMENT</TH>
            <TH>CHANGE CONTROL</TH>
            <TH>DATE ENTERED</TH>
        </TR>
        <?php

    foreach ($totalRequests as $indRequest) {
        $indRequest = str_replace("___", "\M", $indRequest);
        $indRequestArray = explode ("\M", $indRequest);
        ?>
        <TR>
            <TD><a href="finalApprove.php?requestId=<?php print $indRequestArray[0]; ?>"><?php print $indRequestArray[0]; ?></a></TD>
            <TD><?php print $indRequestArray[1]; ?></TD>
            <TD><?php print $indRequestArray[2]; ?></TD>
            <TD><?php print $indRequestArray[4]; ?></TD>
            <TD><?php print $indRequestArray[5]; ?></TD>
        </TR>
        <?php
    }

    // Close the last table
    ?>
    </TABLE>
    <?php
}

/////////////////////////////////////////////////////////////////////////////////
// Function: new_change_request
//
//
function new_change_request ($userId, $variable, $operation, $value, $requestType) {


    // Add some error checking

    $query = "
    SELECT id
    FROM change_requests
    WHERE userId = '$userId'
    AND change_requests.status='0'";

    $result = run_query($query);

    if (!count($result)) {
        // We need to to insert the requestid first
        $cmd = "
        INSERT INTO change_requests (
            userid,
            date_entered,
            date_modified)
        VALUES (
            $userId,
            now(),
            now())";
        run_sql_cmd($cmd);
        $query = "
        SELECT id
        FROM change_requests
        WHERE userId = '$userId'
            AND comment IS NULL";
        $result = run_query($query);
    }

    $requestId = $result[0];
    # $rulescmd = "insert into rules (requestid,variable,operation,value,date_entered) values
    # ($requestId,'$variable','$operation','$value',now())";
    $rulescmd = "
    INSERT INTO rules (
        variable,
        operation,
        value,
        date_entered)
    VALUES (
        '$variable',
        '$operation',
        '$value',
        now())";
    run_sql_cmd($rulescmd);
    $cmd = "
    INSERT INTO request_rules (
        requestid,
        ruleid,
        functionid
    )
    VALUES (
        $requestId,
        last_insert_id(),
        $requestType)";

    run_sql_cmd($cmd);


    log_change($_SESSION['username']." created a new rule in preparation for a change request");
}

/////////////////////////////////////////////////////////////////////////////////
// Function: update_change_request
//
//
function update_change_request($rulesToSubmit, $userId, $priority, $comment, $hosts, $groups, $changeControl) {

    // Add some error checking
    $comment = str_replace ("'", " ", $comment);
    $changeControl = str_replace("'", " ", $changeControl);

    $query = "
    SELECT id
    FROM change_requests
    WHERE userId = '$userId'
    AND change_requests.status='0'";

    $result = run_query($query);

    $oldRequestId = $result[0];
    
    // Update change_requests table
    $cmd = "
    INSERT INTO change_requests (
        userid,
        priority,
        date_entered,
        status,
        comment,
        Change_Control)
    VALUES (
        $userId,
        $priority,
        now(),
        4,
        '$comment',
        '$changeControl')";
    run_sql_cmd($cmd);

    // Let's get the request id
    $query = "SELECT last_insert_id()";
    $result = run_query ($query);
    $requestId = $result[0];

    // Update request_rule table
    $ruleToSubmitArray = explode(" ", $rulesToSubmit);
    $whereField = get_where_for_fields($ruleToSubmitArray, "id");

    # We need to duplicate the rules
    $cmd = "
           INSERT into rules (rulesetid,variable,operation,value,date_entered) 
           SELECT rulesetid,variable,operation,value,date_entered 
           FROM rules WHERE $whereField";

    run_sql_cmd($cmd);

    // Let's get the rules id
    $query = "SELECT last_insert_id()";
    $result = run_query ($query);
    $ruleId = $result[0];
    
    $numberOfRules = count($ruleToSubmitArray);

    $newRulesToSubmit = array();
    $i = 1;
    
    while ($i <= $numberOfRules) {
        $oldRule = $ruleToSubmitArray[$numberOfRules-$i];
        $cmd = "
            INSERT into request_rules (requestid, ruleId, functionid) 
            SELECT  '$requestId', '$ruleId', functionid from request_rules 
            WHERE ruleid='$oldRule'
            and requestid='$oldRequestId'";
        run_sql_cmd($cmd);
        
        $ruleId = $ruleId + 1;
        $i++;
    }
    
    // Update change_targets table
    $hosts = preg_replace("/<[^>]*>/", "", $hosts);
    $hostArray = explode ("\n", $hosts);

    // Let's create a temporary table to store the agentid, groupid

    $cmd = "DROP TABLE IF EXISTS agentlist";
    run_sql_cmd($cmd);
    $cmd = "CREATE TEMPORARY TABLE agentList (agentid int(4) primary key, groupid int(4))";
    run_sql_cmd($cmd);
    $whereHosts = "";
    if (count($hostArray)) {
            foreach ($hostArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                continue;
            }
                $valueArray = explode(" ", $value);
                if ($whereHosts != "") {
                        $whereHosts = $whereHosts  . " OR ";
                        $whereHosts = $whereHosts . " (display_name = '$valueArray[0]' AND port = '$valueArray[1]' AND status='ACTIVE') ";
                }
                else {
                        $whereHosts = "((display_name = '$valueArray[0]' AND port = '$valueArray[1]' AND status='ACTIVE') ";
                }
            }
            $whereHosts = $whereHosts . ")";
            // We need to get all the
        if ($whereHosts != ")") {
            $cmd = "
                INSERT IGNORE INTO agentList (agentid, groupid)
                SELECT
                    id agentid,
                    null groupid
                FROM agents
                WHERE $whereHosts";
            run_sql_cmd($cmd);
        }
    } // end if (count($hostArray))

    $groupArray = explode (" ", $groups);
    foreach ($groupArray as $group) {

        $group = trim ($group);
        if ($group == "") {
            continue;
        }
        // We need to get the id of the group
        $query = "SELECT id FROM server_groups WHERE server_group = '$group'";
        $groupId = run_query($query);
        $groupId = $groupId[0];

        $cmd = "
            INSERT IGNORE INTO agentList (agentid, groupid)
                select agents.id agentid,
                $groupId groupid
            FROM agents,
                agent_groups,
                server_groups
            WHERE agents.id = agent_groups.agentid
                AND agent_groups.groupid = server_groups.id
                AND server_groups.server_group LIKE '$group%'
                AND agents.status ='ACTIVE'
            GROUP BY agents.id";
       run_sql_cmd($cmd);
    }


    $cmd = "
    INSERT INTO change_targets (
        requestid,
        status,
        groupid,
        agentid)
    SELECT
        $requestId requestid,
        0 status,
        a.groupid groupid,
        a.agentid agentid
    FROM agentList a";
    run_sql_cmd($cmd);

    log_change("Change request $requestId submitted by ".$_SESSION['username']);


    #    $msg ="Error: Unable to determine target servers to update.";
    #    echo "$msg<BR/>";
    #    $msg .= " User: ".$_SESSION['username']." Rules: $rulesToSubmit Priority: $priority ";
    #    $msg .= "Target servers: $hosts Target server groups: $groups  Comment: $comment";
    #    log_entry($msg);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: translate_rules_into_english
//
//
function translate_rules_into_english ($rule) {

    $result = array ();

    $rule = str_replace ("___", "\M", $rule);
    $indRuleArray = explode ("\M", $rule);
    $variable = $indRuleArray[0];
    $value = $indRuleArray[1];
    $changeType = $indRuleArray[2];
    $ruleId = $indRuleArray[3];
    $operation = $indRuleArray[4];
    $variableArray = explode ("/", $variable);

    $category = "N/A";
    $appClass = $variableArray[5];
    $instance = $variableArray[6];
    $parameter = $variableArray[7];

    if ($changeType == "Message Wording") {

        $status = $variableArray[8];
        $status = str_replace ("msgText", "", $status);
        if ($status == "") {
            $status = "ALL";
        }

    }

    elseif (($changeType == "Thresholds") || ($changeType == "Process Count")) {

        $value = str_replace(",", " ", $value);
        $valueArray = explode (" ", $value);

        // We need to get the scan for 1 parameter
        $cmd = "SELECT polltime FROM thresholds WHERE parameter = '$parameter' LIMIT 1";
        $result = run_query($cmd);
        $scan = $result[0];

        $stringToTranslate = "1___2___3___4___$valueArray[1]___$valueArray[2]___$valueArray[3]___$valueArray[4]___$valueArray[5]___$valueArray[6]___$valueArray[7]___$valueArray[8]___$valueArray[9]___$valueArray[10]___$valueArray[11]___$valueArray[12]___$valueArray[13]___$valueArray[14]___$valueArray[15]___$valueArray[16]___$valueArray[17]___$valueArray[18]___$valueArray[18]___$valueArray[19]___A___$scan" . "___$valueArray[0]";
        $value = translate_threshold_to_english($stringToTranslate);

        if ($valueArray[0]) {
            # $value = $value . "<BR><i>* Cycle time value may not be accurate, to get an accurate value, please run the threshold report </i>";
        }
        $status = "N/A";
    }
    elseif ($changeType == "Process Add") {
        $instance = str_replace("$", "", $value);
        $instance = str_replace("^", "", $instance);
        $value = "Process Add";
    }
    elseif ($changeType == "Process Delete") {
        $instance = str_replace("_NO_ARGUMENT", "", $value);
        $instance = str_replace("$", "", $instance);
        $instance = str_replace("^", "", $instance);
        $value = "Process Delete";
    }
    elseif ($changeType == "Polling Intervals") {
        $value = $value . " seconds";
        $status = "N/A";
    }
    elseif ($changeType == "Blackouts") {
        
        $category = $variableArray[4];
        $value = str_replace (":","",$value);
        $valueArray = explode (" ", $value);
        $operation = $valueArray[0];
        $value = str_replace ($operation, "", $value);
        $value = trim ($value);
        $value = translate_Blackout_to_english($value);
        $status = "N/A";
        
    }
    elseif ($changeType == "Notification Targets") {
        
        $category = $variableArray[4];
        $value = str_replace (":","",$value);
        $valueArray = explode (" ", $value);
        $operation = $valueArray[0];
        $value = str_replace ($operation, "", $value);
        $value = trim ($value);
        $status = $variableArray[8];
        if (($operation == "DELETE") && ($value == "")) {
            $value = "DELETE ALL E-MAILS";
        }
        
    }
    if ($instance == "__ANYINST__") {
        $instance = "ALL";
    }
    $result[0] = $changeType;
    $result[1] = $category;
    $result[2] = $appClass;
    $result[3] = $instance;
    $result[4] = $parameter;
    $result[5] = $status;
    $result[6] = $value;
    $result[7] = $ruleId;
    $result[8] = $operation;
    return $result;

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_where_for_fields
//
//

function get_where_for_fields($rules, $field) {

    $whereField = "";
    foreach ($rules as $value) {
        $value = trim ($value);
        if ($value == "") {
            continue;
        }
        if ($whereField != "") {
            $whereField = $whereField . " OR ($field = '$value') \n";
        }
        else {
            $whereField = " (($field = '$value')";
        }

    }
    if ($whereField != "") {
        $whereField = $whereField . ")";
    }
    return $whereField;
}
/////////////////////////////////////////////////////////////////////////////////
// Function: delete_rules
//
//

function delete_rules($rulesToDelete) {

    $userId     = $_SESSION['user'];

    $query = "
    SELECT id
    FROM change_requests
    WHERE userId = '$userId'
    AND change_requests.status='0'";

    $result = run_query($query);

    $requestId = $result[0];
    
    $rulesToDeleteArray = explode (" ", $rulesToDelete);
    $whereField = get_where_for_fields($rulesToDeleteArray, "id");
    $cmd = "DELETE FROM rules WHERE $whereField";
    run_sql_cmd($cmd);
    $numberOfDelete = count($rulesToDeleteArray);
    echo "$numberOfDelete rules deleted <BR>";

    $whereField = get_where_for_fields($rulesToDeleteArray, "ruleid");
    $cmd = "DELETE FROM request_rules 
            WHERE requestid = '$requestId' 
            AND $whereField";
    run_sql_cmd($cmd);

    log_change($_SESSION['username']."deleted $numberOfDelete rules from the database.");
}

/////////////////////////////////////////////////////////////////////////////////
// Function: reject_requests
//
//

function reject_requests($requestToReject, $userId, $comment) {

    // Get the comment
    $comment = str_replace ("'", " ", $comment);
    $cmd = "
    SELECT
        comment,
        now()
    FROM change_requests
    WHERE id = '$requestToReject'";
    $oldComment = run_query($cmd);
    $oldComment = implode ("\n", $oldComment);
    $oldComment = str_replace ("___", " -- ", $oldComment);

    $comment = "$oldComment . $comment";
    $comment = str_replace ("'", " ", $comment);

    $cmd = "UPDATE change_requests
            SET status = 3,
                approverId = $userId,
                comment = '$comment',
                date_modified = now()
            WHERE id = '$requestToReject' ";
    run_sql_cmd($cmd);

    log_change("Request ID $requestToReject REJECTED by ".$_SESSION['username']);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_rulesets
//
//

function get_all_rulesets() {

    $query = "SELECT ruleset FROM rulesets GROUP BY ruleset";
    return run_query($query);
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_rulesets_tmp
// This function should be delete
//

function get_all_rulesets_tmp($hostTotal, $groupTotal) {

    $allGroupsforHost = array();
    $whereField = get_where_for_fields($hostTotal, "hostname");
    if ($whereField != "") {
        $cmd = "SELECT server_group
                FROM agents, server_groups, agent_groups
                WHERE $whereField
                AND server_groups.id = agent_groups.groupId
                AND agent_groups.agentid = agents.id
                GROUP BY server_group";

        $allGroupsforHost = run_query($cmd);
    }
    $groupTotal = array_merge($groupTotal, $allGroupsforHost);
    sort ($groupTotal);
    array_unique($groupTotal);

    // This is a stop gap until we design the final solution

    $whereField = "";
    foreach ($groupTotal as $value) {
        $value = trim ($value);
        if ($value == "") {
            continue;
        }
        if ($whereField != "") {
            $whereField = $whereField . " OR (server_group LIKE '$value%') \n";
        }
        else {
            $whereField = " ((server_group LIKE '$value%')";
        }

    }
    if ($whereField != "") {
        $whereField = $whereField . ")";
    }


    $cmd = "SELECT ruleset
            FROM rulesets, ruleset_groups, server_groups
            WHERE $whereField
                AND ruleset LIKE '%cfg'
                AND server_groups.id = ruleset_groups.groupid
                AND rulesets.id = ruleset_groups.rulesetid
            GROUP BY ruleset";

    return run_query($cmd);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_config_for_request
//
//

function get_config_for_request($requestId) {

    $cmd = "SELECT concat('\"', variable, '\" = { ', operation, ' = \"', value, '\" },')
            FROM request_rules, rules
            WHERE request_rules.requestid = '$requestId'
                AND request_rules.ruleid = rules.id";

    return run_query($cmd);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: aprove_requests
//
//

function approve_requests($requestId, $userId, $comment) {

    // Get the comment
    $comment = str_replace("'", " ", $comment);
    $cmd = "SELECT comment, now() FROM change_requests WHERE id = '$requestId'";
    $oldComment = run_query($cmd);
    $oldComment = implode ("", $oldComment);
    $oldComment = str_replace ("___", " -- ", $oldComment);

    $comment = "$oldComment $comment";
    $comment = str_replace ("'", "", $comment);
    $cmd = "UPDATE change_requests
            SET status = 2,
                approverId = $userId,
                comment = '$comment',
                date_modified = now()
            WHERE id = '$requestId' ";
    run_sql_cmd($cmd);

    log_change("Request ID $requestId approved by ".$_SESSION['username']);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_user_name
//
//

function get_user_name($userId) {

    $cmd = "SELECT username, lastname, firstname FROM people WHERE id = '$userId'";
    $result = run_query($cmd);
    $userToReturn = $result[0];
    $userToReturn = str_replace ("___", "\M", $userToReturn);
    $userArray = explode ("\M", $userToReturn);
    return $userArray;

}

/////////////////////////////////////////////////////////////////////////////////
// Function: update_db_password
//
//

function update_db_password($SQL_USER, $newPassword) {

    $cmd = "SET password = password(\"$newPassword\")";
    run_sql_cmd($cmd);

    log_change("Database password changed by ".$_SESSION['username']);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: check_rights
//
//

function check_rights($actionType) {

    $userRights = $_SESSION['rights'];
    if ($userRights == "") {
        return false;
    }
    if (preg_grep ("/-1/", $userRights)) {
        return true;
    }
    if (preg_grep ("/$actionType/", $userRights)) {
        return true;
    }

    // User doesn't have the appropriate rights
    return false;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: check_rights
//
//

function print_lack_of_privledge_warning() {

    echo "You don't have the appropriate privileges to accomplish this task <BR>";
    exit;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_users()
//
//

function get_all_users() {


    $query = "
        SELECT people.id,
            username,
            firstname,
            lastname,
            email,
            user_group,
            date_entered,
            last_login
        FROM people
            LEFT JOIN people_groups ON people.id = people_groups.userid
            LEFT JOIN user_groups ON people_groups.groupid = user_groups.id
        WHERE people.status > 0
        ORDER BY username";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_username()
//
//

function get_all_username() {


    $query = "
        SELECT username
        FROM people
        WHERE status > 0
        GROUP BY username";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_user_groups()
//
//

function get_all_user_groups() {

    $query = "SELECT id, user_group FROM user_groups GROUP BY user_group";
    return run_query($query);
}


/////////////////////////////////////////////////////////////////////////////////
// Function: send_email
//
//

function send_email($address, $subject, $message) {

    if ($address != "") {
        mail("$address", "$subject", "$message");

        log_entry ("EMAIL: To: $address Subject: [$subject] Body: [$message]");
    }

}

/////////////////////////////////////////////////////////////////////////////////
// Function: add_update_user()
//
//

function add_update_user($username, $md5, $password, $firstname, $lastname,$email, $totalGroup, $randomPass) {

    // First we need to check if it's an existing user
    $query = "SELECT id FROM people WHERE username = '$username'";
    $result = run_query($query);
    if (count($result)) {
        // Existing user
        if ($randomPass != 1) {
            $cmd = "
                UPDATE people SET password = '$md5',  firstname = '$firstname', lastname = '$lastname', email = '$email'
                WHERE username = '$username'";
            $changemsg = "Password reset and user details updated for $username";
        }
        else {
            $cmd = "
                UPDATE people SET firstname = '$firstname', lastname = '$lastname', email = '$email'
                WHERE username = '$username'";
            $changemsg = "User details for $username updated";
        }
        run_sql_cmd($cmd);
        $id = $result[0];
        log_change("$changemsg by ".$_SESSION['username']);

        // Deleting it form the user group
        $cmd = "DELETE FROM people_groups WHERE userid = '$id'";
        run_sql_cmd($cmd);

    }
    else {
        // New user - We need to insert into 2 tables

        $cmd = "
            INSERT INTO people (username, password, firstname, lastname, email, date_entered)
            VALUES ('$username', '$md5', '$firstname', '$lastname', '$email', now())";
            run_sql_cmd($cmd);

        echo "User $username entered successfully, if an e-mail address was entered, an automated message was sent to the user<BR>";
        log_change("User $username added by ".$_SESSION['username']);

        // e-mail User
        if ($email != "") {
            $subject = "Patrol Agent Manager";
            $message = "A new user was created for you \n your username is $username and password $password to access it, please go to http://www.wereharf.com/pam";
            # send_email($email, $subject, $message);

        }
    }

    $totalGroup = explode (",", $totalGroup);
    foreach ($totalGroup as $value) {
        $value = trim ($value);
        if ($value == "") {
            next;
        }
        $cmd = "
            INSERT INTO people_groups (userid,groupid)
            SELECT
                people.id userid,
                user_groups.id groupid
            FROM
                people,
                user_groups
            WHERE people.username = '$username'
            AND user_groups.user_group = '$value'";

        run_sql_cmd($cmd);
    }

}

/////////////////////////////////////////////////////////////////////////////////
// Function: inactivate_user
//
//

function inactivate_user($username) {

    $cmd = "DELETE FROM people_groups WHERE userid IN (SELECT id FROM people WHERE username = '$username')";
        run_sql_cmd($cmd);


    $cmd = "
        UPDATE people
        SET status = 0,
            username = concat(username, '--DELETED--', id)
        WHERE username = '$username'";
    echo "User $username deleted <BR>";
    run_sql_cmd($cmd);

    log_change("User $username DELETED by ".$_SESSION['username']);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_groups()
//
//

function get_all_groups() {



    $query = "
        SELECT user_groups.id, user_group,username
        FROM user_groups
            LEFT JOIN people_groups ON user_groups.id = people_groups.groupid
            LEFT JOIN people  ON people_groups.userid = people.id
        WHERE
            people.status > 0
            OR people_groups.id IS NULL
        ORDER BY user_group";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: add_new_group($groupName, $totalUsers)
//
//

function add_new_group($groupname, $totalUsers) {

    $cmd = "INSERT INTO user_groups (user_group)
            VALUES ('$groupname')";
    run_sql_cmd($cmd);

    if ($totalUsers != "") {
        $totalUsers = explode (",", $totalUsers);
        foreach ($totalUsers as $value) {
            $value = trim ($value);
            if ($value == "") {
                next;
            }
            $cmd = "
            INSERT INTO people_groups (userid,groupid)
            SELECT people.id userid,user_groups.id groupid
            FROM people,user_groups
            WHERE people.username = '$value'
            AND user_groups.user_group = '$groupname'";

            run_sql_cmd($cmd);
        }
    }
    echo "Group $groupname added successfully <br>";
    log_change("User group $groupname added by ".$_SESSION['username']);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: delete_group($groupname)
//
//

function delete_group($groupname) {

    // We need to delete it from 3 tables
    $cmd = "
        DELETE FROM people_groups
        WHERE groupid IN (
            SELECT id FROM user_groups WHERE user_group = '$groupname'
        )";
    run_sql_cmd($cmd);

    $cmd = "
        DELETE FROM rights
        WHERE usergroupid IN (
            SELECT id FROM user_groups WHERE user_group = '$groupname'
        )";
    run_sql_cmd($cmd);

    $cmd = "DELETE FROM user_groups WHERE user_group = '$groupname'";
    run_sql_cmd($cmd);

    echo "Group $groupname deleted successfully <br>";

    log_change("User group $groupname DELETED by ".$_SESSION['username']);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: update_group($groupname, $totalUsers)
//
//

function update_group($groupname, $totalUsers, $id) {

    // We need to update the name and the users

    $cmd = "
        DELETE FROM people_groups
        WHERE groupid IN (
            SELECT id
            FROM user_groups
            WHERE user_group = '$groupname'
            )";
    run_sql_cmd($cmd);

    if ($totalUsers != "") {
        $totalUsers = explode (",", $totalUsers);
        foreach ($totalUsers as $value) {
            $value = trim ($value);
            if ($value == "") {
                next;
            }
            $cmd = "
                INSERT INTO people_groups (userid,groupid)
                SELECT people.id userid,user_groups.id groupid
                FROM people,user_groups
                WHERE people.username = '$value'
                    AND user_groups.user_group = '$groupname'";

            run_sql_cmd($cmd);
        }
    }

    $cmd = "
        UPDATE user_groups SET user_group = '$groupname'
        WHERE id = '$id'";
    run_sql_cmd($cmd);
    echo "Group $groupname updated successfully<br>";

    log_change("User group $groupname modified by ".$_SESSION['username']);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_groups_name()
//
//

function get_all_groups_name() {

    $query = "SELECT user_group FROM user_groups";
    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: update_group_right($id, $totalCategories, $totalGroups, $totalAgents)
//
//

function update_group_right($id, $totalCategories, $totalGroups, $totalAgents) {

    // We need to delete the row first and then update them
    $cmd = "DELETE FROM rights WHERE usergroupid = '$id'";
    run_sql_cmd($cmd);

    // We need to loop and add category, groups and agent
    if ($totalCategories != "") {
        $totalCategoryArray = explode (",", $totalCategories);
        foreach ($totalCategoryArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                next;
            }
            $cmd = "
                INSERT INTO rights (usergroupid, ActionId, categoryid)
                SELECT '$id', '1', categories.id
                FROM categories
                WHERE category = '$value'";
            run_sql_cmd($cmd);

        }
    }

    if ($totalGroups != "") {
        $totalGroupsArray = explode (",", $totalGroups);
        foreach ($totalGroupsArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                next;
            }
            $cmd = "
                INSERT INTO rights (usergroupid, ActionId, groupid)
                SELECT '$id', '1', server_groups.id
                FROM server_groups
                WHERE server_group = '$value'";
            run_sql_cmd($cmd);

        }
    }

    $totalAgents = preg_replace("/<[^>]*>/", "", $totalAgents);
    if ($totalAgents != "") {
        $totalAgentsArray = explode ("\n", $totalAgents);
        foreach ($totalAgentsArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                next;
            }
        $valueArray = explode (" ", $value);
        $hostname = $valueArray[0];
        $portNumber = $valueArray[1];
            $cmd = "
                INSERT INTO rights (usergroupid, ActionId, Agentid)
                SELECT '$id', '1', agents.id
                FROM agents
                WHERE hostname = '$hostname'
                AND port = '$portNumber'";

            run_sql_cmd($cmd);
        }
    }

    log_change("Rights modified for user group $id by user ".$_SESSION['username']);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: add_new_group($categoryname, $totalAppClass)
//
//

function add_new_category($categoryname, $totalAppClass, $categorydescription, $categorytype, $functions) {


    // Default category type is User-defined
    if ($categorytype == "") { $categorytype = 2; }

    // We need to update 3 tables, category and parameter descriptions, and the links between them

    $categorydescription = str_replace ("'", "", $categorydescription);
    $cmd = "INSERT INTO categories (category, type, description) VALUES ('$categoryname', '$categorytype', '$categorydescription')";
    $result = run_sql_cmd($cmd);
    $query = "SELECT last_insert_id()";
    $result = run_query ($query);
    $newCatId = $result[0];

    if ($functions != "") {
	    $functionArray = explode (" ", $functions);
	    foreach ($functionArray as $functionid) {
	        $cmd = "INSERT INTO category_functions (categoryid,functionid) VALUES ('$newCatId','$functionid')";
	        run_sql_cmd($cmd);
	    }
    }

    if ($totalAppClass != "") {
        $totalAppClassArray = explode (",", $totalAppClass);

        foreach ($totalAppClassArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                next;
            }
	    $valueArray = explode (":", $value);

	    $appClass = trim ($valueArray[0]);
	    $parmName = trim($valueArray[1]);
	    if ($parmName != "") {
		    # Specific parameters were selected
	            $cmd = "
        	        INSERT INTO parameter_categories (categoryid, parameterid)
                	SELECT categories.id categoryid, parameter_descriptions.id parameterid
	                FROM categories, parameter_descriptions
	                WHERE application_class = '$appClass'
			AND parameter = '$parmName'
	                AND categories.category = '$categoryname'";
	    }
	    else {
		    # Entire application class was selected
		    $cmd = "
        	        INSERT INTO parameter_categories (categoryid, parameterid)
                	SELECT categories.id categoryid, parameter_descriptions.id parameterid
	                FROM categories, parameter_descriptions
	                WHERE application_class = '$appClass'
	                AND categories.category = '$categoryname'";
	    }
            run_sql_cmd($cmd);
        }
    }

        log_change("Parameter category $categoryname added by ".$_SESSION['username']);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: delete_category($categoryname)
//
//

function delete_category($categoryname) {

    $cmd = "
        DELETE FROM parameter_categories
        WHERE categoryid in (
            SELECT id
            FROM categories
            WHERE category = '$categoryname'
            )";
    run_sql_cmd($cmd);

    $cmd = "
        DELETE FROM category_functions
        WHERE categoryid in (
            SELECT id
            FROM categories
            WHERE category = '$categoryname'
            )";
    run_sql_cmd($cmd);
    

    $cmd = "DELETE FROM categories WHERE category = '$categoryname'";
    run_sql_cmd($cmd);

    log_change("Parameter category $categoryname DELETED by ".$_SESSION['username']);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: update_category($categoryname, $totalAppClass, $categorydescription, $categorytype)
//
//

function update_category($categoryname, $totalAppClass, $categorydescription, $categorytype,$functions) {

    if ($categorytype == "") { $categorytype = 2; }
    $cmd = "
    DELETE FROM parameter_categories
    WHERE categoryid IN (
        SELECT id
        FROM categories
        WHERE category = '$categoryname'
    )";
    run_sql_cmd($cmd);

    $cmd = "
        DELETE FROM category_functions
        WHERE categoryid in (
            SELECT id
            FROM categories
            WHERE category = '$categoryname'
            )";
    run_sql_cmd($cmd);
    
    $cmd = "UPDATE categories
            SET category = '$categoryname',
            description = '$categorydescription',
            type = '$categorytype'
            WHERE category = '$categoryname'";
    run_sql_cmd($cmd);

    if ($functions != "") {
	    $functionArray = explode (" ", $functions);
	    foreach ($functionArray as $functionid) {
	        $cmd = "INSERT INTO category_functions (categoryid,functionid) 
			VALUES ((SELECT id from categories WHERE category ='$categoryname'),'$functionid')";
	        run_sql_cmd($cmd);
	    }
    }
    
    if ($totalAppClass != "") {
        $totalAppClassArray = explode (",", $totalAppClass);

        foreach ($totalAppClassArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                next;
            }
	    $valueArray = explode (":", $value);

	    $appClass = trim ($valueArray[0]);
	    $parmName = trim($valueArray[1]);
	    if ($parmName != "") {
		    # Specific parameters were selected
	            $cmd = "
        	        INSERT INTO parameter_categories (categoryid, parameterid)
                	SELECT categories.id categoryid, parameter_descriptions.id parameterid
	                FROM categories, parameter_descriptions
	                WHERE application_class = '$appClass'
			AND parameter = '$parmName'
	                AND categories.category = '$categoryname'";
	    }
	    else {
		    # Entire application class was selected
		    $cmd = "
        	        INSERT INTO parameter_categories (categoryid, parameterid)
                	SELECT categories.id categoryid, parameter_descriptions.id parameterid
	                FROM categories, parameter_descriptions
	                WHERE application_class = '$appClass'
	                AND categories.category = '$categoryname'";
	    }
            run_sql_cmd($cmd);
        }
    }

    log_change("Parameter category $categoryname modified by ".$_SESSION['username']);
}


/////////////////////////////////////////////////////////////////////////////////
// Function: log_change($msg)
//
//

function log_change($msg) {
    log_entry ("AUDIT: $msg");
}

/////////////////////////////////////////////////////////////////////////////////
// Function: log_entry($msg)
//
//

function log_entry($msg) {

    // These are set in the config.php
    global $MESSAGE_LOG;

    $timestamp = date('r');

    $fp = fopen("$MESSAGE_LOG","a");
    fputs($fp, "[$timestamp] $msg" . PHP_EOL);
    fclose($fp);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: log_session($operation)
//
//

function log_session($operation) {

    // Get user details
    $userId     = $_SESSION['user'];
    $username   = $_SESSION['username'];
    $userIPaddr = $_SERVER['REMOTE_ADDR'];
    $userBrowser= $_SERVER['HTTP_USER_AGENT'];

    if ($operation == "login") {
        if (!$_SESSION['logged']) {

            $_SESSION['logged'] = true;

            $log_entry = "$userIPaddr User logged in id$userId $username [$userBrowser]";

        } // else ignore multiple logins for same user session
    } elseif ($operation == "logout") {
        if ($_SESSION['logged']) {

            $_SESSION['logged'] = false;
            $log_entry = "$userIPaddr User logged out id$userId $username";
        } // else ignore multiple logouts for same user session

    } elseif ($operation == "badlogin") {
        if ($_SESSION['badlogin']) {

            $username = $_SESSION['badlogin'];
            unset ($_SESSION['badlogin']);
        }
        $log_entry = "$userIPaddr Login attempt failed for user ($username)";

    } else {
        $log_entry = "log_session(): Invalid operation $operation";
        debug_msg ($log_entry);
    }

    log_entry ($log_entry);
}


/////////////////////////////////////////////////////////////////////////////////
// Function: show_request_detail
//
//

function show_request_detail($requestId, $showAffectedHosts) {

    $detailForRequest = get_details_for_request_id ($requestId);

    if (count($detailForRequest)) {

        $detailForRequestTmp = $detailForRequest[0];
        $detailForRequestTmp = str_replace("___", "\M" , $detailForRequestTmp);
        $detailForRequestTmpArray = explode ("\M", $detailForRequestTmp);
        $userName = $detailForRequestTmpArray[1];
        $firstname= $detailForRequestTmpArray[2];
        $lastname = $detailForRequestTmpArray[3];
        $comment  = $detailForRequestTmpArray[4];
        $priority = $detailForRequestTmpArray[5];
        $changeControl = $detailForRequestTmpArray[14];
        ?>
        <table class="menu">
            <tr >
                <td class="menu"><b>RequestID</b></td>
                <td class="menu"><?php print $requestId; ?> </td>
            </tr>
            <tr>
                <td class="menu"><b>Requestor:</b></td>
                <td class="menu"><?php print "$firstname $lastname ($userName)"; ?></td>
            </tr>
            <!-- <tr>
                <td class="menu"><b>Priority:</b></td>
                <td class="menu"><?php print $priority; ?></td>
                    </tr> -->
            <tr>
                <td class="menu"><b>Change control:</b></td>
                <td class="menu"><?php print $changeControl; ?></td>
            </tr>
        </table>
        <br/>
        <fieldset style="margin-top:2em;">

            <legend>Description:</legend>
            <p><?php print $comment; ?></p>

        </fieldset>

        <br/>
        <br/>
        <?
        // We need to loop to get the hosts, groups, variable, value

        $hostTotal     = array();
        $groupTotal    = array();
        $variablevalue = array();

        foreach ($detailForRequest as $indRequest) {
            $indRequest = str_replace("___", "\M", $indRequest);
            $indRequestArray = explode ("\M", $indRequest);

            array_push ($variablevalue, $indRequestArray[6] . "___" .
                            $indRequestArray[7]. "___" .
                            $indRequestArray[8]);
            if ($indRequestArray[11] != "") {
		$applyStatus = $indRequestArray[15];
		if ($applyStatus == "1") {
			$applyStatus = "SUCCESS";
		}
		elseif ($applyStatus == "2") {
			$applyStatus = "FAILED";
		}
		elseif ($applyStatus == "3") {
			$applyStatus = "RETRYING";
		}
		else {
			$applyStatus = "SCHEDULED";
		}
			
                array_push ($hostTotal, $indRequestArray[11] . " " . $indRequestArray[12] . " " . $applyStatus . " " . $indRequestArray[16]);
            }
            if ($indRequestArray[13] != "") {
                array_push ($groupTotal, $indRequestArray[13]);
            }
        }

        sort ($hostTotal);
        $hostTotal = array_unique ($hostTotal);

        sort ($groupTotal);
        $groupTotal = array_unique ($groupTotal);

        sort ($variablevalue);
        $variablevalue = array_unique ($variablevalue);

        if(count($variablevalue)) {
            ?>
            <fieldset><legend>Rule changes</legend>
            <table class="pconfig">
                <tr>
                    <th>Variable</th>
                    <th>Operation</th>
                    <th>Value</th>
                </tr>
            <?
            foreach($variablevalue as $indVariable) {
                // We need to translate the rule
                $indVariable = str_replace ("___", "\M", $indVariable);
                $indVariableArray = explode ("\M", $indVariable);
                $indConfigVariable  = $indVariableArray[0];
                $indConfigOperation = $indVariableArray[1];
                $indConfigValue     = $indVariableArray[2];

                if (strpos ($indConfigVariable, "/EVENTSPRING/PARAM_SETTINGS/THRESHOLDS")) {
                    $value = str_replace(",", " ", $indConfigValue);
                    $valueArray = explode (" ", $value);

                    // We need to get the scan for 1 parameter

                        $scan = "UNDEFINED";

                    $stringToTranslate = "1___2___3___4___$valueArray[1]___$valueArray[2]___$valueArray[3]___$valueArray[4]___$valueArray[5]___$valueArray[6]___$valueArray[7]___$valueArray[8]___$valueArray[9]___$valueArray[10]___$valueArray[11]___$valueArray[12]___$valueArray[13]___$valueArray[14]___$valueArray[15]___$valueArray[16]___$valueArray[17]___$valueArray[18]___$valueArray[18]___$valueArray[19]___A___$scan" . "___$valueArray[0]";
                    $value = translate_threshold_to_english($stringToTranslate);
                    $indConfigValue = $indConfigValue . "<BR> Interpretation: <br>" . $value;

                }
                ?>
                <tr>
                    <td><?php print $indConfigVariable;?></td>
                    <td><?php print $indConfigOperation;?></td>
                    <td><?php print $indConfigValue; ?></td>
                </tr>
                <?php
            }
            ?>
            </table>
            </fieldset>
            <br/>
            <br/>
            <?
        }

        if(count($groupTotal)) {
            ?>
            <fieldset><legend>Affected server groups</legend>
            <?
            $groupTotalString = "<ul><li>".implode ("</li><li>", $groupTotal)."</li></ul>";
            echo "$groupTotalString <BR>";
            ?>
            </fieldset>
            <br/>
            <br/>
            <?
        }
        if(count($hostTotal)) {

            ?>
            <fieldset><legend>Affected servers</legend>
            <?
            $hostTotalString = "<ul><li>".implode ("</li><li>", $hostTotal)."</li></ul>";
            if ($showAffectedHosts) {
                ?>
                <input class="submit-button" name = "getHosts" type="button" onClick="submit();" value="Hide affected servers">
                <?php print $hostTotalString; ?>
                <BR/>
                <input type = "hidden" name = "showAffectedHosts" value = 0>
                <?php
            }
            else {
                ?>
                <input class="submit-button" name = "getHosts" type="button" onClick="submit();" value="show affected servers">
                <input type = "hidden" name = "showAffectedHosts" value = 1>
                <?php
            }



            ?>
            </fieldset>
            <?
        }

        ?>
        <br>
        <br>

        <?
    // We need to get scheduling details about this requestID
    $query = "
        SELECT
            username,
            firstname,
            lastname,
            comment,
            retries,
            scheduled_changes.date_entered,
            start_time,
            end_time
        FROM
            scheduled_changes,
            people
        WHERE requestid = '$requestId'
            AND   scheduled_changes.schedulerId = people.id";

    $result = run_query($query);

    if (count($result)) {
        $result = $result[0];
        $result = str_replace("___", "\M", $result);
        $resultArray = explode("\M", $result);
        ?>
        <fieldset><legend>Scheduling</legend>
            <table class="menu">
                <tr >
                    <td class="menu"><b>SchedulerID:</b></td>
                    <td class="menu"><?php print $resultArray[0]; ?> </td>
                </tr>
                <tr>
                    <td class="menu"> <b>Requestor:</b> </td>
                    <td class="menu"><?php print "$resultArray[1] $resultArray[2] ($resultArray[0])"; ?> </td>
                </tr>
                <tr>
                    <td class="menu"> <b>Scheduler Comments:</b> </td>
                    <td class="menu"><?php print $resultArray[3]; ?> </td>
                </tr>
                <tr>
                    <td class="menu"> <b>Scheduled at:</b> </td>
                    <td class="menu"><?php print $resultArray[5]; ?> </td>
                </tr>
                <tr>
                    <td class="menu"> <b>Deployment scheduled at:</b> </td>
                    <td class="menu"><?php print $resultArray[6]; ?> </td>
                </tr>
                <tr>
                    <td class="menu"> <b>Deployment end at:</b> </td>
                    <td class="menu"><?php print $resultArray[7]; ?> </td>
                </tr>
                <tr>
                    <td class="menu"><b>Number of retries:</b> </td>
                    <td class="menu"><?php print $resultArray[4]; ?></td>
                </tr>

            </table>
        </fieldset>
            <?
    }

    }
    else {
        ?>
        No details found <BR/>
        <?php
    }

}

/////////////////////////////////////////////////////////////////////////////////
// Function: update_pcm()
//
//

function update_pcm($requestToApprove, $rulesetName) {

    global $PCM_RULESETS_DIR;
    global $DEMO_MODE;

    $platform = $ENV["OS"];

    if ($DEMO_MODE) {
        return 0;
    }

    // Check if it is a new folder or an old one

    $rulesetNameTmp = str_replace (".cfg", "placeholder_123jkl123", $rulesetName);

    if ($platform == "Windows_NT") {

        $dir_sep = "\\";

    }
    elseif ($platform == "verify_valid_strings_for_unix_platforms") {

        // Need to validate OS string for UNIX and remove this line
        return 0;

        $dir_sep = "/";

    } else {
        debug_msg ("Unable to update PCM because platform $platform is not supported.");
        return 0;
    }

    $rulesetNameTmp = str_replace (".", $dir_sep, $rulesetNameTmp);
    $rulesetNameTmp = str_replace ("placeholder_123jkl123", ".cfg", $rulesetNameTmp);

    $rulesetNameOld = $PCM_RULESETS_DIR . $dir_sep . $rulesetNameTmp;


    // we need to get all the rules
    $newConfig = get_config_for_request ($requestToApprove);
    $newConfigString = implode ("\n", $newConfig);

    if (is_file($rulesetNameOld)) {
        ?>
        <p>Updating existing ruleset: $rulesetName </p>
        <?php
        $fileContents = file_get_contents($rulesetNameOld);
        $backupfile = $rulesetNameOld . "~";
        $fp = fopen ($backupfile, "w");
        fwrite ($fp, $fileContents);
        fclose ($fp);
        $fileContents = trim ($fileContents);
        $fileContents = trim ($fileContents, "\n");
        $newFileContent = $fileContents . ",\n" . $newConfigString;
        $newFileContent = trim ($newFileContent, ",");
        $fp = fopen ($rulesetNameOld, "w");
        fwrite ($fp, $newFileContent);
        fclose ($fp);
    }
    else {

        // New ruleset to Create We need to loop and create it
        $rulesetNameTmpArray = explode ($dir_sep, $rulesetNameTmp);
        $dirToCheck = $PCM_RULESETS_DIR;
        foreach ($rulesetNameTmpArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                continue;
            }
            $valueArray = explode ("\n", $value);
            if (preg_grep ("/.cfg$/", $valueArray)) {
                // we created all directories
                break;
            }
            $dirToCheck = $dirToCheck . $dir_sep . $value;
            if (!is_dir ($dirToCheck)) {
                mkdir ($dirToCheck);
            }
        }
        $fp = fopen ($rulesetNameOld, "w");
        $fileToWrite = "PATROL_CONFIG\n" . "$newConfigString";
        $fileToWrite = trim ($fileToWrite, ",");
        fwrite ($fp, $fileToWrite);
        fclose ($fp);
        log_change("PCM Ruleset $rulesetNameOld created by $userId for requestId $requestToApprove");

    }
    $userId = $_SESSION['username'];
    log_change("PCM Ruleset $rulesetNameOld updated by $userId for requestId $requestToApprove");
}


/////////////////////////////////////////////////////////////////////////////////
// Function: format_date()
//
//

function format_date($dateToFormat) {

    $dateToFormatArray = explode (" ", $dateToFormat);
    $dateString = $dateToFormatArray[0];
    $timeString = $dateToFormatArray[1];
    $dateStringArray = explode ("/", $dateString);
    $year = $dateStringArray[2];
    $month = $dateStringArray[0];
    $day   = $dateStringArray[1];
    $valideFormat = "$year-$month-$day $timeString";
    return $valideFormat;
}

/////////////////////////////////////////////////////////////////////////////////
// Function: schedule_request($requestId)
//
//

function schedule_request($requestId, $comments, $startTime, $endTime, $retries, $retryInterval, $changeControl, $priority, $hosts, $groups) {


    // We need to determine if the user add or deleted hosts

    // Update change_targets table
    $hosts = preg_replace("/<[^>]*>/", "", $hosts);
    $hostArray = explode ("\n", $hosts);

    // Let's create a temporary table to store the agentid, groupid

    $cmd = "DROP TABLE IF EXISTS agentlist";
    run_sql_cmd($cmd);
    $cmd = "CREATE TEMPORARY TABLE agentList (agentid int(4) primary key, groupid int(4))";
    run_sql_cmd($cmd);
    $whereHosts = "";
    if (count($hostArray)) {
        foreach ($hostArray as $value) {
            $value = trim ($value);
            if ($value == "") {
                continue;
            }
            $valueArray = explode(" ", $value);

            if ($whereHosts != "") {
                $whereHosts = $whereHosts  . " OR ";
                    $whereHosts = $whereHosts . " (hostname = '$valueArray[0]' AND port = '$valueArray[1]') ";
            }
            else {
                $whereHosts = "((hostname = '$valueArray[0]' AND port = '$valueArray[1]') ";
            }
        }
        $whereHosts = $whereHosts . ")";
        // We need to get all the
        if ($whereHosts != ")") {
            $cmd = "
                INSERT IGNORE INTO agentList (agentid, groupid)
                SELECT
                    id agentid,
                    null groupid
                FROM agents
                WHERE $whereHosts";
            run_sql_cmd($cmd);
        }
    }

    $groups = trim ($groups, ",");
    $groupArray = explode (" ", $groups);
    foreach ($groupArray as $group) {
    $group = trim ($group);
    if ($group == "") {
        continue;
    }
    // We need to get the id of the group
    $query = "SELECT id FROM server_groups WHERE server_group = '$group'";
    $groupId = run_query($query);
    $groupId = $groupId[0];

    $cmd = "
        INSERT IGNORE INTO agentList (agentid, groupid)
        SELECT
            agents.id agentid,
            $groupId groupid
        FROM
            agents,
            agent_groups,
            server_groups
        WHERE
            agents.id = agent_groups.agentid
            AND agent_groups.groupid = server_groups.id
            AND server_groups.server_group LIKE '$group%'
        GROUP BY agents.id";
       run_sql_cmd($cmd);
    }


    // The agent List table have all hosts that the user selected.
    // we need to compare the list with the one that hte user submitted

    $query = "SELECT agentid FROM change_targets WHERE requestid = '$requestId'";
    $userAgent = run_query ($query);


    $query = "SELECT agentid FROM agentList";
    $adminAgent = run_query($query);

    $adminAdd = array_diff ($adminAgent, $userAgent);
    $adminRemove = array_diff ($userAgent,$adminAgent);

    // We need to update 3 tables scheduled_changes - change_requests - change_target
    $comments = str_replace("'", " ", $comments);
    $changeControl = str_replace("'", " ", $changeControl);
    $startTime = format_date($startTime);
    $endTime = format_date($endTime);
    $schedulerId = $_SESSION['user'];


    // We first need to add the hosts that the admin added to the change_targets table
    if (count($adminAdd)) {
        $whereField = get_where_for_fields($adminAdd, "id");

        $cmd = "
            INSERT INTO change_targets (
                requestid,
                status,
                agentid)
            SELECT
                $requestId requestid,
                0 status,
                a.id agentid
            FROM agents a
            WHERE $whereField";
        run_sql_cmd($cmd);
    }

    // We need to add a new request to hold all the hosts that the admin removed

    if (count($adminRemove)) {

        $cmd = "
            INSERT INTO change_requests (
                PID,
                UserId,
                Priority,
                Status,
                ApproverId,
                Comment,
                Date_Entered,
                Date_Modified,
                Change_Control)
            SELECT
                $requestId PID,
                UserId,
                Priority,
                Status,
                ApproverId,
                Comment,
                Date_Entered,
                now() Date_Modified,
                Change_Control
            FROM change_requests
            WHERE id = $requestId";
        run_sql_cmd($cmd);

        // We need to get the new request id
        $query = "SELECT last_insert_id()";
        $result = run_query ($query);
        $newRequestId = $result[0];

        $cmd = "
        INSERT INTO request_rules (
            requestId,
            ruleId)
        SELECT
            $newRequestId requestId,
            ruleId
        FROM request_rules
        WHERE requestId = $requestId";
        run_sql_cmd($cmd);

        // Now we need to update the change target table to make the one removed point to the new request
        $whereField = get_where_for_fields($adminRemove, "id");

        $cmd = "UPDATE change_targets
        SET requestid = $newRequestId
        WHERE requestid = '$requestId'
        AND agentid in (
            SELECT id
            FROM agents
            WHERE $whereField
            )";
        run_sql_cmd($cmd);
    }
    // Now let's update the scheduled_changes table

    $cmd = "
        INSERT INTO scheduled_changes (
            RequestId,
            SchedulerId,
            Retries,
            date_entered,
            date_modified,
            start_time,
            end_time,
            comment)
        VALUES (
            '$requestId',
            '$schedulerId',
            '$retries',
            now(),
            now(),
            '$startTime',
            '$endTime',
            '$comments')";
    run_sql_cmd($cmd);

    $cmd = "
        UPDATE change_requests
        SET status = '4',
            date_modified = now() ,
            change_control = '$changeControl',
            priority = '$priority'
        WHERE Id = '$requestId'";
    run_sql_cmd($cmd);

    if ($changeControl){
        log_change ("Ticket $changeControl: Request ID $requestId has been scheduled to start at $startTime and end at $endTime.");

    } else {
        log_change ("Request ID $requestId has been scheduled to start at $startTime and end at $endTime");
    }
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_category_information
//
//
function get_category_information($kmCategory) {

    $query = "
    SELECT application_class,parameter
    FROM
        categories,
        parameter_categories,
        parameter_descriptions
    WHERE
        category = '$kmCategory'
        AND categories.id = parameter_categories.categoryid
        AND parameter_categories.parameterid = parameter_descriptions.id
    GROUP BY application_class,parameter";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_category_function
//
//
function get_category_function($kmCategory) {

    $query = "
    SELECT functionid
    FROM
    	category_functions,
	categories
    WHERE
        categories.category = '$kmCategory'
	AND  categories.id = category_functions.categoryid;";
    return run_query($query);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_category_function
//
//
function get_category_function_label($kmCategory) {

    $query = "
    SELECT function_labels.function
    FROM
    	category_functions,
	categories,
	function_labels
    WHERE
        categories.category = '$kmCategory'
	AND  categories.id = category_functions.categoryid
	AND  function_labels.id = category_functions.functionid;";
    return run_query($query);

}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_category_function_for_user
//
//
function get_category_function_for_user($userId) {
    
    $query = "
    SELECT category_functions.functionid 
    FROM 
        people_groups,
        rights,
        category_functions,
        function_labels
    WHERE 
        rights.CategoryID=category_functions.categoryid
    AND people_groups.groupid=rights.Usergroupid
    AND people_groups.userid='$userId' 
    GROUP BY category_functions.functionid";
    
    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_appClass_for_param
//
//
function get_appClass_for_category($category) {



    $query = "
        SELECT
            application_class,
            app_class_alias
            
        FROM
            categories,
            parameter_categories,
            parameter_descriptions
        WHERE
            category = '$category'
            AND  categories.id = parameter_categories.categoryid
            AND parameter_categories.parameterid = parameter_descriptions.id
        GROUP BY application_class";

    return run_query($query);

}

/////////////////////////////////////////////////////////////////////////////////
// Function: remove_from_rogue($rogueAgentId)
//
//

function remove_from_rogue($rogueAgentId) {

    $cmd = "
        DELETE FROM actual_config
        WHERE
            agentid = '$rogueAgentId';";
        run_sql_cmd($cmd);

        $cmd = "DELETE FROM thresholds
        WHERE
            agentid = '$rogueAgentId';";
        run_sql_cmd($cmd);

        $cmd = "DELETE FROM loaded_kms
        WHERE
            agentid = '$rogueAgentId';";

        run_sql_cmd($cmd);
    
}


/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_ping_devices($hostToGet)
//
//

function get_all_ping_devices($hostToGet) {

   $hostToGet = explode (" ", $hostToGet);
   $hostName = $hostToGet[0];
   $port = $hostToGet[1];
   $query = "
    SELECT
        instance
    FROM
        agents,
        thresholds
    WHERE
        application_class = 'DVL_PING'
        AND display_name = '$hostName'
        AND port = '$port'
        AND agents.id = thresholds.agentid";

    return run_query($query);
}

/////////////////////////////////////////////////////////////////////////////////
// Function: get_all_processes_devices($hostToGet)
//
//

function get_all_processes_devices ($hostToGet, $parameterToGet, $appClass) {

   $hostToGet = explode (" ", $hostToGet);
   $hostName = $hostToGet[0];
   $port = $hostToGet[1];
   $query = "
    SELECT
        instance,
        border_active,
        border_min 
    FROM
        agents,
        thresholds
    WHERE
        application_class = '$appClass'
        AND display_name = '$hostName'
        AND port = '$port'
        AND parameter = '$parameterToGet'
        AND agents.id = thresholds.agentid";

    return run_query($query);
}

function get_user_request() {

    $query = "
     SELECT 
        PEOPLE.id,
        USERNAME,
        FIRSTNAME,
        LASTNAME
     FROM
        people, 
        change_requests,
        change_targets
     WHERE 
        people.id=change_requests.userid
        AND change_requests.id=change_targets.requestid
        
     GROUP by people.username";

     return run_query($query);

}

function get_group_request() {

    $query = "
     SELECT 
        user_groups.id,
        USER_GROUP
     FROM
        user_groups,
        people_groups,
        people, 
        change_requests 
     WHERE 
        change_requests.userid=people.id
        AND people.id=people_groups.userid
        AND people_groups.groupid=user_groups.id
        
     GROUP by USER_GROUP";

     return run_query($query);

}

function get_host_request() {

    $query = "
     SELECT 
        agents.id,
        display_name
     FROM
        change_targets,
        agents 
     WHERE 
        change_targets.agentid=agents.id

     GROUP by display_name";

     return run_query($query);

}

function get_requests_audit($user,$host,$status) {

    $whereField = "agents.id = change_targets.AgentID";
    if ($user != "0") {
        $whereField = $whereField . " AND people.id='$user'";
    }
    if ($host != "0") {
        $whereField = $whereField . " AND agents.id='$host'";
    }
    if ($status != "ALL") {
        $whereField = $whereField . " AND change_targets.status='$status'";
    }
    
    $query="
        SELECT
            username,
            firstname,
            lastname,
            agents.display_name,
            agents.port,
    	    change_targets.status,
            variable,
            operation,
            value,
            change_targets.date_modified,
            change_control,
            comment
        FROM
            people,
            change_requests,
            request_rules,
            rules,
            change_targets,
            agents
        WHERE
            people.id = change_requests.UserID
            AND change_requests.ID = request_rules.requestid
            AND request_rules.ruleid = rules.id
            AND change_targets.RequestID = change_requests.id
            AND $whereField 
         ORDER BY change_targets.date_modified DESC";

     return run_query($query);

}

function format_display_rules($totalRules) {
    
    # We need to merge the process add rules into 1 rule and keep track of the rule id
    $processRules = preg_grep ("/ProcessConfigurationList/", $totalRules);
    $otherRules = preg_grep ("/ProcessConfigurationList/", $totalRules, PREG_GREP_INVERT);

    # Let's get all the processes that were added
    $addedProcess = preg_grep ("/ProcessConfigurationList\/child_list/", $processRules);
    
    foreach ($addedProcess as $indProcess) {
        $indProcess = str_replace ("___", "\M", $indProcess);
        $indProcessArray = explode ("\M", $indProcess);
        $indProcessName = $indProcessArray[1];
        if (preg_grep ("/DELETE/", $indProcessArray)) {
                $indRuleId = $indProcessArray[3];
                $newRule = $indProcessName . "___" . "$indProcessName" . "___" . "Process Delete" . "___" . $indRuleId . "___" . "DELETE";
                $otherRules[] = $newRule;
        }
        else {
            $processGrep = str_replace ("^", "\^", $indProcessName);
            $processGrep = str_replace ("$", "\\$", $processGrep);
            $allIndProcess = preg_grep ("/$processGrep/", $processRules);
            $allIndProcess = preg_grep ("/DELETE/", $allIndProcess, PREG_GREP_INVERT);
            $mergeRuleId = "";
            foreach ($allIndProcess as $indRule) {
                $indRule = str_replace ("___", "\M", $indRule);
                $indRuleArray = explode ("\M", $indRule);
                $indRuleId = $indRuleArray[3];
                if (($indRuleId != "") && (is_numeric($indRuleId))) {
                    $mergeRuleId = $mergeRuleId . " " . $indRuleId;
                }
            }
            $mergeRuleId = trim ($mergeRuleId);
            
            if ($mergeRuleId != "") {
                # We need to create the new rule
                $indProcessName = str_replace ("_NO_ARGUMENT", "", $indProcessName);
                $newRule = $indProcessName . "___" . "$indProcessName" . "___" . "Process Add" . "___" . $mergeRuleId . "___" . "ADD";
                $otherRules[] = $newRule;
            }
       }
    }
    return $otherRules;

}

function get_all_added_processes() {

    $query = "SELECT 
                value 
              FROM 
                rules,
                request_rules 
              WHERE variable = '/PSX__P4WinSrvs/PWK__PKMforMSWinOS_config/ProcessMonitoring/ProcessConfigurationList/child_list' 
              AND   operation='MERGE'
              AND rules.id=request_rules.ruleid 
              GROUP by value";
     return run_query($query);
}
/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/

?>
