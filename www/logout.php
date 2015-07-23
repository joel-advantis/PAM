<?
    include "header.php";

    log_session("logout");
    unset($_SESSION['user']);
    unset($_SESSION['rights']);
    

?>
<script language="Javascript">
function ChangeLoginLink() {
   var newtitle = "login";
   var head1 = document.getElementById("loginbanner");
   head1.firstChild.nodeValue=newtitle;
   document.links[0].href = "login.php";
}
ChangeLoginLink();
document.location.href = 'index.php';
</script>
<div id="centercontent" class="main">
<h1>Logged out</h1>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
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
