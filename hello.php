<?php

session_start();
require "twitteroauth-master/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

try {
    $oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');

    if (empty($oauth_verifier) ||
            empty($_SESSION['oauth_token']) ||
            empty($_SESSION['oauth_token_secret'])
    ) {
        header('Location: ./');
    }
    $_SESSION['oauth_verifier'] = $oauth_verifier;

    $consumer_key = "wIUuewrYadH1STO6h6gzW5TIJ";
    $consumer_secret = "z3aLLJP95xUDlEEd3jJ6IHzpiqTOdpXDtBgKXWRfNtXVmvMvyZ";

    if (!isset($_SESSION['access_token'])) {
        $connection = new TwitterOAuth(
                $consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']
        );

        $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
        $_SESSION['access_token'] = $access_token;
    }

    header("location:home");
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}
