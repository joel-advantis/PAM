<?

// Load header, scripts, etc
include "header.php";
?>
<script src="advantis-common.js"></script>
<?
// Verify login
if (!verify()) {
    exit;
} else {
    ?>
    <script>
        //ChangeLoginLink();
    </script>

    <?
    // Make sure user is authorized for this page
    $userId = $_SESSION['user'];
    if (!check_rights("2")) {
        print_lack_of_privledge_warning();
    }
}

// Main page starts here
?>
<div id="centercontent" class="main">

    <form action="<? print $PHP_SELF; ?>" method="post" name = "approve">
    <h2>Request Approval</h2>
    <fieldset>
    <legend>Pending Approval</legend>
    <?

    // Setup tokens to prevent duplicate form submissions
    $userId = $_SESSION['user'];
    $sessionToken = $_SESSION['token'];

    if (($requestToReject != "") && ($sessionToken == $formToken)){
        $requestToReject = trim ($requestToReject);
        echo "Request ID $requestToReject Rejected successfully<BR>";
        reject_requests ($requestToReject, $userId, $Approvercomment);
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }
    elseif (($requestToApprove != "") && ($sessionToken == $formToken)){
        $requestToApprove = trim ($requestToApprove);
        update_pcm($requestToApprove, $rulesetName); 
        approve_requests($requestToApprove, $userId, $Approvercomment);
        echo "Request ID $requestToApprove Approved and PCM ruleset $rulesetName Updated successfully<BR>";
        $sessionToken  = $sessionToken + 1;
        $_SESSION["token"] = $sessionToken;

    }

    ?>
    <INPUT TYPE="hidden" NAME=formToken value="<? print $sessionToken; ?>">
    <?

    // We need to get all the requests that are pending approval
    $totalRequests = get_all_requests(1);

    if (!count($totalRequests)) {
        ?>
        No requests pending approval <BR/>
        </fieldset> <? /* End of Pending Approval */ 
    }
    else {
        ?>

        <p>Click on the request id to view details and approve it or reject it</p><BR>

        <?
        // Print table of requests pending approval
        display_all_request_for_approval($totalRequests);
        ?>

            <br>
        </fieldset> <? /* End of Pending Approval */ ?>
        <br>
        <br>
        <?
        }
    ?>
    <input type="hidden" name="requestToReject">
    <input type="hidden" name="requestToApprove">
    </form>



    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
</div>
</body>
</html>
<?
