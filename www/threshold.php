<BR/>
<fieldset>
    <legend>Selection Criteria</legend>

    <?
    $functionChosen = "1";
    include "getParameterDisplay.php";
    ?>

    <br/>
    <i><br/>Parameter Description: <b><?php print "$paramDescription"; ?></i></b>
    <br>
    </fieldset>
    <br/>
    <br/>
    <fieldset>
    <legend>Optional Template Selection</legend>
    <?
    $totalHosts = get_all_hosts_for_user($_SESSION['user']);
    $allAgentsString = implode (",", $totalHosts);
    $allAgentsString = str_replace("___", " ", $allAgentsString);
    ?>
    <div align = "left">
    <p class="it">
    By Selecting a host you can see the thresholds that already exists for the selected parameter on the selected host. Note that changes will not be deployed to the selected host in this step. You will need to go to Step 2 in order to deploy the changes.
    
    </p>

    <input class="requestid-box" name="hostTemplate" value = "<?php print "$hostTemplate"; ?>">
    <input class="submit-button2" name = "validateAgent" type="button" onClick="get_host_settings('<?php print "$allAgentsString"; ?>');" value="Get host settings">

    <br/>
    <SELECT name = "host" size = "5" onChange="populate_hostTemplate()">

    <?
    foreach ($totalHosts as $value) {

        $value = str_replace("___", " ", $value);
        $valueDisp = explode (" ", $value);
        $valueDisp = $valueDisp[0];
        if ($valueDisp == "") {
            continue;
        }
        $valueDisp = trim ($valueDisp, "'");
        if ($value == $host) {
            ?>

            <option value =<?php print "\"$value\" SELECTED>$valueDisp";
        }
        else {
            ?>
            <option value =<?php print "\"$value\">$valueDisp";
        }
    }
    ?>

    </SELECT>
    </div>
    <?

    if ($hostTemplate && $param) {
        # Get all the threshold for that parameter
        $instanceChosen = $_POST["instance"]; 
        if (count($instanceChosen)) {
            $instanceChosen = $instanceChosen[0];
        }
        else {
            $instanceChosen = ""; 
        }
        if ($instanceChosen == "__ANYINST__") {
            $instanceChosen = ""; 
        }
        $thresholdForHost = get_all_threshold_for_host($hostTemplate, $param,$instanceChosen);
        ?>

        <div style="float:left;border:1px solid #333;width:50%">
            <TABLE style="width:100%" class="menu">
            <?php

            if (count($thresholdForHost)) {
                $statement = translate_threshold_to_english($thresholdForHost[0]);
                # We need to save the settings 
                $thresholdSettings = str_replace ("___", "\M", $thresholdForHost[0]);
                $valueArray = explode ("\M", $thresholdSettings);
                $alarm3Active = $valueArray[4];
                $alarm3Min = $valueArray[5];
                $alarm3Max = $valueArray[6];
                $alarm3trigger = $valueArray[7];
                $alarm3Cycle = $valueArray[8];
                $alarm3State = $valueArray[9];
                $alarm1Active = $valueArray[10];
                $alarm1Min = $valueArray[11];
                $alarm1Max = $valueArray[12];
                $alarm1trigger = $valueArray[13];
                $alarm1Cycle = $valueArray[14];
                $alarm1State = $valueArray[15];
                $alarm2Active = $valueArray[16];
                $alarm2Min = $valueArray[17];
                $alarm2Max = $valueArray[18];
                $alarm2trigger = $valueArray[19];
                $alarm2Cycle = $valueArray[20];
                $alarm2State = $valueArray[21];

                if ($alarm3Cycle == "0") {$alarm3Cycle = "1";};
                if ($alarm2Cycle == "0") {$alarm2Cycle = "1";};
                if ($alarm1Cycle == "0") {$alarm1Cycle = "1";};
                if (($alarm3Active == "0") || ($alarm3State == "0")) {
                    $alarm3Min = "";
                    $alarm3Max = "";
                    $alarm3Cycle = "";
                    $alarm3trigger = "";
                    $alarm3Active = "";
                    $alarm3State = "";
                }
                if (($alarm2Active == "0") || ($alarm2State == "0")) {
                    $alarm2Min = "";
                    $alarm2Max = "";
                    $alarm2Cycle = "";
                    $alarm2trigger = "";
                    $alarm2Active = "";
                    $alarm2State = "";
                }
                if (($alarm1Active == "0") || ($alarm1State == "0")) {
                    $alarm1Min = "";
                    $alarm1Max = "";
                    $alarm1Cycle = "";
                    $alarm1trigger = "";
                    $alarm1Active = "";
                    $alarm1State = "";

                }

                $instanceChosen=explode (";", $instanceChosen);
                $instanceChosen=$instanceChosen[0];
                if ($instanceChosen != "") {
                    $statement ="<b>Host=$hostTemplate, instance=$instanceChosen, parameter=$param</b><br><br>". $statement;
                }
                else {
                    $statement ="<b>Host=$hostTemplate, parameter=$param</b><br><br>". $statement;
                }
                ?>
                <tr><td nowrap><?php print "$statement"; ?></td></tr>
                <?php
            }
            else {
                    $alarm3Active = ""; $alarm3Min = ""; $alarm3Max = ""; $alarm3trigger = ""; $alarm3Cycle = ""; $alarm3State = "";
                    $alarm1Active = ""; $alarm1Min = ""; $alarm1Max = ""; $alarm1trigger = ""; $alarm1Cycle = ""; $alarm1State = "";
                    $alarm2Active = ""; $alarm2Min = ""; $alarm2Max = ""; $alarm2trigger = ""; $alarm2Cycle = ""; $alarm2State = "";
                ?>
                <tr><td>No Threshold defined for <?php print "$param on $hostTemplate"; ?></td></tr>
                <?php
            }

            ?>
            </TABLE>
        </div>
        <?php

    }
    else {
          $alarm3Active = ""; $alarm3Min = ""; $alarm3Max = ""; $alarm3trigger = ""; $alarm3Cycle = ""; $alarm3State = "";
          $alarm1Active = ""; $alarm1Min = ""; $alarm1Max = ""; $alarm1trigger = ""; $alarm1Cycle = ""; $alarm1State = "";
          $alarm2Active = ""; $alarm2Min = ""; $alarm2Max = ""; $alarm2trigger = ""; $alarm2Cycle = ""; $alarm2State = "";
    }
    ?>

