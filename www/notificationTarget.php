<fieldset>
    <legend>Selection Criteria</legend>
        <?
        $functionChosen = "4";
        include "getParameterDisplayBlackout.php";
        ?>
        <br>
        Select Status: 
        <SELECT  name = "status">
        <OPTION value = "BOTH">BOTH
        <OPTION value = "ALARM">ALARM
        <OPTION value = "WARNING">WARNING
        </SELECT>
        <?
        echo "<i><br/>Parameter Description: $paramDescription</i>";
        ?>
</fieldset>
<br/>
<br/>
<br/>


<fieldset>
    <legend>Notifcation Target Selection</legend>
    <br/>
    <p> Define email addresses (separated by commas) to receive alerts for a selected Category, Application Class or Parameter. </p>
    <label for = "notificationTarget">Email: </label>
    <input class="input-box2" name="notificationTarget">

     <br>
       <H3>Actions</H3>

        <INPUT class="checkbox" type="RADIO" name="action" VALUE="MERGE" CHECKED>Add new email targets to the list for the selected category, Application Class or parameter<br/>
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="REPLACE">Replace any existing email address with the entered values for the selected category, Application Class or parameter<br/>
        <!-- <INPUT class="checkbox" type="RADIO" name="action" VALUE="DELETE">Remove these email addresses from the list for the selected category, Application Class or parameter<br/> -->
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="DELETE_ALL">Remove all email addresses from the list for the selected category, Application Class or parameter<br/>
<br/>
<br/>
</fieldset>
<br/>
<br/>
<?
