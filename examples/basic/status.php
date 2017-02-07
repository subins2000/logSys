<?php
require "config.php";
?>
<!DOCTYPE html>
<html>
	<head></head>
	<body>
 		<?php
    if($LS->isLoggedIn())
      echo "You are logged in. <a href='home.php'>Home</a>";
    else
      echo "You are not logged in. <a href='login.php'>Log In</a>";
    ?>
	</body>
</html>
