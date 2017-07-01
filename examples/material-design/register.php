<?php
require 'config.php';

if (isset($_POST['submit'])) {
    $username         = $_POST['username'];
    $email            = $_POST['email'];
    $password         = $_POST['password'];
    $retyped_password = $_POST['retyped_password'];
    $name             = $_POST['name'];

    if (empty($username) || empty($email) || empty($password) || empty($retyped_password) || empty($name)) {
        $msg = array(
            'color' => 'teal',
            'text'  => 'Some fields were left blank. Please fill up all fields.',
        );
    } else if (!$LS->validEmail($email)) {
        $msg = array(
            'color' => 'red',
            'The Email is not valid',
        );
    } else if (!ctype_alnum($username)) {
        $msg = array(
            'color' => 'red',
            'text'  => "The Username is not valid. Only ALPHANUMERIC characters are allowed and shouldn't exceed 10 characters.",
        );
    } else if ($password != $retyped_password) {
        $msg = array(
            'color' => 'red',
            'text'  => "The Passwords you entered didn't match",
        );
    } else {
        $createAccount = $LS->register($username, $password,
            array(
                'email'   => $email,
                'name'    => $name,
                'created' => date('Y-m-d H:i:s'), // Just for testing
            )
        );
        if ($createAccount === 'exists') {
            $msg = array(
                'color' => 'red',
                'text'  => 'User Exists',
            );
        } else if ($createAccount === true) {
            $msg = array(
                'color' => 'green',
                'text'  => "Successfully created account. <a href='login.php' class='btn pink' data-ajax>Log In</a>",
            );
        }
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php printHead('Create Account');?>
	</head>
	<body>
		<?php
        showHeader();
        ?>
		<div class="container">
			<h1>Sign Up</h1>
			<?php
            if (isset($msg)) {
                echo <<<HTML
<div class="card-panel {$msg['color']}">
	<span class="white-text">{$msg['text']}</span>
</div>
HTML;
            }
            ?>
			<form action="register.php" method="POST">
				<div class="row">
					<div class="input-field col s12">
						<input type="text" id="username" name="username" />
						<label for="username">Username</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<input type="email" id="email" name="email" />
						<label for="email">Email</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<input type="password" id="password" name="password" />
						<label for="password">Password</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<input type="password" id="retyped_password" name="retyped_password" />
						<label for="retyped_password">Retype Password</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<input type="text" id="name" name="name" />
						<label for="name">Name</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s12">
						<button name="submit" class="btn red">Register</button>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>
