<?php
include "config.php";
\Fr\LS::init();
?>
<!DOCTYPE html>
<html>
  <head></head>
  <body>
    <div class="content">
      <h1>Register</h1>
      <form action="register.php" method="POST">
        <label>
          <input name="username" placeholder="Username" />
        </label>
        <label>
          <input name="email" placeholder="E-Mail" /> 
        </label>
        <label>
          <input name="pass" type="password" placeholder="Password" />
        </label>
        <label>
          <input name="retyped_password" type="password" placeholder="Retype Password" />
        </label>
        <label>
          <input name="name" placeholder="Name" />
        </label>
        <label>
          <button name="submit">Register</button>
        </label>
      </form>
      <?php
      if( isset($_POST['submit']) ){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['pass'];
        $retyped_password = $_POST['retyped_password'];
        $name = $_POST['name'];
        if( $username == "" || $email == "" || $password == '' || $retyped_password == '' || $name == '' ){
            echo "<h2>Fields Left Blank</h2>", "<p>Some Fields were left blank. Please fill up all fields.</p>";
        }elseif( !\Fr\LS::validEmail($email) ){
            echo "<h2>E-Mail Is Not Valid</h2>", "<p>The E-Mail you gave is not valid</p>";
        }elseif( !ctype_alnum($username) ){
            echo "<h2>Invalid Username</h2>", "<p>The Username is not valid. Only ALPHANUMERIC characters are allowed and shouldn't exceed 10 characters.</p>";
        }elseif($password != $retyped_password){
            echo "<h2>Passwords Don't Match</h2>", "<p>The Passwords you entered didn't match</p>";
        }else{
          $createAccount = \Fr\LS::register($username, $password,
            array(
              "email" => $email,
              "name" => $name,
              "created" => date("Y-m-d H:i:s") // Just for testing
            )
          );
          if($createAccount === "exists"){
            echo "<label>User Exists.</label>";
          }elseif($createAccount === true){
            echo "<label>Success. Created account. <a href='login.php'>Log In</a></label>";
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
