<?php
/**
 * For Development Purposes
 */
ini_set('display_errors', 'on');

require __DIR__ . '/../../src/autoload.php';

$LS = new \Fr\LS(array(
    'db'       => array(
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => '',
        'name'     => 'test',
        'table'    => 'users',
    ),
    'features' => array(
        'auto_init' => true,
    ),
    'pages'    => array(
        'no_login'   => array(
            '/',
            '/examples/basic/reset.php',
            '/examples/basic/register.php',
        ),
        'everyone'   => array(
            '/examples/basic/status.php',
        ),
        'login_page' => '/examples/basic/login.php',
        'home_page'  => '/examples/basic/home.php',
    ),
));
