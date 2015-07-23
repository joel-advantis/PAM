<?
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Metric Definition Reports
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

include "header.php";
?>
<div id="centercontent" class="main">
<h2>Metric Definitions</h2> 
<p> Knowledge Module (KM) parameters with a description of the functionality of each one </p>

<form action="<? print $PHP_SELF; ?>" method="post">

<fieldset>
<legend>Parameter Selection </legend>
<br/>
<?

# Get all Categories that exist in PAM
$totalCategory = get_total_category();

if (count ($totalCategory)) {

    ?>
    <TABLE> 
    <TR><TH>CATEGORY</TH><TH>PARAMETER</TH></TR>
    <TR><TD>
    <SELECT  name = "kmCategory" onChange="submit()">
    <?
    $kmCategorySet = 0;

    foreach ($totalCategory as $indCategory) {
        $indCategory = trim ($indCategory);
        $indCategory = str_replace("___", "\M", $indCategory);
        $indCategoryArray = explode ("\M", $indCategory);
        $indCategory = $indCategoryArray[0];
        if ($indCategory == "") {
            continue;
        }

        # Previous selected options
        if ($kmCategory == $indCategory) {
            echo "<OPTION value = \"$indCategory\" SELECTED>$indCategory";
            $kmCategorySet = 1;
        }
        else {
            echo "<OPTION value = \"$indCategory\">$indCategory";
        }

        # If first time, set the category to the first one found
        if (!$kmCategory) {
            $kmCategory = $indCategory;
        }
    }
    ?>
    </SELECT>
    </TD>
    <?
}
else {
    echo "No Category entered <BR>";
    exit;
}
if ($kmCategory) {

    # When a category get selected, display the paramter under it
    $totalParameters = get_total_parameters($kmCategory);

    if (count ($totalParameters)) {
        ?>
        <TD>
        <SELECT  name = "paramSelected" onChange="submit()">
        <?
        array_push ($totalParameters, "SELECT_ALL");
        $parameterSet = 0;
        foreach ($totalParameters as $indParam) {

            # Previously selected one
            if ($paramSelected == $indParam) {
                echo "<OPTION value = \"$indParam\" SELECTED>$indParam";
                $parameterSet = 1;
            }
            else {
                echo "<OPTION value = \"$indParam\">$indParam";
            }
        }
        
        # If no parameter selected or if it's the first time, display them all
        if (!$parameterSet) {
            echo "<OPTION value = \"SELECT_ALL\" SELECTED>SELECT_ALL";
            $paramSelected = "SELECT_ALL";
        }
    }
    ?>
    </SELECT>
    </TD>
    </TABLE>
    <br/>
    </fieldset>
    <br/>
    <br/>
    <br/>

    <?
}

    $AllparamDescription = get_parameter_description($kmCategory, "$paramSelected");
    if ($AllparamDescription == 0) { $sumParms = count ($totalParameters); } 
    else { $sumParms = count($AllparamDescription); }

?>
<BR/>

<fieldset>
<legend>Descriptions  (<?php print howmany ($sumParms,"Parameter","Parameters") ?>)</legend>
<br/>
<br/>
<?

if ($paramSelected) {
    
    # get the description of the parameter selected, if select_all is selected display them all
    echo "<TABLE BORDER><p>";
    echo "<TR><TH>Parameter Name</TH><TH>Application Class</TH><TH>Description</TH><TH>Collector</TH></TR>";

    sort($AllparamDescription);
    foreach ($AllparamDescription as $indParamDescription) {
        $indParamDescription = str_replace ("___", "\M", $indParamDescription);
        $indParamDescriptionArray = explode ("\M", $indParamDescription);
        $indParamSelected = $indParamDescriptionArray[0];
        $indAppSelected = $indParamDescriptionArray[1];
        $paramDescription = $indParamDescriptionArray[2];
        $paramCollector   = $indParamDescriptionArray[3];
        if ($paramDescription == "") {
            $paramDescription = "No description available for $indParamSelected";
        }
        
        ?>
        <TR>
            <TD>
                <?php print $indParamSelected; ?>
            </TD>
            <TD>
                <?php print $indAppSelected; ?>
            </TD>
            <TD>
                <?php print $paramDescription; ?>
            </TD>
            <TD>
                <?php print $paramCollector; ?>
            </TD>
        </TR>
        <?php
    }
    echo "<p></TABLE>";

}
?>
</fieldset>
</form>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>

</body>
</html>
<!--
/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/
-->