<?php
namespace Fr;

/**
.---------------------------------------------------------------------------.
| The Francium Project                                                      |
| ------------------------------------------------------------------------- |
| This software logSys is a part of the Francium (Fr) project.              |
| http://subinsb.com/the-francium-project                                   |
| ------------------------------------------------------------------------- |
|     Author: Subin Siby                                                    |
| Copyright (c) 2014 - 2015, Subin Siby. All Rights Reserved.               |
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
|  Version:       0.5 (Last Updated on 2016 March 11)                       |
|  Contact:       http://github.com/subins2000/logsys                       |
|  Documentation: https://subinsb.com/php-logsys                            |
|  Support:       http://subinsb.com/ask/php-logsys                         |
'---------------------------------------------------------------------------'
*/

class LS {

  /**
   * ------------
   * BEGIN CONFIG
   * ------------
   * Edit the configuraion
   */
  
  public static $default_config = array(
    /**
     * Basic Config of logSys
     */
    "basic" => array(
      "company" => "My Site",
      "email" => "email@mysite.com",
      "email_callback" => 0
    ),
    
    /**
     * Database Configuration
     */
    "db" => array(
      "host" => "",
      "port" => 3306,
      "username" => "",
      "password" => "",
      "name" => "",
      "table" => "users",
      "token_table" => "resetTokens"
    ),
    
    /**
     * Keys used for encryption
     * DONT MAKE THIS PUBLIC
     */
    "keys" => array(
      /**
       * Changing cookie key will expire all current active login sessions
       */
      "cookie" => "ckxc436jd*^30f840v*9!@#$",
      /**
       * `salt` should not be changed after users are created
       */
      "salt" => "^#$4%9f+1^p9)M@4M)V$"
    ),
    
    /**
     * Enable/Disable certain features
     */
    "features" => array(
      /**
       * Should I Call session_start();
       */
      "start_session" => true,
      /**
       * Enable/Disable Login using Username & E-Mail
       */
      "email_login" => true,
      /**
       * Enable/Disable `Remember Me` feature
       */
      "remember_me" => true,
      /**
       * Should \Fr\LS::init() be called automatically
       */
      "auto_init" => false,
      
      /**
       * Prevent Brute Forcing
       * ---------------------
       * By enabling this, logSys will deny login for the time mentioned 
       * in the "brute_force"->"time_limit" seconds after "brute_force"->"tries"
       * number of incorrect login tries.
       */
      "block_brute_force" => true,
      
      /**
       * Two Step Login
       * --------------
       * By enabling this, a checking is done when user visits
       * whether the device he/she uses is approved by the user.
       * Allows the original user to revoke logins in other devices/places
       * Useful if the user forgot to logout in some place.
       */
      "two_step_login" => false
    ),
    
    /**
     * `Blocking Brute Force Attacks` options
     */
    "brute_force" => array(
      /**
       * No of tries alloted to each user
       */
      "tries" => 5,
      /**
       * The time IN SECONDS for which block from login action should be done after
       * incorrect login attempts. Use http://www.easysurf.cc/utime.htm#m60s
       * for converting minutes to seconds. Default : 5 minutes
       */
      "time_limit" => 300
    ),
    
    /**
     * Information about pages
     */
    "pages" => array(
      /**
       * Pages that doesn't require logging in.
       * Exclude login page, but include REGISTER page.
       * Use Relative links or $_SERVER['REQUEST_URI']
       */
      "no_login" => array(
        
      ),
      /**
       * The login page. ex : /login.php or /accounts/login.php
       */
      "login_page" => "",
      /**
       * The home page. The main page for logged in users.
       * logSys redirects to here after user logs in
       */
      "home_page" => "",
    ),
    
    /**
     * Settings about cookie creation
     */
    "cookies" => array(
      /**
       * Default : cookies expire in 30 days. The value is
       * for setting in strtotime() function
       * http://php.net/manual/en/function.strtotime.php
       */
      "expire" => "+30 days",
      "path" => "/",
      "domain" => "",
    ),
    
    /**
     * 2 Step Login
     */
    'two_step_login' => array(
      /**
       * Message to show before displaying "Enter Token" form.
       */
      'instruction' => '',
      
      /**
       * Callback when token is generated.
       * Used to send message to user (Phone/E-Mail)
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
      'first_check_only' => true
    )
  );
  
  /* ------------
   * END Config.
   * ------------
   * No more editing after this line.
   */
  
  public static $config = array();
  private static $constructed = false;
  
  /**
   * Merge user config and default config
   * $direct is for knowing whether the function is called by self::construct()
   */
  public static function config($config = null, $direct = true){
    if($config != null){
      self::$config = $config;
    }
    self::$config = array_replace_recursive(self::$default_config, self::$config);
    if($direct == true){
      self::construct();
    }
  }
  
  /**
   * Log something in the Francium.log file.
   * To enable logging, make a file called "Francium.log" in the directory
   * where "class.logsys.php" file is situated
   */
  public static function log($msg = ""){
    $log_file = __DIR__ . "/Francium.log";
    if(file_exists($log_file)){
      if($msg != ""){
        $message = "[" . date("Y-m-d H:i:s") . "] $msg";
        $fh = fopen($log_file, 'a');
        fwrite($fh, $message . "\n");
        fclose($fh);
      }
    }
  }
  
  public static $loggedIn = false;
  public static $db = true;
  public static $user = false;
  private static $init_called = false;
  private static $cookie, $session, $remember_cookie, $dbh;
  
  public static function construct($called_from = ""){
    if(self::$constructed === false){
      self::config(null, false);
      self::$constructed = true;
      
      if(self::$config['features']['start_session'] === true){
        session_start();
      }
      /**
      * Try connecting to Database Server
      */
      try{
        /**
        * Add the login page to the array of pages that doesn't need logging in
        */
        array_push(self::$config['pages']['no_login'], self::$config['pages']['login_page']);
        
        self::$dbh = new \PDO("mysql:dbname=". self::$config['db']['name'] .";host=". self::$config['db']['host'] .";port=". self::$config['db']['port']. ";charset=utf8", self::$config['db']['username'], self::$config['db']['password']);
        self::$db = true;
        
        self::$cookie = isset($_COOKIE['logSyslogin']) ? $_COOKIE['logSyslogin'] : false;
        self::$session = isset($_SESSION['logSyscuruser']) ? $_SESSION['logSyscuruser'] : false;
        self::$remember_cookie = isset($_COOKIE['logSysrememberMe']) ? $_COOKIE['logSysrememberMe'] : false;
        
        $encUserID = hash("sha256", self::$config['keys']['cookie'] . self::$session . self::$config['keys']['cookie']);

        if(self::$cookie == $encUserID){
          self::$loggedIn = true;
        }else{
          self::$loggedIn = false;
        }
        
        /**
        * If there is a Remember Me Cookie and the user is not logged in,
        * then log in the user with the ID in the remember cookie, if it
        * matches with the decrypted value in `logSyslogin` cookie
        */
        if(self::$config['features']['remember_me'] === true && self::$remember_cookie !== false && self::$loggedIn === false){
          $encUserID = hash("sha256", self::$config['keys']['cookie']. self::$remember_cookie . self::$config['keys']['cookie']);
          if(self::$cookie == $encUserID){
            self::$loggedIn = true;
          }else{
            self::$loggedIn = false;
          }
          
          if(self::$loggedIn === true){
            $_SESSION['logSyscuruser'] = self::$remember_cookie;
            self::$session = self::$remember_cookie;
          }
        }

        self::$user = self::$session;
        
        /**
         * Check if devices is authorized to use the account
         */
        if(self::$config['features']['two_step_login'] === true && self::$loggedIn){
          $login_page = self::curPage() === self::$config['pages']['login_page'];

          if(!isset($_COOKIE['logSysdevice']) && $login_page === false){
            /**
             * The device cookie is not even set. So, logout
             */
            self::logout();
            $called_from = "login";
          }else if(self::$config['two_step_login']['first_check_only'] === false || (self::$config['two_step_login']['first_check_only'] === true && !isset($_SESSION['device_check']))){
            $sql = self::$dbh->prepare("SELECT '1' FROM `". self::$config['two_step_login']['devices_table'] ."` WHERE `uid` = ? AND `token` = ?");
            $sql->execute(array(self::$user, $_COOKIE['logSysdevice']));
            
            /**
             * Device not authorized, so remove device cookie & logout
             */
            if($sql->fetchColumn() !== '1' && $login_page === false){
              setcookie("logSysdevice", "", time() - 10);
              self::logout();
              $called_from = "login";
            }else{
              $_SESSION['device_check'] = 1;
            }
          }
        }
        
        if(self::$config['features']['auto_init'] === true && $called_from != "logout" && $called_from != "login"){
          self::init();
        }
        return true;
      }catch(\PDOException $e) {
        /**
         * Couldn't connect to Database
         */
        self::log('Couldn\'t connect to database. Check \Fr\LS::$config["db"] credentials');
        return false;
      }
    }
  }
  
  /**
   * A function that will automatically redirect user according to his/her login status
   */
  public static function init() {
    self::construct();
    if(self::$loggedIn === true && array_search(self::curPage(), self::$config['pages']['no_login']) !== false){
      self::redirect(self::$config['pages']['home_page']);
    }elseif(self::$loggedIn === false && array_search(self::curPage(), self::$config['pages']['no_login']) === false){
      self::redirect(self::$config['pages']['login_page']);
    }
    self::$init_called = true;
  }
  
  /**
   * A function to login the user with the username and password.
   * As of version 0.4, it is required to include the remember_me parameter
   * when calling this function to avail the "Remember Me" feature.
   */
  public static function login($username, $password, $remember_me = false, $cookies = true){
    self::construct("login");
    if(self::$db === true){
      /**
       * We Add LIMIT to 1 in SQL query because to
       * get an array with key as the column name.
       */
      if(self::$config['features']['email_login'] === true){
        $query = "SELECT `id`, `password`, `password_salt`, `attempt` FROM `". self::$config['db']['table'] ."` WHERE `username`=:login OR `email`=:login ORDER BY `id` LIMIT 1";
      }else{
        $query = "SELECT `id`, `password`, `password_salt`, `attempt` FROM `". self::$config['db']['table'] ."` WHERE `username`=:login ORDER BY `id` LIMIT 1";
      }
      
      $sql = self::$dbh->prepare($query);
      $sql->bindValue(":login", $username);
      $sql->execute();
      
      if($sql->rowCount() == 0){
        // No such user like that
        return false;
      }else{
        /**
         * Get the user details
         */
        $rows = $sql->fetch(\PDO::FETCH_ASSOC);
        $us_id = $rows['id'];
        $us_pass = $rows['password'];
        $us_salt = $rows['password_salt'];
        $status = $rows['attempt'];
        $saltedPass = hash('sha256', $password . self::$config['keys']['salt'] . $us_salt);
        
        if(substr($status, 0, 2) == "b-"){
          $blockedTime = substr($status, 2);
          if(time() < $blockedTime){
            $blocked = true;
            return array(
              "status"  => "blocked",
              "minutes" => round(abs($blockedTime - time()) / 60, 0),
              "seconds" => round(abs($blockedTime - time()) / 60*60, 2)
            );
          }else{
            // remove the block, because the time limit is over
            self::updateUser(array(
              "attempt" => "" // No tries at all
            ), $us_id);
          }
        }
        /**
         * Why login if password is empty ?
         * --------------------------------
         * If using OAuth, you have to login someone without knowing their password,
         * this usage is helpful. But, it makes a serious security problem too.
         * Hence, before calling \Fr\LS::login() in the login page, it is
         * required to check whether the password fieldis left blank
         */
        if(!isset($blocked) && ($saltedPass == $us_pass || $password == "")){
          if($cookies === true){
            
            $_SESSION['logSyscuruser'] = $us_id;
            
            setcookie("logSyslogin", hash("sha256", self::$config['keys']['cookie'] . $us_id . self::$config['keys']['cookie']), strtotime(self::$config['cookies']['expire']), self::$config['cookies']['path'], self::$config['cookies']['domain']);

            if( $remember_me === true && self::$config['features']['remember_me'] === true ){
              setcookie("logSysrememberMe", $us_id, strtotime(self::$config['cookies']['expire']), self::$config['cookies']['path'], self::$config['cookies']['domain']);
            }
            self::$loggedIn = true;
            
            if(self::$config['features']['block_brute_force'] === true){
              /**
               * If Brute Force Protection is Enabled,
               * Reset the attempt status
               */
              self::updateUser(array(
                "attempt" => "0"
              ), $us_id);
            }
            
            // Redirect
            if(self::$init_called){
              self::redirect(self::$config['pages']['home_page']);
            }
            return true;
          }else{
            /**
             * If cookies shouldn't be set,
             * it means login() was called
             * to get the user's ID. So, return it
             */
            return $us_id;
          }
        }else{
          /**
           * Incorrect password
           * ------------------
           * Check if brute force protection is enabled
           */
          if(self::$config['features']['block_brute_force'] === true){
            $max_tries = self::$config['brute_force']['tries'];
            
            if($status == ""){
              // User was not logged in before
              self::updateUser(array(
                "attempt" => "1" // Tried 1 time
              ), $us_id);
            }else if($status == $max_tries){
              /**
               * Account Blocked. User will be only able to
               * re-login at the time in UNIX timestamp
               */
              $eligible_for_next_login_time = strtotime("+". self::$config['brute_force']['time_limit'] ." seconds", time());
              self::updateUser(array(
                "attempt" => "b-" . $eligible_for_next_login_time
              ), $us_id);
              return array(
                "status"  => "blocked",
                "minutes" => round(abs($eligible_for_next_login_time - time()) / 60, 0),
                "seconds" => round(abs($eligible_for_next_login_time - time()) / 60*60, 2)
              );
            }else if($status < $max_tries){
              // If the attempts are less than Max and not Max
              self::updateUser(array(
                "attempt" => $status + 1 // Increase the no of tries by +1.
              ), $us_id);
            }
          }
          return false;
        }
      }
    }
  }
  
  /**
   * A function to register a user with passing the username, password
   * and optionally any other additional fields.
   */
  public static function register( $id, $password, $other = array() ){
    self::construct();
    if( self::userExists($id) || (isset($other['email']) && self::userExists($other['email'])) ){
      return "exists";
    }else{
      $randomSalt  = self::rand_string(20);
      $saltedPass  = hash('sha256', $password. self::$config['keys']['salt'] . $randomSalt);
      
      if( count($other) == 0 ){
        /* If there is no other fields mentioned, make the default query */
        $sql = self::$dbh->prepare("INSERT INTO `". self::$config['db']['table'] ."` (`username`, `password`, `password_salt`) VALUES(:username, :password, :passwordSalt)");
      }else{
        /* if there are other fields to add value to, make the query and bind values according to it */
        $keys   = array_keys($other);
        $columns = implode(",", $keys);
        $colVals = implode(",:", $keys);
        $sql   = self::$dbh->prepare("INSERT INTO `". self::$config['db']['table'] ."` (`username`, `password`, `password_salt`, $columns) VALUES(:username, :password, :passwordSalt, :$colVals)");
        foreach($other as $key => $value){
          $value = htmlspecialchars($value);
          $sql->bindValue(":$key", $value);
        }
      }
      /* Bind the default values */
      $sql->bindValue(":username", $id);
      $sql->bindValue(":password", $saltedPass);
      $sql->bindValue(":passwordSalt", $randomSalt);
      $sql->execute();
      return true;
    }
  }
  
  /**
   * Logout the current logged in user by deleting the cookies and destroying session
   */
  public static function logout(){
    self::construct("logout");
    session_destroy();
    setcookie("logSyslogin", "", time() - 10, self::$config['cookies']['path'], self::$config['cookies']['domain']);
    setcookie("logSysrememberMe", "", time() - 10, self::$config['cookies']['path'], self::$config['cookies']['domain']);
    
    /**
     * Wait for the cookies to be removed, then redirect
     */
    usleep(2000);
    self::redirect(self::$config['pages']['login_page']);
    return true;
  }
  
  /**
   * A function to handle the Forgot Password process
   */
  public static function forgotPassword(){
    self::construct();
    $curStatus = "initial";  // The Current Status of Forgot Password process
    $identName = self::$config['features']['email_login'] === false ? "Username" : "Username / E-Mail";
    
    if( !isset($_POST['logSysForgotPass']) && !isset($_GET['resetPassToken']) && !isset($_POST['logSysForgotPassChange']) ){
      $html = '<form action="'. self::curPageURL() .'" method="POST">';
        $html .= "<label>";
          $html .= "<p>{$identName}</p>";
          $html .= "<input type='text' id='logSysIdentification' placeholder='Enter your {$identName}' size='25' name='identification' />";
        $html .= "</label>";
        $html .= "<p><button name='logSysForgotPass' type='submit'>Reset Password</button></p>";
      $html .= "</form>";
      echo $html;
      /**
       * The user had moved to the reset password form ie she/he is currently seeing the forgot password form
       */
      $curStatus = "resetPasswordForm";
    }elseif( isset($_GET['resetPassToken']) && !isset($_POST['logSysForgotPassChange']) ){
      /**
       * The user gave the password reset token. Check if the token is valid.
       */
      $reset_pass_token = urldecode($_GET['resetPassToken']);
      $sql = self::$dbh->prepare("SELECT `uid` FROM `". self::$config['db']['token_table'] ."` WHERE `token` = ?");
      $sql->execute(array($reset_pass_token));
      
      if($sql->rowCount() == 0 || $reset_pass_token == ""){
        echo "<h3>Error : Wrong/Invalid Token</h3>";
        $curStatus = "invalidToken"; // The token user gave was not valid
      }else{
        /**
         * The token is valid, display the new password form
         */
        $html = "<p>The Token key was Authorized. Now, you can change the password</p>";
        $html .= "<form action='{$_SERVER['PHP_SELF']}' method='POST'>";
          $html .= "<input type='hidden' name='token' value='{$reset_pass_token}' />";
          $html .= "<label>";
            $html .= "<p>New Password</p>";
            $html .= "<input type='password' name='logSysForgotPassNewPassword' />";
          $html .= "</label><br/>";
          $html .= "<label>";
            $html .= "<p>Retype Password</p>";
            $html .= "<input type='password' name='logSysForgotPassRetypedPassword'/>";
          $html .= "</label><br/>";
          $html .= "<p><button name='logSysForgotPassChange'>Reset Password</button></p>";
        $html .= "</form>";
        echo $html;
        /**
         * The token was correct, displayed the change/new password form
         */
        $curStatus = "changePasswordForm";
      }
    }elseif(isset($_POST['logSysForgotPassChange']) && isset($_POST['logSysForgotPassNewPassword']) && isset($_POST['logSysForgotPassRetypedPassword'])){
      $reset_pass_token = urldecode($_POST['token']);
      $sql = self::$dbh->prepare("SELECT `uid` FROM `". self::$config['db']['token_table'] ."` WHERE `token` = ?");
      $sql->execute(array($reset_pass_token));
      
      if( $sql->rowCount() == 0 || $reset_pass_token == "" ){
        echo "<h3>Error : Wrong/Invalid Token</h3>";
        $curStatus = "invalidToken"; // The token user gave was not valid
      }else{
        if($_POST['logSysForgotPassNewPassword'] == "" || $_POST['logSysForgotPassRetypedPassword'] == ""){
          echo "<h3>Error : Passwords Fields Left Blank</h3>";
          $curStatus = "fieldsLeftBlank";
        }elseif( $_POST['logSysForgotPassNewPassword'] != $_POST['logSysForgotPassRetypedPassword'] ){
          echo "<h3>Error : Passwords Don't Match</h3>";
          $curStatus = "passwordDontMatch"; // The new password and retype password submitted didn't match
        }else{
          /**
           * We must create a fake assumption that the user is logged in to
           * change the password as \Fr\LS::changePassword()
           * requires the user to be logged in.
           */
          self::$user = $sql->fetchColumn();
          self::$loggedIn = true;
          
          if(self::changePassword($_POST['logSysForgotPassNewPassword'])){
            self::$user = false;
            self::$loggedIn = false;
            
            /**
             * The token shall not be used again, so remove it.
             */
            $sql = self::$dbh->prepare("DELETE FROM `". self::$config['db']['token_table'] ."` WHERE `token` = ?");
            $sql->execute(array($reset_pass_token));
            
            echo "<h3>Success : Password Reset Successful</h3><p>You may now login with your new password.</p>";
            $curStatus = "passwordChanged"; // The password was successfully changed
          }
        }
      }
    }elseif(isset($_POST['identification'])){
      /**
       * Check if username/email is provided and if it's valid and exists
       */
      $identification = $_POST['identification'];
      if($identification == ""){
        echo "<h3>Error : {$identName} not provided</h3>";
        $curStatus = "identityNotProvided"; // The identity was not given
      }else{
        $sql = self::$dbh->prepare("SELECT `email`, `id` FROM `". self::$config['db']['table'] ."` WHERE `username`=:login OR `email`=:login");
        $sql->bindValue(":login", $identification);
        $sql->execute();
        if($sql->rowCount() == 0){
          echo "<h3>Error : User Not Found</h3>";
          $curStatus = "userNotFound"; // The user with the identity given was not found in the users database
        }else{
          $rows  = $sql->fetch(\PDO::FETCH_ASSOC);
          $email = $rows['email'];
          $uid   = $rows['id'];
          
          /**
           * Make token and insert into the table
           */
          $token = self::rand_string(40);
          $sql = self::$dbh->prepare("INSERT INTO `". self::$config['db']['token_table'] ."` (`token`, `uid`, `requested`) VALUES (?, ?, NOW())");
          $sql->execute(array($token, $uid));
          $encodedToken = urlencode($token);
          
          /**
           * Prepare the email to be sent
           */
          $subject = "Reset Password";
          $body   = "You requested for resetting your password on ". self::$config['basic']['company'] .". For this, please click the following link :
          <blockquote>
            <a href='". self::curPageURL() ."?resetPassToken={$encodedToken}'>Reset Password : {$token}</a>
          </blockquote>";
          self::sendMail($email, $subject, $body);
          
          echo "<p>An email has been sent to your email inbox with instructions. Check Your Mail Inbox and SPAM Folders.</p><p>You can close this window.</p>";
          $curStatus = "emailSent"; // E-Mail has been sent
        }
      }
    }
    return $curStatus;
  }
  
  /**
   * A function that handles the logged in user to change her/his password
   */
  public static function changePassword($newpass){
    self::construct();
    if(self::$loggedIn){
      $randomSalt = self::rand_string(20);
      $saltedPass = hash('sha256', $newpass . self::$config['keys']['salt'] . $randomSalt);
      $sql = self::$dbh->prepare("UPDATE `". self::$config['db']['table'] ."` SET `password` = ?, `password_salt` = ? WHERE `id` = ?");
      $sql->execute(array($saltedPass, $randomSalt, self::$user));
      return true;
    }else{
      echo "<h3>Error : Not Logged In</h3>";
      return "notLoggedIn";
    }
  }
  
  /**
   * Check if user exists with ther username/email given
   * $identification - Either email/username
   */
  public static function userExists($identification){
    self::construct();
    if(self::$config['features']['email_login'] === true){
      $query = "SELECT `id` FROM `". self::$config['db']['table'] ."` WHERE `username`=:login OR `email`=:login";
    }else{
      $query = "SELECT `id` FROM `". self::$config['db']['table'] ."` WHERE `username`=:login";
    }
    $sql = self::$dbh->prepare($query);
    $sql->execute(array(
      ":login" => $identification
    ));
    return $sql->rowCount() == 0 ? false : true;
  }
  
  /**
   * Fetches data of user in database. Returns a single value or an
   * array of value according to parameteres given to the function
   */
  public static function getUser($what = "*", $user = null){
    self::construct();
    if($user == null){
      $user = self::$user;
    }
    if( is_array($what) ){
      $columns = implode("`,`", $what);
      $columns  = "`{$columns}`";
    }else{
      $columns = $what != "*" ? "`$what`" : "*";
    }
    
    $sql = self::$dbh->prepare("SELECT {$columns} FROM `". self::$config['db']['table'] ."` WHERE `id` = ? ORDER BY `id` LIMIT 1");
    $sql->execute(array($user));
    
    $data = $sql->fetch(\PDO::FETCH_ASSOC);
    if( !is_array($what) ){
      $data = $what == "*" ? $data : $data[$what];
    }
    return $data;
  }
  
  /**
   * Updates the info of user in DB
   */
  public static function updateUser($toUpdate = array(), $user = null){
    self::construct();
    if( is_array($toUpdate) && !isset($toUpdate['id']) ){
      if($user == null){
        $user = self::$user;
      }
      $columns = "";
      foreach($toUpdate as $k => $v){
        $columns .= "`$k` = :$k, ";
      }
      $columns = substr($columns, 0, -2); // Remove last ","
    
      $sql = self::$dbh->prepare("UPDATE `". self::$config['db']['table'] ."` SET {$columns} WHERE `id`=:id");
      $sql->bindValue(":id", $user);
      foreach($toUpdate as $key => $value){
        $value = htmlspecialchars($value);
        $sql->bindValue(":$key", $value);
      }
      $sql->execute();
      
    }else{
      return false;
    }
  }
  
  /**
   * Returns a string which shows the time since the user has joined
   */
  public static function joinedSince($user = null){
    self::construct();
    if($user == null){
      $user = self::$user;
    }
    $created = self::getUser("created");
    $timeFirst  = strtotime($created);
    $timeSecond = strtotime("now");
    $memsince   = $timeSecond - strtotime($created);
    $regged     = date("n/j/Y", strtotime($created));
    
    if($memsince < 60) {
      $memfor = $memsince . " Seconds";
    }else if($memsince < 120){
      $memfor = floor ($memsince / 60) . " Minute";
    }else if($memsince < 3600 && $memsince > 120){
      $memfor = floor($memsince / 60) . " Minutes";
    }else if($memsince < 7200 && $memsince > 3600){
      $memfor = floor($memsince / 3600) . " Hour";
    }else if($memsince < 86400 && $memsince > 3600){
      $memfor = floor($memsince / 3600) . " Hours";
    }else if($memsince < 172800){
      $memfor = floor($memsince / 86400) . " Day";
    }else if($memsince < 604800 && $memsince > 172800){
      $memfor = floor($memsince / 86400) . " Days";
    }else if($memsince < 1209600 && $memsince > 604800){
      $memfor = floor($memsince / 604800) . " Week";
    }else if($memsince < 2419200 && $memsince > 1209600){
      $memfor = floor($memsince / 604800) . " Weeks";
    }else if($memsince < 4838400){
      $memfor = floor($memsince / 2419200) . " Month";
    }else if($memsince < 31536000 && $memsince > 4838400){
      $memfor = floor($memsince / 2419200) . " Months";
    }else if($memsince < 63072000){
      $memfor = floor($memsince / 31536000) . " Year";
    }else if($memsince > 63072000){
      $memfor = floor($memsince / 31536000) . " Years";
    }
    return (string) $memfor;
  }
  
  /**
   * 2 Step Verification Login Process
   * ---------------------------------
   * When user logs in, it checks whether there is a cookie named "logSysdevice" and if there is :
   *    1. Checks `config` -> `two_step_login` -> `devices_table` table in DB whethere there is a token with value as that of $_COOKIES['logSysdevice']
   *    2. If there is a row in table, then the "Enter Received Token" form is not shown and is directly logged in if username & pass is correct
   * If there is not a cookie, then :
   *    1. The "Enter Received token" form is shown
   *    2. If the token entered is correct, then a unique string is set as $_COOKIE['logSysdevice'] value and inserted to `config` -> `two_step_login` -> `devices_table` table in DB
   *    3. The $_COOKIE['logSysdevice'] is set to be stored for 4 months
   * ---------------------
   * ^ In the above instructions, the token sending to E-Mail/SMS is not mentioned. Assume that it is done
   */
  public static function twoStepLogin($identification = "", $password = "", $remember_me = false){
    if(isset($_POST['logSys_two_step_login-token']) && isset($_POST['logSys_two_step_login-uid']) && $_SESSION['logSys_two_step_login-first_step'] === '1'){
      /**
       * The user's ID and token is got through the form
       * User = One who is about to log in and is stuck at 2 step verification
       */
      $uid = $_POST['logSys_two_step_login-uid'];
      $token = $_POST['logSys_two_step_login-token'];
      
      $sql = self::$dbh->prepare("SELECT COUNT(1) FROM `". self::$config['db']['token_table'] ."` WHERE `token` = ? AND `uid` = ?");
      $sql->execute(array($token, $uid));
      
      if($sql->fetchColumn() == 0){
        /**
         * To prevent user from Brute Forcing the token, we set the
         * status of the first login step to false,
         * so that the user would have to login again
         */
        $_SESSION['logSys_two_step_login-first_step'] = '0';
        echo "<h3>Error : Wrong/Invalid Token</h3>";
        return "invalidToken";
      }else{
        /**
         * Register User's new device if and only if
         * the user wants to remember the device from
         * which the user is logging in
         */
        if(isset($_POST['logSys_two_step_login-dontask'])){
          $device_token = self::rand_string(10);
          $sql = self::$dbh->prepare("INSERT INTO `". self::$config['two_step_login']['devices_table'] ."` (`uid`, `token`, `last_access`) VALUES (?, ?, NOW())");
          $sql->execute(array($uid, $device_token));
          setcookie("logSysdevice", $device_token, strtotime(self::$config['two_step_login']['expire']), self::$config['cookies']['path'], self::$config['cookies']['domain']);
        }
        
        /**
         * Revoke token from reusing
         */
        $sql = self::$dbh->prepare("DELETE FROM `". self::$config['db']['token_table'] ."` WHERE `token` = ? AND `uid` = ?");
        $sql->execute(array($token, $uid));
        self::login(self::getUser("username", $uid), "", isset($_POST['logSys_two_step_login-remember_me']));
      }
      return true;
    }else if($identification != "" && $password != ""){
      $login = self::login($identification, $password, $remember_me, false);
      if($login === false){
        /**
         * Username/Password wrong
         */
        return false;
      }else if(is_array($login) && $login['status'] == "blocked"){
        return $login;
      }else{
        /**
         * Get the user ID from \Fr\LS::login()
         */
        $uid = $login;
          
        /**
         * Check if device is verfied so that 2 Step Verification can be skipped
         */
        if(isset($_COOKIE['logSysdevice'])){
          $sql = self::$dbh->prepare("SELECT 1 FROM `". self::$config['two_step_login']['devices_table'] ."` WHERE `uid` = ? AND `token` = ?");
          $sql->execute(array($uid, $_COOKIE['logSysdevice']));
          if($sql->fetchColumn() == "1"){
            $verfied = true;
            /**
             * Update last accessed time
             */
            $sql = self::$dbh->prepare("UPDATE `". self::$config['two_step_login']['devices_table'] ."` SET `last_access` = NOW() WHERE `uid` = ? AND `token` = ?");
            $sql->execute(array($uid, $_COOKIE['logSysdevice']));
            
            self::login(self::getUser("username", $uid), "", $remember_me);
            return true;
          }
        }
        /**
         * Start the 2 Step Verification Process
         * Do only if callback is present and if
         * the device is not verified
         */
        if(is_callable(self::$config['two_step_login']['send_callback']) && !isset($verified)){
          /**
           * The first part of 2 Step Login is completed
           */
          $_SESSION['logSys_two_step_login-first_step'] = '1';
          
          /**
           * The 2nd parameter depends on `config` -> `two_step_login` -> `numeric`
           */
          $token = self::rand_string(self::$config['two_step_login']['token_length'], self::$config['two_step_login']['numeric']);
          
          /**
           * Save the token in DB
           */
          $sql = self::$dbh->prepare("INSERT INTO `". self::$config['db']['token_table'] ."` (`token`, `uid`, `requested`) VALUES (?, ?, NOW())");
          $sql->execute(array($token, $uid));
          
          call_user_func_array(self::$config['two_step_login']['send_callback'], array($uid, $token));
          
          /**
           * Display the form
           */
          $html = "<form action='". self::curPageURL() ."' method='POST'>
            <p>". self::$config['two_step_login']['instruction'] ."</p>
            <label>
              <p>Token Received</p>
              <input type='text' name='logSys_two_step_login-token' placeholder='Paste the token here... (case sensitive)' />
            </label>
            <label style='display: block;'>
              <span>Remember this device ?</span>
              <input type='checkbox' name='logSys_two_step_login-dontask' />
            </label>
            <input type='hidden' name='logSys_two_step_login-uid' value='". $uid ."' />
            ". ($remember_me === true ? "<input type='hidden' name='logSys_two_step_login-remember_me' />" : "") ."
            <label>
              <button>Verify</button>
            </label>
          </form>";
          echo $html;
          return "formDisplay";
        }else{
          self::log("two_step_login: Token Callback not present");
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
   * Returns array of devices that are authorized
   * to login by user's account credentials
   */
  public static function getDevices(){
    if(self::$loggedIn){
      $sql = self::$dbh->prepare("SELECT * FROM `". self::$config['two_step_login']['devices_table'] ."` WHERE `uid` = ?");
      $sql->execute(array(self::$user));
      return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }
  
  /**
   * Revoke a device
   */
  public static function revokeDevice($device_token){
    if(self::$loggedIn){
      $sql = self::$dbh->prepare("DELETE FROM `". self::$config['two_step_login']['devices_table'] ."` WHERE `uid` = ? AND `token` = ?");
      $sql->execute(array(self::$user, $device_token));
      if(isset($_SESSION['device_check'])){
        unset($_SESSION['device_check']);
      }
      return $sql->rowCount() == 1;
    }
  }
  
  /**
   * ---------------------
   * Extra Tools/Functions
   * ---------------------
   */
  
  /**
   * Check if E-Mail is valid
   */
  public static function validEmail($email = ""){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }
  
  /**
   * Get the current page URL
   */
  public static function curPageURL() {
    $pageURL = 'http';
    if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){$pageURL .= "s";}
    $pageURL .= "://";
    if($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    }else{
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }
  
  /**
   * Generate a Random String
   * $int - Whether numeric string should be output
   */
  public static function rand_string($length, $int = false) {
    $random_str = "";
    $chars = $int ? "0516243741506927589" : "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $size = strlen($chars) - 1;
    for($i = 0;$i < $length;$i++) {
      $random_str .= $chars[rand(0, $size)];
    }
    return $random_str;
  }
  
  /**
   * Get the current page path.
   * Eg: /mypage, /folder/mypage.php
   */
  public static function curPage(){
    $parts = parse_url(self::curPageURL());
    return $parts["path"];
  }
  
  /**
   * Do a redirect
   */
  public static function redirect($url, $status = 302){
    header("Location: $url", true, $status);
    exit;
  }
  
  /**
   * Any mails need to be sent by logSys goes to here
   */
  public static function sendMail($email, $subject, $body){
    /**
     * If there is a callback for email sending, use it else PHP's mail()
     */
    if(is_callable(self::$config['basic']['email_callback'])){
      call_user_func_array(self::$config['basic']['email_callback'], array($email, $subject, $body));
    }else{
      $headers = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/html; charset=iso-8859-1";
      $headers[] = "From: ". self::$config['basic']['email'];
      $headers[] = "Reply-To: ". self::$config['basic']['email'];
      mail($email, $subject, $body, implode("\r\n", $headers));
    }
  }
  
  /**
   * CSRF Protection
   */
  public static function csrf($type = ""){
    if(!isset($_COOKIE['csrf_token'])){
      $csrf_token = self::rand_string(5);
      setcookie("csrf_token", $csrf_token, 0, self::$config['cookies']['path'], self::$config['cookies']['domain']);
    }else{
      $csrf_token = $_COOKIE['csrf_token'];
    }
    if($type == "s"){
      /**
       * Output as string
       */
      return urlencode($csrf_token);
    }elseif($type == "g"){
      /**
       * Output as a GET parameter
       */
      return "&csrf_token=" . urlencode($csrf_token);
    }elseif($type == "i"){
      /**
       * Output as an input field
       */
      echo "<input type='hidden' name='csrf_token' value='{$csrf_token}' />";
    }else{
      /**
       * Check CSRF validity
       */
      if((isset($_POST['csrf_token']) && $_COOKIE['csrf_token'] == $_POST['csrf_token']) || (isset($_GET['csrf_token']) && $_COOKIE['csrf_token'] == $_GET['csrf_token'])){
        return true;
      }else{
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
