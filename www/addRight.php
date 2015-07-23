<?
include "session.php";
include "jefferson.css";
$groupName = trim ($_GET["groupName"]);
$id = trim ($_GET["id"]);

?>
<script src="advantis-common.js"></script>
<script src="md5.js"></script>


<?

if (!verify()) {
	exit;
} else {
?>
<script>


</script>

<?
	$userId = $_SESSION['user'];
	if (!check_rights("2")) {
		print_lack_of_privledge_warning();
	}
}
?>
<html>
<head>
<title>Right Management</title>
</head>
<body class="report">
<div class="report">

<?
echo "<h2>Right Management for $groupName </h2> <p> Adding/Modifying group rights ... </p>";
?>

<form action="<? print $PHP_SELF; ?>" method="post">

<?
$sessionToken = $_SESSION["token"];
if (($action == 1) && ($sessionToken == $formToken)) {
	update_group_right($id, $totalCategories, $totalGroups, $manualAgents); 
	echo "Group Updated Successfully";
	$sessionToken  = $sessionToken + 1;
	$_SESSION["token"] = $sessionToken;

}

?>

<fieldset>
<legend>Category Assignment</legend>

<TABLE class="menu">
<?
# Setting a session token
$_SESSION["token"] = rand (0, 10000); 
$token = $_SESSION["token"];

$totalCategory = get_total_category();
$totalCategoryGroup = get_total_category_for_group($id);

$totalCategory = array_diff($totalCategory, $totalCategoryGroup);
?>
<TR class="menu"><TH class="menu">Available Categories</TH><TH class="menu"></TH><TH class="menu">Category for group</TH></TR>
<TR class="menu">
<TD class="menu">
<SELECT class="groups" name = "list1" size = "5" onDblClick="moveSelectedOptions(this.form['list1'],this.form['list2'])">
<?
foreach ($totalCategory as $value) {

	$valueTmp = str_replace("___", "\M", $value);
	$valueArray = explode ("\M", $valueTmp);
	$categoryName  = $valueArray[0];
	echo "<option value =\"$categoryName\">$categoryName";
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
<?
foreach ($totalCategoryGroup as $value) {

	$valueTmp = str_replace("___", "\M", $value);
	$valueArray = explode ("\M", $valueTmp);
	$categoryName  = $valueArray[0];
	echo "<option value =\"$categoryName\">$categoryName";
}
?>
</SELECT>
</TD>
</TR>
</TABLE>
</fieldset>
<br>
<br>
<fieldset>
<legend>Group Selection</legend>
<?
$allGroupServers = get_total_group();
$allGroupServersList = implode (",", $allGroupServers);
$firstLevel = preg_grep ("/\./", $allGroupServers, PREG_GREP_INVERT);
$totalGroupGroup =  get_total_group_group($id);
?>

<TABLE class="menu">

<TR><TH class="menu">Server Groups</TH><TH class="menu"></TH><TH class="menu">Group Selected</TH></TR> 
<TR><TD colspan = "4" class="menu" id="groupSelected"></TD><TD class="menu"></TD><TD class="menu"></TD></TR>

<TR><TD class="menu"> 
<?
echo "<SELECT class=\"groups\" name = \"list3\" size = \"5\" onDblClick=\"navigate_next_level('$allGroupServersList');\">";

foreach ($firstLevel as $value) {

	$valueTmp = str_replace("___", "\M", $value);
	$valueArray = explode ("\M", $valueTmp);
	$serverGroup  = $valueArray[0];
	echo "<option value =\"$serverGroup\">$serverGroup";
}
?>
</SELECT>
</TD>
<TD class="menu" style="padding-top:20px;">
<INPUT TYPE="button" NAME="down" VALUE="&gt;&gt;" ONCLICK="moveSelectedGroup(this.form['list3'],this.form['list4'])">
</TD>
<TD class="menu">
<SELECT class="groupswide" name = "list4" size = "5" onDblClick="removeSelectedItem(this.form['list4'])">
<?
foreach ($totalGroupGroup as $value) {

	$valueTmp = str_replace("___", "\M", $value);
	$valueArray = explode ("\M", $valueTmp);
	$serverGroup  = $valueArray[0];
	echo "<option value =\"$serverGroup\">$serverGroup";
}
?>
</SELECT>
</TD></TR>

</TABLE>
</fieldset>

<?
$totalAgents = get_agent_for_mutiple_parent("%");
$totalAgentsString = implode (",", $totalAgents);
$totalAgentsString = str_replace("___", " ", $totalAgentsString);
$totalAgentGroup = get_total_agent_for_group($id);
$totalAgentGroupString = implode ("<br>", $totalAgentGroup);
?>
<br/>
<br/>
<fieldset>
<legend>Agent Selection</legend>
<p>You can enter 1 or multiple agent separated by newline. The format is 'hostname port_number' if you don't specify a port, it will default to the first port it finds</p>
<script language="JavaScript" type="text/javascript" src="richtext.js"></script>
<script language="JavaScript" type="text/javascript">
theForm = document.forms[0];
initRTE("images/","","",false);
writeRichText('manualAgents','',350,100,false,false);
<?
echo "theForm.manualAgents.value = \"$totalAgentGroupString\";";
echo "enableDesignMode(\"manualAgents\", \"$totalAgentGroupString\", \"false\");";

?>
</script>
<br>
<?
echo "<input class=\"submit-button\" name = \"validateAgent\" type=\"button\" onClick=\"validate_agents('$totalAgentsString', '1');\" value=\"VALIDATE AGENTS\">";


?>
</fieldset>

<br/>
<br>

<fieldset>
<legend>Submit</legend>

<?
if ($groupName != "administrator")  {
	echo "<input class=\"submit-button\" name = \"updateGroup\" type=\"button\"  onClick=\"validate_agents('$totalAgentsString','0');update_group_right();\" value=\"UPDATE GROUP\">";

}
else {
	echo "You cannot update the administrator group";
}
?>
</fieldset>
<INPUT TYPE="hidden" NAME=id>
<INPUT TYPE="hidden" NAME=action>
<INPUT TYPE="hidden" NAME=totalCategories>
<INPUT TYPE="hidden" NAME=totalGroups>
<INPUT TYPE="hidden" NAME=previousSelection>
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
</body>
</html>
