<?php
/**
 * For Development Purposes
 */
ini_set("display_errors", "on");

require __DIR__ . "/../../src/LS.php";
$config = array(
  "basic" => array(
    "company" => "My Site",
    "email" => "emails@mysite.com"
  ),
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
  /**
   * These are my localhost paths, change it to yours
   */
  "pages" => array(
    "no_login" => array(
      "/Francium/logSys/examples/two-step-login/",
      "/Francium/logSys/examples/two-step-login/reset.php",
      "/Francium/logSys/examples/two-step-login/register.php"
    ),
    "everyone" => array(
      "/Francium/logSys/examples/two-step-login/status.php"
    ),
    "login_page" => "/Francium/logSys/examples/two-step-login/login.php",
    "home_page" => "/Francium/logSys/examples/two-step-login/home.php"
  ),
  "two_step_login" => array(
    "instruction" => "A token was sent to your E-Mail Address. Please see the mail in your inbox and paste the token found in the textbox below :",
    "send_callback" => function($userID, $token){
      $email = \Fr\LS::getUser("email", $userID);
      \Fr\LS::sendMail($email, "Verify Yourself", "Someone tried to login to your account. If it was you, then use the following token to complete logging in : <blockquote>". $token ."</blockquote>If it was not you, then ignore this email and please consider to change your account's password.");
    }
  )
);
/**
 * Insert config to logSys
 */
\Fr\LS::config($config);
