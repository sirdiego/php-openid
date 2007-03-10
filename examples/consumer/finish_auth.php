<?php

require_once "common.php";
session_start();

function run() {
    $consumer = getConsumer();

    // Complete the authentication process using the server's
    // response.
    $response = $consumer->complete($_GET);

    // Check the response status.
    if ($response->status == Auth_OpenID_CANCEL) {
        // This means the authentication was cancelled.
        $msg = 'Verification cancelled.';
    } else if ($response->status == Auth_OpenID_FAILURE) {
        // Authentication failed; display the error message.
        $msg = "OpenID authentication failed: " . $response->message;
    } else if ($response->status == Auth_OpenID_SUCCESS) {
        // This means the authentication succeeded; extract the
        // identity URL and Simple Registration data (if it was
        // returned).
        $openid = $response->identity_url;
        $esc_identity = htmlspecialchars($openid, ENT_QUOTES);

        $success = sprintf('You have successfully verified ' .
                           '<a href="%s">%s</a> as your identity.',
                           $esc_identity, $esc_identity);

        if ($response->endpoint->canonicalID) {
            $success .= '  (XRI CanonicalID: '.$response->endpoint->canonicalID.') ';
        }

        $sreg = $response->extensionResponse('sreg', true);

        if (@$sreg['email']) {
            $success .= "  You also returned '".$sreg['email']."' as your email.";
        }

        if (@$sreg['postcode']) {
            $success .= "  Your postal code is '".$sreg['postcode']."'";
        }
    }

    include 'index.php';
}

run();

?>