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

if (isset($_SESSION['access_token']['oauth_token']) && isset($_SESSION['access_token']['oauth_token_secret']) && isset($_SESSION['access_token']) && isset($_SESSION['access_token']['oauth_token'])
) {
    
} else {
    header("location:./");
}

$twitter = new TwitterOAuth(
        CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']
);
$error = false;
if (isset($_GET['q']) && !empty(filter_input(INPUT_GET, 'q'))) {
    $user_name = filter_input(INPUT_GET, 'q');
    $tweets = $twitter->get(
            "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name]
    );
    $download_name = $user_name;
} else {
    $tweets = $twitter->get("statuses/home_timeline", ["count" => 20000]);
    $download_name = $_SESSION['user_name'];
}

$i = 1;
if ($_GET['type'] == "csv") {
    header('Content-Type: application/excel');
    header('Content-Disposition: attachment; filename="tweets.csv"');
    $fp = fopen('php://output', 'w');

    $data = array("Name", "Username", "Tweets");
    fputcsv($fp, $data);
    while (true) {
        $flag = false;
        $id = "";
        foreach ($tweets as $temp) {
            if (!isset($temp->text) || !isset($tweets[1])) {
                $flag = true;
                break;
            }
            $data = array(
                $temp->user->name,
                '@' . $temp->user->screen_name,
                $temp->text
            );
            fputcsv($fp, $data);
            $id = $temp->id;
        }
        if ($flag) {
            break;
        }
        if (isset($user_name)) {
            $tweets = $twitter->get(
                    "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name, "max_id" => $id]
            );
        } else {
            $tweets = $twitter->get(
                    "statuses/home_timeline", ["count" => 20000, "max_id" => $id]
            );
        }
        if ($i == 10) {
            break;
        }
        $i++;
    }
    fclose($fp);
} elseif ($_GET['type'] == 'xls') {
    header("Content-Type: application/vnd.ms-excel");
    echo 'User Name' . "\t" . 'Tweets' . "\n";
    while (true) {
        $flag = false;
        $id = "";
        foreach ($tweets as $temp) {
            if (!isset($temp->text) || !isset($tweets[1])) {
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
        if (isset($user_name)) {
            $tweets = $twitter->get(
                    "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name, "max_id" => $id]
            );
        } else {
            $tweets = $twitter->get(
                    "statuses/home_timeline", ["count" => 20000, "max_id" => $id]
            );
        }
        if ($i == 10) {
            break;
        }
        $i++;
    }
    header("Content-disposition: attachment; filename=tweets.xls");
} elseif ($_GET['type'] == 'json') {
    $json = array();
    while (true) {
        //echo $followers->next_cursor;
        $flag = false;
        $id = "";
        foreach ($tweets as $temp) {
            if (!isset($temp->text) || !isset($tweets[1])) {
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
        if (isset($user_name)) {
            $tweets = $twitter->get(
                    "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name, "max_id" => $id]
            );
        } else {
            $tweets = $twitter->get(
                    "statuses/home_timeline", ["count" => 20000, "max_id" => $id]
            );
        }
        if ($i == 10) {
            break;
        }
        $i++;
    }

    header('Content-disposition: attachment; filename=file.json');
    header('Content-type: application/json');
    print_r($json);
} elseif ($_GET['type'] == 'pdf') {
    include 'lib/fpdf/htmlpdf.php';
    $pdf = new PDF_HTML();
    $pdf->AddPage();
    $pdf->SetFont('Arial');
    $text = "";
    $pdf->WriteHTML('<br><p align="center">Tweets</p><br><hr>');
    $id = "";
    while (true) {
        $flag = false;
        $id = 0;
        foreach ($tweets as $temp) {
            if (isset($tweets->errors) && $id === "") {
                $pdf->WriteHTML("<p>limit exceeds. Try again after 15 minutes<br>");
            }
            if (!isset($temp->text) || !isset($tweets[1])) {
                $flag = true;
                break;
            }
            $data = "";
            $pdf->SetFontSize(12);
            $pdf->WriteHTML(
                    "<p>{$temp->user->name} (@{$temp->user->name}) <br><br>"
            );

            $pdf->SetFontSize(10);
            $pdf->WriteHTML(substr($temp->created_at, 0, 19) . " <br><br>");
            $pdf->WriteHTML("{$temp->text} <br><br> <hr><br>");
            //            echo
            //                  $temp->user->name . "\t" . $temp->text . "\n";
            $id = $temp->id;
        }
        //        $pdf->WriteHTML($text);
        //      $text="";
        if ($flag) {
            break;
        }
        if (isset($user_name)) {
            $tweets = $twitter->get(
                    "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name, "max_id" => $id]
            );
        } else {
            $tweets = $twitter->get(
                    "statuses/home_timeline", ["count" => 20000, "max_id" => $id]
            );
        }
        if ($i == 10) {
            break;
        }
        $i++;
    }
    $pdf->Output();
} else if ($_GET['type'] == "google") {
    //   header('Content-Type: application/excel');
    //  header('Content-Disposition: attachment; filename="tweets.csv"');
    $random = substr(md5(mt_rand()), 0, 7);
    $_SESSION['google_file'] = 'archives/' . $random . ".xls";
    //    unlink($_SESSION['google_file']);
    $fp = fopen($_SESSION['google_file'], 'w');

    while (true) {
        $flag = false;
        $id = "";
        $tweet = "tw";
        foreach ($tweets as $temp) {
            if (!isset($temp->text) || !isset($tweets[1])) {
                $flag = true;
                break;
            }
            $data = "";
            $data = array($temp->text);
            fputcsv($fp, $data);
            //        echo $temp->text . "<br />";
            $id = $temp->id;
        }
        if ($flag) {
            break;
        }


        if (isset($user_name)) {
            $tweets = $twitter->get(
                    "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name, "max_id" => $id]
            );
        } else {
            $tweets = $twitter->get(
                    "statuses/home_timeline", ["count" => 20000, "max_id" => $id]
            );
        }
        if ($i == 10) {
            break;
        }
        $i++;
    }
    fclose($fp);

    header("location:success");
} elseif ($_GET['type'] == 'print') {
    echo "<pre>";
    while (true) {
        //echo $followers->next_cursor;
        $flag = false;
        $id = "";
        print_r($tweets);
        /*   foreach ($tweets as $temp) {
          if (!isset($temp->text) || !isset($tweets[1])) {
          $flag = true;
          break;
          }
          $json[] = $temp;
          $data = "";
          $id = $temp->id;
          print_r($temp);
          }
         */
        if ($flag) {
            break;
        }


        if (isset($user_name)) {
            $tweets = $twitter->get(
                    "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name, "max_id" => $id]
            );
        } else {
            $tweets = $twitter->get(
                    "statuses/home_timeline", ["count" => 20000, "max_id" => $id]
            );
        }
        echo $i;
        if ($i == 10) {
            break;
        }
        $i++;
    }

    //  header('Content-disposition: attachment; filename=file.json');
    //  header('Content-type: application/json');
    // print_r($json);
} else if ($_GET['type'] == "xls_all" || $_GET['type'] == "csv_all" ) {
    //   header('Content-Type: application/excel');
    //  header('Content-Disposition: attachment; filename="tweets.csv"');

    $type= "xls";
    if($_GET['type'] == "csv_all"){
        $type = "csv";
    }

    $res = $con->query("select * from twitter_download where d_screen_name = '{$download_name}'");
    if ($res->num_rows > 0) {
        while ($r = $res->fetch_assoc()) {
            if($r['d_status']){
                $dt = date('d M Y');
                 $con->query("update twitter_download set d_middle_status=0,d_start_date='$dt', d_middle_date='{$r['d_start_date']}', d_middle_last_date = '{$r['d_start_date']}'  where d_screen_name = '{$download_name}' ");
            }
            $res_user = $con->query("select * from user where u_screen_id = '{$_SESSION['user_name']}' and u_download_id = '{$r['d_id']}' and u_type='$type' ");
            if ($res_user->num_rows < 1) {
                $con->query("insert into user(u_screen_id, u_download_id, u_type) values('{$_SESSION['user_name']}', '{$r['d_id']}', '$type')");        
            }
        }
    } else {
        $user_info = $twitter->get("users/show", ["screen_name" => $download_name]);
        
        $statuses = $user_info->statuses_count;
        
        $min = round((10* $statuses/900));
                
        $time = strtotime("+$min minute");
        
        $random = substr(md5(mt_rand()), 0, 7);
        $random .= ".xls";
        $dt = date('d M Y');
        $con->query("insert into twitter_download(d_screen_name, d_start_date, d_last_date, d_file, d_type, d_status, d_time, d_middle_status) values('{$download_name}', '$dt','$dt', '$random', 'xls', 0, '$time',1)");
        if($_GET['type'] == "csv_all")
            $con->query("insert into user(u_screen_id, u_download_id, u_type) values('{$_SESSION['user_name']}', '{$con->insert_id}', 'csv')");
        else
            $con->query("insert into user(u_screen_id, u_download_id, u_type) values('{$_SESSION['user_name']}', '{$con->insert_id}', 'xls')");
    }
/*
    //    unlink($_SESSION['google_file']);
    $fp = fopen($_SESSION['google_file'], 'w');

    while (true) {
        $flag = false;
        $id = "";
        $tweet = "tw";
        foreach ($tweets as $temp) {
            if (!isset($temp->text) || !isset($tweets[1])) {
                $flag = true;
                break;
            }
            $data = "";
            $data = array($temp->text);
            fputcsv($fp, $data);
            //        echo $temp->text . "<br />";
            $id = $temp->id;
        }
        if ($flag) {
            break;
        }


        if (isset($user_name)) {
            $tweets = $twitter->get(
                    "statuses/user_timeline", ["count" => 20000, "screen_name" => $user_name, "max_id" => $id]
            );
        } else {
            $tweets = $twitter->get(
                    "statuses/home_timeline", ["count" => 20000, "max_id" => $id]
            );
        }
        if ($i == 10) {
            break;
        }
        $i++;
    }
    fclose($fp);
    */
    if($download_name == $_SESSION['user_name']){
        header("location:home");
    }
    else{
        header("location:home?q=". $download_name);
    }
}

if(isset($_GET['d']) && !empty($_GET['d'])){
    
    $temp = explode(".", $_GET['d']);
    
    copy("archives/".$_GET['d'], "archives/".$temp[0].".csv");
    header("location:archives/{$temp[0]}.csv");
    
}