<?php
/**
.---------------------------------------------------------------------------.
| The Francium Project                                                      |
| ------------------------------------------------------------------------- |
| This software logSys is a part of the Francium ( Fr ) project.            |
| http://subinsb.com/the-francium-project                                   |
| ------------------------------------------------------------------------- |
| Copyright (c) 2014 - 2017, Subin Siby.                                    |
| ------------------------------------------------------------------------- |
|   License: Distributed under the Apache License, Version 2.0              |
|            http://www.apache.org/licenses/LICENSE-2.0                     |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
 */

/**
.---------------------------------------------------------------------------.
|  Software:      PHP Login System - PHP logSys                             |
|  Version:       0.0                                                       |
|  Documentation: http://subinsb.com/php-logsys                             |
|  Contribute:    https://github.com/subins2000/logSys                      |
'---------------------------------------------------------------------------'
 */

namespace Fr;

use Fr\LS\TwoStepLogin;
use PDO;
use PDOException;

class LS
{

    /**
     * @var array Default configuration
     */
    public static $default_config = array(
        /**
         * Basic Config of logSys
         */
        'basic' => array(
            'company'        => 'My Site',
            'email'          => 'email@mysite.com',
            'email_callback' => false,

            /**
             * Callback to override output content
             */
            'output_callback' => false,
        ),

        /**
         * Database Configuration
         */
        'db' => array(
            /**
             * @var string 'mysql' or 'postgresql' or 'sqlite'
             */
            'type' => 'mysql',

            /**
             * MySQL/PostgreSQL options
             */
            'host'     => '',
            'port'     => '3306',
            'username' => '',
            'password' => '',

            /**
             * SQLite options
             */
            'sqlite_path' => '',

            'name'        => '',
            'table'       => 'users',
            'token_table' => 'user_tokens',

            'columns' => array(
                'id'       => 'id',
                'username' => 'username',
                'password' => 'password',
                'email'    => 'email',
                'attempt'  => 'attempt',
                'created'  => 'created',
            ),
        ),

        /**
         * Keys used for encryption
         * DONT MAKE THIS PUBLIC
         */
        'keys' => array(
            /**
             * Changing cookie key will expire all current active login sessions
             */
            'cookie' => 'ckxc436jd*^30f840v*9!@#$',
            /**
             * `salt` should not be changed after users are created
             */
            'salt'   => '^#$4%9f+1^p9)M@4M)V$',
        ),

        /**
         * Enable/Disable certain features
         */
        'features' => array(
            /**
             * Should I Call session_start();
             */
            'start_session' => true,
            /**
             * Enable/Disable Login using Username & E-Mail
             */
            'email_login'   => true,
            /**
             * Enable/Disable `Remember Me` feature
             */
            'remember_me'   => true,
            /**
             * Should \Fr\LS::init() be called automatically
             */
            'auto_init'     => false,

            /**
             * Prevent Brute Forcing
             * ---------------------
             * By enabling this, logSys will deny login for the time mentioned
             * in the 'brute_force'->'time_limit' seconds after 'brute_force'->'tries'
             * number of incorrect login tries.
             */
            'block_brute_force' => true,

            /**
             * Two Step Login
             * --------------
             * By enabling this, a checking is done when user visits
             * whether the device he/she uses is approved by the user.
             * Allows the original user to revoke logins in other devices/places
             * Useful if the user forgot to logout in some place.
             */
            'two_step_login' => false,
        ),

        /**
         * `Blocking Brute Force Attacks` options
         */
        'brute_force' => array(
            /**
             * No of tries alloted to each user
             */
            'tries'      => 5,
            /**
             * The time IN SECONDS for which block from login action should be done after
             * incorrect login attempts. Use http://www.easysurf.cc/utime.htm#m60s
             * for converting minutes to seconds. Default : 5 minutes
             */
            'time_limit' => 300,

            /**
             * Maximum bumber of tokens that can be generated
             */
            'max_tokens' => 5,
        ),

        /**
         * Information about pages
         */
        'pages' => array(
            /**
             * Pages that doesn't require logging in.
             * Exclude login page, but include REGISTER page.
             * Use RELATIVE links. To find the relative link of
             * a page, do var_dump( Fr\LS::curPage() );
             */
            'no_login' => array(),

            /**
             * Pages that both logged in and not logged in users can access
             */
            'everyone' => array(),

            /**
             * The login page. ex : /login.php or /accounts/login.php
             */
            'login_page' => '',

            /**
             * The home page. The main page for logged in users.
             * logSys redirects to here after user logs in
             */
            'home_page' => '',
        ),

        /**
         * Settings about cookie creation
         */
        'cookies' => array(
            /**
             * Default : cookies expire in 30 days. The value is
             * for setting in strtotime() function
             * http://php.net/manual/en/function.strtotime.php
             */
            'expire' => '+30 days',
            'path'   => '/',
            'domain' => '',

            /**
             * Names of cookies created
             */
            'names' => array(
                'login_token' => 'lg',
                'remember_me' => 'rm',
                'device'      => 'dv',

                /**
                 * This is used as $_SESSION key
                 */
                'current_user'    => 'cu',
                'device_verified' => 'dvv',
            ),
        ),

        /**
         * 2 Step Login
         */
        'two_step_login' => array(
            /**
             * Message to show before displaying 'Enter Token' form.
             */
            'instruction' => '',

            /**
             * Callback when token is generated.
             * Used to send message to user ( Phone/E-Mail )
             */
            'send_callback' => '',

            /**
             * The table to stoe user's sessions
             */
            'devices_table' => 'user_devices',

            /**
             * The length of token generated.
             * A low value is better for tokens sent via Mobile SMS
             */
            'token_length' => 4,

            /**
             * Maximum number of tries the user can make for entering token
             */
            'token_tries' => 3,

            /**
             * Whether the token should be numeric only ?
             * Default Token : Alphabetic + Numeric mixed strings
             */
            'numeric' => false,

            /**
             * The expire time of cookie that authorizes the device
             * to login using the user's account with 2 Step Verification
             * The value is for setting in strtotime() function
             * http://php.net/manual/en/function.strtotime.php
             */
            'expire' => '+45 days',

            /**
             * Should logSys checks if device is valid, everytime
             * logSys is initiated ie everytime a page loads
             * If you want to check only the first time a user loads
             * a page, then set the value to TRUE, else FALSE
             */
            'first_check_only' => true,
        ),

        /**
         * Debug info
         */
        'debug' => array(
            /**
             * Enable debugging
             */
            'enable' => false,

            /**
             * Absolute path
             */
            'log_file' => '',
        ),
    );

