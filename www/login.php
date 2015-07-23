<?
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Login screen
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/


include "header.php";
?>
<div id="centercontent" class="main">

<h2>LOGIN SCREEN</h2>
<?


if ( verify() ) {

    echo "<p>User $userName logged in successfully </p><BR>";
?>

<script language="Javascript">
function ChangeLoginLink() {
   var newtitle = "logout";
   var head1 = document.getElementById("loginbanner");
   head1.firstChild.nodeValue=newtitle;
   document.links[0].href = "logout.php";
}
ChangeLoginLink();
document.location.href = 'index.php';
</script>
<?
}


?>

<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>
<BR/><BR/>

</div>
</body>
</html>