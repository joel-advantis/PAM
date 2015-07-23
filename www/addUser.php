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
<h2>User Management</h2> <p> Adding/Modifying and deleting users ... </p>

<form action="<? print $PHP_SELF; ?>" method="post">

<?
$sessionToken = $_SESSION["token"];

if (($action == 1) && ($sessionToken == $formToken)) {
    add_update_user($username, $md5, $password, $firstname, $lastname, $email, $totalGroup, $randomPass);
    $sessionToken  = $sessionToken + 1;
    $_SESSION["token"] = $sessionToken;

}
elseif (($action == 2) && ($sessionToken == $formToken)){
    inactivate_user ($username);
    $sessionToken  = $sessionToken + 1;
    $_SESSION["token"] = $sessionToken;

}

?>

<fieldset>
<legend>Current Users </legend>

<p> To Change or Delete a user, select it ... </p>
<?

# Setting a session token
$_SESSION["token"] = rand (0, 10000); 
$token = $_SESSION["token"];

$allUsers = get_all_users();
if (count ($allUsers)) {
    
    echo "<TABLE class=\"report\">";
    echo "<TR><TH>User Name</TH><TH>First Name</TH><TH>Last Name</TH><TH>e-mail</TH><TH>Group</TH><TH>Date Created</TH><TH>Last Login</TH></TR>";

    foreach ($allUsers as $indUser) {
        $indUser = str_replace ("___", "\M", $indUser);
        $indUserArray = explode ("\M", $indUser);
        if ($indUserArray[0] == $id) {
            $groupName = "$groupName,$indUserArray[5]";
        }
        else {
            if ($id != "") {
                echo "<TR><TD><a href=\"javascript:populate_users('$userName','$firstName','$lastName','$email', '$groupName')\"</a>$userName</TD><TD>$firstName</TD><TD>$lastName</TD><TD>$emailDisp</TD><TD>$groupName</TD><TD>$dateEntered</TH><TH>$lastLogin</TH></TR>";
                $groupName = "$indUserArray[5]";
            }
            else {
                $groupName = "$indUserArray[5]";
            }

        }

        $id        = $indUserArray[0];
        $userName  = $indUserArray[1];
        $firstName = $indUserArray[2];
        $lastName  = $indUserArray[3];
        $email     = $indUserArray[4];
        $dateEntered = $indUserArray[6];
        $lastLogin   = $indUserArray[7];
        
        if ($email == "") {
            $emailDisp = "Not Entered";
        }
        else {
            $emailDisp = "$email";
        }
        

    }
    echo "<TR><TD><a href=\"javascript:populate_users('$userName','$firstName','$lastName','$email','$groupName')\"</a>$userName</TD><TD>$firstName</TD><TD>$lastName</TD><TD>$emailDisp</TD><TD>$groupName</TD><TD>$dateEntered</TD><TD>$lastLogin</TD></TR>";
    echo "</TABLE>";
}
else {
    echo "No Users available<BR>";
}

?>
</fieldset>
<br>
<br>

<fieldset>
<legend>User Add/Delete</legend>
<p> To enter a new user enter the fields .. </p>
 
<TABLE class="menu">
<TR>
<TD class="menu">
<label for = "firstname">First Name:</label>
<input class="username-box"  name="firstname">
<label for = "lastname">Last Name:</label>
<input class="username-box"  name="lastname">
<label for = "username">User Name:</label>
<input class="username-box"  name="username">

</TD>
<TD class="menu">
<label for = "email">e-mail :</label> 
<input class="username-box"  name="email">
<label for = "password">Password:</label>
<input class="username-box"  name="password" type = "password">
<label for = "password2">Confirm Password:</label>
<input class="username-box"  name="password2" type = "password">

</TD>
</TR>
</TABLE>
<?

?>
</fieldset>
<br/>
<br/>
<fieldset>
<legend>Rights</legend>
<p> Group Selection </p>

<TABLE class="menu">
<?
$totalGroups = get_all_user_groups();
?>
<TR class="menu"><TH class="menu">Available Groups</TH><TH class="menu"></TH><TH class="menu">Current Groups</TH></TR>
<TR class="menu">
<TD class="menu">
<SELECT class="paramlist" name = "list1" size = "5" onDblClick="moveSelectedOptions(this.form['list1'],this.form['list2'])">
<?
foreach ($totalGroups as $value) {

    $valueTmp = str_replace("___", "\M", $value);
    $valueArray = explode ("\M", $valueTmp);
    $groupId    = $valueArray[0];
    $groupName  = $valueArray[1];
    echo "<option value =\"$groupName\">$groupName";
}
?>
</SELECT>
</TD>
<TD class="menu" style="padding-top:20px;">
        <INPUT TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="moveSelectedOptions(this.form['list1'],this.form['list2'])"><BR><BR>
        <INPUT TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="moveSelectedOptions(this.form['list2'],this.form['list1'])"><BR><BR>
</TD>

<TD class="menu">
<SELECT class="paramlist" name = "list2" size = "5" onDblClick="moveSelectedOptions(this.form['list2'],this.form['list1'])">
</SELECT>
</TD>
</TR>
</TABLE>
</fieldset>

<br>
<br>

<fieldset>
<legend>Submit</legend>
<input class="submit-button" name = "addUser" type="button" onClick="add_user();" value="ADD/UPDATE">
<input class="submit-button" name = "deleteUser" type="button" onClick="delete_user();" value="DELETE">
<input class="submit-button" name = "resetUser" type="reset" value="RESET" onClick="display_options_for_this(this.form['list1'],this.form['list2'], '')">

</fieldset>
<INPUT TYPE="hidden" NAME=action>
<INPUT TYPE="hidden" NAME=totalGroup>
<INPUT TYPE="hidden" NAME=randomPass>
<INPUT TYPE="hidden" NAME=md5>
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
