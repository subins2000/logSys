<?php
include "class.loginsys.php";
$LS = new LoginSystem();
$LS->init();
?>
<!DOCTYPE html>
<html>
	<head></head>
	<body>
 		<div class="content">
  			<h1>Register</h1>
  			<form action="register.php" method="POST">
   			<label>
    				<input name="username" placeholder="username"/>
   			</label><br/>
   			<label>
    				<input name="email" placeholder="EMail"/> 
   			</label><br/>
	   		<label>
	    			<input name="pass" type="password" placeholder="Password"/>
	   		</label><br/>
	   		<label>
	    			<input name="pass2" type="password" placeholder="Retype pass"/>
	   		</label>
	   		<label><br/>
	    			<input name="name" placeholder="Name"/>
	   		</label><br/>
	   		<button name="submit">Register</button>
	  		</form>  
	  		<?php
	   	if( isset($_POST['submit']) ){
	    		$user	 = $_POST['username'];
	    		$email = $_POST['email'];
	    		$pass	 = $_POST['pass'];
	    		$pass2 = $_POST['pass2'];
	    		$name	 = $_POST['name'];
	    		if( $user=="" || $email=="" || $pass=='' || $pass2=='' || $name=='' ){
	     			echo "Fields Left Blank","Some Fields were left blank. Please fill up all fields.";
	     			exit;
	    		}
	    		if( !$LS->validEmail($email) ){
	     			echo "E-Mail Is Not Valid", "The E-Mail you gave is not valid";
	     			exit;
	    		}
	    		if( !ctype_alnum($user) ){
	     			echo "Invalid Username", "The Username is not valid. Only ALPHANUMERIC characters are allowed and shouldn't exceed 10 characters.";
	     			exit;
	    		}
	    		if($pass != $pass2){
		     		echo "Passwords Don't Match","The Passwords you entered didn't match";
	     			exit;
	    		}
   	 		$createAccount = $LS->register($user, $pass,
	    			array(
	    				"email" 	 => $email,
	    				"name" 	 => $name,
	    				"created" => date("Y-m-d H:i:s") // Just for testing
	    			)
	    		);
	    		if($createAccount === "exists"){
	     			echo "User Exists.";
	    		}elseif($createAccount === true){
	     			echo "Success. Created account. <a href='login.php'>Log In</a>";
	    		}
	   	}
	   	?>
 		</div>
	</body>
</html>