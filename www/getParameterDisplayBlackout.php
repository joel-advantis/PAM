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
    <p> Select ... </p>
    <p> Explain how percolation work</p>
    <TABLE>
    <TR><TH>CATEGORY</TH><TH>APPLICATION CLASS</TH><TH>PARAMETERS</TH></TR>
    <TR><TD>


    <SELECT  name = "category" onChange="set_param_focus()">
    <?
    $categorySet = 0;
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

if (($category) && ($category != "ENTIRE_SERVER")) {

    # Display the application class
    $totalApplicationClass = get_appClass_for_category($category);
    ?>
    <TD>
    <SELECT  name = "appClass" onChange="set_param_focus()">
    <?
    $totalApplicationClass[] = "ALLAPPS";

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
    
    if (($appClass == "") || ($category != $categoryOld)) {
        $appClass = "ALLAPPS";
        echo "<OPTION value = \"$appClass\" SELECTED>$appClass";      
    }
    ?>
    </SELECT>
    </TD>
    <?
    $totalParameters = get_param_for_app_class($category,$appClass);
    if (count ($totalParameters)) {
            
        ?>
        <TD>
            <SELECT  name = "param" onChange="set_param_focus()">
        <?
        $parameterSet = 0;
        $totalParameters[] = "ALLPARAM";
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
        if (!$parameterSet)  {
            $param = "ALLPARAM";
            echo "<OPTION value = \"$param\" SELECTED>$param";
        }
        ?>
        </SELECT>
        </TD>
        <?
    }
}
if (($param) && ($parm != "ALLPARAM")){
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
    
    ?>
    </TR>
    </TABLE>
    <br>
    <?
    # Add the Override instance
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
            foreach ($totalInstance as $indInstance) {

                $indInstance = trim ($indInstance);
                if ($indInstance == "") {
                    next;
                }
                if ($indInstance == $instance) {
                    echo "<OPTION value = \"$indInstance\" SELECTED>$indInstance";
                    $indInstanceSet = "1";
                }
                else {
                    echo "<OPTION value = \"$indInstance\">$indInstance";
                }
            }
            if ($indInstanceSet == "1") {
                echo "<OPTION value = \"__ANYINST__\">ALL";
            }
            else {
                echo "<OPTION value = \"__ANYINST__\" SELECTED>ALL";
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
<input type="hidden" name="categoryOld" value = "<? print ($category) ?>">
</TR>
</TABLE>
