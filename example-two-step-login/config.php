<?php
require __DIR__ . "/../class.logsys.php";
$config = array(
  "db" => array(
    "host" => "localhost",
    "port" => 3306,
    "username" => "root",
    "password" => "backstreetboys",
    "name" => "test",
    "table" => "users"
  ),
  "features" => array(
    "auto_init" => true,
    "two_step_login" => true
  ),
  "pages" => array(
    "no_login" => array(
      "/Francium/logSys/example-two-step-login/",
      "/Francium/logSys/example-two-step-login/reset.php",
      "/Francium/logSys/example-two-step-login/register.php"
    ),
    "login_page" => "/Francium/logSys/example-two-step-login/login.php",
    "home_page" => "/Francium/logSys/example-two-step-login/home.php"
  ),
  "two_step_login" => array(
    "instruction" => "A token was sent to your E-Mail Address. Please see the mail in your inbox and paste the token found in the textbox below :",
    "send_callback" => function($userID, $token){
      echo $token;
    }
  )
);
\Fr\LS::config($config);
