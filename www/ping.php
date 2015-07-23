<fieldset>
 <legend>Optional Template Selection</legend>
     <?
     $totalHosts = get_all_hosts_for_user($_SESSION['user']);
     $totalHostApp = get_all_hosts_with_appClass("DVL_PING");
     $totalHosts = array_intersect ($totalHostApp, $totalHosts);
     sort($totalHosts);
     $allAgentsString = implode (",", $totalHosts);
     $allAgentsString = str_replace("___", " ", $allAgentsString);
     ?>
     <div align = "left">
     <p class="it">
     By Selecting a host you can see all the devices this host is pinging
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
         $pingForHost = get_all_ping_devices($hostTemplate);
         $pingForHost = implode ("\n", $pingForHost);
         $pingForHost = trim ($pingForHost);
         if ($pingForHost != "") {
             echo ("<b> Devices pinged from $hostTemplate are: " . $pingForHost . "</b>");
         }
         else {
             echo "<b>host: $hostTemplate is not pinging any devices</b>";
         }

     }
     ?>


<br>
</fieldset>
<br>
<br>
<fieldset>
    <legend>Ping Selection</legend>
    <br/>
    <p> Define a host for availability checking with the ping command.  <br/>
    <br/>Provide the IP address or host name on seperate lines<br/>
    </p>

    <label for = "ping_checker">Hosts</label>
   <textarea name=pingHosts cols="10" rows="4"><?print ($pingForHost) ?></textarea>


    <br>

    <br>
        <H3>Actions</H3>
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="MERGE" CHECKED>Add new devices to existing list<br/>
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="REPLACE">Replace existing lists with the entered value<br/>
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="DELETE">Remove the existing list<br/>


    <br/>
    <br/>
    </fieldset>
    <br/>
    <br/>
    <br/>

<INPUT type="HIDDEN" name="starttimeseconds">
