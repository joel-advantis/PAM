<BR/>
<fieldset>
    <legend>Selection Criteria</legend>
    <p>By selecting a consumer Parameter, the associated collector parameter will be displayed</p>
    <?
    $functionChosen = "3";
    include "getParameterDisplay.php";
    ?>
    <br/>
    <i><?php print "$paramDescription"; ?></i>
    <br/>
<br/>
<br/>
<?

if ($category) {
    $totalCollectorParameters = get_param_coll_std($category);


    if (count ($totalCollectorParameters)) {
	
        $parameterSet = 0;
    ?>
    	Please select a Collector Parameters: <br>
        <SELECT  name = "collParam" onChange="submit()">
    <?
        foreach ($totalCollectorParameters as $indParam) {

            if ($collParam == $indParam) {
                echo "<OPTION value = \"$indParam\" SELECTED>$indParam";
                $parameterSet = 1;
            }
            else {
                echo "<OPTION value = \"$indParam\">$indParam";
            }
        }
        if (!$parameterSet) {
            $collParam = $totalCollectorParameters[0];
        }
    if ($_SESSION['consumer'] != $param) {
        $collectorForConsumer = get_collector($category, $param);
            $collectorForConsumer = $collectorForConsumer[0];
            $_SESSION['consumer'] = $param;
        echo "<OPTION value = \"$collectorForConsumer\" SELECTED>$collectorForConsumer";
        $collParam = $collectorForConsumer;
        }
    }
    ?>
    </SELECT>
    <?
    
}
else {
    echo "No Collector available under the selected category<br>";
}
if ($collParam) {
    echo "&nbsp&nbsp ";
    $paramDescription = get_parameter_description($category, $collParam);
    if (count ($paramDescription)) {
        $paramDescription = $paramDescription[0];
        $paramDescription = str_replace ("___", "\M", $paramDescription);
        $paramDescriptionArray = explode ("\M", $paramDescription);
        $paramDescription = $paramDescriptionArray[2];
    }
    else {
        $paramDescription = "No description found for $collParam";
    }
?>
    <br/>
    <?
    $collParam = trim ($collParam);
    if ($collParam != "NA") {
    	    ?>
	    <i><?php print "$paramDescription"; ?></i>
	    <br/>
	    <br/>
	     The selected collector parameter updates the following:
            <br/>
 	    <?
	    $totalConsumerForCollector = get_consumer_for_coll($collParam);
	    if (count($totalConsumerForCollector)) {
	            echo "<ul>";
	            echo "<li>" . (implode ("<li>", $totalConsumerForCollector));
	            echo "</ul>";
	
	    }
	    else {
	        echo "No consumer are associated to the selected Collector <b>";
	    }
     }
}
    ?>
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
    By Selecting a host you can see the polltime that already exists for the selected parameter on the selected host
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

    if ($hostTemplate && $collParam) {
        # Get all polling cycle for that parameter 
        $PollForHost = get_polltime_for_host($hostTemplate, $collParam);
        ?>
        
        <div style="float:left;border:1px solid #333;width:50%">        
            <TABLE style="width:100%" class="menu">
            <?php

            if (count($PollForHost)) {
                $statement = "PollTime is $PollForHost[0] seconds";
                ?>
                <tr><td style="font-size:150%"><?php print "$statement"; ?></td></tr>
                <?php
            }
            else {
                ?>
                <tr><td style="font-size:150%">No PollTime defined for <?php print "$collParam on $hostTemplate"; ?></td></tr>
                <?php
            }

            ?>
            </TABLE>
        </div>
        <?php

    }
    ?>
    
</fieldset>
<br/>
<br/>
<br/>


<fieldset>
    <legend>PollTime Selection</legend>
    <br/>
    <div>
        <input class="checkbox" TYPE="CHECKBOX" NAME ="paramEnable" value = "1" CHECKED onClick = "hide_pollTime(document.forms[0])">           
        <i>Enable parameter</i>

    </div>
    
    <TABLE class="menu2">
        <TR id = row1>
            <TD class="menu"> Enter new polling interval (seconds): &nbsp;</TD><TD class="menu" style="width:10em;">&nbsp;<input class="input-box2" name="pollTime" size = 5 type="number" value = "<?php print "$PollForHost[0]"; ?>"></TD>
        </TR>
    </TABLE>
<br/>
<br/>
</fieldset>
<br/>
<br/>
<?php
