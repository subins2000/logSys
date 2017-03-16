<?php
/**
 * For Development Purposes
 */
ini_set('display_errors', 'on');

require __DIR__ . '/../../src/autoload.php';

$config = array(
    'basic'          => array(
        'company' => 'My Site',
        'email'   => 'emails@mysite.com',
    ),
    'db'             => array(
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => '',
        'name'     => 'test',
        'table'    => 'users',
    ),
    'features'       => array(
        'auto_init'      => true,
        'two_step_login' => true,
    ),
    /**
     * These are my localhost paths, change it to yours
     */
    'pages'          => array(
        'no_login'   => array(
            '/examples/two-step-login/',
            '/examples/two-step-login/reset.php',
            '/examples/two-step-login/register.php',
        ),
        'everyone'   => array(
            '/examples/two-step-login/status.php',
        ),
        'login_page' => '/examples/two-step-login/login.php',
        'home_page'  => '/examples/two-step-login/home.php',
    ),
    'two_step_login' => array(
        'instruction'   => 'A token was sent to your E-Mail Address. Please see the mail in your inbox and paste the token found in the textbox below :',
        'send_callback' => function (&$LS, $userID, $token) {
            $email = $LS->getUser('email', $userID);
            $LS->sendMail($email, "Verify Yourself", "Someone tried to login to your account. If it was you, then use the following token to complete logging in : <blockquote>". $token ."</blockquote>If it was not you, then ignore this email and please consider to change your account's password.");
        },
    ),
);

$LS = new \Fr\LS($config);
