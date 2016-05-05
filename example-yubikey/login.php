<?php
require "config.php";
if(isset($_POST['action_login'])){
	$identification = $_POST['login'];
	$password = $_POST['password'];
  $otp = $_POST['otp'];
	if($identification == "" || $password == "" || $otp == ""){
		$msg = array("Error", "Please Fill username, password and key!");
	}else{
		$login = \Fr\LS::login($identification, $password, $otp, isset($_POST['remember_me']));
		if($login === false){
			$msg = array("Error", "Username / Password /OTP Wrong !");
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
      <?php
      if(isset($msg)){
        echo "<h2>{$msg[0]}</h2><p>{$msg[1]}</p>";
      }
      ?>
      <form action="login.php" method="POST" style="margin:0px auto;display:table;">
        <label>
          <p>Username / E-Mail</p>
          <input name="login" type="text"/>
        </label><br/>
        <label>
          <p>Password</p>
          <input name="password" type="password"/>
        </label><br/>
        <label>
          <p>Yubikey</p>
          <input name="otp" type="password"/>
        </label><br/>
        <label>
          <p>
            <input type="checkbox" name="remember_me"/> Remember Me
          </p>
        </label>
        <div clear></div>
        <button style="width:150px;" name="action_login">Log In</button>
      </form>
      <style>
        input[type=text], input[type=password]{
          width: 230px;
        }
      </style>
      <p>
        <p>Don't have an account ?</p>
        <a class="button" href="register.php">Register</a>
      </p>
      <p>
        <p>Don't have a key ?</p>
        <a class="button" href="register_key.php">Register Yubikey</a>
      </p>
      <p>
        <p>Forgot Your Password ?</p>
        <a class="button" href="reset.php">Reset Password</a>
      </p>
    </div>
  </body>
</html>
