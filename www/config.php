<?

// APPLICATION MODE (Don't change these unless instructed by Advantis Support)

    $DEMO_MODE = 0;
    $WITH_MCRYPT = 1;

// LDAP SETTINGS

    // TO ENABLE LDAP AUTHENTICATION, SET USE_LDAP TO 1, AND SET LDAP_SERVER TO THE APPROPRIATE HOSTHAME OR IP ADDRESS
    $USE_LDAP = 0;

    $LDAP_SERVER = "AD.BlS.COM";

    // DEFAULT LDAP PORT IS 389
    $LDAP_PORT = 389;

// DIRECTORY SETTINGS

    // NOTE: ESCAPE DIRECTORY SEPARATORS FOR WINDOWS PATHS (EXAMPLE: C:\\WINDOWS, NOT C:\WINDOWS)
    $PCM_RULESETS_DIR = "C:\\BMC\\pconfmgr\\rulesets";
    $PCM_PENDING_DIR  = $RULESETS_DIR . "\\ChangeSpring\\PENDING_FOLDER";
    $REPORT_DIR  = "logs/cache";
    $MESSAGE_LOG = "logs/pam.log";

// DATABASE SETTINGS

    $DB_TYPE    = "MYSQL";                 // ODBC or MYSQL
    $ODBC_DSN   = "";                      // SET THIS IF ODBC IS USED

    #$DB_TYPE    = "ODBC";                 // ODBC or MYSQL
    #$ODBC_DSN   = "myodbcLocal";          // SET THIS IF ODBC IS USED

    if (! $DEMO_MODE) {
        $SQL_SERVER     = "localhost";     // DATABASE SERVER NAME
        $SQL_DATABASE   = "pamdb"; // DATABASE NAME (INSTANCE NAME)
        #$SQL_DATABASE   = "patrol_report"; // DATABASE NAME (INSTANCE NAME)
        $SQL_USER       = "webpatrol";     // DATABASE USER NAME
        $SQL_PASSWORD   = "/8Wkn436";      // DATABASE PASSWORD
    } 











// DON'T CHANGE THIS
    foreach($_POST as $key=>$value) {
        eval("$$key = \"$value\";");
    }


// NOT USED
    #
    #// EDIT THE FOLLOWING VARS TO CHANGE THE REPORT COLORS
    #$header_row_bg_color = "#000066";   // Top row of report background color
    #$header_row_fg_color = "#FFFFFF";   // Top row of report text color
    #$alt_1_row_bg_color = "#FFFFFF";    // First alternating row color
    #$alt_2_row_bg_color = "#CCCCCC";    // Second alternating row color
    #$text_color = "#000066";            // Color of text on page
    #$highlight_bg_color = "#336699";    // Background color of row when mouse is over
    #$highlight_fg_color = "#FFFFFF";    // Text color of row when mouse is over

?>
