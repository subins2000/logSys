<?php
require "class.logsys.php";
\Fr\LS::$config = array(
  "db" => array(
    "host" => "localhost",
    "port" => 3306,
    "username" => "root",
    "password" => "mypassword",
    "name" => "test",
    "table" => "users"
  ),
  "pages" => array(
    "no_login" => array(
      "/",
      "/reset.php",
      "/register.php"
    ),
    "login_page" => "/login.php",
    "home_page" => "/home.php"
  )
);
