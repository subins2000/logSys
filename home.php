<?
require "class.loginsys.php";
$LS = new LoginSystem();
$LS->init();
?>
<a href="logout.php">Log Out</a>
<?
$details=$LS->getUser();
print_r($details);
?>