</fieldset>
<br/>
<br/>
<br/>


<fieldset>
    <legend>Threshold Selection</legend>


    <P>
    <!-- <a href="getdetails.php?changeType=1" target="_blank"> -->
    Choose up to three threshold combinations. Regular alarm ranges must not overlap each other nor intersect boundary (if boundary is active.)
    <br>

     </p>

    <div>
        <input class="checkbox" TYPE="CHECKBOX" NAME ="paramEnable" value = "1" CHECKED onClick = "hide_alarm_range(document.forms[0])">
        <i>Enable parameter </i>&nbsp;<strong><?php echo "$param</strong> <i>of class</i> <strong>$appClass</strong>"; ?>

    </div>

    <fieldset class="inset-threshold">
    <?
        if ($alarm1Active != "1") {
        ?> 
        <input class="checkbox" TYPE="CHECKBOX" NAME ="list1" value = "1" onClick = "determine_message(document.forms[0].list1, '1')">
        <?
        } else {
        ?>
            <input class="checkbox" TYPE="CHECKBOX" NAME ="list1" CHECKED value = "1" onClick = "determine_message(document.forms[0].list1, '1')">
        <?
        }    
        ?>
        <i>Enable Alarm1 threshold  for <strong><?php echo "$param</strong>"; ?></i>
        <TABLE class="menu2">
            <TR id = row1>
                <TD class="t2">
                <SELECT name = "alarm1Status">
                    <?
                        if ($alarm1State == "1") {
                        ?>
                            <OPTION value = "2">Alarm
                            <OPTION value = "1" SELECTED>Warning
                        <?
                        }
                        else {
                        ?>
                            <OPTION value = "2" SELECTED>Alarm
                            <OPTION value = "1">Warning
                        <?
                        }
                        ?>
                </SELECT>
                </TD>

                <TD class="t1" ID = param>when value is</TD>

                <TD class="t2">
                <SELECT name = "alarm1range" onChange = "check_equal('1')">
                    <OPTION value = "0">between
                    <OPTION value = "1">equal to
                </SELECT>
                </TD>

            <TD class="t3"><input class="threshold" name="alarm1Min" size = 2 type="number" value="<?print ($alarm1Min)?>" ></TD>

                <TD id = col11 class="t3">and</TD>

                <TD id = col21 class="t3"><input class="threshold" name="alarm1Max" size = 2 type="number" value="<?print ($alarm1Max)?>"></TD>

                <TD class="t3">for</TD>

                <TD class="t3"><input class="consecutive" name="alarm1Cycle" size = 1 type=number value="<?print ($alarm1Cycle)?>"></TD>

                <TD class="t1">consecutive cycles.</TD>
            </TR>
        </TABLE>
    </fieldset>
    <br>
    <fieldset class="inset-threshold">
        <?
        if ($alarm2Active != "1") {
        ?> 
            <input class="checkbox" TYPE="CHECKBOX" NAME ="list2" value = "1" onClick = "determine_message(document.forms[0].list2, '2')">
        <?
        }
        else {
        ?> 
            <input class="checkbox" TYPE="CHECKBOX" NAME ="list2" value = "1" CHECKED onClick = "determine_message(document.forms[0].list2, '2')">
        <?
        }
        ?>
        <i>Enable Alarm2 threshold for <strong><?php echo "$param</strong>"; ?></i>
        <TABLE class="menu2">
            <TR id = row2>
                <TD class="t2">
                <SELECT name = "alarm2Status">
                    <?
                        if ($alarm2State == "1") {
                        ?>
                            <OPTION value = "2">Alarm
                            <OPTION value = "1" SELECTED>Warning
                        <?
                        }
                        else {
                        ?>
                            <OPTION value = "2" SELECTED>Alarm
                            <OPTION value = "1">Warning
                        <?
                        }
                        ?>

                </SELECT>
                </TD>

                <TD class="t1" ID = param>when value is</TD>
                <TD class="t2">
                <SELECT name = "alarm2range" onChange = "check_equal('2')">
                    <OPTION value = "0">between
                    <OPTION value = "1">equal to
                </SELECT>
                </TD>

                <TD  class="t3"><input class="threshold" name="alarm2Min" size = 5 TYPE = "number" value="<?print ($alarm2Min)?>" ></TD>

                <TD id = col12 class="t3">and</TD>

                <TD id = col22 class="t3"><input class="threshold" name="alarm2Max" size = 5 TYPE = "number" value="<?print ($alarm2Max)?>" ></TD>

                <TD class="t3">for</TD>

                <TD class="t3"><input class="consecutive" name="alarm2Cycle" size = 1 TYPE = "number" value="<?print ($alarm2Cycle)?>" ></TD>

                <TD class="t1">consecutive cycles.</TD>
            </TR>
        </TABLE>
    </fieldset>
    <br/>

    <fieldset class="inset-threshold">
    <?
        if ($alarm3Active != "1") {
            ?> 
            <input class="checkbox" TYPE="CHECKBOX" NAME ="list3" value = "1"  onClick = "determine_message(document.forms[0].list3, '3')">
        <? 
        }
        else {
            ?>
            <input class="checkbox" TYPE="CHECKBOX" NAME ="list3" value = "1" CHECKED onClick = "determine_message(document.forms[0].list3, '3')">
            <?

        }
        ?>
        <i>Enable Border threshold for <strong><?php echo "$param</strong>"; ?></i>

        <TABLE class="menu2" id = "tableId">
            <TR id = row3>
                <TD class="t2">
                <SELECT name = "alarm3Status">
                    <?
                        if ($alarm3State == "1") {
                        ?>
                            <OPTION value = "2">Alarm
                            <OPTION value = "1" SELECTED>Warning
                        <?
                        }
                        else {
                        ?>
                            <OPTION value = "2" SELECTED>Alarm
                            <OPTION value = "1">Warning
                        <?
                        }
                        ?>
                </SELECT>
                </TD>

                <TD class="t1" ID = param>when value is <b>not</b></TD>

                <TD class="t2">
                <SELECT name = "alarm3range" onChange = "check_equal('3')">
                    <OPTION value = "0">between
                    <OPTION value = "1">equal to
                </SELECT>
                </TD>

                <TD class="t3"><input class="threshold" name="alarm3Min" size = 2 TYPE = "number" value="<?print ($alarm3Min)?>"></TD>

                <TD id = col13 class="t3">and</TD>

                <TD id = col23 class="t3"><input class="threshold" name="alarm3Max" size = 2 TYPE = "number" value="<?print ($alarm3Max)?>"></TD>

                <TD class="t3">for</TD>

                <TD class="t3"><input class="consecutive" name="alarm3Cycle" size = 1 TYPE = "number" value="<?print ($alarm3Cycle)?>"></TD>

                <TD class="t1">consecutive cycles.</TD>
            </TR>
        </TABLE>
    </fieldset>
</fieldset>
            <SCRIPT>
            determine_message(document.forms[0].list1, '1');
            determine_message(document.forms[0].list2, '2');
            determine_message(document.forms[0].list3, '3');
            </SCRIPT>
<br/>
<br/>
<br/>
<br/>
<?php
