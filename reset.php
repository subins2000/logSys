<html>
 <head></head>
 <body>
  <?
  require "class.loginsys.php";
  $LS = new LoginSystem();
  $LS->init();
  $LS->forgotPassword();
  ?>
 </body>
</html>