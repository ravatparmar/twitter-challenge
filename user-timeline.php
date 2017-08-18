<?php

/**
 * Get the time of current logged in user.
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
    && isset($_GET['screen_name'])
) {
    
} else {
    //	header("location:./");
}
$screen_name = $_GET['screen_name'];

$twitter = new TwitterOAuth(
        CONSUMER_KEY, 
        CONSUMER_SECRET, 
        $_SESSION['access_token']['oauth_token'], 
        $_SESSION['access_token']['oauth_token_secret']
);
$id = "";
$tweets = $twitter->get("statuses/user_timeline", ["count" => 10, "screen_name" => $screen_name]);
?>								
<?php
foreach ($tweets as $temp) {
    ?>
    <div class="slide" >
        <div class="row" >
            <div class="col-md-12" >
                <div class="tweet-user text-center" >
                    <?php
                    echo $temp->user->name . " (<span>@" . $temp->user->screen_name . "</span>)";
                    ?>
                </div>
                <div class="tweet text-center" >
                    <?php
                    echo "<p>" . $temp->text . "</p>";

                    if (isset($temp->entities->media)) {


                        foreach ($temp->entities->media as $m) {
                            if ($m->type == "photo") {
                                echo '<img src="' . $m->media_url . '" />';
                            }
                            if ($m->type == "video") {
                                //              echo $m->url;
                            }
                        }
                    }
                    ?>
                </div>
            </div>

        </div>

    </div>

    <?php
}
?>