<?php
/**
 * For Development Purposes
 */
ini_set('display_errors', 'on');

require __DIR__ . '/../../src/autoload.php';

date_default_timezone_set('UTC');

$config = array(
    'basic' => array(
        'company' => 'My Site',
        'email'   => 'emails@mysite.com',
    ),

    'db' => array(
        'host'     => 'localhost',
        'port'     => 3306,
        'username' => 'root',
        'password' => '',
        'name'     => 'test',
        'table'    => 'users',
    ),

    'features' => array(
        'auto_init'      => true,
        'two_step_login' => true,
    ),

    /**
     * These are my localhost paths, change it to yours
     */
    'pages' => array(
        'no_login'   => array(
            '/examples/material-design/',
            '/examples/material-design/reset.php',
            '/examples/material-design/register.php',
            '/examples/material-design/profile.php',
        ),
        'everyone'   => array(
            '/examples/material-design/',
            '/examples/material-design/index.php',
            '/examples/material-design/profile.php',
        ),
        'login_page' => '/examples/material-design/login.php',
        'home_page'  => '/examples/material-design/home.php',
    ),

    'two_step_login' => array(
        'instruction'   => 'A token was sent to your E-Mail Address. Please see the mail in your inbox and paste the token found in the textbox below :',
        'send_callback' => function (&$LS, $userID, $token) {
            $email = $LS->getUser('email', $userID);
            $LS->sendMail($email, 'Verify Yourself', 'Someone tried to login to your account. If it was you, then use the following token to complete logging in : <blockquote>' . $token . "</blockquote>If it was not you, then ignore this email and please consider to change your account's password.");
        },
    ),
);

$LS = new \Fr\LS($config);

function printHead($title = 'My Site')
{
    ?>
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>

    <script src="js/jquery.router.js"></script>
    <script src="js/app.js"></script>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title><?php echo $title; ?></title>

    <meta name="page-path" content="<?php echo basename($_SERVER['REQUEST_URI']); ?>">

    <style>
        .container{
            padding-bottom: 40px;
        }
    </style>
<?php
}

function showHeader()
{
    global $LS;
    require_once 'partial/header.php';
}
