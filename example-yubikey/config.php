<?php
/**
 * For Development Purposes
 */
ini_set("display_errors", "on");


require __DIR__ . "/../class.logsys.php";
\Fr\LS::config(array(
  "db" => array(
    "host" => "localhost",
    "port" => 3306,
    "username" => "toto",
    "password" => "toto123",
    "name" => "project",
    "table" => "users"
  ),
  "yubi" => array(
    "clientid" => "28364",
    "secret" => "BvMBBDf/jZDmpMetO9JgM6BG1W0="
  ),
  "features" => array(
    "auto_init" => true,
    "remember_me" => false
  ),
  "pages" => array(
    "no_login" => array(
      "/",
      "/example-yubikey/reset.php",
      "/example-yubikey/register.php",
      "/example-yubikey/register_key.php"
    ),
    "login_page" => "/example-yubikey/login.php",
    "home_page" => "/example-yubikey/home.php"
  )
));
