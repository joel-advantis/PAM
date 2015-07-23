<?

/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Rule Audits
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

include "header.php";
?>
<div id="centercontent" class="main">
    <form name = "defineReport" method = "post">
    <script src="advantis-common.js"></script>
    <h2>Patrol Audit Reports</h2>
    <fieldset>

        <!-- Feature under construction
            <h3><a href = "javascript:pcm_submit_report(11)">Show Agent/Group relationships</a></h3>
            <i>Report detailing what PCM groups a given PatrolAgent resides in and the list of rulesets that apply to the groups  </i> <BR>
        -->
        <h3><a href = "javascript:pcm_submit_report(12)">Rogue Agents</a></h3>
        <i> Audit report showing PatrolAgents active in the environment but not found in PCM </i>
        <BR>
        <h3><a href = "hostSelection.php?reportType=3">Compliance Report</a>
        </h3><i>Audit of agents whose actual configurations are not synchronized with the rules</i>
        <BR>
        <h3><a href = "hostSelection.php?reportType=4">Agent and OS Versions</a>
        </h3><i>OS Versions and PatrolAgent versions for selected servers </i>
        <BR>
        <h3><a href = "hostSelection.php?reportType=5">Misconfigured Agents</a>  
        </h3><i>Audit of key configuration variables for agents (missing preloaded variable, rtserver variable) </i>
        <BR>
        <h3><a href = "hostSelection.php?reportType=6">Stale data points</a>  
        </h3><i>Audit of agents with potentially hung collectors</i>
        <BR>
        <h3><a href = "hostSelection.php?reportType=2">Alertable Metrics (with threshold definitions)</a>
        </h3><i>Parameters which will generate events/notification if existing thresholds settings are crossed </i>
        <BR>
        <h3><a href = "javascript:pcm_submit_report(13)">Statistics</a></h3>
        <i> Audit report showing statistics of PAM (Number of rules, agents ...)</i>
        <BR>
        <h3><a href = "javascript:pcm_submit_report(10)">Duplicate Rules</a></h3>
        <i>Report listing rules duplicated within Patrol Configuration Manager that could cause a conflict in PatrolAgent configuration </i> 
        <br/>
        <br/>
        Select Filter String to exclude (Optional)
        <br/>
        <div align = "left">
        <input class="requestid-box" type="input" name="filterList">

        <BR><BR>
        <BR><BR>
        <BR><BR>
        <br/>
    </form>
        <form action="generateConfigReport.php" target="_blank" method="post" name="pcmForm">
        <INPUT TYPE="hidden" NAME="reportType">
         <INPUT TYPE="hidden" NAME="filterList">
        <BR><BR>
    </fieldset>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>
    <BR><BR>

    </form>
</div>
</body>
</html>
<!--
/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/
-->
