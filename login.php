<?php

session_start();
require "twitteroauth-master/autoload.php";
require "inc/config.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

$request_token = $connection->oauth(
        'oauth/request_token', [
    'oauth_callback' => 'http://ravatparmar.com/twitter/hello'
        ]
);

if ($connection->getLastHttpCode() != 200) {
    throw new \Exception('There was a problem performing this request');
}

$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$url = $connection->url(
        'oauth/authorize', [
    'oauth_token' => $request_token['oauth_token']
        ]
);
header('Location: ' . $url);