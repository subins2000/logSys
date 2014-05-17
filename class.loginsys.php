<?
/*
.---------------------------------------------------------------------------.
|  Software: PHP Login System - PHP logSys                                  |
|   Version: 0.1                                                            |
|   Contact: http://github.com/subins2000/logsys  (also subinsb.com)        |
|      Info: http://github.com/subins2000/logsys                            |
|   Support: http://subinsb.com/ask/php-logsys                              |
| ------------------------------------------------------------------------- |
|    Author: Subin Siby (project admininistrator)                           |
| Copyright (c) 2014, Subin Siby. All Rights Reserved.                      |
| ------------------------------------------------------------------------- |
|   License: Distributed under the General Public License (GPL)             |
|            http://www.gnu.org/licenses/gpl-3.0.html                       |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
'---------------------------------------------------------------------------'
*/
ini_set("display_errors", "on");

class LoginSystem{
 /* Start Config */
 private $dbhost = "localhost"; // Host Name
 private $dbport = "3306"; //Port
 private $dbuser = "root"; // MySQL Database Username
 private $dbpass = "backstreetboys"; // MySQL Database Password
 private $dbname = "p"; // Database Name
 private $dbtable = "users"; // Your Users Table
 
 private $secureKey    = "ckxc436jd*^30f840v*9!@#$"; // A Secure Key For Cookie Encryption. Don't make this public
 private $passwordSalt = "^#$4%9f+1^p9)M@4M)V$"; // Secret Password Salt. Only change once before setting user registration public.
 private $company      = "My Site"; // Company for name for including in emails
 var $phpsessionstart  = true; // Should I Start A PHP Session
 var $emailLogin       = true; // Make Login With Username & E-Mail Possible
 var $rememberMe       = true; // Add Remember Me Feature.
 /* Extra Settings*/
 // Set the following variables only if you're going to use $LS->init()
 public $staticPages  = array(
  "/test/login-system/", "/test/login-system/reset.php"
 ); // Pages that doesn't require logging in (exclude login page)
 private $loginPage    = "/test/login-system/login.php"; // The login page. ex : /login.php or /accounts/login.php
 private $homePage     = "/test/login-system/home.php"; // The home page. The main page for logged in users. Redirects to here when logs in
 /* End Config */
 
 public $loggedIn = false;
 public $db       = true;
 public $user     = false;
 private $cookie;
 private $session;
 private $remCook;
 private $dbh;
 private $initCalled=false;
 
