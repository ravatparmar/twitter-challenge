<?php

/**
 * Landing page.
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

if (isset($_SESSION['access_token']['oauth_token']) 
    && isset($_SESSION['access_token']['oauth_token_secret']) 
    && isset($_SESSION['access_token']) 
    && isset($_SESSION['access_token']['oauth_token'])
) {
    header('location:home');
}

?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- linked css stylesheet -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="description"  content="" />
    <title> Twitter </title>
</head>
<body>
    <header class="home-hero">
        <div class="page-content" >
                <h1>Twitter</h1>
                <p> Please login with twitter to display your tweets  </p>
        </div>
    </header>
    <section class="home-body">
        <div class="page-content" >
                <a href="login.php" class="btn btn-info btn-lg" >
                    SignIn with Twitter
                </a>
        </div>
    </section>
    <footer>
        <div class="part page-content" >
                &copy; 2017 
        </div>
    </footer>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>