<?php
require "class.logsys.php";
\Fr\LS::config(array(
  "db" => array(
    "host" => "localhost",
    "port" => 3306,
    "username" => "root",
    "password" => "backstreetboys",
    "name" => "test",
    "table" => "users"
  ),
  "features" => array(
    "auto_init" => true
  ),
  "pages" => array(
    "no_login" => array(
      "/Francium/logSys/",
      "/Francium/logSys/reset.php",
      "/Francium/logSys/register.php"
    ),
    "login_page" => "/Francium/logSys/login.php",
    "home_page" => "/Francium/logSys/home.php"
  )
));
