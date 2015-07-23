<fieldset>
 <legend>Optional Template Selection</legend>
     <?
     $totalHosts = get_all_hosts_for_user($_SESSION['user']);
     $totalHostProcess = get_all_hosts_with_appClass("NT_PROCESS_GROUP");
     $totalHosts = array_intersect ($totalHostProcess, $totalHosts);
     sort($totalHosts);
     
     $allAgentsString = implode (",", $totalHosts);
     $allAgentsString = str_replace("___", " ", $allAgentsString);
     ?>
     <div align = "left">
     <p class="it">
     By Selecting a host you can see all the processes this host is monitoring with the corresponding count
     </p>

     <input class="requestid-box" name="hostTemplate" value = "<?php print "$hostTemplate"; ?>">
     <input class="submit-button2" name = "validateAgent" type="button" onClick="get_host_settings('<?php print "$allAgentsString"; ?>');" value="Get host settings">

     <br/>
     <SELECT name = "host" size = "5" onChange="populate_hostTemplate()">

     <?
     foreach ($totalHosts as $value) {

         $value = str_replace("___", " ", $value);
         if ($value == $host) {
             ?>

             <option value =<?php print "\"$value\" SELECTED>$value";
         }
         else {
             ?>
             <option value =<?php print "\"$value\">$value";
         }
     }
     ?>

     </SELECT>
     </div>
     <?

     if ($hostTemplate) {
         # Get all the threshold for that parameter
         $processForHost = get_all_processes_devices($hostTemplate, "PROCActiveProcessCount", "NT_PROCESS_GROUP");
         $processForHostDis = implode ("\n", $processForHost);
         $processForHostDis = trim ($processForHostDis);
         if (count($processForHost)) {
             echo ("<b> All Processes that are being monitored on $hostTemplate are shown in the section below (the ones that have the process count enable)</b>");
         }
         else {
             echo "<b>host: $hostTemplate is not monitoring any processes</b>";
         }

     }
     else {
        $processForHost = array();   
     }
     ?>


<br>
</fieldset>
<br>
<br>
<fieldset>
    <legend>Process Count Selection</legend>
    <br>
    <p> In order to create a change for a monitored process, please click on the corresponding checkbox and select the process count value </p>
    <?
        # We need to get all the processes that this user have access to
        $totalHosts = get_all_hosts_for_user($_SESSION['user']);
        $totalInstance = get_all_processes_threshold ($totalHosts, "PROCActiveProcessCount", "NT_PROCESS_GROUP");

        $count=0;
        if (count($totalInstance)) {
            # We need to list them all in a table
            echo "<TABLE>";
            echo "<TR><TH>MODIFY</TH><TH>PROCESS NAME</TH><TH>PROCESS COUNT</TH></TR>";
            foreach ($totalInstance as $value) {
                $count++;    
                $valueArray = str_replace ("___", "\M", $value);
                $valueArray = explode ("\M", $valueArray);
                $processName = $valueArray[0];
                $processDisplay = str_replace ("_group", "", $processName);
                $processDisplay = str_replace ("$", "", $processDisplay);
                $processDisplay = str_replace ("^", "", $processDisplay);
                $processActive = $valueArray[1];
                $processCount = $valueArray[2];
                $indProcessForHost = preg_grep ("/$processName/", $processForHost);
                if (count ($indProcessForHost)) {
                    foreach ($indProcessForHost as $uniqueProcess) {
                        $uniqueProcess = str_replace ("___", "\M", $uniqueProcess);
                        $uniqueProcess = explode ("\M", $uniqueProcess);
                        if ($uniqueProcess[0] == $processName) {
                            $processCount = $uniqueProcess[2];

                        }
                        
                    }
                    ?>
                    <TR><TD><INPUT class="checkbox" TYPE="CHECKBOX" CHECKED NAME ="list<?print($count)?>" value="<? print($processName)?>" onClick = "determine_process(document.forms[0].list<?print($count)?>, '<? print ($count) ?>', '0')"></TD><TD><? print ($processDisplay) ?></TD><TD id = row<? print ($count)?>><input class="threshold" name="processCountPicked[]" size = 2 type="number" value="<?print ($processCount)?>" ></TD></TR>
                    <?
                }
                else {
                    ?>
                <TR><TD><INPUT class="checkbox" TYPE="CHECKBOX" NAME ="list<?print($count)?>" value="<?print($processName)?>" onClick = "determine_process(document.forms[0].list<?print($count)?>, '<? print ($count) ?>', '0')"></TD><TD><? print ($processDisplay) ?></TD><TD id = row<? print ($count)?>><input class="threshold" name="processCountPicked[]" size = 2 type="number" value="<?print ($processCount)?>" ></TD></TR>
                    <?
                }
          }
          echo "</TABLE>";
      }
      else {
      ?>
          <h3>NO PROCESSES DEFINED</h3>
      <?
      }
      $processCheckbox = 1;
      while ($processCheckbox <= $count) {

        ?>    
        <SCRIPT>
        determine_process(document.forms[0].list<? print($processCheckbox)?>, '<? print ($processCheckbox) ?>','1')
        </SCRIPT>
        <?
        $processCheckbox++;
      }

    # need to add a process to add      
    ?>
    </fieldset>
    <br>
    <br>
    <fieldset>
    <legend>Add/Delete processes</legend>
    <br>
    <p>To add a new process for monitoring, please enter it here:<p>
    <TABLE>
    <TR><TD>Process Name: </TD><TD>Process Count: </TD><TR>
    <TR><TD><input class="username-box" name="newProcess" size = 2></TD>
    <TD><input class="threshold" name="newCount" size = 2 type="number"></TD>
    </TABLE>
    <br/>
    <br>
    <?
        $addedProcess = get_all_added_processes();
        if (count($addedProcess)) {
        ?>
                <p>To delete a process previously added, please select it from the list:</p>
         
                <SELECT name = "processToDelete[]" size = "5" multiple>
                <?
                foreach ($addedProcess as $indProcessToDelete) {
                    $indProcessToDelete = trim ($indProcessToDelete);
                    if ($indProcessToDelete == "") {
                        continue;
                    }
                    $indProcessToDeleteDisp = str_replace ("_NO_ARGUMENT", "", $indProcessToDelete);
                    $indProcessToDeleteDisp = str_replace ("$", "", $indProcessToDeleteDisp);
                    $indProcessToDeleteDisp = str_replace ("^", "", $indProcessToDeleteDisp);
                    echo "<option value =\"$indProcessToDelete\">$indProcessToDeleteDisp";       
                }
                echo "</SELECT>";
        }
        else {
            ?>
                <INPUT type="HIDDEN" name="processToDelete">
            <?
        }
     ?>
                
    </fieldset>
    <br/>
    <br/>
    <br/>

<INPUT type="HIDDEN" name="oneSelected">
<INPUT type="HIDDEN" name="count" value="<?print($count)?>">
