<?
$totalCategoryId = array();
$totalCategoryId = get_total_category_for_user($_SESSION['user']);

$totalCategory = array ();
if (count ($totalCategoryId)) {
    

    if (preg_grep ("/-1/", $totalCategoryId)) {
        $totalCategory = get_total_category();      
    }
    else {
        $totalCategoryId = implode (",", $totalCategoryId);
        $totalCategoryId = trim ($totalCategoryId, ",");
        $totalCategory = get_category($totalCategoryId,$functionChosen);            
    }
}

if (count ($totalCategory)) {
    ?>
    <br>
    <p>Select the Object you want to update<p>
    <TABLE>
    <?
    ?>
        <TR><TH>CATEGORY</TH><TH>PARAMETERS</TH><TH>APPLICATION CLASS</TH></TR>
    <?
    ?>
    <TR><TD>
    <SELECT  name = "category" onChange="submit()">
    <?
    $categorySet = 0;
    $totalCategory = preg_grep ("/ENTIRE_SERVER/", $totalCategory, PREG_GREP_INVERT);
    foreach ($totalCategory as $indCategory) {
        $indCategory = trim ($indCategory);
        $indCategory = str_replace("___", "\M", $indCategory);
        $indCategoryArray = explode ("\M", $indCategory);
        $indCategory = $indCategoryArray[0];
        if ($indCategory == "") {
            continue;
        }
        if ($category == $indCategory) {
            echo "<OPTION value = \"$indCategory\" SELECTED>$indCategory";
            $categorySet = 1;
        }
        else {
            echo "<OPTION value = \"$indCategory\">$indCategory";
        }

    }
    if (!$category) {
        $category = str_replace ("___", "\M", $totalCategory[0]);
    $categoryArray = explode ("\M", $category);
    $category = $categoryArray[0];
    
    }
    ?>
    </SELECT>
    </TD>
    <?
} else {
    ?>
    No accessible parameter categories were found.
    <BR/>
    </fieldset>
    <br/>
    <br/>
    <?
    exit;
}


if ($category) {
    $totalParameters = get_param_con_std($category);
    if (count ($totalParameters)) {
            
        ?>
        <TD>
            <SELECT  name = "param" onChange="submit()">
        <?
        $parameterSet = 0;
    foreach ($totalParameters as $indParam) {

            $indParam = str_replace ("___", "\M", $indParam);
            $indParam = explode("\M", $indParam);
            $parameterName = $indParam[0];
            $parameterAlias = $indParam[2];
            if ($parameterAlias == "") {
                $parameterAlias = $parameterName;
            }
            if ($param == $parameterName) {
                echo "<OPTION value = \"$parameterName\" SELECTED>$parameterAlias";
                $parameterSet = 1;
            }
            else {
                echo "<OPTION value = \"$parameterName\">$parameterAlias";
            }
        }
        if (!$parameterSet) {
            $param = $totalParameters[0];
            $param = str_replace ("___", "\M", $param);
            $param = explode("\M", $param);
            $param = $param[0];
        }
        ?>
        </SELECT>
        </TD>
        <?
    }
}
if ($param) {
    echo "&nbsp&nbsp ";
    $paramDescription = get_parameter_description($category, $param);
    if (count ($paramDescription)) {
        $paramDescription = $paramDescription[0];
        $paramDescription = str_replace ("___", "\M", $paramDescription);
        $paramDescriptionArray = explode ("\M", $paramDescription);
        $paramDescription = $paramDescriptionArray[2];
    }
    else {
        $paramDescription = "No description found for $param";
    }

    # Display the application class
    $totalApplicationClass = get_appClass_for_param($category, $param);
    if (count($totalApplicationClass) > 1) {
        ?>
        <TD>
        <SELECT  name = "appClass" onChange="submit()">
        <?
        foreach ($totalApplicationClass as $value) {
            $value = str_replace ("___", "\M", $value);
            $valueArray = explode("\M", $value);
            $appName = $valueArray[0];
            $appAlias = $valueArray[1];
            if ($appAlias == "") {
                $appAlias = $appName;
            }

            if ($appName == $appClass) {
                echo "<OPTION value = \"$appName\" SELECTED>$appAlias";      
            }
            else {
                echo "<OPTION value = \"$appName\">$appAlias";       
            }
        }
        ?>
        </SELECT>
        </TD>
        <?
    }
    else {
        $appClass = $totalApplicationClass[0];
        $appClassArray = str_replace ("___", "\M", $appClass);
        $appClassArray = explode("\M", $appClassArray);
        $appName = $appClassArray[0];
        $appAlias = $appClassArray[1];
        if ($appAlias == "") {
             $appAlias = $appName;
        }
        echo "<TD>$appAlias</TD>";
        $appClass = $appName;
        echo "<INPUT TYPE=\"hidden\" NAME=appClass value=\"$appClass\">";
    }
    ?>
    </TR>
    </TABLE>
    <br>
    <?
    
    # Add the Override instance
    if ($param) {
        ?>
            <TABLE>
            <TR><TH>SPECIFIC INSTANCE</TH></TR>
        <?
        $totalHosts = get_all_hosts_for_user($_SESSION['user']);
        $totalInstance = get_all_instances ($totalHosts, $param, $appClass);
        if (count($totalInstance)) {
        ?>
        <TD>
            <SELECT  name = "instance[]" multiple size="5">
        <?
            $indInstanceSet = "0";
            $instanceChosen = $_POST["instance"]; 
            if (!is_array($instanceChosen)) {
                $instanceChosen = array();
                $instanceChosen[] = "";
            }
            $totalInstance[] = "__ANYINST__";
            foreach ($totalInstance as $indInstance) {



                $indInstance = trim ($indInstance);
                if ($indInstance == "") {
                    next;
                }
                # SPECIFIC REQUIREMENT FROM EDS 
                # MAY NEED TO ADD THE CHECK TO CONFIRM IT"S NT_PERFMON COUNTER

                $indInstanceArray = explode (";", $indInstance);
                $indInstanceDisp =  $indInstanceArray[0];
                if ($indInstanceDisp == "") {
                    $indInstanceDisp = $indInstance;
                }
                # End of SPECIFIC REQUIREMENT
                #
                
                if ($indInstance == "__ANYINST__") {
                    $indInstanceDisp = "ALL";
                }
                
                if (preg_grep ("/$indInstance/", $instanceChosen)) {
                    echo "<OPTION value = \"$indInstance\" SELECTED>$indInstanceDisp";
                    $indInstanceSet = "1";
                }
                else {
                    echo "<OPTION value = \"$indInstance\">$indInstanceDisp";
                }
            }
            ?>
            </SELECT>
        </TD>
        <?
        }
    else {
        echo "<TD>N/A</TD>";
        }
    }
    else {
        echo "<TD>N/A</TD>";
    }
    ?>
     </TR>
     </TABLE>
    <?
}
?>
