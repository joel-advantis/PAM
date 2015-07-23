<?

$messageWording = get_all_messageWording("");
sort ($messageWording);
$messageWording = array_unique ($messageWording);
?>
<fieldset>
<legend>Available messages</legend>
<p> These are all the message rewording that were
              already in the enterprise, by selecting one,
              the data will appear in the message box and you
              can use it as a template to the new message
</p>


<SELECT name = "totalmessage" size = "5" onChange="populate(1)" STYLE="float:top:right;width:600px;">
<?
foreach ($messageWording as $value) {

    $valueTmp = str_replace("___", "\M", $value);
    $valueArray = explode ("\M", $valueTmp);
    $messageWording = $valueArray[1];
    $messageDisplay = substr ($value, 0, 30);
    $value = str_replace ("___", ": ", $value);
    if ($totalmessage == $value) {
        echo "<option value =\"$messageWording\" SELECTED>$value";
    }
    else {
        echo "<option value =\"$messageWording\">$value";
    }
}
?>
</SELECT>
<BR/><BR/>
</fieldset>

<BR/><BR/>

<fieldset>
    <legend>Selection Criteria</legend>
        <?
        $functionChosen = "2";
        include "getParameterDisplay.php";
        ?>
	<br>
        Select Status: 
        <SELECT  name = "status">
        <OPTION value = "">ANY_STATUS
        <OPTION value = "ALARM">ALARM
        <OPTION value = "WARNING">WARNING
        <OPTION value = "INFORMATION">INFORMATION
        </SELECT>
        <br>
        
        <?
        echo "<i><br/>Parameter Description: <b>$paramDescription</i></b>";
        ?>
</fieldset>
<br/>
<br/>
<br/>


<fieldset>
<legend>Message</legend>
<P>Type the message wording you would like for this alert, or select one from the "Available messages" or "Host-specific messages" boxes</p>
<label for="messageValue">Message:</label>
<?
echo "<input class=\"input-box2\"  name=\"message\" value=\"$message\"><br/>";
?>

<a href="getdetails.php?changeType=2" target="_blank">Show macros</a>
</fieldset>
<br/>
<br/>
<br/>



<fieldset>
<legend>Host-specific messages</legend>
<?
$totalHosts = get_all_hosts_for_user($_SESSION['user']);
$allAgentsString = implode (",", $totalHosts);
$allAgentsString = str_replace("___", " ", $allAgentsString);

?>
<div align = "left">
<p class="it">
By Selecting a host you can populate all the message rewording
that already exists for the selected parameter
</p>
<?
echo "<input class=\"requestid-box\" name=\"hostTemplate\" value = \"$hostTemplate\">";

echo "<input class=\"submit-button2\" name = \"validateAgent\" type=\"button\" onClick=\"get_host_settings('$allAgentsString');\" value=\"Get host settings\">";
?>
<br>
<SELECT name = "host" size = "5" onChange="populate_hostTemplate()">
<?
foreach ($totalHosts as $value) {

    $value = str_replace("___", " ", $value);
    if ($value == $host) {
        echo "<option value =\"$value\" SELECTED>$value";
    }
    else {
        echo "<option value =\"$value\">$value";
    }
}
?>

</SELECT>

<?

if ($hostTemplate) {
    # Get all the message wording for that host
    $messageForHost = get_all_messageWording_for_host($hostTemplate);
    if ($param) {
        #$messageForHost = preg_grep("/$param/", $messageForHost);
    }
    if (count($messageForHost)) {
        echo "<SELECT name = \"messageForHost\" size = \"5\" onChange=\"populate(2)\" STYLE = \"width:500px;\">";
        foreach ($messageForHost as $value) {
            $valueTmp = str_replace("___", "\M", $value);
            $valueArray = explode ("\M", $valueTmp);
            $messageWording = $valueArray[1];
            $value = str_replace("___", ": ", $value);
            if ($messageWording == $messageForHost) {
                echo "<option value =\"$messageWording\" SELECTED>$value";
            }
            else {
                echo "<option value =\"$messageWording\">$value";
            }

        }
        echo "</SELECT>";
        echo "<BR/><BR/>";
    }
    else {
        echo "No message wording for $param on $host <BR/>";
    }
}
?>
</div>
</fieldset>
<?
