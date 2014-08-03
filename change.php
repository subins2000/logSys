<?php
require "class.loginsys.php";
$LS = new LoginSystem();
$LS->init();
?>
<!DOCTYPE html>
<html>
	<head></head>
	<body>
 		<?php
 		$LS->changePassword();
 		?>
	</body>
</html>