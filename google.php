<?php

require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->addScope(Google_Service_Drive::DRIVE);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

//    var_dump($_SESSION['access_token']);
    $client->setAccessToken($_SESSION['access_token']);
    $drive = new Google_Service_Drive($client);
    $file = new Google_Service_Drive_DriveFile();
    $file->setName("data.xls");
    $file->setMimeType("application/vnd.google-apps.spreadsheet");

    $result = $drive->files->create($file, array(
        'data' => file_get_contents("get-tweets-1.xls"),
        'mimeType' => "text/xls",
        'uploadType' => "media",
    ));
    var_dump($result);
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/twitter/success';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