    /**
     * @var array
     */
    protected $config = array();

    /**
     * Config
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public function config($config = array())
    {
        /**
         * Callback to display messages for different states
         * @var array
         */
        self::$default_config['basic']['output_callback'] = function (&$LS, $state, $extraInfo = array()) {
            if ($state === 'invalidToken') {
                return '<h3>Error : Wrong/Invalid Token</h3>';
            } elseif ($state === 'fieldsLeftBlank') {
                return '<h3>Error : Fields Left Blank</h3>';
            } elseif ($state === 'passwordDontMatch') {
                return '<h3>Error : Passwords Don\'t Match</h3>';
            } elseif ($state === 'passwordChanged') {
                return '<h3>Success : Password Reset Successful</h3><p>You may now login with your new password.</p>';
            } elseif ($state === 'identityNotProvided') {
                return '<h3>Error : ' . $extraInfo['identity_type'] . ' not provided</h3>';
            } elseif ($state === 'userNotFound') {
                return '<h3>Error : User Not Found</h3>';
            } elseif ($state === 'notLoggedIn') {
                return '<h3>Error : Not Logged In</h3>';
            } elseif ($state === 'resetPasswordRequestForm') {
                $curPageURL = self::curPageURL();
                return <<<HTML
<form action='{$curPageURL}' method='POST'>
    <label>
        <p>{$extraInfo['identity_type']}</p>
        <input type='text' id='logSysIdentification' placeholder='Enter your {$extraInfo['identity_type']}' size='25' name='identification' />
    </label>
    <p><button name='logSysForgotPass' type='submit'>Reset Password</button></p>
</form>
HTML;
            } elseif ($state === 'resetPasswordForm') {
                $curPageURL = self::curPageURL();
                return <<<HTML
<p>The Token key was Authorized. Now, you can change the password</p>
<form action='{$curPageURL}' method='POST'>
    <input type='hidden' name='token' value='{$extraInfo['resetPassToken']}' />
    <label>
        <p>New Password</p>
        <input type='password' name='logSysForgotPassNewPassword' />
    </label><br/>
    <label>
        <p>Retype Password</p>
        <input type='password' name='logSysForgotPassRetypedPassword'/>
    </label><br/>
    <p><button name='logSysForgotPassChange'>Reset Password</button></p>
</form>
HTML;
            }
        };

        $this->config = array_replace_recursive(self::$default_config, $config);

        /**
         * Add the login page to the array of pages that doesn't need logging in
         */
        array_push($this->config['pages']['no_login'], $this->config['pages']['login_page']);

