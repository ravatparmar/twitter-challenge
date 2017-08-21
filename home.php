<?php
/**
 * This is home page of user after authentication.
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
require 'twitteroauth-master/autoload.php';
require 'inc/config.php';

use Abraham\TwitterOAuth\TwitterOAuth;

if (isset($_SESSION['access_token']['oauth_token']) 
    && isset($_SESSION['access_token']['oauth_token_secret']) 
    && isset($_SESSION['access_token']) 
    && isset($_SESSION['access_token']['oauth_token'])
) {
} else {
    //    echo "hello";
    header('location:./');
}

$twitter = new TwitterOAuth(
    CONSUMER_KEY, CONSUMER_SECRET, 
    $_SESSION['access_token']['oauth_token'], 
    $_SESSION['access_token']['oauth_token_secret']
);
$tweets_heading = 'Home Tweets';
$followers_heading = 'Followers';
$error = false;
if (isset($_GET['q']) && !empty(filter_input(INPUT_GET, 'q'))) {
    $user_name = filter_input(INPUT_GET, 'q');
    $tweets = $twitter->get(
        'statuses/user_timeline', 
        ['count' => 10, 'screen_name' => $user_name]
    );
    if (!isset($tweets->errors) && isset($tweets[0]->user->name)) {
        $tweets_heading = $tweets[0]->user->name."'s tweets";
        $followers = $twitter->get(
            'followers/list', 
            ['count' => 10, 'screen_name' => $user_name]
        );
        $followers_heading = $tweets[0]->user->name."'s followers";
    } else {
        $error = true;
        $tweets_heading = "It's looks like you've entered wrong name.";
    }
} else {
    $tweets = $twitter->get('statuses/home_timeline', ['count' => 10]);
    $followers = $twitter->get('followers/list', ['count' => 10]);
}

$id = '';
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- linked css stylesheet -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
        <link rel="stylesheet" href="assets/css/owl.theme.default.css">

        <link rel="stylesheet" href="assets/css/style.css">
        <meta name="description"  content="" />
        <title> Welcome to Twitter </title>
    </head>
    <body>
        <header class="header">
            <div class="page-content" >
                <div class="pull-left heading-text" >Twitter</div>
                <div class="pull-right" >
                    <div class="menu-button" >
                        <span class="glyphicon glyphicon-menu-hamburger" ></span>
                    </div>
                    <ul class="list-inline header-menu">
                        <li><a href="home" >Home</a></li>
                        <li><a href="#" >Download</a>
                            <ul class="list-unstyled">
                                <?php
                                $user = '';
                                if (!$error && isset($user_name)) {
                                    $user = '&q='.$user_name;
                                }
                                ?>
                                <li><a href="get-tweets?type=pdf<?php echo $user; ?>" >PDF</a></li>
                                <li><a href="get-tweets?type=json<?php echo $user; ?>" >JSON</a></li>
                                <li><a href="get-tweets?type=xls<?php echo $user; ?>" >XSL</a></li>
                                <li><a href="get-tweets?type=csv<?php echo $user; ?>" >CSV</a></li>
                                <li><a href="get-tweets?type=google<?php echo $user; ?>" >Google Drive</a></li>     
                            </ul>
                            
                        </li>
                        <li><a href="logout" >Logout</a></li>
                    </ul>
                </div>
                <div class="pull-right search-box" >
                    <form method="get">
                        <div class="input-group form-inline" >
                            <input type="text" list="followers" name="q" class="form-control" placeholder="Search for...">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-secondary" type="button">Go!</button>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="clearfix" ></div>
            </div>
        </header>

        <section class="home-slider" id="home-slider" >
            <div class="page-content" >
                <h1 id="home-slider-name" ><?php echo $tweets_heading ?></h1>
                <div class="row" >
                    <div class="col-md-2" ></div>
                    <div class="col-md-8 home-slider-container" >
                        <div class="row" >
                            <div class="col-sm-1 hidden-xs" >
                                <div class="tweet-nav tweet-nav-left" >
                                    <div>
                                        <span class="glyphicon glyphicon-chevron-left"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-10" >
                                <div class="owl-carousel tweet-carousel" >								
                                    <?php
                                    if (!$error) {
                                        if (isset($tweets->errors)) {
                                            echo 'limit exceeds or entered wrong name ';
                                        } else {
                                            foreach ($tweets as $temp) {
                                                ?>
                                            <div class="slide" >
                                                <div class="row" >
                                                    <div class="col-md-12" >
                                                        <div class="tweet-user text-center" >
                                                            <?php
                                                            echo $temp->user->name.' (<span>@'.$temp->user->screen_name.'</span>)'; ?>
                                                        </div>
                                                        <div class="tweet text-center" >
                                                            <?php
                                                            echo '<p>'.$temp->text.'</p>';

                                                            if (isset($temp->entities->media)) {
                                                                foreach ($temp->entities->media as $m) {
                                                                    if ($m->type == 'photo') {
                                                                        echo '<img src="'.$m->media_url.'" />';
                                                                    }
                                                                    if ($m->type == 'video') {
                                                                        //              echo $m->url;
                                                                    }
                                                                }
                                                            } ?>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <?php

                                            }
                                        }
                                    }
                                    ?>
                                </div>


                            </div>
                            <div class="col-sm-1 hidden-xs" >
                                <div class="tweet-nav tweet-nav-right" >
                                    <div>
                                        <span class="glyphicon glyphicon-chevron-right"></span>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>

                </div>

            </div>
        </section>
        <section class="followers-container" >
            <div class="page-content" >
                <h2><?php echo $followers_heading ?></h2>

                <div class="row text-center" >
                    <div class="col-md-3" ></div>
                    <div class="col-md-6" >
                        <div class="row followers-list" >
                            <div class="col-md-6" >
                                <ul class="list-unstyled">
                                    <?php
                                    $i = 1;
                                    if (!$error) {
                                        foreach ($followers->users as $temp) {
                                            echo "<li> <a data-name='{$temp->screen_name}' class='followers-name' href='user-timeline?screen_name={$temp->screen_name}'>{$temp->name}</a></li> ";
                                            if ($i == 5) {
                                                echo '</ul>
                    </div>
                    <div class="col-md-6" >
                            <ul class="list-unstyled">';
                                            }
                                            ++$i;
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <div class="part page-content" >
                Designed & Developed by <a href="http://ravatparmar.com" >Ravat Parmar</a>
            </div>
        </footer>
        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/script.js"></script>
        <?php
        $arr[] = array();
        $followers = $twitter->get('followers/list', ['count' => 1000]);
        if (!isset($_SESSION['followers'])) {
            while ($followers->next_cursor != 0) {
                foreach ($followers->users as $temp) {
                    $arr[] = $temp->screen_name;
                }
                $followers = $twitter->get('followers/list', ['count' => 1000, 'cursor' => $followers->next_cursor]);
                foreach ($followers->users as $temp) {
                    $arr[] = $temp->screen_name;
                }
            }
            $_SESSION['followers'] = $arr;
        } else {
            $arr = $_SESSION['followers'];
        }
        echo '<datalist id="followers">';
        foreach ($arr as $a) {
            if (!is_array($a)) {
                echo '<option value="'.$a.'">';
            }
        }
        echo '</datalist>';
        ?>

    </body>
</html>