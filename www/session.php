<?
session_start();

include "config/config.php";
include "advantis-functions.php";

// Enable/disable debug

if (isset($_GET["debug"])) {
    $debug = $_GET["debug"];
    $_SESSION["debug"] = $debug;
    $username = $_SESSION['username'];

    if ($debug) {
        debug_msg("Debugging enabled for user $username.");
    } else {
        log_entry("DEBUG: Debugging disabled for user $username.");
    }


} elseif (isset($_SESSION["debug"])) {
    $debug = $_SESSION["debug"];
}


// Get user data for the session
$userId     = $_SESSION['user'];
$userRights = $_SESSION['rights'];

// Output session variables to debug window
#debug_var ("User details",      get_user_name ($userId));
debug_var ("Session variables", $_SESSION);
debug_var ("Server variables",  $_SERVER);

// Log session if it hasn't been logged yet

#if (!$_SESSION['logged']) {
#    log_session ("login");
#}
?>
