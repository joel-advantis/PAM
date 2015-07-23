<?
include "header.php";
?>
<div id="centercontent" class="main">
    <script src="advantis-common.js"></script>
    <script src="md5.js"></script>


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
    <h2>Category Management</h2> <p> Adding/Modifying and deleting Categories</p>

    <form action="<? print $PHP_SELF; ?>" method="post">

    <?
    $sessionToken = $_SESSION["token"];

    $functions = $_POST['functions'];
    if (($action == 1) && ($sessionToken == $formToken)) {
        $totalAppClass = trim ($totalAppClass, ",");

        if (is_array($functions)) {
            $functions = implode (" ", $functions);
        }
        $categoryname = str_replace (" ", "_", $categoryname);
        add_new_category($categoryname, $totalAppClass, $categorydescription, $categorytype, $functions);
        echo "Category $categoryname added<br>";
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }
    elseif (($action == 2) && ($sessionToken == $formToken)) {
        delete_category($categoryname);
        echo "Category $categoryname deleted<br>";
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }
    elseif (($action == 3) && ($sessionToken == $formToken)) {
        $totalAppClass = trim ($totalAppClass, ",");
    if (is_array($functions)) {
        $functions = implode (" ", $functions);
    }
        update_category($categoryname, $totalAppClass, $categorydescription, $categorytype,$functions);
        echo "Category $categoryname updated <br>";
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }

    ?>

    <fieldset>
    <legend>Available Categories</legend>

    <p> To Change or Delete a category, select it</p>
    <?

    # Setting a session token
    $_SESSION["token"] = rand (0, 10000);
    $token = $_SESSION["token"];

    $allCategories = get_total_category();
    $allCategoriesString = "";
    $categoryTypesArray = get_categorytype_labels ();
    $categoryTypes = implode ("\n",$categoryTypesArray);
    $functionLabelsArray = get_function_labels();
    $functionLabels = implode ("\n",$functionLabelsArray);


    debug_var ("Category Types",$categoryTypes);

    if (count ($allCategories)) {
        ?>
        <TABLE class="report">
        <TR><TH>Category Name</TH><TH>Type</TH><TH>Description</TH><TH>Categories Functions</TH></TR>
        <?
        foreach ($allCategories as $indCat) {

            $indCat = trim ($indCat);
            if ($indCat == "") {
                next;
            }
            $indCat = str_replace ("___", "\M", $indCat);
            $indCatArray = explode ("\M", $indCat);
            $categoryName = $indCatArray[0];
            $categoryType = $indCatArray[1];
            if (($hideOutOfBox == 1) && ($categoryType == 1)) {
                continue;
            }
            $categoryDescription = $indCatArray[2];

            // Update this to query category type
            //if ($categoryType == 1) {
            //    $categoryTypeDisp = "Custom";
            //}
            //else {
            //    $categoryTypeDisp = "Out of the Box";
            //}

            $grepString = "/($categoryType)___(.*)/";

            debug_msg ("Category type: $categoryType");
            debug_msg ("Grep string: $grepString");

            preg_match_all($grepString,$categoryTypes,$catLabelRow,PREG_SET_ORDER);

            debug_var ("Category types matching this type",$catLabelRow);

            foreach ($catLabelRow as $row) {
                $catLabelId = $row[1];
                $categoryTypeDisp = $row[2];

                if ($catLabelId == $categoryType) { continue; }
            }

            debug_var("Result of category label grep",$catLabelRow);

            $categoryDescriptionDisp = $categoryDescription;
        # We need to get the

        $catFunction = get_category_function_label($categoryName);
        $catFunction = implode (",", $catFunction);
        if ($catFunction == "") {
            $catFunction = "NONE";
        }
        $allCategoriesString = $allCategoriesString . $categoryName . ",";
            ?>
            <TR><TD><a href="javascript:populate_categories_submit(<? print "'$categoryName','$categoryDescriptionDisp','$categoryType'"; ?>)"</a><? print "$categoryName"; ?></TD>
            <TD NOWRAP><? print "$categoryTypeDisp"; ?></TD><TD NOWRAP><? print "$categoryDescriptionDisp"; ?></TD><TD NOWRAP><? print "$catFunction"; ?></TD></TR>
            <?
        }
    $categoryFunctions = array();
    if ($categorySelected == "1") {
        # We need to loop and get all the app classes
                $previousSelection = get_category_information($categoryname);
                $previousSelection = implode (",", $previousSelection);
            $previousSelection = str_replace ("___", ":", $previousSelection);
        $categorySelected = 0;
        $showParameters = 0;
        $categoryFunctions = get_category_function($categoryname);
    }
    else {
        if (is_array($functions)) {
            $categoryFunctions = $functions;
        }
    }
        ?>
        </TABLE>
        <?
        if ($hideOutOfBox == 1) {
            ?>
            <input class="submit-button" name = "showOutOfTheBox" type="button"  onClick="hide_outofbox('0');" value="Show all">
            <?
        }
        else {
            ?>
            <input class="submit-button" name = "showOutOfTheBox" type="button"  onClick="hide_outofbox('1');" value="Hide generic">
            <?
        }
    }

    ?>
    </fieldset>
    <br>
    <br>

    <fieldset>
    <legend>Category Assignment</legend>

    <?
     if ($showParameters == "1") {
     ?>
        <a href="javascript:onClick=toggle_parameters('0')">Hide Parameters</a></p>
    <TABLE class="menu">
        <?
        $totalappClass = get_all_application_class();
        ?>
        <TR class="menu"><TH class="menu">Application Class</TH><TH class="menu">Parameters</TH><TH class="menu"></TH><TH class="menu">Selection in Category</TH></TR>
        <TR class="menu">
        <TD class="menu"><SELECT  name="appclassSelected" onChange="remember_value()">
        <?php
    $appclassSelected = trim ($appclassSelected);
    if ($appclassSelected == "") {
        $appclassSelected = $totalappClass[0];
    }
        foreach ($totalappClass as $value) {

            $valueTmp = str_replace("___", "\M", $value);
            $valueArray = explode ("\M", $valueTmp);
            $appClassDisplay  = trim ($valueArray[0]);
        if ($appclassSelected == $appClassDisplay) {

            echo "<option value = \"$appClassDisplay\" SELECTED>$appClassDisplay";
        }
        else {
            echo "<option value = \"$appClassDisplay\" >$appClassDisplay";
        }
        }

    echo "</SELECT>";
        $totalParameters = get_all_param_for_app_class($appclassSelected);
        if (count ($totalParameters)) {

            ?>
            <TD class="menu">
            <SELECT class="groups" MULTIPLE name = "list1" size = "5" onDblClick="moveSelectedParameters('<?php print "$appclassSelected"; ?>',this.form['list1'],this.form['list2'])">
            <?
            $parameterSet = 0;
            foreach ($totalParameters as $indParam) {

                    if ($param == $indParam) {
                        echo "<OPTION value = \"$indParam\" SELECTED>$indParam";
                        $parameterSet = 1;
                    }
                    else {
                        echo "<OPTION value = \"$indParam\">$indParam";
                    }
            }
            if (!$parameterSet) {
                    $param = $totalParameters[0];
            }
            ?>
            </SELECT>
            </TD>
            <?php
        }
        ?>
        </TD>
        <TD class="menu" style="padding-top:20px;">
        <INPUT TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="moveSelectedParameters('<?php print "$appclassSelected"; ?>',this.form['list1'],this.form['list2'])"><BR><BR>
         <INPUT TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="moveSelectedParameters('<?php print "$appclassSelected"; ?>',this.form['list2'],this.form['list1'])"><BR><BR>
         </TD>

         <TD class="menu">
         <SELECT class="paramlist" MULTIPLE name = "list2" size = "5" onDblClick="moveSelectedParameters('<?php print "$appclassSelected"; ?>',this.form['list2'],this.form['list1'])">
    <?
        if ($previousSelection != "") {
            $previousSelection = explode (",", $previousSelection);
            foreach ($previousSelection as $value) {
                $value = trim ($value);
                $value = trim ($value,":");
                if ($value != "") {
                    echo "<option value = \"$value\" >$value";
                }
            }
        }
    ?>
        </SELECT>
        </TD>
        </TR>
        </TABLE>
        <?

     }
     else {
     ?>
        <a href="javascript:onClick=toggle_parameters('1')">Show Parameters</a></p>
    <TABLE class="menu">
        <?
        $totalappClass = get_all_application_class();
        ?>
        <TR class="menu"><TH class="menu">Available Application Class</TH><TH class="menu"></TH><TH class="menu">Selection in Category</TH></TR>
        <TR class="menu">
        <TD class="menu">
        <SELECT class="groups" MULTIPLE name = "list1" size = "5" onDblClick="moveSelectedApps(this.form['list1'],this.form['list2'])">
        <?
        foreach ($totalappClass as $value) {

            $valueTmp = str_replace("___", "\M", $value);
            $valueArray = explode ("\M", $valueTmp);
            $appClass  = $valueArray[0];
            ?>
            <option value =<?php print "\"$appClass\">$appClass";
        }
        ?>
        </SELECT>
        </TD>
        <TD class="menu" style="padding-top:20px;">
        <INPUT TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="moveSelectedApps(this.form['list1'],this.form['list2'])"><BR><BR>
        <INPUT TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="moveSelectedApps(this.form['list2'],this.form['list1'])"><BR><BR>
        </TD>

        <TD class="menu">
        <SELECT class="paramlist" MULTIPLE name = "list2" size = "5" onDblClick="moveSelectedApps(this.form['list2'],this.form['list1'])">
    <?
        if ($previousSelection != "") {
            $previousSelection = explode (",", $previousSelection);
            foreach ($previousSelection as $value) {
                $value = trim ($value);
                $value = trim ($value,":");
                if ($value != "") {
                    echo "<option value = \"$value\" >$value";
                }
            }
        }
    ?>
        </SELECT>
        </TD>
        </TR>
        </TABLE>

     <?
     }
     ?>
    </fieldset>
    <br>
    <br>
    <fieldset>
        <legend>Category Add/Delete</legend>
        <p> To enter a new category enter the fields .. </p>

        <label for = "categoryname">Category Name:</label>
        <input class="username-box"  name="categoryname" value=<?php print "$categoryname"; ?>>
        <br/><br/>
        <label for = "functionList">Type:</label>
        <TABLE><TR>
        <?php
        foreach ($functionLabelsArray as $catType) {
            $catType = str_replace ("___", "\M", $catType);
            $catTypeArray = explode ("\M", $catType);
            $typeId       = $catTypeArray[0];
            $typeLabel    = $catTypeArray[1];

            #// Skip Pre-defined
            #if ($typeId == 1) { continue; }

        # Need to determine if this category had this function
        if (preg_grep("/$typeId/", $categoryFunctions)) {
                print "<TD><INPUT class=\"checkbox\" type=\"CHECKBOX\" name=\"functions[]\" value=\"$typeId\" CHECKED>$typeLabel</TD>";
        }
        else {
                print "<TD><INPUT class=\"checkbox\" type=\"CHECKBOX\" name=\"functions[]\" value=\"$typeId\">$typeLabel</TD>";
        }

        }
        ?>
        </TR></TABLE>

        <br/><br/>
        <b>Category Description:</b>
        <textarea class="comment-box" name="categorydescription"  rows="5" ><?php print "$categorydescription"; ?></textarea>

        <br/>
        <br/>
    </fieldset>
    <br/>
    <br/>
    <fieldset>
        <legend>Submit</legend>
        <input class="submit-button2" name = "addCategory" type="button" onClick="add_category('<? print "$allCategoriesString"; ?>');" value="Add category">
        <input class="submit-button2" name = "deleteCategory" type="button" onClick="delete_category('<? print "$allCategoriesString"; ?>');" value="Delete category">
        <input class="submit-button2" name = "updateCategory" type="button"  onClick="update_category();" value="Update category">
    <!-- <input class="submit-button2" name = "mergeCategory" type="button"  onClick="set_variable_merge();" value="Merge data"> -->
        <br/>
    </fieldset>
    <?
    if ($categorySelectedFocus == "1") {
    ?><SCRIPT>
        document.forms[0].categoryname.focus();
    </SCRIPT>
    <?
    $categorySelectedFocus = 0;
    }
    $categorySelectedFocus = 1;
    ?>
    <INPUT TYPE="hidden" NAME=action>
    <INPUT TYPE="hidden" NAME=mergeSelected>
    <INPUT TYPE="hidden" NAME=categoryType VALUE=<? print "$categoryType"; ?>>
    <INPUT TYPE="hidden" NAME=totalAppClass>
    <INPUT TYPE="hidden" NAME=hideOutOfBox>
    <INPUT TYPE="hidden" NAME=previousSelection>
    <INPUT TYPE="hidden" NAME=categorySelectedFocus VALUE=<? print "$categorySelectedFocus"; ?>>
    <INPUT TYPE="hidden" NAME=categorySelected VALUE=<? print "$categorySelected"; ?>>
    <INPUT TYPE="hidden" NAME=showParameters VALUE=<? print "$showParameters"; ?>>
    <INPUT TYPE="hidden" NAME=formToken value="<? print "$token"; ?>">
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
