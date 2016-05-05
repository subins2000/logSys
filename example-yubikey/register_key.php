<?php
include "config.php";
?>
<!DOCTYPE html>
<html>
  <head></head>
  <body>
    <div class="content">
      <h1>Register Key</h1>
      <form action="register_key.php" method="POST">
        <label>
          <input name="username" placeholder="Username" />
        </label>
        <label>
          <input name="otp" placeholder="Yubikey" />
        </label>
        <label>
          <button name="submit">Register</button>
        </label>
      </form>
      <?php
      if( isset($_POST['submit']) ){
        $username = $_POST['username'];
        $otp = $_POST['otp'];
        if( $username == "" || $otp == ""){
            echo "<h2>Fields Left Blank</h2>", "<p>Some Fields were left blank. Please fill up all fields.</p>";
        }elseif( !\Fr\LS::userExists($username) ){
            echo "<h2>Username Is Not Valid</h2>", "<p>The username you gave is not valid</p>";
        }else{
          $addKey = \Fr\LS::register_key($username, $otp);
          if($addKey === "exists"){
            echo "<label>This key has already been associated to a user.</label>";
          }elseif($addKey === true){
            echo "<label>Success. Key Registered. <a href='login.php'>Log In</a></label>";
          }
        }
      }
      ?>
      <style>
        label{
          display: block;
          margin-bottom: 5px;
        }
      </style>
    </div>
  </body>
</html>
