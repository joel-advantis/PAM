<?
/*******************************************************************************
 *  PATROL Agent Manager
 *
 *  Build and display report for Database Query (querydb.php)
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/

session_start();
include "config/config.php";
?>
<html>
<head>
<title>Configuration Report</title>
<link rel=stylesheet type=text/css href=css/dolphin.css>

</head>
<body text="<? print $text_color; ?>">
<div align="center">
<?

$key = "pam";

$iv = substr(md5($key), 0,mcrypt_get_iv_size (MCRYPT_CAST_256,MCRYPT_MODE_CFB));
$DECRIPT_PASSWORD = trim(chop(base64_decode($SQL_PASSWORD)));
$DECRIPT_PASSWORD = mcrypt_cfb (MCRYPT_CAST_256, $key, $DECRIPT_PASSWORD, MCRYPT_DECRYPT, $iv);

$connection = mysql_connect("$SQL_SERVER","$SQL_USER","$DECRIPT_PASSWORD") or die ("Unable to connect to MySQL server.");
$db = mysql_select_db("$SQL_DATABASE") or die ("Unable to select requested database.");

if($print_title == "yes") {
    if($report_title == "") {
        $report_title = "$SQL_DATABASE -> $SQL_TABLE";
    }
    print ("<br><br><b>$report_title</b><p>");
}

$fields = mysql_list_fields("$SQL_DATABASE", "$SQL_TABLE", $connection);

$columns = mysql_num_fields($fields);

$file = fopen('EXPORT_REPORT.csv', 'w');  // erase textdata.txt if itexists!!
$fp = fopen("EXPORT_REPORT.csv", "a+");

$num = 0;
$next_field_order = 0;

while ($next_field_order < "$columns") {
    for ($i = 0; $i < $columns; $i++) {
        if(${"field_order".$i} == "$next_field_order") {
            $next_field_order += 1;

            if(${"field_number".$i} != ""){
                $num += 1;
                ${"field_".$num} = mysql_field_name($fields, $i);
                ${"search_type_".$num} = ${"search_type".$i};
                ${"search_value_".$num} = ${"search_value".$i};

                $svtext = "search_value_";

                $fields_info = ($fields_info."&field_number".$i."=".${"field_".$num}."&search_type".$i."=".${"search_type_".$num}."&search_value".$i."=".${$svtext.$num});

                if($num == 1) {
                    $search_query = ${"field_".$num}." ".${"search_type_".$num}." '${$svtext.$num}'";
                } else {
                    $search_query = $search_query." AND ".${"field_".$num}." ".${"search_type_".$num}." '${$svtext.$num}'";
                }
            }
        }
    }
}

if($per_page == "") {$per_page = 1000;}
if($limit == "") {$limit = 0;}
if($group_by != "") {$group_by = "GROUP BY ".$group_by;}

if($search_query == "") {
    print "<br><big><b>No Search Criteria Was Selected!</b></big>";
} else {
    $sql = mysql_query("SELECT * FROM $SQL_TABLE WHERE $search_query $group_by ORDER BY $sort_by $order_by LIMIT $limit,$per_page");

    print ("<table border=0 cellspacing=0 cellpadding=3>
        <tr style=\"background: $header_row_bg_color;color: $header_row_fg_color;\">");

    if($line_numbers == "yes") {
        print ("<td valign=top align=center style=\"border-left:solid 1px #666666;border-right:solid 1px #666666;\"><b># &nbsp</b></td>");
    }
    $num = 0;
    $next_field_order = 0;

    while ($next_field_order < "$columns") {
        for ($i = 0; $i < $columns; $i++) {
            if(${"field_order".$i} == "$next_field_order") {
                $next_field_order += 1;

                if(${"field_number".$i} != ""){
                    $num += 1;
                    ${"field_".$num} = mysql_field_name($fields, $i);
                    $ftext = "field_";
                    $field_name = ${$ftext.$num};
                    $field_description = explode("_",$field_name);
                    $field_description1 = ucfirst($field_description[0]);
                    $field_description2 = ucfirst($field_description[1]);
                    $field_description3 = ucfirst($field_description[2]);
                    $field_description4 = ucfirst($field_description[3]);
                    $field_description5 = ucfirst($field_description[4]);
                    $field_description6 = ucfirst($field_description[5]);
                    $field_description7 = ucfirst($field_description[6]);
                    $field_description8 = ucfirst($field_description[7]);
                    $field_description9 = ucfirst($field_description[8]);
                    $field_description10 = ucfirst($field_description[9]);
                    print ("<td valign=top align=center style=\"border-left:solid 1px #666666;border-right:solid 1px #666666;\"><b>$field_description1 $field_description2 $field_description3 $field_description4 $field_description5 $field_description6 $field_description7 $field_description8 $field_description9 $field_description10</b></td>");
                    if($num == "1") {$column_headings = $field_name;} else {$column_headings = $column_headings.",".$field_name;}
                }
            }
        }
    }

    fwrite($fp,""."$column_headings\n");

    print ("</tr>");

    $color = 0;

    $line_numbering = 0;

    while ($row = mysql_fetch_array($sql)) {
        if($color == 0) {$alt_color = $alt_1_row_bg_color;$color = 1;} else {$alt_color = $alt_2_row_bg_color;$color = 0;}

        print ("<tr style=\"background:$alt_color;\" onMouseOut = \"this.style.color = '$text_color';this.style.background ='$alt_color';\" onMouseOver = \"this.style.color = '$highlight_fg_color';this.style.background = '$highlight_bg_color';\">");

        $line_numbering += 1;
        if($line_numbers == "yes") {
            print ("<td valign=top align=center style=\"border-left:solid 1px #666666;border-right:solid 1px #666666;\"><b>$line_numbering &nbsp</b></td>");
        }
        $num = 0;
        $next_field_order = 0;

        while ($next_field_order < "$columns") {
            for ($i = 0; $i < $columns; $i++) {
                if(${"field_order".$i} == "$next_field_order") {
                    $next_field_order += 1;

                    if(${"field_number".$i} != ""){
                        $num += 1;
                        ${"field_".$num} = mysql_field_name($fields, $i);
                        $ftext = "field_";
                        $row_id = ${$ftext.$num};

                        if(${"number_format".$i} == ""){
                            $cell_data = $row[$row_id];
                            $cell_data_tot = $row[$row_id];
                            $c_align = "left";
                        } else {
                            $cell_data = number_format($row[$row_id],2);
                            $cell_data_tot = $row[$row_id];
                            $c_align = "right";
                        }
                        if(${"total_col".$i} != ""){${$row_id."_total"} = ${$row_id."_total"}+$cell_data_tot;}
                        print ("<td valign=top align=$c_align style=\"border-left:solid 1px #666666;border-right:solid 1px #666666;\"><b>$cell_data &nbsp</b></td>");
                        if($num == "1") {$column_data = $cell_data;} else {$column_data = $column_data.",".$cell_data;}
                    }
                }
            }
        }

        fwrite($fp,""."$column_data\n");

        print ("</tr>");
    }

    if($any_totals == "yes") {
        print ("<tr><td style=\"border:solid 2px #666666;background: $header_row_bg_color;color: $header_row_fg_color;\" colspan=$columns align=center><b> &nbsp TOTALS &nbsp </b></td></tr>");

        print ("<tr>");

        if($line_numbers == "yes") {
            print ("<td valign=top align=center style=\"border-left:solid 1px #666666;border-right:solid 1px #666666;\"><b> &nbsp</b></td>");
        }

        $num = 0;
        $next_field_order = 0;

        while ($next_field_order < "$columns") {
            for ($i = 0; $i < $columns; $i++) {
                if(${"field_order".$i} == "$next_field_order") {
                    $next_field_order = $next_field_order+1;

                    if(${"field_number".$i} != ""){
                        $num = $num+1;
                        ${"field_".$num} = mysql_field_name($fields, $i);
                        $ftext = "field_";
                        $row_id = ${$ftext.$num};

                        if(${"number_format".$i} == ""){
                            $c_align = "left";
                            $col_total = ${$row_id."_total"};
                        } else {
                            $c_align = "right";
                            $col_total = number_format(${$row_id."_total"},2);
                        }

                        print ("<td valign=top align=$c_align style=\"border-left:solid 1px #666666;border-right:solid 1px #666666;\"><b>$col_total &nbsp</b></td>");
                        if($num == "1") {$column_data = $col_total;} else {$column_data = $column_data.",".$col_total;}
                    }
                }
            }
        }
    }

    print ("<tr><td style=\"border-top:solid 2px #666666\" colspan=$columns align=center><b> &nbsp End Of Page &nbsp </b></td></tr>");
?>
</table>
<?
    fclose($fp);
}
?>
<p>
<a href="EXPORT_REPORT.csv">Export Data</a>
( Right click on "Export Data" and select "Save Target As..." to save the file to your computer. )
<p>
<a href="javascript:void(window.close())">Close Window</a>
<p>
</div>
</body>
</html>
