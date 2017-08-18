<?php
/**
 * Downloads the tweets of user.
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

if (isset($_SESSION['access_token']['oauth_token']) 
    && isset($_SESSION['access_token']['oauth_token_secret']) 
    && isset($_SESSION['access_token']) 
    && isset($_SESSION['access_token']['oauth_token'])
) {
    
} else {
    header("location:./");
}

$twitter = new TwitterOAuth(
    CONSUMER_KEY, 
    CONSUMER_SECRET, 
    $_SESSION['access_token']['oauth_token'], 
    $_SESSION['access_token']['oauth_token_secret']
);

$tweets = $twitter->get("statuses/home_timeline", ["count" => 20000]);

$i = 1;
if ($_GET['type'] == "csv") {
    header('Content-Type: application/excel');
    header('Content-Disposition: attachment; filename="tweets.csv"');
    $fp = fopen('php://output', 'w');

    while (true) {
        $flag = false;
        $id = "";
        foreach ($tweets as $temp) {
            if (!isset($temp->text)) {
                $flag = true;
                break;
            }
            $data = "";
            $data = array($temp->text);
            fputcsv($fp, $data);
            echo $temp->text . "<br />";
            $id = $temp->id;
        }
        if ($flag) {
            break;
        }
        $tweets = $twitter->get("statuses/home_timeline", ["count" => 20000, "max_id" => $id]);
        if ($i == 10) {
            break;
        }
        $i++;
    }
    fclose($fp);
} 
else if ($_GET['type'] == 'xls') {
    header("Content-Type: application/vnd.ms-excel");
    echo 'User Name' . "\t" . 'Tweets' . "\n";
    while (true) {
        $flag = false;
        $id = "";
        foreach ($tweets as $temp) {
            if (!isset($temp->text)) {
                $flag = true;
                break;
            }
            $data = "";
            echo $temp->user->name . "\t" . $temp->text . "\n";
            $id = $temp->id;
        }
        if ($flag) {
            break;
        }
        $tweets = $twitter->get("statuses/home_timeline", ["count" => 20000, "max_id" => $id]);
        if ($i == 10) {
            break;
        }
        $i++;
    }
    header("Content-disposition: attachment; filename=tweets.xls");
} else if ($_GET['type'] == 'json') {

    $json = array();
    ;
    while (true) {
        //echo $followers->next_cursor;
        $flag = false;
        $id = "";
        foreach ($tweets as $temp) {
            if (!isset($temp->text)) {
                $flag = true;
                break;
            }
            $json[] = $temp;
            $data = "";
            $id = $temp->id;
        }
        if ($flag) {
            break;
        }
        $tweets = $twitter->get("statuses/home_timeline", ["count" => 20000, "max_id" => $id]);
        if ($i == 10) {
            break;
        }
        $i++;
    }

    header('Content-disposition: attachment; filename=file.json');
    header('Content-type: application/json');
    print_r($json);
}


