<?
include "header.php";

?>
<div id="centercontent" class="main">
<script src="advantis-common.js"></script>

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

<h2>Query the Database</h2>

<?
// CHANGE TABLE
if($clear_table == "yes") {
    $SQL_TABLE = "";
}

// CHANGE DATABASE
if($clear_database == "yes") {
    $SQL_DATABASE = "";
    $SQL_TABLE = "";
}

// CHANGE ALL MYSQL INFORMATION
if($clear_all == "yes") {
    $SQL_SERVER = "";
    $SQL_USER = "";
    $SQL_PASSWORD = "";
    $SQL_DATABASE = "";
    $SQL_TABLE = "";
}

if(isset($SQL_SERVER)){$_SESSION[SQL_SERVER] = $SQL_SERVER;}
if(isset($SQL_USER)){$_SESSION[SQL_USER] = $SQL_USER;}
if(isset($SQL_PASSWORD)){$_SESSION[SQL_PASSWORD] = $SQL_PASSWORD;}
if(isset($SQL_DATABASE)){$_SESSION[SQL_DATABASE] = $SQL_DATABASE;}
if(isset($SQL_TABLE)){$_SESSION[SQL_TABLE] = $SQL_TABLE;}

if($SQL_TABLE != "") {


    if ($WITH_MCRYPT) {
        $key = "pam";

        $iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
        $DECRIPT_PASSWORD = trim(chop(base64_decode($SQL_PASSWORD)));
        $DECRIPT_PASSWORD = mcrypt_cfb (MCRYPT_CAST_256, $key, $DECRIPT_PASSWORD, MCRYPT_DECRYPT, $iv);
    } else {
        $DECRIPT_PASSWORD = $SQL_PASSWORD;
    }

    $connection = mysql_connect("$SQL_SERVER","$SQL_USER","$DECRIPT_PASSWORD") or die ("Unable to connect to MySQL server.");
    $fields = mysql_list_fields("$_SESSION[SQL_DATABASE]", "$_SESSION[SQL_TABLE]", $connection);
    $columns = mysql_num_fields($fields);
?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function checkAll(theForm){
    for (var j = 0; j <= "<? print $columns; ?>"; j++)  {
        box = eval("document.form.field_number" + j);
        if (box.checked == false) box.checked = true;
    }
}

function uncheckAll(theForm){
    for (var j = 0; j <= "<? print $columns; ?>"; j++)  {
        box = eval("document.form.field_number" + j);
        if (box.checked == true) box.checked = false;
    }
}
//  End -->
</SCRIPT>
<?
}
?>
<body text="<? print $text_color; ?>">

<br>
<table border=0 cellspacing=0 cellpadding=3>
    <tr>
        <form action="<? print $PHP_SELF; ?>" method="post">
        <td align=center>
            <input type="hidden" name="clear_table" value="yes">
            <input type="submit" value="Change Table">
        </td>
        </form>
    </tr>
</table>
<br>
<?
if($_SESSION[SQL_SERVER] == "" OR $_SESSION[SQL_USER] == "" OR $_SESSION[SQL_PASSWORD] == "") {
?>
    <table>
        <form action="<? print $PHP_SELF; ?>" method="post">
        <tr>
            <td align=right><b>MySQL Server Name:</b></td>
            <td><input type="text" name="SQL_SERVER"></td>
        </tr>
        <tr>
            <td align=right><b>MySQL User Name:</b></td>
            <td><input type="text" name="SQL_USER"></td>
        </tr>
        <tr>
            <td align=right><b>MySQL Password:</b></td>
            <td><input type="password" name="SQL_PASSWORD"></td>
        </tr>
        <tr>
            <td colspan=2 align=center>
                <input type="submit" value="Submit">
                <p>
            </td>
        </tr>
        </form>
    </table>
<?
    exit;
} else {

    $key = "pam";
    $iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
    $DECRIPT_PASSWORD = trim(chop(base64_decode($SQL_PASSWORD)));
    $DECRIPT_PASSWORD = mcrypt_cfb (MCRYPT_CAST_256, $key, $DECRIPT_PASSWORD, MCRYPT_DECRYPT, $iv);

    $connection = mysql_connect("$SQL_SERVER","$SQL_USER","$DECRIPT_PASSWORD") or die ("Unable to connect to MySQL server.");
}

if($_SESSION[SQL_DATABASE] == "") {
?>
    <table>
        <form action="<? print $PHP_SELF; ?>" method="post">
        <tr>
            <td align=right><b>MySQL Database:</b></td>
            <td><input type="text" name="SQL_DATABASE"></td>
        </tr>
        <tr>
            <td colspan=2 align=center>
                <input type="submit" value="Submit">
                <p>
            </td>
        </tr>
        </form>
    </table>
<?
    exit;
} else {
    $db = mysql_select_db("$_SESSION[SQL_DATABASE]") or die ("Unable to select requested database.");
}

