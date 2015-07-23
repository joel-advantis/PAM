<?
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Message Wording Macro Descriptions
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

session_start();
include "config/config.php";
include "advantis-functions.php";
include "config/jefferson.css";

$changeType = $_GET["changeType"];
?>
<html>
<head>
<?
echo "<title>Macro Description</title>";
?>

<script>
function changeScreenSize(w,h) {
    window.resizeTo( w,h )
}
</script>

</head>

<?
if ($changeType == "1") {
?>
<body onload="changeScreenSize(700,200)" class="report">
<fieldset>
<legend>Threshold constraints</legend>
<p>
<pre>
Regular alarm ranges must not overlap each other
nor intersect boundary (if boundary is active.)
</pre>
</p>
</fieldset>
</body>
</html>
<?
}
elseif ($changeType == "2") {
?>
<body onload="changeScreenSize(1000,800)" class="report">
<fieldset>
<legend>Message Wording Macro Variables</legend>
<p>
<pre>
Use the following message variables to help create a meaningful message that anyone can understand.

%HOSTNAME%         - Hostname (e.g., hrdbprod01)
%IPADDRESS%        - IP Address (e.g., 192.168.1.1)
%APPCLASS%         - Application Class Name (e.g., ORACLE)
%APPINSTANCE%      - Instance Name (e.g., PROD1)
%ICON_NAME%        - Instance Icon Name (e.g., PROD1)
%PARENT_INSTANCE%  - Parent Instance Name (e.g., /ORACLE/ORACLE)
%PARAMETER_NAME%   - Parameter Name (e.g., CPUBusy)
%PARAMETER_STATUS% - Parameter Status (e.g., ALARM,WARN,OK)
%PARAMETER_VALUE%  - Parameter Value (e.g., 99.65)
%DATE%             - Date of Alert (MM/DD/YYYY)
%TIME%             - Time of Alert (HH:MM:SS)
%TIMEZONE%         - Time Zone on affected System (e.g., 'US/Eastern/EDT')
%LAST10%           - Last 10 Parameter Values (e.g., 1.00 2.00 3.00 4.00 5.00 etc.)
%AVE10%            - Average of last 10 Parameter Values (e.g., 3.00)
%LAST10TS%         - Last 10 Parameter Timestamps (e.g., 957359389 957359395 957359399 etc.)
%LAST10TP%         - Overall Time Period, in minutes, of LAST10 (e.g., 50.00)
%EVENT_ID%         - Event Manager's Event Id for the alert (e.g., 8765)
%USERDEFINED%      - Value of variable '/_my_%APPCLASS%_%APPINSTANCE%_%PARAMETER_NAME%'
%NOTIFY_EVENT_ID%  - Event Manager's Event Id for the NOTIFY_EVENT (e.g., 8766)
%EVENT_TYPE%       - Event Manager's Event Type for the alert (e.g., ALARM)
%EVENT_STATUS%     - Event Manager's Event Status for the alert (e.g., OPEN)
%OS_TYPE%          - Operating System type (e.g., NT, SOLARIS)
%ALARM_MIN%        - Lower threshold of current alarm range (e.g., 90)
%ALARM_MAX%        - Upper threshold of current alarm range (e.g., 100)
%CUSTOM_ID1%       - Custom identifier assigned to object
%CUSTOM_ID2%       - Custom identifier assigned to object


For example, if you entered:

The Oracle server %APPINSTANCE% on %HOSTNAME% is consuming %PARAMETER_VALUE%% CPU (Ave CPU=%AVE10%%).

The message you would see is:

The Oracle server PROD1 on glamis is consuming 99.55% CPU (Ave CPU=76.54%).
</pre>
</p>
</fieldset>
</body>
</html>
<?
}

/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/

