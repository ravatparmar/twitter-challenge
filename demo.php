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
//require "twitteroauth-master/autoload.php";
require "inc/config.php";
ini_set('max_execution_time', 600);

/* use Abraham\TwitterOAuth\TwitterOAuth;

  $twitter = new TwitterOAuth(
  CONSUMER_KEY, CONSUMER_SECRET, "2478166266-tI8XkijrOCRq90evBavRWzkdUFVO91Amgh7BBJt", "GxtOG27zNkP31RJ0yVUirqStGtvDVdlYle3ztthRw8NJi"
  );
  $error = false;
 */
include_once 'simple_html_dom.php';

/* $html = file_get_html('http://www.google.com/');
  //$d = file_get_contents("");
  //file_ge
  $ret = $html->find('div[class=tweet]');

  print_r($d);
 */

$query = $con->query("select * from twitter_download where d_status = 0");
while ($res = $query->fetch_assoc()) {
        
    $last_date = $res['d_last_date'];
    $temp1 = strtotime($last_date);
    $t = date('Y-m-d', strtotime("-1 day", $temp1));
    $counter = 1;
    //$random = substr(md5(mt_rand()), 0, 7);
    //$_SESSION['google_file'] = 'archives/'.$random.".xls";
    //    unlink($_SESSION['google_file']);
    $fp = fopen("archives/". $res['d_file'], 'a');

    while (true) {
        $url = "https://twitter.com/search?f=tweets&vertical=default&q=from%3A{$res['d_screen_name']}%20until%3A$t&src=tyah";
        $html = new simple_html_dom();
        // Load a file
        $html->load_file($url);
        $collection = $html->find('.tweet');
        $i = 0;
        foreach ($collection as $c) {
            //    var_dump($c->find('.tweet-text'));
            //tweet-timestamp

            $data = array();
            foreach ($c->find('.tweet-timestamp') as $li) {
                $tmp = explode("- ", $li->title);

                $last_date = $tmp[1];

//                $data[] = $i;
                $data[] = $last_date;
                // echo $last_date. "$i<br />";
                // do something...
                $i++;
            }
            foreach ($c->find('.tweet-text') as $li) {
                $data[] = $li->plaintext;
//                  echo $li->plaintext. "$i<br />";
                // do something...
            }
  //          print_r($data);
            fputcsv($fp, $data);
        }
        if ($i < 16) {
            $con->query("update twitter_download set d_status=1");
            break;
        }
        if ($counter >= 50) {
            break;
        }
        $counter++;

        $temp1 = strtotime($last_date);
        $t = date('Y-m-d', strtotime("-1 day", $temp1));
//    $temp1 = strtotime("", $i)
        //  $t = date('Y-m-d', strtotime($last_date));
    }
echo    $con->query("update twitter_download set d_last_date='$last_date' where d_id = ". $res['d_id']);
    fclose($fp);
    
    
}
//print_r($collection);


exit;
/*
$tweets = $twitter->get(
        "search/tweets", ["count" => 20000,"q"=> "from:narendramodi", "until"=> "2016-01-01"]
    );

print_r($tweets);
exit;
$query = $con->query("select * from twitter_download");

$query_result = $query->fetch_assoc();

    echo "<pre>";
print_r($query_result );
if($query_result['d_first_time'] == 0){
    $tweets = $twitter->get(
        "statuses/user_timeline", ["count" => 20000, "screen_name" => 'dna', "include_rts" => false]
    );
    $con->query("update twitter_download set d_first_time=1");
}
else{
    
    $tweets = $twitter->get(
        "statuses/user_timeline", ["count" => 900, "screen_name" => 'dna', "max_id" => $query_result['d_last_id'], "include_rts" => false]
    );
}


$i = 1;
print_r($tweets);
//   header('Content-Type: application/excel');
//  header('Content-Disposition: attachment; filename="tweets.csv"');
$random = substr(md5(mt_rand()), 0, 7);
//$_SESSION['google_file'] = 'archives/' . $random . ".xls";
//    unlink($_SESSION['google_file']);
//$fp = fopen("archives/mytweets1.xls", 'a');

    $id = "";
    $t = 0;
while (true) {
    $flag = false;
    $tweet = "tw";
    foreach ($tweets as $temp) {
        if (!isset($temp->text) || !isset($tweets[1])) {
            $flag = true;
            break;
        }
        $data = "";
        $data = array($temp->id, $temp->text);
        print_r($data);
        echo $t++;
//        fputcsv($fp, $data);
        //        echo $temp->text . "<br />";
        $id = $temp->id;
        
    }
    
    if ($flag) {
        break;
    }
    $tweets = $twitter->get(
            "statuses/user_timeline", ["count" => 20000, "screen_name" => 'dna', "max_id" => $id, "include_rts" => false]
    );

    if ($i == 10) {
        break;
    }
    $i++;
}
$con->query("update twitter_download set d_last_id=". $id);
//fclose($fp);
*/