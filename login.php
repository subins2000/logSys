<?php
require "class.loginsys.php";
$LS = new LoginSystem();
$LS->init();
if(isset($_POST['act_login'])){
	$user=$_POST['login'];
	$pass=$_POST['pass'];
	if($user == "" || $pass==""){
		$msg = array("Error", "Username / Password Wrong !");
	}else{
		$login = $LS->login($user, $pass);
		if($login === false){
			$msg = array("Error", "Username / Password Wrong !");
		}else if(is_array($login) && $login['status'] == "blocked"){
			$msg = array("Error", "Too many login attempts. You can attempt login after ". $login['minutes'] ." minutes (". $login['seconds'] ." seconds)");
		}
	}
}
?>
<html>
 <head>
  <title>Log In</title>
 </head>
 <body>
  <div class="content">
   <h2>Log In</h2>
   <form action="login.php" method="POST" style="margin:0px auto;display:table;">
    <label>Username / E-Mail</label><br/>
    <input name="login" type="text"/><br/>
    <label>Password</label><br/>
    <input name="pass" type="password"/><br/>
    <label>
     <input type="checkbox" name="remember_me"/> Remember Me
    </label>
    <div clear></div>
    <button style="width:150px;" name="act_login">Log In</button>
   </form>
   <style>
   input[type=text], input[type=password]{
    width: 230px;
   }
   </style>
   <?
   if(isset($msg)){
    echo $msg[0]."<br/>".$msg[1];
   }
   ?>
   <p>
    Don't have an account ?<div clear></div>
    <a class="button" href="register.php">Register</a>
   </p>
   <p>
    Forgot Your Password ?<div clear></div>
    <a class="button" href="reset.php">Reset Password</a>
   </p>
  </div>
 </body>
</html>