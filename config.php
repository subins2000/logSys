<?php
require "class.logsys.php";
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
      "/Francium/logSys/",
      "/Francium/logSys/reset.php",
      "/Francium/logSys/register.php",
      "/Francium/logSys/two-step-login.php"
    ),
    "login_page" => "/Francium/logSys/login.php",
    "home_page" => "/Francium/logSys/home.php"
  ),
  "two_step_login" => array(
    "instruction" => "A token was sent to your E-Mail Address. Please see the mail in your inbox and paste the token found in the textbox below :",
    "send_callback" => function($userID, $token){
      echo $token;
    }
  )
);
/**
 * Two Step Login & Normal Login won't work together,
 * so enable two step login only on it's login page
 */
if(isset($normal_login_page)){
  $config["features"]["two_step_login"] = false;
}
\Fr\LS::config($config);
