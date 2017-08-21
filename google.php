<?php

/**
 * After successful google authentication, google transfer control to this page.
 *
 * PHP version 7
 *
 * @category PHP
 * @package  Twitter
 * @author   Ravat Parmar <ravatparmar@hotmail.com>
 * @version  CVS: 1.0
 * @link     http://ravatparmar.com
 */

require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->addScope(Google_Service_Drive::DRIVE);

if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {

    $client->setAccessToken($_SESSION['google_access_token']);
    $drive = new Google_Service_Drive($client);
    $file = new Google_Service_Drive_DriveFile();
    $file->setName("tweets.xls");
    $file->setMimeType("application/vnd.google-apps.spreadsheet");

    $result = $drive->files->create($file, 
        array(
            'data' => file_get_contents($_SESSION['google_file']),
            'mimeType' => "text/xls",
            'uploadType' => "media"
        )
    );
    header("location:home");
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/twitter/success';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
