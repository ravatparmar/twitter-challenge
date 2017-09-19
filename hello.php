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
        
        $twitter = new TwitterOAuth(
            CONSUMER_KEY, CONSUMER_SECRET, 
            $_SESSION['access_token']['oauth_token'], 
            $_SESSION['access_token']['oauth_token_secret']
        );
        $tweets1 = $twitter->get('account/verify_credentials');
       /* print_r($tweets1);
        echo $tweets1->statuses_count;
        echo $tweets1->id;
        echo $tweets1->screen_name; */
        
        $r = $con->query("select * from user where u_screen_id = '{$tweets1->screen_name}'");
        
/*        if($r->num_rows > 0){
            $res = $r->fetch_assoc();
            $_SESSION['user_id'] = $res[0]['u_id'];
        }
        else{
            $_SESSION['user_id']= $con->query("insert into user(u_screen_id) values('{$tweets1->screen_name}')");
        }
  */      
        $_SESSION['user_name'] = $tweets1->screen_name; 
        $_SESSION['user_statuses'] = $tweets1->statuses_count; 
    /*    echo $tweets1->name;
        $res = $mysqli->query("SELECT * from twitter_user where u_twitter_id = " + $tweets1->id);
        
        $row = $res->row_count();
        
      */  
        
//        echo "hello";
    }
 //   print_r($_SESSION);
    header("location:home");
} catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
}
