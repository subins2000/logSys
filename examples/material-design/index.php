<?php
require 'config.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<?php printHead();?>
	</head>
	<body>
		<?php
		showHeader();
		?>
		<div class="container">
			<h1>My Site</h1>
			<p>Welcome to my site. You can sign in or create an account.</p>
			<a class="btn green" href="login.php" data-ajax>Sign In</a>
			<a class="btn red" href="register.php" data-ajax>Sign Up</a>
		</div>
	</body>
</html>