if($_SESSION[SQL_TABLE] == "") {
?>
    <table>
        <form action="<? print $PHP_SELF; ?>" method="post">
        <tr>
            <td align=right><b>Database Table:</b></td>
            <td>
                <select type="text" name="SQL_TABLE">
<?
                $result = mysql_list_tables($_SESSION[SQL_DATABASE]);
                if (!$result) {
                    print "DB Error, could not list tables\n";
                    print 'MySQL Error: ' . mysql_error();
                    exit;
                }

                while ($row = mysql_fetch_row($result)) {
                    # include on the patrol tables
                    print "<option value=\"$row[0]\">$row[0]</option>";
                }

                mysql_free_result($result);
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan=2 align=center>
                <input type="submit" value="Submit">
                <p>
            </td>
        </tr>
        </form>
    </table>
<?
    exit;
}
?>
<table border=0 cellspacing=0 cellpadding=3>
    <form action="generate.php" target="_blank" method="post" name="form">
    <tr style="background:<? print $header_row_bg_color; ?>;color:<? print $header_row_fg_color; ?>;">
        <td style="border-top: 2px solid <? print $header_row_bg_color; ?>;border-left: 2px solid <? print $header_row_bg_color; ?>;" align=center valign=bottom><b> &nbsp Field &nbsp <br> &nbsp Name &nbsp </b></td>
        <td style="border-top: 2px solid <? print $header_row_bg_color; ?>;" align=center valign=bottom><b> &nbsp Operator &nbsp </b></td>
        <td style="border-top: 2px solid <? print $header_row_bg_color; ?>;" align=center valign=bottom><b> &nbsp Value &nbsp <br> &nbsp ('%%' = wildcard) &nbsp </b></td>
        <td style="border-top: 2px solid <? print $header_row_bg_color; ?>;" align=center valign=bottom><b> &nbsp Number &nbsp <br> &nbsp Format &nbsp </b></td>
        <td style="border-top: 2px solid <? print $header_row_bg_color; ?>;" align=center valign=bottom><b> &nbsp Total &nbsp </b></td>
        <td style="border-top: 2px solid <? print $header_row_bg_color; ?>;border-right: 2px solid <? print $header_row_bg_color; ?>;" align=center valign=bottom><b> &nbsp Field &nbsp <br> &nbsp Order &nbsp </b></td>
    </tr>
    <?
    $fields = mysql_list_fields("$_SESSION[SQL_DATABASE]", "$_SESSION[SQL_TABLE]", $connection);
    $columns = mysql_num_fields($fields);
    for ($i = 0; $i < $columns; $i++) {
    $field_name = mysql_field_name($fields, $i);
        if($color == 0) {$alt_color = $alt_1_row_bg_color;$color = 1;} else {$alt_color = $alt_2_row_bg_color;$color = 0;}

        print ("<tr style=\"background:$alt_color;\">
            <td style=\"border-left: 2px solid $header_row_bg_color;\">
                <input type=\"checkbox\" name=\"field_number$i\" value=\"$field_name\"> &nbsp <b>$field_name</b>
            </td>
            <td>
                <select name=\"search_type$i\">
                <option value=\"LIKE\">LIKE</option>
                <option value=\"NOT LIKE\">NOT LIKE</option>
                <option value=\"=\">=</option>
                <option value=\"!=\">!=</option>
                <option value=\"<\"><</option>
                <option value=\">\">></option>
                <option value=\"<=\"><=</option>
                <option value=\">=\">>=</option>
                </select>
            </td>
            <td>
                <input type=\"text\" name=\"search_value$i\" value=\"%%\">
            </td>
            <td align=center>
                <input type=\"checkbox\" name=\"number_format$i\">
            </td>
            <td align=center>
                <input type=\"checkbox\" name=\"total_col$i\">
            </td>
            <td align=center style=\"border-right: 2px solid $header_row_bg_color;\">
                <input type=\"text\" style=\"text-align:center;\" name=\"field_order$i\" size=\"4\" maxlength=\"4\" value=\"$i\">
            </td>
        </tr>");
    }
    print ("<tr>
        <td style=\"border-left: 2px solid $header_row_bg_color;border-right: 2px solid $header_row_bg_color;\" colspan=6>
            <input type=\"button\" onClick=\"javascript:checkAll(this.form);\" value=\"Check All\">
            <input type=\"button\" onClick=\"javascript:uncheckAll(this.form);\" value=\"Uncheck All\">
        </td>
    </tr>");
    ?>
    <tr>
        <td style="border-left: 2px solid <? print $header_row_bg_color; ?>;border-top: 2px solid <? print $header_row_bg_color; ?>;" align=right><b>Sort By: &nbsp</b></td>
        <td style="border-top: 2px solid <? print $header_row_bg_color; ?>;">
            <select name="sort_by">
            <?
            $fields = mysql_list_fields("$_SESSION[SQL_DATABASE]", "$_SESSION[SQL_TABLE]", $connection);
            $columns = mysql_num_fields($fields);
            for ($i = 0; $i < $columns; $i++) {
                $field_name = mysql_field_name($fields, $i);
                print ("<option value=\"$field_name\">$field_name</option>");
            }
            ?>
            </select>
        </td>
        <td style="border-right: 2px solid <? print $header_row_bg_color; ?>;border-top: 2px solid <? print $header_row_bg_color; ?>;" colspan=4>
            <select name="order_by">
            <option value="ASC">ASC</option>
            <option value="DESC">DESC</option>
            </select>
        </td>
    </tr>
    <tr>
        <td style="border-left: 2px solid <? print $header_row_bg_color; ?>;" align=right><b>Group By: &nbsp</b></td>
        <td style="border-right: 2px solid <? print $header_row_bg_color; ?>;" colspan=5>
            <select name="group_by">
            <option value=""></option>
            <?
            $fields = mysql_list_fields("$_SESSION[SQL_DATABASE]", "$_SESSION[SQL_TABLE]", $connection);
            $columns = mysql_num_fields($fields);
            for ($i = 0; $i < $columns; $i++) {
                $field_name = mysql_field_name($fields, $i);
                print ("<option value=\"$field_name\">$field_name</option>");
            }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td style="border-left: 2px solid <? print $header_row_bg_color; ?>;" align=right><b>Starting At Record: &nbsp</b></td>
        <td style="border-right: 2px solid <? print $header_row_bg_color; ?>;" colspan=5><input type="limit" name="limit" value="0"></td>
    </tr>
    <tr>
        <td style="border-left: 2px solid <? print $header_row_bg_color; ?>;" align=right><b>Number Of Results To Show: &nbsp</b></td>
        <td style="border-right: 2px solid <? print $header_row_bg_color; ?>;" colspan=5><input type="limit" name="per_page" value="10000"></td>
    </tr>
    <tr>
        <td style="border-left: 2px solid <? print $header_row_bg_color; ?>;" align=right><b>Any Totals?: &nbsp</b></td>
        <td style="border-right: 2px solid <? print $header_row_bg_color; ?>;" colspan=5><input type="radio" name="any_totals" value="no" CHECKED><b>NO</b> &nbsp <input type="radio" name="any_totals" value="yes"><b>YES</b></td>
    </tr>
    <tr>
        <td style="border-left: 2px solid <? print $header_row_bg_color; ?>;" align=right><b>Number Lines?: &nbsp</b></td>
        <td style="border-right: 2px solid <? print $header_row_bg_color; ?>;" colspan=5><input type="radio" name="line_numbers" value="no" ><b>NO</b> &nbsp <input type="radio" name="line_numbers" value="yes" CHECKED><b>YES </b></td>
    </tr>
    <tr>
        <td style="border-left: 2px solid <? print $header_row_bg_color;?>;" align=right><b>Title: &nbsp</b></td>
        <td style="border-right: 2px solid <? print $header_row_bg_color; ?>;" colspan=5><input type="text" name="report_title"></td>
    </tr>
    <tr>
        <td style="border-bottom: 2px solid <? print $header_row_bg_color; ?>;border-left: 2px solid <? print $header_row_bg_color; ?>;" align=right><b>Print Title?: &nbsp</b></td>
        <td style="border-bottom: 2px solid <? print $header_row_bg_color; ?>;border-right: 2px solid <? print $header_row_bg_color; ?>;" colspan=5><input type="radio" name="print_title" value="no" CHECKED><b>NO</b> &nbsp <input type="radio" name="print_title" value="yes"><b>YES</b></td>
    </tr>
    <tr>
        <td colspan=5 align=center>
            &nbsp
        </td>
    <tr>
        <td colspan=5 align=center>
            <input type=hidden name="SQL_SERVER" value="<? print $_SESSION[SQL_SERVER]; ?>">
            <input type=hidden name="SQL_USER" value="<? print $_SESSION[SQL_USER]; ?>">
            <input type=hidden name="SQL_PASSWORD" value="<? print $_SESSION[SQL_PASSWORD]; ?>">
            <input type=hidden name="SQL_DATABASE" value="<? print $_SESSION[SQL_DATABASE]; ?>">
            <input type=hidden name="SQL_TABLE" value="<? print $_SESSION[SQL_TABLE]; ?>">
            <input type="submit" value="Generate Report">
            <p>
        </td>
    </tr>
    </form>
</table>
</div>

</td>
</tr>
</table>
</td>
</tr>
<div>
<?
