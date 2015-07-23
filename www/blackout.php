<fieldset>
    <legend>Selection Criteria</legend>
        <?
        $functionChosen = "4";
        include "getParameterDisplayBlackout.php";
        echo "<i><br/>$paramDescription</i>";
        ?>
</fieldset>
<br/>
<br/>
<br/>


<fieldset>
    <legend>Blackout Selection</legend>
    <br/>
    <p> Define a blackout window for one or more days of the week.  <br/>
    <br/>Time should be entered on a 24-hour clock.
    <br/>Examples - 8:15 or 22:30</p>

    <H3>Blackout Definition</H3>
    <TABLE>
    <TR><TH colspan=2>START</TH></TR>
    <TR><TD NOWRAP>TIME &nbsp;&nbsp;</TD><TD>&nbsp;<input class="time" name="starttime"></TD></TR>
    <TR><TD>DAY</TD>
    <TD NOWRAP>
    <INPUT class="checkbox" type="radio" name="startday" value="86400" >Monday    &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="startday" value="172800" >Tuesday   &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="startday" value="259200" >Wednesday &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="startday" value="345600" >Thursday  &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="startday" value="432000" >Friday    &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="startday" value="518400" >Saturday  &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="startday" value="0" >Sunday    &nbsp; &nbsp;
    </TD></TR>
    </TABLE>
    <br/>
    <br/>
    <TABLE>
    <TR><TH colspan=2>END</TH></TR>
    <TR><TD NOWRAP>TIME &nbsp;&nbsp;</TD><TD>&nbsp;<input class="time" name="endtime"></TD></TR>
    <TR><TD>DAY</TD>
    <TD NOWRAP>
    <INPUT class="checkbox" type="radio" name="endday" value="86400" >Monday    &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="endday" value="172800" >Tuesday   &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="endday" value="259200" >Wednesday &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="endday" value="345600" >Thursday  &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="endday" value="432000" >Friday    &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="endday" value="518400" >Saturday  &nbsp; &nbsp;
    <INPUT class="checkbox" type="radio" name="endday" value="0" >Sunday    &nbsp; &nbsp;
    </TD></TR>
    </TABLE>

     <br>
        <!-- Action You want to take:--><BR>
        <H3>Actions</H3>
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="MERGE" CHECKED>Add new blackout period to existing lists for the selected category, Application Class or parameter<br/>
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="REPLACE">Replace any existing blackout with these values for the selected category, Application Class or parameter<br/>
        <!-- <INPUT class="checkbox" type="RADIO" name="action" VALUE="DELETE">Remove this blackout period from selected lists for the selected category, Application Class or parameter<br/> -->
        <INPUT class="checkbox" type="RADIO" name="action" VALUE="DELETE_ALL">Remove all blackout periods from the list for the selected category, Application Class or parameter<br/>

<br/>
<br/>
</fieldset>
<br/>
<br/>
<br/>

<INPUT type="HIDDEN" name="starttimeseconds">
<INPUT type="HIDDEN" name="endtimeseconds">
<?
