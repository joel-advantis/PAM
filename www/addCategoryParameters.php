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

    if (($action == 1) && ($sessionToken == $formToken)) {
        $totalAppClass = trim ($totalAppClass, ",");
        add_new_category($categoryname, $totalAppClass, $categorydescription, $categorytype);
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
        update_category($categoryname, $totalAppClass, $categorydescription, $categorytype);
        echo "Category $categoryname updated <br>";
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }

    ?>

    <fieldset>
    <legend>Available Categories</legend>

    <p> To Change or Delete a category, select it ... You cannot change the generic (out of the box) categories ... </p>
    <?

    # Setting a session token
    $_SESSION["token"] = rand (0, 10000);
    $token = $_SESSION["token"];

    $allCategories = get_total_category();
    $allCategoriesString = "";

    $categoryTypesArray = get_categorytype_labels ();
    $categoryTypes = implode ("\n",$categoryTypesArray);


    debug_var ("Category Types",$categoryTypes);

    if (count ($allCategories)) {
        ?>
        <TABLE class="report">
        <TR><TH>Category Name</TH><TH>Type</TH><TH>Description</TR>
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

            if ($categoryDescription == "") {
                $categoryDescriptionDisp = "Not Entered";
            }
            else {
                $categoryDescriptionDisp = $categoryDescription;
            }

            # We need to loop and get all the app classes
            $allAppClass = get_total_application($categoryName);
            $allAppClass = implode (",", $allAppClass);

            $allCategoriesString = $allCategoriesString . "," . $categoryName;
            ?>
            <TR><TD><a href="javascript:populate_categories('<? print "$categoryName', '$categoryType','$allAppClass', '$categoryDescription"; ?>')"</a><? print "$categoryName"; ?></TD>
            <TD><? print "$categoryTypeDisp"; ?></TD><TD><? print "$categoryDescriptionDisp"; ?></TD></TR>
            <?
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
    <legend>Application Class Assignment</legend>
    <p><a href="addCategory.php">Hide Parameters</a></p>


    <TABLE class="menu">
    <?
    $totalappClass = get_all_application_class();
    ?>
    <TR class="menu"><TH class="menu">Application Class</TH><TH class="menu">Parameters</TH><TH class="menu"></TH><TH class="menu">Application Class in Category</TH></TR>
    <TR class="menu">
    <TD class="menu"><SELECT  name = "appclass" onChange="submit()">
    <?php
    foreach ($totalappClass as $value) {

        $valueTmp = str_replace("___", "\M", $value);
        $valueArray = explode ("\M", $valueTmp);
        $appClass  = $valueArray[0];
        ?>
        <option value =<? print "\"$appClass\">$appClass";

    }
    $totalParameters = get_all_param_for_app_class($appClass);
    if (count ($totalParameters)) {

        ?>
        <TD class="menu">
        <SELECT class="groups" MULTIPLE name = "list1" size = "5" onDblClick="moveSelectedParameters('<?php print "$appClass"; ?>',this.form['list1'],this.form['list2'])">
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
    </SELECT>
    </TD>
    <TD class="menu" style="padding-top:20px;">
            <INPUT TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="moveSelectedParameters('<?php print "$appClass"; ?>',this.form['list1'],this.form['list2'])"><BR><BR>
            <INPUT TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="moveSelectedParameters('<?php print "$appClass"; ?>',this.form['list2'],this.form['list1'])"><BR><BR>
    </TD>

    <TD class="menu">
    <SELECT class="groups" MULTIPLE name = "list2" size = "5" onDblClick="moveSelectedOptions(this.form['list2'],this.form['list1'])">
    </SELECT>
    </TD>
    </TR>
    </TABLE>
    </fieldset>
    <br>
    <br>
    <fieldset>
        <legend>Category Add/Delete</legend>
        <p> To enter a new category enter the fields .. </p>

        <label for = "categoryname">Category Name:</label>
        <input class="username-box"  name="categoryname">
        <br/><br/>
        <label for = "categorytype">Type:</label>
        <SELECT  name = "categorytype">
        <?php
        foreach ($categoryTypesArray as $catType) {
            $catType = str_replace ("___", "\M", $catType);
            $catTypeArray = explode ("\M", $catType);
            $typeId       = $catTypeArray[0];
            $typeLabel    = $catTypeArray[1];

            // Skip Pre-defined
            if ($typeId == 1) { continue; }

            print "<OPTION value=\"$typeId\">$typeLabel\n";
        }
        ?>
        </SELECT>

        <br/><br/>
        <b>Category Description:</b>
        <textarea class="comment-box" name="categorydescription" rows="5"></textarea>

        <br/>
        <br/>
    </fieldset>
    <br/>
    <br/>
    <fieldset>
        <legend>Submit</legend>
        <input class="submit-button2" name = "addCategory" type="button" onClick="add_category('<? print "$allCategoriesString"; ?>');" value="Add category">
        <input class="submit-button2" name = "deleteCategory" type="button" onClick="delete_category();" value="Delete category">
        <input class="submit-button2" name = "updateCategory" type="button"  onClick="update_category();" value="Update category">
        <input class="submit-button2" name = "mergeCategory" type="button"  onClick="set_variable_merge();" value="Merge data">
        <br/>
    </fieldset>
    <INPUT TYPE="hidden" NAME=action>
    <INPUT TYPE="hidden" NAME=mergeSelected>
    <INPUT TYPE="hidden" NAME=categoryType>
    <INPUT TYPE="hidden" NAME=totalAppClass>
    <INPUT TYPE="hidden" NAME=hideOutOfBox>
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
