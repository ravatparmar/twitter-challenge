<?php

/**
 * This file will be called be twitter when user authorize.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  Twitter
 * @author   Ravat Parmar <ravatparmar@hotmail.com>
 * @version  CVS: 1.0
 * @link     http://ravatparmar.com
 */
session_start();
require "twitteroauth-master/autoload.php";
require "inc/config.php";

use Abraham\TwitterOAuth\TwitterOAuth;

try {
    $oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');

    if (empty($oauth_verifier) || empty($_SESSION['oauth_token']) || empty($_SESSION['oauth_token_secret'])
    ) {
        header('Location: ./');
    }
    $_SESSION['oauth_verifier'] = $oauth_verifier;

    if (!isset($_SESSION['access_token'])) {
        $connection = new TwitterOAuth(
                CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']
        );

        $access_token = $connection->oauth(
                "oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]
        );
        
        $_SESSION['access_token'] = $access_token;
//        echo "hello";
    }
 //   print_r($_SESSION);
    header("location:home");
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}
