<?
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Build and display configuration reports
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

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
    $userId = $_SESSION['user'];
    if (!check_rights("3")) {
        print_lack_of_privledge_warning();
    }

}
?>
<h2 style="padding-bottom:20px";>Please select the new password for the db webpatrol:</h2>

<form method='post' action='updateDbPass.php' onSubmit="return change_db_password(this);" >

<?
if ($passwordChange == 1) {

    $dateStr = date("mdyhms");
    $configFileName = "config/config.php";
    $backupFileName = "config/config.$dateStr.php";


    // Verify that the user entered his login

    $key = "pam";
    $iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));    
    $ENCRYPT_PASSWORD = mcrypt_cfb (MCRYPT_CAST_256, $key, $password, MCRYPT_ENCRYPT, $iv);
    $ENCRYPT_PASSWORD = trim(chop(base64_encode($ENCRYPT_PASSWORD)));
    $ENCRYPT_PASSWORD_TMP = str_replace ("/", "\\/", $ENCRYPT_PASSWORD);

    # We need to read the config file and confim it
    $fileContent = file($configFileName);
    $stringToCheck = "SQL_PASSWORD =  \"$ENCRYPT_PASSWORD_TMP\"";
    $isPasswordCorrent = preg_grep ("/$stringToCheck/", $fileContent);
    if (!count ($isPasswordCorrent)) {
        echo "<p><b>You entered the wrong webpatrol password - Password not changed</p></b>";   
    }
    else {
        # We need to encrypt the new password
        $newPassEncrypt = mcrypt_cfb (MCRYPT_CAST_256, $key, $newPassword, MCRYPT_ENCRYPT, $iv);
        $newPassEncrypt = trim(chop(base64_encode($newPassEncrypt)));
        $stringToChange = "SQL_PASSWORD =  \"$ENCRYPT_PASSWORD\"";
        $newString = "SQL_PASSWORD =  \"$newPassEncrypt\"";

        # Update the password in the database
        update_db_password($SQL_USER, $newPassword);

        # We need to update the file
        $fileContentString = implode ("", $fileContent);
        $newFile =  str_replace($stringToChange, $newString, $fileContentString);
        
        # Backing up the old file
        $backupfile = $backupFileName;
        $fp = fopen ($backupfile, "w");
        fwrite ($fp, $fileContentString);
        fclose ($fp);

        # Updating the config.php file
        $fp = fopen ($configFileName, "w");
        fwrite ($fp, $newFile);
        fclose ($fp);
        
        echo "<p><b>Password Updated Successfully</p></b>"; 
        echo "<BR><BR><BR>";
        exit;
    }
}
?>

<p> Password should be between 4 and 10 character long </p>
<fieldset class="login">
<legend>Password Change</legend>

<label for="password">Current Password:</label>
<input class="input-box" type="password" name="password"> <BR>

<label for="password">New Password:</label>
<input class="input-box" type="password" name="newPassword"> <BR>

<label for="password">New Password:</label>
<input class="input-box" type="password" name="newPasswordConfirm"> <BR>

<input class="submit-button" type="submit" value="OK">
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
</div>
</body>
</html>

<?php
/* ****************************************************************************
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *  
 ******************************************************************************/
