<?php
require "config.php";
if( isset($_POST['newName']) ){
	$_POST['newName'] = $_POST['newName'] == "" ? "Dude" : $_POST['newName'];
	\Fr\LS::updateUser(array(
		"name" => $_POST['newName']
	));
}
?>
<html>
	<head></head>
	<body>
		<h1>Welcome</h1>
		<p>You have been successfully logged in.</p>
		<p>
			<a href="logout.php">Log Out</a>
		</p>
		<p>
			You registered on this website <strong><?php echo \Fr\LS::joinedSince(); ?></strong> ago.
		</p>
		<p>
			Here is the full data, the database stores on this user :
		</p>
		<pre><?php
			$details = \Fr\LS::getUser();
			print_r($details);
			?></pre>
		<p>
			Change the name of your account :
		</p>
		<form action="home.php" method="POST">
			<input name="newName" placeholder="New name" />
			<button>Change Name</button>
		</form>
    <p>
      <a href="change.php">Change Password</a>
    </p>
	</body>
</html>
