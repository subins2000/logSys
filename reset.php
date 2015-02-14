<html>
 <head></head>
 <body>
  <?php
  require "class.loginsys.php";
  $LS = new LoginSystem();
  $LS->init();
  $LS->forgotPassword();
  ?>
 </body>
</html>
