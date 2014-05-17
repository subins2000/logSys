<?
require "class.loginsys.php";
$LS = new LoginSystem();
$LS->init();
?>
<!DOCTYPE html>
<html><head>

</head><body>
 <?
 $LS->changePassword();
 ?>
</body></html>