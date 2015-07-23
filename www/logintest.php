<?
session_start();

/* ---------- user settings ---------- */

$un = "Bob";
$pw = "fubar";

/* ----------  actions ----------  */

if ($action == "logout") {
   session_unset();
}

if ($action == "login") {
   if ($put_un == $un)    {
       if ($put_pw == $pw) {
           $_SESSION['auth'] = true;
       } else {
           $error = "incorrect password.";
       }
   } else {
       $error = "incorrect username.";
   }
}

/* ---------- authenticate ----------  */
if ($_SESSION['auth'] == true) {
   /* secure code */
} else {
   /* non-secure code */
   $view = "login";
}

?>
<html>
<title>Secure Area</title>
<body>
<? if ($view == "login") { ?>
<form action="logintest.php" method="post">
  user<br>
  <input type="text" name="put_un"><br>
  password<br>
  <input type="password" name="put_pw">
   <input name="action" type="hidden" id="action" value="login"><br>
   <input type="submit" name="Submit" value="login"><? echo "$error<br>"; ?>
</form>
<? } if ($_SESSION['auth'] == true) { ?>

<! -- SECURE CONTENT -->
<a href="logintest.php?action=logout">logout</a>

<? } ?>
</body>
</html>