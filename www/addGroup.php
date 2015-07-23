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
<h2>Group Management</h2> <p> Adding/Modifying and deleting groups ... </p>

<form action="<? print $PHP_SELF; ?>" method="post">

<?
$sessionToken = $_SESSION["token"];

if (($action == 1) && ($sessionToken == $formToken)) {
    $totalUsers = trim ($totalUsers, ",");
    add_new_group($groupname, $totalUsers);
    $sessionToken  = $sessionToken + 1;
    $_SESSION["token"] = $sessionToken;

}
elseif (($action == 2) && ($sessionToken == $formToken)) {
    delete_group($groupname);
    $sessionToken  = $sessionToken + 1;
    $_SESSION["token"] = $sessionToken;

}
elseif (($action == 3) && ($sessionToken == $formToken)) {
    $totalUsers = trim ($totalUsers, ",");
    update_group($groupname, $totalUsers, $id);
    $sessionToken  = $sessionToken + 1;
    $_SESSION["token"] = $sessionToken;

}

?>

<fieldset>
<legend>Current Groups </legend>

<p> To Change or Delete a group, select it ... </p>
<?

# Setting a session token
$_SESSION["token"] = rand (0, 10000); 
$token = $_SESSION["token"];

$allgroups = get_all_groups();
if (count ($allgroups)) {
    
    $allGroups = "";    
    echo "<TABLE class=\"report\">";
    echo "<TR><TH>Group Name</TH><TH>Users</TH><TH>Right</TH></TR>";

    foreach ($allgroups as $indGroup) {
        
        $indGroup = trim ($indGroup);
        if ($indGroup == "") {
            next;
        }
        $indGroup = str_replace ("___", "\M", $indGroup);
        $indGroupArray = explode ("\M", $indGroup);
        if (($indGroupArray[0] == $id) && ($indGroupArray[2] != "")) {
            $userName = "$userName,$indGroupArray[2]";
        }
        else {
            if ($id != "") {
                if ($userName == "") {
                    $userNameDisp = "NO USERS";
                }
                else {
                    $userNameDisp = $userName;
                }
                if ($groupName != "") {
                    $allGroups = $allGroups . $groupName . ",";
                    echo "<TR><TD><a href=\"javascript:populate_groups('$groupName', '$userName', '$id')\"</a>$groupName</TD><TD>$userNameDisp</TD><TD><a href=\"addRight.php?groupName=$groupName&id=$id\" target = \"_blank\"</a>Rights for $groupName</TD></TR>";
                }
            }
            $userName = $indGroupArray[2];
        }

        $id         = $indGroupArray[0];
        $groupName  = $indGroupArray[1];        
    }
    if ($userName == "") {
        $userNameDisp = "NO USERS";
    }
    else {
        $userNameDisp = $userName;
    }

    if ($groupName != "") {

        $allGroups = $allGroups . $groupName . ",";
        echo "<TR><TD><a href=\"javascript:populate_groups('$groupName', '$userName', '$id')\"</a>$groupName</TD><TD>$userNameDisp</TD><TD><a href=\"addRight.php?groupName=$groupName&id=$id\" target = \"_blank\"</a>Rights for $groupName</TD></TR>";
        
    }
    echo "</TABLE>";
}
else {
    echo "No Groups available<BR>";
}

?>
<br>
</fieldset>
<br>
<br>

<fieldset>
<legend>User Assignment</legend>
<p> Group ... </p>

<TABLE class="menu">
<?
$totalUsers = get_all_username();
?>
<TR class="menu"><TH class ="menu">Available Users</TH><TH class ="menu"></TH><TH class ="menu">User in Groups</TH></TR>
<TR class="menu">
<TD class="menu">
<SELECT class="groups" name = "list1" size = "5" onDblClick="moveSelectedOptions(this.form['list1'],this.form['list2'])">
<?
foreach ($totalUsers as $value) {

    $valueTmp = str_replace("___", "\M", $value);
    $valueArray = explode ("\M", $valueTmp);
    $userName  = $valueArray[0];
    echo "<option value =\"$userName\">$userName";
}
?>
</SELECT>
</TD>
<TD class="menu" style="padding-top:20px;">
        <INPUT TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'])"><BR><BR>
        <INPUT TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'])"><BR><BR>
</TD>

<TD class="menu">
<SELECT class="groups" name = "list2" size = "5" onDblClick="moveSelectedOptions(this.form['list2'],this.form['list1'])">
</SELECT>
</TD>
</TR>
</TABLE>
</fieldset>
<br>
<br>
<fieldset>
<legend>Group Add/Delete</legend>
<p> To enter a new group enter the fields .. </p>
 
<TABLE class="menu">
<TR>
<TD class="menu">
<label for = "groupname">Group Name:</label>
<input class="username-box"  name="groupname">
</TD>
</TR>
</TABLE>
<?

?>
</fieldset>
<br/>
<br/>
<fieldset>
<legend>Submit</legend>
<?
echo "<input class=\"submit-button\" name = \"addGroup\" type=\"button\" onClick=\"add_group('$allGroups');\" value=\"ADD GROUP\">";
echo "<input class=\"submit-button\" name = \"deleteGroup\" type=\"button\" onClick=\"delete_group('$allGroups');\" value=\"DELETE GROUP\">";
echo "<input class=\"submit-button\" name = \"updateGroup\" type=\"button\"  onClick=\"update_group('$allGroups');\" value=\"UPDATE GROUP\">";


?>

<input class="submit-button" name = "resetUser" type="reset" value="RESET" onClick="display_options_for_this(this.form['list1'],this.form['list2'], '');disable_button_group()">
<script>
disable_button_group();
</script>
</fieldset>
<INPUT TYPE="hidden" NAME=action>
<INPUT TYPE="hidden" NAME=id>
<INPUT TYPE="hidden" NAME=groupSelect>
<INPUT TYPE="hidden" NAME=totalUsers>
<?
 echo "<INPUT TYPE=\"hidden\" NAME=formToken value=\"$token\">";
?>

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