        if ($this->config['debug']['enable']) {
            ini_set('display_errors', 'on');
        }
    }

    /**
     * Add messages to log file
     *
     * @param  string  $msg Message
     * @return boolean      Whether message was written
     */
    public function log($msg = '')
    {
        if ($this->config['debug']['enable']) {
            $log_file = $this->config['debug']['log_file'];

            if ($log_file === '') {
                $log_file = __DIR__ . '/Francium.log';
            }

            if ($msg !== '') {
                $message = '[' . date('Y-m-d H:i:s') . '] ' . $msg;
                $fh      = fopen($log_file, 'a');
                fwrite($fh, $message . '\n');
                fclose($fh);

                return true;
            }
        }

        return false;
    }

    /**
     * @var boolean Is user logged in
     */
    public $loggedIn = false;

    /**
     * @var int|boolean User ID
     */
    public $userID = false;

    /**
     * @var PDO Database handler
     */
    protected $dbh;

    /**
     * @var boolean Whether Fr\LS::init() was called
     */
    protected $initCalled = false;

    /**
     * Intialize
     * @param array $config Configuration
     */
    public function __construct($config = array())
    {
        $this->config($config);

        if ($this->config['features']['start_session'] === true) {
            session_start();
        }

        /**
         * Try connecting to Database Server
         */
        try {
            if ($this->config['db']['type'] === 'sqlite') {
                $this->dbh = new PDO(
                    'sqlite:' . $this->config['db']['sqlite_path'],
                    $this->config['db']['username'],
                    $this->config['db']['password'],
                    array(
                        PDO::ATTR_PERSISTENT        => true,
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_STRINGIFY_FETCHES => true,
                    )
                );

                /**
                 * Enable Multithreading Read/Write
                 */
                $this->dbh->exec('PRAGMA journal_mode=WAL;');
            } elseif ($this->config['db']['type'] === 'postgresql') {
                $this->dbh = new PDO(
                    'pgsql:dbname=' . $this->config['db']['name']
                    . ';host=' . $this->config['db']['host']
                    . ';port=' . $this->config['db']['port'] . ';',
                    $this->config['db']['username'],
                    $this->config['db']['password'],
                    array(
                        PDO::ATTR_PERSISTENT        => true,
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_STRINGIFY_FETCHES => true,
                    )
                );
            } else {
                $this->dbh = new PDO(
                    'mysql:dbname=' . $this->config['db']['name']
                    . ';host=' . $this->config['db']['host']
                    . ';port=' . $this->config['db']['port']
                    . ';charset=utf8',
                    $this->config['db']['username'],
                    $this->config['db']['password'],
                    array(
                        PDO::ATTR_PERSISTENT        => true,
                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_STRINGIFY_FETCHES => true,
                    ));
            }

            $cookieToken = isset($_COOKIE[$this->config['cookies']['names']['login_token']]) ? $_COOKIE[$this->config['cookies']['names']['login_token']] : false;

            /**
             * @var int|boolean User ID is stored in session
             */
            $sessionUID = isset($_SESSION[$this->config['cookies']['names']['current_user']]) ? $_SESSION[$this->config['cookies']['names']['current_user']] : false;

            $rememberMe = isset($_COOKIE[$this->config['cookies']['names']['remember_me']]) ? $_COOKIE[$this->config['cookies']['names']['remember_me']] : false;

            if ($rememberMe) {
                /**
                 * Remember Me cookie is present. Decrypt its value
                 * to get the user ID who needs to be remembered
                 */
                $rememberMeParts = explode('::', $rememberMe);

                if(count($rememberMeParts) !== 2){
                    $this->logout();
                }

                list($rememberMeUser, $iv) = $rememberMeParts;
                $iv = base64_decode($iv);

                $rememberMe = openssl_decrypt($rememberMeUser, 'AES-128-CBC', $this->config['keys']['cookie'], 0, $iv);
            }

            if ($cookieToken) {
                $loginToken = hash('sha256', $this->config['keys']['cookie'] . $sessionUID . $this->config['keys']['cookie']);

                if ($cookieToken === $loginToken) {
                    $this->loggedIn = true;
                } else if ($rememberMe && $this->config['features']['remember_me'] === true) {
                    /**
                     * If there is a Remember Me Cookie and the user is not logged in,
                     * then log in the user with the ID in the remember cookie if it
                     * matches with the decrypted value in `logSyslogin` cookie
                     */
                    $loginTokenFromRememberMeCookie = hash('sha256', $this->config['keys']['cookie'] . $rememberMe . $this->config['keys']['cookie']);

                    if ($cookieToken === $loginTokenFromRememberMeCookie) {
                        $this->loggedIn = true;

                        $_SESSION[$this->config['cookies']['names']['current_user']] = $rememberMe;
                        $sessionUID                                                  = $rememberMe;
                    }
                }
            }

            $this->userID = $sessionUID;

            /**
             * Check if devices is authorized to use the account
             */

            if ($this->config['features']['two_step_login'] === true && $this->loggedIn) {
                $login_page = self::curPage() === $this->config['pages']['login_page'];

                if (!isset($_SESSION[$this->config['cookies']['names']['device_verified']]) && !isset($_COOKIE[$this->config['cookies']['names']['device']]) && $login_page === false) {
                    /**
                     * The device cookie is not even set. So, logout
                     */
                    $this->logout();
                } elseif ($this->config['two_step_login']['first_check_only'] === false ||
                    (
                        $this->config['two_step_login']['first_check_only'] === true
                        && !isset($_SESSION[$this->config['cookies']['names']['device_verified']])
                    )
                ) {
                    $sql = $this->dbh->prepare('SELECT "1" FROM ' . $this->config['two_step_login']['devices_table'] . ' WHERE uid = ? AND token = ?');
                    $sql->execute(array($this->userID, $_COOKIE[$this->config['cookies']['names']['device']]));

                    /**
                     * Device not authorized, so remove device cookie & logout
                     */

                    if ($sql->fetchColumn() !== '1' && $login_page === false) {
                        setcookie($this->config['cookies']['names']['device'], '', time() - 10);
                        $this->logout();
                    } else {
                        /**
                         * This session has been checked and verified
                         */
                        $_SESSION[$this->config['cookies']['names']['device_verified']] = 1;
                    }
                }
            }

            if ($this->config['features']['auto_init'] === true) {
                $this->init();
            }

            return true;
        } catch (PDOException $e) {
            /**
             * Couldn't connect to Database
             */
            self::log('Could not connect to database. Check config->db credentials. PDO Output: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * A function that will automatically redirect user according to his/her login status
     * @return void
     */
    public function init()
    {
        if (in_array(self::curPage(), $this->config['pages']['everyone'])) {
            /**
             * No redirects as this page can be accessed
             * by anyone whether he/she is logged in or not
             */
        } elseif ($this->loggedIn === true && in_array(self::curPage(), $this->config['pages']['no_login'])) {
            self::redirect($this->config['pages']['home_page']);
        } elseif ($this->loggedIn === false && array_search(self::curPage(), $this->config['pages']['no_login']) === false) {
            self::redirect($this->config['pages']['login_page']);
        }

        $this->initCalled = true;
    }

    /**
     * Login the user
     * @param  string               $username    Username or email
     * @param  boolean|string       $password    Password as string or
     *                                           FALSE for skipping password check
     * @param  boolean              $remember_me Remember me
     * @param  boolean              $cookies     Should cookies be created and redirected
     * @return array|boolean|string              Array if user is blocked
     *                                           TRUE if login was success
     *                                           User ID if login was success and $cookies FALSE
     */
    public function login($username, $password, $remember_me = false, $cookies = true)
    {
        /**
         * We Add LIMIT to 1 in SQL query because to
         * get an array with key as the column name.
         */

        if ($this->config['features']['email_login'] === true) {
            $query = 'SELECT ' . $this->config['db']['columns']['id'] . ', ' . $this->config['db']['columns']['password'] . ', ' . $this->config['db']['columns']['attempt'] . ' FROM ' . $this->config['db']['table'] . ' WHERE ' . $this->config['db']['columns']['username'] . '=:login OR ' . $this->config['db']['columns']['email'] . '=:login ORDER BY ' . $this->config['db']['columns']['id'] . ' LIMIT 1';
        } else {
            $query = 'SELECT ' . $this->config['db']['columns']['id'] . ', ' . $this->config['db']['columns']['password'] . ', ' . $this->config['db']['columns']['attempt'] . ' FROM ' . $this->config['db']['table'] . ' WHERE ' . $this->config['db']['columns']['username'] . '=:login ORDER BY ' . $this->config['db']['columns']['id'] . ' LIMIT 1';
        }

        $sql = $this->dbh->prepare($query);
        $sql->bindValue(':login', $username);

        $sql->execute();
        $cols = $sql->fetch(PDO::FETCH_ASSOC);

        if (empty($cols)) {
            // No such user like that
            return false;
        } else {
            /**
             * Get the user details
             */
            $userID        = $cols[$this->config['db']['columns']['id']];
            $user_password = $cols[$this->config['db']['columns']['password']];
            $status        = (int) $cols[$this->config['db']['columns']['attempt']];

            if (substr($status, 0, 2) === 'b-') {
                $blockedTime = substr($status, 2);

                if (time() < $blockedTime) {
                    $blocked = true;

                    return array(
                        'status'  => 'blocked',
                        'minutes' => round(abs($blockedTime - time()) / 60, 0),
                        'seconds' => round(abs($blockedTime - time()) / 60 * 60, 2),
                    );
                } else {
                    // remove the block, because the time limit is over
                    $this->updateUser(array(
                        $this->config['db']['columns']['attempt'] => '', // No tries at all
                    ), $userID);
                }
            }

            if (!isset($blocked) && ($password === false || password_verify($password . $this->config['keys']['salt'], $user_password))) {
                if ($cookies === true) {
                    $_SESSION[$this->config['cookies']['names']['current_user']] = $userID;

                    setcookie(
                        $this->config['cookies']['names']['login_token'],
                        hash('sha256', $this->config['keys']['cookie'] . $userID . $this->config['keys']['cookie']),
                        strtotime($this->config['cookies']['expire']),
                        $this->config['cookies']['path'], $this->config['cookies']['domain']
                    );

                    if ($remember_me === true && $this->config['features']['remember_me'] === true) {
                        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CBC'));
                        $rememberMeCookie = openssl_encrypt($userID, 'AES-128-CBC', $this->config['keys']['cookie'], 0, $iv) . '::' . base64_encode($iv);

                        setcookie(
                            $this->config['cookies']['names']['remember_me'],
                            $rememberMeCookie,
                            strtotime($this->config['cookies']['expire']),
                            $this->config['cookies']['path'],
                            $this->config['cookies']['domain']
                        );
                    }

                    $this->loggedIn = true;

                    if ($this->config['features']['block_brute_force'] === true) {
                        /**
                         * If Brute Force Protection is Enabled,
                         * Reset the attempt status
                         */
                        $this->updateUser(array(
                            $this->config['db']['columns']['attempt'] => '0',
                        ), $userID);
                    }

                    if ($this->initCalled) {
                        self::redirect($this->config['pages']['home_page']);
                    }

                    return true;
                } else {
                    /**
                     * If cookies shouldn't be set,
                     * it means login() was called
                     * to get the user's ID. So, return it
                     */
                    return $userID;
                }
            } else {
                /**
                 * Incorrect password
                 * ------------------
                 * Check if brute force protection is enabled
                 */
                if ($this->config['features']['block_brute_force'] === true) {
                    $max_tries = $this->config['brute_force']['tries'];

                    if ($status === '') {
                        // User was not logged in before
                        $this->updateUser(array(
                            $this->config['db']['columns']['attempt'] => '1', // Tried 1 time
                        ), $userID);
                    } elseif ($status === $max_tries) {
                        /**
                         * Account Blocked. User will be only able to
                         * re-login at the time in UNIX timestamp
                         */
                        $eligible_for_next_login_time = strtotime('+' . $this->config['brute_force']['time_limit'] . ' seconds', time());
                        $this->updateUser(array(
                            $this->config['db']['columns']['attempt'] => 'b-' . $eligible_for_next_login_time,
                        ), $userID);

                        return array(
                            'status'  => 'blocked',
                            'minutes' => round(abs($eligible_for_next_login_time - time()) / 60, 0),
                            'seconds' => round(abs($eligible_for_next_login_time - time()) / 60 * 60, 2),
                        );
                    } elseif ($status < $max_tries) {
                        // If the attempts are less than Max and not Max
                        $this->updateUser(array(
                            $this->config['db']['columns']['attempt'] => $status + 1, // Increase the no of tries by +1.
                        ), $userID);
                    }
                }

                return false;
            }
        }
    }

    /**
     * Register a user
     * @param  string         $identification Username or email
     * @param  string         $password       Password
     * @param  array          $other          Values for other columns
     * @return boolean|string                 Whether user exists ( exists ) or account was created
     */
    public function register($identification, $password, $other = array())
    {
        if ($this->userExists($identification) || (isset($other['email']) && $this->userExists($other['email']))) {
            return 'exists';
        } else {
            $hashedPass = password_hash($password . $this->config['keys']['salt'], PASSWORD_DEFAULT);

            if (count($other) === 0) {
                /* If there is no other fields mentioned, make the default query */
                $sql = $this->dbh->prepare('INSERT INTO ' . $this->config['db']['table'] . ' ( ' . $this->config['db']['columns']['username'] . ', ' . $this->config['db']['columns']['password'] . ' ) VALUES(:username, :password )');
            } else {
                /* if there are other fields to add value to, make the query and bind values according to it */
                $keys    = array_keys($other);
                $columns = implode(',', $keys);
                $colVals = implode(',:', $keys);
                $sql     = $this->dbh->prepare(
                    'INSERT INTO ' . $this->config['db']['table'] . ' ( ' .
                    $this->config['db']['columns']['username'] . ', ' . $this->config['db']['columns']['password'] . ", $columns " .
                    ') VALUES( ' .
                    ":username, :password, :$colVals" .
                    ')'
                );

                foreach ($other as $key => $value) {
                    $value = $value;
                    $sql->bindValue(":$key", $value);
                }
            }

            /* Bind the default values */
            $sql->bindValue(':username', $identification);
            $sql->bindValue(':password', $hashedPass);
            $sql->execute();

            return true;
        }
    }

    /**
     * Logout the user by destroying the cookies and session
     */
    public function logout()
    {
        session_destroy();
        setcookie($this->config['cookies']['names']['login_token'], '', time() - 10, $this->config['cookies']['path'], $this->config['cookies']['domain']);
        setcookie($this->config['cookies']['names']['remember_me'], '', time() - 10, $this->config['cookies']['path'], $this->config['cookies']['domain']);

        /**
         * Wait for the cookies to be removed, then redirect
         */
        usleep(2000);
        self::redirect($this->config['pages']['login_page']);

        return true;
    }

    /**
     * A function to handle the Forgot Password process
     * @return string The current state of the process
     */
    public function forgotPassword()
    {
        $curStatus = 'initial'; // The Current Status of Forgot Password process
        $identName = $this->config['features']['email_login'] === false ? 'Username' : 'Username / E-Mail';

        if (!isset($_POST['logSysForgotPass']) && !isset($_GET['resetPassToken']) && !isset($_POST['logSysForgotPassChange'])) {
            echo $this->getOutput(
                'resetPasswordRequestForm',
                array(
                    'identity_type' => $identName,
                )
            );

            /**
             * The user had moved to the reset password form ie she/he is currently seeing the forgot password form
             */
            $curStatus = 'resetPasswordRequestForm';
        } elseif (isset($_GET['resetPassToken']) && !isset($_POST['logSysForgotPassChange'])) {
            /**
             * The user gave the password reset token. Check if the token is valid.
             */
            $reset_pass_token = urldecode($_GET['resetPassToken']);

            if (!$this->verifyResetPasswordToken($reset_pass_token)) {
                $curStatus = 'invalidToken'; // The token user gave was not valid
                echo $this->getOutput($curStatus);
            } else {
                /**
                 * The token is valid, display the new password form
                 */
                echo $this->getOutput('resetPasswordForm', array(
                    'resetPassToken' => $reset_pass_token,
                ));

                /**
                 * The token was correct, displayed the change/new password form
                 */
                $curStatus = 'resetPasswordForm';
            }
        } elseif (isset($_POST['logSysForgotPassChange']) && isset($_POST['logSysForgotPassNewPassword']) && isset($_POST['logSysForgotPassRetypedPassword'])) {
            $reset_pass_token = urldecode($_POST['token']);

            $sql = $this->dbh->prepare('SELECT uid FROM ' . $this->config['db']['token_table'] . ' WHERE token = ?');
            $sql->execute(array($reset_pass_token));

            $userID = $sql->fetchColumn();

            if ($userID === false) {
                $curStatus = 'invalidToken'; // The token user gave was not valid
                echo $this->getOutput($curStatus);
            } else {
                if (empty($_POST['logSysForgotPassNewPassword']) || empty($_POST['logSysForgotPassRetypedPassword'])) {
                    $curStatus = 'fieldsLeftBlank';
                    echo $this->getOutput($curStatus);
                } elseif ($_POST['logSysForgotPassNewPassword'] !== $_POST['logSysForgotPassRetypedPassword']) {
                    $curStatus = 'passwordDontMatch'; // The new password and retype password submitted didn't match
                    echo $this->getOutput($curStatus);
                } else {
                    $this->changePassword($_POST['logSysForgotPassNewPassword'], $userID);

                    /**
                     * The token shall not be used again, so remove it.
                     */
                    $this->removeToken($reset_pass_token);

                    $curStatus = 'passwordChanged'; // The password was successfully changed
                    echo $this->getOutput($curStatus);
                }
            }
        } elseif (isset($_POST['identification'])) {
            /**
             * Check if username/email is provided and if it's valid and exists
             */
            $identification = $_POST['identification'];

            if (empty($identification)) {
                $curStatus = 'identityNotProvided'; // The identity was not given
                echo $this->getOutput($curStatus, array(
                    'identity_type' => $identName,
                ));
            } else {
                $sql = $this->dbh->prepare('SELECT ' . $this->config['db']['columns']['id'] . ' FROM ' . $this->config['db']['table'] . ' WHERE ' . $this->config['db']['columns']['username'] . '=:login OR ' . $this->config['db']['columns']['email'] . '=:login');
                $sql->bindValue(':login', $identification);

                $sql->execute();
                $cols = $sql->fetch(PDO::FETCH_ASSOC);

                if (empty($cols)) {
                    $curStatus = 'userNotFound'; // The user with the identity given was not found in the users database
                    echo $this->getOutput($curStatus);
                } else {
                    $this->sendResetPasswordToken($cols['id']);

                    echo '<p>An email has been sent to your email inbox with instructions. Check Your Mail Inbox and SPAM Folders.</p><p>You can close this window.</p>';

                    $curStatus = 'emailSent'; // E-Mail has been sent
                }
            }
        }

        return $curStatus;
    }

    /**
     * Send token to reset password
     * @param  int              $uid             User ID
     * @param  boolean|function $messageCallback Callback to make the body of email
     * @return boolean
     */
    public function sendResetPasswordToken($uid, $messageCallback = false)
    {
        $token = self::randStr(40);
        $email = $this->getUser($this->config['db']['columns']['email'], $uid);

        if (!$email) {
            return false;
        }

        $sth = $this->dbh->prepare('INSERT INTO ' . $this->config['db']['token_table'] . ' ( token, uid, requested ) VALUES (?, ?, ?)');
        $sth->execute(
            array(
                $token,
                $uid,
                time(),
            )
        );

        /**
         * Prepare the email to be sent
         */
        $subject = 'Reset Password';

        if (is_callable($messageCallback)) {
            $body = call_user_func_array(
                $messageCallback,
                array(
                    $token,
                    self::curPageURL(),
                )
            );
        } else {
            $body = 'You requested for resetting your password on ' . $this->config['basic']['company'] . '. For this, please click the following link :
            <blockquote>
                <a href="' . self::curPageURL() . '?resetPassToken=' . urlencode($token) . '">Reset Password : ' . $token . '</a>
            </blockquote>';
        }

        $this->sendMail($email, $subject, $body);

        return true;
    }

    /**
     * Check if token for resetting password is valid
     * @return boolean
     */
    public function verifyResetPasswordToken($reset_pass_token)
    {
        $sql = $this->dbh->prepare('SELECT COUNT(1) FROM ' . $this->config['db']['token_table'] . ' WHERE token = ?');
        $sql->execute(array($reset_pass_token));

        return $sql->fetchColumn() !== '0';
    }

    /**
     * Remove a token
     * @param  string  $token Token
     * @return boolean
     */
    public function removeToken($token)
    {
        $sql = $this->dbh->prepare('DELETE FROM ' . $this->config['db']['token_table'] . ' WHERE token = ?');
        $sql->execute(array($token));

        return $sql->rowCount() != 0;
    }

    /**
     * A function that handles the logged in user to change her/his password
     *
     * @param  string $newPassword The new password
     * @return boolean             Whether operation was succesful
     */
    public function changePassword($newPassword, $userID = null)
    {
        if ($userID === null) {
            $userID = $this->userID;
        }

        $hashedPassword = password_hash($newPassword . $this->config['keys']['salt'], PASSWORD_DEFAULT);
        $this->updateUser(array(
            $this->config['db']['columns']['password'] => $hashedPassword,
        ), $userID);

        return true;
    }

    /**
     * Check if user exists
     * @param  string  $identification Username or email
     * @return boolean                 Whether user exist
     */
    public function userExists($identification)
    {
        if ($this->config['features']['email_login'] === true) {
            $query = 'SELECT COUNT(1) FROM ' . $this->config['db']['table'] . ' WHERE ' . $this->config['db']['columns']['username'] . '=:login OR ' . $this->config['db']['columns']['email'] . '=:login';
        } else {
            $query = 'SELECT COUNT(1) FROM ' . $this->config['db']['table'] . ' WHERE ' . $this->config['db']['columns']['username'] . '=:login';
        }

        $sql = $this->dbh->prepare($query);
        $sql->execute(array(
            ':login' => $identification,
        ));

        return $sql->fetchColumn() === '0' ? false : true;
    }

    /**
     * Check if a user ID exists
     * @param  string $userID User ID
     * @return boolean
     */
    public function userIDExists($userID)
    {
        $sql = $this->dbh->prepare('SELECT COUNT(1) FROM ' . $this->config['db']['table'] . ' WHERE ' . $this->config['db']['columns']['id'] . ' = :login');
        $sql->execute(array(
            ':login' => $userID,
        ));

        return $sql->fetchColumn() !== '0';
    }

    /**
     * Get user's info
     * @param  string       $what Column name
     * @param  string       $user User ID
     * @return array|string Value
     */
    public function getUser($what = '*', $user = null)
    {
        if ($user === null) {
            $user = $this->userID;
        }

        if (is_array($what)) {
            $columns = implode(',', $what);
        } else {
            $columns = $what !== '*' ? $what : '*';
        }

        $sql = $this->dbh->prepare('SELECT ' . $columns . ' FROM ' . $this->config['db']['table'] . ' WHERE ' . $this->config['db']['columns']['id'] . ' = ? ORDER BY ' . $this->config['db']['columns']['id'] . ' LIMIT 1');
        $sql->execute(array($user));

        $data = $sql->fetch(PDO::FETCH_ASSOC);

        if (!is_array($what)) {
            $data = $what === '*' ? $data : $data[$what];
        }

        return $data;
    }

    /**
     * Updates the info of user
     * @param  array  $toUpdate Fields to update
     * @param  [type] $user     User ID
     * @return boolean          Whether it was a success
     */
    public function updateUser($toUpdate = array(), $user = null)
    {
        if (is_array($toUpdate) && !isset($toUpdate['id'])) {
            if ($user === null) {
                $user = $this->userID;
            }

            $columns = '';

            foreach ($toUpdate as $k => $v) {
                $columns .= "$k = :$k, ";
            }

            $columns = substr($columns, 0, -2); // Remove last ','

            $sql = $this->dbh->prepare('UPDATE ' . $this->config['db']['table'] . ' SET ' . $columns . ' WHERE ' . $this->config['db']['columns']['id'] . '=:id');
            $sql->bindValue(':id', $user);

            foreach ($toUpdate as $key => $value) {
                $sql->bindValue(":$key", $value);
            }

            $sql->execute();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the time since user joined
     * @param  string $user User ID
     * @return string       Time since
     */
    public function joinedSince($userID = null)
    {
        if ($userID === null) {
            $userID = $this->userID;
        }

        $created    = $this->getUser($this->config['db']['columns']['created'], $userID);
        $timeFirst  = strtotime($created);
        $timeSecond = strtotime('now');
        $memsince   = $timeSecond - strtotime($created);
        $regged     = date('n/j/Y', strtotime($created));

        if ($memsince < 60) {
            $memfor = $memsince . ' Seconds';
        } elseif ($memsince < 120) {
            $memfor = floor($memsince / 60) . ' Minute';
        } elseif ($memsince < 3600 && $memsince > 120) {
            $memfor = floor($memsince / 60) . ' Minutes';
        } elseif ($memsince < 7200 && $memsince > 3600) {
            $memfor = floor($memsince / 3600) . ' Hour';
        } elseif ($memsince < 86400 && $memsince > 3600) {
            $memfor = floor($memsince / 3600) . ' Hours';
        } elseif ($memsince < 172800) {
            $memfor = floor($memsince / 86400) . ' Day';
        } elseif ($memsince < 604800 && $memsince > 172800) {
            $memfor = floor($memsince / 86400) . ' Days';
        } elseif ($memsince < 1209600 && $memsince > 604800) {
            $memfor = floor($memsince / 604800) . ' Week';
        } elseif ($memsince < 2419200 && $memsince > 1209600) {
            $memfor = floor($memsince / 604800) . ' Weeks';
        } elseif ($memsince < 4838400) {
            $memfor = floor($memsince / 2419200) . ' Month';
        } elseif ($memsince < 31536000 && $memsince > 4838400) {
            $memfor = floor($memsince / 2419200) . ' Months';
        } elseif ($memsince < 63072000) {
            $memfor = floor($memsince / 31536000) . ' Year';
        } elseif ($memsince > 63072000) {
            $memfor = floor($memsince / 31536000) . ' Years';
        }

        return (string) $memfor;
    }

    /**
     * 2 Step Verification Login Process
     * ---------------------------------
     * When user logs in, it checks whether there is a device cookie.
     * If there is :
     *   * Checks whether device is registered
     *   * If registered and username & password is correct, user is logged in
     * If there is not :
     *   * Token is sent
     *   * The 'Enter Received token' form is shown
     *   * If the token entered is correct, then a device ID is inserted to table
     *   * The cookie with device ID is created
     * @see  LS::login()      Parameters are similar
     *
     * @param  string         $identification Similar to LS::login()
     * @param  string         $password       Similar to LS::login()
     * @param  boolean        $remember_me    Similar to LS::login()
     * @return boolean|string                 Whether login was successful
     *                                        Current state of 2 Step Login
     */
    public function twoStepLogin($identification = '', $password = '', $remember_me = false, $cookies = true)
    {
        if (isset($_POST['two_step_login_token']) && isset($_SESSION['two_step_login_first_step']) && isset($_SESSION['two_step_login_uid']) && $_SESSION['two_step_login_first_step'] === '1') {
            if (!$this->csrf()) {
                throw new TwoStepLogin('invalid_csrf_token');

                return;
            }

            $uid   = $_SESSION['two_step_login_uid'];
            $token = $_POST['two_step_login_token'];

            $sql = $this->dbh->prepare('SELECT COUNT(1) FROM ' . $this->config['db']['token_table'] . ' WHERE token = ? AND uid = ?');
            $sql->execute(array($token, $uid));

            if ($sql->fetchColumn() === '0') {
                if (($_SESSION['two_step_login_token_tries'] + 1) >= $this->config['two_step_login']['token_tries']) {
                    /**
                     * Prevent user from brute forcing the token
                     */
                    $this->logout();
                } else {
                    $_SESSION['two_step_login_token_tries']++;
                }

                throw new TwoStepLogin(
                    'invalid_token',
                    array(
                        'tries_left' => $this->config['two_step_login']['token_tries'] - $_SESSION['two_step_login_token_tries'],
                    )
                );
            } else {
                /**
                 * Register User's new device if and only if
                 * the user wants to remember the device from
                 * which the user is logging in
                 */

                if (isset($_POST['two_step_login_remember_device'])) {
                    $device_token = self::randStr(10);

                    $sth = $this->dbh->prepare('INSERT INTO ' . $this->config['two_step_login']['devices_table'] . ' ( uid, token, last_access ) VALUES ( ?, ?, ? )');
                    $sth->execute(array($uid, $device_token, time()));

                    setcookie($this->config['cookies']['names']['device'], $device_token, strtotime($this->config['two_step_login']['expire']), $this->config['cookies']['path'], $this->config['cookies']['domain']);
                } else {
                    /**
                     * Verify login for this session
                     */
                    $_SESSION[$this->config['cookies']['names']['device_verified']] = '1';
                }

                /**
                 * Revoke all tokens of user
                 */
                $sql = $this->dbh->prepare('DELETE FROM ' . $this->config['db']['token_table'] . ' WHERE uid = ?');
                $sql->execute(array($uid));

                if ($cookies) {
                    $this->login($this->getUser($this->config['db']['columns']['username'], $uid), false, isset($_POST['two_step_login_remember_me']));
                }

                throw new TwoStepLogin(
                    'login_success',
                    array(
                        'uid' => $uid,
                    )
                );
            }
        } elseif ($identification !== '' && $password !== '') {
            $login = $this->login($identification, $password, $remember_me, false);

            if ($login === false) {
                /**
                 * Username/Password wrong
                 */
                throw new TwoStepLogin('login_fail');
            } elseif (is_array($login) && $login['status'] === 'blocked') {
                throw new TwoStepLogin('blocked', $login);

                return $login;
            } else {
                /**
                 * Get the user ID from \Fr\LS::login()
                 */
                $uid = $login;

                /**
                 * Check if device is verfied so that 2 Step Verification can be skipped
                 */

                if (isset($_COOKIE[$this->config['cookies']['names']['device']])) {
                    $sql = $this->dbh->prepare('SELECT 1 FROM ' . $this->config['two_step_login']['devices_table'] . ' WHERE uid = ? AND token = ?');
                    $sql->execute(array($uid, $_COOKIE[$this->config['cookies']['names']['device']]));

                    if ($sql->fetchColumn() === '1') {
                        $verfied = true;
                        /**
                         * Update last accessed time
                         */
                        $sth = $this->dbh->prepare('UPDATE ' . $this->config['two_step_login']['devices_table'] . ' SET last_access = ? WHERE uid = ? AND token = ?');
                        $sth->execute(
                            array(
                                time(),
                                $uid,
                                $_COOKIE[$this->config['cookies']['names']['device']],
                            )
                        );

                        /**
                         * Delete all session vars used for 2 Step Login
                         */
                        unset($_SESSION['two_step_login_token_first_step']);
                        unset($_SESSION['two_step_login_token_tries']);
                        unset($_SESSION['two_step_login_uid']);

                        if ($cookies) {
                            $this->login($this->getUser($this->config['db']['columns']['username'], $uid), false, $remember_me);
                        }

                        throw new TwoStepLogin(
                            'login_success',
                            array(
                                'uid' => $uid,
                            )
                        );
                    }
                }

                /**
                 * Start the 2 Step Verification Process
                 * Do only if callback is present and if
                 * the device is not verified
                 */

                if (is_callable($this->config['two_step_login']['send_callback']) && !isset($verified)) {
                    /**
                     * The first part of 2 Step Login is completed
                     */
                    $_SESSION['two_step_login_first_step']  = '1';
                    $_SESSION['two_step_login_token_tries'] = 0;
                    $_SESSION['two_step_login_uid']         = $uid;

                    if ($this->config['features']['block_brute_force']) {
                        $sth = $this->dbh->prepare('SELECT COUNT(1) FROM ' . $this->config['db']['token_table'] . ' WHERE uid = ? ');
                        $sth->execute(array($uid));

                        if ($sth->fetchColumn() >= $this->config['brute_force']['max_tokens']) {
                            /**
                             * Account Blocked. User will be only able to
                             * re-login at the time in UNIX timestamp
                             */
                            $eligible_for_next_login_time = strtotime('+' . $this->config['brute_force']['time_limit'] . ' seconds', time());

                            $this->updateUser(array(
                                $this->config['db']['columns']['attempt'] => 'b-' . $eligible_for_next_login_time,
                            ), $uid);

                            /**
                             * Delete all 5 tokens
                             */
                            $sth = $this->dbh->prepare('DELETE FROM ' . $this->config['db']['token_table'] . ' WHERE uid = :uid LIMIT :max_tokens');
                            $sth->bindValue(':uid', $uid);
                            $sth->bindValue(':max_tokens', $this->config['brute_force']['max_tokens'], PDO::PARAM_INT);
                            $sth->execute();

                            $_SESSION['two_step_login_first_step'] = '0';

                            $this->logout();
                        }
                    }

                    /**
                     * The 2nd parameter depends on `config` -> `two_step_login` -> `numeric`
                     */
                    $token = self::randStr($this->config['two_step_login']['token_length'], $this->config['two_step_login']['numeric']);

                    /**
                     * Save the token in DB
                     */
                    $sth = $this->dbh->prepare('INSERT INTO ' . $this->config['db']['token_table'] . ' ( token, uid, requested ) VALUES (?, ?, ?)');
                    $sth->execute(
                        array(
                            $token,
                            $uid,
                            time(),
                        )
                    );

                    $that = $this;
                    call_user_func_array($this->config['two_step_login']['send_callback'], array(&$that, $uid, $token));

                    throw new TwoStepLogin('enter_token_form', array(
                        'remember_me' => $remember_me,
                        'uid'         => $uid,
                    ));
                } else {
                    self::log('two_step_login: Token Callback not present');
                }
            }
        }

        /**
         * 2 Step Login is not doing any actions or
         * hasn't returned anything before. If so,
         * then return false to indicate that the
         * function is not doing anything
         */

        return false;
    }

    /**
     * Get authorized devices
     * @return array Authorized devices
     */
    public function getDevices()
    {
        if ($this->loggedIn) {
            $sql = $this->dbh->prepare('SELECT * FROM ' . $this->config['two_step_login']['devices_table'] . ' WHERE uid = ?');
            $sql->execute(array($this->userID));

            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * Revoke a device
     * @param  string  $device_id Device ID
     * @return boolean               Whether it was revoked
     */
    public function revokeDevice($device_id)
    {
        if ($this->loggedIn) {
            $sql = $this->dbh->prepare('DELETE FROM ' . $this->config['two_step_login']['devices_table'] . ' WHERE uid = ? AND token = ?');
            $sql->execute(array($this->userID, $device_id));

            if (isset($_SESSION[$this->config['cookies']['names']['device_verified']])) {
                unset($_SESSION[$this->config['cookies']['names']['device_verified']]);
            }

            if ($this->getDeviceID() === $device_id) {
                $this->logout();
            }

            return $sql->rowCount() == 1;
        }
    }

    /**
     * Get output for each states
     * @param  string $state     The output of the state
     * @param  array  $extraInfo Extra parameters about the state
     * @return string            HTML output
     */
    public function getOutput($state, $extraInfo = array())
    {
        if (is_callable($this->config['basic']['output_callback'])) {
            $that = $this;

            return call_user_func_array($this->config['basic']['output_callback'], array(
                &$that,
                $state,
                $extraInfo,
            ));
        } else {
            return null;
        }
    }

    /**
     * Whether a user is logged in
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    /**
     * Get device ID
     * @return boolean|string FALSE if device cookie does not exist
     */
    public function getDeviceID()
    {
        if (isset($_COOKIE[$this->config['cookies']['names']['device']])) {
            return $_COOKIE[$this->config['cookies']['names']['device']];
        } else {
            return false;
        }
    }

    /**
     * ---------------------
     * Extra Tools/Functions
     * ---------------------
     */

    /**
     * Check if E-Mail is valid
     * @param  string $email Email
     * @return boolean
     */
    public static function validEmail($email = '')
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Get the current page URL
     * @return string URL
     */
    public static function curPageURL()
    {
        $pageURL = 'http';

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $pageURL .= 's';
        }

        $pageURL .= '://';

        if ($_SERVER['SERVER_PORT'] !== '80') {
            $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }

        return $pageURL;
    }

    /**
     * Generate a Random String
     * @param  int     $length Length of string
     * @param  boolean $int    Should string be integer
     * @return string          Random string
     */
    public static function randStr($length, $int = false)
    {
        $random_str = '';
        $chars      = $int ? '0516243741506927589' : 'subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $size       = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $random_str .= $chars[rand(0, $size)];
        }

        return $random_str;
    }

    /**
     * Get the current page path.
     * @return string Example: /mypage, /folder/mypage.php
     */
    public static function curPage()
    {
        $parts = parse_url(self::curPageURL());

        return $parts['path'];
    }

    /**
     * Do a redirect
     * @param  [type]  $url    Where to redirect to
     * @param  integer $status Redirect status code
     * @return void
     */
    public static function redirect($url, $status = 302)
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    /**
     * Send an email
     * @param  string $email   Recipient's email
     * @param  string $subject Subject
     * @param  string $body    Message
     * @return void
     */
    public function sendMail($email, $subject, $body)
    {
        /**
         * If there is a callback for email sending, use it else PHP's mail()
         */

        if (is_callable($this->config['basic']['email_callback'])) {
            $that = $this;
            call_user_func_array($this->config['basic']['email_callback'], array(&$that, $email, $subject, $body));
        } else {
            $headers   = array();
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
            $headers[] = 'From: ' . $this->config['basic']['email'];
            $headers[] = 'Reply-To: ' . $this->config['basic']['email'];
            mail($email, $subject, $body, implode('\r\n', $headers));
        }
    }

    /**
     * CSRF Protection
     */
    public function csrf($type = '')
    {
        if (!isset($_COOKIE['csrf_token'])) {
            $csrf_token = self::randStr(5);
            setcookie('csrf_token', $csrf_token, 0, $this->config['cookies']['path'], $this->config['cookies']['domain']);
        } else {
            $csrf_token = $_COOKIE['csrf_token'];
        }

        if ($type === 's') {
            /**
             * Output as string
             */
            return urlencode($csrf_token);
        } elseif ($type === 'g') {
            /**
             * Output as a GET parameter
             */
            return '&csrf_token=' . urlencode($csrf_token);
        } elseif ($type === 'i') {
            /**
             * Output as an input field
             */
            return '<input type="hidden" name="csrf_token" value="' . $csrf_token . '" />';
        } else {
            /**
             * Check CSRF validity
             */

            if ((isset($_POST['csrf_token']) && $_COOKIE['csrf_token'] === $_POST['csrf_token']) || (isset($_GET['csrf_token']) && $_COOKIE['csrf_token'] === $_GET['csrf_token'])) {
                return true;
            } else {
                /**
                 * CSRF Token doesn't match.
                 */
                return false;
            }
        }
    }

    /**
     * -------------------------
     * End Extra Tools/Functions
     * -------------------------
     */
}
