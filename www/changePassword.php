<?
include "header.php";
?>
<div id="centercontent" class="main">

<?
if (!verify()) {
    exit;
} else {
    ?>
    <script language="Javascript">
        function ChangeLoginLink() {
           var newtitle = "logout";
           var head1 = document.getElementById("loginbanner");
           head1.firstChild.nodeValue=newtitle;
           document.links[0].href = "logout.php";
        }
        //ChangeLoginLink();
    </script>
    <script src="md5.js"></script>
    <script src="advantis-common.js"></script>
    <?
}
?>
<h2 style="padding-bottom:20px";>Password Change</h2>

<form method='post' action='changePassword.php' onSubmit="return change_password(this);" >

<?
$userId = $_SESSION['user'];

$userInfo = get_user_name($userId);
$userName = $userInfo[0];
echo "<input type=\"hidden\" name=\"userName\" value=\"$userName\">";

if ($passwordChange == 1) {

    # Verify that the user entered his login
    $currentPassword = $_POST["md5"];
    

    // verify Current user name and password
    if (!is_right_password($userId, $currentPassword)) {
        echo "<p><b>Current Password Invalid - Password not changed</p></b>";   
    }
    else {
        $newPassword = $_POST["md5NewPassword"];
        update_password($userId, $newPassword);
        echo "<script>";
            echo "alert(\"Password Updated Successfully\");";
            echo "document.location.href = 'index.php'";
        echo "</script>";
        echo "<BR><BR><BR>";
        exit;
    }
}

?>

<p> Password should be between 4 and 10 characters long </p>
<fieldset class="login">
<legend>Password Change</legend>

<label for="password">Current Password:</label>
<input class="input-box" type="password" name="password"> <BR>

<label for="password">New Password:</label>
<input class="input-box" type="password" name="newPassword"> <BR>

<label for="password">New Password:</label>
<input class="input-box" type="password" name="newPasswordConfirm"> <BR>

<input class="submit-button" type="submit" value="OK">
<input type="hidden" name="md5" value="">
<input type="hidden" name="md5NewPassword" value="">
<input type="hidden" name="passwordChange" value="">

</form>
</fieldset>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
<BR/>
<BR/><BR/>
