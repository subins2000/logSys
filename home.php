<?php
require "class.loginsys.php";
$LS = new LoginSystem();
$LS->init();
if( isset($_POST['newName']) ){
	$_POST['newName'] = $_POST['newName']=="" ? "Dude" : $_POST['newName'];
	$LS->updateUser(array(
		"name" => $_POST['newName']
	));
}
?>
<html>
	<head></head>
	<body>
		<a href="logout.php">Log Out</a>
		<p>
			You registered on this website <strong><?php echo $LS->timeSinceJoin(); ?></strong> ago.
		</p>
		<p>
			Here is the full data the database stores on this user :
		</p>
		<pre><?php
			$details = $LS->getUser();
			print_r($details);
			?></pre>
		<p>
			Change the name of your account :
		</p>
		<form action="home.php" method="POST">
			<input name="newName" placeholder="New name" />
			<button>Change Name</button>
		</form>
	</body>
</html>