 public function __construct(){
  if($this->phpsessionstart == true){
   session_start();
  }
  try{
   $this->dbh=new PDO("mysql:dbname={$this->dbname};host={$this->dbhost};port={$this->dbport}", $this->dbuser, $this->dbpass);
   $this->db=true;
   $this->cookie  = isset($_COOKIE['logSyslogin']) ? $_COOKIE['logSyslogin'] : false;
   $this->session = isset($_SESSION['logSyscuruser']) ? $_SESSION['logSyscuruser'] : false;
   $this->remCook = isset($_COOKIE['logSysrememberMe']) ? $_COOKIE['logSysrememberMe'] : false;
   array_push($this->staticPages, $this->loginPage);
   $encUserId=hash("sha256", $this->secureKey.$this->session.$this->secureKey);
   $this->loggedIn = $this->cookie==$encUserId ? true:false;
   if($this->rememberMe===true && isset($this->remCook) && $this->loggedIn===false){
    $encUserId=hash("sha256", $this->secureKey.$this->remCook.$this->secureKey);
    $this->loggedIn = $this->cookie==$encUserId ? true:false;
    if($this->loggedIn===true){
     $_SESSION['logSyscuruser'] = $this->remCook;
    }
   }
   $this->user = $this->session;
   return true;
  }catch(PDOException $e){
   return false;
  }
 }
 public function init(){
  if($this->loggedIn && array_search($this->curPage(), $this->staticPages)!==false){
   $this->redirect($this->homePage);
  }elseif(!$this->loggedIn && array_search($this->curPage(), $this->staticPages)===false){
   $this->redirect($this->loginPage);
  }
  $this->initCalled=true;
 }
 public function login($username, $password, $cookies=true){
  if($this->db === true){
   if($this->emailLogin === true){
    $query = "SELECT id, password, password_salt FROM {$this->dbtable} WHERE `username`=:login OR `email`=:login ORDER BY id LIMIT 1";
   }else{
    $query = "SELECT id, password, password_salt FROM {$this->dbtable} WHERE `username`=:login ORDER BY id LIMIT 1";
   }
   /* We Add LIMIT to 1 because we need to get just an array of data. Nothing else */
   $sql=$this->dbh->prepare($query);
   $sql->bindValue(":login", $username);
   $sql->execute();
   if($sql->rowCount()==0){
    return false;
   }else{
    $rows=$sql->fetch(PDO::FETCH_ASSOC);
    $us_id   = $rows['id'];
    $us_pass = $rows['password'];
    $us_salt = $rows['password_salt'];
    $saltedPass = hash('sha256',$password.$this->passwordSalt.$us_salt);
    if($saltedPass == $us_pass){
     if($cookies===true){
      $_SESSION['logSyscuruser']=$us_id;
      setcookie("logSyslogin", hash("sha256", $this->secureKey.$us_id.$this->secureKey), time()+3600*99*500, "/");
      if(isset($_POST['remember_me']) && $this->rememberMe===true){
       setcookie("logSysrememberMe", $us_id, time()+3600*99*500, "/");
      }
      $this->loggedIn=true;
      if($this->initCalled){
       $this->redirect($this->homePage);
      }
     }
     return true;
    }else{
     return false;
    }
   }
  }
 }
 public function register($id, $password, $other=array()){
  if($this->userExists($id)){
   return "exists";
  }else{
   $randomSalt=$this->rand_string(20);
   $saltedPass=hash('sha256',$password.$this->passwordSalt.$randomSalt);
   if(count($other)==0){
    $sql=$this->dbh->prepare("INSERT INTO {$this->dbtable} (`username`, `password`, `password_salt`) VALUES(:username, :password, :passwordSalt)");
   }else{
    $columns=implode(",", array_keys($other));
    $colVals=implode(",:", array_keys($other));
    $sql=$this->dbh->prepare("INSERT INTO {$this->dbtable} (`username`, `password`, `password_salt`, $columns) VALUES(:username, :password, :passwordSalt, :$colVals)");
    foreach($other as $k=>$v){
     $sql->bindValue(":$k", $v);
    }
   }
   $sql->bindValue(":username", $id);
   $sql->bindValue(":password", $saltedPass);
   $sql->bindValue(":passwordSalt", $randomSalt);
   $sql->execute();
   return true;
  }
 }
 public function logout(){
  session_destroy();
  setcookie("logSyslogin", "", time()-3600, "/");
  setcookie("logSysrememberMe", "", time()-3600, "/");
  $this->redirect($this->loginPage);
  return true;
 }
 public function forgotPassword(){
  $curStatus="initial"; // The Current Status of Forgot Password action
  $identName=$this->emailLogin===false ? "Username":"Username / E-Mail";
  if(!isset($_POST['logSysforgotPass']) && !isset($_GET['resetPassToken']) && !isset($_POST['logSysforgotPassRePass'])){
   $html='<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
    $html.="<label>$identName<br/><input type='text' id='loginSysIdentification' placeholder='Which one do you remember ?' size='25' name='identification'/></label>";
    $html.="<br/><button name='logSysforgotPass' type='submit'>Reset Password</button>";
   $html.="</form>";
   echo $html;
   $curStatus="resetPasswordForm";
  }elseif(isset($_GET['resetPassToken']) && !isset($_POST['logSysforgotPassRePass'])){
   $sql=$this->dbh->prepare("SELECT uid FROM `resetTokens` WHERE token=?");
   $sql->execute(array($_GET['resetPassToken']));
   if($sql->rowCount()==0 || $_GET['resetPassToken']==""){
    echo "<h3>Error : Wrong/Invalid Token</h3>";
    $curStatus="invalidToken";
   }else{
    $html="<p>The Token key was Authorized. Now, you can change the password</p>";
    $html.="<form action='".$_SERVER['PHP_SELF']."' method='POST'>";
     $html.="<input type='hidden' name='token' value='".$_GET['resetPassToken']."'/>";
     $html.="<label>New Password<br/><input type='password' name='password'/></label><br/>";
     $html.="<label>Retype Password<br/><input type='password' name='password2'/></label><br/>";
     $html.="<button name='logSysforgotPassRePass'>Reset Password</button>";
    $html.="</form>";
    echo $html;
    $curStatus="changePasswordForm";
   }
  }elseif(isset($_POST['logSysforgotPassRePass'])){
   $sql=$this->dbh->prepare("SELECT uid FROM resetTokens WHERE token=?");
   $sql->execute(array($_POST['token']));
   if($sql->rowCount()==0 || $_POST['token']==""){
    echo "<h3>Error : Wrong/Invalid Token</h3>";
    $curStatus="invalidToken";
   }else{
    if($_POST['password']!=$_POST['password2'] || $_POST['password']=="" || $_POST['password2']==""){
     echo "<h3>Error : Passwords Don't Match Or Passwords Left Blank</h3>";
     $curStatus="passwordDontMatch";
    }else{
     $_POST['newPassword']=$_POST['password2'];
     $this->user=$sql->fetchColumn();
     $this->loggedIn=true;
     if($this->changePassword($this->secureKey)){
      $this->user=false;
      $this->loggedIn=false;
      $sql=$this->dbh->prepare("DELETE FROM resetTokens WHERE token=?");
      $sql->execute(array($_POST['token']));
      echo "<h3>Success : Password Reset Successful</h3><p>You may now login with your new password.</p>";
      $curStatus="passwordChanged";
     }
    }
   }
  }else{
   $identification=isset($_POST['identification']) ? $_POST['identification']:"";
   if($identification==""){
    echo "<h3>Error : $identName not provided</h3>";
    $curStatus="identityNotProvided";
   }else{
    $sql=$this->dbh->prepare("SELECT email, id FROM {$this->dbtable} WHERE `username`=:login OR `email`=:login");
    $sql->bindValue(":login", $identification);
    $sql->execute();
    if($sql->rowCount()==0){
     echo "<h3>Error : User Not Found</h3>";
     $curStatus="userNotFound";
    }else{
     $rows=$sql->fetch(PDO::FETCH_ASSOC);
     $email = $rows['email'];
     $uid   = $rows['id'];
     $token = $this->rand_string(40);
     $sql=$this->dbh->prepare("INSERT INTO `resetTokens` (`token`, `uid`, `requested`) VALUES (?, ?, NOW())");
     $sql->execute(array($token, $uid));
     $subject="Reset Password";
     $body="You requested for resetting your password on {$this->company}. For this, please click the following link :
     <blockquote>
      <a href='{$this->curPageURL()}?resetPassToken=$token'>Reset Password : $token</a>
     </blockquote>";
     mail($email, $subject, $body); /* Change mail() function to something else if you like */
     $html="<p>An email has been sent to your email inbox with instructions. Check Your Mail Inbox and SPAM Folders.</p><p>You can close this window.</p>";
     echo $html;
     $curStatus="emailSent";
    }
   }
  }
  return $curStatus;
 }
 public function changePassword($parent=""){
  $curStatus="initial"; // The Current Status of Forgot Password action
  if($this->loggedIn){
   if($parent==$this->secureKey && isset($_POST['newPassword']) && $_POST['newPassword']!=""){
    $randomSalt=$this->rand_string(20);
    $saltedPass=hash('sha256',$_POST['newPassword'].$this->passwordSalt.$randomSalt);
    $sql=$this->dbh->prepare("UPDATE {$this->dbtable} SET password=?, password_salt=? WHERE id=?");
    $sql->execute(array($saltedPass, $randomSalt, $this->user));
    return true;
   }elseif(!isset($_POST['logSysChangePassword'])){
    $html="<form action='".$_SERVER['PHP_SELF']."' method='POST'>";
     $html.="<label>Current Password<br/><input type='password' name='curpass'/></label><br/>";
     $html.="<label>New Password<br/><input type='password' name='newPassword'/></label><br/>";
     $html.="<label>Retype New Password<br/><input type='password' name='newPassword2'/></label><br/>";
     $html.="<button name='logSysChangePassword' type='submit'>Change Password</button>";
    $html.="</form>";
    echo $html;
    $curStatus="changePasswordForm";
   }elseif(isset($_POST['logSysChangePassword'])){
    if(isset($_POST['newPassword']) && $_POST['newPassword']!="" && isset($_POST['newPassword2']) && $_POST['newPassword2']!="" && isset($_POST['curpass']) && $_POST['curpass']!=""){
     $curpass=$_POST['curpass'];
     $newPassword=$_POST['newPassword'];
     $newPassword2=$_POST['newPassword2'];
     $sql=$this->dbh->prepare("SELECT username FROM {$this->dbtable} WHERE id=?");
     $sql->execute(array($this->user));
     $curuserUsername=$sql->fetchColumn();
     if($this->login($curuserUsername, $curpass, false)){
      if($newPassword!=$newPassword2){
       echo "<h3>Error : Password Mismatch</h3>";
       $curStatus="newPasswordMismatch";
      }else{
       $this->changePassword($this->secureKey);
       echo "<h3>Success : Password Changed Successful</h3>";
       $curStatus="passwordChanged";
      }
     }else{
      echo "<h3>Error : Current Password Was Wrong</h3>";
      $curStatus="currentPasswordWrong";
     }
    }else{
     echo "<h3>Error : Password Fields was blank</h3>";
     $curStatus="newPasswordFieldsBlank";
    }
   }
  }else{
   echo "<h3>Error : Not Logged In</h3>";
   $curStatus="notLoggedIn";
  }
  return $curStatus;
 }
 public function userExists($username){
  if($this->emailLogin === true){
   $query = "SELECT id FROM {$this->dbtable} WHERE `username`=:login OR `email`=:login ORDER BY id LIMIT 1";
  }else{
   $query = "SELECT id FROM {$this->dbtable} WHERE `username`=:login ORDER BY id LIMIT 1";
  }
  $sql=$this->dbh->prepare($query);
  $sql->execute(array(
   ":login" => $username
  ));
  return $sql->rowCount()==0 ? false:true;
 }
 public function getUser($user=null){
  if($user==null){
   $user=$this->user;
  }
  $sql=$this->dbh->prepare("SELECT * FROM {$this->dbtable} WHERE `id`=? ORDER BY id LIMIT 1");
  $sql->execute(array($user));
  return $sql->fetch(PDO::FETCH_ASSOC);
 }
 /* Extra Tools/Functions */
 public function validEmail($m){
  return !preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/', $m) ? false:true;
 }
 public function curPageURL() {
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
 public function rand_string($length) {
  $str="";
  $chars = "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $size = strlen($chars);
  for($i = 0;$i < $length;$i++) {
   $str .= $chars[rand(0,$size-1)];
  }
  return $str;
 }
 public function curPage(){
  $parts=parse_url($this->curPageURL());
  return $parts["path"];
 }
 public function redirect($url, $status=302){
  header("Location: $url", true, $status);
 }
 /* End Extra Tools/Functions */
}
?>