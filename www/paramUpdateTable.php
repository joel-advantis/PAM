<?
include "header.php";

?>
<div id="centercontent" class="main">
<script src="advantis-common.js"></script>

<?

if (!verify()) {
    exit;
} else {
    ?>
    <script>
        //ChangeLoginLink();
    </script>

    <?
    $userId = $_SESSION['user'];
    if (!check_rights("2")) {
        print_lack_of_privledge_warning();
    }
}
?>

<h2>Parameter Description Update</h2> <p> Updating the Parameter Description table </p>

<form action="<? print $PHP_SELF; ?>" method="post">



<?

if ($updateDescription) {
    update_param_description($appSelected, $paramSelected, $description, $collector,$appClassAlias,$paramAlias);
    echo "<H3>Parameter $paramSelected updated </h3>";
}

?>
<fieldset>
<legend>Parameter Selection </legend>
<br/>
<?

$totalCategory = get_total_category();
if (count ($totalCategory)) {

    ?>	
    <TABLE>
    <TR><TH>CATEGORY</TH><TH>APPLICATION CLASS</TH><TH>PARAMETER</TH></TR>
    <TR><TD>
    <SELECT  name = "kmCategory" onChange="submit()">

    <?
    $kmCategorySet = 0;
    $totalCategory = preg_grep ("/ENTIRE_SERVER/", $totalCategory, PREG_GREP_INVERT);
    foreach ($totalCategory as $indCategory) {
        $indCategory = trim ($indCategory);
        $indCategory = str_replace("___", "\M", $indCategory);
        $indCategoryArray = explode ("\M", $indCategory);
        $indCategory = $indCategoryArray[0];
        if ($indCategory == "") {
            continue;
        }
        if ($kmCategory == $indCategory) {
            echo "<OPTION value = \"$indCategory\" SELECTED>$indCategory";
            $kmCategorySet = 1;
        }
        else {
            echo "<OPTION value = \"$indCategory\">$indCategory";
        }
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
    
    $totalAppClass   = get_total_application($kmCategory);
    if (count ($totalAppClass)) {

	?>
        <TD>
        <SELECT  name = "appSelected" onChange ="submit()">
	<?
	$applicationSet = 0;
        foreach ($totalAppClass as $indApp) {

            if ($appSelected == $indApp) {
                echo "<OPTION value = \"$indApp\" SELECTED>$indApp";
                $applicationSet  = 1;
            }
            else {
                echo "<OPTION value = \"$indApp\">$indApp";
            }
        }
        if (!$applicationSet) {
            $appSelected = $totalAppClass[0];
        }
    }
    ?>
    </SELECT>
    </TD>
    <?

    $totalParameters = get_param_for_app_class($kmCategory, $appSelected);  
    if (count ($totalParameters)) {

	?>
        <TD>
        <SELECT  name = "paramSelected" onChange="submit()">
	<?
	$parameterSet = 0;
        foreach ($totalParameters as $indParam) {

            $indParam = str_replace ("___", "\M", $indParam);
            $indParam = explode("\M", $indParam);
            $parameterName = $indParam[0];

            if ($paramSelected == $parameterName) {
                echo "<OPTION value = \"$parameterName\" SELECTED>$parameterName";
                $parameterSet = 1;
            }
            else {
                echo "<OPTION value = \"$parameterName\">$parameterName";
            }
        }
        if (!$parameterSet) {
            $paramSelected = $totalParameters[0];
            $paramSelected = str_replace ("___", "\M", $paramSelected);
            $paramSelected = explode("\M", $paramSelected);
            $paramSelected = $indParam[0];
        }
    }
    ?>
    </SELECT>
    </TD></TR>
    </TABLE>
    <?
}


?>
</fieldset>
<br>
<br>

<fieldset>
<legend>Parameter Descriptions Update</legend>

<?

if ($paramSelected) {

    $AllparamDescription = get_parameter_description($kmCategory, "$paramSelected");
    $AllparamDescription = preg_grep("/$appSelected/", $AllparamDescription);
    $AllparamDescription = implode ("", $AllparamDescription);
    $indParamDescription = str_replace ("___", "\M", $AllparamDescription);
    $indParamDescriptionArray = explode ("\M", $indParamDescription);
    $indParamSelected = $indParamDescriptionArray[0];
    $paramDescription = $indParamDescriptionArray[2];
    echo "<p> Enter Description for $indParamSelected <p>";
    echo "<textarea name=description cols=\"75\" rows=\"5\" >$paramDescription</textarea>";
    echo "<br/>";
    echo "<br/>";
    $result = get_collector_alias($kmCategory, $paramSelected);
    $result = $result[0];
    $result = str_replace("___", "\M", $result);
    $result = explode ("\M", $result);
    $collector = $result[0];
    $appClassAlias = $result[1];
    $paramAlias = $result[2];
    
    echo "Collector: <br>";
    echo "<input class=\"username-box\"  name=\"collector\" value = \"$collector\">";
    echo "<br>";

    echo "Application Class Alias: <br>";
    echo "<input class=\"username-box\"  name=\"appClassAlias\" value = \"$appClassAlias\">";
    echo "<br>";

    echo "Parameter Alias: <br>";
    echo "<input class=\"username-box\"  name=\"paramAlias\" value = \"$paramAlias\">";
}
?>
</fieldset>
<br>
<input class="submit-button" type="button" value="Update Description" onClick = "validate_Description()">
<input type="hidden" name="updateDescription">

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
